<?php
// filepath: app/Http/Controllers/ReportController.php

namespace App\Http\Controllers;

use App\Models\Jobcard;

class ReportController extends Controller
{
    public function index()
    {
        $totalMinutes = Jobcard::whereIn('status', ['in_progress', 'completed'])
            ->sum('time_spent');

        $hoursBooked = round($totalMinutes / 60, 2); // 2 decimal places

        $jobcards = Jobcard::whereIn('status', ['in_progress', 'completed'])->get();

        return view('reports', [
            'hoursBooked' => $hoursBooked,
            'jobcards' => $jobcards,
        ]);
    }
}