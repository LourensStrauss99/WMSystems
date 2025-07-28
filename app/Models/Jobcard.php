<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jobcard extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Add these to your fillable/guarded
    protected $fillable = [
        'jobcard_number', 'job_date', 'client_id', 'category', 'work_request', 
        'special_request', 'status', 'work_done', 'time_spent', 'progress_note',
        'normal_hours', 'overtime_hours', 'weekend_hours', 'public_holiday_hours',
        'call_out_fee', 'mileage_km', 'mileage_cost', 'total_labour_cost'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_jobcard')
                    ->withPivot([
                        'hours_worked', 
                        'hour_type',     // Add this
                        'hourly_rate',   // Add this  
                        'total_cost',    // Add this
                        'travel_km'      // Ensure travel_km is included
                    ])
                    ->withTimestamps();
    }

    public function inventory()
    {
        return $this->belongsToMany(\App\Models\Inventory::class, 'inventory_jobcard')
            ->withPivot('quantity', 'buying_price', 'selling_price')
            ->withTimestamps();
    }

    // Helper method to get inventory total
    public function getInventoryTotal()
    {
        return $this->inventory->sum(function($item) {
            $quantity = $item->pivot->quantity ?? 0;
            $sellingPrice = $item->selling_price ?? $item->sell_price ?? 0;
            return $quantity * $sellingPrice;
        });
    }
    
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
    
    public function calculateLabourCost()
    {
        $company = \App\Models\CompanyDetail::first();
        
        $normalCost = $this->normal_hours * $company->labour_rate;
        $overtimeCost = $this->overtime_hours * ($company->labour_rate * $company->overtime_multiplier);
        $weekendCost = $this->weekend_hours * ($company->labour_rate * $company->weekend_multiplier);
        $holidayCost = $this->public_holiday_hours * ($company->labour_rate * $company->public_holiday_multiplier);
        
        $totalLabour = $normalCost + $overtimeCost + $weekendCost + $holidayCost;
        $totalWithCallOut = $totalLabour + $this->call_out_fee + $this->mileage_cost;
        
        return $totalWithCallOut;
    }
    
    public function calculateGrandTotal()
    {
        $inventoryTotal = $this->inventory->sum(function($item) {
            return $item->pivot->quantity * $item->selling_price;
        });
        
        $labourTotal = $this->calculateLabourCost();
        
        return $inventoryTotal + $labourTotal;
    }

    // Add helper method to calculate total labor costs
    public function calculateLaborCosts()
    {
        $companyDetails = \App\Models\CompanyDetail::first();
        
        $costs = [
            'normal_cost' => $this->normal_hours * ($companyDetails->labour_rate ?? 450),
            'overtime_cost' => $this->overtime_hours * ($companyDetails->labour_rate ?? 450) * ($companyDetails->overtime_multiplier ?? 1.5),
            'weekend_cost' => $this->weekend_hours * ($companyDetails->labour_rate ?? 450) * ($companyDetails->weekend_multiplier ?? 2.0),
            'holiday_cost' => $this->public_holiday_hours * ($companyDetails->labour_rate ?? 450) * ($companyDetails->public_holiday_multiplier ?? 2.5),
            'call_out_cost' => $this->call_out_fee,
            'mileage_cost' => $this->mileage_cost,
        ];
        
        $totalCost = array_sum($costs);
        
        $this->update(['total_labour_cost' => $totalCost]);
        
        return $costs;
    }

    public function mobilePhotos()
    {
        return $this->hasMany(\App\Models\MobileJobcardPhoto::class, 'jobcard_id');
    }
}

