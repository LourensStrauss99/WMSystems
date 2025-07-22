<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Jobcard;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Inventory;

class MobileJobcardController extends Controller
{
    public function index()
    {
        $jobcards = Jobcard::all();
        return view('mobile.jobcard-view', compact('jobcards'));
    }

    public function edit($id)
    {
        $jobcard = Jobcard::with(['employees', 'inventory'])->findOrFail($id);
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
        return view('mobile.jobcard-edit', compact('jobcard', 'clients', 'employees', 'inventory', 'assignedInventory'));
    }

    public function update(Request $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $jobcard = \App\Models\Jobcard::findOrFail($id);
            // Update jobcard basic fields
            $jobcard->update($request->only([
                'jobcard_number', 'job_date', 'client_id', 'category', 'work_request', 'special_request',
                'status', 'work_done', 'time_spent', 'progress_note',
                'normal_hours', 'overtime_hours', 'weekend_hours', 'public_holiday_hours',
                'call_out_fee', 'mileage_km', 'mileage_cost', 'total_labour_cost'
            ]));

            // Sync employees (with hours and hour_type)
            if ($request->has('employees')) {
                $syncData = [];
                foreach ($request->employees as $employeeId) {
                    $hours = $request->employee_hours[$employeeId] ?? 0;
                    $hourType = $request->employee_hour_types[$employeeId] ?? 'normal';
                    $syncData[$employeeId] = [
                        'hours_worked' => $hours,
                        'hour_type' => $hourType
                    ];
                }
                $jobcard->employees()->sync($syncData);
            }

            // Sync inventory (with quantities)
            if ($request->has('inventory_items')) {
                $syncData = [];
                foreach ($request->inventory_items as $idx => $itemId) {
                    $qty = $request->inventory_qty[$idx] ?? 1;
                    $syncData[$itemId] = ['quantity' => $qty];
                }
                $jobcard->inventory()->sync($syncData);
            }
        });
        return redirect()->route('mobile.jobcards.edit', $id)->with('success', 'Jobcard updated successfully!');
    }

    // Add other methods as needed from JobcardController, adjusting view paths to mobile/
}
