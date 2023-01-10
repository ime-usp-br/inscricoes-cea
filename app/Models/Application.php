<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use app\Models\Semester;
use App\Models\Attachment;
use App\Models\DepositReceipt;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'semesterID',
        'protocol',
        'projectResponsible',
        'contactPhone',
        'cpf-cnpj',
        'email',
        'institution',
        'institutionRelationship',
        'mentor',
        'projectPurpose',
        'ppOther',
        'fundingAgency',
        'faOther',
        'knowledgeArea',
        'kaOther',
        'bdName',
        'bdCpfCnpj',
        'bdBankName',
        'bdAgency',
        'bdAccount',
        'bdType',
        'projectTitle',
        'generalAspects',
        'generalObjectives',
        'features',
        'otherFeatures',
        'limitations',
        'storage',
        'conclusions',
        'expectedHelp',
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
}
