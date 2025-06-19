<?php

namespace App\Http\Controllers;

use App\Models\Jobcard;
use App\Models\Employee;
use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JobcardController extends Controller
{
    public function index(Request $request)
    {
        $jobcards = Jobcard::with(['client', 'employees', 'inventory'])
            ->orderByDesc('job_date')
            ->paginate(10);

        return view('jobcard.index', compact('jobcards'));
    }

    public function show(Jobcard $jobcard)
    {
        $jobcard->load(['client', 'employees', 'inventory']);
        $employees = Employee::all();
        $inventory = Inventory::all();
        $clients = Client::all();

        return view('livewire.jobcard-editor', compact('jobcard', 'employees', 'inventory', 'clients'));
    }

    public function create()
    {
        $employees = Employee::all();
        $inventory = Inventory::all();
        return view('jobcard.create', compact('employees', 'inventory'));
    }

    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {
            $jobcard = Jobcard::create($request->only([
                'jobcard_number', 'job_date', 'client_id', 'category', 'work_request', 'special_request', 'status', 'work_done', 'time_spent'
            ]));

            // Sync employees (with optional hours)
            if ($request->has('employee_hours')) {
                $syncData = [];
                foreach ($request->employee_hours as $employeeId => $hours) {
                    $syncData[$employeeId] = ['hours_worked' => $hours];
                }
                $jobcard->employees()->sync($syncData);
            } elseif ($request->has('employees')) {
                $jobcard->employees()->sync($request->employees);
            }

            // Sync inventory (with quantities) and update stock
            if ($request->has('inventory_qty')) {
                $syncData = [];
                foreach ($request->inventory_qty as $itemId => $qty) {
                    $syncData[$itemId] = ['quantity' => $qty];
                    $inventory = Inventory::find($itemId);
                    if ($inventory) {
                        $inventory->stock_level = max(0, $inventory->stock_level - $qty);
                        $inventory->save();
                    }
                }
                $jobcard->inventory()->sync($syncData);
            }
        });

        return redirect()->route('jobcard.index')->with('success', 'Jobcard created!');
    }

    public function edit(Jobcard $jobcard)
    {
        $jobcard->load(['client', 'employees', 'inventory']);
        $employees = Employee::all();
        $inventory = Inventory::all();
        $clients = Client::all(); // <-- Use imported Client class

        return view('livewire.jobcard-editor', compact('jobcard', 'employees', 'inventory', 'clients'));
    }

    public function update(Request $request, Jobcard $jobcard)
    {
        DB::transaction(function () use ($request, $jobcard) {
            $jobcard->update($request->only([
                'jobcard_number', 'job_date', 'client_id', 'category', 'work_request', 'special_request', 'status', 'work_done', 'time_spent'
            ]));

            // Sync employees (with optional hours)
            if ($request->has('employee_hours')) {
                $syncData = [];
                foreach ($request->employee_hours as $employeeId => $hours) {
                    $syncData[$employeeId] = ['hours_worked' => $hours];
                }
                $jobcard->employees()->sync($syncData);
            } elseif ($request->has('employees')) {
                $jobcard->employees()->sync($request->employees);
            }

            // Sync inventory (with quantities) and update stock
            if ($request->has('inventory_qty')) {
                $syncData = [];
                foreach ($request->inventory_qty as $itemId => $qty) {
                    $syncData[$itemId] = ['quantity' => $qty];
                    $inventory = Inventory::find($itemId);
                    if ($inventory) {
                        $inventory->stock_level = max(0, $inventory->stock_level - $qty);
                        $inventory->save();
                    }
                }
                $jobcard->inventory()->sync($syncData);
            }
        });

        return redirect()->route('jobcard.show', $jobcard->id)->with('success', 'Jobcard updated!');
    }

    public function submitForInvoice(Jobcard $jobcard)
    {
        // Example: mark as invoiced and create invoice
        $jobcard->status = 'invoiced';
        $jobcard->save();

        $invoice = Invoice::create([
            'jobcard_id' => $jobcard->id,
            'client_id' => $jobcard->client_id,
            // ...other invoice fields...
        ]);

        return redirect()->route('invoice.show', $invoice->id)->with('success', 'Invoice created!');
    }

    public function updateProgress(Request $request, $id)
    {
        $jobcard = Jobcard::findOrFail($id);

        if ($request->has('action')) {
            if ($request->action === 'completed') {
                $jobcard->status = 'completed';
                $jobcard->save();
            }

            if ($request->action === 'invoice') {
                // Prevent duplicate invoices
                if (!$jobcard->invoice_number) {
                    Invoice::create([
                        'jobcard_id'     => $jobcard->id,
                        'client_id'      => $jobcard->client_id,
                        'amount'         => $jobcard->amount ?? 0,
                        'status'         => 'unpaid',
                        'invoice_number' => $jobcard->jobcard_number,
                        'invoice_date'   => now()->toDateString(),
                    ]);
                    $jobcard->status = 'invoiced';
                    $jobcard->invoice_number = $jobcard->jobcard_number;
                    $jobcard->save();
                }
            }
        }

        return redirect()->route('progress')->with('success', 'Jobcard updated!');
    }
}
