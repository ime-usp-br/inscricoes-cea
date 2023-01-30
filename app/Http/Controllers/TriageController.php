<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTriageRequest;
use App\Http\Requests\UpdateTriageRequest;
use App\Http\Requests\RescheduleTriageRequest;
use App\Http\Requests\InformDecisionTriageRequest;
use App\Mail\NotifyAboutTriageSchedule;
use App\Mail\NotifyAboutTriageDecision;
use Illuminate\Support\Facades\Mail;
use App\Models\Semester;
use App\Models\Triage;
use App\Models\Application;
use App\Models\MailTemplate;
use Session;
use Auth;

class TriageController extends Controller
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
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria"])){
            abort(403);
        }

        $semester = Semester::getLatest();

        $triagens = Triage::whereHas("application", function($query)use($semester){
            $query->whereBelongsTo($semester);
        })->get();

        return view("triages.index", compact(["semester", "triagens"]));
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
     * @param  \App\Http\Requests\StoreTriageRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTriageRequest $request)
    {
        if(!Auth::check()){
            return redirect("/login");
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria"])){
            abort(403);
        }

        $validated = $request->validated();
        
        $triage = Triage::create($validated);

        $application = Application::find($validated["applicationID"]);
        $application->status = "Aguardando resultado da triagem";
        $application->save();


        $mailtemplate = MailTemplate::where([
            "mail_class"=>"NotifyAboutTriageSchedule",
            "sending_frequency"=>"A cada agendamento de triagem",
            "active"=>true
            ])->first();

        if($mailtemplate){
            Mail::to($application->email)->cc(env("MAIL_CEA"))->queue(new NotifyAboutTriageSchedule($triage, $mailtemplate));
        }

        Session::flash("alert-success", "Triagem agendada com sucesso.");

        return back();

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Triage  $triage
     * @return \Illuminate\Http\Response
     */
    public function show(Triage $triage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Triage  $triage
     * @return \Illuminate\Http\Response
     */
    public function edit(Triage $triage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTriageRequest  $request
     * @param  \App\Models\Triage  $triage
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTriageRequest $request, Triage $triage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Triage  $triage
     * @return \Illuminate\Http\Response
     */
    public function destroy(Triage $triage)
    {
        //
    }

    public function reschedule(RescheduleTriageRequest $request, Triage $triage)
    {
        if(!Auth::check()){
            return redirect("/login");
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria"])){
            abort(403);
        }

        $validated = $request->validated();

        $triage->link = null;
        $triage->local = null;

        $triage->update($validated);        


        $mailtemplate = MailTemplate::where([
            "mail_class"=>"NotifyAboutTriageSchedule",
            "sending_frequency"=>"A cada reagendamento de triagem",
            "active"=>true
            ])->first();

        if($mailtemplate){
            Mail::to($triage->application->email)->cc(env("MAIL_CEA"))->queue(new NotifyAboutTriageSchedule($triage, $mailtemplate));
        }

        Session::flash("alert-success", "Triagem reagendada com sucesso.");

        return back();
        
    }

    public function informDecision(InformDecisionTriageRequest $request, Triage $triage)
    {
        if(!Auth::check()){
            return redirect("/login");
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria"])){
            abort(403);
        }

        $validated = $request->validated();

        $triage->update($validated);     

        $triage->application->status = $triage->decision;
        $triage->application->save();

        $mailtemplate = MailTemplate::where([
            "mail_class"=>"NotifyAboutTriageDecision",
            "sending_frequency"=>"A cada resultado de triagem",
            "active"=>true
            ])->first();

        if($mailtemplate){
            Mail::to($triage->application->email)->cc(env("MAIL_CEA"))->queue(new NotifyAboutTriageDecision($triage, $mailtemplate));
        }

        Session::flash("alert-success", "Resultado da triagem cadastrado com sucesso.");

        return back();
    }
}
