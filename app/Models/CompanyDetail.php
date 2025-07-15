<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        // Rates and business settings
        'labour_rate',
        'call_out_rate',
        'overtime_multiplier',
        'weekend_multiplier',
        'public_holiday_multiplier',
        'mileage_rate',
        'vat_percent',
        'markup_percentage',
        'discount_threshold',
        'default_payment_terms',
        'late_payment_fee',
        'late_payment_fee_percent',
        'quote_validity_days',
        'warranty_period_months',
        'minimum_invoice_amount',
        'po_auto_approval_limit',
        
        // Company information
        'company_name',
        'trading_name',
        'company_reg_number',
        'vat_reg_number',
        'paye_number',
        'uif_number',
        'bee_level',
        
        // Contact details
        'company_telephone',
        'company_fax',
        'company_cell',
        'company_email',
        'accounts_email',
        'orders_email',
        'support_email',
        'company_website',
        'company_logo',
        
        // Address information
        'address',
        'physical_address',
        'postal_address',
        'city',
        'province',
        'postal_code',
        'country',
        
        // Banking information
        'bank_name',
        'account_holder',
        'account_number',
        'branch_code',
        'branch_name',
        'swift_code',
        'account_type',
        
        // Document settings
        'reference_format',
        'invoice_terms',
        'invoice_footer',
        'quote_terms',
        'po_terms',
        'warranty_terms',
        'company_slogan',
        'company_description',
        'letterhead_template',
        
        // JSON fields
        'hourly_rate_categories',
        'business_sectors',
        'certification_numbers',
        'insurance_details',
        'safety_certifications',
    ];

    protected $casts = [
        'labour_rate' => 'decimal:2',
        'call_out_rate' => 'decimal:2',
        'overtime_multiplier' => 'decimal:2',
        'weekend_multiplier' => 'decimal:2',
        'public_holiday_multiplier' => 'decimal:2',
        'mileage_rate' => 'decimal:2',
        'vat_percent' => 'decimal:2',
        'markup_percentage' => 'decimal:2',
        'discount_threshold' => 'decimal:2',
        'late_payment_fee' => 'decimal:2',
        'late_payment_fee_percent' => 'decimal:2',
        'minimum_invoice_amount' => 'decimal:2',
        'po_auto_approval_limit' => 'decimal:2',
        'default_payment_terms' => 'integer',
        'quote_validity_days' => 'integer',
        'warranty_period_months' => 'integer',
        'hourly_rate_categories' => 'array',
        'business_sectors' => 'array',
        'certification_numbers' => 'array',
        'insurance_details' => 'array',
        'safety_certifications' => 'array',
    ];

    // Get the singleton company details
    public static function getCompany()
    {
        return static::first() ?? static::createDefault();
    }

    // Create default company details with industry standards
    public static function createDefault()
    {
        return self::create([
            'company_name' => 'Your Company Name',
            'trading_name' => 'Your Trading Name',
            'vat_percent' => 15,
            'labour_rate' => 450.00,
            'call_out_rate' => 850.00,
            'overtime_multiplier' => 1.5,
            'weekend_multiplier' => 2.0,
            'public_holiday_multiplier' => 2.5,
            'mileage_rate' => 3.50,
            'markup_percentage' => 25,
            'discount_threshold' => 10,
            'default_payment_terms' => 30,
            'late_payment_fee' => 100.00,
            'late_payment_fee_percent' => 2.0,
            'quote_validity_days' => 30,
            'warranty_period_months' => 12,
            'minimum_invoice_amount' => 500.00,
            'country' => 'South Africa',
            'province' => 'Gauteng',
            'reference_format' => 'INV-{YYYY}{MM}-{0000}',
            'invoice_terms' => 'Payment due within 30 days of invoice date. Interest at prime rate + 2% per month charged on overdue accounts.',
            'quote_terms' => 'This quotation is valid for 30 days from date of issue. Prices exclude VAT unless otherwise stated.',
            'po_terms' => 'All goods remain the property of the supplier until payment is received in full.',
            'warranty_terms' => 'Standard 12-month warranty applies to all workmanship. Parts warranty as per manufacturer specifications.',
            'hourly_rate_categories' => json_encode([
                'standard' => ['name' => 'Standard Labour', 'rate' => 450],
                'skilled' => ['name' => 'Skilled Technician', 'rate' => 650],
                'specialist' => ['name' => 'Specialist/Engineer', 'rate' => 850],
                'management' => ['name' => 'Project Management', 'rate' => 1200],
                'apprentice' => ['name' => 'Apprentice', 'rate' => 250],
                'overtime' => ['name' => 'Overtime Rate', 'rate' => 675], // 450 * 1.5
                'weekend' => ['name' => 'Weekend Rate', 'rate' => 900],   // 450 * 2.0
                'holiday' => ['name' => 'Holiday Rate', 'rate' => 1125],  // 450 * 2.5
                'callout' => ['name' => 'Call Out Rate', 'rate' => 850],
                'travel' => ['name' => 'Travel Rate', 'rate' => 450],
            ]),
            'business_sectors' => json_encode([
                'Industrial Maintenance',
                'Electrical Installation', 
                'Mechanical Repairs',
                'Project Management',
                'Consulting Services'
            ]),
        ]);
    }

    // Industry standard calculations
    public function calculateLabourCost($hours, $category = 'standard', $multiplier = 1.0)
    {
        $rate = $this->getHourlyRate($category);
        return $hours * $rate * $multiplier;
    }

    public function calculateCallOut($includeFirstHour = true)
    {
        $cost = $this->call_out_rate;
        if ($includeFirstHour) {
            $cost += $this->getHourlyRate('standard');
        }
        return $cost;
    }

    public function calculateMileage($kilometers)
    {
        return $kilometers * $this->mileage_rate;
    }

    // Get formatted company info for documents
    public function getDocumentHeader()
    {
        return [
            'company_name' => $this->company_name,
            'trading_name' => $this->trading_name,
            'address' => $this->physical_address,
            'city' => $this->city,
            'province' => $this->province,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'phone' => $this->company_telephone,
            'email' => $this->company_email,
            'website' => $this->company_website,
            'vat_number' => $this->vat_reg_number,
            'reg_number' => $this->company_reg_number,
            'logo_url' => $this->logo_url
        ];
    }

    // Get banking details for invoices
    public function getBankingDetails()
    {
        return [
            'bank_name' => $this->bank_name,
            'branch_name' => $this->branch_name,
            'branch_code' => $this->branch_code,
            'account_holder' => $this->account_holder,
            'account_number' => $this->account_number,
            'account_type' => $this->account_type,
            'swift_code' => $this->swift_code,
        ];
    }

    // Check if company setup is complete for business operations
    public function isSetupComplete()
    {
        $required = [
            'company_name', 'physical_address', 'city', 'country', 
            'company_telephone', 'company_email', 'vat_reg_number',
            'labour_rate', 'vat_percent', 'bank_name', 'account_number'
        ];
        
        foreach ($required as $field) {
            if (empty($this->$field)) {
                return false;
            }
        }
        
        return true;
    }

    // Get rate multiplier for time/day
    public function getRateMultiplier($datetime = null)
    {
        if (!$datetime) $datetime = now();
        
        $isWeekend = $datetime->isWeekend();
        $isPublicHoliday = $this->isPublicHoliday($datetime);
        $hour = $datetime->hour;
        
        if ($isPublicHoliday) return $this->public_holiday_multiplier;
        if ($isWeekend) return $this->weekend_multiplier;
        if ($hour < 7 || $hour > 17) return $this->overtime_multiplier;
        
        return 1.0;
    }

    // Simple public holiday check (you can enhance this)
    private function isPublicHoliday($date)
    {
        $publicHolidays = [
            '01-01', '03-21', '04-27', '05-01', '06-16', 
            '08-09', '09-24', '12-16', '12-25', '12-26'
        ];
        
        return in_array($date->format('m-d'), $publicHolidays);
    }
}
