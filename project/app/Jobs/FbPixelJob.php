<?php

namespace App\Jobs;

use App\Http\Controllers\Utils\FacebookEvent;
use App\Http\Controllers\Utils\FbPixel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FbPixelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $eventName;
    protected $clientIp;
    protected $userAgent;
    protected $fbclid;
    protected $fbp;
    protected $name;
    protected $email;
    protected $phone;
    protected $url;
    protected $purchaseData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
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
    ) {
        $this->eventName = $eventName;
        $this->clientIp = $clientIp;
        $this->userAgent = $userAgent;
        $this->fbclid = $fbclid;
        $this->fbp = $fbp;
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->url = $url;
        $this->purchaseData = $purchaseData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        FbPixel::purchaseJobEvent(
            $this->eventName,
            $this->clientIp,
            $this->userAgent,
            $this->fbclid,
            $this->fbp,
            $this->name,
            $this->email,
            $this->phone,
            $this->url,
            $this->purchaseData
        );
    }
}
