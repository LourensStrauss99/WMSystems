<?php
// filepath: app/Http/Controllers/ReportController.php

namespace App\Http\Controllers;

use App\Models\Jobcard;
use App\Models\Invoice;

class ReportController extends Controller
{
    public function index()
    {
        // Fetch all invoices with their jobcard (adjust relationship if needed)
        $invoices = Invoice::with('jobcard')->get();

        // Calculate combined grand total
        $invoicesGrandTotal = $invoices->sum('amount');

        // Existing logic for hoursBooked and jobcards
        $hoursBooked = Jobcard::whereIn('status', ['in_progress', 'completed'])->sum('time_spent');
        $jobcards = Jobcard::whereIn('status', ['in_progress', 'completed'])->get();

        return view('reports', compact('invoices', 'invoicesGrandTotal', 'hoursBooked', 'jobcards'));
    }

    public function reports()
    {
        // Fetch all invoices (or filter as needed)
        $invoices = Invoice::with('jobcard')->get();

        // Calculate combined grand total
        $invoicesGrandTotal = $invoices->sum('amount');

        $totalMinutes = Jobcard::whereIn('status', ['in_progress', 'completed'])
            ->sum('time_spent');

        $hoursBooked = round($totalMinutes / 60, 2); // 2 decimal places

        $jobcards = Jobcard::whereIn('status', ['in_progress', 'completed'])->get();

        return view('reports', compact('invoices', 'invoicesGrandTotal', 'hoursBooked', 'jobcards'));
    }
}