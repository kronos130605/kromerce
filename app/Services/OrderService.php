<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Store;
use App\Repositories\OrderRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderService
{
    public function __construct(
        private OrderRepository $orderRepository
    ) {}

    /**
     * Get orders for store with filters.
     */
    public function getOrdersForStore(Store $store, array $filters = []): LengthAwarePaginator
    {
        try {
            $paginator = $this->orderRepository->paginateForStore($store->id, $filters);
            
            // Transform items to include calculated fields
            $transformedItems = $paginator->getCollection()->map(function ($order) {
                return [
                    'id' => $order->uuid,
                    'uuid' => $order->uuid,
                    'order_number' => $order->full_order_number,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'currency' => $order->currency,
                    'total_amount' => $order->total_amount,
                    'formatted_total' => $order->formatted_total,
                    'customer' => $order->customer ? [
                        'id' => $order->customer->id,
                        'name' => $order->customer->name,
                        'email' => $order->customer->email,
                    ] : null,
                    'items_count' => $order->items()->count(),
                    'created_at' => $order->created_at->toISOString(),
                    'is_paid' => $order->isPaid(),
                    'is_shipped' => $order->isShipped(),
                    'is_delivered' => $order->isDelivered(),
                    'is_cancelled' => $order->isCancelled(),
                ];
            });
            
            return new LengthAwarePaginator(
                $transformedItems,
                $paginator->total(),
                $paginator->perPage(),
                $paginator->currentPage(),
                ['path' => $paginator->path()]
            );
        } catch (\Exception $e) {
            throw new \Exception('Failed to retrieve orders: ' . $e->getMessage());
        }
    }

    /**
     * Get order with full details.
     */
    public function getOrderWithDetails(Order $order): array
    {
        try {
            return [
                'id' => $order->uuid,
                'uuid' => $order->uuid,
                'order_number' => $order->full_order_number,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'fulfillment_status' => $order->fulfillment_status,
                'currency' => $order->currency,
                'subtotal' => $order->subtotal,
                'tax_amount' => $order->tax_amount,
                'shipping_amount' => $order->shipping_amount,
                'discount_amount' => $order->discount_amount,
                'total_amount' => $order->total_amount,
                'formatted_total' => $order->formatted_total,
                'payment_method' => $order->payment_method_title ?: $order->payment_method,
                'shipping_method' => $order->shipping_method_title ?: $order->shipping_method,
                'shipping_address' => $order->shipping_address,
                'billing_address' => $order->billing_address,
                'notes' => $order->notes,
                'customer_notes' => $order->customer_notes,
                'shipped_at' => $order->shipped_at?->toISOString(),
                'delivered_at' => $order->delivered_at?->toISOString(),
                'paid_at' => $order->paid_at?->toISOString(),
                'cancelled_at' => $order->cancelled_at?->toISOString(),
                'created_at' => $order->created_at->toISOString(),
                'customer' => $order->customer ? [
                    'id' => $order->customer->id,
                    'name' => $order->customer->name,
                    'email' => $order->customer->email,
                    'phone' => $order->customer->phone ?? null,
                ] : null,
                'items' => $order->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_name' => $item->product_name,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'total_price' => $item->total_price,
                        'sku' => $item->sku,
                    ];
                }),
                'status_history' => $order->statusHistory->map(function ($history) {
                    return [
                        'status' => $history->status,
                        'payment_status' => $history->payment_status,
                        'fulfillment_status' => $history->fulfillment_status,
                        'notes' => $history->notes,
                        'created_at' => $history->created_at->toISOString(),
                    ];
                }),
            ];
        } catch (\Exception $e) {
            throw new \Exception('Failed to retrieve order details: ' . $e->getMessage());
        }
    }

    /**
     * Get order statistics for store.
     */
    public function getStatisticsForStore(Store $store): array
    {
        try {
            return $this->orderRepository->getStatistics($store->id);
        } catch (\Exception $e) {
            throw new \Exception('Failed to retrieve statistics: ' . $e->getMessage());
        }
    }

    /**
     * Update order status.
     */
    public function updateOrderStatus(Order $order, string $status, ?string $notes = null, ?int $userId = null): bool
    {
        try {
            $oldStatus = $order->status;
            
            $order->status = $status;
            
            // Update timestamps based on status
            switch ($status) {
                case 'shipped':
                    $order->shipped_at = now();
                    $order->fulfillment_status = 'shipped';
                    break;
                case 'delivered':
                    $order->delivered_at = now();
                    $order->fulfillment_status = 'delivered';
                    break;
            }
            
            $order->save();
            
            // Add to status history
            $order->addStatusHistory($status, $notes, $userId);
            
            return true;
        } catch (\Exception $e) {
            throw new \Exception('Failed to update order status: ' . $e->getMessage());
        }
    }

    /**
     * Update payment status.
     */
    public function updatePaymentStatus(Order $order, string $paymentStatus): bool
    {
        try {
            $order->payment_status = $paymentStatus;
            
            if ($paymentStatus === 'paid') {
                $order->paid_at = now();
            }
            
            $order->save();
            
            return true;
        } catch (\Exception $e) {
            throw new \Exception('Failed to update payment status: ' . $e->getMessage());
        }
    }

    /**
     * Cancel order.
     */
    public function cancelOrder(Order $order, ?string $reason = null, ?int $userId = null): bool
    {
        try {
            $order->status = 'cancelled';
            $order->cancelled_at = now();
            $order->save();
            
            // Add to status history
            $order->addStatusHistory('cancelled', $reason, $userId);
            
            return true;
        } catch (\Exception $e) {
            throw new \Exception('Failed to cancel order: ' . $e->getMessage());
        }
    }
}
