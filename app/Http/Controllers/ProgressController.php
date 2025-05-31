<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jobcard;

class ProgressController extends Controller
{
    public function index(Request $request)
    {
        $assignedJobcards = \App\Models\Jobcard::where('status', 'assigned')->with('client')->orderByDesc('job_date')->paginate(8, ['*'], 'assigned_page');
        $inProgressJobcards = \App\Models\Jobcard::where('status', 'in progress')->with('client')->orderByDesc('job_date')->paginate(8, ['*'], 'inprogress_page');
        $completedJobcards = \App\Models\Jobcard::where('status', 'completed')->with('client')->orderByDesc('job_date')->paginate(8, ['*'], 'completed_page');

        return view('progress', compact('assignedJobcards', 'inProgressJobcards', 'completedJobcards'));
    }

    public function show($id)
    {
        $jobcard = Jobcard::with(['client', 'employees', 'spares'])->findOrFail($id);
       //dd($jobcard); // This will dump the jobcard and stop execution
        $employees = \App\Models\Employee::all();
        $inventory = \App\Models\Inventory::all();
        return view('jobcard.show', compact('jobcard', 'employees', 'inventory'));
    }
    public function ajaxShow($id)
    {
        try {
            $jobcard = \App\Models\Jobcard::with(['client', 'employees', 'spares'])->findOrFail($id);
            return response()->json($jobcard);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function updateProgress(Request $request, $id)
    {
        $jobcard = \App\Models\Jobcard::findOrFail($id);

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

        return redirect()->route('progress.jobcard.show', $jobcard->id)->with('success', 'Jobcard updated!');
    }
}
