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
        
        $company = Company::getSettings();
        
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
        
        // Get active artisans for this month
        $artisans = Employee::where('role', 'artisan')
                           ->where('is_active', true)
                           ->where('created_at', '<=', $endDate)
                           ->count();
        
        // Calculate available hours (8 hours per day per artisan)
        $availableHours = $workingDays * 8 * $artisans;
        
        // Get booked hours from jobcards
        $bookedHours = DB::table('employee_jobcard')
                        ->join('jobcards', 'employee_jobcard.jobcard_id', '=', 'jobcards.id')
                        ->join('employees', 'employee_jobcard.employee_id', '=', 'employees.id')
                        ->where('employees.role', 'artisan')
                        ->whereYear('jobcards.created_at', $startDate->year)
                        ->whereMonth('jobcards.created_at', $startDate->month)
                        ->sum('employee_jobcard.hours_worked');
        
        // Calculate overtime, weekend, and holiday hours
        $overtimeHours = $this->getOvertimeHours($startDate, $endDate);
        $weekendHours = $this->getWeekendHours($startDate, $endDate);
        $holidayHours = $this->getHolidayHours($startDate, $endDate);
        
        $utilizationRate = $availableHours > 0 ? round(($bookedHours / $availableHours) * 100, 2) : 0;
        
        return [
            'working_days' => $workingDays,
            'artisan_count' => $artisans,
            'available_hours' => $availableHours,
            'booked_hours' => $bookedHours,
            'normal_hours' => $bookedHours - $overtimeHours - $weekendHours - $holidayHours,
            'overtime_hours' => $overtimeHours,
            'weekend_hours' => $weekendHours,
            'holiday_hours' => $holidayHours,
            'utilization_rate' => $utilizationRate,
            'hours_per_employee' => $artisans > 0 ? round($bookedHours / $artisans, 2) : 0,
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
        
        // Get average artisan count for the year
        $artisans = Employee::where('role', 'artisan')
                           ->where('is_active', true)
                           ->where('created_at', '<=', $endDate)
                           ->count();
        
        $availableHours = $workingDays * 8 * $artisans;
        
        $bookedHours = DB::table('employee_jobcard')
                        ->join('jobcards', 'employee_jobcard.jobcard_id', '=', 'jobcards.id')
                        ->join('employees', 'employee_jobcard.employee_id', '=', 'employees.id')
                        ->where('employees.role', 'artisan')
                        ->whereYear('jobcards.created_at', $year)
                        ->sum('employee_jobcard.hours_worked');
        
        $overtimeHours = $this->getOvertimeHours($startDate, $endDate);
        $weekendHours = $this->getWeekendHours($startDate, $endDate);
        $holidayHours = $this->getHolidayHours($startDate, $endDate);
        
        $utilizationRate = $availableHours > 0 ? round(($bookedHours / $availableHours) * 100, 2) : 0;
        
        return [
            'working_days' => $workingDays,
            'artisan_count' => $artisans,
            'available_hours' => $availableHours,
            'booked_hours' => $bookedHours,
            'normal_hours' => $bookedHours - $overtimeHours - $weekendHours - $holidayHours,
            'overtime_hours' => $overtimeHours,
            'weekend_hours' => $weekendHours,
            'holiday_hours' => $holidayHours,
            'utilization_rate' => $utilizationRate,
            'hours_per_employee' => $artisans > 0 ? round($bookedHours / $artisans, 2) : 0,
        ];
    }

    private function getMonthlyRevenueBreakdown($month)
    {
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
        $company = Company::getSettings();
        
        // Get jobcards for the month
        $jobcards = Jobcard::whereYear('created_at', $startDate->year)
                          ->whereMonth('created_at', $startDate->month)
                          ->get();
        
        // Calculate hours revenue
        $normalHours = 0;
        $overtimeHours = 0;
        $weekendHours = 0;
        $holidayHours = 0;
        $calloutHours = 0;
        
        foreach ($jobcards as $jobcard) {
            $hours = $jobcard->employees()->sum('employee_jobcard.hours_worked');
            
            // Determine hour type based on jobcard date/time
            if ($this->isHoliday($jobcard->created_at)) {
                $holidayHours += $hours;
            } elseif ($this->isWeekend($jobcard->created_at)) {
                $weekendHours += $hours;
            } elseif ($this->isOvertime($jobcard)) {
                $overtimeHours += $hours;
            } elseif ($jobcard->is_callout) {
                $calloutHours += $hours;
            } else {
                $normalHours += $hours;
            }
        }
        
        $hoursRevenue = [
            'normal' => $normalHours * $company->standard_labour_rate,
            'overtime' => $overtimeHours * $company->calculateHourlyRate('overtime'),
            'weekend' => $weekendHours * $company->calculateHourlyRate('weekend'),
            'holiday' => $holidayHours * $company->calculateHourlyRate('holiday'),
            'callout' => $calloutHours * $company->calculateHourlyRate('callout'),
        ];
        
        $totalHoursRevenue = array_sum($hoursRevenue);
        
        // Calculate inventory revenue and profit
        $inventoryData = $this->getInventoryRevenue($startDate, $endDate);
        
        // Calculate totals
        $subtotal = $totalHoursRevenue + $inventoryData['revenue'];
        $vatAmount = $subtotal * ($company->vat_percentage / 100);
        $totalIncVat = $subtotal + $vatAmount;
        $netProfit = $totalHoursRevenue + $inventoryData['profit']; // Hours revenue + inventory profit (no VAT deduction as it's pass-through)
        
        return [
            'hours_revenue' => $totalHoursRevenue,
            'hours_breakdown' => $hoursRevenue,
            'hours_detail' => [
                'normal' => $normalHours,
                'overtime' => $overtimeHours,
                'weekend' => $weekendHours,
                'holiday' => $holidayHours,
                'callout' => $calloutHours,
            ],
            'inventory_revenue' => $inventoryData['revenue'],
            'inventory_cost' => $inventoryData['cost'],
            'inventory_profit' => $inventoryData['profit'],
            'inventory_margin' => $inventoryData['margin'],
            'subtotal' => $subtotal,
            'vat_amount' => $vatAmount,
            'total_inc_vat' => $totalIncVat,
            'net_profit' => $netProfit,
            'profit_margin' => $subtotal > 0 ? round(($netProfit / $subtotal) * 100, 2) : 0,
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
        
        return $this->getMonthlyRevenueBreakdown($startDate->format('Y-m'));
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

    private function getEmployeeStats($month, $viewMode)
    {
        $startDate = $viewMode === 'ytd' 
            ? Carbon::createFromFormat('Y-m', $month)->startOfYear()
            : Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        
        $endDate = $viewMode === 'ytd' 
            ? Carbon::createFromFormat('Y-m', $month)->endOfYear()
            : Carbon::createFromFormat('Y-m', $month)->endOfMonth();
        
        return Employee::where('role', 'artisan')
                  ->where('is_active', true)
                  ->get()
                  ->map(function($employee) use ($startDate, $endDate) {
                      // Get total hours worked by this employee in the period
                      $totalHours = DB::table('employee_jobcard')
                                     ->join('jobcards', 'employee_jobcard.jobcard_id', '=', 'jobcards.id')
                                     ->where('employee_jobcard.employee_id', $employee->id)
                                     ->whereDate('jobcards.created_at', '>=', $startDate)
                                     ->whereDate('jobcards.created_at', '<=', $endDate)
                                     ->sum('employee_jobcard.hours_worked');
                      
                      // Get jobcard count for this employee in the period
                      $jobcardCount = DB::table('employee_jobcard')
                                       ->join('jobcards', 'employee_jobcard.jobcard_id', '=', 'jobcards.id')
                                       ->where('employee_jobcard.employee_id', $employee->id)
                                       ->whereDate('jobcards.created_at', '>=', $startDate)
                                       ->whereDate('jobcards.created_at', '<=', $endDate)
                                       ->distinct('jobcards.id')
                                       ->count('jobcards.id');

                      return [
                          'name' => $employee->name . ' ' . $employee->surname,
                          'total_hours' => $totalHours,
                          'jobcard_count' => $jobcardCount,
                          'avg_hours_per_jobcard' => $jobcardCount > 0 ? round($totalHours / $jobcardCount, 2) : 0,
                      ];
                  });
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
    
    private function getOvertimeHours($startDate, $endDate)
    {
        // Calculate overtime hours for the period
        return 0;
    }
    
    private function getWeekendHours($startDate, $endDate)
    {
        // Calculate weekend hours for the period
        return 0;
    }
    
    private function getHolidayHours($startDate, $endDate)
    {
        // Calculate holiday hours for the period
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
}