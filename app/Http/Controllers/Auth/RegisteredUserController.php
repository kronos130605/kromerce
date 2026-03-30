<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\TranslationHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Store;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('modules/auth/pages/Register', [
            'translations' => TranslationHelper::forPreset('auth'),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'phone' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'user_type' => 'required|in:' . implode(',', array_keys(config('roles.registration_types', ['customer', 'business_owner']))),
            'store_name' => 'required_if:user_type,business_owner|string|max:255|nullable',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'status' => 'active',
        ]);

        // Assign role using config mapping
        $userType = $request->user_type;
        $role = config('roles.registration_types.' . $userType, $userType);
        $user->assignRole($role);

        // Create store for business owners
        if ($request->user_type === 'business_owner') {
            // Creating a store
            $store = DB::transaction(function () use ($request, $user, $role) {
                // Generate UUID
                $uuid = Str::uuid();

                // Insert store manually to bypass tenancy package interception
                $storeId = DB::table('stores')->insertGetId([
                    'name' => $request->store_name,
                    'slug' => Str::slug($request->store_name),
                    'owner_id' => $user->id,
                    'uuid' => $uuid,
                    'business_type' => 'retail',
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Create default currency configuration for store
                DB::table('store_currency_configs')->insert([
                    'id' => $uuid,
                    'store_id' => $storeId,
                    'default_currency' => 'USD',
                    'display_currencies' => json_encode(['USD', 'EUR', 'CUP']),
                    'use_custom_rates' => false,
                    'auto_update_rates' => false,
                    'rate_update_frequency' => 'weekly',
                    'historical_retention_years' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Get the store instance for relationships
                $store = \App\Models\Store::find($storeId);

                // Create domain for store
                $store->domains()->create([
                    'domain' => Str::slug($request->store_name) . '.' . config('tenancy.central_domains')[0] ?? 'kromerce.test',
                ]);

                // Associate user with store
                DB::table('store_users')->insert([
                    'store_id' => $storeId,
                    'user_id' => $user->id,
                    'is_active' => true,
                    'joined_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return $store;
            });
        }

        event(new Registered($user));

        Auth::login($user);

        // Redirect based on user type
        if ($request->user_type === 'business_owner') {
            return redirect()->route('dashboard');
        }

        return redirect()->route('dashboard');
    }
}
