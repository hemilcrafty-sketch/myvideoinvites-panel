<?php

namespace App\Http\Controllers\Utils;

use App\Http\Controllers\Api\RateController;
use App\Http\Controllers\Api\Utils\ApiController;
use App\Models\Revenue\MasterPurchaseHistory;
use App\Models\Video\VideoCategory;
use App\Models\Video\VideoTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use stdClass;

class HelperController extends Controller
{

    public static bool $cacheEnabled = true;
    public static int $paginationLimit = 20;
    public static int $cacheTimeOut = 3600;
    public static string $webPageUrl = "https://www.myvideoinvites.com/";
    public static string $mediaUrl = "https://media.myvideoinvites.com/";

    public static function getPaginationLimit(?int $size = null): int
    {
        if (is_int($size))
            return $size;
        return HelperController::$paginationLimit;
    }

    public static function getVideoItemData(
        VideoTemplate|stdClass $item,
        Collection $rates = null
    ): array {

        $payment = RateController::getVideoRates($rates, $item->pages);
        $category = $item->virtualCat ?? $item->videoCat;

        return array(
            'category_id' => $item->category_id,
            'category_name' => $category?->category_name,
            'category_title' => $category?->category_name ?? $category?->category_title,
            'video_id' => $item->id,
            'string_id' => $item->string_id,
            'video_name' => $item->video_name,
            'file_name' => $item->folder_name,
            'video_thumb' => HelperController::$mediaUrl . $item->video_thumb,
            'video_url' => HelperController::$mediaUrl . $item->video_url,
            'width' => $item->width,
            'height' => $item->height,
            'keyword' => $item->keyword,
            'views' => $item->views,
            'creation' => $item->creation,
            'is_premium' => $item->is_premium,
            'payment' => $payment,
            'template_link' => "$item->slug",
            'cat_link' => $category?->slug ?? "/templates/p/$item->string_id",
        );
    }

    public static function checkStringFormat($string, $convert = false): string
    {

        if (preg_match("/^\['(.+)'\]$/", $string, $matches)) {
            if ($convert) {
                return $matches[1];
            }
            return $string;
        }
        return $string;
    }

    public static function checkSubsStatus($status): string
    {
        if ($status == '1') {
            return "Active";
        } else {
            return "Expired";
        }
    }

    public static function getSubsColor($status): string
    {
        if ($status == '1') {
            return "#2EC4B6";
        } else {
            return "#FF0000";
        }
    }

    public static function swapThumbs($thumbArray, $j): array
    {
        if ($j < 0 || $j >= count($thumbArray)) {
            return $thumbArray;
        }

        $element = $thumbArray[$j];
        unset($thumbArray[$j]);

        $thumbArray = array_values($thumbArray);

        array_unshift($thumbArray, $element);

        return $thumbArray;
    }

    public static function generateID($prefix = '', $length = 10, bool $appendNumber = true, $stringType = "noraml"): string
    {
        if ($appendNumber)
            $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        else
            $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $random = '';
        for ($i = 0; $i < $length; $i++) {
            try {
                $index = random_int(0, strlen($pool) - 1);
            } catch (\Random\RandomException $e) {
                $index = mt_rand(0, strlen($pool) - 1);
            }
            $random .= $pool[$index];
        }

        $finalString = $prefix ? $prefix . '_' . $random : $random;
        return match ($stringType) {
            'upper' => strtoupper($finalString),
            'lower' => strtolower($finalString),
            default => $finalString,
        };
    }

