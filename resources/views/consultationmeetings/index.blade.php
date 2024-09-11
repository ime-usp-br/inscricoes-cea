@extends('layouts.app')

@section('content')
@parent
<div id="layout_conteudo">
    <div class="justify-content-center">
        <div class="col-md-12">
            <h1 class='text-center mt-4'>Reuniões de Consulta</h1>
            <h4 class='text-center pb-4'>{{ $semester->period }} de {{ $semester->year }}</h4>

            @include('consultationmeetings.modals.chooseSemester')
            @include('consultationmeetings.modals.rescheduleModal')
            @include('consultationmeetings.modals.decisionModal')

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


            @if (count($consultationmeetings) > 0)
                <table class="table table-bordered table-striped table-hover" style="font-size:15px;">
                    <tr>
                        <th>Protocolo</th>
                        <th>Nome do pesquisador</th>
                        <th>Modalidade</th>
                        <th>Data</th>
                        <th>Local ou Link</th>
                        <th>Resultado</th>
                        <th>Observações</th>                    
                        <th>Feedback<br>do<br>Professor</th>
                        @hasanyrole("Administrador|Secretaria")
                            <th></th>
                        @endhasanyrole
                    </tr>

                    @foreach($consultationmeetings as $consultationmeeting)
                        <tr class="text-center">
                            <td>{{ $consultationmeeting->application->protocol }}</td>
                            <td>{{ $consultationmeeting->application->projectResponsible }}</td>
                            <td>{{ $consultationmeeting->application->serviceType }}</td>
                            <td>{{ $consultationmeeting->date ." ". $consultationmeeting->hour }}</td>
                            <td>{{ $consultationmeeting->link ?? $consultationmeeting->local }}</td>
                            <td>{{ $consultationmeeting->decision }}</td>
                            <td>{{ $consultationmeeting->note }}</td>
                            <td>
                                @hasrole("Docente")
                                    <?php $date = $consultationmeeting->date ." ". $consultationmeeting->hour ?>
                                    @if(Carbon\Carbon::createFromFormat("d/m/Y H:i", $date) < now())
                                        <div class="col-12" style="white-space:nowrap;text-align:left">
                                            <input class="form-check-input" type="radio" name="{{'feedback-'.$consultationmeeting->id}}" value="Consulta realizada" onClick="rdFBChange(this)" {{ $consultationmeeting->feedback=="Consulta realizada" ? "checked" : "" }}>
                                            <label class="font-weight-normal">Consulta realizada</label><br>
                                            <input class="form-check-input" type="radio" name="{{'feedback-'.$consultationmeeting->id}}" value="Consulta não realizada" onClick="rdFBChange(this)" {{ $consultationmeeting->feedback=="Consulta não realizada" ? "checked" : "" }}>
                                            <label class="font-weight-normal">Consulta não realizada</label>
                                        </div>
                                    @endif
                                @endhasrole
                                @hasanyrole("Administrador|Secretaria")
                                    {{ $consultationmeeting->feedback }}
                                @endhasanyrole
                            </td>
                            @hasanyrole("Administrador|Secretaria")
                                <td style="white-space:nowrap">
                                    <a class="btn btn-outline-dark btn-sm"
                                        data-toggle="modal"
                                        data-target="#rescheduleModal"
                                        title="Reagendar Reunião de Consulta"
                                        href="{{ route('consultationmeetings.reschedule', $consultationmeeting) }}"
                                    >
                                        <i class="fas fa-calendar-plus"></i> Reagendar
                                    </a>
                                    <a class="btn btn-outline-dark btn-sm"
                                        data-toggle="modal"
                                        data-target="#decisionModal"
                                        title="Informar Resultado"
                                        href="{{ route('consultationmeetings.informdecision', $consultationmeeting) }}"
                                    >
                                    <i class="fas fa-plus"></i> Resultado
                                    </a>
                                    <form action="{{ route('consultationmeetings.destroy', $consultationmeeting) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method("delete")
                                        
                                        <button class="btn btn-outline-dark btn-sm" type="submit"
                                                onclick="return confirm('Você tem certeza que deseja cancelar essa reunião de consulta?')">
                                            <i class="fas fa-trash-alt"></i> Cancelar
                                        </button>
                                    </form>
                                </td>
                            @endhasanyrole
                        </tr>
                    @endforeach
                </table>
            @else
                <p class="text-center">Não há reuniões de consulta cadastradas</p>
            @endif
        </div>
    </div>
</div>

@endsection

@section('javascripts_bottom')
 @parent
<script>
    $('#rescheduleModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var routePath = button.attr('href');
        $(this).find('.modal-content form').attr('action', routePath);
        $("input:radio[name='meetingMode']").each(function(i) {
            this.checked = false;
        });
        $('#div-reuniao').empty();
    });
    $('#decisionModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var routePath = button.attr('href');
        $(this).find('.modal-content form').attr('action', routePath);
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
        }
    }
    $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    function rdFBChange(rdFeedBack){
        var res = rdFeedBack.name.split("-");
        $.ajax({
            type:"patch",
            url:"/consultationmeetings/feedback/"+res[1]+"/update",
            data:{valor:rdFeedBack.value},
            success:function(response){
                if(response["status"]=="Feedback alterado!"){
                    console.log("Feedback alterado com sucesso!!");
                }
            },        
            error:function(response){
                console.log(response);
            }}
        );
    }
</script>
@endsection