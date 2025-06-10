<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'telephone' => [
                'required',
                'regex:/^\+?[0-9]{7,20}$/'
            ],
            'email' => 'required|email|unique:employees,email', // or users table if applicable
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,artisan,staff',
            'admin_level' => 'required|integer|min:0|max:3',
        ]);

        // ...store logic...
    }
}

