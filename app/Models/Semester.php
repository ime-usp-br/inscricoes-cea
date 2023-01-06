<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'period',
        'started_at',
        'finished_at',
        'start_date_enrollments',
        'end_date_enrollments',
    ];

    protected $casts = [
        'started_at' => 'date:d/m/Y',
        'finished_at' => 'date:d/m/Y',
        'start_date_enrollments' => 'date:d/m/Y',
        'end_date_enrollments' => 'date:d/m/Y',
    ];  

    public function setStartedAtAttribute($value)
    {
        $this->attributes['started_at'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
    }

    public function setFinishedAtAttribute($value)
    {
        $this->attributes['finished_at'] = Carbon::createFromFormat('d/m/Y', $value)->endOfDay();
    }  

    public function setStartDateEnrollmentsAttribute($value)
    {
        $this->attributes['start_date_enrollments'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
    }

    public function setEndDateEnrollmentsAttribute($value)
    {
        $this->attributes['end_date_enrollments'] = Carbon::createFromFormat('d/m/Y', $value)->endOfDay();
    }

    public function getStartedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d/m/Y') : '';
    }

    public function getFinishedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d/m/Y') : '';
    }

    public function getStartDateEnrollmentsAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d/m/Y') : '';
    }

    public function getEndDateEnrollmentsAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d/m/Y') : '';
    }

    public static function getLatest()
    {
        $year = Semester::max("year");
        $period = Semester::where("year",$year)->max("period");
        return Semester::where(["year"=>$year,"period"=>$period])->first();
    } 

    public function IsEnrollmentPeriod()
    {
        $today = Carbon::now();
        $start = Carbon::createFromFormat("d/m/Y", $this->start_date_enrollments)->startOfDay();
        $end = Carbon::createFromFormat("d/m/Y", $this->end_date_enrollments)->endOfDay();
        return ($start <= $today and $end >= $today);
    }

    public function getStartDateEnrollments()
    {
        return Carbon::createFromFormat("d/m/Y", $this->start_date_enrollments)->startOfDay();
    }

    public function getEndDateEnrollments()
    {
        return Carbon::createFromFormat("d/m/Y", $this->end_date_enrollments)->endOfDay();
    }
}
