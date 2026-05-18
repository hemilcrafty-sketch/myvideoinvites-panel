<?php

use App\Http\Controllers\Api\LottieApiController;
use App\Http\Controllers\Api\LottieSitemapController;
use App\Http\Controllers\Api\MyVideoSitemapController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Payment\PaymentController;
use App\Http\Controllers\Api\Payment\RazorpayWebhookController;
use App\Http\Controllers\Api\PReviewController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\VideoFilterController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\CategoryApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ContactUsController;
use App\Http\Controllers\Api\ReviewController;


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
Route::post('video/header-hierarchy', [LottieApiController::class, 'getHeaderHierarchy']);
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

//start of razorpay
Route::any('payment/razorpay/webhook', [RazorpayWebhookController::class, 'handleWebhook']);
//end of razorpay

Route::any('otp', [VerificationController::class, 'sendVerificationOTP']);
Route::any('otp/verify', [VerificationController::class, 'verifyOTP']);

//start of user management
Route::any('user/update', [UserApiController::class, 'updateUser']);
Route::any('user/delete', [UserApiController::class, 'deleteUser']);
Route::post('user/purchases', [UserApiController::class, 'getPurchases']);
//end of user management

//start of contact
Route::any('contact', [ContactUsController::class, 'contactUs']);
//end of contact

//start of review
Route::any('review/anl', [ReviewController::class, 'allAnalyticReviews']);
Route::any('reviews', [ReviewController::class, 'getReviews']);
Route::any('review/post', [ReviewController::class, 'postReview']);
Route::any('review/user', [ReviewController::class, 'getUserReview']);
Route::any('review/delete', [ReviewController::class, 'deleteReview']);
Route::any('review/edit', [ReviewController::class, 'editReview']);
//end of review

//start of p_review
Route::any('p_reviews', [PReviewController::class, 'getReviews']);
Route::any('p_review/post', [PReviewController::class, 'postReview']);
Route::any('p_review/delete', [PReviewController::class, 'deleteReview']);
Route::any('p_review/edit', [PReviewController::class, 'editReview']);
//end of p_review