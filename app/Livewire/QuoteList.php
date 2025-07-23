<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Quote;
use App\Models\Jobcard;

class QuoteList extends Component
{
    public $quotes = [];
    public $selectedQuoteId = null;

    public function mount()
    {
        $this->quotes = Quote::orderByDesc('created_at')->get();
    }

    public function selectQuote($id)
    {
        $this->selectedQuoteId = $id;
    }

    public function viewQuote()
    {
        if ($this->selectedQuoteId) {
            return redirect()->route('quotes.show', $this->selectedQuoteId);
        }
    }

    public function sendQuote()
    {
        if ($this->selectedQuoteId) {
            // TODO: Implement email logic
            session()->flash('success', 'Quote emailed to client (stub).');
        }
    }

    public function acceptQuote()
    {
        if ($this->selectedQuoteId) {
            $quote = Quote::findOrFail($this->selectedQuoteId);
            // Create jobcard from quote
            $jobcard = Jobcard::create([
                'client_id' => $quote->client_id ?? null,
                'jobcard_number' => 'JC-' . now()->format('Ymd') . '-' . str_pad(Jobcard::max('id') + 1, 4, '0', STR_PAD_LEFT),
                'job_date' => now()->toDateString(),
                'category' => 'in progress',
                'work_request' => $quote->work_request ?? '',
                'special_request' => $quote->special_request ?? '',
                'status' => 'in progress',
                'quote_reference' => $quote->quote_number,
            ]);
            // Optionally, copy inventory/hours if needed
            $quote->status = 'accepted';
            $quote->save();
            session()->flash('success', 'Quote accepted and jobcard created!');
            $this->mount(); // Refresh list
        }
    }

    public function rejectQuote()
    {
        if ($this->selectedQuoteId) {
            $quote = Quote::findOrFail($this->selectedQuoteId);
            $quote->status = 'rejected';
            $quote->save();
            session()->flash('success', 'Quote rejected.');
            $this->mount(); // Refresh list
        }
    }

    public function amendQuote()
    {
        if ($this->selectedQuoteId) {
            return redirect()->route('quotes.edit', $this->selectedQuoteId);
        }
    }

    public function render()
    {
        return view('livewire.quote-list');
    }
}
