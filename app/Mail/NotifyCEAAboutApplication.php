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

class NotifyCEAAboutApplication extends Mailable
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

        $mail = $this->html($cssToInlineStyles->convert($body, $css))->subject($subject)
            ->attachFromStorage($this->application->depositReceipt->path, $this->application->depositReceipt->name);

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
