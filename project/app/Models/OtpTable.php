<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\OtpTable
 *
 * @property int $id
 * @property string|null $mail
 * @property string|null $otp
 * @property string|null $msg
 * @property string|null $type
 * @property int|null $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|OtpTable newModelQuery()
 * @method static Builder|OtpTable newQuery()
 * @method static Builder|OtpTable query()
 * @method static Builder|OtpTable whereCreatedAt($value)
 * @method static Builder|OtpTable whereId($value)
 * @method static Builder|OtpTable whereMail($value)
 * @method static Builder|OtpTable whereMsg($value)
 * @method static Builder|OtpTable whereOtp($value)
 * @method static Builder|OtpTable whereStatus($value)
 * @method static Builder|OtpTable whereType($value)
 * @method static Builder|OtpTable whereUpdatedAt($value)
 * @mixin Eloquent
 */
class OtpTable extends Model
{
    protected $connection = 'mysql';
    use HasFactory;
}
