<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'name', // add other fields as needed
    ];

    // If you have a many-to-many relationship with Jobcard:
    public function jobcards()
    {
        return $this->belongsToMany(Jobcard::class);
    }
}