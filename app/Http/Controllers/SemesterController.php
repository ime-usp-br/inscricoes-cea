<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSemesterRequest;
use App\Http\Requests\UpdateSemesterRequest;
use App\Models\Semester;
use App\Models\Application;
use App\Models\Event;
use Auth;
use Illuminate\Support\Facades\DB;

class SemesterController extends Controller
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

        $periodos = Semester::all()->sortBy(["year","period"])->reverse();

        return view('semesters.index', compact('periodos'));
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

        $periodo = new Semester;

        return view('semesters.create', compact('periodo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSemesterRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSemesterRequest $request)
    {
        if(!Auth::check()){
            return redirect("/login");
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria"])){
            abort(403);
        }

        $validated = $request->validated();

        $periodo = Semester::updateOrCreate(['year'=>$validated['year'], 'period'=>$validated['period']],$validated);

        if ($periodo->wasRecentlyCreated) {
            $this->migratePendingApplications($periodo);
        }

        return redirect('/semesters');
    }

    private function migratePendingApplications(Semester $newSemester)
    {
        DB::transaction(function () use ($newSemester) {
            $applications = Application::where('transfer_pending', true)
                ->where('deleted', false)
                ->lockForUpdate()
                ->get();

            foreach ($applications as $application) {
                $application->semesterID = $newSemester->id;
                $application->transfer_pending = false;

                if ($application->serviceType == "Projeto") {
                    $application->status = "Aguardando agendamento da triagem";
                } elseif ($application->serviceType == "Consulta") {
                    $application->status = "Aguardando agendamento da reunião de consulta";
                }

                $application->save();

                Event::create([
                    'applicationID' => $application->id,
                    'name' => 'Transferência de Semestre (Automática)',
                    'description' => "Inscrição transferida automaticamente para o semestre {$newSemester->period} de {$newSemester->year}.",
                    'event_date' => now(),
                ]);
            }
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Semester  $semester
     * @return \Illuminate\Http\Response
     */
    public function show(Semester $semester)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Semester  $semester
     * @return \Illuminate\Http\Response
     */
    public function edit(Semester $semester)
    {
        if(!Auth::check()){
            return redirect("/login");
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria"])){
            abort(403);
        }

        $periodo = $semester;

        return view('semesters.edit', compact('periodo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSemesterRequest  $request
     * @param  \App\Models\Semester  $semester
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSemesterRequest $request, Semester $semester)
    {
        if(!Auth::check()){
            return redirect("/login");
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria"])){
            abort(403);
        }
        
        $validated = $request->validated();

        $semester->update($validated);

        return redirect('/semesters');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Semester  $semester
     * @return \Illuminate\Http\Response
     */
    public function destroy(Semester $semester)
    {
        //
    }
}
