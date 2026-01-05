<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Application;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class NotifyUserNewBoleto extends Mailable
{
    use Queueable, SerializesModels;

    public $application;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $cssToInlineStyles = new CssToInlineStyles();
        
        $subject = 'Inscrições CEA - Novo Boleto Gerado';
        
        $body = "
            <p>Olá {$this->application->name},</p>
            <p>Segue em anexo o seu boleto de pagamento para a inscrição no CEA.</p>
            <p>Atenciosamente,<br>Equipe CEA</p>
        ";

        // Try to load css, fallback if fails
        $css = '';
        try {
            $css = file_get_contents(base_path() . '/public/css/mail.css');
        } catch (\Exception $e) {
            // ignore
        }

        $mail = $this->html($cssToInlineStyles->convert($body, $css))
                     ->subject($subject);

        if ($this->application->applicationFee) {
             $mail->attachData(
                base64_decode($this->application->applicationFee->obterBoletoPDF()), 
                'boleto.pdf'
            );
        }

        return $mail;
    }
}
