<?php

namespace Database\Factories;

use App\Models\MailTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class MailTemplateFactory extends Factory
{
    protected $model = MailTemplate::class;

    public function definition()
    {
        return [
            'name' => 'Template Teste',
            'description' => 'Descrição do template',
            'subject' => 'Assunto do E-mail',
            'body' => 'Corpo do e-mail para {{ $application->name }}',
            'mail_class' => 'NotifyInscribedAboutApplication',
            'sending_frequency' => 'A cada inscrição',
            'active' => true,
        ];
    }
}
