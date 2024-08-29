<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Semester;
use App\Models\Attachment;
use App\Models\DepositReceipt;
use App\Models\BankSlip;

class Application extends Model
{
    use HasFactory;

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
}
