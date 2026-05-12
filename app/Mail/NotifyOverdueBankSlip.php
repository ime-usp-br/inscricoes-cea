<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use Illuminate\Support\Facades\Blade;
use App\Models\Application;
use App\Models\BankSlip;
use App\Models\MailTemplate;

class NotifyOverdueBankSlip extends Mailable
{
    use Queueable, SerializesModels;

    public $application, $bankSlip, $mailtemplate;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Application $application, BankSlip $bankSlip, MailTemplate $mailtemplate)
    {
        $this->application = $application;
        $this->bankSlip = $bankSlip;
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
                "application" => $this->application,
                "bankSlip" => $this->bankSlip,
            ]
        );

        $body = Blade::render(
            html_entity_decode($this->mailtemplate->body),
            [
                "application" => $this->application,
                "bankSlip" => $this->bankSlip,
            ]
        );

        $css = '';
        try {
            $css = file_get_contents(base_path() . '/public/css/mail.css');
        } catch (\Exception $e) {
            // ignore
        }

        return $this->html($cssToInlineStyles->convert($body, $css))
                    ->subject($subject);
    }
}
