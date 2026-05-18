<?php

namespace App\Http\Controllers\Utils;

enum FacebookEvent: string
{
    case INITIATE_CHECKOUT = 'Initiate Checkout';
    case PURCHASE = 'Purchase';
}
