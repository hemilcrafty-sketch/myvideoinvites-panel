<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Api\Utils\ApiController;
use App\Models\Order;
use App\Models\Revenue\MasterPurchaseHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RazorpayWebhookController extends ApiController
{
    public function handleWebhook(Request $request): JsonResponse
    {
        $webhookSecret = "ykNDXu6SqjfuQo6cqc9EmMvVudF6hWT9";

        $signature = $request->header('X-Razorpay-Signature') ?? "";
        $payload = $request->getContent() ?? "";

        if (!$this->verifySignature($payload, $signature, $webhookSecret)) {
            return response()->json(['status' => 'invalid signature'], 400);
        }

        $event = $request->event ?? null;
        $payloadData = $request->payload ?? []; //plan_RbxL9SiaJnw9j5
        
        switch ($event) {
            case 'order.paid':         // ✅ Order paid
                $this->handleOrderPaid($request, $payloadData);
                break;

            case 'payment.authorized': // ⏳ Payment authorized
                $this->handlePaymentAuthorized($payloadData);
                break;

            case 'payment.captured':   // ✅ Payment success
                $this->handlePaymentSuccess($request, $payloadData);
                break;

            case 'payment.failed':     // ❌ Payment failed
                $this->handlePaymentFailed($payloadData);
                break;

            case 'refund.created':     // 🟡 Refund initiated
                $this->handleRefundCreated($payloadData);
                break;

            case 'refund.processed':   // 🔄 Refund success
                $this->handleRefundProcessed($payloadData);
                break;

            case 'refund.failed':      // ❌ Refund failed
                $this->handleRefundFailed($payloadData);
                break;

            case 'payment.dispute.lost': // ❌ Refund failed
                $this->handleDisputeLost($payloadData);
                break;
        }

        return response()->json(['status' => 'ok']);
    }

    private function verifySignature($payload, $signature, $secret): bool
    {
        $expected = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expected, $signature);
    }

    private function handleOrderPaid(Request $request, $payload): void
    {
        $orderEntity = $payload['order']['entity'];
        $payment = $payload['payment']['entity'];

        $notes = $payment['notes'] ?? [];
        $craftyId = $notes['craftyId'] ?? null;
        $query = Order::where('order_id', $orderEntity['id']);
        if (!empty($craftyId)) $query->orWhere('crafty_id', $craftyId);
        $order = $query->first();

        if ($order) {
            $order->status = 'paid';
            $order->razorpay_payment_id = $payment['id'];
            if (empty($order->order_id)) $order->order_id = $orderEntity['id'];
            $order->payment_id = $payment['id'];
            $order->amount = $payment['amount'] / 100;
            $order->paid = $payment['amount'] / 100;
            $order->save();
            PaymentController::removeOrdersDuplicate($order);
        }


        (new PaymentController($request))->enterTransData(
            request: $request,
            transaction_id: $payment['id'],
            method: 'Razorpay',
            isManual: 0);
    }

    private function handlePaymentAuthorized($payload): void
    {
        $payment = $payload['payment']['entity'];
        $order = Order::where('razorpay_order_id', $payment['order_id'])->first();
        if ($order && !in_array($order->status, ["paid", "success"], true)) {
            $order->status = 'processing';
            $order->save();
        }
    }

    private function handlePaymentSuccess(Request $request, $payload): void
    {
        $payment = $payload['payment']['entity'];

        $order = Order::where('razorpay_order_id', $payment['order_id'])->first();
        if ($order) {
            if ($order->status !== 'paid') $order->status = 'success';
            $order->save();
        }
    }

    private function handlePaymentFailed($payload): void
    {
        $payment = $payload['payment']['entity'];
        $order = Order::where('razorpay_order_id', $payment['order_id'])->first();
        if ($order && !in_array($order->status, ["paid", "success"], true)) {
            $order->status = 'failed';
            $order->save();
        }
    }

    private function handleRefundCreated($payload): void
    {
        $refund = $payload['refund']['entity'];
        $order = Order::where('razorpay_payment_id', $refund['payment_id'])->first();
        if ($order) {
            $order->status = 'refund_initiated';
            $order->save();
        }
        MasterPurchaseHistory::whereTransactionId($refund['payment_id'])->update(['payment_status' => 'refund_initiated']);
    }

    private function handleRefundProcessed($payload): void
    {
        $refund = $payload['refund']['entity'];
        $order = Order::where('razorpay_payment_id', $refund['payment_id'])->first();
        if ($order) {
            $order->status = 'refunded';
            $order->save();
        }
        MasterPurchaseHistory::whereTransactionId($refund['payment_id'])->update(['payment_status' => 'refunded']);
    }

    private function handleRefundFailed($payload): void
    {
        $refund = $payload['refund']['entity'];
        $order = Order::where('razorpay_payment_id', $refund['payment_id'])->first();
        if ($order) {
            $order->status = 'refund_failed';
            $order->save();
        }
        MasterPurchaseHistory::whereTransactionId($refund['payment_id'])->update(['payment_status' => 'refund_failed']);
    }

    private function handleDisputeLost($payload): void
    {
        $refund = $payload['refund']['entity'];
        $order = Order::where('razorpay_payment_id', $refund['payment_id'])->first();
        if ($order) {
            $order->status = 'refunded';
            $order->save();
        }
        MasterPurchaseHistory::whereTransactionId($refund['payment_id'])->update(['payment_status' => 'lost_dispute']);
    }
}
