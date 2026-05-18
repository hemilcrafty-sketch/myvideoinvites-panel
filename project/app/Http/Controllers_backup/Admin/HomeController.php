<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Utils\RoleManager;
use App\Models\PurchaseHistory;
use App\Models\Subscription;
use App\Models\Video\VideoCategory;
use App\Models\Video\VideoPurchaseHistory;
use App\Enums\UserRole;
use App\Models\User;
use App\Models\Video\VideoTemplate;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HomeController extends AppBaseController
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($isManager = null)
    {

        $currentuserid = Auth::user()->user_type;

        if (RoleManager::isSalesManager($currentuserid) || RoleManager::isSalesEmployee($currentuserid)) {
            return redirect()->route('order_user.index');
        }

        $idAdmin = RoleManager::isAdmin(Auth::user()->user_type);
        $isSeoExecutive = RoleManager::isSeoExecutive(Auth::user()->user_type);
        $condition = "=";
        if ($idAdmin) {
            $condition = "!=";
            $currentuserid = -1;
        } else {
            $currentuserid = Auth::user()->id;
        }

        if (isset($isManager) && $isManager == 1) {
            $datas['video_cat_item'] = VideoCategory::count();
            $datas['video_cat_item_live'] = VideoCategory::where('status', '1')->count();
            $datas['video_cat_item_unlive'] = VideoCategory::where('status', '0')->count();
        } else {
            $datas['video_cat_item'] = VideoCategory::where('emp_id', $condition, $currentuserid)->count();
            $datas['video_cat_item_live'] = VideoCategory::where('emp_id', $condition, $currentuserid)->where('status', '1')->count();
            $datas['video_cat_item_unlive'] = VideoCategory::where('emp_id', $condition, $currentuserid)->where('status', '0')->count();
        }

        if (isset($isManager) && $isManager == 1) {
            $datas['video_template_item'] = VideoTemplate::count();
            $datas['video_template_item_live'] = VideoTemplate::where('status', '1')->count();
            $datas['video_template_item_unlive'] = VideoTemplate::where('status', '0')->count();
        } elseif ($isSeoExecutive) {
            $seoExecId = Auth::id();
            $seoTeamIds = User::where('team_leader_id', $seoExecId)->where('status', 1)->pluck('id')->toArray();
            $seoAssignedIds = array_merge([$seoExecId], $seoTeamIds);
            $vtAssigned = VideoTemplate::where('is_deleted', 0)->whereIn('seo_emp_id', $seoAssignedIds);
            $datas['video_template_item'] = (clone $vtAssigned)->count();
            $datas['video_template_item_live'] = (clone $vtAssigned)->where('status', '1')->count();
            $datas['video_template_item_unlive'] = (clone $vtAssigned)->where('status', '0')->count();
        } elseif ((int) Auth::user()->user_type === UserRole::SEO_INTERN->id()) {
            $vtIntern = VideoTemplate::where('is_deleted', 0)->where('seo_emp_id', Auth::id());
            $datas['video_template_item'] = (clone $vtIntern)->count();
            $datas['video_template_item_live'] = (clone $vtIntern)->where('status', '1')->count();
            $datas['video_template_item_unlive'] = (clone $vtIntern)->where('status', '0')->count();
        } else {
            $datas['video_template_item'] = VideoTemplate::where('emp_id', $condition, $currentuserid)->count();
            $datas['video_template_item_live'] = VideoTemplate::where('emp_id', $condition, $currentuserid)->where('status', '1')->count();
            $datas['video_template_item_unlive'] = VideoTemplate::where('emp_id', $condition, $currentuserid)->where('status', '0')->count();
        }

        $datas['cache'] = env('CACHE_VER', '1');

        return view('dashboard')->with('datas', $datas);
    }

    public function refreshTanscation(Request $request)
    {

    }

    public function update_cache_ver(Request $request)
    {
        $cache_ver = $request->input('cache_ver');

        $this->setEnv('CACHE_VER', $cache_ver);

        return response()->json([
            'success' => 'Cache update successfully.'
        ]);
    }

    private function setEnv($key, $value)
    {
        file_put_contents(app()->environmentFilePath(), str_replace(
            $key . '=' . env($key, '1'),
            $key . '=' . $value,
            file_get_contents(app()->environmentFilePath())
        ));
    }
}
