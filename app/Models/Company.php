<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'standard_labour_rate',
        'call_out_rate',
        'vat_percentage',
        'overtime_multiplier',
        'weekend_multiplier',
        'public_holiday_multiplier',
        'mileage_rate',
    ];

    protected $casts = [
        'standard_labour_rate' => 'decimal:2',
        'call_out_rate' => 'decimal:2',
        'vat_percentage' => 'decimal:2',
        'overtime_multiplier' => 'decimal:2',
        'weekend_multiplier' => 'decimal:2',
        'public_holiday_multiplier' => 'decimal:2',
        'mileage_rate' => 'decimal:2',
    ];

    /**
     * Get the company settings (singleton pattern)
     */
    public static function getSettings()
    {
        // Try to get existing company record first
        $company = self::first();
        
        if (!$company) {
            // Create with your actual business rates
            $company = self::create([
                'name' => 'Your Company Name',
                'standard_labour_rate' => 750.00,        // From your master settings
                'call_out_rate' => 1000.00,              // From your master settings  
                'vat_percentage' => 15.00,               // From your master settings
                'overtime_multiplier' => 1.50,          // From your master settings
                'weekend_multiplier' => 2.00,           // From your master settings
                'public_holiday_multiplier' => 2.50,    // From your master settings
                'mileage_rate' => 7.50,                  // From your master settings
            ]);
        }
        
        return $company;
    }

    /**
     * Calculate rate based on hour type
     */
    public function calculateHourlyRate($hourType = 'normal')
    {
        switch ($hourType) {
            case 'overtime':
                return $this->standard_labour_rate * $this->overtime_multiplier;
            case 'weekend':
                return $this->standard_labour_rate * $this->weekend_multiplier;
            case 'holiday':
                return $this->standard_labour_rate * $this->public_holiday_multiplier;
            case 'callout':
                return $this->call_out_rate;
            default:
                return $this->standard_labour_rate;
        }
    }
}
