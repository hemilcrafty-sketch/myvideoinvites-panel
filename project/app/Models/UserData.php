<?php

namespace App\Models;

use App\Models\Revenue\MasterPurchaseHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * App\Models\UserData
 *
 * @property int $id
 * @property string $uid
 * @property string|null $user_name
 * @property string|null $bio
 * @property int $is_username_update
 * @property string $refer_id
 * @property string|null $stripe_cus_id
 * @property string|null $razorpay_cus_id
 * @property string|null $photo_uri
 * @property string $name
 * @property string|null $contact_no
 * @property int $contact_no_verified
 * @property string|null $email
 * @property string|null $password
 * @property string $login_type
 * @property string|null $utm_source
 * @property string|null $utm_medium
 * @property string|null $fldr_str
 * @property string|null $email_preference
 * @property int $profile_count
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, MasterPurchaseHistory> $purchaseLogs
 * @property-read int|null $purchase_logs_count
 * @property-read Collection<int, UserData> $referredUsers
 * @property-read int|null $referred_users_count
 * @property-read UserData|null $referrer
 * @property-read Collection<int, PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static Builder|UserData newModelQuery()
 * @method static Builder|UserData newQuery()
 * @method static Builder|UserData query()
 * @method static Builder|UserData whereBio($value)
 * @method static Builder|UserData whereContactNo($value)
 * @method static Builder|UserData whereContactNoVerified($value)
 * @method static Builder|UserData whereCreatedAt($value)
 * @method static Builder|UserData whereEmail($value)
 * @method static Builder|UserData whereEmailPreference($value)
 * @method static Builder|UserData whereFldrStr($value)
 * @method static Builder|UserData whereId($value)
 * @method static Builder|UserData whereIsUsernameUpdate($value)
 * @method static Builder|UserData whereLoginType($value)
 * @method static Builder|UserData whereName($value)
 * @method static Builder|UserData wherePassword($value)
 * @method static Builder|UserData wherePhotoUri($value)
 * @method static Builder|UserData whereProfileCount($value)
 * @method static Builder|UserData whereRazorpayCusId($value)
 * @method static Builder|UserData whereReferId($value)
 * @method static Builder|UserData whereStripeCusId($value)
 * @method static Builder|UserData whereUid($value)
 * @method static Builder|UserData whereUpdatedAt($value)
 * @method static Builder|UserData whereUserName($value)
 * @method static Builder|UserData whereUtmMedium($value)
 * @method static Builder|UserData whereUtmSource($value)
 * @mixin \Eloquent
 */
class UserData extends Authenticatable
{
    protected $table = 'user_data'; // Specify correct table name (singular)

    use HasApiTokens, HasFactory, Notifiable;



    public function getReviews(): HasMany
    {
        return $this->hasMany(Review::class, 'user_id', 'uid');
    }

    public function purchaseLogs(): HasMany
    {
        return $this->hasMany(MasterPurchaseHistory::class, 'user_id', 'uid');
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(UserData::class, 'referral_user_id', 'id');
    }

    public function referredUsers(): HasMany
    {
        return $this->hasMany(UserData::class, 'referral_user_id', 'id');
    }
}
