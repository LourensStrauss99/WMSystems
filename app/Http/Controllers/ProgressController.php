<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jobcard;

class ProgressController extends Controller
{
    public function index(Request $request)
    {
        $assignedJobcards = Jobcard::where('status', 'assigned')->with('client')->orderByDesc('job_date')->paginate(8, ['*'], 'assigned_page');
        $inProgressJobcards = Jobcard::where('status', 'in progress')->with('client')->orderByDesc('job_date')->paginate(8, ['*'], 'inprogress_page');
        $completedJobcards = Jobcard::where('status', 'completed')->with('client')->orderByDesc('job_date')->paginate(8, ['*'], 'completed_page');

        return view('progress', compact('assignedJobcards', 'inProgressJobcards', 'completedJobcards'));
    }

    public function show($id)
    {
        $jobcard = Jobcard::with(['client', 'employees', 'inventory'])->findOrFail($id);
        
        // Ensure inventory is properly loaded with pivot data
        $jobcard->load(['inventory' => function($query) {
            $query->withPivot('quantity');
        }]);
        
        return view('progress_show', compact('jobcard'));
    }
    public function ajaxShow($id)
    {
        try {
            $jobcard = Jobcard::with(['client', 'employees', 'inventory'])->findOrFail($id);
            return response()->json($jobcard);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function updateProgress(Request $request, $id)
    {
        $jobcard = Jobcard::with(['employees', 'inventory'])->findOrFail($id);

        if ($request->action === 'invoice') {
            // Prevent duplicate invoices
            if (!$jobcard->invoice_number) {
                // Calculate inventory total using selling_price
                $inventoryTotal = 0;
                foreach ($jobcard->inventory as $item) {
                    $quantity = $item->pivot->quantity ?? 0;
                    $sellingPrice = $item->selling_price ?? $item->sell_price ?? 0;
                    $inventoryTotal += ($quantity * $sellingPrice);
                }

                // Calculate labour total using enhanced jobcard data
                $company = \App\Models\CompanyDetail::first();
                $totalLabourCost = floatval($jobcard->total_labour_cost ?? 0);
                
                // If no enhanced data, fall back to old calculation
                if ($totalLabourCost == 0) {
                    $labourHours = $jobcard->employees->pluck('pivot.hours_worked')->sum();
                    $totalLabourCost = $labourHours * ($company->labour_rate ?? 0);
                }

                // Calculate subtotal, VAT, and grand total
                $subtotal = $inventoryTotal + $totalLabourCost;
                $vat = $subtotal * (($company->vat_percent ?? 15) / 100);
                $grandTotal = $subtotal + $vat;

                \App\Models\Invoice::create([
                    'jobcard_id'     => $jobcard->id,
                    'client_id'      => $jobcard->client_id,
                    'amount'         => $grandTotal,
                    'status'         => 'unpaid',
                    'invoice_number' => $jobcard->jobcard_number,
                    'invoice_date'   => now()->toDateString(),
                ]);
                $jobcard->status = 'invoiced';
                $jobcard->invoice_number = $jobcard->jobcard_number;
                $jobcard->save();
            }
            return redirect()->route('progress')->with('success', 'Invoice created and jobcard removed from progress!');
        }

        // Update employee hours
        if ($request->has('employee_hours')) {
            $syncData = [];
            foreach ($request->employee_hours as $employeeId => $hours) {
                $syncData[$employeeId] = ['hours_worked' => $hours];
            }
            $jobcard->employees()->sync($syncData); 
        }

        // Save fields
        $jobcard->progress_note = $request->input('progress_note');
        $jobcard->work_done = $request->input('work_done');
        $jobcard->time_spent = $request->input('time_spent');

        // Save inventory quantities if needed
        if ($request->has('inventory')) {
            foreach ($request->input('inventory') as $itemId => $qty) {
                $jobcard->inventory()->updateExistingPivot($itemId, ['quantity' => $qty]);
            }
        }

        // Check which button was pressed
        if ($request->input('action') === 'completed') {
            $jobcard->status = 'completed';
        }

        $jobcard->save();

        return redirect()->route('progress.jobcard.show', $jobcard->id)
            ->with('success', 'Jobcard progress updated!');
    }
}
