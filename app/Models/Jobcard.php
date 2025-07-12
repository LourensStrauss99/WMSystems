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
        return $this->belongsToMany(Employee::class)
           ->withPivot('hours_worked', 'hour_type')  // Include hour_type
           ->withTimestamps();
    }

    public function inventory()
    {
        return $this->belongsToMany(Inventory::class)
            ->withPivot('quantity')
            ->withTimestamps();
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
}

