<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quote;

class MobileQuoteController extends Controller
{
    public function index()
    {
        $quotes = Quote::all();
        return view('mobile.quote-view', compact('quotes'));
    }

    public function edit($id)
    {
        $quote = Quote::findOrFail($id);
        return view('mobile.quote-edit', compact('quote'));
    }
} 