<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Inventory::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('short_description', 'like', '%' . $request->search . '%');
        }

        $items = $query->get();

        return view('inventory.index', compact('items'));
    }

    public function create()
    {
        return view('inventory.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'short_description' => 'nullable',
            'buying_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'supplier' => 'nullable',
            'goods_received_voucher' => 'required',
            'stock_level' => 'required|integer',
            'min_level' => 'required|integer',
        ]);

        Inventory::create($data);

        return redirect('/inventory')->with('success', 'Inventory added!');
    }

    public function adminPanel()
    {
        $items = \App\Models\Inventory::all();
        return view('admin-panel', compact('items'));
    }
}