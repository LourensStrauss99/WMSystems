<?php

namespace App\Http\Controllers;

use App\Models\Jobcard;
use App\Models\Employee;
use App\Models\Inventory;
use Illuminate\Http\Request;

class JobcardController extends Controller
{
    public function index(Request $request)
    {
        $jobcards = [];
        if ($request->filled('client')) {
            $jobcards = \App\Models\Jobcard::whereHas('client', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->client . '%');
            })->with('client')->get();
        }
        return view('jobcard.index', compact('jobcards'));
    }

    public function show(Jobcard $jobcard)
    {
        $employees = \App\Models\Employee::all();
        $statuses = ['in progress', 'assigned', 'completed'];
        $inventory = \App\Models\Inventory::all(); // <-- Add this line

        return view('jobcard.show', compact('jobcard', 'employees', 'statuses', 'inventory'));
    }

    public function update(Request $request, Jobcard $jobcard)
    {
        $inventoryData = json_decode($request->inventory_data, true) ?? [];
        $syncData = [];
        foreach ($inventoryData as $item) {
            $syncData[$item['id']] = ['quantity' => $item['quantity']];
        }
        $jobcard->inventory()->sync($syncData);

        // Add other update logic for employees, status, etc. here

        return redirect()->route('jobcard.show', $jobcard->id)->with('success', 'Jobcard updated!');
    }
    public function store(Request $request)
    {
        $jobcard = Jobcard::create([
            'client_id' => $request->client_id,
            'work_done' => $request->work_done,
            'status' => $request->status,
            // ...other fields
        ]);
        $jobcard->employees()->sync($request->employees);

        return redirect()->route('progress.index')->with('success', 'Jobcard created!');
    }
}
