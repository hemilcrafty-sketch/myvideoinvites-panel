<?php

namespace App\Http\Controllers\Utils;

use App\Http\Controllers\Api\Utils\ApiController;
use App\Jobs\FbPixelJob;
use FacebookAds\Api;
use FacebookAds\Object\ServerSide\ActionSource;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;
use FacebookAds\Object\ServerSide\UserData;
use FacebookAds\Object\ServerSide\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FbPixel
{
    private static ?Api $apiInstance = null;

    private static string $pixelId = '1352594906926882';
    private static string $accessToken = 'EAAxpeR8ZAouIBRWZAa2E2tBxtk1tharOmYmYw9pkX0Fa9cBVbIZAA48gUEzaWOZA7dowIFKChLjPnZCy9GEqJD9ld1N6uJCH1eVyLX6eFPFl3MRTDPcZB25L6y3SaY1OLwKNDF8Rd91vHi7XVuBCuZCFoj4q6YZCFEqU6gvStecvM5OMkczePQxGnBet4g62IQZDZD';

    /**
     * Initialize the Facebook API only once.
     */
    private static function initApi(): void
    {
        if (self::$apiInstance === null) {
            Api::init(null, null, self::$accessToken);
            self::$apiInstance = Api::instance();
        }
    }

    /**
     * Entry point for tracking events. Dispatches a background job.
     */
    public static function purchaseEvent(
        FacebookEvent $eventName,
        Request       $request,
        ?string       $name = null,
        ?string       $email = null,
        ?string       $phone = null,
        ?string       $url = null,
        ?array        $purchaseData = null,
        ?array        $userDataOverride = null
    ): void {
        // Capture data from request or overrides
        $clientIp = $userDataOverride['ip'] ?? ApiController::findIp($request);
        $userAgent = $userDataOverride['user_agent'] ?? $request->header('User-Agent', 'Unknown');
        $fbclid = $userDataOverride['fbc'] ?? $request->cookie('_fbclid');
        $fbp = $userDataOverride['fbp'] ?? $request->cookie('_fbp') ?? $request->cookie('_caid');

        FbPixelJob::dispatch(
            $eventName,
            $clientIp,
            $userAgent,
            $fbclid,
            $fbp,
            $name,
            $email,
            $phone,
            $url,
            $purchaseData
        );
    }

    /**
     * Actual SDK logic executed in the background.
     */
    public static function purchaseJobEvent(
        FacebookEvent $eventName,
        ?string       $clientIp,
        ?string       $userAgent,
        ?string       $fbclid,
        ?string       $fbp,
        ?string       $name = null,
        ?string       $email = null,
        ?string       $phone = null,
        ?string       $url = null,
        ?array        $purchaseData = null
    ): void {
        try {
            Log::info("fbpixel FaceBook Event Start", [
                'eventName' => $eventName->value,
                'clientIp' => $clientIp,
                'userAgent' => $userAgent,
                'fbclid' => $fbclid,
                'fbp' => $fbp,
                'url' => $url,
                'purchaseData' => $purchaseData
            ]);
            self::initApi();

            // Apply formatting logic
            if ($fbclid && !str_starts_with($fbclid, 'fb.1.')) {
                $fbclid = "fb.1." . time() . "." . $fbclid;
            }

            if ($fbp && str_starts_with($fbp, "cid.1.")) {
                $fbp = str_replace("cid.1.", "fb.1.", $fbp);
            }

            // 1. Setup User Data
            $userData = (new UserData())
                ->setClientIpAddress($clientIp)
                ->setClientUserAgent($userAgent);

            if ($fbclid && $fbclid !== 'undefined') $userData->setFbc($fbclid);
            if ($fbp && $fbp !== 'undefined') $userData->setFbp($fbp);

            if ($email) {
                $userData->setEmail(Util::hash(strtolower(trim($email))));
            }

            if ($name) {
                $userData->setFirstName(Util::hash(strtolower(trim($name))));
            }

            if ($phone) {
                $userData->setPhone(Util::hash(trim($phone)));
            }

            // 2. Setup Custom Data
            $customData = new CustomData();
            if ($purchaseData) {
                if (isset($purchaseData['currency'])) {
                    $customData->setCurrency($purchaseData['currency']);
                }
                if (isset($purchaseData['value'])) {
                    $customData->setValue((float)$purchaseData['value']);
                }
                if (isset($purchaseData['ids'])) {
                    $customData->setContentIds((array)$purchaseData['ids']);
                }
                $customData->setContentType('product');
            }

            // 3. Create the Event
            $event = (new Event())
                ->setEventName($eventName->value)
                ->setEventTime(time())
                ->setUserData($userData)
                ->setCustomData($customData)
                ->setActionSource(ActionSource::WEBSITE)
                ->setEventId(uniqid('evt_'));

            if ($url) {
                $event->setEventSourceUrl($url);
            }

            // 4. Send the Event Request
            $eventRequest = (new EventRequest(self::$pixelId))
                ->setEvents([$event]);

            $eventRequest->execute();
            Log::info("fbpixel FaceBook Event End", [
                'eventName' => $eventName->value
            ]);
        } catch (\Exception $e) {
            Log::error("Facebook CAPI Error [{$eventName->value}]: " . $e->getMessage(), [
                'email' => $email,
                'phone' => $phone,
                'purchaseData' => $purchaseData,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
