<?php

namespace App\Models;

if(!class_exists('nusoap_client')) {
    require_once(base_path('app/Http/SoapClient/nusoap.php'));
}

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Models\Application;
use App\Models\Triage;
use nusoap_client;

class BankSlip extends Model
{
    use HasFactory;

    protected $fillable = [
        'applicationID',
        'relativoA',
        'codigoIDBoleto',
        'dataVencimentoBoleto',
        'dataEfetivaPagamento',
        'valorDocumento',
        'valorDesconto',
        'valorEfetivamentePago',
        'statusBoletoBancario',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class, "applicationID");
    }

    public function getStatus($atualizar = false)
    {
        $estados = [
            "E"=>"Emitido",
            "P"=>"Pago",
            "V"=>"Verificar",
            "C"=>"Cancelado"
        ];
        if($atualizar){
            $this->atualizarSituacao();
        }

        return $estados[$this->statusBoletoBancario];
    }

    public static function gerarBoletoRegistrado(Application $app, $valorDocumento, $valorDesconto, $relativoA)
    {
        $codigoUnidadeDespesa = 45;
        $codigoFonteRecurso = 770;
        $estruturaHierarquica ='\DIRETORIA\MAE\CEA';
        $informacoesBoletoSacado = 'Dúvidas ou demais informações, entrar em contato com o CEA pelo e-mail cea@ime.usp.br';
        $instrucoesObjetoCobranca = 'Não receber após o vencimento';

        $clienteSoap = new nusoap_client(env("WSDL_URL"),'wsdl');

        $erro = $clienteSoap->getError();
        if ($erro){
            Log::error("Erro de conexão com o serviço WS-Boleto.");
            return false;
        }
        
        $soapHeaders = array('username' => env("WS_USERNAME"), 'password' => env("WS_PASSWORD")); 
        $clienteSoap->setHeaders($soapHeaders);

        $cpfCnpj = str_replace(array('.','-','/'), "", $app->bdCpfCnpj);
        $tipo_sacado = strlen($cpfCnpj) == 14 ? "PJ" : "PF";
        
        $param = array ('codigoUnidadeDespesa' => $codigoUnidadeDespesa,
                        'codigoFonteRecurso' => $codigoFonteRecurso,
                        'estruturaHierarquica' => $estruturaHierarquica,
                        'dataVencimentoBoleto' => date("d/m/Y", strtotime("+3 days")),
                        'valorDocumento' => $valorDocumento,
                        'valorDesconto' => $valorDesconto,
                        'tipoSacado' => $tipo_sacado,
                        'cpfCnpj' => $cpfCnpj,
                        'nomeSacado' => utf8_decode($app->bdName),
                        'informacoesBoletoSacado' => utf8_decode($informacoesBoletoSacado),
                        'instrucoesObjetoCobranca' => utf8_decode($instrucoesObjetoCobranca)
                    );


        $result = $clienteSoap->call('gerarBoletoRegistrado', array('boletoRegistrado' => $param));
        
        if ($clienteSoap->fault) { 
            Log::error("Falha no cliente - Geração Código.");
            return false;
        } 
        if ($clienteSoap->getError()){    
            Log::error( $clienteSoap->getError());
            return false;
        }

        $boleto = BankSlip::create([
            "codigoIDBoleto"=>$result["identificacao"]["codigoIDBoleto"],
            'valorDocumento' => $valorDocumento,
            'valorDesconto' => $valorDesconto,
            'relativoA' => $relativoA,
        ]);

        $boleto->atualizarSituacao();
        
        return $boleto;
    }

    public function obterBoletoPDF()
    {
        $clienteSoap = new nusoap_client(env("WSDL_URL"),'wsdl');

        $erro = $clienteSoap->getError();
        if ($erro){
            Log::error("Erro de conexão com o serviço WS-Boleto.");
            return false;
        }
        
        $soapHeaders = array('username' => env("WS_USERNAME"), 'password' => env("WS_PASSWORD")); 
        $clienteSoap->setHeaders($soapHeaders);

        
        $param = array ('codigoIDBoleto' => $this->codigoIDBoleto);

        $result = $clienteSoap->call('obterBoleto', array('identificacao' => $param));
        
        if ($clienteSoap->fault) { 
            Log::error("Falha no cliente - Geração Código.");
            return false;
        } 
        if ($clienteSoap->getError()){    
            Log::error( $clienteSoap->getError());
            return false;
        }

	    return $result["boletoPDF"];
    }

    public function atualizarSituacao()
    {
        $clienteSoap = new nusoap_client(env("WSDL_URL"),'wsdl');

        $erro = $clienteSoap->getError();
        if ($erro){
            Log::error("Erro de conexão com o serviço WS-Boleto.");
            return false;
        }
        
        $soapHeaders = array('username' => env("WS_USERNAME"), 'password' => env("WS_PASSWORD")); 
        $clienteSoap->setHeaders($soapHeaders);

        
        $param = array ('codigoIDBoleto' => $this->codigoIDBoleto);

        $result = $clienteSoap->call('obterSituacao', array('identificacao' => $param));
        
        if ($clienteSoap->fault) { 
            Log::error("Falha no cliente - Geração Código.");
            return false;
        } 
        if ($clienteSoap->getError()){    
            Log::error( $clienteSoap->getError());
            return false;
        }

        $this->update([
            'dataVencimentoBoleto'=>$result["situacao"]["dataVencimentoBoleto"],
            'dataEfetivaPagamento'=>$result["situacao"]["dataEfetivaPagamento"],
            'valorEfetivamentePago'=>$result["situacao"]["valorEfetivamentePago"],
            'statusBoletoBancario'=>$result["situacao"]["statusBoletoBancario"],
        ]);

	    return true;
    }

    public function cancelarBoleto()
    {
        $clienteSoap = new nusoap_client(env("WSDL_URL"),'wsdl');

        $erro = $clienteSoap->getError();
        if ($erro){
            Log::error("Erro de conexão com o serviço WS-Boleto.");
            return false;
        }
        
        $soapHeaders = array('username' => env("WS_USERNAME"), 'password' => env("WS_PASSWORD")); 
        $clienteSoap->setHeaders($soapHeaders);

        
        $param = array ('codigoIDBoleto' => $this->codigoIDBoleto);

        $result = $clienteSoap->call('cancelarBoleto', array('identificacao' => $param));
        
        if ($clienteSoap->fault) { 
            Log::error("Falha no cliente - Geração Código.");
            return false;
        } 
        if ($clienteSoap->getError()){    
            Log::error( $clienteSoap->getError());
            return false;
        }

        if($result["situacao"]["statusBoletoBancario"] == "C"){
            return true;
        }else{
            Log::error("Falha ao cancelar boleto - call retornou status diferente de C");
            return false;
        }
    }
}
