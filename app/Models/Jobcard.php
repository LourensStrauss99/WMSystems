<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jobcard extends Model
{
    use HasFactory;

    // Add fillable or guarded properties as needed
    protected $guarded = [];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class);
    }

    public function inventory()
    {
        return $this->belongsToMany(Inventory::class)
            ->withPivot('quantity')
            ->withTimestamps();
    }
    
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}

