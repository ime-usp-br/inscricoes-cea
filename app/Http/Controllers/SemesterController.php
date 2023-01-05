<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSemesterRequest;
use App\Http\Requests\UpdateSemesterRequest;
use App\Models\Semester;

class SemesterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

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

        $validated = $request->validated();

        $periodo = Semester::updateOrCreate(['year'=>$validated['year'], 'period'=>$validated['period']],$validated);

        return redirect('/semesters');
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
