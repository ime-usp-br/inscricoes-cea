<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Semester;
use App\Models\Attachment;
use App\Models\DepositReceipt;
use App\Models\BankSlip;
use App\Models\Event;
use App\Models\Triage;
use App\Models\ConsultationMeeting;

class Application extends Model
{
    use HasFactory;

    protected $casts = [
        'transfer_pending' => 'boolean',
    ];

    protected $fillable = [
        'semesterID',
        'protocol',
        'serviceType',
        'status',
        'projectResponsible',
        'contactPhone',
        'CPFCNPJ',
        'email',
        'institution',
        'course',
        'institutionRelationship',
        'irOther',
        'mentor',
        'projectPurpose',
        'ppOther',
        'fundingAgency',
        'faOther',
        'knowledgeArea',
        'kaOther',
        'refundReceipt',
        'refundReceiptData',
        'bdName',
        'bdCpfCnpj',
        'bdBankName',
        'bdAgency',
        'bdAccount',
        'bdType',
        'dataCollect',
        'projectTitle',
        'generalAspects',
        'generalObjectives',
        'features',
        'otherFeatures',
        'limitations',
        'storage',
        'conclusions',
        'expectedHelp',
        'deleted',
        'whatsapp',
        'transfer_pending',
    ];

    public function semester()
    {
        return $this->belongsTo(Semester::class, "semesterID");
    }

    public function depositReceipt()
    {
        return $this->hasOne(DepositReceipt::class, "applicationID");
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, "applicationID");
    }

    public function applicationFee()
    {
        return $this->hasOne(BankSlip::class, "applicationID")
            ->where("relativoA", "Taxa de Inscrição");
    }

    public function projectFee()
    {
        return $this->hasOne(BankSlip::class, "applicationID")
            ->where("relativoA", "Taxa de Projeto");
    }

    public function complementaryFee()
    {
        return $this->hasOne(BankSlip::class, "applicationID")
            ->where("relativoA", "Complemento de Taxa");
    }

    // New Relationships to access ALL boletos
    public function allApplicationFees()
    {
        return $this->hasMany(BankSlip::class, "applicationID")
            ->whereIn("relativoA", ["Taxa de Inscrição", "Taxa de Inscrição (Substituído)"])
            ->orderBy('id', 'desc');
    }

    public function allProjectFees()
    {
        return $this->hasMany(BankSlip::class, "applicationID")
            ->whereIn("relativoA", ["Taxa de Projeto", "Taxa de Projeto (Substituído)"])
            ->orderBy('id', 'desc');
    }

    // Aggregated Status Logic
    public function getAggregatedInscriptionFeeStatus()
    {
        $fees = $this->allApplicationFees;
        if ($fees->isEmpty()) return "Não Emitido";

        // 1. Priority: Paid
        if ($fees->contains('statusBoletoBancario', 'P')) return "Pago";

        // 2. Priority: Issued (Valid and Not Expired)
        // Check if there is any boleto that is 'E' (Emitido) AND not expired
        foreach ($fees as $fee) {
            if ($fee->statusBoletoBancario == 'E') {
                $dueDate = \Carbon\Carbon::createFromFormat('d/m/Y', $fee->dataVencimentoBoleto);
                if ($dueDate->isFuture() || $dueDate->isToday()) {
                    return "Emitido";
                }
            }
        }

        // 3. Fallback: Return status of the latest one
        return $fees->first()->getStatus();
    }

    public function getAggregatedProjectFeeStatus()
    {
        $fees = $this->allProjectFees;
        if ($fees->isEmpty()) return "Não Emitido";

        if ($fees->contains('statusBoletoBancario', 'P')) return "Pago";

        foreach ($fees as $fee) {
            if ($fee->statusBoletoBancario == 'E') {
                 // Assuming date format d/m/Y stored as string in DB for legacy reasons or date cast? 
                 // BankSlip uses date("d/m/Y") to set it.
                $dueDate = \Carbon\Carbon::createFromFormat('d/m/Y', $fee->dataVencimentoBoleto);
                if ($dueDate->isFuture() || $dueDate->isToday()) {
                    return "Emitido";
                }
            }
        }

        return $fees->first()->getStatus();
    }

    public function events()
    {
        return $this->hasMany(Event::class, "applicationID");
    }

    public function triage()
    {
        return $this->hasOne(Triage::class, "applicationID");
    }

    public function consultationMeeting()
    {
        return $this->hasOne(ConsultationMeeting::class, "applicationID");
    }
}
