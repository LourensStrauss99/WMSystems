<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;

class CustomerSearch extends Component
{
    public $search = '';

    public function render()
    {
        $customers = Customer::query()
            ->when($this->search, function($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('surname', 'like', '%'.$this->search.'%')
                  ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            ->get();

        return view('livewire.customer-search', [
            'customers' => $customers,
        ]);
    }
}
