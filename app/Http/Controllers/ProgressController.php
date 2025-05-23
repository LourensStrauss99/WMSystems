<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jobcard;

class ProgressController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Jobcard::with(['client', 'employees']);

        // Search by client name only
        if ($request->filled('client')) {
            $query->whereHas('client', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->client . '%');
            });
        }

        // Only show in progress, assigned, or completed
        $query->whereIn('status', ['in progress', 'assigned', 'completed']);

        $jobcards = $query->orderBy('job_date', 'desc')->limit(10)->get();

        return view('progress', compact('jobcards'));
    }

    public function show($id)
    {
        $jobcard = Jobcard::with(['client', 'employees', 'spares'])->findOrFail($id);
       //dd($jobcard); // This will dump the jobcard and stop execution
        $employees = \App\Models\Employee::all();
        $inventory = \App\Models\Inventory::all();
        return view('jobcard.show', compact('jobcard', 'employees', 'inventory'));
    }
}
