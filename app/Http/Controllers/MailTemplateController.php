<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMailTemplateRequest;
use App\Http\Requests\UpdateMailTemplateRequest;
use App\Http\Requests\TestMailTemplateRequest;
use App\Models\MailTemplate;
use Auth;
use Session;

class MailTemplateController extends Controller
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

        $mailtemplates = MailTemplate::all();

        return view("mailtemplates.index", compact("mailtemplates"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::check()){
            return redirect("/login");
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria"])){
            abort(403);
        }

        $mailtemplate = new MailTemplate;

        return view("mailtemplates.create", compact("mailtemplate"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreMailTemplateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMailTemplateRequest $request)
    {
        if(!Auth::check()){
            return redirect("/login");
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria"])){
            abort(403);
        }
        
        $validated = $request->validated();

        $description_and_class_name = json_decode($validated["description_and_mail_class"]);
        $validated["description"] = $description_and_class_name->description;
        $validated["mail_class"] = $description_and_class_name->mail_class;
        unset($validated["description_and_mail_class"]);
        
        if(MailTemplate::where("name",$validated["name"])->exists()){
            Session::flash('alert-warning', 'Já existe um modelo com esse nome.');
            return back();
        }

        MailTemplate::create($validated);

        return redirect("mailtemplates");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MailTemplate  $mailTemplate
     * @return \Illuminate\Http\Response
     */
    public function show(MailTemplate $mailtemplate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MailTemplate  $mailTemplate
     * @return \Illuminate\Http\Response
     */
    public function edit(MailTemplate $mailtemplate)
    {
        if(!Auth::check()){
            return redirect("/login");
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria"])){
            abort(403);
        }
        
        return view("mailtemplates.edit", compact("mailtemplate"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMailTemplateRequest  $request
     * @param  \App\Models\MailTemplate  $mailTemplate
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMailTemplateRequest $request, MailTemplate $mailtemplate)
    {
        if(!Auth::check()){
            return redirect("/login");
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria"])){
            abort(403);
        }
        
        $validated = $request->validated();

        $description_and_class_name = json_decode($validated["description_and_mail_class"]);
        $validated["description"] = $description_and_class_name->description;
        $validated["mail_class"] = $description_and_class_name->mail_class;
        unset($validated["description_and_mail_class"]);
        
        if(MailTemplate::where("name",$validated["name"])->where("id", "!=", $mailtemplate->id)->exists()){
            Session::flash('alert-warning', 'Já existe um modelo com esse nome.');
            return back();
        }
        
        if(MailTemplate::where("mail_class", $validated["mail_class"])
                ->where("id","!=", $mailtemplate->id)
                ->where("active",true)->where("sending_frequency", "Manual")->exists() and
                $validated["sending_frequency"] == "Manual"){
            Session::flash('alert-warning', 'Já existe um modelo ativo com essa aplicação para disparo manual.');
            return back();
        }

        if($validated["sending_frequency"]=="Manual"){
            $validated["sending_date"] = null;
            $validated["sending_hour"] = null;
        }

        $mailtemplate->update($validated);

        return redirect("mailtemplates");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MailTemplate  $mailTemplate
     * @return \Illuminate\Http\Response
     */
    public function destroy(MailTemplate $mailtemplate)
    {
        if(!Auth::check()){
            return redirect("/login");
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria"])){
            abort(403);
        }

        $mailtemplate->delete();

        return back();
    }

    public function activate(MailTemplate $mailtemplate)
    {
        if(!Auth::check()){
            return redirect("/login");
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria"])){
            abort(403);
        }
        
        if(MailTemplate::where("mail_class", $mailtemplate->mail_class)
                ->where("id","!=", $mailtemplate->id)
                ->where("active",true)->where("sending_frequency", "Manual")->exists() and
                $mailtemplate->sending_frequency == "Manual"){
            Session::flash('alert-warning', 'Já existe um modelo ativo com essa aplicação para disparo manual.');
            return back();
        }
        
        $mailtemplate->active = true;
        $mailtemplate->save();

        return back();
    }

    public function deactivate(MailTemplate $mailtemplate)
    {
        if(!Auth::check()){
            return redirect("/login");
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria"])){
            abort(403);
        }

        $mailtemplate->active = false;
        $mailtemplate->save();


        return back();
    }

    public function test(TestMailTemplateRequest $request)
    {
        //
    }
}
