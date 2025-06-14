<?php

namespace App\Http\Controllers;

use App\Models\Jobcard;
use App\Services\JobcardWorkflowService;
use Illuminate\Http\Request;

class GlobalController extends Controller
{
    protected $workflow;

    public function __construct(JobcardWorkflowService $workflow)
    {
        $this->workflow = $workflow;
    }

    public function bookInventory(Request $request, $jobcardId)
    {
        $jobcard = Jobcard::findOrFail($jobcardId);
        $this->workflow->assignInventory($jobcard, $request->inventory_id, $request->quantity);

        return back()->with('success', 'Inventory booked!');
    }

    public function bookEmployee(Request $request, $jobcardId)
    {
        $jobcard = Jobcard::findOrFail($jobcardId);
        $this->workflow->assignEmployee($jobcard, $request->employee_id, $request->hours);

        return back()->with('success', 'Employee booked!');
    }
}