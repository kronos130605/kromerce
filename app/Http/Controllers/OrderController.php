<?php

namespace App\Http\Controllers;

use App\Helpers\TranslationHelper;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {
        // Apply business role middleware to all order methods
        $this->middleware('role:business_owner');
    }

    /**
     * Display orders page with DataTable.
     */
    public function index(Request $request): Response|JsonResponse
    {
        try {
            $store = $this->validateStore();

            // Get orders data using the service
            $filters = $request->all();
            $orders = $this->orderService->getOrdersForStore($store, $filters);
            $statistics = $this->orderService->getStatisticsForStore($store);

            // Return orders page with SPA structure
            return Inertia::render('orders/Index', [
                'orders' => $orders,
                'filters' => $filters,
                'statistics' => $statistics,
                'translations' => TranslationHelper::forPreset('orders'),
            ]);

        } catch (\Exception $e) {
            Log::error('OrderController::index - ERROR', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'path' => $request->path(),
            ]);

            throw $e;
        }
    }

    /**
     * Display the specified order.
     */
    public function show(Request $request, Order $order): Response|JsonResponse|RedirectResponse
    {
        try {
            $store = $this->validateStore();

            // Verify order belongs to the store
            if ($order->store_id !== $store->id) {
                if ($request->wantsJson()) {
                    return $this->error('Order not found', 404);
                }
                abort(404);
            }

            $orderData = $this->orderService->getOrderWithDetails($order);

            if ($request->wantsJson()) {
                return $this->success($orderData);
            }

            return Inertia::render('orders/Show', [
                'order' => $orderData,
            ]);

        } catch (\Exception $e) {
            Log::error('OrderController::show - ERROR', [
                'error' => $e->getMessage(),
                'order_id' => $order->id ?? null,
                'user_id' => auth()->id(),
            ]);

            throw $e;
        }
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, Order $order): JsonResponse|RedirectResponse
    {
        try {
            $store = $this->validateStore();

            // Verify order belongs to the store
            if ($order->store_id !== $store->id) {
                return $this->error('Order not found', 404);
            }

            $validated = $request->validate([
                'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled',
                'notes' => 'nullable|string|max:500',
            ]);

            $updated = $this->orderService->updateOrderStatus(
                $order,
                $validated['status'],
                $validated['notes'] ?? null,
                auth()->id()
            );

            if (!$updated) {
                return $this->error('Failed to update order status', 500);
            }

            if ($request->wantsJson()) {
                return $this->success(null, 'Order status updated successfully');
            }

            return redirect()->back()->with('success', 'Order status updated successfully');

        } catch (\Exception $e) {
            Log::error('OrderController::updateStatus - ERROR', [
                'error' => $e->getMessage(),
                'order_id' => $order->id ?? null,
                'user_id' => auth()->id(),
            ]);

            return $this->error('Failed to update order status', 500);
        }
    }

    /**
     * Update payment status.
     */
    public function updatePaymentStatus(Request $request, Order $order): JsonResponse|RedirectResponse
    {
        try {
            $store = $this->validateStore();

            // Verify order belongs to the store
            if ($order->store_id !== $store->id) {
                return $this->error('Order not found', 404);
            }

            $validated = $request->validate([
                'payment_status' => 'required|string|in:pending,paid,failed,refunded',
            ]);

            $updated = $this->orderService->updatePaymentStatus(
                $order,
                $validated['payment_status']
            );

            if (!$updated) {
                return $this->error('Failed to update payment status', 500);
            }

            if ($request->wantsJson()) {
                return $this->success(null, 'Payment status updated successfully');
            }

            return redirect()->back()->with('success', 'Payment status updated successfully');

        } catch (\Exception $e) {
            Log::error('OrderController::updatePaymentStatus - ERROR', [
                'error' => $e->getMessage(),
                'order_id' => $order->id ?? null,
                'user_id' => auth()->id(),
            ]);

            return $this->error('Failed to update payment status', 500);
        }
    }

    /**
     * Cancel order.
     */
    public function cancel(Request $request, Order $order): JsonResponse|RedirectResponse
    {
        try {
            $store = $this->validateStore();

            // Verify order belongs to the store
            if ($order->store_id !== $store->id) {
                return $this->error('Order not found', 404);
            }

            $validated = $request->validate([
                'reason' => 'nullable|string|max:500',
            ]);

            $cancelled = $this->orderService->cancelOrder(
                $order,
                $validated['reason'] ?? null,
                auth()->id()
            );

            if (!$cancelled) {
                return $this->error('Failed to cancel order', 500);
            }

            if ($request->wantsJson()) {
                return $this->success(null, 'Order cancelled successfully');
            }

            return redirect()->back()->with('success', 'Order cancelled successfully');

        } catch (\Exception $e) {
            Log::error('OrderController::cancel - ERROR', [
                'error' => $e->getMessage(),
                'order_id' => $order->id ?? null,
                'user_id' => auth()->id(),
            ]);

            return $this->error('Failed to cancel order', 500);
        }
    }
}
