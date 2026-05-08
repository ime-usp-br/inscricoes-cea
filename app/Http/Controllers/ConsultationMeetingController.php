<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreConsultationMeetingRequest;
use App\Http\Requests\UpdateConsultationMeetingRequest;
use App\Http\Requests\RescheduleConsultationMeetingRequest;
use App\Http\Requests\InformDecisionConsultationMeetingRequest;
use App\Http\Requests\IndexConsultationMeetingRequest;
use App\Models\ConsultationMeeting;
use App\Models\Semester;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotifyAboutConsultationMeetingSchedule;
use App\Mail\NotifyAboutConsultationMeetingDecision;
use App\Models\Application;
use App\Models\MailTemplate;
use App\Models\BankSlip;
use App\Models\Event;
use Session;
use Auth;

class ConsultationMeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IndexConsultationMeetingRequest $request)
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

        $consultationmeetings = ConsultationMeeting::whereHas("application", function($query)use($semester){
            $query->whereBelongsTo($semester)->where("deleted", false);
        })->get();

        return view("consultationmeetings.index", compact(["semester", "consultationmeetings"]));
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
     * @param  \App\Http\Requests\StoreConsultationMeetingRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreConsultationMeetingRequest $request)
    {
        if(!Auth::check()){
            return redirect("/login");
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria"])){
            abort(403);
        }

        $validated = $request->validated();

        $application = Application::find($validated["applicationID"]);
        
        if($application->serviceType == "Projeto"){

            Session::flash("alert-warning", "Projetos não passam pela reunião de consulta.");
    
            return back();
        }

        $consultationmeeting = ConsultationMeeting::create($validated);

        $application->status = "Aguardando resultado da reunião de consulta";
        $application->save();


        $mailtemplate = MailTemplate::where([
            "mail_class"=>"NotifyAboutConsultationMeetingSchedule",
            "sending_frequency"=>"A cada agendamento de reunião de consulta",
            "active"=>true
            ])->first();

        if($mailtemplate){
            Mail::to($application->email)->cc(env("MAIL_CEA"))->queue(new NotifyAboutConsultationMeetingSchedule($consultationmeeting, $mailtemplate));
        }

        Session::flash("alert-success", "Reunião de consulta agendada com sucesso.");

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ConsultationMeeting  $consultationMeeting
     * @return \Illuminate\Http\Response
     */
    public function show(ConsultationMeeting $consultationMeeting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ConsultationMeeting  $consultationMeeting
     * @return \Illuminate\Http\Response
     */
    public function edit(ConsultationMeeting $consultationMeeting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateConsultationMeetingRequest  $request
     * @param  \App\Models\ConsultationMeeting  $consultationMeeting
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateConsultationMeetingRequest $request, ConsultationMeeting $consultationMeeting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ConsultationMeeting  $consultationMeeting
     * @return \Illuminate\Http\Response
     */
    public function destroy(ConsultationMeeting $consultationmeeting)
    {
        if(!Auth::check()){
            return redirect("/login");
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria"])){
            abort(403);
        }

        $consultationmeeting->application->status = "Aguardando agendamento da reunião de consulta";
        $consultationmeeting->application->save();
        $consultationmeeting->delete();

        $event = Event::create([
            'applicationID'=>$consultationmeeting->application->id,
            'name'=>'cancelamento consulta',
            'description'=>'Cancelamento de consulta',
            'event_date'=>date("Y-m-d H:i:s")
        ]);

        Session::flash("alert-success", "Reunião de consulta cancelada com sucesso.");

        return back();
    }

    public function reschedule(RescheduleConsultationMeetingRequest $request, ConsultationMeeting $consultationmeeting)
    {
        if(!Auth::check()){
            return redirect("/login");
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria"])){
            abort(403);
        }

        $validated = $request->validated();

        $consultationmeeting->link = null;
        $consultationmeeting->local = null;

        $consultationmeeting->update($validated);        


        $mailtemplate = MailTemplate::where([
            "mail_class"=>"NotifyAboutConsultationMeetingSchedule",
            "sending_frequency"=>"A cada reagendamento da reunião de consulta",
            "active"=>true
            ])->first();

        if($mailtemplate){
            Mail::to($consultationmeeting->application->email)->cc(env("MAIL_CEA"))->queue(new NotifyAboutConsultationMeetingSchedule($consultationmeeting, $mailtemplate));
        }

        $event = Event::create([
            'applicationID'=>$consultationmeeting->application->id,
            'name'=>'reagendamento consulta',
            'description'=>'Reagendamento de consulta',
            'event_date'=>date("Y-m-d H:i:s")
        ]);

        Session::flash("alert-success", "Reunião de consulta reagendada com sucesso.");

        return back();
        
    }

    public function informDecision(InformDecisionConsultationMeetingRequest $request, ConsultationMeeting $consultationmeeting)
    {
        if(!Auth::check()){
            return redirect("/login");
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria", "Docente"])){
            abort(403);
        }

        $validated = $request->validated();

        $consultationmeeting->update($validated);     

        $consultationmeeting->application->status = $consultationmeeting->decision;
        $consultationmeeting->application->save();

        if($consultationmeeting->decision == "Aprovado como projeto" and $consultationmeeting->application->getAggregatedProjectFeeStatus() != 'Pago'){
            $bankSlip = BankSlip::gerarBoletoRegistrado($consultationmeeting->application, 250.00, 0, "Taxa de Projeto");
            if ($bankSlip) {
                $consultationmeeting->application->projectFee()->save($bankSlip);
            }
        }

        $mailtemplate = MailTemplate::where([
            "mail_class"=>"NotifyAboutConsultationMeetingDecision",
            "sending_frequency"=>"A cada resultado de reunião de consulta",
            "active"=>true
            ])->first();

        if($mailtemplate){
            Mail::to($consultationmeeting->application->email)->cc(env("MAIL_CEA"))->queue(new NotifyAboutConsultationMeetingDecision($consultationmeeting, $mailtemplate));
        }

        Session::flash("alert-success", "Resultado da reunião de consulta cadastrado com sucesso.");

        return back();
    }

    public function updateFeedback(Request $request, ConsultationMeeting $consultationmeeting)
    {
        if(!Auth::check()){
            return response()->json([
                'status' => 'Precisa estar logado!',
            ]);
        }elseif(!Auth::user()->hasRole("Docente")){
            return response()->json([
                'status' => 'Usuário sem perfil!',
            ]);
        }

        $consultationmeeting->update([
            "feedback" => $request->get('valor')
        ]);

        return response()->json([
            'status' => 'Feedback alterado!'
        ]);
    }
}