    public static function generateRandomId($length = 10, $prefix = '', ?string $modelSource = null, $column = 'string_id', bool $appendNumber = false, $stringType = "normal"): string
    {

        if ($modelSource && class_exists($modelSource)) {
            $modelClass = $modelSource;
            $modelInstance = new $modelClass;
            $table = $modelInstance->getTable();
            // Check if the column exists
            if (Schema::hasColumn($table, $column)) {
                do {
                    $finalId = self::generateID(prefix: $prefix, length: $length, appendNumber: $appendNumber, stringType: $stringType);
                    $exists = $modelClass::where($column, $finalId)->exists();
                } while ($exists);
            } else {
                $finalId = self::generateID(prefix: $prefix, length: $length, appendNumber: $appendNumber, stringType: $stringType);
            }
        } else {
            $finalId = self::generateID(prefix: $prefix, length: $length, appendNumber: $appendNumber, stringType: $stringType);
        }
        return $finalId;
    }


    public static function getIpAndCountry(Request $request, $ip = null): array
    {
        $ipAddress = $ip ?? ApiController::findIp($request);
        return self::getCountryByIp($ipAddress);
    }

    public static function getCountryByIp($ipAddress): array
    {
        // Get the location details from the IP address
        $location = GeoIP($ipAddress);

        // Extract the country code and country name
        $countryCode = $location['iso_code'];
        $countryName = $location['country'];
        $currency = $location['currency'];

        //        if ($request->isTester) {
//            return ['ip' => $ipAddress, 'cc' => "US", 'cn' => "US", 'cur' => "USD"];
//        }

        return [
            'ip' => $ipAddress,
            'cc' => strtoupper($countryCode),
            'cn' => $countryName,
            'cur' => strtoupper($currency),
            'symbol' => strtoupper($currency) === "INR" ? "₹" : "$",
        ];
    }

    public static function getTopKeywords($topKeywords): array
    {
        if (!empty($topKeywords)) {
            foreach ($topKeywords as &$keyword) {
                if (isset($keyword->openinnewtab)) {
                    $keyword->openinnewtab = (int) $keyword->openinnewtab;
                }
                if (isset($keyword->nofollow)) {
                    $keyword->nofollow = (int) $keyword->nofollow;
                }
            }
            unset($keyword);
        }

        return $topKeywords;
    }

    public static function generatePublicUrl($path): string
    {
        if (str_starts_with($path, "https://assets.craftyart.in") || str_starts_with($path, HelperController::$mediaUrl)) {
            return $path;
        }
        return HelperController::$mediaUrl . $path;
    }

    public static function getCTA($cta)
    {
        if (!$cta) {
            return null;
        }

        $ctaData = json_decode($cta, true);
        // Process the image paths
        foreach ($ctaData as $key => &$ctaItem) {
            // Handle single image objects
            if (isset($ctaItem['image']['src'])) {
                $ctaItem['image']['src'] = HelperController::generatePublicUrl($ctaItem['image']['src']);
            }
            // Handle arrays of images
            if (isset($ctaItem['images']) && is_array($ctaItem['images'])) {
                foreach ($ctaItem['images'] as &$imageItem) {
                    if (isset($imageItem['src'])) {
                        $imageItem['src'] = HelperController::generatePublicUrl($imageItem['src']);
                    }
                }
            }
            // Handle step section images
            if (isset($ctaItem['stepsection']) && is_array($ctaItem['stepsection'])) {
                foreach ($ctaItem['stepsection'] as &$step) {
                    if (isset($step['image']['src'])) {
                        $step['image']['src'] = HelperController::generatePublicUrl($step['image']['src']);
                    }
                }
            }
        }

        return $ctaData;
    }

    public static function getTemplateCount($count, $primaryKeyword): array
    {
        if ($primaryKeyword) {
            if ($count > 20)
                $final = $count . "+ " . $primaryKeyword;
            else
                $final = $count . " " . $primaryKeyword;
        } else {
            if ($count > 20)
                $final = $count . "+  Templates";
            else {
                if ($count <= 1)
                    $final = $count . " Template";
                else
                    $final = $count . " Templates";
            }
        }

        return ["count" => $count, "msg" => $final];
    }

