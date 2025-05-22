<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employees';

    protected $fillable = [
        'name',
        'surname',
        'email',
        'password',
        'role',
        'admin_level',
        'telephone',
    ];

    // If you have a many-to-many relationship with Jobcard:
    public function jobcards()
    {
        return $this->belongsToMany(Jobcard::class);
    }
}