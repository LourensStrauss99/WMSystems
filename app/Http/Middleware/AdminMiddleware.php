<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || (!$user->is_superuser && !in_array($user->admin_level, [3,4]))) {
            return redirect()->route('dashboard')->with('admin_error', 'Unauthorized entry prohibited.');
        }

        return $next($request);
    }
}
