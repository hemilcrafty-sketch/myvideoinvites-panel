<?php

namespace App\Models\Revenue;

use App\Models\UserData;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Revenue\PurchaseTransaction
 *
 * @property int $id
 * @property int $emp_id
 * @property int $by_sales_team
 * @property string $user_id
 * @property string|null $contact_no
 * @property string|null $subscription_id
 * @property string|null $order_id
 * @property string $transaction_id
 * @property string $payment_id
 * @property string $currency_code
 * @property float $amount
 * @property float|null $paid_amount
 * @property float $net_amount
 * @property float $fee_percentage
 * @property string|null $next_amount
 * @property int $promo_code_id
 * @property string $payment_method
 * @property string $from_where
 * @property string|null $fbc
 * @property string|null $gclid
 * @property int $isManual
 * @property string|null $url
 * @property int $validity
 * @property int $yearly
 * @property string|null $plan_limit
 * @property string|null $raw_notes
 * @property int $is_trial
 * @property int $is_e_mandate
 * @property string|null $payment_status
 * @property int $refund_by
 * @property int $status
 * @property int $total_purchases
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string|null $expired_at
 * @property-read UserData|null $userData
 * @property-read \Illuminate\Database\Eloquent\Collection<int, PurchaseTransactionProduct> $products
 * @method static Builder|PurchaseTransaction newModelQuery()
 * @method static Builder|PurchaseTransaction newQuery()
 * @method static Builder|PurchaseTransaction query()
 * @mixin Eloquent
 */
class PurchaseTransaction extends Model
{
    use HasFactory;

    protected $table = 'purchase_transactions';
    public static array $types = [
        '0' => 'template',
        '1' => 'font',
        '2' => 'sticker',
        '3' => 'background',
        '4' => 'video',
        '5' => 'caricature',
        '6' => 'ai_credit',
        '7' => 'old_sub',
        '8' => 'new_sub',
        '9' => 'offer',
        '11' => 'vendor_panel',
    ];

    protected $guarded = [];

    public function userData(): BelongsTo
    {
        return $this->belongsTo(UserData::class, 'user_id', 'uid');
    }

    public function products(): HasMany
    {
        return $this->hasMany(PurchaseTransactionProduct::class, 'purchase_transaction_id');
    }
}
