<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Utils\ApiController;
use App\Models\TemplateRate;
use Illuminate\Support\Collection;

class RateController extends ApiController
{

    public static function getRates(): Collection
    {

        $rateTypes = ['video'];

        return TemplateRate::whereIn('name', $rateTypes)
            ->pluck('value', 'name')
            ->map(function ($value) {
                $decoded = json_decode($value);
                return $decoded ?? [];
            });
    }

    public static function getVideoRates($rates, $size): array
    {
        $value = $rates['video'] ?? TemplateRate::getRates("video");

        if ($size == 0) {
            $size = 1;
        }

        $extraPage = $size - 1;

        $inrAmount = $value->inr->base_price + ($value->inr->page_price * $extraPage);
        $usdAmount = $value->usd->base_price + ($value->usd->page_price * $extraPage);
        $inrAmount = min($inrAmount, $value->inr->max_price);
        $usdAmount = min($usdAmount, $value->usd->max_price);

        $payment['inrVal'] = $inrAmount;
        $payment['usdVal'] = $usdAmount;
        $payment['inrAmount'] = '₹' . $inrAmount;
        $payment['usdAmount'] = '$' . $usdAmount;

        return $payment;
    }

}
