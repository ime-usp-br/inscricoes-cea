<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Application;
use App\Models\Semester;
use App\Models\Event;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $semester = Semester::getLatest();

            $applications = Application::whereBelongsTo($semester)->get();

            foreach($applications as $application){
                if($application->applicationFee){
                    if($application->applicationFee->getStatus($atualizar = true) == "Pago"){
                        $criar_evento = true;
                        foreach($application->events as $event){
                            if($event->name == "Taxa de inscrição"){
                                $criar_evento = false;
                            }
                        }
                        if($criar_evento){
                            $event = Event::create([
                                'applicationID'=>$application->id,
                                'name'=>'Taxa de inscrição',
                                'description'=>'Pagamento da taxa de inscrição',
                                'event_date'=>Carbon::createFromFormat("!d/m/Y", $application->applicationFee->dataEfetivaPagamento)
                            ]);
                        }
                    }
                }


                if($application->projectFee){
                    if($application->projectFee->getStatus($atualizar = true) == "Pago"){
                        $criar_evento = true;
                        foreach($application->events as $event){
                            if($event->name == "Taxa do projeto"){
                                $criar_evento = false;
                            }
                        }
                        if($criar_evento){
                            $event = Event::create([
                                'applicationID'=>$application->id,
                                'name'=>'Taxa do projeto',
                                'description'=>'Pagamento da taxa do projeto',
                                'event_date'=>Carbon::createFromFormat("!d/m/Y", $application->projectFee->dataEfetivaPagamento)
                            ]);
                        }
                    }
                }
            }
        })->everyFifteenMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
