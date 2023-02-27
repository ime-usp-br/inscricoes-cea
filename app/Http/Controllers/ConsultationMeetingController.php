<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConsultationMeetingRequest;
use App\Http\Requests\UpdateConsultationMeetingRequest;
use App\Models\ConsultationMeeting;
use App\Models\Semester;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotifyAboutConsultationMeetingSchedule;
use App\Models\Application;
use App\Models\MailTemplate;
use Session;
use Auth;

class ConsultationMeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::check()){
            return redirect("/login");
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria", "Docente"])){
            abort(403);
        }

        $semester = Semester::getLatest();

        $consultationmeetings = ConsultationMeeting::whereHas("application", function($query)use($semester){
            $query->whereBelongsTo($semester);
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

        Session::flash("alert-success", "Reunião de consulta cancelada com sucesso.");

        return back();
    }
}
