<?php

namespace Database\Factories;

use App\Models\BankSlip;
use App\Models\Application;
use Illuminate\Database\Eloquent\Factories\Factory;

class BankSlipFactory extends Factory
{
    protected $model = BankSlip::class;

    public function definition()
    {
        return [
            'applicationID' => Application::factory(),
            'valorDocumento' => 80.00,
            'valorDesconto' => 0.00,
            'valorEfetivamentePago' => 0.00,
            'codigoIDBoleto' => $this->faker->numerify('###########'),
            'dataVencimentoBoleto' => now()->addDays(5)->format('d/m/Y'),
            'relativoA' => 'Taxa de Inscrição',
            'statusBoletoBancario' => '0', // 0 = Em aberto? Check BankSlip logic if needed, but schema allows string 1 char
        ];
    }
}
