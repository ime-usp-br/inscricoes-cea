<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\UpdateApplicationRequest;
use App\Models\Application;
use App\Models\Semester;
use App\Models\Attachment;
use App\Models\DepositReceipt;
use Session;
use Auth;

class ApplicationController extends Controller
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

        $fichas = Application::whereBelongsTo($semester)->get();

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

        $semester = Semester::getInEnrollmentPeriod();

        $validated["semesterID"] = $semester->id;

        $validProtocol = False;

        while(!$validProtocol){
            $protocol = str_pad(random_int(1,999999999),9,"0",STR_PAD_LEFT);
            $validProtocol = Application::where("protocol", $protocol)->first() ? False : True;
        }

        $validated["protocol"] = $protocol;

        $validated["projectPurpose"] = implode(",", $validated["projectPurpose"]);
        $validated["knowledgeArea"] = implode(",", $validated["knowledgeArea"]);
        $validated["fundingAgency"] = implode(",", $validated["fundingAgency"]);

        $application = Application::create($validated);

        $receipt  = new DepositReceipt;
            
        $receipt->name = $validated["paymentVoucher"]->getClientOriginalName();
        $receipt->path = $validated["paymentVoucher"]->store($protocol);

        $application->depositReceipt()->save($receipt);

        $receipt->link = route("receipts.download",$receipt);
        $receipt->save();

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
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria"])){
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
        //
    }
}
