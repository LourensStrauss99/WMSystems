<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $fillable = [
        'client_name',
        'client_address',
        'client_email',
        'client_telephone',
        'quote_number',
        'quote_date',
        'items',
        'notes',
    ];

    protected $casts = [
        'items' => 'array',
        'quote_date' => 'date',
    ];
}