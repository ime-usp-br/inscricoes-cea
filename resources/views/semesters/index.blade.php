@extends('layouts.app')

@section('content')
@parent
<div id="layout_conteudo">
    <div class="justify-content-center">
        <div class="col-md-12">
            <h1 class='text-center mb-5'>Semestres</h1>

            <p class="text-right">
                <a class="btn btn-outline-primary" href="{{ route('semesters.create') }}">
                    <i class="fas fa-plus-circle"></i>
                    Cadastrar semestre
                </a>
            </p>


            @if (count($periodos) > 0)
                <table class="table table-bordered table-striped table-hover" style="font-size:15px;">
                    <tr>
                        <th>Ano</th>
                        <th>Período</th>
                        <th>Data inícial</th>
                        <th>Data final</th>
                        <th>Data inicial<br> das inscrições<br></th>
                        <th>Data final<br> das inscrições<br></th>
                        <th></th>
                    </tr>

                    @foreach($periodos as $periodo)
                        <tr class="text-center">
                            <td>{{ $periodo->year }}</td>
                            <td style="white-space: nowrap;">{{ $periodo->period }}</td>
                            <td>{{ $periodo->started_at }}</td>
                            <td>{{ $periodo->finished_at }}</td>
                            <td>{{ $periodo->start_date_enrollments }}</td>
                            <td>{{ $periodo->end_date_enrollments }}</td>
                            <td>
                                <a class="text-dark text-decoration-none"
                                    data-toggle="tooltip" data-placement="top"
                                    title="Editar"
                                    href="{{ route('semesters.edit', $periodo) }}"
                                >
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </table>
            @else
                <p class="text-center">Não há semestres cadastrados</p>
            @endif
        </div>
    </div>
</div>

@endsection