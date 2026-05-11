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
        'manual_payment_confirmed',
        'manual_payment_confirmed_at',
        'manual_payment_confirmed_by',
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

        if (env('APP_ENV') === 'local') {
            $informacoesBoletoSacado = '[TESTE - NÃO PAGAR] ' . $informacoesBoletoSacado;
            $instrucoesObjetoCobranca = '[TESTE - NÃO PAGAR] ' . $instrucoesObjetoCobranca;
        }

        // Create client with caching disabled for debugging if necessary
        $clienteSoap = new nusoap_client(env("WSDL_URL"), 'wsdl');
        // $clienteSoap->soap_defencoding = 'UTF-8';
        // $clienteSoap->decode_utf8 = false;

        $erro = $clienteSoap->getError();
        if ($erro) {
            Log::error("Erro de conexão com o serviço WS-Boleto: " . $erro);
            return false;
        }

        $soapHeaders = array('username' => env("WS_USERNAME"), 'password' => env("WS_PASSWORD"));
        $clienteSoap->setHeaders($soapHeaders);

        $cpfCnpj = str_replace(array('.', '-', '/'), "", $app->bdCpfCnpj);
        $tipo_sacado = strlen($cpfCnpj) == 14 ? "PJ" : "PF";

        $param = array(
            'codigoUnidadeDespesa' => $codigoUnidadeDespesa,
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

        // Logging request parameters for debugging (excluding sensitive credentials if any)
        Log::info("Tentando gerar boleto para aplicação {$app->id}. Params: " . json_encode($param));

        $result = $clienteSoap->call('gerarBoletoRegistrado', array('boletoRegistrado' => $param));

        if ($clienteSoap->fault) {
            Log::error("Falha no cliente SOAP (Fault).");
            Log::error("Fault content: " . print_r($result, true));
            Log::debug("SOAP Request: " . $clienteSoap->request);
            Log::debug("SOAP Response: " . $clienteSoap->response);
            Log::debug("NuSOAP Debug: " . $clienteSoap->getDebug());
            return false;
        }

        if ($clienteSoap->getError()) {
            Log::error("Erro no cliente SOAP: " . $clienteSoap->getError());
            Log::debug("SOAP Request: " . $clienteSoap->request);
            Log::debug("SOAP Response: " . $clienteSoap->response);
            Log::debug("NuSOAP Debug: " . $clienteSoap->getDebug());
            return false;
        }

        Log::info("Boleto gerado com sucesso. ID: " . ($result["identificacao"]["codigoIDBoleto"] ?? 'N/A'));

        $boleto = BankSlip::create([
            "codigoIDBoleto" => $result["identificacao"]["codigoIDBoleto"],
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
        if (empty($this->codigoIDBoleto)) {
            return false;
        }

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
