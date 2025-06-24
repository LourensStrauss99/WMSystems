<?php
// filepath: app/Http/Controllers/GrvController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GrvController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of GRVs
     */
    public function index()
    {
        // For now, return a simple view
        return view('grv.index', [
            'grvs' => collect() // Empty collection for now
        ]);
    }

    /**
     * Show the form for creating a new GRV
     */
    public function create()
    {
        return view('grv.create');
    }

    /**
     * Store a newly created GRV
     */
    public function store(Request $request)
    {
        // Placeholder for now
        return redirect()->route('grv.index')
            ->with('success', 'GRV functionality coming soon!');
    }

    /**
     * Display the specified GRV
     */
    public function show($id)
    {
        return view('grv.show', ['grv' => null]);
    }

    /**
     * Show the form for editing the specified GRV
     */
    public function edit($id)
    {
        return view('grv.edit', ['grv' => null]);
    }

    /**
     * Update the specified GRV
     */
    public function update(Request $request, $id)
    {
        return redirect()->route('grv.index')
            ->with('success', 'GRV updated successfully!');
    }

    /**
     * Remove the specified GRV
     */
    public function destroy($id)
    {
        return redirect()->route('grv.index')
            ->with('success', 'GRV deleted successfully!');
    }

    /**
     * Approve GRV
     */
    public function approve($id)
    {
        return back()->with('success', 'GRV approved successfully!');
    }

    /**
     * Reject GRV
     */
    public function reject($id)
    {
        return back()->with('success', 'GRV rejected successfully!');
    }

    /**
     * Generate PDF
     */
    public function generatePdf($id)
    {
        return response()->json(['message' => 'PDF generation coming soon!']);
    }
}

// Update quantity received
$purchaseOrderItem->update([
    'quantity_received' => $newQuantityReceived,
    'status' => $newQuantityReceived >= $purchaseOrderItem->quantity_ordered ? 'fully_received' : 'partially_received'
]);