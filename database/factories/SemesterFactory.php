<?php

namespace Database\Factories;

use App\Models\Semester;
use Illuminate\Database\Eloquent\Factories\Factory;

class SemesterFactory extends Factory
{
    protected $model = Semester::class;

    public function definition()
    {
        return [
            'year' => $this->faker->year,
            'period' => '1º Semestre',
            'started_at' => now()->startOfYear()->format('d/m/Y'),
            'finished_at' => now()->endOfYear()->format('d/m/Y'),
            'start_date_enrollments' => now()->subDays(10)->format('d/m/Y'),
            'end_date_enrollments' => now()->addDays(10)->format('d/m/Y'),
        ];
    }
}
