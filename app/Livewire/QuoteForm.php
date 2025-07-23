<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Client;
use App\Models\Inventory;
use App\Models\Quote;

class QuoteForm extends Component
{
    public $clients = [];
    public $client_id;
    public $showAddClientModal = false;
    public $work_request = '';
    public $special_request = '';
    public $inventory_items = [];
    public $hours = [];
    public $notes = '';
    public $quote_number;
    public $quote_date;
    public $new_client = [
        'name' => '',
        'surname' => '',
        'email' => '',
        'telephone' => '',
        'address' => '',
    ];
    public $all_inventory = [];

    public function mount()
    {
        $this->clients = Client::orderBy('name')->get();
        $this->all_inventory = Inventory::orderBy('description')->get();
        $this->quote_number = $this->generateQuoteNumber();
        $this->quote_date = now()->toDateString();
        $this->inventory_items = [];
        $this->hours = [];
    }

    public function generateQuoteNumber()
    {
        $latest = Quote::orderByDesc('id')->first();
        $next = $latest ? $latest->id + 1 : 1;
        return 'Q-' . now()->format('Ymd') . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function showAddClientModal()
    {
        $this->showAddClientModal = true;
    }

    public function hideAddClientModal()
    {
        $this->showAddClientModal = false;
    }

    public function addClient()
    {
        $client = Client::create($this->new_client);
        $this->clients = Client::orderBy('name')->get();
        $this->client_id = $client->id;
        $this->hideAddClientModal();
        $this->new_client = [
            'name' => '',
            'surname' => '',
            'email' => '',
            'telephone' => '',
            'address' => '',
        ];
    }

    public function addInventoryItem($item_id = null)
    {
        $this->inventory_items[] = [
            'inventory_id' => $item_id,
            'quantity' => 1,
        ];
    }

    public function removeInventoryItem($index)
    {
        unset($this->inventory_items[$index]);
        $this->inventory_items = array_values($this->inventory_items);
    }

    public function addHourRow()
    {
        $this->hours[] = [
            'type' => 'normal',
            'hours' => 0,
        ];
    }

    public function removeHourRow($index)
    {
        unset($this->hours[$index]);
        $this->hours = array_values($this->hours);
    }

    public function saveQuote()
    {
        $client = Client::find($this->client_id);
        $quote = Quote::create([
            'client_name' => $client->name,
            'client_address' => $client->address,
            'client_email' => $client->email,
            'client_telephone' => $client->telephone,
            'quote_number' => $this->quote_number,
            'quote_date' => $this->quote_date,
            'items' => $this->inventory_items,
            'notes' => $this->notes,
        ]);
        // Optionally, emit event or redirect
        session()->flash('success', 'Quote saved successfully!');
        // Reset form
        $this->reset(['client_id', 'work_request', 'special_request', 'inventory_items', 'hours', 'notes']);
        $this->quote_number = $this->generateQuoteNumber();
    }

    public function render()
    {
        return view('livewire.quote-form');
    }
}
