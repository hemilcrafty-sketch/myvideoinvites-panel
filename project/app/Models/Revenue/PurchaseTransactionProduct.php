<?php

namespace App\Models\Revenue;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Revenue\PurchaseTransactionProduct
 *
 * @property int $id
 * @property int $purchase_transaction_id
 * @property string $product_id
 * @property string $product_type
 * @property float $amount
 * @property float $paid_amount
 * @property float $desc_amount
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read PurchaseTransaction $transaction
 * @method static Builder|PurchaseTransactionProduct newModelQuery()
 * @method static Builder|PurchaseTransactionProduct newQuery()
 * @method static Builder|PurchaseTransactionProduct query()
 * @mixin Eloquent
 */
class PurchaseTransactionProduct extends Model
{
    use HasFactory;

    protected $table = 'purchase_transaction_products';

    protected $guarded = [];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(PurchaseTransaction::class, 'purchase_transaction_id');
    }
}
