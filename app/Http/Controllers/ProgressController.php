<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jobcard;

class ProgressController extends Controller
{
    public function index(Request $request)
    {
        $assignedJobcards = Jobcard::where('status', 'assigned')->with('client')->orderByDesc('job_date')->paginate(8, ['*'], 'assigned_page');
        $inProgressJobcards = Jobcard::where('status', 'in progress')->with('client')->orderByDesc('job_date')->paginate(8, ['*'], 'inprogress_page');
        $completedJobcards = Jobcard::where('status', 'completed')->with('client')->orderByDesc('job_date')->paginate(8, ['*'], 'completed_page');

        return view('progress', compact('assignedJobcards', 'inProgressJobcards', 'completedJobcards'));
    }

    public function show($id)
    {
        $jobcard = Jobcard::with(['client', 'employees', 'inventory'])->findOrFail($id);
        return view('progress_show', compact('jobcard'));
    }
    public function ajaxShow($id)
    {
        try {
            $jobcard = Jobcard::with(['client', 'employees', 'inventory'])->findOrFail($id);
            return response()->json($jobcard);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function updateProgress(Request $request, $id)
    {
        $jobcard = Jobcard::findOrFail($id);

        // Update employee hours
        if ($request->has('employee_hours')) {
            $syncData = [];
            foreach ($request->employee_hours as $employeeId => $hours) {
                $syncData[$employeeId] = ['hours_worked' => $hours];
            }
            $jobcard->employees()->sync($syncData);
        }

        // Save fields
        $jobcard->progress_note = $request->input('progress_note');
        $jobcard->work_done = $request->input('work_done');
        $jobcard->time_spent = $request->input('time_spent');

        // Save inventory quantities if needed
        if ($request->has('inventory')) {
            foreach ($request->input('inventory') as $itemId => $qty) {
                $jobcard->inventory()->updateExistingPivot($itemId, ['quantity' => $qty]);
            }
        }

        // Check which button was pressed
        if ($request->input('action') === 'completed') {
            $jobcard->status = 'completed';
        }

        $jobcard->save();

        return redirect()->route('progress.jobcard.show', $jobcard->id)
            ->with('success', 'Jobcard progress updated!');
    }
}
