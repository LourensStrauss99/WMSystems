<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    // Add fillable or guarded properties as needed
    protected $guarded = [];

    public function jobcards()
    {
        return $this->hasMany(Jobcard::class, 'client_id');
    }

    public function invoices()
    {
        return $this->hasMany(Jobcard::class, 'client_id')->where('status', 'invoiced');
    }
}