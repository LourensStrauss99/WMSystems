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

        if (!$user || (!$user->is_superuser && $user->admin_level != 3)) {
            return redirect()->route('dashboard')->with('admin_error', 'Unauthorized entry prohibited.');
        }

        return $next($request);
    }
}
