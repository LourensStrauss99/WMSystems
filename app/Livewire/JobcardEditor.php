<?php   

namespace App\Livewire;  

use Livewire\Component;
use App\Models\Jobcard;
use App\Models\Employee;
use App\Models\Inventory;
use Illuminate\Support\Facades\Log;

class JobcardEditor extends Component
{
    public $jobcard;
    public $employees;
    public $inventory;
    public $assignedEmployees = [];
    public $status;
    public $work_done;
    public $time_spent;
    public $selectedEmployee = '';
    public $selectedInventory = '';
    public $inventoryQuantity = 1;
    public $assignedInventory = []; // Add this property if you want to track added inventory

    public function mount(Jobcard $jobcard)
    {
        Log::info('Mounting JobcardEditor', ['jobcard_id' => $jobcard->id]);

        $this->jobcard = $jobcard;
        $this->employees = Employee::all();
        $this->inventory = Inventory::all();
        $this->assignedEmployees = $jobcard->employees->pluck('id')->toArray();
        $this->status = $jobcard->status;
        $this->work_done = $jobcard->work_done;
        $this->time_spent = $jobcard->time_spent;
        
        // Load existing inventory assignments
        $this->assignedInventory = $jobcard->inventory->map(function($item) {
            return [
                'id' => $item->id,
                'quantity' => $item->pivot->quantity ?? 1,
                'name' => $item->name ?? $item->description
            ];
        })->toArray();
    }

    public function addEmployee()
    {
        $employeeId = $this->selectedEmployee;
        if ($employeeId && !in_array($employeeId, $this->assignedEmployees)) {
            $this->assignedEmployees[] = $employeeId;
            $this->selectedEmployee = '';
        }
    }

    public function removeEmployee($employeeId)
    {
        $this->assignedEmployees = array_values(array_diff($this->assignedEmployees, [$employeeId]));
    }

    public function addInventory()
    {
        if ($this->selectedInventory && $this->inventoryQuantity > 0) {
            $this->assignedInventory[] = [
                'id' => $this->selectedInventory,
                'quantity' => $this->inventoryQuantity,
            ];
            $this->selectedInventory = '';
            $this->inventoryQuantity = 1;
        }
    }

    public function editInventory($index)
    {
        $item = $this->assignedInventory[$index];
        $this->selectedInventory = $item['id'];
        $this->inventoryQuantity = $item['quantity'];
        // Optionally remove the item so it can be re-added after editing
        unset($this->assignedInventory[$index]);
        $this->assignedInventory = array_values($this->assignedInventory);
    }

    public function removeInventory($index)
    {
        unset($this->assignedInventory[$index]);
        $this->assignedInventory = array_values($this->assignedInventory); // Re-index array
    }

    public function save()
    {
        try {
            $this->jobcard->status = $this->status;
            $this->jobcard->work_done = $this->work_done;
            $this->jobcard->time_spent = $this->time_spent;
            $this->jobcard->save();

            // Sync employees
            $this->jobcard->employees()->sync($this->assignedEmployees);

            // Sync inventory from the assigned inventory array
            if (!empty($this->assignedInventory)) {
                $syncData = [];
                foreach ($this->assignedInventory as $item) {
                    $syncData[$item['id']] = ['quantity' => $item['quantity']];
                }
                $this->jobcard->inventory()->sync($syncData);
            }

            session()->flash('success', 'Jobcard updated!');
        } catch (\Throwable $e) {
            Log::error('Jobcard error: ' . $e->getMessage(), [
                'exception' => $e,
                'jobcard' => $this->jobcard ?? null,
            ]);
            throw $e; // Optionally re-throw to show the error in the browser
        }
    }

    public function show(Jobcard $jobcard)
    {
        return view('jobcard.show', compact('jobcard'));
    }

    public function render()
    {
        Log::info('Rendering JobcardEditor', ['jobcard_id' => $this->jobcard->id ?? null]);
        return view('livewire.jobcard-editor', [
            'jobcard' => $this->jobcard,
            'employees' => $this->employees,
            'inventory' => $this->inventory,
            'assignedEmployees' => $this->assignedEmployees,
            'assignedInventory' => $this->assignedInventory,
            'status' => $this->status,
            'work_done' => $this->work_done,
            'time_spent' => $this->time_spent,
        ]);
    }
}
