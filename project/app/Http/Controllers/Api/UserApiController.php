<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Utils\ApiController;
use App\Http\Controllers\Utils\HelperController;
use App\Http\Controllers\Utils\StorageUtils;
use App\Http\Controllers\Utils\ValidEmail;
use App\Models\OTPTable;
use App\Models\Revenue\PurchaseTransaction;
use App\Models\Video\VideoTemplate as Design;
use App\Models\UserData;
use App\Models\UserDataDeleted;
use App\Models\UserSession;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserApiController extends ApiController
{

    function updateUser(Request $request): array|string
    {
        if ($this->isFakeRequestAndUser($request)) {
            return $this->failed(msg: "Unauthorized");
        }

        $photo_uri = $request->file('photo_uri');
        $name = $request->get('name');
        $updateDp = $request->get('update_dp');
        $contact_no = $request->get('contact_no');

        $userData = UserData::where("uid", $this->uid)->first();

        if ($request->has('bio')) {
            if (strlen($request->bio) > 100) {
                return $this->failed(msg: "Bio must be at most 100 characters.");
            }
            $userData->bio = $request->bio;
        }

        if (isset($request->user_name) && $request->user_name !== $userData->user_name) {
            if (!preg_match('/^[A-Za-z0-9_]+$/', $request->user_name)) {
                return $this->failed(msg: "Username can only contain letters, numbers, and underscores.");
            }
            $existingUser = UserData::where('user_name', $request->user_name)->first();
            if ($existingUser) {
                return $this->failed(msg: "Username already taken by another user.");
            }
            if ($userData->is_username_update == 1) {
                return $this->failed(msg: "Username can only be updated once.");
            }
            $userData->user_name = $request->user_name;
            $userData->is_username_update = 1;
        }

        if ($photo_uri == null) {
            if ($updateDp == 1) {
                $userData->photo_uri = null;
            }
        } else {
            $new_name = $this->uid . '-' . HelperController::generateID('') . '.png';
            StorageUtils::delete($userData->photo_uri);
            StorageUtils::storeAs($photo_uri, 'uploadedFiles/user_dp', $new_name);
            $new_photo_uri = 'uploadedFiles/user_dp/' . $new_name;
            $userData->photo_uri = $new_photo_uri;
        }

        $userData->name = $name;
        if ($request->has('contact_no')) {
            $userData->contact_no = $contact_no;
        }
        $userData->save();

        $userData = UserData::where("uid", $this->uid)->first();

        return $this->successed(msg: "User updated successfully.", datas: $this->getNewUserRes($request, $userData));
    }

    public function getPurchases(Request $request)
    {
        if ($this->isFakeRequestAndUser($request)) {
            return $this->failed(msg: "Unauthorized");
        }

        try {
            $page = $request->get('page', 1);
            $limit = 10;

            // 1. Fetch Transactions with their Products
            $transactions = PurchaseTransaction::with('products')
                ->where('user_id', $this->uid)
                ->where('payment_status', 'paid')
                ->orderBy('id', 'DESC')
                ->paginate($limit, ['*'], 'page', $page);

            // 2. Collect unique product IDs for enrichment
            $productIds = $transactions->getCollection()
                ->pluck('products')
                ->flatten()
                ->pluck('product_id')
                ->unique();

            $designs = Design::whereIn('string_id', $productIds)->get()->keyBy('string_id');

            $purHistory = [];
            foreach ($transactions->items() as $trans) {
                $currency = $trans->currency_code === "INR" ? "₹" : "$";
                $transactionProducts = [];

                foreach ($trans->products as $item) {
                    $design = $designs->get($item->product_id);
                    
                    $transactionProducts[] = [
                        'id'             => $item->product_id,
                        'type'           => $item->product_type,
                        'name'           => $design?->post_name ?? $design?->video_name ?? 'Product',
                        'image'          => HelperController::$mediaUrl . ($design?->post_thumb ?? $design?->video_thumb ?? ''),
                        'width'          => $design?->width ?? 0,
                        'height'         => $design?->height ?? 0,
                        'amount'         => $currency . $item->amount,
                    ];
                }

                $purHistory[] = [
                    'transaction_id' => $trans->payment_id,
                    'amount'         => $currency . ($trans->paid_amount ?? $trans->amount),
                    'purchase_date'  => $trans->created_at->format('d/m/Y H:i:s'),
                    'status'         => HelperController::checkSubsStatus($trans->status),
                    'color'          => HelperController::getSubsColor($trans->status),
                    'products'       => $transactionProducts
                ];
            }

            $response = [
                'isLastPage' => $transactions->currentPage() >= $transactions->lastPage(),
                'datas'      => $purHistory
            ];

            return $this->successed(msg: "Data loaded", datas: $response);

        } catch (\Exception $e) {
            return $this->failed(msg: $e->getMessage());
        }
    }

    function deleteUser(Request $request): array|string
    {
        if ($this->isFakeRequestAndUser($request)) {
            return $this->failed(msg: "Unauthorized");
        }

        $otp = $request->get('otp');

        if ($otp == null) return $this->failed(msg: "Invalid params");

        $user_data = UserData::where('uid', $this->uid)->first();

        $data = OTPTable::where('mail', $user_data->email)->where('type', 'delete_acc')->get()->last();

        if (!$data || $data->status == "0" || $data->otp != $otp) {
            return $this->failed(msg: "Invalid OTP");
        }

        $res = OTPTable::find($data->id);
        $res->status = "0";
        $res->save();

        try {

            $res = new UserDataDeleted();
            $res->user_int_id = $user_data->id;
            $res->uid = $user_data->uid;
            $res->refer_id = $user_data->refer_id;
            $res->stripe_cus_id = $user_data->stripe_cus_id;
            $res->razorpay_cus_id = $user_data->razorpay_cus_id;
            $res->photo_uri = $user_data->photo_uri;
            $res->name = $user_data->name;
            $res->number = $user_data->contact_no;
            $res->email = $user_data->email;
            $res->login_type = $user_data->login_type;
            $res->utm_source = $user_data->utm_source;
            $res->utm_medium = $user_data->utm_medium;
            $res->fldr_str = $user_data->fldr_str;
            $res->creation_date = $user_data->created_at;
            $res->save();

            UserData::where('uid', $this->uid)->delete();

            return $this->successed(msg: "Your account has been successfully deleted.");
        } catch (Exception $e) {
            return $this->failed(msg: $e->getMessage(), showDecoded: true);
        }
    }

    public function addUser(Request $request, $uid, $photo_uri, $name, $email, $number, $login_type, $device_id, $utm_medium, $utm_source, $password = null): array
    {
        $isValid = ValidEmail::passes($email);
        if (!is_null($isValid)) {
            return $this->failed(msg: $isValid, showDecoded: true);
        }

        $res = new UserData();
        $res->uid = $uid;
        $res->refer_id = $this->generateReferID();
        $res->photo_uri = $photo_uri;
        $res->name = $name;
        $res->email = $email;
        $res->password = $password;
        $res->contact_no = $number;
        $res->login_type = $login_type;
        $res->utm_medium = $utm_medium;
        $res->utm_source = $utm_source;
        $res->save();

        $user_data = UserData::where("uid", $uid)->first();
        if ($user_data) $user_data->business_user = 0;

        $isNull = is_null($user_data);
        $statusCode = $isNull ? 404 : 200;
        $success = !$isNull;
        $msg = $isNull ? "Something went wrong." : "valid";
        return $this->sendResponse(statusCode: $statusCode, success: $success, msg: $msg, datas: ['data' => $user_data], showDecoded: true);
    }

    public function getNewUserRes(Request $request, UserData $userData, $isSessionCheck = false, $isNewUser = false, $minimalResponse = false): array
    {
        if (!$userData->user_name || empty($userData->user_name)) {
            $userName = self::generateUserName();
            $userData->user_name = $userName;
            UserData::where('id', $userData->id)->update(['user_name' => $userName]);
        }

        $user['uid'] = $userData->uid;
        $user['name'] = $userData->name;
        $user['email'] = $userData->email;
        $user['number'] = $userData->contact_no;
        $user['contact_no'] = $userData->contact_no;
        $user['user_name'] = $userData->user_name;
        $user['is_username_update'] = $userData->is_username_update == 1;
        $user['bio'] = $userData->bio;
        $user['photo_uri'] = $userData->photo_uri;

        if (str_contains($userData->photo_uri, 'uploadedFiles/')) {
            $user['photo_uri'] = HelperController::$mediaUrl . $userData->photo_uri;
        }

        $user['total_spent'] = PurchaseTransaction::whereUserId($userData->uid)->where('payment_status', 'paid')->sum('paid_amount');

        $response['user_details'] = [
            'device_limit' => $subData['device_limit'] ?? ((int)($userData->device_limit ?? 1)),
            'active_sessions' => UserSession::whereUserId($userData->uid)->get(),
        ];

        $response['user'] = $user;
        $response['isNewUser'] = $isNewUser;
        if (!$minimalResponse) {
            $response['purHistory'] = $this->getUserPurchaseHistory($userData);
        }

        $response['ipData'] = HelperController::getIpAndCountry($request);

        return $response;
    }

    private function getUserPurchaseHistory(UserData $user_data): array|null
    {
        $purchaseDatas = PurchaseTransaction::with('products')
            ->whereUserId($user_data->uid)
            ->where('payment_status', 'paid')
            ->get();

        $purchase_rows = [];

        foreach ($purchaseDatas as $purchaseData) {
            foreach ($purchaseData->products as $product) {
                $purchase_rows[] = array(
                    'id' => $product->product_id,
                    'type' => $product->product_type,
                );
            }
        }

        return $purchase_rows;
    }

    private function formatCount($count): string
    {
        if ($count >= 1000000) {
            return round($count / 1000000, 1) . 'M';
        }
        if ($count >= 1000) {
            return round($count / 1000, 1) . 'K';
        }
        return (string)$count;
    }

    public function generateUsernamesForAll(): array|string
    {
        UserData::select(['id', 'user_name'])->where('creator', 1)->whereNull('user_name')->orderBy('id')->chunk(1000, function ($rows) {
            foreach ($rows as $row) {
                UserData::where('id', $row->id)
                    ->update(['user_name' => self::generateUserName()]);
            }
        });
        return "done";
    }

    public static function generateUserName($prefix = 'user', $length = 8): string
    {
        $pool = '0123456789';
        do {
            $username = $prefix . substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
            $exists = UserData::where('user_name', $username)->exists();
        } while ($exists);
        return $username;
    }

    public function createFirebaseUser(Request $request, $name, $email, $number, $password, $device_id = null, $utm_medium = null, $utm_source = null, $sendMail = true): array
    {

        try {
            ValidEmail::passes($email);

            $userData = UserData::whereEmail($email)->first();
            if (!$userData) {
                $uid = UserData::generateUid();
                $userInfo = [
                    'name' => $name,
                    'email' => $email,
                    'photoUrl' => null,
                    'uid' => $uid,
                    ];

                $result = $this->addUser(
                    request: $request,
                    uid: $userInfo['uid'],
                    photo_uri: $userInfo['photoUrl'],
                    name: $userInfo['name'],
                    email: $userInfo['email'],
                    number: $number,
                    login_type: "email",
                    device_id: $device_id ?? null,
                    utm_medium: $utm_medium ?? "offer",
                    utm_source: $utm_source ?? "offer",
                    password: Hash::make($password));

                if (!$result['success']) return $result;
                $userData = $result['data'];
            }

//            if ($sendMail) EmailController::sendUserCreation($userData, $password);

            return $this->successed(datas: ['data' => $userData], showDecoded: true);


        } catch (\Exception $e) {

        }

        return $this->failed(showDecoded: true);
    }

    public static function generateReferID($id = "", $length = 6): string
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        do {
            $refer_id = $id . substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
        } while (UserData::where('refer_id', $refer_id)->exists());
        return $refer_id;
    }

    private static function formatDays($days): string
    {
        if ($days < 0) {
            return "Invalid";
        }

        $daysInYear = 365;
        $daysInMonth = 30;

        if ($days < $daysInMonth) {
            return $days . ' day' . ($days != 1 ? 's' : '');
        }

        if ($days < $daysInYear) {
            $months = $days / $daysInMonth;
            return number_format($months, 1) . ' month' . ($months >= 2 ? 's' : '');
        }

        $years = $days / $daysInYear;
        return number_format($years, 1) . ' year' . ($years >= 2 ? 's' : '');
    }
}
