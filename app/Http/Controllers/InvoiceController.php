<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\TenantDatabaseSwitch;
use App\Models\Jobcard;
use App\Models\CompanyDetail; // <- CHANGE THIS (not Company)
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMailable;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Invoice; // <- ADD THIS
use Illuminate\Support\Facades\DB; // <- ADD THIS
use App\Mail\InvoiceReminderMailable;

class InvoiceController extends Controller
{
    use TenantDatabaseSwitch;
    public function index(Request $request)
    {
        $this->switchToTenantDatabase();
        
        $query = Jobcard::with('client')->where('status', 'invoiced');

        if ($request->filled('client')) {
            $query->whereHas('client', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->client . '%');
            });
        }

        if ($request->filled('from')) {
            $query->whereDate('updated_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('updated_at', '<=', $request->to);
        }

        $jobcards = $query->paginate(10);

        return view('invoice', compact('jobcards'));
    }

    public function show($jobcardId)
    {
        $this->switchToTenantDatabase();
        
        $jobcard = Jobcard::with(['client', 'inventory'])->findOrFail($jobcardId);
        $company = CompanyDetail::first(); // <- FIXED
        return view('invoice_view', compact('jobcard', 'company'));
    }

    public function email($jobcardId)
    {
        $this->switchToTenantDatabase();
        
        $jobcard = Jobcard::with(['client', 'inventory'])->findOrFail($jobcardId);
        $company = CompanyDetail::first(); // <- FIXED

        // Send email using a Mailable (see step 4)
        Mail::to($jobcard->client->email)->send(new InvoiceMailable($jobcard, $company));

        return back()->with('success', 'Invoice emailed successfully!');
    }

    public function generatePDF($id)
    {
        $this->switchToTenantDatabase();
        
        try {
            $jobcard = Jobcard::with(['client', 'inventory', 'employees'])->findOrFail($id);
            $company = CompanyDetail::first(); // <- FIXED

            // Calculate totals
            $inventoryTotal = $jobcard->inventory->sum(function($item) {
                $quantity = $item->pivot->quantity ?? 0;
                $sellingPrice = $item->selling_price ?? $item->sell_price ?? 0;
                return $quantity * $sellingPrice;
            });

            // --- BEGIN: Enhanced Labour Calculation ---
            $labourRate = $company->labour_rate ?? 750;
            $overtimeRate = $labourRate * ($company->overtime_multiplier ?? 1.5);
            $weekendRate = $labourRate * ($company->weekend_multiplier ?? 2.0);
            $holidayRate = $labourRate * ($company->public_holiday_multiplier ?? 2.5);
            $callOutRate = $company->call_out_rate ?? 1000;
            $mileageRate = $company->mileage_rate ?? 7.5;

            $normalHours = $overtimeHours = $weekendHours = $holidayHours = $callOutHours = 0;
            $totalTravelKm = 0;

            foreach ($jobcard->employees as $employee) {
                $type = $employee->pivot->hour_type ?? 'normal';
                $hours = floatval($employee->pivot->hours_worked ?? 0);
                if ($type === 'normal') $normalHours += $hours;
                elseif ($type === 'overtime') $overtimeHours += $hours;
                elseif ($type === 'weekend') $weekendHours += $hours;
                elseif ($type === 'public_holiday') $holidayHours += $hours;
                elseif ($type === 'call_out') $callOutHours += $hours;
                elseif ($type === 'traveling') $totalTravelKm += floatval($employee->pivot->travel_km ?? 0);
            }

            $normalCost = $normalHours * $labourRate;
            $overtimeCost = $overtimeHours * $overtimeRate;
            $weekendCost = $weekendHours * $weekendRate;
            $holidayCost = $holidayHours * $holidayRate;
            $callOutCost = $callOutHours * $callOutRate;
            $mileageCost = $totalTravelKm * $mileageRate;

            $totalLabourCost = $normalCost + $overtimeCost + $weekendCost + $holidayCost + $callOutCost + $mileageCost;
            // --- END: Enhanced Labour Calculation ---

            // Use $totalLabourCost for invoice totals
            $subtotal = $inventoryTotal + $totalLabourCost;
            $vat = $subtotal * (($company->vat_percent ?? 15) / 100); // <- ADD NULL CHECK
            $grandTotal = $subtotal + $vat;

            $data = [
                'jobcard' => $jobcard,
                'company' => $company,
                'inventoryTotal' => $inventoryTotal,
                'labourHours' => $normalHours + $overtimeHours + $weekendHours + $holidayHours + $callOutHours,
                'labourTotal' => $totalLabourCost,
                'subtotal' => $subtotal,
                'vat' => $vat,
                'grandTotal' => $grandTotal
            ];

            // Generate PDF
            $pdf = PDF::loadView('invoice_pdf', $data);
            $pdf->setPaper('A4', 'portrait');
            
            // Set options for better rendering
            $pdf->setOptions([
                'dpi' => 150,
                'defaultFont' => 'sans-serif',
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true
            ]);

            $filename = 'Invoice-' . $jobcard->jobcard_number . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->switchToTenantDatabase();
        
        $validated = $request->validate([
            'jobcard_id' => 'required|exists:jobcards,id',
            'due_date' => 'nullable|date',
        ]);

        try {
            DB::transaction(function() use ($validated) {
                $jobcard = Jobcard::with(['client', 'inventory', 'employees'])->findOrFail($validated['jobcard_id']);
                $company = CompanyDetail::first();

                // --- BEGIN: Enhanced Labour Calculation ---
                $labourRate = $company->labour_rate ?? 750;
                $overtimeRate = $labourRate * ($company->overtime_multiplier ?? 1.5);
                $weekendRate = $labourRate * ($company->weekend_multiplier ?? 2.0);
                $holidayRate = $labourRate * ($company->public_holiday_multiplier ?? 2.5);
                $callOutRate = $company->call_out_rate ?? 1000;
                $mileageRate = $company->mileage_rate ?? 7.5;

                $normalHours = $overtimeHours = $weekendHours = $holidayHours = $callOutHours = 0;
                $totalTravelKm = 0;

                foreach ($jobcard->employees as $employee) {
                    $type = $employee->pivot->hour_type ?? 'normal';
                    $hours = floatval($employee->pivot->hours_worked ?? 0);
                    if ($type === 'normal') $normalHours += $hours;
                    elseif ($type === 'overtime') $overtimeHours += $hours;
                    elseif ($type === 'weekend') $weekendHours += $hours;
                    elseif ($type === 'public_holiday') $holidayHours += $hours;
                    elseif ($type === 'call_out') $callOutHours += $hours;
                    elseif ($type === 'traveling') $totalTravelKm += floatval($employee->pivot->travel_km ?? 0);
                }

                $normalCost = $normalHours * $labourRate;
                $overtimeCost = $overtimeHours * $overtimeRate;
                $weekendCost = $weekendHours * $weekendRate;
                $holidayCost = $holidayHours * $holidayRate;
                $callOutCost = $callOutHours * $callOutRate;
                $mileageCost = $totalTravelKm * $mileageRate;

                $totalLabourCost = $normalCost + $overtimeCost + $weekendCost + $holidayCost + $callOutCost + $mileageCost;
                // --- END: Enhanced Labour Calculation ---

                // ✅ CREATE ACTUAL INVOICE RECORD
                $invoice = Invoice::create([
                    'invoice_number' => Invoice::generateInvoiceNumber(),
                    'client_id' => $jobcard->client_id,
                    'jobcard_id' => $jobcard->id,
                    'amount' => $totalLabourCost, // Use totalLabourCost for invoice record
                    'invoice_date' => now(),
                    'due_date' => $validated['due_date'] ?? now()->addDays(30),
                    'status' => 'unpaid',
                    'paid_amount' => 0,
                    'outstanding_amount' => $totalLabourCost,
                ]);

                // Update jobcard status
                $jobcard->update(['status' => 'invoiced']);

                return redirect()->route('invoices.show', $jobcard->id)
                    ->with('success', "Invoice {$invoice->invoice_number} created successfully!");
            });

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating invoice: ' . $e->getMessage());
        }
    }

    public function sendReminder($invoiceId)
    {
        $this->switchToTenantDatabase();
        
        $invoice = \App\Models\Invoice::with(['client', 'jobcard'])->findOrFail($invoiceId);
        $company = \App\Models\CompanyDetail::first();
        // Send reminder email
        Mail::to($invoice->client->email)
            ->send(new InvoiceReminderMailable($invoice, $company));
        return response()->json(['success' => true]);
    }
}