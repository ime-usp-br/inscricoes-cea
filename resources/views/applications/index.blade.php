@extends('layouts.app')

@section('content')
@parent
<div id="layout_conteudo">
    <div class="justify-content-center">
        <div class="col-md-12">
            <h1 class='text-center mt-4'>Fichas de Inscrição</h1>
            <h4 class='text-center pb-4'>{{ $semester->period }} de {{ $semester->year }}</h4>

            @include('applications.modals.chooseSemester')
            @include('applications.modals.scheduleTriageModal')
            @include('applications.modals.scheduleConsultationMeetingModal')

            <p class="text-right">
                <a  id="btn-chooseSemesterModal"
                    class="btn btn-outline-primary"
                    data-toggle="modal"
                    data-target="#chooseSemesterModal"
                    title="Escolher Semestre" 
                >
                    Escolher Semestre
                </a>
            </p>


            @if (count($fichas) > 0)
                <table class="table table-bordered table-striped table-hover" style="font-size:15px;">
                    <tr>
                        <th>Protocolo</th>
                        <th>Modalidade</th>
                        <th>Nome do pesquisador</th>
                        <th>E-mail</th>
                        <th>Status</th>
                        <th>Boleto</th>
                        <th></th>
                    </tr>

                    @foreach($fichas as $ficha)
                        <tr class="text-center">
                            <td>{{ $ficha->protocol }}</td>
                            <td style="white-space:nowrap">                            
                                <form method="POST" enctype="multipart/form-data" action="{{ route('applications.changeServiceType',$ficha) }}">
                                    @csrf
                                    @method("PATCH")

                                    {{ $ficha->serviceType }}
                                    <button class="btn btn-outline-dark btn-sm" title="Mudar Modalidade" type="submit">Mudar</button>                                    
                                </form>    
                            </td>
                            <td>{{ $ficha->projectResponsible }}</td>
                            <td>{{ $ficha->email }}</td>
                            <td>
                                {{ $ficha->status }}
                                @if($ficha->status == "Aguardando agendamento da triagem")
                                    <a class="text-dark text-decoration-none"
                                        data-toggle="modal"
                                        data-target="#scheduleTriageModal"
                                        data-id="{{ $ficha->id }}"
                                        title="Agendar Triagem"
                                        href="{{ route('triages.store') }}"
                                    >
                                        <i class="fas fa-calendar-plus"></i>
                                    </a>
                                @elseif($ficha->status == "Aguardando pagamento" or $ficha->status == "Aguardando confirmação de pagamento")
                                    <a href="{{ route('applications.show', $ficha) }}" class="btn btn-sm btn-primary">Detalhes</a>
                                    @if(!$ficha->applicationFee)
                                        <form action="{{ route('applications.regenerateBoleto', $ficha) }}" method="POST" class="d-inline" onsubmit="return confirm('Tentaremos gerar um novo boleto via SOAP e enviar por e-mail. Confirmar?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning">Gerar Boleto (Manual)</button>
                                        </form>
                                    @endif
                                @elseif($ficha->status == "Aguardando agendamento da reunião de consulta")
                                    <a class="text-dark text-decoration-none"
                                        data-toggle="modal"
                                        data-target="#scheduleConsultationMeetingModal"
                                        data-id="{{ $ficha->id }}"
                                        title="Agendar Reunião de Consulta"
                                        href="{{ route('consultationmeetings.store') }}"
                                    >
                                        <i class="fas fa-calendar-plus"></i>
                                    </a>
                                @endif
                            </td>
                            <td style="white-space:nowrap">
                                Taxa de Inscrição, {{$ficha->getAggregatedInscriptionFeeStatus()}}
                                <br>
                                Taxa de Projeto, {{$ficha->getAggregatedProjectFeeStatus()}}
                                @if($ficha->complementaryFee)
                                    <br>
                                    Complemento, {{$ficha->complementaryFee->getStatus()}}
                                @endif
                            </td>
                            <td style="white-space:nowrap">
                                <div class="row justify-content-center pb-1">
                                    <div class="col-12">
                                        <a class="btn btn-outline-dark btn-sm"
                                            data-toggle="tooltip" data-placement="top"
                                            title="Visualizar"
                                            href="{{ route('applications.show', $ficha) }}"
                                        >
                                            Visualizar
                                        </a>
                                        <a class="btn btn-outline-dark btn-sm"
                                            data-toggle="tooltip" data-placement="top"
                                            title="Visualizar"
                                            href="{{ route('applications.downloadAsPDF', $ficha->protocol) }}"
                                        >
                                            PDF Completo
                                        </a>
                                    </div>
                                </div>
                                <a class="btn btn-outline-dark btn-sm my-1"
                                    data-toggle="tooltip" data-placement="top"
                                    title="Visualizar"
                                    href="{{ route('applications.downloadFirstPageAsPDF', $ficha->protocol) }}"
                                >
                                    PDF Primeira Pagina
                                </a>
                                                       
                                <form method="POST" enctype="multipart/form-data" action="{{ route('applications.destroy',$ficha) }}">
                                    @csrf
                                    @method("DELETE")
                                    <button class="btn btn-outline-danger btn-sm" title="Mudar Modalidade" type="submit">Excluir</button>                                    
                                </form>    
                            </td>
                        </tr>
                    @endforeach
                </table>
            @else
                <p class="text-center">Não há inscrições cadastrados</p>
            @endif
        </div>
    </div>
</div>

@endsection

@section('javascripts_bottom')
 @parent
<script>
    $('#scheduleTriageModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var routePath = button.attr('href');
        $(this).find('.modal-content form').attr('action', routePath);
        $("input:radio[name='meetingMode']").each(function(i) {
            this.checked = false;
        });
        $('#div-reuniao').empty();
        $("#hiddenApplicationID").val(button.attr('data-id'));
    });
    $('#scheduleConsultationMeetingModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var routePath = button.attr('href');
        $(this).find('.modal-content form').attr('action', routePath);
        $("input:radio[name='meetingMode']").each(function(i) {
            this.checked = false;
        });
        $('#div-reuniao-cm').empty();
        $(this).find("#hiddenApplicationID").val(button.attr('data-id'));
    });
    function rdChange(ckType){
        if(ckType.value == "Online"){
            var html = [
                '<div class="row custom-form-group d-flex align-items-center">',
                    '<div class="col-12 col-md-5 text-md-right">',
                        '<label>Link:</label>',
                    '</div>',
                    '<div class="col-12 col-md-7">',
                        '<input class="custom-form-control" type="text" name="link" required>',
                    '</div>',
                '</div>'
            ].join("\n");
            $('#div-reuniao').html(html);
            $('#div-reuniao-cm').html(html);
        }else if (ckType.value == "Presencial"){
            var html = [
                '<div class="row custom-form-group d-flex align-items-center">',
                    '<div class="col-12 col-md-5 text-md-right">',
                        '<label>Local:</label>',
                    '</div>',
                    '<div class="col-12 col-md-7">',
                        '<input class="custom-form-control" type="text" name="local" required>',
                    '</div>',
                '</div>'
            ].join("\n");
            $('#div-reuniao').html(html);
            $('#div-reuniao-cm').html(html);
        }
    }
</script>
@endsection
