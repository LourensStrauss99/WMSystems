<?php
// filepath: app/Http/Controllers/ReportController.php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Jobcard;
use App\Models\Invoice;
use App\Models\Inventory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $selectedMonth = $request->get('month', Carbon::now()->format('Y-m'));
        $selectedYear = $request->get('year', Carbon::now()->year);
        $viewMode = $request->get('view', 'monthly'); // monthly or ytd
        
        $company = \App\Models\CompanyDetail::first();
        
        return view('reports', [
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'viewMode' => $viewMode,
            'company' => $company,
            'monthlyData' => $this->getMonthlyReport($selectedMonth),
            'ytdData' => $this->getYTDReport($selectedYear),
            'monthlyHours' => $this->getMonthlyHoursReport($selectedMonth),
            'ytdHours' => $this->getYTDHoursReport($selectedYear),
            'monthlyRevenue' => $this->getMonthlyRevenueBreakdown($selectedMonth),
            'ytdRevenue' => $this->getYTDRevenueBreakdown($selectedYear),
            'availableMonths' => $this->getAvailableMonths(),
            'employeeStats' => $this->getEmployeeStats($selectedMonth, $viewMode),
        ]);
    }

    private function getMonthlyHoursReport($month)
    {
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
        
        // Calculate working days (excluding weekends)
        $workingDays = 0;
        $current = $startDate->copy();
        while ($current <= $endDate) {
            if ($current->isWeekday()) {
                $workingDays++;
            }
            $current->addDay();
        }
        
        // FIX: Get ALL active employees for this month (not just artisans)
        $employees = Employee::where('is_active', true)
                        ->where('created_at', '<=', $endDate)
                        ->count();
        
        // Calculate available hours (8 hours per day per employee)
        $availableHours = $workingDays * 8 * $employees;
        
        // FIX: Get booked hours from ALL employees (remove artisan filter)
        $bookedHours = DB::table('employee_jobcard')
                    ->join('jobcards', 'employee_jobcard.jobcard_id', '=', 'jobcards.id')
                    ->join('employees', 'employee_jobcard.employee_id', '=', 'employees.id')
                    ->join('invoices', 'jobcards.id', '=', 'invoices.jobcard_id') // Only invoiced
                    ->where('employees.is_active', true) // Only active employees
                    ->whereDate('invoices.invoice_date', '>=', $startDate)
                    ->whereDate('invoices.invoice_date', '<=', $endDate)
                    ->sum('employee_jobcard.hours_worked');
    
        // Calculate overtime, weekend, and holiday hours (from invoiced jobs only)
        $overtimeHours = $this->getOvertimeHours($startDate, $endDate, true);
        $weekendHours = $this->getWeekendHours($startDate, $endDate, true);
        $holidayHours = $this->getHolidayHours($startDate, $endDate, true);
        
        $utilizationRate = $availableHours > 0 ? round(($bookedHours / $availableHours) * 100, 2) : 0;
        
        $traveling_km = DB::table('employee_jobcard')
            ->join('jobcards', 'employee_jobcard.jobcard_id', '=', 'jobcards.id')
            ->join('employees', 'employee_jobcard.employee_id', '=', 'employees.id')
            ->join('invoices', 'jobcards.id', '=', 'invoices.jobcard_id')
            ->where('employees.is_active', true)
            ->whereDate('invoices.invoice_date', '>=', $startDate)
            ->whereDate('invoices.invoice_date', '<=', $endDate)
            ->where('employee_jobcard.hour_type', 'traveling')
            ->sum('employee_jobcard.travel_km');

        $callOutHours = DB::table('employee_jobcard')
            ->join('jobcards', 'employee_jobcard.jobcard_id', '=', 'jobcards.id')
            ->join('employees', 'employee_jobcard.employee_id', '=', 'employees.id')
            ->join('invoices', 'jobcards.id', '=', 'invoices.jobcard_id')
            ->where('employees.is_active', true)
            ->whereDate('invoices.invoice_date', '>=', $startDate)
            ->whereDate('invoices.invoice_date', '<=', $endDate)
            ->where('employee_jobcard.hour_type', 'call_out')
            ->sum('employee_jobcard.hours_worked');

        return [
            'working_days' => $workingDays,
            'artisan_count' => $employees, // Rename this to employee_count
            'available_hours' => $availableHours,
            'booked_hours' => $bookedHours,
            'normal_hours' => $bookedHours - $overtimeHours - $weekendHours - $holidayHours - $callOutHours,
            'overtime_hours' => $overtimeHours,
            'weekend_hours' => $weekendHours,
            'holiday_hours' => $holidayHours,
            'utilization_rate' => $utilizationRate,
            'hours_per_employee' => $employees > 0 ? round($bookedHours / $employees, 2) : 0,
            'traveling_km' => $traveling_km,
            'call_out_hours' => $callOutHours,
        ];
    }

    private function getYTDHoursReport($year)
    {
        $startDate = Carbon::createFromFormat('Y', $year)->startOfYear();
        $endDate = Carbon::createFromFormat('Y', $year)->endOfYear();
        
        // Calculate working days for the year
        $workingDays = 0;
        $current = $startDate->copy();
        while ($current <= $endDate) {
            if ($current->isWeekday()) {
                $workingDays++;
            }
            $current->addDay();
        }
        
        // FIX: Get ALL active employees for the year (not just artisans)
        $employees = Employee::where('is_active', true)
                        ->where('created_at', '<=', $endDate)
                        ->count();
    
        $availableHours = $workingDays * 8 * $employees;
        
        // FIX: Get booked hours from ALL employees (remove artisan filter)
        $bookedHours = DB::table('employee_jobcard')
                    ->join('jobcards', 'employee_jobcard.jobcard_id', '=', 'jobcards.id')
                    ->join('employees', 'employee_jobcard.employee_id', '=', 'employees.id')
                    ->join('invoices', 'jobcards.id', '=', 'invoices.jobcard_id') // Only invoiced
                    ->where('employees.is_active', true) // Only active employees
                    ->whereDate('invoices.invoice_date', '>=', $startDate)
                    ->whereDate('invoices.invoice_date', '<=', $endDate)
                    ->sum('employee_jobcard.hours_worked');
    
        $overtimeHours = $this->getOvertimeHours($startDate, $endDate, true);
        $weekendHours = $this->getWeekendHours($startDate, $endDate, true);
        $holidayHours = $this->getHolidayHours($startDate, $endDate, true);
        
        $utilizationRate = $availableHours > 0 ? round(($bookedHours / $availableHours) * 100, 2) : 0;
        
        $traveling_km = DB::table('employee_jobcard')
            ->join('jobcards', 'employee_jobcard.jobcard_id', '=', 'jobcards.id')
            ->join('employees', 'employee_jobcard.employee_id', '=', 'employees.id')
            ->join('invoices', 'jobcards.id', '=', 'invoices.jobcard_id')
            ->where('employees.is_active', true)
            ->whereDate('invoices.invoice_date', '>=', $startDate)
            ->whereDate('invoices.invoice_date', '<=', $endDate)
            ->where('employee_jobcard.hour_type', 'traveling')
            ->sum('employee_jobcard.travel_km');

        $callOutHours = DB::table('employee_jobcard')
            ->join('jobcards', 'employee_jobcard.jobcard_id', '=', 'jobcards.id')
            ->join('employees', 'employee_jobcard.employee_id', '=', 'employees.id')
            ->join('invoices', 'jobcards.id', '=', 'invoices.jobcard_id')
            ->where('employees.is_active', true)
            ->whereDate('invoices.invoice_date', '>=', $startDate)
            ->whereDate('invoices.invoice_date', '<=', $endDate)
            ->where('employee_jobcard.hour_type', 'call_out')
            ->sum('employee_jobcard.hours_worked');

        return [
            'working_days' => $workingDays,
            'artisan_count' => $employees, // Rename this to employee_count
            'available_hours' => $availableHours,
            'booked_hours' => $bookedHours,
            'normal_hours' => $bookedHours - $overtimeHours - $weekendHours - $holidayHours - $callOutHours,
            'overtime_hours' => $overtimeHours,
            'weekend_hours' => $weekendHours,
            'holiday_hours' => $holidayHours,
            'utilization_rate' => $utilizationRate,
            'hours_per_employee' => $employees > 0 ? round($bookedHours / $employees, 2) : 0,
            'traveling_km' => $traveling_km,
            'call_out_hours' => $callOutHours,
        ];
    }

    private function getMonthlyRevenueBreakdown($month)
    {
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
        $company = Company::getSettings();
        
        $invoices = Invoice::whereDate('invoice_date', '>=', $startDate)
                          ->whereDate('invoice_date', '<=', $endDate)
                          ->with(['jobcard.inventory', 'jobcard.employees'])
                          ->get();
        
        $totalRevenue = $invoices->sum('amount');
        $hoursRevenue = 0;
        $inventoryRevenue = 0;
        $inventoryCost = 0;
        $breakdown = [
            'normal' => 0,
            'overtime' => 0,
            'weekend' => 0,
            'public_holiday' => 0,
            'call_out' => 0,
            'traveling' => 0,
        ];
        $hours_detail = [
            'normal' => 0,
            'overtime' => 0,
            'weekend' => 0,
            'public_holiday' => 0,
            'call_out' => 0,
            'traveling' => 0,
        ];
        foreach ($invoices as $invoice) {
            $jobcard = $invoice->jobcard;
            if (!$jobcard) continue;
            // Inventory
            $invTotal = $jobcard->inventory->sum(function($item) {
                // Use selling_price from pivot if available, else from inventory
                $selling = $item->pivot->selling_price ?? $item->selling_price ?? $item->sell_price ?? 0;
                return $item->pivot->quantity * $selling;
            });
            $invCost = $jobcard->inventory->sum(function($item) {
                // Use buying_price from pivot if available, else from inventory
                $cost = $item->pivot->buying_price ?? $item->buying_price ?? 0;
                return $item->pivot->quantity * $cost;
            });
            $inventoryRevenue += $invTotal;
            $inventoryCost += $invCost;
            // Labour by hour type
            $types = ['normal','overtime','weekend','public_holiday','call_out','traveling'];
            foreach ($types as $type) {
                if ($type === 'traveling') {
                    $km = $jobcard->employees->filter(fn($e) => ($e->pivot->hour_type ?? '') === 'traveling')->sum('pivot.travel_km');
                    $breakdown['traveling'] += $km * ($company->mileage_rate ?? 0);
                    $hours_detail['traveling'] += $km;
                } elseif ($type === 'call_out') {
                    $hours = $jobcard->employees->filter(fn($e) => ($e->pivot->hour_type ?? '') === 'call_out')->sum('pivot.hours_worked');
                    $breakdown['call_out'] += $hours * ($company->call_out_rate ?? 0);
                    $hours_detail['call_out'] += $hours;
                } else {
                    $hours = $jobcard->employees->filter(fn($e) => ($e->pivot->hour_type ?? ($type === 'normal' ? 'normal' : '')) === $type)->sum('pivot.hours_worked');
                    $rate = match($type) {
                        'normal' => $company->standard_labour_rate ?? 0,
                        'overtime' => ($company->standard_labour_rate ?? 0) * ($company->overtime_multiplier ?? 1),
                        'weekend' => ($company->standard_labour_rate ?? 0) * ($company->weekend_multiplier ?? 1),
                        'public_holiday' => ($company->standard_labour_rate ?? 0) * ($company->public_holiday_multiplier ?? 1),
                        default => 0,
                    };
                    $breakdown[$type] += $hours * $rate;
                    $hours_detail[$type] += $hours;
                }
            }
        }
        $hoursRevenue = array_sum($breakdown);
        $inventoryProfit = $inventoryRevenue - $inventoryCost;
        $inventoryMargin = $inventoryRevenue > 0 ? round(($inventoryProfit / $inventoryRevenue) * 100, 2) : 0;
        $subtotal = $hoursRevenue + $inventoryRevenue;
        $vatAmount = $subtotal * (($company->vat_percentage ?? $company->vat_percent ?? 0) / 100);
        $netProfit = $hoursRevenue + $inventoryProfit;
        $total_invoiced = $totalRevenue;
        return [
            'hours_revenue' => $hoursRevenue,
            'inventory_revenue' => $inventoryRevenue,
            'inventory_cost' => $inventoryCost,
            'inventory_profit' => $inventoryProfit,
            'inventory_margin' => $inventoryMargin,
            'subtotal' => $subtotal,
            'vat_amount' => $vatAmount,
            'total_inc_vat' => $totalRevenue,
            'net_profit' => $netProfit,
            'profit_margin' => $subtotal > 0 ? round(($netProfit / $subtotal) * 100, 2) : 0,
            'hours_breakdown' => $breakdown,
            'hours_detail' => $hours_detail,
            'total_invoiced' => $total_invoiced,
        ];
    }

    private function getInventoryRevenue($startDate, $endDate)
    {
        $inventoryUsed = DB::table('inventory_jobcard')
                      ->join('jobcards', 'inventory_jobcard.jobcard_id', '=', 'jobcards.id')
                      ->join('inventory', 'inventory_jobcard.inventory_id', '=', 'inventory.id')
                      ->whereDate('jobcards.created_at', '>=', $startDate)
                      ->whereDate('jobcards.created_at', '<=', $endDate)
                      ->select(
                          'inventory.selling_price',    // Your table has this column
                          'inventory.buying_price',     // Use buying_price instead of cost_price
                          'inventory_jobcard.quantity',
                          'inventory.name',             // For debugging
                          'inventory.short_code'        // For reference
                      )
                      ->get();
    
    $totalRevenue = 0;
    $totalCost = 0;
    
    foreach ($inventoryUsed as $item) {
        $revenue = $item->selling_price * $item->quantity;
        $cost = $item->buying_price * $item->quantity;
        
        $totalRevenue += $revenue;
        $totalCost += $cost;
    }
    
    $profit = $totalRevenue - $totalCost;
    $margin = $totalRevenue > 0 ? round(($profit / $totalRevenue) * 100, 2) : 0;
    
    return [
        'revenue' => $totalRevenue,
        'cost' => $totalCost,
        'profit' => $profit,
        'margin' => $margin,
        'items_count' => $inventoryUsed->count(),
    ];
    }

    private function getYTDRevenueBreakdown($year)
    {
        $startDate = Carbon::createFromFormat('Y', $year)->startOfYear();
        $endDate = Carbon::createFromFormat('Y', $year)->endOfYear();
        $company = Company::getSettings();
        $invoices = Invoice::whereDate('invoice_date', '>=', $startDate)
                          ->whereDate('invoice_date', '<=', $endDate)
                          ->with(['jobcard.inventory', 'jobcard.employees'])
                          ->get();
        $totalRevenue = $invoices->sum('amount');
        $hoursRevenue = 0;
        $inventoryRevenue = 0;
        $inventoryCost = 0;
        $breakdown = [
            'normal' => 0,
            'overtime' => 0,
            'weekend' => 0,
            'public_holiday' => 0,
            'call_out' => 0,
            'traveling' => 0,
        ];
        $hours_detail = [
            'normal' => 0,
            'overtime' => 0,
            'weekend' => 0,
            'public_holiday' => 0,
            'call_out' => 0,
            'traveling' => 0,
        ];
        foreach ($invoices as $invoice) {
            $jobcard = $invoice->jobcard;
            if (!$jobcard) continue;
            $invTotal = $jobcard->inventory->sum(function($item) {
                return $item->pivot->quantity * $item->selling_price;
            });
            $invCost = $jobcard->inventory->sum(function($item) {
                return $item->pivot->quantity * $item->buying_price;
            });
            $inventoryRevenue += $invTotal;
            $inventoryCost += $invCost;
            $types = ['normal','overtime','weekend','public_holiday','call_out','traveling'];
            foreach ($types as $type) {
                if ($type === 'traveling') {
                    $km = $jobcard->employees->filter(fn($e) => ($e->pivot->hour_type ?? '') === 'traveling')->sum('pivot.travel_km');
                    $breakdown['traveling'] += $km * ($company->mileage_rate ?? 0);
                    $hours_detail['traveling'] += $km;
                } elseif ($type === 'call_out') {
                    $hours = $jobcard->employees->filter(fn($e) => ($e->pivot->hour_type ?? '') === 'call_out')->sum('pivot.hours_worked');
                    $breakdown['call_out'] += $hours * ($company->call_out_rate ?? 0);
                    $hours_detail['call_out'] += $hours;
                } else {
                    $hours = $jobcard->employees->filter(fn($e) => ($e->pivot->hour_type ?? ($type === 'normal' ? 'normal' : '')) === $type)->sum('pivot.hours_worked');
                    $rate = match($type) {
                        'normal' => $company->standard_labour_rate ?? 0,
                        'overtime' => ($company->standard_labour_rate ?? 0) * ($company->overtime_multiplier ?? 1),
                        'weekend' => ($company->standard_labour_rate ?? 0) * ($company->weekend_multiplier ?? 1),
                        'public_holiday' => ($company->standard_labour_rate ?? 0) * ($company->public_holiday_multiplier ?? 1),
                        default => 0,
                    };
                    $breakdown[$type] += $hours * $rate;
                    $hours_detail[$type] += $hours;
                }
            }
        }
        $hoursRevenue = array_sum($breakdown);
        $inventoryProfit = $inventoryRevenue - $inventoryCost;
        $inventoryMargin = $inventoryRevenue > 0 ? round(($inventoryProfit / $inventoryRevenue) * 100, 2) : 0;
        $subtotal = $hoursRevenue + $inventoryRevenue;
        $vatAmount = $subtotal * (($company->vat_percentage ?? $company->vat_percent ?? 0) / 100);
        $netProfit = $hoursRevenue + $inventoryProfit;
        $total_invoiced = $totalRevenue;
        return [
            'hours_revenue' => $hoursRevenue,
            'inventory_revenue' => $inventoryRevenue,
            'inventory_cost' => $inventoryCost,
            'inventory_profit' => $inventoryProfit,
            'inventory_margin' => $inventoryMargin,
            'subtotal' => $subtotal,
            'vat_amount' => $vatAmount,
            'total_inc_vat' => $totalRevenue,
            'net_profit' => $netProfit,
            'profit_margin' => $subtotal > 0 ? round(($netProfit / $subtotal) * 100, 2) : 0,
            'hours_breakdown' => $breakdown,
            'hours_detail' => $hours_detail,
            'total_invoiced' => $total_invoiced,
        ];
    }

    private function getAvailableMonths()
    {
        $months = [];
        $start = Carbon::now()->subYear();
        $end = Carbon::now();
        
        while ($start <= $end) {
            $months[] = [
                'value' => $start->format('Y-m'),
                'label' => $start->format('F Y')
            ];
            $start->addMonth();
        }
        
        return array_reverse($months);
    }



    // Helper methods for hour type determination
    private function isHoliday($date)
    {
        // Add your holiday logic here
        // You could create a holidays table or use a package
        return false;
    }
    
    private function isWeekend($date)
    {
        return Carbon::parse($date)->isWeekend();
    }
    
    private function isOvertime($jobcard)
    {
        // Logic to determine if jobcard is overtime
        // Could be based on total hours per day > 8 or specific flag
        return false;
    }
    
    private function getOvertimeHours($startDate, $endDate, $invoicedOnly = false)
    {
        // For now, return 0 - you can implement overtime logic later
        return 0;
    }
    
    private function getWeekendHours($startDate, $endDate, $invoicedOnly = false)
    {
        if (!$invoicedOnly) {
            return 0; // Implement your weekend logic here
        }
        
        // FIX: Get weekend hours from ALL employees (not just artisans)
        $weekendHours = DB::table('employee_jobcard')
                 ->join('jobcards', 'employee_jobcard.jobcard_id', '=', 'jobcards.id')
                 ->join('employees', 'employee_jobcard.employee_id', '=', 'employees.id')
                 ->join('invoices', 'jobcards.id', '=', 'invoices.jobcard_id')
                 ->where('employees.is_active', true) // Remove role filter
                 ->whereDate('invoices.invoice_date', '>=', $startDate)
                 ->whereDate('invoices.invoice_date', '<=', $endDate)
                 ->where(function($query) {
                     // Add logic to identify weekend work
                     // This is a placeholder - adjust based on your business logic
                     $query->whereRaw('DAYOFWEEK(jobcards.created_at) IN (1, 7)'); // Sunday = 1, Saturday = 7
                 })
                 ->sum('employee_jobcard.hours_worked');

        return $weekendHours ?? 0;
    }
    
    private function getHolidayHours($startDate, $endDate, $invoicedOnly = false)
    {
        // For now, return 0 - you can implement holiday logic later
        // You would need a holidays table or holiday detection logic
        return 0;
    }

    // Legacy methods for backward compatibility
    private function getMonthlyReport($month)
    {
        return $this->getMonthlyHoursReport($month);
    }
    
    private function getYTDReport($year)
    {
        return $this->getYTDHoursReport($year);
    }
    
    private function getEmployeeStats($selectedMonth, $viewMode)
    {
        // Parse the date parameters correctly
        if ($viewMode === 'monthly') {
            $startDate = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
            $endDate = Carbon::createFromFormat('Y-m', $selectedMonth)->endOfMonth();
        } else {
            // For YTD, extract year from month parameter
            $year = Carbon::createFromFormat('Y-m', $selectedMonth)->year;
            $startDate = Carbon::createFromFormat('Y', $year)->startOfYear();
            $endDate = Carbon::createFromFormat('Y', $year)->endOfYear();
        }

        // FIX: Get jobcards that have invoices - correct relationship direction
        $jobcards = Jobcard::whereExists(function($query) use ($startDate, $endDate) {
            $query->select(DB::raw(1))
                  ->from('invoices')
                  ->whereColumn('invoices.jobcard_id', 'jobcards.id')
                  ->whereDate('invoices.invoice_date', '>=', $startDate)
                  ->whereDate('invoices.invoice_date', '<=', $endDate);
        })->with('employees')->get();

        $employeeStats = [];
        
        foreach ($jobcards as $jobcard) {
            foreach ($jobcard->employees as $employee) {
                $employeeName = $employee->full_name ?? ($employee->name . ' ' . $employee->surname);
                $hours = $employee->pivot->hours_worked ?? 0;
                $traveling_km = ($employee->pivot->hour_type ?? '') === 'traveling' ? ($employee->pivot->travel_km ?? 0) : 0;
                
                if (!isset($employeeStats[$employee->id])) {
                    $employeeStats[$employee->id] = [
                        'name' => $employeeName,
                        'total_hours' => 0,
                        'jobcard_count' => 0,
                        'traveling_km' => 0,
                    ];
                }
                
                $employeeStats[$employee->id]['total_hours'] += $hours;
                $employeeStats[$employee->id]['jobcard_count']++;
                $employeeStats[$employee->id]['traveling_km'] += $traveling_km;
            }
        }
        
        // Calculate averages and format
        $stats = [];
        foreach ($employeeStats as $stat) {
            if ($stat['total_hours'] > 0 || $stat['traveling_km'] > 0) {
                $stats[] = [
                    'name' => $stat['name'],
                    'total_hours' => $stat['total_hours'],
                    'jobcard_count' => $stat['jobcard_count'],
                    'avg_hours_per_jobcard' => $stat['jobcard_count'] > 0 ? round($stat['total_hours'] / $stat['jobcard_count'], 2) : 0,
                    'traveling_km' => $stat['traveling_km'],
                ];
            }
        }
        
        return collect($stats)->sortByDesc('total_hours')->values()->all();
    }

    // Add this method to get role breakdown
    private function getEmployeeRoleBreakdown($startDate, $endDate)
    {
        return DB::table('employee_jobcard')
                ->join('jobcards', 'employee_jobcard.jobcard_id', '=', 'jobcards.id')
                ->join('employees', 'employee_jobcard.employee_id', '=', 'employees.id')
                ->join('invoices', 'jobcards.id', '=', 'invoices.jobcard_id')
                ->where('employees.is_active', true)
                ->whereDate('invoices.invoice_date', '>=', $startDate)
                ->whereDate('invoices.invoice_date', '<=', $endDate)
                ->groupBy('employees.role')
                ->select(
                    'employees.role',
                    DB::raw('SUM(employee_jobcard.hours_worked) as total_hours'),
                    DB::raw('COUNT(DISTINCT employees.id) as employee_count')
                )
                ->get();
    }
}

