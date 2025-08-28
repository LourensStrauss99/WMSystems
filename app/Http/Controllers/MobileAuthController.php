<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Jobcard;
use Illuminate\Support\Facades\Log;

class MobileAuthController extends Controller
{
    /**
     * Mobile login using email and password
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $employee = Employee::where('email', $validated['email'])->where('is_active', true)->first();
        if (!$employee || !Hash::check($validated['password'], $employee->password)) {
            Log::debug('Mobile login failed for email: ' . $validated['email']);
            return back()
                ->withErrors(['error' => 'Invalid credentials.'])
                ->withInput($request->only('email'));
        }

        // Set employee_id in session for mobile authentication
        session(['mobile_employee_id' => $employee->id]);
    Log::debug('Mobile login success, session set for employee_id: ' . $employee->id);

        // Redirect to jobcard list view
        return redirect()->route('mobile.jobcard.index');
    }

    /**
     * List jobcards for signed-in employee (token required)
     * Request: { "token": "unique_employee_token" }
     */
    public function listJobcards(Request $request)
    {
        $token = $request->input('token');
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token required.'
            ], 400);
        }

        $employee = Employee::where('employee_id', $token)->where('is_active', true)->first();
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or inactive token.'
            ], 401);
        }

        $jobcards = $employee->jobcards()->where('status', '!=', 'completed')->get();

        return response()->json([
            'success' => true,
            'jobcards' => $jobcards
        ]);
    }
}
