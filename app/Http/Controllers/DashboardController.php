<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the appropriate dashboard based on user role.
     */
    public function index(Request $request): Response
    {
        $user = Auth::user();
        
        // Determine which dashboard to show based on user role
        $dashboardView = $this->getDashboardView($user);
        
        // Pass common data to all dashboards
        $commonData = [
            'auth' => [
                'user' => $user,
            ],
        ];
        
        return Inertia::render($dashboardView, $commonData);
    }
    
    /**
     * Determine which dashboard view to render based on user role.
     */
    private function getDashboardView($user): string
    {
        // Check for specific roles in order of priority
        if ($user->hasRole('super_admin')) {
            return 'DashboardAdmin';
        }
        
        if ($user->hasRole('business_owner')) {
            return 'DashboardBusiness';
        }
        
        if ($user->hasRole('customer')) {
            return 'DashboardCustomer';
        }
        
        // Default to customer dashboard if no specific role
        return 'DashboardCustomer';
    }
}
