<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StoreResource;
use App\Http\Resources\StoreCollection;
use App\Http\Requests\StoreRequest;
use App\Services\StoreService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    protected $storeService;

    public function __construct(StoreService $storeService)
    {
        $this->storeService = $storeService;
    }

    /**
     * Display a listing of stores.
     */
    public function index(\Illuminate\Http\Request $request): JsonResponse
    {
        $stores = $this->storeService->getAccessibleStores()
            ->when($request->business_type, function ($query, $businessType) {
                $query->where('business_type', $businessType);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->verified, function ($query, $verified) {
                $query->where('verified_business', $verified === 'true');
            })
            ->when($request->search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => new StoreCollection($stores),
            'meta' => [
                'total' => $stores->total(),
                'per_page' => $stores->perPage(),
                'current_page' => $stores->currentPage(),
                'last_page' => $stores->lastPage(),
            ]
        ]);
    }

    /**
     * Store a newly created store.
     */
    public function store(StoreRequest $request): JsonResponse
    {
        try {
            $store = $this->storeService->createStore($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Store created successfully',
                'data' => new StoreResource($store),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create store: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified store.
     */
    public function show(\App\Models\Store $store): JsonResponse
    {
        $store->load(['owner', 'currencyConfig', 'contacts', 'paymentMethods']);

        return response()->json([
            'success' => true,
            'data' => new StoreResource($store),
        ]);
    }

    /**
     * Update the specified store.
     */
    public function update(StoreRequest $request, \App\Models\Store $store): JsonResponse
    {
        $this->authorize('update', $store);

        try {
            $store->update($request->validated());

            // Update currency config if provided
            if ($request->has(['default_currency', 'display_currencies'])) {
                $this->storeService->updateStoreCurrencyConfig($store, [
                    'default_currency' => $request->default_currency,
                    'display_currencies' => $request->display_currencies,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Store updated successfully',
                'data' => new StoreResource($store),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update store: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified store.
     */
    public function destroy(\App\Models\Store $store): JsonResponse
    {
        $this->authorize('delete', $store);

        try {
            $store->delete();

            return response()->json([
                'success' => true,
                'message' => 'Store deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete store: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle store status (active/inactive).
     */
    public function toggleStatus(\App\Models\Store $store): JsonResponse
    {
        $this->authorize('update', $store);

        try {
            $newStatus = $this->storeService->toggleStoreStatus($store);

            return response()->json([
                'success' => true,
                'message' => "Store status changed to {$newStatus}",
                'data' => [
                    'status' => $newStatus,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle store status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get store statistics.
     */
    public function statistics(\App\Models\Store $store): JsonResponse
    {
        $this->authorize('view', $store);

        try {
            $stats = $this->storeService->getStoreStatistics($store);

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get store statistics: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify store.
     */
    public function verify(\App\Models\Store $store): JsonResponse
    {
        $this->authorize('update', $store);

        try {
            $store = $this->storeService->verifyStore($store);

            return response()->json([
                'success' => true,
                'message' => 'Store verified successfully',
                'data' => new StoreResource($store),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify store: ' . $e->getMessage(),
            ], 500);
        }
    }
}
