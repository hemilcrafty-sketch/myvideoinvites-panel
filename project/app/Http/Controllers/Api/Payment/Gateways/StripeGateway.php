<?php

namespace App\Http\Controllers\Api\Payment\Gateways;

use App\Service\PaymentGateway;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class StripeGateway
{

    public static function createStripeOrder(
        PaymentGateway $paymentGateway,
                       $craftyId,
                       $paymentMethodId,
                       $amount,
                       $currency,
                       $stripeCusId,
    ): array
    {

        /** @var StripeClient $stripeClient */
        $stripeClient = $paymentGateway->client;

        $payData = [
            'amount' => (int)($amount * 100),
            'currency' => $currency,
            'customer' => $stripeCusId,
            'payment_method' => $paymentMethodId,
            'description' => 'Payment for Craftyart Service',
            'return_url' => 'https://www.craftyartapp.com',
            'confirmation_method' => 'automatic',
            'setup_future_usage' => 'off_session',
            'confirm' => true,
            'metadata' => [
                'craftyId' => $craftyId
            ],
        ];

        try {
            $paymentIntent = $stripeClient->paymentIntents->create($payData);
        } catch (ApiErrorException $e) {
            return [];
        }

        return ['id' => $paymentIntent->id, 'data' => $paymentIntent];
    }
}
