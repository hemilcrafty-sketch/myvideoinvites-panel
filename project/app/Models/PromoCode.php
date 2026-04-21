<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\PromoCode
 *
 * @property int $id
 * @property string|null $user_id
 * @property string $promo_code
 * @property int $disc
 * @property int $additional_days
 * @property string|null $type
 * @property int $status
 * @property string|null $expiry_date
 * @property int $disc_upto_inr
 * @property int $min_cart_inr
 * @property int $disc_upto_usd
 * @property int $min_cart_usd
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @method static Builder|PromoCode newModelQuery()
 * @method static Builder|PromoCode newQuery()
 * @method static Builder|PromoCode query()
 * @method static Builder|PromoCode whereAdditionalDays($value)
 * @method static Builder|PromoCode whereCreatedAt($value)
 * @method static Builder|PromoCode whereDisc($value)
 * @method static Builder|PromoCode whereDiscUptoInr($value)
 * @method static Builder|PromoCode whereDiscUptoUsd($value)
 * @method static Builder|PromoCode whereExpiryDate($value)
 * @method static Builder|PromoCode whereId($value)
 * @method static Builder|PromoCode whereMinCartInr($value)
 * @method static Builder|PromoCode whereMinCartUsd($value)
 * @method static Builder|PromoCode wherePromoCode($value)
 * @method static Builder|PromoCode whereStatus($value)
 * @method static Builder|PromoCode whereType($value)
 * @method static Builder|PromoCode whereUpdatedAt($value)
 * @method static Builder|PromoCode whereUserId($value)
 * @mixin Eloquent
 */
class PromoCode extends Model
{
    protected $table = 'promo_codes';
    protected $connection = 'mysql';
    use HasFactory;
}
