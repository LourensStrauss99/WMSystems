<?php
namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Jobcard;
use App\Models\Client;
use Illuminate\Support\Facades\Validator;

class JobcardForm extends Component
{
    public $jobcard_number;
    public $job_date;
    public $customer;
    public $name;
    public $surname;
    public $telephone;
    public $address;
    public $email;
    public $category;
    public $work_request;
    public $special_request;
    public $clients = [];
    public $showNewClientForm = false;
    public $newClient = [
        'name' => '',
        'surname' => '',
        'telephone' => '',
        'address' => '',
        'email' => '',
    ];
    public $successMessage = '';

    public function mount()
    {
        $this->clients = Client::all();
    }

    public function updated($property)
    {
        if (!$this->jobcard_number && (
            $this->customer || $this->name || $this->surname
        )) {
            $this->jobcard_number = $this->generateJobcardNumber();
        }
    }

    public function generateJobcardNumber()
    {
        $date = now()->format('Ymd');
        $count = Jobcard::whereDate('created_at', now()->toDateString())->count() + 1;
        return 'JC-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function submit()
    {
        $this->validate([
            'jobcard_number' => 'required|unique:jobcards,jobcard_number',
            'job_date' => 'required|date',
            'customer' => 'required',
            'name' => 'required',
            'surname' => 'required',
            'telephone' => 'required',
            'address' => 'required',
            'email' => 'required|email',
            'category' => 'required',
            'work_request' => 'required',
        ]);

        $jobcard = Jobcard::create([
            'jobcard_number' => $this->jobcard_number,
            'job_date' => $this->job_date,
            'client_id' => $this->customer, // assuming customer is client_id
            'category' => $this->category,
            'work_request' => $this->work_request,
            'special_request' => $this->special_request,
        ]);

        // Redirect to jobcard page or show success
        return redirect()->route('jobcard.show', $jobcard->id);
    }

    public function showNewClientForm()
    {
        $this->showNewClientForm = true;
    }

    public function saveNewClient()
    {
        $validated = Validator::make($this->newClient, [
            'name' => 'required',
            'surname' => 'required',
            'telephone' => 'required',
            'address' => 'required',
            'email' => 'required|email|unique:clients,email',
        ])->validate();

        $client = \App\Models\Client::create($validated);

        $this->clients = \App\Models\Client::all();
        $this->customer = $client->id;
        $this->showNewClientForm = false;
        $this->newClient = ['name' => '', 'surname' => '', 'telephone' => '', 'address' => '', 'email' => ''];
        $this->successMessage = 'Client saved successfully!';
    }

    public function render()
    {
        return view('livewire.jobcard-form', [
            'clients' => $this->clients,
        ]);
    }
}