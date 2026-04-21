<?php

namespace App\Http\Controllers\Api;

use App\Helpers\JwtHelper;
use App\Http\Controllers\Api\Utils\ApiController;
use App\Http\Controllers\Api\Utils\ResponseHandler;
use App\Http\Controllers\Api\Utils\ResponseInterface;
use App\Models\OtpTable;
use App\Models\UserData;
use App\Models\UserSession;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Factory;

class AuthController extends ApiController
{

    protected Auth $auth;

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $serviceAccountPath = "/private-files/video-firebase-service-account.json";
        $factory = (new Factory)->withServiceAccount($serviceAccountPath);
        $this->auth = $factory->createAuth();
    }

    function getUser(Request $request): array|string
    {
        if ($this->isFakeRequestAndUser($request))
            return $this->failed(msg: "Unauthorized");

        if (empty($this->deviceId))
            return $this->failed(msg: "Unauthorized");

        $user_data = UserData::whereUid($this->uid)->first();
        $userController = new UserApiController($request);

        $sessionResponse = $this->checkAndUpdateSession(userData: $user_data, deviceId: $this->deviceId, IP: $request->ip(), userAgent: $request->userAgent());
        if (!$sessionResponse['success'])
            return $this->failed(msg: $sessionResponse['msg'], datas: $sessionResponse['data']);

        return $this->successed(datas: $userController->getNewUserRes(request: $request, userData: $user_data));
    }

    private function checkAndUpdateSession($userData, string $deviceId, string $IP, string $userAgent): array
    {

        $session = UserSession::whereUserId($userData->uid)
            ->whereDeviceId($deviceId)
            ->first();

        if (!$session) {
            $session = new UserSession();
            $session->user_id = $userData->uid;
            $session->device_id = $deviceId;
            $session->ip_address = $IP;
            $session->user_agent = $userAgent;
            $session->last_active = now();
            $session->save();
        } else {
            $session->update([
                'ip_address' => $IP,
                'user_agent' => $userAgent,
                'last_active' => now(),
            ]);
        }
        return ['success' => true, 'data' => $session];
    }

    function login(Request $request): array|string
    {
        if ($this->isFakeRequest($request))
            return $this->failed(msg: "Unauthorized");

        $email = $request->get('email');
        $password = $request->get('password');

        if (empty($email) || empty($password))
            return $this->failed(statusCode: 400, msg: "Invalid request");

        $user_data = UserData::whereEmail($email)->first();
        if (!$user_data)
            return $this->failed(statusCode: 400, msg: "Email is not registered");

        if (!Hash::check($password, $user_data->password)) {
            try {
                $result = $this->auth->signInWithEmailAndPassword($email, $password);
                if (empty($result->firebaseUserId()))
                    return $this->failed(statusCode: 400, msg: "Incorrect Password");
                else {
                    $user_data->password = Hash::make($password);
                    $user_data->save();
                }
            } catch (\Exception $e) {
                return $this->failed(statusCode: 400, msg: "Incorrect Password");
            }
        }

        $this->deviceId = UserSession::generateDeviceId();

        $sessionResponse = $this->checkAndUpdateSession(userData: $user_data, deviceId: $this->deviceId, IP: $request->ip(), userAgent: $request->userAgent());
        if (!$sessionResponse['success'])
            return $this->failed(msg: $sessionResponse['msg'], datas: $sessionResponse['data']);

        $userController = new UserApiController($request);
        $data = $userController->getNewUserRes(request: $request, userData: $user_data, minimalResponse: true);

        $jwtPayload = [
            'uid' => $user_data->uid,
            'email' => $user_data->email,
            'device_id' => $this->deviceId,
            'session_id' => $sessionResponse['data']['id'],
        ];

        $data['session'] = [
            "nsdf" => JwtHelper::generate($jwtPayload),
            "dvi" => $this->deviceId,
        ];

        $data['token'] = JwtHelper::generate($jwtPayload);

        return $this->successed(datas: $data);
    }

    function signup(Request $request): array|string
    {
        if ($this->isFakeRequest($request))
            return $this->failed(msg: "Unauthorized");

        $otp = $request->get('otp');
        $name = $request->get('name');
        $email = $request->get('email');
        $password = $request->get('password');
        $contactNo = $request->get('contact');
        $utm_medium = $request->get('utm_medium', "craftyart");
        $utm_source = $request->get('utm_source', "craftyart");

        if (empty($otp) || empty($name) || empty($email) || empty($password))
            return $this->failed(msg: "Invalid request");
        if (UserData::whereEmail($email)->exists())
            return $this->failed(msg: "Email is already registered");
        if (strlen($password) < 6)
            return $this->failed(msg: "Password length is short");

        $data = OTPTable::whereMail($email)->whereType('account_create')->get()->last();
        if (!$data || $data->status == "0" || $data->otp != $otp)
            return $this->failed(msg: "Invalid otp");
        $success = OTPTable::whereMail($email)->update(["status" => 0]);
        if (!$success)
            return $this->failed();

        $this->deviceId = UserSession::generateDeviceId();

        $userController = new UserApiController($request);
        $result = $userController->createFirebaseUser($request, $name, $email, $contactNo, $password, $this->deviceId, $utm_medium, $utm_source);

        if (!$result['success'])
            return ResponseHandler::sendEncryptedResponse($request, $result);

        $user_data = $result['data'];

        $data = $userController->getNewUserRes(request: $request, userData: $user_data, minimalResponse: true);

        $sessionResponse = $this->checkAndUpdateSession(userData: $user_data, deviceId: $this->deviceId, IP: $request->ip(), userAgent: $request->userAgent());
        if (!$sessionResponse['success'])
            return $this->failed(msg: $sessionResponse['msg'], datas: $sessionResponse['data']);

        $jwtPayload = [
            'uid' => $user_data->uid,
            'email' => $user_data->email,
            'device_id' => $this->deviceId,
            'session_id' => $sessionResponse['data']['id'],
        ];

        $data['session'] = [
            "nsdf" => JwtHelper::generate($jwtPayload),
            "dvi" => $this->deviceId,
        ];

        $data['token'] = JwtHelper::generate($jwtPayload);

        return $this->successed(datas: $data);
    }

    function handleGoogleSignIn(Request $request): array|string
    {
        if ($this->isFakeRequest($request))
            return $this->failed(statusCode: 400, msg: "Unauthorized");

        $token = $request->get('token');

        $utm_medium = $request->get('utm_medium', "craftyart");
        $utm_source = $request->get('utm_source', "craftyart");

        try {
            $data = $this->auth->verifyIdToken($token);
            $name = $data->claims()->get('name');
            $email = $data->claims()->get('email');
            $photo_uri = $data->claims()->get('picture');
        } catch (\Exception $e) {
            return $this->failed(statusCode: 400, msg: "Invalid token");
        }
        $this->auth->verifyIdToken($token)->claims()->get('email');

        $user_data = UserData::whereEmail($email)->first();
        $userController = new UserApiController($request);

        $this->deviceId = UserSession::generateDeviceId();

        if (!$user_data) {
            $isExists = $userController->checkFirebaseUid($email);
            if (!$isExists['registered'])
                return $this->failed();

            $userController = new UserApiController($request, $this->auth);

            $userInfo = $isExists['user'];
            $result = $userController->addUser($request, $userInfo['uid'], $photo_uri, $name, $email, null, "Google", $this->deviceId, $utm_medium, $utm_source);

            if (!$result['success'])
                return ResponseHandler::sendEncryptedResponse($request, $result);
            $user_data = $result['data'];
        }

        $data = $userController->getNewUserRes(request: $request, userData: $user_data);

        $sessionResponse = $this->checkAndUpdateSession(userData: $user_data, deviceId: $this->deviceId, IP: $request->ip(), userAgent: $request->userAgent());
        if (!$sessionResponse['success'])
            return $this->failed(msg: $sessionResponse['msg'], datas: $sessionResponse['data']);

        $jwtPayload = [
            'uid' => $user_data->uid,
            'email' => $user_data->email,
            'device_id' => $this->deviceId,
            'session_id' => $sessionResponse['data']['id'],
        ];

        $data['session'] = [
            "nsdf" => JwtHelper::generate($jwtPayload),
            "dvi" => $this->deviceId,
        ];

        $data['token'] = JwtHelper::generate($jwtPayload);

        return ResponseHandler::sendResponse($request, new ResponseInterface(200, true, "Done", datas: $data));
    }

    function resetPassword(Request $request): array|string
    {
        if ($this->isFakeRequest($request))
            return $this->failed(msg: "Unauthorized");

        $email = $request->get('email');
        $otp = $request->get('otp');
        $password = $request->get('password');

        if (empty($email) || empty($otp) || empty($password))
            return $this->failed(msg: "Invalid request");
        if (strlen($password) < 6)
            return $this->failed(msg: "Password length is short");

        $user_data = UserData::whereEmail($email)->first();
        if (!$user_data)
            return $this->failed(msg: "Invalid request");

        $data = OTPTable::whereMail($email)->whereType('forgot_pass')->get()->last();
        if (!$data || $data->status == "0" || $data->otp != $otp)
            return $this->failed(msg: "Invalid otp");
        $success = OTPTable::whereMail($email)->update(["status" => 0]);
        if (!$success)
            return $this->failed();

        try {
            $this->auth->changeUserPassword($user_data->uid, $password);
            $password = Hash::make($password);
            $success = UserData::where('email', $email)->update(["password" => $password]);
            if (!$success)
                return $this->failed();

            $sessions = UserSession::whereUserId($user_data->uid)->get();
            foreach ($sessions as $session) {
                $session->delete();
            }

            return $this->successed(msg: "Password has been changed successfully");
        } catch (Exception | AuthException | FirebaseException $e) {
            return $this->failed();
        }
    }

    function logout(Request $request): array|string
    {
        if ($this->isFakeRequest($request))
            return $this->failed(msg: "Unauthorized");

        $email = $request->get('email');
        $deviceId = $request->get('device_id');
        $logoutAll = $request->get('logout_all', false);

        if (!$logoutAll && !$deviceId) {
            return $this->failed(msg: "Device ID required for single logout");
        }

        $user = UserData::whereEmail($email)->first();

        if (!$user)
            return $this->failed(msg: "User not found");

        if ($logoutAll) {
            $sessions = UserSession::whereUserId($user->uid)->get();

            foreach ($sessions as $session) {
                $session->delete();
            }
            return $this->successed("All devices logged out successfully");
        } else {
            $session = UserSession::where('user_id', $user->uid)
                ->where('device_id', $deviceId)
                ->first();

            $session?->delete();

            return $this->successed("Logged out successfully");
        }
    }

}
