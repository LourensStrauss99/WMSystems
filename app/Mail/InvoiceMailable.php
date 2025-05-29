<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $jobcard;
    public $company;

    /**
     * Create a new message instance.
     */
    public function __construct($jobcard, $company)
    {
        $this->jobcard = $jobcard;
        $this->company = $company;
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    public function build()
    {
        return $this->subject('Your Invoice')
            ->view('emails.invoice')
            ->with([
                'jobcard' => $this->jobcard,
                'company' => $this->company,
            ]);
    }
}
