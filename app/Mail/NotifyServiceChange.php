<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Application;
use App\Models\BankSlip;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class NotifyServiceChange extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $boleto;
    public $tipoMudanca; // 'ProjetoParaConsulta' usually

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Application $application, $boleto = null, $tipoMudanca = 'ProjetoParaConsulta')
    {
        $this->application = $application;
        $this->boleto = $boleto;
        $this->tipoMudanca = $tipoMudanca;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $cssToInlineStyles = new CssToInlineStyles();
        
        $subject = 'Inscrições CEA - Mudança de Modalidade';
        
        $mensagemExtra = "";
        if ($this->boleto) {
            $mensagemExtra = "<p>Como houve mudança na modalidade e alteração no valor da taxa, segue em anexo o boleto para pagamento.</p>";
            if ($this->boleto->relativoA == 'Complemento de Taxa') {
                $mensagemExtra .= "<p>Este boleto é referente à diferença de valor entre a taxa de projeto (já paga) e a taxa de consulta.</p>";
            }
        } else {
            // Case where maybe it was already paid fully or no fee involved? (Unlikely per requirements, but good to handle)
            $mensagemExtra = "<p>Sua inscrição foi atualizada no sistema.</p>";
        }

        $body = "
            <p>Olá {$this->application->bdName},</p>
            <p>Informamos que a modalidade da sua inscrição (Protocolo: {$this->application->protocol}) foi alterada de <strong>Projeto</strong> para <strong>Consulta</strong>.</p>
            $mensagemExtra
            <p>Qualquer dúvida, entre em contato.</p>
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

        if ($this->boleto) {
             $pdfContent = $this->boleto->obterBoletoPDF();
             if ($pdfContent) {
                 $mail->attachData(
                    base64_decode($pdfContent), 
                    'boleto.pdf',
                    ['mime' => 'application/pdf']
                );
             }
        }

        return $mail;
    }
}
