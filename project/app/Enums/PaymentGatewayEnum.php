<?php

namespace App\Enums;

enum PaymentGatewayEnum: string
{
  case PHONEPE = 'phonepe';
  case RAZORPAY = 'razorpay';
  case STRIPE = 'stripe';
  case PAYTM = 'paytm';
}
