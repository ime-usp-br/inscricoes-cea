<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBankSlipRequest;
use App\Http\Requests\UpdateBankSlipRequest;
use App\Models\BankSlip;

class BankSlipController extends Controller
{
    public function store($cpfCnpj, $nomeSacado, $email)
    {
        $codigoUnidadeDespesa = 1;
        $nomeFonte = 'Prestação de Serviços';
        $nomeSubfonte = 'Concurso Público';
        $codigoConvenio = '';
        $estruturaHierarquica ='\GR\CODAGE\DRH\PROCSELET';
        $dataVencimentoBoleto = date('Y-m-d', mktime(0,0,0,11,10,2012));
        $valorDesconto = 0;
        $valorDocumento = 80;
        $informacoesBoletoSacado = 'Referente ao pagamento da taxa de inscrição de XXX-2012<br>A inscrição só será efetivada após o pagamento deste boleto.<br>Dúvidas ou demais informações, entrar em contato com a organização do evento pelo e-mail<br>xxx@yyy.com';
        $instrucoesObjetoCobranca = 'Referente ao pagamento da taxa de inscrição de XXX-2012<br>Não receber após o vencimento';
        $tipoSacado = 'PF';
    }
}
