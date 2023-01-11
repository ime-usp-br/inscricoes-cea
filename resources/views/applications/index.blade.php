@extends('layouts.app')

@section('content')
@parent
<div id="layout_conteudo">
    <div class="justify-content-center">
        <div class="col-md-12">
            <h1 class='text-center mt-4'>Fichas de Inscrição</h1>
            <h4 class='text-center pb-4'>{{ $semester->period }} de {{ $semester->year }}</h4>

            @include('applications.modals.chooseSemester')

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
                        <th>Responsável(is) pelo projeto</th>
                        <th>E-mail</th>
                        <th></th>
                    </tr>

                    @foreach($fichas as $ficha)
                        <tr class="text-center">
                            <td>{{ $ficha->protocol }}</td>
                            <td>{{ $ficha->projectResponsible }}</td>
                            <td>{{ $ficha->email }}</td>
                            <td>
                                <a class="btn btn-outline-dark btn-sm"
                                    data-toggle="tooltip" data-placement="top"
                                    title="Visualizar"
                                    href="{{ route('applications.show', $ficha) }}"
                                >
                                    Visualizar Ficha Completa
                                </a>
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