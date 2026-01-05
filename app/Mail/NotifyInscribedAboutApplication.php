<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use Illuminate\Support\Facades\Blade;
use App\Models\Application;
use App\Models\MailTemplate;
use Ismaelw\LaraTeX\LaraTeX;

class NotifyInscribedAboutApplication extends Mailable
{
    use Queueable, SerializesModels;

    public $application, $mailtemplate;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Application $application, MailTemplate $mailtemplate)
    {
        $this->application = $application;
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
                "application"=>$this->application,
            ]
        );
        
        $body = Blade::render(
            html_entity_decode($this->mailtemplate->body),
            [
                "application"=>$this->application,
            ]
        );

        $css = file_get_contents(base_path() . '/public/css/mail.css');

        if ($this->application->applicationFee) {
            $mail = $this->html($cssToInlineStyles->convert($body, $css))->subject($subject)->attachData(
                base64_decode($this->application->applicationFee->obterBoletoPDF()), 
                'boleto.pdf');
        } else {
            // Fault Tolerance: If boleto failed, inform the user in the email body
            $warning = "<br><br><p style='color: red; font-weight: bold;'>Atenção: Houve uma instabilidade momentânea no sistema de geração de boletos. Sua inscrição foi recebida, mas o boleto não pode ser gerado automaticamente neste momento. O CEA já foi notificado e nossa equipe entrará em contato em breve enviando o seu boleto. Não é necessário realizar nova inscrição.</p>";
            $body .= $warning;
            
            $mail = $this->html($cssToInlineStyles->convert($body, $css))->subject($subject);
        }

        try{
            $mail->attachData(
                (new LaraTeX('applications.latex'))->with(['application' => $this->application,])->content(), 
                'ficha-de-inscricao-'.$this->application->protocol.'.pdf');
        } catch (\Exception $e){
            //
        }

        foreach($this->application->attachments as $attachment){
            $mail->attachFromStorage($attachment->path, $attachment->name);
        }
        
        return $mail;
    }
}
