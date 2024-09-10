<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;


    protected $fillable = [
        'applicationID', 
        'name', 
        'description', 
        'event_date'
    ];


    public function application()
    {
        return $this->belongsTo(Application::class, "applicationID");
    }
}
