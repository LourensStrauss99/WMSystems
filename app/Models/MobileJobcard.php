<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MobileJobcard extends Model
{
    use HasFactory;

    protected $table = 'mobile_jobcards';

    protected $fillable = [
        'jobcard_number', 'job_date', 'client_id', 'category', 'work_request', 
        'special_request', 'status', 'work_done', 'time_spent', 'progress_note',
        'normal_hours', 'overtime_hours', 'weekend_hours', 'public_holiday_hours',
        'call_out_fee', 'mileage_km', 'mileage_cost', 'total_labour_cost'
    ];

    // Relationships (add as needed)
    // public function employees() { ... }
    // public function inventory() { ... }
}
