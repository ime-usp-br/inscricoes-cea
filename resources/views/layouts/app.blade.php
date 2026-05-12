@extends('laravel-usp-theme::master')

@section('title')
  @parent 
@endsection

@section('styles')
  @parent
  <link rel="stylesheet" href="{{ asset('css/app.css').'?version=2' }}" />
  <link rel="stylesheet" href="{{ asset('css/listmenu_v.css').'?version=1' }}" />
  @if(!Auth::check())
    <style>  
        #layout_conteudo {
            padding-left: 0;
        }
    </style>
  @endif
@endsection

@section('javascripts_bottom')
  @parent
  <script type="text/javascript">
    let baseURL = "{{ env('APP_URL') }}";
  </script>
  <script type="text/javascript" src="{{ asset('js/app.js').'?version=1' }}"></script>
  <script src="{{ asset('js/datepicker-pt-BR.js').'?version=1' }}"></script>
  <script src="https://cdn.tiny.cloud/1/fluxyozlgidop2o9xx3484rluezjjiwtcodjylbuwavcfnjg/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
  <script>
    $( "#menulateral" ).menu();
  </script>
@endsection


@section('content')
@if(Auth::check())
  <div id="layout_menu">
      <ul id="menulateral" class="menulist">
          <li class="menuHeader">Acesso Restrito</li>
          <li>
              <a href="{{ route('home') }}">Página Inicial</a>
          </li>
          @can("editar usuario")
              <li>
                  <a href="{{ route('users.index') }}">Usuários</a>
                  <ul>
                      <li>
                          <a href="{{ route('users.loginas') }}">Logar Como</a>
                      </li>
                  </ul>
              </li>
          @endcan
          @can("visualizar semestres")
              <li>
                  <a href="{{ route('semesters.index') }}">Semestres</a>
              </li>
          @endcan
          @hasanyrole("Administrador|Secretaria")
              <li>
                  <a href="{{ route('events.index') }}">Histórico</a>
              </li>
          @endhasanyrole
          @can("visualizar inscrições")
              <li>
                  <a href="{{ route('applications.index') }}">Inscrições</a>
              </li>
          @endcan
          @can("visualizar inscrições")
              <li>
                  <a href="{{ route('applications.deleted_index') }}">Inscrições Excluidas</a>
              </li>
          @endcan
          @hasanyrole("Administrador|Secretaria")
              <li>
                  <a href="{{ route('applications.overdue_index') }}">Cobranças Manuais</a>
              </li>
              <li>
                  <a href="{{ route('financial-reports.index') }}">Relatório Financeiro</a>
              </li>
          @endhasanyrole
          @can("visualizar triagens")
              <li>
                  <a href="{{ route('triages.index') }}">Triagens</a>
              </li>
          @endcan
          @can("visualizar reuniões de consulta")
              <li>
                  <a href="{{ route('consultationmeetings.index') }}">Reuniões de Consulta</a>
              </li>
          @endcan
          @can("Editar E-mails")
              <li>
                  <a href="{{ route('mailtemplates.index') }}">E-mails</a>
              </li>
          @endcan
          <li>
              <form style="padding:0px;" action="{{ route('logout') }}" method="POST" id="logout_form2">
                  @csrf
                  <a onclick="document.getElementById('logout_form2').submit(); return false;">Sair</a>
              </form>
          </li>
      </ul>
  </div>
@endif
<div id="layout_conteudo">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{!! $error !!}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="flash-message">
    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('alert-' . $msg))
        <p class="alert alert-{{ $msg }}">{!! Session::get('alert-' . $msg) !!}</p>
        <?php Session::forget('alert-' . $msg) ?>
        @endif
    @endforeach
    </div>
</div>
@endsection