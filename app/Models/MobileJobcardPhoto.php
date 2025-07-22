<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MobileJobcardPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'jobcard_id',
        'file_path',
        'uploaded_at',
        'uploaded_by',
        'caption',
    ];
} 