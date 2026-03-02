<?php

namespace App\Repositories;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenantRepository
{
    /**
     * Find tenant by slug
     */
    public function findBySlug(string $slug): ?Tenant
    {
        try {
            return Tenant::where('slug', $slug)->first();
        } catch (\Exception $e) {
            Log::error('Error finding tenant by slug', [
                'slug' => $slug,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Create new tenant
     */
    public function create(array $data): ?Tenant
    {
        try {
            return Tenant::create($data);
        } catch (\Exception $e) {
            Log::error('Error creating tenant', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Create tenant using DB direct insert
     */
    public function createDirect(array $data): ?int
    {
        try {
            return DB::table('tenants')->insertGetId($data);
        } catch (\Exception $e) {
            Log::error('Error creating tenant directly', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Find tenant by ID
     */
    public function findById(int $id): ?Tenant
    {
        try {
            return Tenant::find($id);
        } catch (\Exception $e) {
            Log::error('Error finding tenant by ID', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Get tenant by UUID
     */
    public function findByUuid(string $uuid): ?Tenant
    {
        try {
            return Tenant::where('uuid', $uuid)->first();
        } catch (\Exception $e) {
            Log::error('Error finding tenant by UUID', [
                'uuid' => $uuid,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Get all active tenants
     */
    public function getActiveTenants(): Collection
    {
        try {
            return Tenant::where('is_active', true)->get();
        } catch (\Exception $e) {
            Log::error('Error getting active tenants', [
                'error' => $e->getMessage(),
            ]);
            return collect();
        }
    }

    /**
     * Get tenants by owner
     */
    public function getTenantsByOwner(int $ownerId): Collection
    {
        try {
            return Tenant::where('owner_id', $ownerId)->get();
        } catch (\Exception $e) {
            Log::error('Error getting tenants by owner', [
                'owner_id' => $ownerId,
                'error' => $e->getMessage(),
            ]);
            return collect();
        }
    }

    /**
     * Update tenant data
     */
    public function updateData(int $tenantId, array $data): bool
    {
        try {
            $updated = DB::table('tenants')
                ->where('id', $tenantId)
                ->update(['data' => json_encode($data)]);
            
            return $updated > 0;
        } catch (\Exception $e) {
            Log::error('Error updating tenant data', [
                'tenant_id' => $tenantId,
                'data' => $data,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get available owner ID
     */
    public function getAvailableOwnerId(): ?int
    {
        try {
            $user = DB::table('users')
                ->orderBy('id', 'asc')
                ->first();
            
            return $user ? $user->id : null;
        } catch (\Exception $e) {
            Log::error('Error getting available owner ID', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Check if tenant exists by slug
     */
    public function existsBySlug(string $slug): bool
    {
        try {
            return Tenant::where('slug', $slug)->exists();
        } catch (\Exception $e) {
            Log::error('Error checking tenant existence', [
                'slug' => $slug,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Delete tenant
     */
    public function delete(int $tenantId): bool
    {
        try {
            return Tenant::destroy($tenantId) > 0;
        } catch (\Exception $e) {
            Log::error('Error deleting tenant', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get tenant with relationships
     */
    public function getWithRelationships(int $tenantId, array $relationships = []): ?Tenant
    {
        try {
            $query = Tenant::where('id', $tenantId);
            
            foreach ($relationships as $relation) {
                $query->with($relation);
            }
            
            return $query->first();
        } catch (\Exception $e) {
            Log::error('Error getting tenant with relationships', [
                'tenant_id' => $tenantId,
                'relationships' => $relationships,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
