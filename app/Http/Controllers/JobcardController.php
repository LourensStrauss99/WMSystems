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
        $query = Jobcard::with('client');
        
        if ($request->filled('client')) {
            $query->whereHas('client', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->client . '%');
            });
        }

        $jobcards = $query->paginate(10);

        if ($request->ajax()) {
            return response()->json($jobcards);
        }

        return view('jobcard.index', compact('jobcards'));
    }

    public function show(Jobcard $jobcard)
    {
        $employees = \App\Models\Employee::all();  
        $statuses = ['in progress', 'assigned', 'completed'];
        $inventory = \App\Models\Inventory::all();

        return view('jobcard.show', compact('jobcard', 'employees', 'statuses', 'inventory'));
    }

    public function update(Request $request, Jobcard $jobcard)
    {
        $validated = $request->validate([
            'employees' => 'array',
            'employees.*' => 'exists:employees,id',
            'inventory_data' => 'required|string',
            'time_spent' => 'nullable|integer',
            'work_done' => 'nullable|string',
            'status' => 'required|string',
        ]);

        // Save jobcard fields
        $jobcard->status = $validated['status'];
        $jobcard->work_done = $validated['work_done'];
        $jobcard->time_spent = $validated['time_spent'];
        $jobcard->save();

        // Sync employees
        $jobcard->employees()->sync($validated['employees'] ?? []);

        // Handle inventory
        $newInventory = collect(json_decode($validated['inventory_data'], true) ?? []);
        $oldInventory = $jobcard->inventory->keyBy('id');

        $syncData = [];
        foreach ($newInventory as $item) {
            $itemId = $item['id'];
            $newQty = (int)$item['quantity'];
            $oldQty = (int)($oldInventory[$itemId]->pivot->quantity ?? 0);
            $diff = $newQty - $oldQty;

            // Update inventory stock_level
            $inventory = \App\Models\Inventory::find($itemId);
            if ($inventory) {
                $inventory->stock_level = max(0, $inventory->stock_level - $diff);
                $inventory->save();
            }

            $syncData[$itemId] = ['quantity' => $newQty];
        }
        $jobcard->inventory()->sync($syncData);

        return redirect()->route('jobcard.show', $jobcard->id)->with('success', 'Jobcard updated successfully!');
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

    public function progress()
    {
        $assignedJobcards = Jobcard::where('status', 'assigned')->get();
        $inProgressJobcards = Jobcard::where('status', 'in progress')->get();
        $completedJobcards = Jobcard::where('status', 'completed')->get();
        return view('progress', compact('assignedJobcards', 'inProgressJobcards', 'completedJobcards'));
    }

    public function showProgress($id)
    {
        $jobcard = Jobcard::with(['client', 'inventory', 'employees'])->findOrFail($id);
        return view('progress_show', compact('jobcard'));
    }

    public function updateProgress(Request $request, $id)
    {
        $jobcard = Jobcard::findOrFail($id);

        // Update inventory quantities
        if ($request->has('inventory')) {
            $syncData = [];
            foreach ($request->inventory as $itemId => $qty) {
                $syncData[$itemId] = ['quantity' => $qty];
            }
            $jobcard->inventory()->sync($syncData);
        }

        // Update time spent and work done
        $jobcard->time_spent = $request->time_spent;
        $jobcard->work_done = $request->work_done;
        $jobcard->save();

        // Optionally handle progress note, completed, etc.

        return redirect()->route('progress.jobcard.show', $jobcard->id)
            ->with('success', 'Jobcard progress updated!');
    }
}