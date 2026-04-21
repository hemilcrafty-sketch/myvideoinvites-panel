<?php

namespace App\Listeners;

use App\Events\TemplatePurchased;
use App\Services\FreelancerEarningsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Listener to process freelancer earnings when a template is purchased
 */
class ProcessFreelancerEarnings implements ShouldQueue
{
  use InteractsWithQueue;

  protected FreelancerEarningsService $earningsService;

  /**
   * Create the event listener.
   */
  public function __construct(FreelancerEarningsService $earningsService)
  {
    $this->earningsService = $earningsService;
  }

  /**
   * Handle the event.
   */
  public function handle(TemplatePurchased $event): void
  {
    $purchase = $event->purchase;

    // Only process successful payments
    if ($purchase->payment_status !== 'success' && $purchase->status != 1) {
      Log::info("Skipping freelancer earnings - payment not successful", [
        'purchase_id' => $purchase->id,
        'payment_status' => $purchase->payment_status,
      ]);
      return;
    }

    // Process the earnings
    $result = $this->earningsService->processTemplatePurchase(
      purchaseId: $purchase->id,
      productId: $event->productId,
      productType: $event->productType,
      purchaseAmount: (float) $purchase->amount,
      buyerUserId: $purchase->user_id,
      currency: $purchase->currency_code ?? 'INR'
    );

    if ($result) {
      Log::info("Freelancer earnings processed successfully", $result);
    }
  }

  /**
   * Handle a job failure.
   */
  public function failed(TemplatePurchased $event, \Throwable $exception): void
  {
    Log::error("Failed to process freelancer earnings", [
      'purchase_id' => $event->purchase->id,
      'error' => $exception->getMessage(),
      'trace' => $exception->getTraceAsString(),
    ]);
  }
}
