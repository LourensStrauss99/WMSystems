<?php

namespace App\Services;

use App\Models\Jobcard;
use App\Models\Inventory;
use App\Models\Employee;

class JobcardWorkflowService
{
    public function assignInventory(Jobcard $jobcard, $inventoryId, $quantity)
    {
        // Attach inventory to jobcard (update if already exists)
        $jobcard->inventories()->syncWithoutDetaching([
            $inventoryId => ['quantity' => $quantity]
        ]);

        // Decrement inventory stock
        $inventory = Inventory::find($inventoryId);
        if ($inventory) {
            $inventory->quantity = max(0, $inventory->quantity - $quantity);
            $inventory->stock_level = max(0, $inventory->stock_level - $quantity);
            $inventory->save();
        }
    }

    public function assignEmployee(Jobcard $jobcard, $employeeId, $hours)
    {
        // Attach employee to jobcard with hours (update if already exists)
        $jobcard->employees()->syncWithoutDetaching([
            $employeeId => ['hours' => $hours]
        ]);
    }

    // Add more workflow methods as needed...
}