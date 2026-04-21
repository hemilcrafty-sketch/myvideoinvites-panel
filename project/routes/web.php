<?php

use App\Http\Controllers\Admin\DensityCheckerController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\NoIndexController;
use App\Http\Controllers\Admin\Pricing\PaymentConfigController;
use App\Http\Controllers\Admin\TemplateRateController;
use App\Http\Controllers\Admin\Video\VideoCatController;
use App\Http\Controllers\Admin\Video\VideoInterestController;
use App\Http\Controllers\Admin\Video\VideoLangController;
use App\Http\Controllers\Admin\Video\VideoPageReviewController;
use App\Http\Controllers\Admin\Video\VideoReligionController;
use App\Http\Controllers\Admin\Video\VideoReviewController;
use App\Http\Controllers\Admin\Video\VideoSearchTagController;
use App\Http\Controllers\Admin\Video\VideoSizeController;
use App\Http\Controllers\Admin\Video\VideoStyleController;
use App\Http\Controllers\Admin\Video\VideoTemplateController;
use App\Http\Controllers\Admin\Video\VideoThemeController;
use App\Http\Controllers\Admin\Video\VideoVirtualCategoryController;
use App\Http\Controllers\Api\Utils\ApiContentManager;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\isAdminOrSeoManger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['reset' => true, 'register' => false]);

Route::get('ip', function (Request $request) {
	return [
		'ips' => $request->getClientIps(),
		'ip' => $request->ip(),
		'server' => [
			'X-Forwarded-For' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null,
			'X-Real-IP' => $_SERVER['HTTP_X_REAL_IP'] ?? null,
			'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'] ?? null,
		]
	];
});

