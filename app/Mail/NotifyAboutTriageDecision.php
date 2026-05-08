<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use Illuminate\Support\Facades\Blade;
use App\Models\MailTemplate;
use App\Models\Triage;

class NotifyAboutTriageDecision extends Mailable
{
    use Queueable, SerializesModels;

    public $triage, $mailtemplate;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Triage $triage, MailTemplate $mailtemplate)
    {
        $this->triage = $triage;
        $this->mailtemplate = $mailtemplate;
        $this->afterCommit();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $cssToInlineStyles = new CssToInlineStyles();
        
        $subject = Blade::render(
            html_entity_decode($this->mailtemplate->subject),
            [
                "triage"=>$this->triage,
                "application"=>$this->triage->application,
            ]
        );
        
        $body = Blade::render(
            html_entity_decode($this->mailtemplate->body),
            [
                "triage"=>$this->triage,
                "application"=>$this->triage->application,
            ]
        );

        $css = file_get_contents(base_path() . '/public/css/mail.css');

        $projectFeeStatus = $this->triage->application->getAggregatedProjectFeeStatus();

        if ($projectFeeStatus == 'Pago') {
            $body .= "<br><br><p style='color: green; font-weight: bold;'>Nota: O sistema identificou que a taxa referente a esta modalidade já foi paga anteriormente. Portanto, sua inscrição está validada e você deve desconsiderar qualquer cobrança.</p>";
            return $this->html($cssToInlineStyles->convert($body, $css))->subject($subject);
        }

        if ($this->triage->application->projectFee) {
            return $this->html($cssToInlineStyles->convert($body, $css))->subject($subject)->attachData(
                base64_decode($this->triage->application->projectFee->obterBoletoPDF()),
                'boleto.pdf');
        } else {
            return $this->html($cssToInlineStyles->convert($body, $css))->subject($subject);
        }
    }
}
