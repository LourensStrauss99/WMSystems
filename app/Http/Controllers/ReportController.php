<?php
// filepath: app/Http/Controllers/ReportController.php

namespace App\Http\Controllers;

use App\Models\Jobcard;
use App\Models\Invoice;
use App\Models\Inventory;
use App\Models\Employee;
use App\Models\CompanyDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        // 1. Hours Booked Report
        $hoursData = $this->getHoursReport();
        
        // 2. Invoice Aging Report
        $invoiceAging = $this->getInvoiceAgingReport();
        
        // 3. Inventory Cost vs Income Report
        $inventoryReport = $this->getInventoryReport();
        
        // 4. Jobcard Status Overview
        $jobcardStatus = $this->getJobcardStatusReport();
        
        // 5. General totals
        $invoicesGrandTotal = Invoice::sum('amount');
        $totalJobcards = Jobcard::count();

        return view('reports', compact(
            'hoursData',
            'invoiceAging', 
            'inventoryReport',
            'jobcardStatus',
            'invoicesGrandTotal',
            'totalJobcards'
        ));
    }

    private function getHoursReport()
    {
        // Get artisan employees
        $artisans = Employee::where('role', 'artisan')->get();
        
        // Calculate hours booked vs available
        $workingDaysThisMonth = Carbon::now()->diffInDaysFiltered(function (Carbon $date) {
            return $date->isWeekday();
        }, Carbon::now()->startOfMonth());
        
        $hoursPerDay = 8;
        $availableHours = $artisans->count() * $workingDaysThisMonth * $hoursPerDay;
        
        // Get total hours booked (from jobcards with employees)
        $bookedHours = Jobcard::whereIn('status', ['in progress', 'completed', 'invoiced'])
            ->with('employees')
            ->get()
            ->sum(function($jobcard) {
                return $jobcard->employees->sum('pivot.hours_worked');
            });
            
        // Get invoiced hours
        $invoicedHours = Jobcard::where('status', 'invoiced')
            ->with('employees')
            ->get()
            ->sum(function($jobcard) {
                return $jobcard->employees->sum('pivot.hours_worked');
            });

        return [
            'total_artisans' => $artisans->count(),
            'available_hours' => $availableHours,
            'booked_hours' => $bookedHours,
            'invoiced_hours' => $invoicedHours,
            'utilization_rate' => $availableHours > 0 ? round(($bookedHours / $availableHours) * 100, 2) : 0,
            'artisans' => $artisans
        ];
    }

    private function getInvoiceAgingReport()
    {
        $now = Carbon::now();
        
        return [
            'paid' => Invoice::where('status', 'paid')->count(),
            'unpaid_current' => Invoice::where('status', 'unpaid')
                ->where('invoice_date', '>=', $now->copy()->subDays(30))
                ->count(),
            'unpaid_30_days' => Invoice::where('status', 'unpaid')
                ->whereBetween('invoice_date', [$now->copy()->subDays(60), $now->copy()->subDays(31)])
                ->count(),
            'unpaid_60_days' => Invoice::where('status', 'unpaid')
                ->whereBetween('invoice_date', [$now->copy()->subDays(90), $now->copy()->subDays(61)])
                ->count(),
            'unpaid_90_days' => Invoice::where('status', 'unpaid')
                ->whereBetween('invoice_date', [$now->copy()->subDays(120), $now->copy()->subDays(91)])
                ->count(),
            'unpaid_120_plus' => Invoice::where('status', 'unpaid')
                ->where('invoice_date', '<', $now->copy()->subDays(120))
                ->count(),
            'total_paid_amount' => Invoice::where('status', 'paid')->sum('amount'),
            'total_unpaid_amount' => Invoice::where('status', 'unpaid')->sum('amount')
        ];
    }

    private function getInventoryReport()
    {
        // Get all inventory used in invoiced jobcards
        $inventoryData = Jobcard::where('status', 'invoiced')
            ->with(['inventory'])
            ->get()
            ->flatMap(function($jobcard) {
                return $jobcard->inventory->map(function($item) {
                    $quantity = $item->pivot->quantity;
                    return [
                        'name' => $item->name,
                        'quantity' => $quantity,
                        'buying_cost' => $quantity * $item->buying_price,
                        'selling_income' => $quantity * $item->selling_price,
                        'profit' => $quantity * ($item->selling_price - $item->buying_price)
                    ];
                });
            })
            ->groupBy('name')
            ->map(function($items, $name) {
                return [
                    'name' => $name,
                    'total_quantity' => $items->sum('quantity'),
                    'total_cost' => $items->sum('buying_cost'),
                    'total_income' => $items->sum('selling_income'),
                    'total_profit' => $items->sum('profit')
                ];
            });

        return [
            'items' => $inventoryData,
            'total_cost' => $inventoryData->sum('total_cost'),
            'total_income' => $inventoryData->sum('total_income'),
            'total_profit' => $inventoryData->sum('total_profit')
        ];
    }

    private function getJobcardStatusReport()
    {
        return [
            'assigned' => Jobcard::where('status', 'assigned')->count(),
            'in_progress' => Jobcard::where('status', 'in progress')->count(),
            'completed' => Jobcard::where('status', 'completed')->count(),
            'invoiced' => Jobcard::where('status', 'invoiced')->count(),
            'total' => Jobcard::count()
        ];
    }
}