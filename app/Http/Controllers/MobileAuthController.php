<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;
use App\Traits\TenantDatabaseSwitch;

class MobileAuthController extends Controller
{
    use TenantDatabaseSwitch;
    
    public function login(Request $request)
    {
        $this->switchToTenantDatabase();
        
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $employee = Employee::where('email', $request->email)->first();
        if ($employee && Hash::check($request->password, $employee->password)) {
            // Store employee ID in session
            $request->session()->put('mobile_employee_id', $employee->id);
            return redirect()->route('mobile.jobcards.index');
        }
        return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
    }
} 