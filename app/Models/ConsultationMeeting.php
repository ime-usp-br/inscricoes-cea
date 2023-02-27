<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Application;

class ConsultationMeeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'applicationID',
        'date',
        'hour',
        'meetingMode',
        'link',
        'local',
        'decision',
        'note',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class, "applicationID");
    }
}
