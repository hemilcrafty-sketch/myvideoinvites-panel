<?php

namespace App\Http\Controllers\Api\Payment\Gateways;

use App\Models\UserData;
use App\Service\PaymentGateway;
use Razorpay\Api\Api;

class RazorpayGateway
{

    public static function createRazorpayOrder(
        PaymentGateway $paymentGateway,
        UserData       $user_data,
                       $craftyId,
                       $amount,
                       $currency,
                       $razorpayCusId,
                       $description,
    ): array
    {

        /** @var Api $razorpay */
        $razorpay = $paymentGateway->client;
        $credentials = $paymentGateway->credentials;

        $datas = $razorpay->order->create([
            'amount' => $amount * 100,
            'currency' => $currency,
            'notes' => ['craftyId' => $craftyId]
        ]);

        $orderId = $datas['id'];

        $payment_data = [];
        $payment_data['key'] = $credentials['key_id'];
        $payment_data['name'] = "MyVideoInvites";
        $payment_data['description'] = $description;
        $payment_data['customer_id'] = $razorpayCusId;
        $payment_data['order_id'] = $datas['id'];
        $payment_data['remember_customer'] = True;
        $payment_data['notes'] = ['craftyId' => $craftyId];
        $payment_data['method'] = [ // Add this to restrict to UPI
            'upi' => true,
            'card' => true,
            'netbanking' => true,
            'wallet' => true
        ];
        $payment_data['config'] = [
            'display' => [
                'preferences' => [
                    'show_default_blocks' => true,
                    'payment_options_order' => ['upi', 'card', 'netbanking', 'wallet'],
                    'highlight' => ['upi', 'card'],
                ]
            ]
        ];

        $payment_data['prefill'] = [
            'email' => $user_data->email,
            'contact' => $user_data->contact_no
        ];

        return ['id' => $orderId, 'data' => $payment_data];
    }

}
