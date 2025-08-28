<?php
namespace App\Http\Middleware;
use Illuminate\Support\Facades\Log;
use Closure;
use Illuminate\Http\Request;

class EmployeeAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->has('mobile_employee_id')) {
            Log::debug('EmployeeAuth: mobile_employee_id not found in session.');
            return redirect()->route('mobile.login.form')->withErrors(['auth' => 'Please log in as an employee.']);
        }
        Log::debug('EmployeeAuth: mobile_employee_id found in session: ' . $request->session()->get('mobile_employee_id'));
        return $next($request);
    }
}