    public static function extractAndRemoveTrailingNumber($string): array
    {
        if (preg_match('/\/(\d+)$/', $string, $matches)) {
            $number = intval($matches[1]);
            $cleaned = preg_replace('/\/\d+$/', '', $string);
            return [
                'number' => $number,
                'string' => $cleaned
            ];
        }
        return [
            'number' => null,
            'string' => $string
        ];
    }

    public static function getExtensionFromMimeType(string $mimeType): string
    {
        $map = [
            'image/png' => 'png',
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
        ];

        return $map[$mimeType] ?? 'png';
    }

    public static function getMailOrNumber($user)
    {
        if ($user->email == '' || $user->email == null) {
            return $user->country_code . ' ' . $user->number;
        } else {
            return $user->email;
        }
    }

    public static function getParentVideoCatName($id, $isFromCategoryegory = false)
    {
        return self::getParentCategoryName($id, $isFromCategoryegory);
    }

    public static function getParentCategoryName($id, $isFromCategoryegory = false)
    {
        $res = VideoCategory::with('parentCategory')->where('id', $id)->first();
        if ($res) {
            if ($isFromCategoryegory && $id != 0) {
                return $res->category_name;
            }
            if (isset($res->parentCategory->category_name)) {
                return $res->parentCategory->category_name;
            }
        }
        return "NA";
    }

    public static function getVPurchaseTemplateCount($product_id)
    {
        return MasterPurchaseHistory::whereProductId($product_id)->whereProductType('video')->count();
    }

    public static function stringContain($mainString, $containString)
    {
        if ($mainString == null) {
            return false;
        }

        $jsonArray = json_decode($mainString, true);

        if (is_array($jsonArray)) {
            foreach ($jsonArray as $json) {
                if ($containString == $json) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function filterArrayOrder($selectedValuesString, $mainArray, $column, $tg = false): ?array
    {
        if (is_string($selectedValuesString)) {
            $selectionArray = json_decode($selectedValuesString);
            $themesMap = [];
            foreach ($mainArray as $theme) {
                $themesMap[$theme->{$column}] = $theme;
            }
            $reorderedThemes = [];
            if (is_array($selectionArray)) {
                foreach ($selectionArray as $themeName) {
                    if (isset($themesMap[$themeName])) {
                        $reorderedThemes[] = $themesMap[$themeName];
                    }
                }
                foreach ($mainArray as $theme) {
                    if (!in_array($theme->{$column}, $selectionArray)) {
                        $reorderedThemes[] = $theme;
                    }
                }
            } else {
                $reorderedThemes = $mainArray;
            }
            return $reorderedThemes;
        }
        return null;
    }

    public static function getOrientations(): array
    {
        return [
            "portrait",
            "landscape",
            "square"
        ];
    }

    public static function getVideoMainCatNames($id): string
    {
        if ($id === null || $id === '') {
            return 'NA';
        }
        $ids = is_array($id) ? $id : [$id];
        $ids = array_filter($ids, static fn($v) => $v !== '' && $v !== null);
        if ($ids === []) {
            return 'NA';
        }
        $res = VideoCategory::whereIn('id', $ids)->pluck('category_name');

        return $res->isNotEmpty() ? $res->implode(',') : 'NA';
    }

    public static function generateStars($rate)
    {
        $fullStar = '<span class="fa fa-star checked"></span>';
        $halfStar = '<span class="fa fa-star-half-alt checked"></span>';
        $emptyStar = '<span class="fa fa-star"></span>';
        $ratingHtml = '';
        $wholeStars = floor($rate);
        $hasHalfStar = ($rate - $wholeStars) >= 0.5;
        for ($i = 0; $i < $wholeStars; $i++) {
            $ratingHtml .= $fullStar;
        }
        if ($hasHalfStar) {
            $ratingHtml .= $halfStar;
        }

        for ($i = $wholeStars + ($hasHalfStar ? 1 : 0); $i < 5; $i++) {
            $ratingHtml .= $emptyStar;
        }
        return $ratingHtml;
    }
}
