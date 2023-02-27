<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use Illuminate\Support\Facades\Blade;
use App\Models\MailTemplate;
use App\Models\ConsultationMeeting;

class NotifyAboutConsultationMeetingSchedule extends Mailable
{
    use Queueable, SerializesModels;

    public $consultationmeeting, $mailtemplate;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ConsultationMeeting $consultationmeeting, MailTemplate $mailtemplate)
    {
        $this->consultationmeeting = $consultationmeeting;
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
                "consultationmeeting"=>$this->consultationmeeting,
                "application"=>$this->consultationmeeting->application,
            ]
        );
        
        $body = Blade::render(
            html_entity_decode($this->mailtemplate->body),
            [
                "consultationmeeting"=>$this->consultationmeeting,
                "application"=>$this->consultationmeeting->application,
            ]
        );

        $css = file_get_contents(base_path() . '/public/css/mail.css');

        return $this->html($cssToInlineStyles->convert($body, $css))->subject($subject);
    }
}
