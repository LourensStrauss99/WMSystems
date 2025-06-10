<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterSettingsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Allow if user is superuser (is_superuser == 1) OR admin_level == 4
        if (!$user || (!$user->is_superuser && !in_array($user->admin_level, [3,4]))) {
            return redirect()->route('dashboard')->with('admin_error', 'Unauthorized entry prohibited.');
        }

        return $next($request);
    }
}
