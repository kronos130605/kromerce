<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        return Inertia::render('Auth/Register');
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
            'user_type' => 'required|in:customer,business_owner',
            'tenant_name' => 'required_if:user_type,business_owner|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        // Assign role
        $user->assignRole($request->user_type);

        // Create tenant for business owners
        if ($request->user_type === 'business_owner') {
            $tenant = Tenant::create([
                'name' => $request->tenant_name,
                'slug' => Str::slug($request->tenant_name),
                'owner_id' => $user->id,
                'branding_config' => [
                    'primary_color' => '#3B82F6',
                    'secondary_color' => '#10B981',
                    'accent_color' => '#F59E0B',
                    'theme' => 'light',
                ],
            ]);

            // Create domain for tenant
            $tenant->domains()->create([
                'domain' => $tenant->slug . '.' . config('tenancy.central_domains')[0] ?? 'kromerce.test',
            ]);

            // Associate user with tenant
            $user->tenants()->attach($tenant->id, ['role' => 'owner']);
            $user->update(['current_tenant_id' => $tenant->id]);
        }

        event(new Registered($user));

        Auth::login($user);

        // Redirect based on user type
        if ($request->user_type === 'business_owner') {
            return redirect()->route('business.dashboard');
        }

        return redirect()->route('dashboard');
    }
}
