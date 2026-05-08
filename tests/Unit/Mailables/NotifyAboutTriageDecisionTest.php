<?php

namespace Tests\Unit\Mailables;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Application;
use App\Models\BankSlip;
use App\Models\MailTemplate;
use App\Models\Triage;
use App\Mail\NotifyAboutTriageDecision;

class NotifyAboutTriageDecisionTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_with_paid_fee_has_no_attachment_and_shows_exemption()
    {
        $mailTemplate = MailTemplate::factory()->create([
            'mail_class' => 'NotifyAboutTriageDecision',
            'sending_frequency' => 'A cada resultado de triagem',
            'active' => true,
            'subject' => 'Resultado da Triagem',
            'body' => 'Prezado, sua inscrição foi aprovada.',
        ]);

        $app = Application::factory()->create();

        BankSlip::factory()->create([
            'applicationID' => $app->id,
            'relativoA' => 'Taxa de Projeto',
            'statusBoletoBancario' => 'P',
            'dataVencimentoBoleto' => now()->addDays(5)->format('d/m/Y'),
        ]);

        $triage = Triage::factory()->create(['applicationID' => $app->id]);

        $mailable = new NotifyAboutTriageDecision($triage, $mailTemplate);
        $mailable->build();
        $html = $mailable->render();

        $this->assertStringContainsString('style="color: green; font-weight: bold;"', $html);
        $this->assertStringContainsString('Nota: O sistema identificou que a taxa referente a esta modalidade já foi paga anteriormente', $html);

        $reflection = new \ReflectionClass($mailable);
        $property = $reflection->getProperty('rawAttachments');
        $property->setAccessible(true);
        $this->assertCount(0, $property->getValue($mailable));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_email_with_unpaid_fee_has_attachment()
    {
        \nusoap_client::$mockStatus = 'E';

        $mailTemplate = MailTemplate::factory()->create([
            'mail_class' => 'NotifyAboutTriageDecision',
            'sending_frequency' => 'A cada resultado de triagem',
            'active' => true,
            'subject' => 'Resultado da Triagem',
            'body' => 'Prezado, sua inscrição foi aprovada.',
        ]);

        $app = Application::factory()->create();

        BankSlip::factory()->create([
            'applicationID' => $app->id,
            'relativoA' => 'Taxa de Projeto',
            'statusBoletoBancario' => 'E',
            'dataVencimentoBoleto' => now()->addDays(5)->format('d/m/Y'),
        ]);

        $triage = Triage::factory()->create(['applicationID' => $app->id]);

        $mailable = new NotifyAboutTriageDecision($triage, $mailTemplate);
        $mailable->build();

        $reflection = new \ReflectionClass($mailable);
        $property = $reflection->getProperty('rawAttachments');
        $property->setAccessible(true);
        $this->assertCount(1, $property->getValue($mailable));
    }
}
