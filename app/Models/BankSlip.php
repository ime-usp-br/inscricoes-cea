<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankSlip extends Model
{
    use HasFactory;

    protected $fillable = [
        'controle',
        'applicationID',
        'codigoUnidadeDespesa',
        'nomeFonte',
        'nomeSubfonte',
        'estruturaHierarquica',
        'codigoConvenio',
        'dataVencimentoBoleto',
        'valorDocumento',
        'valorDesconto',
        'tipoSacado',
        'cpfCnpj',
        'nomeSacado',
        'codigoEmail',
        'informacoesBoletoSacado',
        'instrucoesObjetoCobranca',
        'codigoIDBoleto',
        'instituicao',
        'telefone',
        'dataInscricao',
    ];
}
