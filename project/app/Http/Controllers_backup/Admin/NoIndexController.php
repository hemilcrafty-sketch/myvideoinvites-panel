<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Utils\Controller;
use App\Models\Video\VideoCategory;
use App\Models\Video\VideoTemplate;
use App\Models\Video\VideoVirtualCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoIndexController extends Controller
{
    public function checkNoindex(Request $request): JsonResponse
    {
        $type = $request->get('type');
        $res = null;

        switch ($type) {

            case 'video_cat':
                $res = VideoCategory::find($request->id);
                break;

            case 'video_virtual_cat':
                $res = VideoVirtualCategory::find($request->id);
                break;

        }

        if (!$res) {
            return response()->json(['error' => 'Data not found']);
        }

        if ($type == 'template') {
            if ($res->no_index == 1) {
                $res->no_index = 0;
                if (!isset($res->h2_tag) || !isset($res->description) || !isset($res->meta_description)) {
                    return response()->json([
                        'error' => 'Page is index. Please add H2, Description, and Meta Description.'
                    ]);
                }
            } else {
                $res->no_index = 1;
                if (isset($res->h2_tag) || isset($res->description) || isset($res->meta_description)) {
                    if (!isset($res->h2_tag)) {
                        return response()->json(['error' => 'H2 tag is required']);
                    }
                    if (!isset($res->description)) {
                        return response()->json(['error' => 'Description is required']);
                    }
                    if (!isset($res->meta_description)) {
                        return response()->json(['error' => 'Meta description is required']);
                    }
                }
            }
        } else {
            $res->no_index = $res->no_index == 1 ? 0 : 1;
        }

        if ($res->save()) {
            return response()->json(['success' => 'done']);
        } else {
            return response()->json(['error' => 'Failed to save']);
        }
    }

    public function checkStatus(Request $request): JsonResponse
    {
        $type = $request->get('type');
        $res = null;

        switch ($type) {
            case 'video_cat':
                $res = VideoCategory::find($request->id);
                break;
            case 'video_virtual_cat':
                $res = VideoVirtualCategory::find($request->id);
                break;
            case 'video_item':
                $res = VideoTemplate::find($request->id);
                break;
        }

        if (!$res) {
            return response()->json(['error' => 'Data not found']);
        }

        // Toggle status: 1 (LIVE) <-> 0 (NOT LIVE)
        $res->status = $res->status == 1 ? 0 : 1;

        if ($res->save()) {
            return response()->json(['success' => 'done']);
        } else {
            return response()->json(['error' => 'Failed to save']);
        }
    }

    public function checkPremium(Request $request): JsonResponse
    {
        $type = $request->get('type');
        $res = null;

        switch ($type) {
            case 'video_item':
                $res = VideoTemplate::find($request->id);
                break;
        }

        if (!$res) {
            return response()->json(['error' => 'Data not found']);
        }

        // Toggle is_premium: 1 (PREMIUM) <-> 0 (FREE)
        $res->is_premium = $res->is_premium == 1 ? 0 : 1;

        if ($res->save()) {
            return response()->json(['success' => 'done']);
        } else {
            return response()->json(['error' => 'Failed to save']);
        }
    }
}
