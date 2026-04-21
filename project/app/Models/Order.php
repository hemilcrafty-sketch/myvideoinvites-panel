<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Http\Controllers\Utils\HelperController;
use App\Models\Video\VideoTemplate;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property int $emp_id
 * @property string|null $user_id
 * @property string|null $plan_id
 * @property string|null $contact_no
 * @property string|null $crafty_id
 * @property string|null $order_id
 * @property string|null $subscription_id
 * @property string|null $payment_id
 * @property string|null $gateway
 * @property string $status
 * @property string|null $amount
 * @property string|null $paid
 * @property string $currency
 * @property string $type
 * @property int $has_offer
 * @property string|null $url
 * @property string|null $fbc
 * @property string|null $fbp
 * @property string|null $gclid
 * @property string|null $wbraid
 * @property string|null $gbraid
 * @property string|null $gcl_au
 * @property string|null $ga
 * @property string|null $userAgent
 * @property string|null $ip_address
 * @property array $raw_notes
 * @property int $email_template_count
 * @property int $whatsapp_template_count
 * @property string|null $followup_label
 * @property int $followup_call
 * @property string|null $followup_note
 * @property int $is_deleted
 * @property int $show_data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection $plan_items
 * @property-read UserData|null $user
 * @method static Builder|Order newModelQuery()
 * @method static Builder|Order newQuery()
 * @method static Builder|Order query()
 * @method static Builder|Order whereAmount($value)
 * @method static Builder|Order whereContactNo($value)
 * @method static Builder|Order whereCraftyId($value)
 * @method static Builder|Order whereCreatedAt($value)
 * @method static Builder|Order whereCurrency($value)
 * @method static Builder|Order whereEmailTemplateCount($value)
 * @method static Builder|Order whereEmpId($value)
 * @method static Builder|Order whereFbc($value)
 * @method static Builder|Order whereFbp($value)
 * @method static Builder|Order whereFollowupCall($value)
 * @method static Builder|Order whereFollowupLabel($value)
 * @method static Builder|Order whereFollowupNote($value)
 * @method static Builder|Order whereGa($value)
 * @method static Builder|Order whereGateway($value)
 * @method static Builder|Order whereGbraid($value)
 * @method static Builder|Order whereGclAu($value)
 * @method static Builder|Order whereGclid($value)
 * @method static Builder|Order whereHasOffer($value)
 * @method static Builder|Order whereId($value)
 * @method static Builder|Order whereIpAddress($value)
 * @method static Builder|Order whereIsDeleted($value)
 * @method static Builder|Order whereOrderId($value)
 * @method static Builder|Order wherePaid($value)
 * @method static Builder|Order wherePaymentId($value)
 * @method static Builder|Order wherePlanId($value)
 * @method static Builder|Order whereRawNotes($value)
 * @method static Builder|Order whereShowData($value)
 * @method static Builder|Order whereStatus($value)
 * @method static Builder|Order whereSubscriptionId($value)
 * @method static Builder|Order whereType($value)
 * @method static Builder|Order whereUpdatedAt($value)
 * @method static Builder|Order whereUrl($value)
 * @method static Builder|Order whereUserAgent($value)
 * @method static Builder|Order whereUserId($value)
 * @method static Builder|Order whereWbraid($value)
 * @method static Builder|Order whereWhatsappTemplateCount($value)
 * @mixin Eloquent
 */
