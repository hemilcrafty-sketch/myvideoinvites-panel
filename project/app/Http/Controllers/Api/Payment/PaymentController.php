<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Api\Payment\Gateways\PhonePeGateway;
use App\Http\Controllers\Api\Payment\Gateways\RazorpayGateway;
use App\Http\Controllers\Api\Payment\Gateways\StripeGateway;
use App\Http\Controllers\Api\RateController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\Utils\ApiController;
use App\Http\Controllers\Utils\DomainChecker;
use App\Http\Controllers\Utils\HelperController;
use App\Models\Order;
use App\Models\PromoCode;
use App\Models\Revenue\MasterPurchaseHistory;
use App\Models\UserData;
use App\Models\Video\VideoTemplate;
use App\Service\PaymentGateway;
use Cache;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use PhonePe\common\exceptions\PhonePeException;
use PhonePe\payments\v2\standardCheckout\StandardCheckoutClient;
use Razorpay\Api\Api;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class PaymentController extends ApiController
{

    function refreshTransaction(Request $request, $id = null): array|string
    {

//        if (!DomainChecker::isPanel($request)) return "error";

        if ($id) {
            return $this->enterTransData(
                request: $request,
                transaction_id: $id,
                method: str_starts_with($id, 'pay_') ? "razorpay" : ((str_starts_with($id, 'txn_') || str_starts_with($id, 'order_')) ? 'phonepe_pg' : "stripe"),
                isManual: 1
            );
        }

        try {

            $errors = [];
            $data = [];

            $paymentGateway = PaymentGateway::initByGateway('razorpay', null);
            if (!$paymentGateway) {
                $errors[] = "Razorpay init failed";
            } else {
                /** @var Api $razorpay */
                $razorpay = $paymentGateway->client;
                $datas = $razorpay->payment->all(array('count' => '100'));
                $datas = $datas->toArray();
                foreach ($datas['items'] as $value) {
                    if (MasterPurchaseHistory::whereTransactionId($value['id'])->exists()) continue;
                    if ($value['status'] == 'captured') {
                        $result = $this->enterTransData(
                            request: $request,
                            transaction_id: $value['id'],
                            method: $paymentGateway->name,
                            isManual: 1);
                        $data[] = $result;
                    }
                }
            }

            $paymentGateway = PaymentGateway::initByGateway('stripe', null);
            if (!$paymentGateway) {
                $errors[] = "Stripe init failed";
            } else {
                /** @var StripeClient $stripe */
                $stripe = $paymentGateway->client;
                $datas = $stripe->charges->all(['limit' => 100]);
                $datas = $datas->toArray();
                foreach ($datas['data'] as $value) {

                    if (MasterPurchaseHistory::whereTransactionId($value['balance_transaction'])->exists()) continue;

                    if ($value['amount'] == $value['amount_captured'] && $value['amount_refunded'] === 0 && $value['amount'] > 100) {
                        $result = $this->enterTransData(
                            request: $request,
                            transaction_id: $value['balance_transaction'],
                            method: $paymentGateway->name,
                            isManual: 1);
                        $data[] = $result;
                    }
                }
            }

            $paymentGateway = PaymentGateway::initByGateway('phonepe_pg', null);
            if (!$paymentGateway) {
                $errors[] = "PhonePe init failed";
            } else {
                /** @var StandardCheckoutClient $phonepe */
                $phonepe = $paymentGateway->client;
                $datas = Order::whereGateway('phonepe_pg')->where("status", "!=", "failed")->get();
                foreach ($datas as $value) {
                    $phonepeData = $phonepe->getOrderStatus($value->crafty_id, true);
                    if ($phonepeData->getState() === 'COMPLETED' && !MasterPurchaseHistory::whereTransactionId($value->crafty_id)->exists()) {

                        $paidAmount = $phonepeData->getAmount() / 100;

                        $value->payment_id = $value->crafty_id;
                        $value->paid = $paidAmount;
                        $value->status = 'paid';
                        $value->save();

                        $result = $this->enterTransData(
                            request: $request,
                            transaction_id: $value->crafty_id,
                            method: $paymentGateway->name,
                            isManual: 1);
                        $data[] = $result;
                    }
                }
            }

            $response['success'] = true;
            $response['message'] = 'Done';
            $response['data'] = $data;
            $response['errors'] = $errors;

        } catch (Exception $e) {
            $response['success'] = false;
            $response['errors'] = $errors;
            $response['message'] = $e->getMessage();
        }

        return $response;
    }

    function checkPromoCode(Request $request): array|string
    {
        if ($this->isFakeRequest($request)) return $this->failed(msg: "Unauthorized");

        $ipData = HelperController::getIpAndCountry($request);
        $amount = $request->get('amount');
        $code = $request->get('code');
        $show24Buyers = $request->get('sb', false);

        $data = $this->cpm($amount, $code, $ipData['cur']);
        $data['curSymbol'] = $ipData['cur'] === "INR" ? '₹' : '$';

        if ($show24Buyers) {
            $last24Buyers = MasterPurchaseHistory::where('created_at', '>=', now()->subHours(24))->count();
            $data['last_24_hours_buyers'] = "$last24Buyers sold in last 24 hours";
        }

        return $this->sendRawResponse(response: $data);
    }

    function getOrder(Request $request): array|string
    {
        $errorMsg = "Looks like this payment link was cancelled or already paid";
        if ($this->isFakeRequest($request)) return $this->failed(msg: "Unauthorized");

        $id = $request->get('id');
        if (is_null($id)) return $this->failed(msg: "Parameters missing!");

        $order = Order::whereCraftyId($id)->whereStatus('failed')->first();
        if (!$order) return $this->successed(msg: $errorMsg);

        $user_data = UserData::where("uid", $order->user_id)->first();
        if (!$user_data) return $this->failed(msg: "Invalid User");

        $purchaseData = null;
        $id = null;
        $amount = 0;
        $isInr = $order->currency === "INR";

        if ($order->type === 'video') {
            $purchaseData = VideoTemplate::getTempDatas($order);
            $id = $purchaseData['data']['id'];
            $amount = $purchaseData['amount'];
        }

        if (!$purchaseData) return $this->successed(msg: $errorMsg);

        $returnRes['id'] = $id;
        $returnRes['amount'] = (float)$amount;
        $returnRes['currency'] = $order->currency;
        $returnRes['promo'] = true;
        $returnRes['orderData'] = $purchaseData;
        $returnRes['name'] = '';
        $returnRes['offer'] = (bool)$order->has_offer;
        $returnRes['type'] = $order->gateway;

        if ($order->gateway === 'razorpay') {
            $returnRes['type'] = 'razorpay';
            $returnRes['create'] = true;
        } else if ($order->gateway === 'stripe') {
            try {
                $paymentGateway = PaymentGateway::initByGateway('stripe', null);
                if (!$paymentGateway) return $this->failed(msg: "Stripe init failed");
                $stripe = $paymentGateway->client;

                $datas = $stripe->customers->allPaymentMethods($user_data->stripe_cus_id, ['type' => 'card']);
                $returnRes['type'] = 'stripe';
                $returnRes['create'] = true;
                $returnRes['pm_data'] = [
                    'email' => $user_data->email,
                    'data' => $datas,
                    'ipData' => ['ip' => '', 'cc' => $isInr ? 'IN' : 'US', 'cn' => '', 'cur' => $order->currency]
                ];
            } catch (ApiErrorException|Exception $e) {
                return $this->successed(msg: $errorMsg);
            }
        }

        return $this->successed(datas: $returnRes);
    }

    function listMethods(Request $request): array|string
    {
        if (!$request->has('u') && $this->isFakeRequest($request)) return $this->failed(msg: "Unauthorized");

        $this->uid = $request->input("u", $this->uid);
        $ipData = HelperController::getIpAndCountry($request);
        $user_data = UserData::where("uid", $this->uid)->first();

        if (!$user_data || $request->offer === true) {
            return $this::successed(datas: [
                'email' => '',
                'data' => ['data' => []],
                'ipData' => $ipData
            ]);
        }

        $paymentGateway = PaymentGateway::initByGateway('stripe', null);
        if (!$paymentGateway) return $this->failed(msg: "Stripe init failed");

        $stripeCusId = $this::createUserPaymentGatewayRefId($user_data, null, $paymentGateway);

        return $this::successed(datas: [
            'email' => $user_data->email,
            'data' => $paymentGateway->client->customers->allPaymentMethods($stripeCusId, ['type' => 'card']),
            'ipData' => $ipData
        ]);

    }

    function updatePm(Request $request): array|string
    {
        if (!$request->has('u') && $this->isFakeRequestAndUser($request)) return $this->failed(msg: "Unauthorized");

        $this->uid = $request->input("u", $this->uid);
        $pmId = $request->get('pm');
        $billing_details = $request->get('billing_details');
        $month = $request->get('month');
        $year = $request->get('year');

        $user = UserData::whereUid($this->uid)->first();
        if (empty($pmId) || empty($user) || empty($user->stripe_cus_id) || empty($billing_details) || empty($month) || empty($year)) return $this->failed("Parameters missing!");

        $paymentGateway = PaymentGateway::initByGateway('stripe', null);
        if (!$paymentGateway) return $this->failed(msg: "Stripe init failed");
        $stripeClient = $paymentGateway->client;

        $billingDatas = json_decode($billing_details, true);

        try {
            $customer = $stripeClient->customers->retrieve($user->stripe_cus_id);
            $paymentMethod = $stripeClient->paymentMethods->retrieve($pmId);

            if (empty($paymentMethod->customer) || $paymentMethod->customer !== $customer->id) {
                return $this->failed(msg: "Unable to edit payment method.");
            }

            $subscriptions = $stripeClient->subscriptions->all([
                'customer' => $customer->id,
                'status' => 'all',
                'limit' => 100,
            ]);

            foreach ($subscriptions->data as $subscription) {
                if (in_array($subscription->status, ['active', 'past_due'], true) && $subscription->default_payment_method === $pmId) {
                    return $this->failed(msg: "Payment method is used by an active or past due subscription.");
                }
            }

            $data = $stripeClient->paymentMethods->update($pmId, ['billing_details' => $billingDatas, 'card' => ['exp_month' => $month, 'exp_year' => $year]]);

            return $this->successed(datas: ['data' => $data]);

        } catch (\Exception $e) {
            return $this->failed();
        }
    }

    function detachPm(Request $request): array|string
    {
        if (!$request->has('u') && $this->isFakeRequestAndUser($request)) return $this->failed(msg: "Unauthorized");

        $this->uid = $request->input("u", $this->uid);
        $pmId = $request->get('pm');
        $user = UserData::whereUid($this->uid)->first();
        if (empty($pmId) || empty($user) || empty($user->stripe_cus_id)) return $this->failed("Parameters missing!");

        $paymentGateway = PaymentGateway::initByGateway('stripe', null);
        if (!$paymentGateway) return $this->failed(msg: "Stripe init failed");
        $stripeClient = $paymentGateway->client;

        try {
            $customer = $stripeClient->customers->retrieve($user->stripe_cus_id);
            $paymentMethod = $stripeClient->paymentMethods->retrieve($pmId);

            if (empty($paymentMethod->customer) || $paymentMethod->customer !== $customer->id) {
                return $this->failed(msg: "Unable to detach payment method.");
            }

            if ($customer->invoice_settings->default_payment_method === $pmId) return $this->failed(msg: "Cannot detach default payment method.");

            $subscriptions = $stripeClient->subscriptions->all([
                'customer' => $customer->id,
                'status' => 'all',
                'limit' => 100,
            ]);

            foreach ($subscriptions->data as $subscription) {
                if (in_array($subscription->status, ['active', 'past_due'], true) && $subscription->default_payment_method === $pmId) {
                    return $this->failed(msg: "Payment method is used by an active or past due subscription.");
                }
            }

            $data = $stripeClient->paymentMethods->detach($pmId, []);

            return $this->successed(datas: ['data' => $data]);
        } catch (\Exception $e) {
            return $this->failed();
        }
    }

    function createOrder(Request $request): array|string
    {

        $this->uid = $request->input("u", $this->uid);
        $paymentMethodId = $request->get('id');
        $name = $request->get('name');
        $number = $request->get('number');
        $email = $request->get('email');
        $refId = $request->get('ref');
        $url = $request->get('url');
        $seats = $request->get('seats', 1);

        $allExists = $name && $email && $number;

        if (!$request->has("u")) {
            if (($allExists || $refId) ? $this->isFakeRequest($request) : $this->isFakeRequestAndUser($request)) {
                return $this->failed(msg: "Unauthorized");
            }
        }

        $assetDetails = $request->get('p');
        $from = $request->get('from', 'Web');
        $code = $request->get('code');

        if ($assetDetails == null) {
            return $this->failed(msg: "Parameters missing!");
        }

        if (DomainChecker::isEditor($request)) $from = 'Editor';

        $ipData = HelperController::getIpAndCountry($request);
        $currency = $ipData['cur'];
        $fromAllowedIp = DomainChecker::isAllowedIps($ipData['ip']);

        if (strtoupper($currency) != "INR") $currency = "USD";

        $isInr = $currency === "INR";

        $password = HelperController::generateID(length: 6);

        if ($refId) {
            $order = Order::whereCraftyId($refId)->first();
            if ($order) $this->uid = $order->user_id ?? $order->uid;
        }

        if ($name && $email && $number) {
            $user_data = UserData::whereEmail($email)->first();
            if (!$user_data) {
                $userController = new UserApiController($request);
                $result = $userController->createFirebaseUser($request, $name, $email, $number, $password);
                if (!$result['success']) return $this->sendRawResponse($result);
                $user_data = $result['data'];
            }
        } else {
            $user_data = UserData::where("uid", $this->uid)->first();
        }

        if (!$user_data) return $this->failed(msg: "Error");

        $this->uid = $user_data->uid;

        $paymentDetails = $this->getPaymentDetails(request: $request, user_data: $user_data, currency: $currency, assetDetails: $assetDetails, code: $code, url: $url, seats: $seats);
        if (!$paymentDetails['success']) return $this->sendRawResponse($paymentDetails);


        $assetDetails = $paymentDetails['assetDetails'];
        $amount = $paymentDetails['amount'];
        $payMode = $paymentDetails['payMode'];
        $promoCodeId = $paymentDetails['promoCodeId'];
        $description = $paymentDetails['description'];
        $eventData = $paymentDetails['eventData'];

        $paymentGateway = PaymentGateway::initByType($payMode, $isInr ? "NATIONAL" : "INTERNATIONAL");

        if (!$paymentGateway) return $this->failed(msg: "Gateway init failed");

        $gatewayType = $paymentGateway->name;

        $paymentGatewayRefId = $this->createUserPaymentGatewayRefId(user_data: $user_data, number: $number, paymentGateway: $paymentGateway);
        if (!$paymentGatewayRefId) return $this->failed(msg: "User error!");

        $craftyId = Order::generateCraftyId();

        $newAddOns = [];

        $notes = [
            'craftyId' => $craftyId,
            'user_id' => $user_data->uid,
            'plan_id' => $assetDetails,
            'amount' => $amount,
            'currency' => $currency,
            'fromWallet' => 0,
            'coins' => 0,
            'from' => $from,
            'pay_mode' => $payMode,
            'code' => $promoCodeId,
            'ip' => $ipData['ip'],
            'seats' => $seats,
            'eventData' => json_encode($eventData),
            'add_ons' => $newAddOns,
        ];

        if ($this->isTester()) {
            $amount = 1;
        }

        if ($gatewayType === 'razorpay') {
            $datas = RazorpayGateway::createRazorpayOrder(
                paymentGateway: $paymentGateway,
                user_data: $user_data,
                craftyId: $craftyId,
                amount: $amount,
                currency: $currency,
                razorpayCusId: $paymentGatewayRefId,
                description: $description,
            );
        } else if ($gatewayType === 'stripe') {
            $datas = StripeGateway::createStripeOrder(
                paymentGateway: $paymentGateway,
                craftyId: $craftyId,
                paymentMethodId: $paymentMethodId,
                amount: $amount,
                currency: $currency,
                stripeCusId: $paymentGatewayRefId,
            );
        } else if ($gatewayType === 'phonepe_pg') {
            $datas = PhonePeGateway::createPhonepe(
                paymentGateway: $paymentGateway,
                craftyId: $craftyId,
                amount: $amount,
            );
        } else {
            return $this->failed(datas: ['gateway' => $gatewayType]);
        }

        if (empty($datas) || is_string($datas)) return $this->failed(msg: "Empty Data", datas: ['datas' => $datas, 'paymentDetails' => $paymentDetails, 'gatewayType' => $gatewayType]);

        $orderData = [
            'emp_id' => Order::getOrderAssignEmpId($this->uid),
            'user_id' => $this->uid,
            'plan_id' => $assetDetails,
            'crafty_id' => $craftyId,
            'contact_no' => $user_data->contact_no,
            'order_id' => $datas['id'] ?? null,
            'subscription_id' => $datas['subscription_id'] ?? null,
            'gateway' => $gatewayType,
            'status' => 'pending',
            'currency' => $currency,
            'amount' => $amount,
            'type' => $payMode,
            'has_offer' => 0,
            'raw_notes' => json_encode($notes),
            'show_data' => $fromAllowedIp ? 0 : 1,
            'url' => $url,
            'fbc' => $request->cookie('_fbclid'),
            'fbp' => $request->cookie('_caid'),
            'gclid' => $request->cookie('_gclid'),
            'wbraid' => $request->cookie('_wbraid'),
            'gbraid' => $request->cookie('_gbraid'),
            'gcl_au' => $request->cookie('_gcl_au'),
            'ga' => $request->cookie('_ga'),
            'userAgent' => $request->header('User-Agent', 'Unknown'),
            'ip_address' => $ipData['ip'],
        ];

        if (isset($datas['key'])) {
            $orderData[$datas['key']] = $datas['id'];
        }

        Order::create($orderData);

        return $this->successed(datas: ['data' => $datas['data'], 'type' => $gatewayType]);
    }

    function webhook(Request $request): array|string
    {

        $method = $request->get('method');
        $transaction_id = $request->get('transaction_id');
        $isManual = $request->has('isManual') ? $request->get('isManual') : 1;

        if ($method == null || $transaction_id == null) {
            return $this->failed(msg: "Parameters missing!");
        }

        $data = $this->enterTransData($request, $transaction_id, $method, $isManual);

        $success = $data['success'];
        $msg = $data['msg'];
        $is_trial = $data['is_trial'] ?? false;
        $days = $data['days'] ?? 0;

        return $this->sendResponse(statusCode: $success ? 200 : 401, success: $success, msg: $msg, datas: ['is_trial' => $is_trial, 'days' => $days]);
    }

    private function getPaymentDetails(Request $request, UserData $user_data, $currency, $assetDetails, $code, $url, $seats): array
    {

        $tempAssetData = json_decode($assetDetails, true);
        $amount = 0;
        $description = 'Premium Assets';

        if ($tempAssetData && is_array($tempAssetData)) {
            $assetDatas = $this->getAssetData(RateController::getRates(), $tempAssetData, $currency);
            if (!$assetDatas['success']) return $this->failed(msg: $assetDatas['message'], showDecoded: true);

            $payMode = $assetDatas['payMode'];
            $amount = $assetDatas['message'];
            $assetDetails = json_encode($assetDatas['datas']);
        }

        if (empty($payMode)) return $this->failed(msg: 'Invalid Mode', showDecoded: true);
        if ($amount <= 0) return $this->failed(msg: 'Amount Error', showDecoded: true);

        $amount = $amount * $seats;

        $promoCodeId = 0;
        $promoCode = $this->cpm($amount, $code, $currency, true);
        if ($promoCode['success']) {
            $amount = $promoCode['amount'];
            $promoCodeId = $promoCode['id'];
        }

        $eventData = [
            '_fbclid' => $request->cookie('_fbclid'),
            '_caid' => $request->cookie('_caid'),
            '_gclid' => $request->cookie('_gclid'),
            '_wbraid' => $request->cookie('_wbraid'),
            '_gbraid' => $request->cookie('_gbraid'),
            '_ga' => $request->cookie('_ga'),
            '_gcl_au' => $request->cookie('_gcl_au'),
            'userAgent' => $request->header('User-Agent', 'Unknown'),
            'clientIp' => ApiController::findIp($request) ?? '0.0.0.0',
            'url' => $url
        ];

        return $this->successed(datas: [
            'amount' => $amount,
            'payMode' => $payMode,
            'promoCodeId' => $promoCodeId,
            'description' => $description,
            'eventData' => $eventData,
            'assetDetails' => $assetDetails,
        ], showDecoded: true);
    }

    private function getAssetData($rates, array $assetDetails, $currency): array
    {
        $payMode = null;
        $amount = 0;
        $paymentDatas = [];
        /** @var array $assetDetail */
        foreach ($assetDetails as $assetDetail) {
            if ($assetDetail['type'] == 4 || $assetDetail['type'] == '4') {
                $desData = VideoTemplate::where('string_id', $assetDetail['id'])->first();
                if ($desData) {
                    $size = $desData->pages;
                    $pyt = RateController::getVideoRates($rates, $size);
                    $pyt['id'] = $desData->string_id;
                    $pyt['type'] = 4;
                    $paymentDatas[] = $pyt;
                    $amount += $currency == 'INR' ? $pyt['inrVal'] : $pyt['usdVal'];
                    if (is_null($payMode)) $payMode = "video";
                } else {
                    $response['success'] = false;
                    $response['message'] = 'Data Error';
                    return $response;
                }
            } else {
                $response['success'] = false;
                $response['message'] = 'Invalid type';
                return $response;
            }
        }

        if ($amount <= 0 || is_null($payMode)) {
            $response['success'] = false;
            $response['message'] = 'Invalid amount';
        } else {
            $response['success'] = true;
            $response['message'] = $amount;
            $response['datas'] = $paymentDatas;
            $response['payMode'] = $payMode;
        }
        return $response;
    }

    private function cpm($amount, $code, $currency, $provideID = false): array
    {
        if ($amount == null || $code == null) {
            return $this->failed(msg: "Parameters missing!", showDecoded: true);
        }

        if (!is_numeric($amount)) {
            return $this->failed(msg: "Amount invalid!", showDecoded: true);
        }

        $promoData = PromoCode::where('promo_code', $code)->first();

        if (!$promoData || $promoData->status == 0) {
            return $this->failed(msg: "Code is invalid!", showDecoded: true);
        }

        if ($promoData->expiry_date) {
            $expiryDate = Carbon::createFromFormat('Y-m-d', $promoData->expiry_date);

            if ($expiryDate->isPast()) {
                return $this->failed(msg: "Code is expired!", showDecoded: true);
            }
        }

        $isInr = $currency === "INR";

        $curSymbol = $isInr ? '₹' : '$';
        $minimumPurchase = $isInr ? $promoData->min_cart_inr : $promoData->min_cart_usd;
        $discountUpto = $isInr ? $promoData->disc_upto_inr : $promoData->disc_upto_usd;

        if ($amount < $minimumPurchase) {
            return $this->failed(msg: "Minimum order value is $curSymbol$minimumPurchase", showDecoded: true);
        }

        if ($currency === 'INR') $discount = round($amount * $promoData->disc / 100);
        else $discount = $amount * $promoData->disc / 100;

        if ($discountUpto !== 0) $discount = min($discount, $discountUpto);

        $discAmount = $amount - $discount;
        $discAmount = number_format((float)$discAmount, 2, '.', '');

        $response = [
            'discount' => $promoData->disc,
            'amount' => $discAmount,
            'saving' => $discount,
            'msg' => "You have saved ",
            'curSymbol' => $curSymbol,
            'amountStr' => $curSymbol . $discAmount,
            'additional_days' => $promoData->additional_days
        ];

        if ($provideID) $response['id'] = $promoData->id;

        return $this->successed(msg: "Promo code applied", datas: $response, showDecoded: true);
    }

    private function createUserPaymentGatewayRefId(UserData $user_data, mixed $number, PaymentGateway $paymentGateway): string|null
    {

        $cusIdUpdate = false;

        $gatewayType = $paymentGateway->name;
        $gatewayClient = $paymentGateway->client;

        if ($gatewayType === 'razorpay') {
            $paymentGatewayRefId = $user_data->razorpay_cus_id;
        } else if ($gatewayType === 'stripe') {
            $paymentGatewayRefId = $user_data->stripe_cus_id;
        } else {
            $paymentGatewayRefId = $user_data->email;
        }

        if (empty($paymentGatewayRefId)) {
            $cusIdUpdate = true;

            $pattern = '/^[a-zA-Z ]{3,50}$/';
            $customer_name = $user_data->name;

            if (!preg_match($pattern, $customer_name)) {
                $customer_name = "CraftyArt";
            }

            if ($gatewayType === 'razorpay') {
                if (!$user_data->country_code) {
                    $cusRes = $gatewayClient->customer->create(array('fail_existing' => '0', 'name' => $customer_name, 'email' => $user_data->email));
                } else {
                    $cusRes = $gatewayClient->customer->create(array('fail_existing' => '0', 'name' => $customer_name, 'contact' => $user_data->country_code . $user_data->number));
                }
                $paymentGatewayRefId = $cusRes['id'];

            } else if ($gatewayType === 'stripe') {
                if (!$user_data->country_code) {
                    $cusRes = $gatewayClient->customers->create(array('name' => $user_data->name, 'email' => $user_data->email));
                } else {
                    $cusRes = $gatewayClient->customers->create(array('name' => $user_data->name, 'phone' => $user_data->country_code . $user_data->number));
                }
                $paymentGatewayRefId = $cusRes['id'];
            }

            if (empty($paymentGatewayRefId)) return null;
        }

        $numberUpdate = !is_null($number) && is_null($user_data->contact_no);

        if ($cusIdUpdate || $numberUpdate) {
            $res = UserData::find($user_data->id);
            if ($cusIdUpdate) {
                if ($gatewayType === 'razorpay') {
                    $res->razorpay_cus_id = $paymentGatewayRefId;
                } else if ($gatewayType === 'stripe') {
                    $res->stripe_cus_id = $paymentGatewayRefId;
                }
            }
            if ($numberUpdate) {
                $res->contact_no = $number;
                $user_data->contact_no = $number;
            }
            $res->save();
        }

        return $paymentGatewayRefId;
    }

    public function enterTransData(Request $request, $transaction_id, $method, $isManual, array|null $metaData = null): array
    {

        $successRes = ['success' => true, 'msg' => 'Purchase successfully.'];
        $errorRes = ['success' => false, 'msg' => 'Payment not valid.'];

        $paymentGateway = PaymentGateway::initByGateway(strtolower($method), null);
        if (!$paymentGateway) return $this->failed(msg: "Stripe init failed");

        $metaData = $metaData ?? $this->getMetaData($paymentGateway, $transaction_id);

        if (!$metaData['isSuccessed']) return ['success' => false, 'msg' => 'Payment not valid.', 'metaData' => $metaData];

        $user_data = $metaData['user_data'];
        $transaction_id = $metaData['transaction_id'];

        if (MasterPurchaseHistory::whereTransactionId($transaction_id)->exists()) return $successRes;

        $promo_code_id = $metaData['promoCodeId'];
        $totalPaidAmount = $metaData['paidAmount'];
        $totalNetAmount = $metaData['netAmount'];
        $feePercentage = $metaData['feePercentage'];
        $exchangeRate = $metaData['exchange_rate'];
        $fees = $metaData['fees'];
        $details = $metaData['details'];
        $sales_person_id_for_report = $metaData['sales_person_id_for_report'];

        if ($totalNetAmount < 0) return $errorRes;

        $eventData = is_array($details->eventData) ? $details->eventData : json_decode($details->eventData);
        $fbcId = $eventData->_fbclid ?? null;
        $gclId = $eventData->_gclid ?? $eventData->_wbraid ?? $eventData->_gbraid ?? null;
        $url = $eventData->url ?? null;

        $contact = $metaData['contact'];

        $currency_code = $details->currency;
        $fromWhere = $details->from ?? 'Web';
        $assetDetails = json_decode($details->plan_id);

        foreach ($assetDetails as $assetDetail) {

            $paidAmount = $assetDetail->inrVal;

            if (strtoupper($currency_code) != "INR") $paidAmount = $assetDetail->usdVal * $exchangeRate;

            $netAmount = $paidAmount - ($paidAmount * $feePercentage / 100);

            $this->savePurchaseData(
                user_data: $user_data,
                contact: $contact,
                assetDetail: $assetDetail,
                transaction_id: $transaction_id,
                currency_code: $currency_code,
                paidAmount: $paidAmount,
                netAmount: $netAmount,
                promo_code_id: $promo_code_id,
                method: $method,
                fromWhere: $fromWhere,
                isManual: $isManual,
                fbcId: $fbcId,
                gclId: $gclId,
                sales_person_id_for_report: $sales_person_id_for_report,
            );
        }

        $successRes['taData'] = $metaData;
        return $successRes;
    }

    public function getMetaData(PaymentGateway $paymentGateway, $transaction_id): array
    {
        $isSuccessed = false;
        $promoCodeId = 0;
        $paidAmount = 0;
        $netAmount = 0;
        $feePercentage = 0;
        $details = null;
        $name = $user_data->name ?? null;
        $email = $user_data->email ?? null;
        $contact = null;
        $error = null;
        $uid = null;
        $sales_person_id_for_report = 0;
        $sales_person_id = 0;
        $paymentIntentId = null;
        $exchange_rate = 1;
        $fees = 0;
        try {
            if (strtolower($paymentGateway->name) === 'stripe') {

                /** @var StripeClient $stripeClient */
                $stripeClient = $paymentGateway->client;

                if (str_starts_with($transaction_id, 'pi_')) {
                    $paymentIntent = $stripeClient->paymentIntents->retrieve($transaction_id);
                    if ($paymentIntent && isset($paymentIntent->latest_charge)) {
                        $charge = $stripeClient->charges->retrieve($paymentIntent->latest_charge);
                        $transaction_id = $charge->balance_transaction;
                    }
                }

                $transaction = $stripeClient->balanceTransactions->retrieve($transaction_id);
                if ($transaction) {

                    if (isset($transaction['source'])) {

                        $exchange_rate = $transaction->exchange_rate ?? 1;
                        $fees = ($transaction->fee / 100) ?? 0;

                        $charge = $stripeClient->charges->retrieve($transaction['source']);
                        $isSuccessed = !($charge->amount_refunded > 0);

                        $paidAmount = $transaction['amount'] / 100;
                        $netAmount = $transaction['net'] / 100;

                        if ($paidAmount > 0) {
                            $feePercentage = (($paidAmount - $netAmount) / $paidAmount) * 100;
                        }

                        $metadata = $charge->metadata->toArray();
                        $craftyId = $metadata['craftyId'] ?? null;

                        $query = Order::whereStripeTxnId($transaction_id);
                        if (!empty($craftyId)) $query->orWhere('crafty_id', $craftyId);
                        $orderData = $query->first();

                        if ($orderData) {
                            $notes = $orderData->raw_notes;
                            if (isset($notes['code'])) {
                                $promoCodeId = $notes['code'];
                            }
                            $uid = $notes['user_id'] ?? $orderData->user_id;
                            $details = json_decode(json_encode($notes));
                            $sales_person_id_for_report = $orderData->emp_id;
                        }

                        $paymentMethod = $stripeClient->paymentMethods->retrieve($charge['payment_method']);

                        $paymentIntentId = $charge->payment_intent;
                        $name = $paymentMethod->billing_details->name ?? $name;
                        $email = $paymentMethod->billing_details->email ?? $email;
                        $contact = $paymentMethod->billing_details->phone ?? $contact;
                    }
                }
            } else if (strtolower($paymentGateway->name) === 'razorpay') {

                /** @var Api $stripeClient */
                $razorpay = $paymentGateway->client;

                $transaction = $razorpay->payment->fetch($transaction_id);
                if ($transaction && ($transaction['status'] == 'authorized' || $transaction['status'] == 'captured')) {

                    $isSuccessed = !($transaction['amount_refunded'] > 0);

//                    $isSuccessed = true;
                    $paidAmount = $transaction['amount'] / 100;
                    $netAmount = ($transaction['amount'] - $transaction['fee'] - $transaction['tax']) / 100;

                    $fees = $transaction['fee'] + $transaction['tax'];

                    if ($paidAmount > 0) {
                        $feePercentage = (($paidAmount - $netAmount) / $paidAmount) * 100;
                    }

                    $notes = $payment['notes'] ?? [];
                    $craftyId = $notes['craftyId'] ?? null;

                    $query = Order::whereRazorpayPaymentId($transaction_id)->orWhere('payment_id', $transaction_id);
                    if (!empty($craftyId)) $query->orWhere('crafty_id', $craftyId);
                    $orderData = $query->first();

                    if ($orderData) {
                        $notes = $orderData->raw_notes;
                        if (isset($notes['code'])) {
                            $promoCodeId = $notes['code'];
                        }
                        $uid = $notes['user_id'] ?? null;
                        $sales_person_id = $notes['sales_person_id'] ?? 0;
                        $details = json_decode(json_encode($notes));

                        if (empty($sales_person_id)) $sales_person_id_for_report = $orderData->emp_id;
                    }

                    $email = $transaction['email'] ?? $email;
                    $contact = $transaction['contact'] ?? $contact;
                }

            } else if (strtolower($paymentGateway->name) === 'phonepe_pg') {

                /** @var StandardCheckoutClient $phonePePaymentsClient */
                $phonePePaymentsClient = $paymentGateway->client;

                try {
                    $statusCheckResponse = $phonePePaymentsClient->getOrderStatus($transaction_id, true);
                    if ($statusCheckResponse->getState() === 'COMPLETED') {
                        $isSuccessed = true;
                        $paidAmount = $statusCheckResponse->getAmount() / 100;
                        $netAmount = $paidAmount;

                        $query = Order::whereOrderId($transaction_id)->orWhere('crafty_id', $transaction_id);
                        $orderData = $query->first();

                        if ($orderData) {
                            $notes = $orderData->raw_notes;
                            if (isset($notes['code'])) {
                                $promoCodeId = $notes['code'];
                            }
                            $uid = $notes['user_id'] ?? null;
                            $sales_person_id = $notes['sales_person_id'] ?? 0;
                            $details = json_decode(json_encode($notes));
                            if (empty($sales_person_id)) $sales_person_id_for_report = $orderData->emp_id;
                        }
                    }

                } catch (PhonePeException $e) {
                    $error = $e->getMessage();
                    $isSuccessed = false;
                }
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        $user_data = UserData::where("uid", $uid)->first();
        if ($user_data) {
            $name = $name ?? $user_data->name;
            $email = $email ?? $user_data->email;
            $contact = $contact ?? $user_data->contact_no;
        } else {
            $isSuccessed = false;
        }

        if (empty($sales_person_id)) $sales_person_id = 0;

        return [
            'isSuccessed' => $isSuccessed,
            'promoCodeId' => $promoCodeId,
            'transaction_id' => $transaction_id,
            'paidAmount' => $paidAmount,
            'netAmount' => $netAmount,
            'feePercentage' => $feePercentage,
            'exchange_rate' => $exchange_rate,
            'fees' => $fees,
            'details' => $details,
            'name' => $name,
            'email' => $email,
            'contact' => $contact,
            'error' => $error,
            'user_data' => $user_data,
            'paymentMethodId' => $paymentIntentId,
            'method' => $paymentGateway->name,
            'sales_person_id' => $sales_person_id,
            'sales_person_id_for_report' => $sales_person_id_for_report,
            'orderData' => $statusCheckResponse ?? null,
        ];
    }

    private function savePurchaseData(
        UserData $user_data,
                 $contact,
                 $assetDetail,
                 $transaction_id,
                 $currency_code,
                 $paidAmount,
                 $netAmount,
                 $promo_code_id,
                 $method,
                 $fromWhere,
                 $isManual,
                 $fbcId,
                 $gclId,
                 $sales_person_id_for_report): void
    {


        if (MasterPurchaseHistory::where('transaction_id', $transaction_id)->where('product_id', $assetDetail->id)->exists()) return;

        $payment_id = HelperController::generateRandomId(prefix: 'txn_', modelSource: MasterPurchaseHistory::class);

        if (!strcasecmp($currency_code, "INR")) {
            $currency_code = "INR";
            $amount = $assetDetail->inrVal;
        } else {
            $currency_code = "USD";
            $amount = $assetDetail->usdVal;
        }

        $lockKey = "save_data_{$transaction_id}_{$assetDetail->id}";
        $lock = Cache::lock($lockKey, 1); // Lock for 10 seconds

        try {
            if ($lock->get()) {
                if (MasterPurchaseHistory::where('transaction_id', $transaction_id)->where('product_id', $assetDetail->id)->exists()) return;

                MasterPurchaseHistory::firstOrCreate(
                    [
                        'transaction_id' => $transaction_id,
                        'product_id' => $assetDetail->id,
                    ],
                    [
                        'user_id' => $user_data->uid,
                        'emp_id' => $sales_person_id_for_report,
                        'contact_no' => $contact,
                        'product_id' => $assetDetail->id,
                        'product_type' => MasterPurchaseHistory::$types[$assetDetail->type],
                        'transaction_id' => $transaction_id,
                        'payment_id' => $payment_id,
                        'currency_code' => $currency_code,
                        'amount' => $amount,
                        'paid_amount' => $paidAmount,
                        'net_amount' => $netAmount,
                        'promo_code_id' => $promo_code_id,
                        'payment_method' => $method,
                        'from_where' => $fromWhere,
                        'fbc' => $fbcId,
                        'gclid' => $gclId,
                        'isManual' => $isManual,
                        'status' => 1,
                    ]
                );
            }

        } catch (Exception $e) {

        }
    }

    public static function removeOrdersDuplicate(Order $order): void
    {
        Order::whereUserId($order->user_id)->whereIn('status', ['pending', 'failed'])->update(['status' => 'override']);
    }
}
