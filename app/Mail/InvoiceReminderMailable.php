<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceReminderMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $company;

    public function __construct($invoice, $company)
    {
        $this->invoice = $invoice;
        $this->company = $company;
    }

    public function build()
    {
        return $this->from('info@wmsystems.co.za', $this->company->name ?? 'Workflow Management System')
            ->subject('Invoice Reminder: ' . ($this->invoice->invoice_number ?? ''))
            ->view('emails.invoice-reminder');
    }
} 