class Order extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'orders';

    protected $guarded = [];

    protected static function booted(): void
    {
        static::created(function ($model) {
            // Runs after record is inserted
        });

        static::updated(function ($model) {
            // Runs after update
        });
    }

    public static function generateCraftyId(): string
    {
        return HelperController::generateRandomId(length: 20, prefix: 'txn_', modelSource: self::class, column: 'crafty_id');
    }

    public static function getOrderAssignEmpId($userId)
    {
        /** @var Order $previousOrderUser */
        $previousOrderUser = Order::whereUserId($userId)->whereNotNull('emp_id')
            ->where('emp_id', '!=', 0)
            ->orderBy('id', 'desc')
            ->first();

        if ($previousOrderUser) {
            $employee = User::whereId($previousOrderUser->emp_id)->whereStatus(1)->first();
            if ($employee) return $previousOrderUser->emp_id;
        }

        // Get Sales Users
        $salesUsers = User::whereUserType(UserRole::SALES->id())
            ->where('status', 1)
            ->orderBy('id')
            ->pluck('id')
            ->toArray();

        // Safety check
        if (empty($salesUsers)) {
            return 0;
        }

        $salesManagerIds = User::whereUserType(UserRole::SALES_MANAGER->id())
            ->where('status', 1)
            ->pluck('id')
            ->toArray();

        $lastOrder = Order::whereIn('status', ['pending', 'failed'])
            ->whereNotNull('emp_id')
            ->where('emp_id', '!=', 0)
            ->whereNotIn('emp_id', $salesManagerIds)
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastOrder || !$lastOrder->emp_id) {
            return $salesUsers[0];
        }

        $lastEmpId = $lastOrder->emp_id;

        $currentIndex = array_search($lastEmpId, $salesUsers);

        if ($currentIndex === false) {
            return $salesUsers[0];
        }

        $nextIndex = ($currentIndex + 1) % count($salesUsers);
        return $salesUsers[$nextIndex];
    }

    public function getRawNotesAttribute($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserData::class, 'user_id', 'uid');
    }


    /**
     * Accessor for video templates for video type orders
     */
    public function getVideoTemplates(): Collection
    {
        if ($this->type !== 'video') {
            return collect();
        }

        $templateData = json_decode($this->plan_id, true);
        if (!is_array($templateData)) {
            return collect();
        }

        $videoIds = collect($templateData)->pluck('id')->filter()->toArray();

        return VideoTemplate::whereIn('string_id', $videoIds)->get();
    }


    /**
     * Get formatted plan items (common interface for both designs and videos)
     */
    public function getPlanItemsAttribute(): Collection
    {
        return match ($this->type) {
            'video' => $this->getVideoTemplates(),
            default => collect()
        };
    }

    /**
     * Get contact number for the order
     */
    public function getContactNoAttribute(): ?string
    {
        if (!empty($this->contact_no)) {
            return $this->contact_no;
        }
        return $this->user?->contact_no;
    }

    public function getAutomationCommonData(): array
    {
        $planType = $this->type;
        $currency = $this->currency;

        // Check if user exists
        if (!$this->user) {
            return ['success' => false, 'message' => "User not found for order {$this->id}"];
        }

        $commonData['userData'] = [
            'name' => $this->user->name ?? '',
            'email' => $this->user->email ?? '',
            'password' => "",
        ];

        $paymentLink = "https://www.myvideoinvites.com/payment/$this->crafty_id";

        if ($planType == 'video') {
            // Always ensure we have a collection, not null
            $planItems = $this->planItems ?? collect();

            if ($planItems->isEmpty()) {
                return ['success' => false, 'message' => "No items found for {$planType} order {$this->id}"];
            }

            $templateData = json_decode($this->plan_id, true);
            if (!is_array($templateData)) {
                return ['success' => false, 'message' => "Invalid plan data for {$planType} order {$this->id}"];
            }

            $newArray = [];
            $paymentProps = [];

            foreach ($templateData as $item) {
                $planItem = $planItems->firstWhere(
                    $planType === 'template' ? 'string_id' : 'id',
                    $item['id']
                );

                if ($planItem) {
                    $paymentProps[] = ["id" => $item['id'], "type" => $planType === 'video' ? 1 : 0];
                    $newArray[] = [
                        "title" => $planItem->video_name,
                        "image" => HelperController::generatePublicUrl($planItem->video_thumb),
                        "width" => $planItem->width,
                        "height" => $planItem->height,
                        "amount" => $currency == "INR" ? $item['inrAmount'] : $item['usdAmount'],
                        "link" => "",
                    ];
                }
            }

            if (empty($newArray)) {
                return ['success' => false, 'message' => "No valid items found for order {$this->id}"];
            }

            $commonData['type'] = $planType;
            $commonData['data'] = [
                "templates" => $newArray,
                "amount" => ($currency == "INR" ? "₹" : "$") . $this->amount,
            ];
            $commonData['planType'] = $planType;
            $commonData['paymentProps'] = $paymentProps;

        } else {
            return ['success' => false, 'message' => "Invalid plan type provided for order {$this->id}"];
        }

        $commonData['link'] = $paymentLink;
        $commonData['waBtnLink'] = str_replace("https://www.myvideoinvites.com/", "", $paymentLink);

        return $commonData;
    }
}
