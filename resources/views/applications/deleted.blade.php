@extends('layouts.app')

@section('content')
@parent
<div id="layout_conteudo">
    <div class="justify-content-center">
        <div class="col-md-12">
            <h1 class='text-center mt-4'>Fichas de Inscrição Excluídas</h1>
            <h4 class='text-center pb-5'>{{ $semester->period }} de {{ $semester->year }}</h4>

            @if (count($fichas) > 0)
                <table class="table table-bordered table-striped table-hover" style="font-size:15px;">
                    <tr>
                        <th>Protocolo</th>
                        <th>Modalidade</th>
                        <th>Nome do pesquisador</th>
                        <th>E-mail</th>
                        <th></th>
                    </tr>

                    @foreach($fichas as $ficha)
                        <tr class="text-center">
                            <td>{{ $ficha->protocol }}</td>
                            <td>{{ $ficha->serviceType }}</td>
                            <td>{{ $ficha->projectResponsible }}</td>
                            <td>{{ $ficha->email }}</td>
                            <td>                                                       
                                <form method="POST" enctype="multipart/form-data" action="{{ route('applications.restore',$ficha) }}">
                                    @csrf
                                    @method("PATCH")
                                    <button class="btn btn-outline-dark btn-sm" title="Restaurar" type="submit">Restaurar</button>                                    
                                </form>    
                            </td>
                        </tr>
                    @endforeach
                </table>
            @else
                <p class="text-center">Não há inscrições excluidas</p>
            @endif
        </div>
    </div>
</div>
@endsection
