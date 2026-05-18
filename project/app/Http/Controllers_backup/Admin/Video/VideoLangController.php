<?php

namespace App\Http\Controllers\Admin\Video;

use App\Http\Controllers\Admin\AppBaseController;
use App\Models\Video\VideoLanguage;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VideoLangController extends AppBaseController
{
    public function showLanguage(Request $request): Factory|View|Application
    {
        $searchableFields = [
            ["id" => 'id', "value" => 'Id'],
            ["id" => 'name', "value" => 'Name'],
            ["id" => 'id_name', "value" => 'ID Name'],
            ["id" => 'status', "value" => 'Status']
        ];

        $filteredLangs = $this->applyFiltersAndPagination($request, VideoLanguage::query(), $searchableFields);

        return view('videos.filters.language')->with([
            'langArray' => $filteredLangs,
            'searchableFields' => $searchableFields
        ]);
    }

    public function storeOrUpdateLanguage(Request $request): JsonResponse
    {
        $user = auth()->user();

        if ($request->has('id') && $request->id) {
            $res = VideoLanguage::find($request->id);
            if (!$res) {
                return response()->json(['error' => 'Video Language not found.']);
            }
        } else {
            $existing = VideoLanguage::where("name", $request->input('name'))->first();
            if ($existing) {
                return response()->json(['error' => 'Video Language already exists.']);
            }

            $res = new VideoLanguage();
            $res->emp_id = $user->id;
        }

        $accessCheck = $this->isAccessByRole("seo_all", $request->id, $res->emp_id);
        if ($accessCheck) {
            return response()->json(['error' => $accessCheck]);
        }

        $res->name = $request->input('name');
        $res->status = $request->input('status');
        $res->id_name = $request->input('id_name');
        $res->save();

        $msg = $request->has('id') ? 'Data updated successfully.' : 'Data added successfully.';
        return response()->json(['success' => $msg]);
    }

    public function deleteLanguage(Request $request): JsonResponse
    {
//        VideoLanguage::destroy($request->id);
        return response()->json(['success' => $request->id]);
    }
}