Route::group(['middleware' => ['restrict.ip']], function () {

	Route::get('/', [HomeController::class, 'index']);
	Route::get('/dashboard/{manager?}', [HomeController::class, 'index'])->name('dashboard');

	// ============================================
	// VIDEO ROUTES
	// ============================================

	// Video Categories
	Route::get('show_v_cat', [VideoCatController::class, 'show'])->name('show_v_cat');
	Route::get('create_v_cat', [VideoCatController::class, 'create'])->name('create_v_cat');
	Route::post('submit_v_cat', [VideoCatController::class, 'store'])->name('v_cat.store');
	Route::get('edit_v_cat/{id}', [VideoCatController::class, 'edit'])->name('edit_v_cat');
	Route::post('update_v_cat/{id}', [VideoCatController::class, 'update'])->name('v_cat.update');
	Route::get('delete_v_cat/{id}', [VideoCatController::class, 'destroy'])->name('v_cat.delete');
	Route::post('v_cat_imp/{id}', [VideoCatController::class, 'imp_update'])->name('v_cat.imp');

	// Video Templates
	Route::get('show_v_item', [VideoTemplateController::class, 'show'])->name('show_v_item');
	Route::get('create_v_item', [VideoTemplateController::class, 'create'])->name('create_v_item');
	Route::post('submit_v_item', [VideoTemplateController::class, 'store'])->name('v_item.store');
	Route::get('edit_v_item/{id}', [VideoTemplateController::class, 'edit'])->name('edit_v_item');
	Route::post('update_v_item/{id}', [VideoTemplateController::class, 'update'])->name('v_item.update');
	Route::post('delete_v_item/{id}', [VideoTemplateController::class, 'destroy'])->name('v_item.delete');
	Route::get('edit_seo_v_item/{id}', [VideoTemplateController::class, 'editSeo'])->name('edit_seo_v_item');
	Route::post('update_seo_v_item/{id}', [VideoTemplateController::class, 'updateSeo'])->name('v_item_seo.update');
	Route::post('v_item_noindex/{id}', [VideoTemplateController::class, 'noindex_update'])->name('v_item.noindex');
	Route::post('v_item_assign_seo', [VideoTemplateController::class, 'assignSeo'])->name('v_item.assign-seo');
	Route::post('v_item_assign_category', [VideoTemplateController::class, 'assignCategory'])->name('v_item.assign-category');

	// Video Virtual Categories
	Route::get('show_video_virtual_cat', [VideoVirtualCategoryController::class, 'index'])->name('show_video_virtual_cat');
	Route::get('create_video_virtual_cat', [VideoVirtualCategoryController::class, 'create'])->name('create_video_virtual_cat');
	Route::post('submit_video_virtual_cat', [VideoVirtualCategoryController::class, 'store'])->name('submit_video_virtual_cat');
	Route::get('edit_video_virtual_cat/{id}', [VideoVirtualCategoryController::class, 'edit'])->name('edit_video_virtual_cat');
	Route::post('update_video_virtual_cat/{id}', [VideoVirtualCategoryController::class, 'store'])->name('video_virtual_cat.update');
	Route::get('delete_video_virtual_cat/{id}', [VideoVirtualCategoryController::class, 'destroy'])->name('delete_video_virtual_cat');

	// Video Styles
	Route::get('show_video_style', [VideoStyleController::class, 'show_video_style'])->name('show_video_style');
	Route::post('submit_video_style', [VideoStyleController::class, 'submitStyle'])->name('video_style.submit');
	Route::post('delete_video_style/{id}', [VideoStyleController::class, 'deleteStyle'])->name('video_style.delete');

	// Video Themes
	Route::get('show_video_theme', [VideoThemeController::class, 'show_video_theme'])->name('show_video_theme');
	Route::post('submit_video_theme', [VideoThemeController::class, 'submitTheme'])->name('video_theme.submit');
	Route::post('delete_video_theme/{id}', [VideoThemeController::class, 'deleteTheme'])->name('video_theme.delete');

	// Video Search Tags
	Route::get('show_video_search_tag', [VideoSearchTagController::class, 'show_video_search_tag'])->name('show_video_search_tag');
	Route::post('submit_video_search_tag', [VideoSearchTagController::class, 'submitVideoSearchTag'])->name('video_search_tag.submit');
	Route::post('delete_video_search_tag/{id}', [VideoSearchTagController::class, 'deleteVideoSearchTag'])->name('video_search_tag.delete');

	// Video Interests
	Route::get('show_video_interest', [VideoInterestController::class, 'showInterest'])->name('show_video_interest');
	Route::post('store_or_update_video_interest', [VideoInterestController::class, 'storeOrUpdateInterest'])->name('store_or_update_video_interest');
	Route::post('delete_video_interest/{id}', [VideoInterestController::class, 'deleteInterest'])->name('video_interest.delete');

	// Video Languages
	Route::get('show_video_lang', [VideoLangController::class, 'showLanguage'])->name('show_video_lang');
	Route::post('store_or_update_video_lang', [VideoLangController::class, 'storeOrUpdateLanguage'])->name('store_or_update_video_lang');
	Route::post('delete_video_lang/{id}', [VideoLangController::class, 'deleteLanguage'])->name('video_lang.delete');

	// Video Religions
	Route::get('video_religions', [VideoReligionController::class, 'index'])->name('video_religions.index');
	Route::post('video_religions/submit', [VideoReligionController::class, 'submit'])->name('video_religions.submit');
	Route::delete('video_religions/{id}', [VideoReligionController::class, 'destroy'])->name('video_religions.destroy');

	Route::resource('video_sizes', VideoSizeController::class);
	Route::post('loadVideoSizeAndTheme', [VideoTemplateController::class, 'loadVideoSizeAndTheme'])->name('loadVideoSizeAndTheme');

	// ============================================
	// END VIDEO ROUTES
	// ============================================

	// NoIndex
	Route::post('check_n_i', [NoIndexController::class, 'checkNoindex'])->name('check_n_i')->middleware(isAdminOrSeoManger::class);
	Route::post('check_status', [NoIndexController::class, 'checkStatus'])->name('check_status')->middleware(isAdminOrSeoManger::class);
	Route::post('check_premium', [NoIndexController::class, 'checkPremium'])->name('check_premium')->middleware(isAdminOrSeoManger::class);

    // Density Checker
    Route::post('/check-density-by-slug', [DensityCheckerController::class, 'checkFromSlug'])->name('density.check.slug');
    Route::post('/density-checker/primary-check', [DensityCheckerController::class, 'checkPrimaryKeyword'])->name('density-checker.primary-check');

    //Route::get('refreshTanscation', [App\Http\Controllers\Api\PaymentController::class, 'refreshTanscation'])->name('refreshTanscation')->middleware(IsAdmin::class);

	Route::get('/clear-cache', function () {
		Artisan::call('optimize');
		Artisan::call('route:cache');
		Artisan::call('route:clear');
		Artisan::call('view:clear');
		Artisan::call('config:cache');
		Artisan::call('config:clear');
		Artisan::call('cache:clear');
		return '<h1>Cache facade value cleared</h1>';
	})->middleware(IsAdmin::class);

	Route::resource('templateRate', TemplateRateController::class)->middleware(IsAdmin::class);
	Route::resource('caricatureRate', TemplateRateController::class)->middleware(IsAdmin::class);

	// Video Reviews Routes (Simple Reviews for Videos)
	Route::resource('video_reviews', VideoReviewController::class);
	Route::post('/video-reviews/status', [VideoReviewController::class, 'reviewStatus'])->name('video_reviews.reviewStatus');

	// Video Page Reviews Routes (Page Reviews for Videos)
	Route::resource('video_page_reviews', VideoPageReviewController::class);
	Route::post('/video-page-reviews/status', [VideoPageReviewController::class, 'reviewStatus'])->name('video_page_reviews.reviewStatus');
	Route::get('/video-page-reviews/video-page-data', [VideoPageReviewController::class, 'getSelectedVideoPageData'])->name('get_selected_video_page_data');
	Route::get('/video-page-review/video-page-title', [VideoPageReviewController::class, 'getSelectedVideoPageTitle'])->name('get_selected_video_page_title');

	Route::get('/get-storage-link', function (Request $request) {
		$src = $request->query('src');
		return response()->json(['url' => ApiContentManager::getStorageLink($src)]);
	})->middleware('auth');

	Route::get('get-options/{table}/{idColumn}/{nameColumn}/{database?}', function ($table, $idColumn, $nameColumn, $database = null) {
		$connection = $database ? DB::connection($database) : DB::connection();
		return $connection->table($table)->select($idColumn, $nameColumn)->get();
	})->middleware('auth');

	Route::get('/get-dependent-value/{table}/{dependentColumn}/{dependentColumnId}/{id}/{database?}', function ($table, $dependentColumn, $dependentColumnId, $id, $database = null) {
		$connection = $database ? DB::connection($database) : DB::connection();
		$value = $connection->table($table)
			->where($dependentColumnId, $id)
			->value($dependentColumn);
		// return response()->json(['data' => $value ?? ""]);
		return response()->json($value ?? "");
	})->middleware('auth');
	;

	Route::get('get-unique-options/{table}/{column}/{database?}', function ($table, $column, $database = null) {
		$connection = $database ? DB::connection($database) : DB::connection();
		$results = $connection->table($table)->select($column)->distinct()->get();

		$options = [];
		foreach ($results as $result) {
			// Remove brackets and double quotes, then split by comma
			$cleaned = str_replace(['[', ']', '"'], '', $result->$column);
			$tags = explode(',', $cleaned);
			foreach ($tags as $tag) {
				$trimmedTag = trim($tag);
				if ($trimmedTag !== '') { // Ensure empty values are skipped, but not '0'
					$options[] = ['value' => $trimmedTag, 'text' => $trimmedTag];
				}
			}
		}

		return response()->json($options);
	})->middleware('auth');

});

Route::middleware(IsAdmin::class)->prefix('payment_configuration')->group(function () {
	Route::get('/', [PaymentConfigController::class, 'index'])->name('payment_configuration.index');
	Route::post('/store', [PaymentConfigController::class, 'store'])->name('payment.config.store');
	Route::post('/add-gateway', [PaymentConfigController::class, 'addNewGateway'])->name('payment.config.add-gateway');
	Route::get('/{id}/get', [PaymentConfigController::class, 'getGateway'])->name('payment.config.get');
	Route::post('/{id}/update', [PaymentConfigController::class, 'updateGateway'])->name('payment.config.update');
	Route::post('/{id}/activate', [PaymentConfigController::class, 'activate'])->name('payment.config.activate');
	Route::delete('/{id}', [PaymentConfigController::class, 'destroy'])->name('payment.config.destroy');
});
