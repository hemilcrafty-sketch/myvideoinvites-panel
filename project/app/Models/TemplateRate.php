<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\TemplateRate
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @method static Builder|TemplateRate newModelQuery()
 * @method static Builder|TemplateRate newQuery()
 * @method static Builder|TemplateRate query()
 * @method static Builder|TemplateRate whereCreatedAt($value)
 * @method static Builder|TemplateRate whereId($value)
 * @method static Builder|TemplateRate whereName($value)
 * @method static Builder|TemplateRate whereType($value)
 * @method static Builder|TemplateRate whereUpdatedAt($value)
 * @method static Builder|TemplateRate whereValue($value)
 * @mixin Eloquent
 */
class TemplateRate extends Model
{
    protected $table = 'template_rates';
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'value',
    ];

    public static function getRates($name)
    {
        $rate = TemplateRate::where('name', $name)->pluck('value')->first();
        if ($rate) {
            return json_decode($rate);
        } else {
            return self::getDefaultValue();
        }
    }


    private static function checkRateStructure(array $default, array $data): bool
    {
        foreach ($default as $currency => $fields) {
            if (!isset($data[$currency]) || !is_array($data[$currency])) {
                return false;
            }

            foreach ($fields as $key => $value) {
                if (!array_key_exists($key, $data[$currency])) {
                    return false;
                }
            }
        }
        return true;
    }

    public static function getDefaultValue()
    {
        return json_decode(json_encode([
            'inr' => [
                'base_price' => 99,
                'page_price' => 100,
                'max_price' => 399,
                'editor_choice' => 0,
                'animation' => 0,
                'caricature' => 0
            ],
            'usd' => [
                'base_price' => 4.99,
                'page_price' => 2,
                'max_price' => 8.99,
                'editor_choice' => 0,
                'animation' => 0,
                'caricature' => 0,
            ],
        ]));
    }
}
