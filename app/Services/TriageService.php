<?php

namespace App\Services;

use App\Models\Triage;
use App\Models\BankSlip;
use App\Models\MailTemplate;
use App\Mail\NotifyAboutTriageDecision;
use Illuminate\Support\Facades\Mail;

class TriageService
{
    public function processDecision(Triage $triage, array $data)
    {
        $triage->update($data);

        $triage->application->status = $triage->decision;
        $triage->application->save();

        $this->handleFees($triage);
        $this->sendNotification($triage);
    }

    protected function handleFees(Triage $triage)
    {
        if ($triage->decision == "Aprovado como projeto" and $triage->application->getAggregatedProjectFeeStatus() != 'Pago') {
            $bankSlip = BankSlip::gerarBoletoRegistrado($triage->application, 250.00, 0, "Taxa de Projeto");
            if ($bankSlip) {
                $triage->application->projectFee()->save($bankSlip);
            }
        } elseif ($triage->decision == "Aprovado como Consulta" and !$triage->application->complementaryFee) {
            $bankSlip = BankSlip::gerarBoletoRegistrado($triage->application, 60.00, 0, "Complemento de Taxa");
            if ($bankSlip) {
                $triage->application->complementaryFee()->save($bankSlip);
            }
        }
    }

    protected function sendNotification(Triage $triage)
    {
        $mailtemplate = MailTemplate::where([
            "mail_class" => "NotifyAboutTriageDecision",
            "sending_frequency" => "A cada resultado de triagem",
            "active" => true
        ])->first();

        if ($mailtemplate) {
            Mail::to($triage->application->email)->cc(env("MAIL_CEA"))->queue(new NotifyAboutTriageDecision($triage, $mailtemplate));
        }
    }
}
