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
 * @method static Builder|OTPTable newModelQuery()
 * @method static Builder|OTPTable newQuery()
 * @method static Builder|OTPTable query()
 * @method static Builder|OTPTable whereCreatedAt($value)
 * @method static Builder|OTPTable whereId($value)
 * @method static Builder|OTPTable whereMail($value)
 * @method static Builder|OTPTable whereMsg($value)
 * @method static Builder|OTPTable whereOtp($value)
 * @method static Builder|OTPTable whereStatus($value)
 * @method static Builder|OTPTable whereType($value)
 * @method static Builder|OTPTable whereUpdatedAt($value)
 * @mixin Eloquent
 */
class OTPTable extends Model
{
    protected $connection = 'mysql';
    protected $table = 'otp_tables';

    use HasFactory;
}
