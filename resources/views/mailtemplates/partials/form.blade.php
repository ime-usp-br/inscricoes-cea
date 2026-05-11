<div class="custom-form-group align-items-center">
    <div class="col-md-12 text-lg-left">
        <label for="body">Nome do Modelo*:</label>
    </div>
    <div class="col-md-12">
        <input class="custom-form-control" type="text" name="name" id="name"
            value="{{ old('name') ?? $mailtemplate->name ?? ''}}" 
        />
    </div>
</div>

<div class="custom-form-group align-items-center">
    <div class="col-md-12 text-lg-left">
        <label for="body">Aplicação*:</label>
    </div>
    <div class="col-md-12">
        <select class="custom-form-control" type="text" name="description_and_mail_class"
        >
                <option value="" {{ ($mailtemplate->mail_class) ? '' : 'selected'}}></option>

            @foreach ([
                        "E-mail enviado a secretaria do CEA a cada inscrição"=>"NotifyCEAAboutApplication",
                        "E-mail enviado ao inscrito a cada inscrição"=>"NotifyInscribedAboutApplication",
                        "E-mail enviado a secretaria do CEA quando solicitado recibo de reembolso"=>"NotifyCEAAboutRefundReceipt",
                        "E-mail enviado ao inscrito quando a triagem é agendada"=>"NotifyAboutTriageSchedule",
                        "E-mail enviado ao inscrito quando sobre o resultado da triagem"=>"NotifyAboutTriageDecision",
                        "E-mail enviado ao inscrito quando a reunião de consulta é agendada"=>"NotifyAboutConsultationMeetingSchedule",
                        "E-mail enviado ao inscrito quando sobre o resultado da reunião de consulta"=>"NotifyAboutConsultationMeetingDecision",
                        "E-mail de cobrança por depósito para boletos vencidos"=>"NotifyOverdueBankSlip",
                     ] as $key=>$value)
                <option value='{"description":"{{$key}}","mail_class":"{{$value}}"}' {{ ( $mailtemplate->mail_class === $value) ? 'selected' : ''}}>{{ $key }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row custom-form-group">
    <div class="col-md">
        <div class="col-md-12 text-lg-left">
            <label for="sending_frequency">Frequência de envio*:</label>
        </div>
        <div class="col-md-10">
            <select class="custom-form-control" type="text" name="sending_frequency" id="sending_frequency"
            >
                    <option value="" {{ ($mailtemplate->sending_frequency) ? '' : 'selected'}}></option>

                @foreach ([
                            "Manual",
                            "Única",
                            "Mensal",
                            "A cada inscrição",
                            "A cada agendamento de triagem",
                            "A cada reagendamento de triagem",
                            "A cada resultado de triagem",
                            "A cada agendamento de reunião de consulta",
                            "A cada reagendamento da reunião de consulta",
                            "A cada resultado de reunião de consulta",
                        ] as $frequency)
                    <option value='{{$frequency}}' {{ ( $mailtemplate->sending_frequency === $frequency) ? 'selected' : ''}}>{{ $frequency }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div id="date-div" class="col-md">
        @if($mailtemplate->sending_frequency == "Única")
            <div class="col-12 text-left">
                <label for="sending_date">Data*:</label>
            </div>
            <div class="col-12">
                <input  class="custom-form-control custom-datepicker" style="max-width:130px" name="sending_date" autocomplete="off" value="{{ $mailtemplate->sending_date }}">
            </div>
        @elseif($mailtemplate->sending_frequency == "Mensal")
            <div class="col-12 text-left">
                <label for="sending_date">Dia*:</label>
            </div>
            <div class="col-12">
                <input  class="custom-form-control" style="max-width:80px" type="number" min="1" max="31" name="sending_date" value="{{ $mailtemplate->sending_date }}">
            </div>
        @endif
    </div>
    <div id="hour-div" class="col-md">
        @if($mailtemplate->sending_frequency == "Única" or $mailtemplate->sending_frequency == "Mensal")
            <div class="col-12 text-left">
                <label for="sending_hour">Hora*:</label>
            </div>
            <div class="col-12">
                <input class="custom-form-control" style="max-width:100px" name="sending_hour" type="time" value="{{ $mailtemplate->sending_hour }}">
            </div>
        @endif
    </div>
</div>

<div class="custom-form-group align-items-center">
    <div class="col-md-12 text-lg-left">
        <label for="body">Assunto*:</label>
    </div>
    <div class="col-md-12">
        <input class="custom-form-control" type="text" name="subject" id="subject"
            value="{{ old('subject') ?? $mailtemplate->subject ?? ''}}" 
        />
    </div>
</div>

<div class="custom-form-group align-items-center">
    <div class="col-md-12 text-lg-left">
        <label for="body">Corpo*:</label>
    </div>
    <div class="col-md-12">
        <textarea class="custom-form-control" name="body" id="bodymailtemplate">{{ old('body') ?? $mailtemplate->body ?? ''}}</textarea>
    </div>
</div>

<div class="row custom-form-group justify-content-center">
    <div class="col-sm-6 text-center text-sm-right my-1">
        <button type="submit" class="btn btn-outline-dark">
            {{ $buttonText }}
        </button>
    </div>
    <div class="col-sm-6 text-center text-sm-left my-1">
        <a class="btn btn-outline-dark"
            href="{{ route('mailtemplates.index') }}"
        >
            Cancelar
        </a>
    </div>
</div>