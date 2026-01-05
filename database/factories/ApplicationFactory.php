<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Semester;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    public function definition()
    {
        return [
            'semesterID' => Semester::factory(),
            'protocol' => $this->faker->uuid,
            'serviceType' => 'Consulta',
            'conclusions' => 'Conclusions',
            'expectedHelp' => 'Help',
            'bdName' => $this->faker->name,
            'bdCpfCnpj' => $this->faker->numerify('###########'),
            'bdBankName' => 'Bank',
            'bdAgency' => '0001',
            'bdAccount' => '123456',
            'bdType' => 'Corrente',
            'status' => 'Aguardando agendamento', 
            'projectResponsible' => $this->faker->name,
            'contactPhone' => $this->faker->phoneNumber,
            'CPFCNPJ' => $this->faker->numerify('###########'),
            'email' => $this->faker->safeEmail,
            'institution' => 'USP',
            'course' => 'Estatística',
            'institutionRelationship' => 'Aluno',
            'projectPurpose' => 'TCC',
            'fundingAgency' => 'Nenhuma',
            'knowledgeArea' => 'Exatas',
            'dataCollect' => 'Sim',
            'projectTitle' => 'Test Project',
            'generalAspects' => 'Aspects',
            'generalObjectives' => 'Objectives',
            'features' => 'Features',
            'otherFeatures' => 'Other',
            'limitations' => 'Limits',
            'storage' => 'Storage',
            'conclusions' => 'Conclusions',
            'expectedHelp' => 'Help',
        ];
    }
}
