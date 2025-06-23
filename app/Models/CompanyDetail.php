<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyDetail extends Model
{
    protected $fillable = [
        'id',
        'labour_rate',
        'vat_percent',
        'company_name',
        'company_reg_number',
        'vat_reg_number',
        'bank_name',
        'account_holder',
        'account_number',
        'branch_code',
        'swift_code',
        'address',
        'city',
        'province',
        'postal_code',
        'country',
        'company_telephone',
        'company_email',
        'company_website',
        'company_logo', // <- ADD THIS
        'invoice_terms',
        'invoice_footer',
    ];

    // Add casts for decimal fields
    protected $casts = [
        'labour_rate' => 'decimal:2',
        'vat_percent' => 'decimal:2',
    ];
}
