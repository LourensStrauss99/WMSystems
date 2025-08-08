<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LandlordMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this area.');
        }

        $user = Auth::user();
        
        // Check if user has landlord privileges (admin_level = 10 and is_landlord = 1)
        if ($user->admin_level !== 10 || $user->is_landlord !== 1) {
            abort(403, 'Access denied. Landlord privileges required.');
        }

        return $next($request);
    }
}
