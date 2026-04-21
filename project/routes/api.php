<?php

use App\Http\Controllers\Api\LottieApiController;
use App\Http\Controllers\Api\LottieSitemapController;
use App\Http\Controllers\Api\MyVideoSitemapController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Payment\PaymentController;
use App\Http\Controllers\Api\Payment\RazorpayWebhookController;
use App\Http\Controllers\Api\VideoFilterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('video/categories', [LottieApiController::class, 'getCategories']);
Route::post('video/page', [LottieApiController::class, 'getPage']);
Route::post('video/template', [LottieApiController::class, 'getTemplate']);
Route::post('video/filters', [VideoFilterController::class, 'getFilters']);
Route::post('video/purchases', [LottieApiController::class, 'getPurchases']);

Route::get('myvideo-sitemap.xml', [MyVideoSitemapController::class, 'sitemapIndex']);
Route::get('myvideo-sitemap/category.xml', [MyVideoSitemapController::class, 'categorySitemap']);
Route::get('myvideo-sitemap/virtualcategory.xml', [MyVideoSitemapController::class, 'virtualCategorySitemap']);
Route::get('myvideo-sitemap/template-{page}.xml', [MyVideoSitemapController::class, 'templateSitemap']);
Route::get('myvideo-sitemap/others.xml', [MyVideoSitemapController::class, 'otherSitemap']);

Route::any('video_keywords', [LottieSitemapController::class, 'keywords']);
Route::any('video_sitemap', [LottieSitemapController::class, 'sitemap']);

Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/signup', [AuthController::class, 'signup']);
Route::post('auth/reset-password', [AuthController::class, 'resetPassword']);
Route::post('auth/logout', [AuthController::class, 'logout']);
Route::post('auth/user', [AuthController::class, 'getUser']);
Route::post('auth/google', [AuthController::class, 'handleGoogleSignIn']);

Route::any('cancel-subscription', [PaymentController::class, 'cancelSubscription']);
Route::any('payment/tr', [PaymentController::class, 'getTempRates']);
Route::any('payment/pc', [PaymentController::class, 'checkPromoCode']);
Route::any('payment/order', [PaymentController::class, 'getOrder']);
Route::any('payment/order/create', [PaymentController::class, 'createOrder']);
Route::any('payment/list', [PaymentController::class, 'listMethods']);
Route::any('payment/update', [PaymentController::class, 'updatePm']);
Route::any('payment/detach', [PaymentController::class, 'detachPm']);
Route::any('payment/stripe', [PaymentController::class, 'createStripeIntent']);
Route::any('payment/webhook', [PaymentController::class, 'webhook']);
Route::any('payment/verifyPayId', [PaymentController::class, 'verifyStripeId']);
Route::any('payment/refreshTransaction', [PaymentController::class, 'refreshTransaction']);
Route::any('payment/refreshTransaction/{id}', [PaymentController::class, 'refreshTransaction']);
Route::any('payment/razorpay/webhook', [RazorpayWebhookController::class, 'handleWebhook']);
