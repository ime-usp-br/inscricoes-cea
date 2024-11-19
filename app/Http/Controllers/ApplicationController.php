<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\UpdateApplicationRequest;
use App\Http\Requests\IndexApplicationRequest;
use App\Http\Requests\DeletedIndexApplicationRequest;
use App\Mail\NotifyCEAAboutApplication;
use App\Mail\NotifyCEAAboutRefundReceipt;
use App\Mail\NotifyInscribedAboutApplication;
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

        $bankSlip = BankSlip::gerarBoletoRegistrado($application, 80.00, 0, "Taxa de Inscrição");
        $application->applicationFee()->save($bankSlip);

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
            $application->serviceType = "Consulta";
            $application->status = "Aguardando agendamento da reunião de consulta";
        }elseif($application->serviceType == "Consulta"){
            $application->serviceType = "Projeto";
            $application->status = "Aguardando agendamento da triagem";
        }

        $application->save();

        return back();

    }
}
