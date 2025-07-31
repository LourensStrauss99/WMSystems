<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ClientStatementController extends Controller
{
    public function show($clientId, Request $request)
    {
        $client = Client::findOrFail($clientId);
        $start = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : now()->startOfMonth();
        $end = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : now()->endOfMonth();

        // Opening balance: sum of all invoices - payments before start date
        $invoicesBefore = Invoice::where('client_id', $clientId)->where('date', '<', $start)->sum('total');
        $paymentsBefore = Payment::where('client_id', $clientId)->where('date', '<', $start)->sum('amount');
        $openingBalance = $invoicesBefore - $paymentsBefore;

        // Invoices and payments in period
        $invoices = Invoice::where('client_id', $clientId)->whereBetween('date', [$start, $end])->get();
        $payments = Payment::where('client_id', $clientId)->whereBetween('date', [$start, $end])->get();

        // Closing balance
        $invoicesInPeriod = $invoices->sum('total');
        $paymentsInPeriod = $payments->sum('amount');
        $closingBalance = $openingBalance + $invoicesInPeriod - $paymentsInPeriod;

        return view('clients.statement', compact('client', 'start', 'end', 'openingBalance', 'invoices', 'payments', 'closingBalance'));
    }
}
