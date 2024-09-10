@extends('layouts.app')

@section('content')
@parent
<div id="layout_conteudo">
    <div class="justify-content-center">
        <div class="col-md-12">
            <h1 class='text-center mt-4'>Registros</h1>
            <h4 class='text-center pb-4'>{{ $semester->period }} de {{ $semester->year }}</h4>

            @include('events.modals.chooseSemester')

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


            @if (count($events) > 0)
                <table class="table table-bordered table-striped table-hover" style="font-size:15px;">
                    <tr>
                        <th>Protocolo</th>
                        <th>Nome do pesquisador</th>
                        <th>E-mail</th>
                        <th>Descrição</th>
                        <th>Data</th>
                    </tr>

                    @foreach($events as $event)
                        <tr class="text-center">
                            <td>{{ $event->application->protocol }}</td>
                            <td>{{ $event->application->projectResponsible }}</td>
                            <td>{{ $event->application->email }}</td>
                            <td>{{ $event->description }}</td>
                            <td>{{ $event->event_date }}</td>
                        </tr>
                    @endforeach
                </table>
            @else
                <p class="text-center">Não há registros cadastrados</p>
            @endif
        </div>
    </div>
</div>

@endsection
