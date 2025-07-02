<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_superuser',
        'admin_level',
        'role', // Added role field
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function initials()
    {
        $names = explode(' ', $this->name ?? '');
        $initials = '';
        foreach ($names as $n) {
            $initials .= strtoupper(substr($n, 0, 1));
        }
        return $initials ?: strtoupper(substr($this->email, 0, 1));
    }

    /**
     * Check if the user can approve based on their role.
     *
     * @return bool
     */
    public function canApprove()
    {
        // For now, let's say all users can approve (change this logic as needed)
        return true;

        // OR if you have a role column:
        // return $this->role === 'manager' || $this->role === 'admin';

        // OR if you have specific user IDs that can approve:
        // return in_array($this->id, [1, 2, 3]); // Replace with actual user IDs
    }
}
