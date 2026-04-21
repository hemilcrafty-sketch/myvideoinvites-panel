<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Utils\ApiController;
use App\Http\Controllers\Utils\HelperController;
use App\Http\Controllers\Utils\StorageUtils;
use App\Http\Controllers\Utils\ValidEmail;
use App\Models\OTPTable;
use App\Models\Revenue\MasterPurchaseHistory;
use App\Models\UserData;
use App\Models\UserDataDeleted;
use App\Models\UserSession;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Factory;

class UserApiController extends ApiController
{

    protected Auth $auth;

    public function __construct(Request $request, Auth|null $auth = null)
    {
        parent::__construct($request);
        if ($auth == null) {
            $serviceAccountPath = "/private-files/video-firebase-service-account.json";
            $factory = (new Factory)->withServiceAccount($serviceAccountPath);
            $this->auth = $factory->createAuth();
        } else {
            $this->auth = $auth;
        }
    }

    function updateUser(Request $request): array|string
    {
        if ($this->isFakeRequestAndUser($request)) {
            return $this->failed(msg: "Unauthorized");
        }

        $photo_uri = $request->file('photo_uri');
        $name = $request->get('name');
        $updateDp = $request->get('update_dp');

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
        $userData->save();

        $userData = UserData::where("uid", $this->uid)->first();

        return $this->successed(msg: "User updated successfully.", datas: $this->getNewUserRes($request, $userData));
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

            $this->auth->deleteUser($user_data->uid);

            $res = new UserDataDeleted();
            $res->user_int_id = $user_data->id;
            $res->uid = $user_data->uid;
            $res->refer_id = $user_data->refer_id;
            $res->stripe_cus_id = $user_data->stripe_cus_id;
            $res->razorpay_cus_id = $user_data->razorpay_cus_id;
            $res->photo_uri = $user_data->photo_uri;
            $res->name = $user_data->name;
            $res->country_code = $user_data->country_code;
            $res->number = $user_data->number;
            $res->email = $user_data->email;
            $res->login_type = $user_data->login_type;
            $res->total_validity = $user_data->total_validity;
            $res->validity = $user_data->validity;
            $res->ai_credit = $user_data->ai_credit;
            $res->is_premium = $user_data->is_premium;
            $res->special_user = $user_data->special_user;
            $res->can_update = $user_data->can_update;
            $res->utm_source = $user_data->utm_source;
            $res->utm_medium = $user_data->utm_medium;
            $res->coins = $user_data->coins;
            $res->device_id = $user_data->device_id;
            $res->fldr_str = $user_data->fldr_str;
            $res->creation_date = $user_data->created_at;
            $res->save();

            UserData::where('uid', $this->uid)->delete();

            return $this->successed(msg: "Your account has been successfully deleted.");
        } catch (Exception|AuthException|FirebaseException $e) {
            return $this->failed();
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
        $user['number'] = $userData->number;
        $user['contact_no'] = $userData->contact_no;
        $user['user_name'] = $userData->user_name;
        $user['is_username_update'] = $userData->is_username_update == 1;
        $user['bio'] = $userData->bio;
        $user['photo_uri'] = $userData->photo_uri;

        if (str_contains($userData->photo_uri, 'uploadedFiles/')) {
            $user['photo_uri'] = HelperController::$mediaUrl . $userData->photo_uri;
        }

        $user['total_spent'] = MasterPurchaseHistory::whereUserId($userData->uid)->wherePaymentStatus('paid')->sum('paid_amount');

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
        $purchaseDatas = MasterPurchaseHistory::where('user_id', $user_data->uid)->wherePaymentStatus('paid')->get();

        $purchase_rows = [];

        if ($purchaseDatas != null && $purchaseDatas->count() != 0) {
            foreach ($purchaseDatas as $purchaseData) {
                $purchase_rows[] = array(
                    'id' => $purchaseData->product_id,
                    'type' => $purchaseData->product_type,
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

    public function checkFirebaseUid($uidORmail): array
    {
        try {
            $user = $this->auth->getUserByEmail($uidORmail);

            $userId = $user->uid;
            $emailId = $user->email;

            if ($userId && $emailId) {
                return [
                    'registered' => true,
                    'user' => [
                        'name' => $user->displayName ?? "User",
                        'email' => $emailId,
                        'photoUrl' => $user->photoUrl,
                        'uid' => $userId,
                    ]
                ];
            }
            return ['registered' => false];
        } catch (Exception|AuthException|FirebaseException $e) {
            return ['registered' => false];
        }
    }

    public function createFirebaseUser(Request $request, $name, $email, $number, $password, $device_id = null, $utm_medium = null, $utm_source = null, $sendMail = true): array
    {
        try {
            $userInfo = null;

            ValidEmail::passes($email);

            $data = $this->checkFirebaseUid($email);
            if ($data['registered']) {
                $userInfo = $data['user'];
            } else {
                $user = $this->auth->createUser([
                    'displayName' => $name,
                    'email' => $email,
                    'password' => $password
                ]);

                $userId = $user->uid;
                $emailId = $user->email;

                if ($userId && $emailId) {

                    $this->auth->updateUser($userId, [
                        'emailVerified' => true
                    ]);

                    $userInfo = [
                        'name' => $user->displayName ?? "CraftyArt",
                        'email' => $emailId,
                        'photoUrl' => $user->photoUrl,
                        'uid' => $userId,
                    ];
                }
            }
            if ($userInfo) {
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
//                if ($sendMail) EmailController::sendUserCreation($userData, $password);
                return $result;
            }


        } catch (Exception|AuthException|FirebaseException $e) {

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
