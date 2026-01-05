<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifyPaymentFailure extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $application;
    public $pdfContent;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Application $application, $pdfContent)
    {
        $this->application = $application;
        $this->pdfContent = $pdfContent;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Aviso de Falha no Envio do Boleto - Inscrição CEA')
                    ->view('emails.payment_failure')
                    ->attachData(base64_decode($this->pdfContent), 'boleto.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
