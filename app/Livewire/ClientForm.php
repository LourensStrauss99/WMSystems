<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Client;
use App\Models\Jobcard;

class ClientForm extends Component
{
    public $clients = [];
    public $clientId = '';
    public $jobcard_number = '';
    public $job_date = '';
    public $work_request = '';
    public $special_request = '';
    public $showNewClientForm = false;
    public $successMessage = '';
    public $clientFields = [
        'name' => '',
        'surname' => '',
        'telephone' => '',
        'address' => '',
        'email' => '',
    ];

    public function mount()
    {
        $this->clients = Client::all();
        $this->generateJobcardNumber();
    }

    public function updatedClientId($value)
    {
        if ($value) {
            $client = Client::find($value);
            if ($client) {
                $this->clientFields = [
                    'name' => $client->name,
                    'surname' => $client->surname,
                    'telephone' => $client->telephone,
                    'address' => $client->address,
                    'email' => $client->email,
                ];
                $this->showNewClientForm = false;
            }
        } else {
            $this->clientFields = [
                'name' => '',
                'surname' => '',
                'telephone' => '',
                'address' => '',
                'email' => '',
            ];
            $this->showNewClientForm = true;
        }
        $this->generateJobcardNumber();
    }

    public function showNewClientForm()
    {
        $this->showNewClientForm = true;
        $this->clientId = '';
        $this->clientFields = [
            'name' => '',
            'surname' => '',
            'telephone' => '',
            'address' => '',
            'email' => '',
        ];
    }

    public function saveNewClient()
    {
        $validated = $this->validate([
            'clientFields.name' => 'required',
            'clientFields.surname' => 'required',
            'clientFields.telephone' => 'required',
            'clientFields.address' => 'required',
            'clientFields.email' => 'required|email|unique:clients,email',
        ])['clientFields'];

        $client = Client::create($validated);
        $this->clients = Client::all();
        $this->clientId = $client->id;
        $this->successMessage = 'Client saved successfully!';
        $this->showNewClientForm = false;
    }

    public function generateJobcardNumber()
    {
        $date = now()->format('Ymd');
        $count = Jobcard::whereDate('created_at', now()->toDateString())->count() + 1;
        $this->jobcard_number = 'JC-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function submit()
    {
        $this->validate([
            'jobcard_number' => 'required|unique:jobcards,jobcard_number',
            'job_date' => 'required|date',
            'clientId' => 'required|exists:clients,id',
            'work_request' => 'required',
        ]);

        $jobcard = Jobcard::create([
            'jobcard_number' => $this->jobcard_number,
            'job_date' => $this->job_date,
            'client_id' => $this->clientId,
            'work_request' => $this->work_request,
            'special_request' => $this->special_request,
            'status' => 'in process',
            'category' => 'in progress', // optional, since it's the default
        ]);

        return redirect()->route('jobcard.show', $jobcard->id);
    }

    public function render()
    {
        return view('livewire.client-form');
    }
}