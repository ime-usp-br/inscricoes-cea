<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\IndexEventRequest;

use Auth;
use App\Models\Semester;
use App\Models\Event;

class EventController extends Controller
{
    public function index(IndexEventRequest $request)
    {
        if(!Auth::check()){
            return redirect("/login");
        }elseif(!Auth::user()->hasRole(["Administrador", "Secretaria"])){
            abort(403);
        }

        $validated = $request->validated();

        if(isset($validated['semester_id'])){
            $semester = Semester::find($validated['semester_id']);
        }else{
            $semester = Semester::getLatest();
        }

        $events = Event::whereHas("application", function ($query) use ($semester){
            $query->whereBelongsTo($semester);
        })->get();


        return view("events.index", compact(["semester", "events"]));
    }
}
