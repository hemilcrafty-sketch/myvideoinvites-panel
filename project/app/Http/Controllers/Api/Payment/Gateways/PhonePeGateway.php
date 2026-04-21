<?php

namespace App\Http\Controllers\Api\Payment\Gateways;

use App\Http\Controllers\Api\Utils\ApiController;
use App\Service\PaymentGateway;
use Exception;
use PhonePe\payments\v2\models\request\builders\StandardCheckoutPayRequestBuilder;
use PhonePe\payments\v2\standardCheckout\StandardCheckoutClient;

class PhonePeGateway extends ApiController
{

    public static function createPhonepe(
        PaymentGateway $paymentGateway,
                       $craftyId,
                       $amount,
    ): string|array|false
    {

        /** @var StandardCheckoutClient $phonePePaymentsClient */
        $phonePePaymentsClient = $paymentGateway->client;

        try {

            $phonePeRequest = StandardCheckoutPayRequestBuilder::builder()
                ->merchantOrderId($craftyId)
                ->amount($amount * 100)
                ->redirectUrl('https://www.myvideoinvites.com')
                ->message("Phone Pe Payment Integration")
                ->udf1($craftyId)
                ->build();
            $response = $phonePePaymentsClient->pay($phonePeRequest);
            $response->transactionId = $craftyId;

            return ['id' => $response->getOrderId(), 'data' => $response];

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
