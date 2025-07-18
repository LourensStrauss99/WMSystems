<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jobcard;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Inventory;
use Illuminate\Support\Facades\Auth;

class MobileJobcardController extends Controller
{
    public function edit($jobcard)
    {
        $jobcard = Jobcard::with(['employees', 'inventory'])->findOrFail($jobcard);
        $clients = Client::all();
        $employees = Employee::all();
        $inventory = Inventory::all();
        $assignedInventory = $jobcard->inventory->map(function($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'quantity' => $item->pivot->quantity,
            ];
        });
        return view('mobile app.jobcard-editor-mobile', compact('jobcard', 'clients', 'employees', 'inventory', 'assignedInventory'));
    }

    public function update(Request $request, $jobcard)
    {
        // Implement update logic here
        // Validate and update the jobcard, handle file uploads, etc.
        return response()->json(['success' => true]);
    }

    public function showLoginForm()
    {
        return view('mobile app.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('jobcard.mobile.index');
        }
        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    public function mobileIndex(Request $request)
    {
        $employee = Auth::user();
        if (!$employee) {
            return redirect()->route('mobile.login');
        }
        $jobcards = Jobcard::whereHas('employees', function($q) use ($employee) {
            $q->where('employee_jobcard.employee_id', $employee->id);
        })->with(['client', 'employees'])->orderByDesc('created_at')->get();

        return view('mobile app.index.mobile', compact('jobcards'));
    }
}
