<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\UpdateApplicationRequest;
use App\Http\Requests\IndexApplicationRequest;
use App\Http\Requests\DeletedIndexApplicationRequest;
use App\Mail\NotifyCEAAboutApplication;
use App\Mail\NotifyCEABoletoFailure;
use App\Mail\NotifyCEAAboutRefundReceipt;
use App\Mail\NotifyInscribedAboutApplication;
use App\Mail\NotifyUserNewBoleto;
use Illuminate\Support\Facades\Mail;
use Ismaelw\LaraTeX\LaraTeX;
use App\Models\Application;
use App\Models\Semester;
use App\Models\Attachment;
use App\Models\DepositReceipt;
use App\Models\MailTemplate;
use App\Models\BankSlip;
use App\Models\Event;
use GuzzleHttp\Client;
use Session;
use Auth;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IndexApplicationRequest $request)
    {
        if(!Auth::check()){
            return redirect("/login");
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria", "Docente"])){
            abort(403);
        }

        $validated = $request->validated();

        if(isset($validated['semester_id'])){
            $semester = Semester::find($validated['semester_id']);
        }else{
            $semester = Semester::getLatest();
        }

        $fichas = Application::whereBelongsTo($semester)->where("deleted", false)->get();

        return view("applications.index", compact(["semester", "fichas"]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreApplicationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreApplicationRequest $request)
    {
        $validated = $request->validated();

        if (env('APP_ENV') != 'local') {
            $client = new Client();

            $response = $client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => [
                    'secret' => env("GOOGLE_RECAPTCHA_SECRET"),
                    'response' => $validated["g-recaptcha-response"],
                ]
            ]);
            $body = (string) $response->getBody();
            $body = json_decode($body, true);

            if(!$body["success"]){
                Session::flash("alert-danger", "Falhou na validação do reCaptcha.");   
                return back();
            }
        }

        $semester = Semester::getLatest();

        if($validated["serviceType"] == "Projeto" and !$semester->IsEnrollmentPeriod()){
            Session::flash("alert-warning", "Fora do período de inscrição para projetos.");    
            return redirect("/");
        }

        $validated["semesterID"] = $semester->id;

        $validProtocol = False;

        while(!$validProtocol){
            $protocol = str_pad(random_int(1,999999999),9,"0",STR_PAD_LEFT);
            $validProtocol = Application::where("protocol", $protocol)->first() ? False : True;
        }

        $validated["protocol"] = $protocol;

        $validated["institutionRelationship"] = implode(",", $validated["institutionRelationship"]);
        $validated["projectPurpose"] = implode(",", $validated["projectPurpose"]);
        $validated["knowledgeArea"] = implode(",", $validated["knowledgeArea"]);
        if(array_key_exists("fundingAgency", $validated)){
            $validated["fundingAgency"] = implode(",", $validated["fundingAgency"]);
        }

        $application = Application::create($validated);

        $anexos = $validated["anexosNovos"] ?? [];
        unset($validated["anexosNovos"]);

        foreach($anexos as $anexo){
            $attachment  = new Attachment;
            
            $attachment->name = $anexo["arquivo"]->getClientOriginalName();
            $attachment->path = $anexo["arquivo"]->store($protocol);

            $application->attachments()->save($attachment);

            $attachment->link = route("attachments.download",$attachment);
            $attachment->save();
        }

        if($application->serviceType == "Projeto"){
            $application->status = "Aguardando agendamento da triagem";
        }elseif($application->serviceType == "Consulta"){
            $application->status = "Aguardando agendamento da reunião de consulta";
        }

        try {
            if($application->serviceType == 'Consulta'){
                // Format as string to ensure SOAP compatibility
                $bankSlip = BankSlip::gerarBoletoRegistrado($application, '140.00', 0, "Taxa de Inscrição");
            }else{
                $bankSlip = BankSlip::gerarBoletoRegistrado($application, '80.00', 0, "Taxa de Inscrição");
            }

            if ($bankSlip) {
                $application->applicationFee()->save($bankSlip);
            } else {
                // Fault Tolerance: Notify CEA about the failure so they can regenerate later
                Mail::to(env("MAIL_CEA"))->send(new NotifyCEABoletoFailure($application));
            }
        } catch (\Throwable $e) {
            // Check if it's the specific NuSOAP array to string conversion error or any other crash
            \Log::error("Critical Error generating bank slip for application {$application->id}: " . $e->getMessage());
            
            // Still notify admin so they know manual intervention is needed
            try {
                Mail::to(env("MAIL_CEA"))->send(new NotifyCEABoletoFailure($application));
            } catch (\Exception $mailException) {
                \Log::error("Failed to send failure notification email: " . $mailException->getMessage());
            }
        }

        $application->save();

        $mailtemplate = MailTemplate::where([
            "mail_class"=>"NotifyCEAAboutApplication",
            "sending_frequency"=>"A cada inscrição",
            "active"=>true
            ])->first();

        if($mailtemplate){
            Mail::to(env("MAIL_CEA"))->queue(new NotifyCEAAboutApplication($application, $mailtemplate));
        }

        $mailtemplate = MailTemplate::where([
            "mail_class"=>"NotifyInscribedAboutApplication",
            "sending_frequency"=>"A cada inscrição",
            "active"=>true
            ])->first();

        if($mailtemplate){
            Mail::to($application->email)->queue(new NotifyInscribedAboutApplication($application, $mailtemplate));
        }

        if($application->refundReceipt == "Sim"){
            $mailtemplate = MailTemplate::where([
                "mail_class"=>"NotifyCEAAboutRefundReceipt",
                "sending_frequency"=>"A cada inscrição",
                "active"=>true
                ])->first();
    
            if($mailtemplate){
                Mail::to(env("MAIL_CEA"))->queue(new NotifyCEAAboutRefundReceipt($application, $mailtemplate));
            }
        }

        $event = Event::create([
            'applicationID'=>$application->id,
            'name'=>'Inscrição',
            'description'=>'Inscrição Realizada',
            'event_date'=>$application->created_at
        ]);

        Session::flash("alert-success", "Sua inscrição foi efetuada com sucesso! Seu número de protocolo é ".$protocol.".");

        return redirect("/");

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Http\Response
     */
    public function show(Application $application)
    {
        if(!Auth::check()){
            return redirect("/login");
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria", "Docente"])){
            abort(403);
        }

        return view("applications.show", compact("application"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Http\Response
     */
    public function edit(Application $application)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateApplicationRequest  $request
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateApplicationRequest $request, Application $application)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Http\Response
     */
    public function destroy(Application $application)
    {
        if(!Auth::check()){
            return redirect("/login");
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria", "Docente"])){
            abort(403);
        }

        $application->deleted = true;
        $application->save();

        Session::flash("alert-success", "A inscrição de protocolo ".$application->protocol." foi excluida com sucesso.");

        return back();
    }

    public function deleted_index()
    {
        if(!Auth::check()){
            return redirect("/login");
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria", "Docente"])){
            abort(403);
        }

        $semester = Semester::getLatest();

        $fichas = Application::whereBelongsTo($semester)->where("deleted", true)->get();

        return view("applications.deleted", compact(["semester", "fichas"]));
    }

    public function restore(Application $application)
    {
        if(!Auth::check()){
            return redirect("/login");
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria", "Docente"])){
            abort(403);
        }

        $application->deleted = false;
        $application->save();

        Session::flash("alert-success", "A inscrição de protocolo ".$application->protocol." foi restaurada com sucesso.");

        return back();

    }

    public function downloadAsPDF($protocol)
    {
        if(!Auth::check()){
            return redirect("/login");
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria", "Docente"])){
            abort(403);
        }
        
        $application = Application::where("protocol", $protocol)->first();


        return (new LaraTeX('applications.latex'))->with([
            'application' => $application,
        ])->download($protocol.'.pdf');

    }

    public function downloadFirstPageAsPDF($protocol)
    {
        if(!Auth::check()){
            return redirect("/login");
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria"])){
            abort(403);
        }
        
        $application = Application::where("protocol", $protocol)->first();


        return (new LaraTeX('applications.latexfirstpage'))->with([
            'application' => $application,
        ])->download($protocol.'.pdf');

    }

    public function changeServiceType(Application $application)
    {
        if(!Auth::check()){
            return redirect("/login");
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria"])){
            abort(403);
        }

        if($application->serviceType == "Projeto"){
            // Mudança de Projeto para Consulta
            $application->serviceType = "Consulta";
            $application->status = "Aguardando agendamento da reunião de consulta";

            // Lógica de Boletos
            $boletoToSend = null;
            $currentFee = $application->applicationFee; // Busca "Taxa de Inscrição"

            if ($currentFee) {
                // Tenta atualizar status para ter certeza
                try {
                    $currentFee->atualizarSituacao();
                } catch (\Exception $e) {
                    \Log::error("Erro ao atualizar boleto na troca de serviço: " . $e->getMessage());
                }

                if ($currentFee->statusBoletoBancario == 'P') {
                    // PAGO: Gerar Complemento de R$ 60,00 (140 - 80)
                    if (!$application->complementaryFee) {
                        $boletoToSend = BankSlip::gerarBoletoRegistrado($application, '60.00', 0, "Complemento de Taxa");
                        if ($boletoToSend) {
                            $application->complementaryFee()->save($boletoToSend);
                        } else {
                            Session::flash("alert-warning", "Atenção: Falha ao gerar boleto de complemento de taxa via sistema bancário.");
                        }
                    } else {
                        // Se já existir, usamos o existente (evita duplicidade se clicarem duas vezes)
                        $boletoToSend = $application->complementaryFee;
                    }
                } else {
                    // NÃO PAGO (ou Cancelado/Verificar/Emitido): Gerar nova Taxa Cheia R$ 140,00
                    
                    // Marca o antigo como substituído para liberar a relação hasOne 'applicationFee'
                    $currentFee->relativoA = "Taxa de Inscrição (Substituído)";
                    $currentFee->save();

                    // Tenta cancelar o antigo no banco (best effort)
                    try {
                        if($currentFee->statusBoletoBancario == 'E') {
                            $currentFee->cancelarBoleto();
                        }
                    } catch (\Exception $e) {
                        \Log::warning("Não foi possível cancelar o boleto antigo ID {$currentFee->id}");
                    }

                    $boletoToSend = BankSlip::gerarBoletoRegistrado($application, '140.00', 0, "Taxa de Inscrição");
                    if ($boletoToSend) {
                        $application->applicationFee()->save($boletoToSend);
                    } else {
                        Session::flash("alert-warning", "Atenção: Falha ao gerar novo boleto de Taxa de Inscrição via sistema bancário.");
                    }
                }
            } else {
                // Não havia boleto (ex: erro na inscrição inicial). Gera o de R$ 140.
                $boletoToSend = BankSlip::gerarBoletoRegistrado($application, '140.00', 0, "Taxa de Inscrição");
                if ($boletoToSend) {
                    $application->applicationFee()->save($boletoToSend);
                } else {
                     Session::flash("alert-warning", "Atenção: Falha ao gerar boleto via sistema bancário.");
                }
            }

            // Enviar E-mail
            try {
                if($application->email) {
                    Mail::to($application->email)->queue(new \App\Mail\NotifyServiceChange($application, $boletoToSend));
                }
            } catch (\Exception $e) {
                \Log::error("Erro ao enviar email de mudança de serviço: " . $e->getMessage());
                Session::flash("alert-warning", "Mudança realizada, mas falha ao enviar e-mail de notificação.");
            }
            
            Session::flash("alert-success", "Modalidade alterada para Consulta. Processo de ajuste de taxas realizado.");

        }elseif($application->serviceType == "Consulta"){
            $application->serviceType = "Projeto";
            $application->status = "Aguardando agendamento da triagem";
             Session::flash("alert-success", "Modalidade alterada para Projeto.");
        }

        $application->save();

        return back();

    }
    public function regenerateBoleto(Application $application)
    {
        if(!Auth::check()){
            return redirect("/login");
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria"])){
            abort(403);
        }

        if ($application->applicationFee) {
            return redirect()->back()->withErrors(['Este boleto já foi gerado.']);
        }

        try {
            if($application->serviceType == 'Consulta'){
                // Format as string to ensure SOAP compatibility
                // Format as string to ensure SOAP compatibility
                $bankSlip = BankSlip::gerarBoletoRegistrado($application, '140.00', 0, "Taxa de Inscrição");
            }else{
                $bankSlip = BankSlip::gerarBoletoRegistrado($application, '80.00', 0, "Taxa de Inscrição");
            }

            if ($bankSlip) {
                $application->applicationFee()->save($bankSlip);
                
                // Notify user about the new boleto
                Mail::to($application->email)->queue(new NotifyUserNewBoleto($application));

                return redirect()->back()->with('success', 'Boleto gerado com sucesso e enviado ao usuário.');
            } else {
                return redirect()->back()->withErrors(['Falha ao gerar o boleto via SOAP (Retornou Falso). Tente novamente mais tarde.']);
            }
        } catch (\Throwable $e) {
            \Log::error("Critical Error regenerating bank slip: " . $e->getMessage());
            return redirect()->back()->withErrors(['Erro crítico ao gerar boleto: ' . $e->getMessage()]);
        }
    }
    public function downloadBoleto(BankSlip $bankSlip)
    {
        if(!Auth::check()){
            return redirect("/login");
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria", "Docente"])){
            abort(403);
        }

        $pdfContent = $bankSlip->obterBoletoPDF();
        
        if (!$pdfContent) {
            return back()->withErrors(['Falha ao obter PDF do boleto. O serviço pode estar indisponível.']);
        }

        // The SOAP service usually returns base64.
        $pdf = base64_decode($pdfContent);

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="boleto_'.$bankSlip->id.'.pdf"');
    }
}
