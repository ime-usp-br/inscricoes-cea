<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Semester;
use Auth;

class MainController extends Controller
{
    public function index()
    {  
        $semester = Semester::getLatest();
        return view('main', compact("semester"));
    }
}
