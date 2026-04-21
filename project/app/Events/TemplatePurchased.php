<?php

namespace App\Events;

use App\Models\Revenue\MasterPurchaseHistory;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when a template/design is purchased
 */
class TemplatePurchased
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public MasterPurchaseHistory $purchase;
  public string $productId;
  public string $productType;

  /**
   * Create a new event instance.
   */
  public function __construct(MasterPurchaseHistory $purchase, string $productId, string $productType)
  {
    $this->purchase = $purchase;
    $this->productId = $productId;
    $this->productType = $productType;
  }
}
