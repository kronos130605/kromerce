<?php

namespace App\Jobs\Orders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOrderStatusNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;
    public int $timeout = 60;

    public function __construct(
        private Order $order,
        private string $oldStatus,
        private string $newStatus,
        private ?string $notes = null,
        private ?int $userId = null
    ) {}

    public function handle(): void
    {
        try {
            $order = $this->order;
            $customer = $order->customer;

            if (!$customer) {
                Log::warning('Cannot send order notification - no customer', [
                    'order_id' => $order->id,
                ]);
                return;
            }

            // Prepare notification data
            $notificationData = [
                'order_id' => $order->uuid,
                'order_number' => $order->full_order_number,
                'old_status' => $this->oldStatus,
                'new_status' => $this->newStatus,
                'notes' => $this->notes,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'total' => $order->formatted_total,
                'store_name' => $order->store?->name ?? 'Kromerce',
            ];

            // TODO: Implement actual email sending when Mail system is ready
            // For now, just log the notification
            Log::info('Order status notification prepared', $notificationData);

            // When Mail is implemented:
            // Mail::to($customer->email)->send(new OrderStatusUpdated($notificationData));

            // Also notify store owners if critical status
            if (in_array($this->newStatus, ['cancelled', 'shipped', 'delivered'])) {
                $this->notifyStoreOwners($order, $notificationData);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send order status notification', [
                'order_id' => $this->order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    private function notifyStoreOwners(Order $order, array $data): void
    {
        try {
            $store = $order->store;
            if (!$store) {
                return;
            }

            // Get store owners/admins
            $owners = $store->users()->whereHas('roles', function ($query) {
                $query->whereIn('name', ['business_owner', 'store_admin']);
            })->get();

            foreach ($owners as $owner) {
                Log::info('Store owner notification prepared', [
                    'order_id' => $order->id,
                    'owner_id' => $owner->id,
                    'owner_email' => $owner->email,
                    'status' => $this->newStatus,
                ]);

                // TODO: Mail::to($owner->email)->send(new OrderStatusAlertForOwner($data));
            }
        } catch (\Exception $e) {
            Log::error('Failed to notify store owners', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SendOrderStatusNotification job failed permanently', [
            'order_id' => $this->order->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
