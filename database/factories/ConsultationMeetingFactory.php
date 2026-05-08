<?php

namespace Database\Factories;

use App\Models\ConsultationMeeting;
use App\Models\Application;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConsultationMeetingFactory extends Factory
{
    protected $model = ConsultationMeeting::class;

    public function definition()
    {
        return [
            'applicationID' => Application::factory(),
            'date' => now()->addDays(3)->format('d/m/Y'),
            'hour' => '14:00',
            'meetingMode' => 'Remoto',
            'link' => 'https://meet.test',
            'local' => null,
            'decision' => null,
            'note' => null,
            'feedback' => null,
        ];
    }
}
