<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EmployeeAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->has('mobile_employee_id')) {
            return redirect()->route('mobile.login.form')->withErrors(['auth' => 'Please log in as an employee.']);
        }
        return $next($request);
    }
}
