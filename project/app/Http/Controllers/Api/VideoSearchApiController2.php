<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Utils\ApiController;
use App\Http\Controllers\Utils\HelperController;
use App\Models\UserData;
use App\Models\Video\VideoTemplate;

class VideoSearchApiController extends ApiController
{
    private array $extra = ["aboard", "about", "above", "across", "after", "against", "along", "amid", "amidst", "among", "amongst", "around, as, at", "before", "behind", "below", "beneath", "beside", "between", "beyond", "but, by", "concerning", "considering", "despite", "down", "during", "except", "for", "from, in", "inside", "into", "like", "near, of", "off, on", "onto", "out", "outside", "over", "past", "regarding", "round", "since", "through", "throughout", "till", "until, to", "toward", "towards", "under", "underneath", "unlike", "until", "unto, up", "upon", "with", "within", "without, am, is", "are", "was", "were", "been", "being", "have", "has", "had, do", "does", "did", "can", "could", "may", "might", "shall", "should", "will", "would", "must"];

    public function exactKeywordTemplates($rates, array $keywords, int $limit, $excludeTemplate = null): array
    {

        $hasShowAll = false;

        $user_data = UserData::where("uid", $this->uid)->first();

        if ($user_data && ($user_data->can_update == 1 || $user_data->can_update == '1')) {
            $hasShowAll = true;
        }

        $status_condition = $hasShowAll ? "!=" : "=";
        $status = $hasShowAll ? "-1" : "1";

        $itemData = VideoTemplate::with(['videoCat', 'virtualCat'])
            ->where("string_id", "!=", $excludeTemplate)
            ->where('status', $status_condition, $status)
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhereJsonContains('keyword', $keyword);
                }
            })
            ->orderByRaw('id DESC')
            ->limit($limit)
            ->get();

        $item_rows = [];

        foreach ($itemData as $item) {
            $item_rows[] = HelperController::getVideoItemData(item: $item, rates: $rates);
        }

        return [
            'isLastPage' => true,
            'datas' => $item_rows,
        ];

    }
}
