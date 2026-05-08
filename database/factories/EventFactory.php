<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Application;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition()
    {
        return [
            'applicationID' => Application::factory(),
            'name' => 'Evento de Teste',
            'description' => 'Descrição de teste',
            'event_date' => now(),
        ];
    }
}
