<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class StatementMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $customer;
    public $company;

    public function __construct($customer, $company)
    {
        $this->customer = $customer;
        $this->company = $company;
    }

    public function build()
    {
        $pdf = Pdf::loadView('emails.statement', [
            'customer' => $this->customer,
            'company' => $this->company
        ]);
        return $this->from('info@wmsystems.co.za', $this->company->name ?? 'Workflow Management System')
            ->subject('Account Statement')
            ->view('emails.statement-email')
            ->attachData($pdf->output(), 'statement.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
} 