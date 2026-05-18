<?php

namespace App\Http\Controllers\Api\Utils;

use App\Helpers\JwtHelper;
use App\Http\Controllers\Utils\Controller;
use App\Http\Controllers\Utils\CryptoJsAes;
use App\Http\Controllers\Utils\DomainChecker;
use App\Models\UserData;
use Exception;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public array|null|string $uid = null;
    public array|null|string $deviceId;
    public string $testingUid = "";
    public string $aesPassword = 'E@7r1K7!6v#KZx^m';
    public string|null $clientIp = null;
    private Request $request;

    public function __construct(Request $request)
    {
        $token = CryptoJsAes::decrypt($request->cookie('nsdf')) ?? CryptoJsAes::decrypt($request->header('nsdf')) ?? $request->cookie('nsdf') ?? $request->header('nsdf');
        $this->deviceId = CryptoJsAes::decrypt($request->cookie('dvi')) ?? CryptoJsAes::decrypt($request->header('dvi')) ?? $request->cookie('dvi') ?? $request->header('dvi');

        if (!is_null($token)) {
            try {
                $decoded = JwtHelper::decode($token);
                $this->uid = $decoded->uid;
            } catch (Exception $e) {
                $this->uid = null;
            }
        }

        $this->clientIp = $request->header('Client-Ip');
        $request->clientIp = $this->clientIp;

        $request->uid = $this->uid;
        $request->isTester = $this->isTester();
        $this->request = $request;
    }

    public function successed(string $msg = "Loaded!!", array $datas = [], bool $noIndex = false, bool $showDecoded = false): array|string
    {
        return ResponseHandler::sendResponse(request: $this->request, response: new ResponseInterface(200, true, $msg, $datas), noIndex: $noIndex, showDecoded: $showDecoded);
    }

    public function failed(int $statusCode = 401, string $msg = "Something went wrong", array $datas = [], bool $noIndex = false, bool $showDecoded = false): array|string
    {
        return ResponseHandler::sendResponse(request: $this->request, response: new ResponseInterface($statusCode, false, $msg, $datas), noIndex: $noIndex, showDecoded: $showDecoded);
    }

    public function sendResponse(int $statusCode, bool $success, string $msg, array $datas = [], bool $noIndex = false, bool $showDecoded = false): array|string
    {
        return ResponseHandler::sendResponse(request: $this->request, response: new ResponseInterface($statusCode, $success, $msg, $datas), noIndex: $noIndex, showDecoded: $showDecoded);
    }

    public function sendRawResponse(array $response, bool $noIndex = false, bool $showDecoded = false): array|string
    {
        return ResponseHandler::sendRawResponse($this->request, $response, noIndex: $noIndex, showDecoded: $showDecoded);
    }

    public function isTester($uid = null): bool
    {
        return $this->testingUid == ($uid ?? $this->uid);
    }

    public function getPageUrl(): ?string
    {
        $pageUrl = $this->request->header("page-url");
        if (empty($pageUrl)) $pageUrl = $this->request->header("Page-Url");
        if (empty($pageUrl)) $pageUrl = $this->request->header("X-Real-Page");
        if (empty($pageUrl)) $pageUrl = $this->request->header("x-real-page");
        return $pageUrl;
    }

    public function isFakeRequest(Request $request): bool
    {
        if ($request->isMethod('get')) return true;
        if (!DomainChecker::isValidDomain($request)) return true;
        if ($this->checkAuthKey()) return true;
        return false;
    }

    public function isFakeRequestAndUser(Request $request): bool
    {
        if ($request->isMethod('get')) return true;
        if (!DomainChecker::isValidDomain($request)) return true;
        if ($this->checkAuthKeyAndUid()) return true;
        return false;
    }

    public function isFakeRequestAndCreator(Request $request): bool
    {
        if ($request->isMethod('get')) return true;
        if (!DomainChecker::isValidDomain($request)) return true;
        if ($this->checkAuthKeyAndCreator()) return true;
        return false;
    }

    public function isFromBgRemoverDomain(Request $request): bool
    {
        if (DomainChecker::isValidDomain($request)) return true;
        return false;
    }

    private function checkAuthKey(): bool
    {
        return false;
    }

    private function checkAuthKeyAndUid(): bool
    {
        if (is_null($this->uid) || is_array($this->uid)) return true;
        $userData = UserData::where("uid", $this->uid)->exists();
        if (!$userData) return true;
        return false;
    }

    private function checkAuthKeyAndCreator(): bool
    {
        if (is_null($this->uid) || is_array($this->uid)) return true;

        $userData = UserData::where('uid', $this->uid)
            ->where(function ($query) {
                $query->where('creator', 1)->orWhere('hoc', 1);
            })->exists();

        if (!$userData) return true;
        return false;
    }

    public static function findIp(Request $request): string
    {


        $clientIP = $request->header('Client-Ip');

        $defaultUserIp = '89.116.134.215';
//        $userIp = $request->clientIp ?? $request->ip();
        $userIp = !is_null($clientIP) ? ($clientIP) : $request->ip();
        if (!$userIp || $userIp == $defaultUserIp) {
            $userIp = $request->header('X-Forwarded-For', $defaultUserIp);
        }
        return $userIp ?? $defaultUserIp;
    }
}
