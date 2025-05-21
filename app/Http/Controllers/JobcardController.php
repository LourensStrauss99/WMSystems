<?php

namespace App\Http\Controllers;

use App\Models\Jobcard;
use App\Models\Employee;
use App\Models\Inventory;
use Illuminate\Http\Request;

class JobcardController extends Controller
{
    public function show(Jobcard $jobcard)
    {
        $employees = Employee::all();
        $inventory = Inventory::all();
        $jobcard->load('client', 'employees', 'inventory');
        return view('jobcard.show', compact('jobcard', 'employees', 'inventory'));
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
}
