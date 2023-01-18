@extends('layouts.app')

@section('content')
@parent
<div id="layout_conteudo">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-11 col-md-10 col-lg-9 col-xl-8">
            <h1 class='text-center mb-5'>
                Cadastrar Novo Modelo de E-mail
            </h1>

            <p class="alert alert-info rounded-0">
                <b>Atenção:</b>
                Os campos assinalados com * são de preenchimento obrigatório.
            </p>

            <form method="POST"
                action="{{ route('mailtemplates.store', $mailtemplate) }}"
            >
                @csrf
                @include('mailtemplates.partials.form', ['buttonText' => 'Cadastrar'])
            </form>

        </div>
    </div>
</div>
@endsection


@section('javascripts_bottom')
 @parent
<script>
    $("#sending_frequency").change(function(){
        var value = $("#sending_frequency option:selected").val();
        var datediv = $("#date-div");
        var hourdiv = $("#hour-div");
        $("#date-div").empty();
        $("#hour-div").empty();
        if(value=="Única"){
            datediv.append('<div class="col-12 text-left">'+
                            '<label for="sending_date">Data*:</label>'+
                           '</div>'+
                           '<div class="col-12">'+
                            '<input  class="custom-form-control custom-datepicker" style="max-width:130px" name="sending_date" autocomplete="off">'+
                           '</div>');
            hourdiv.append('<div class="col-12 text-left">'+
                            '<label for="sending_hour">Hora*:</label>'+
                           '</div>'+
                           '<div class="col-12">'+
                            '<input  class="custom-form-control" style="max-width:100px" name="sending_hour" type="time">'+
                           '</div>');
            $('.custom-datepicker').datepicker({showOn: 'both',buttonText: '<i class="far fa-calendar"></i>'});
        }else if(value=="Mensal"){
            datediv.append('<div class="col-12 text-left">'+
                            '<label for="sending_date">Dia*:</label>'+
                           '</div>'+
                           '<div class="col-12">'+
                            '<input  class="custom-form-control" style="max-width:80px" type="number" min="1" max="31" name="sending_date">'+
                           '</div>');
            hourdiv.append('<div class="col-12 text-left">'+
                            '<label for="sending_hour">Hora*:</label>'+
                           '</div>'+
                           '<div class="col-12">'+
                            '<input  class="custom-form-control" style="max-width:100px" name="sending_hour" type="time">'+
                           '</div>');
        }
    });
    tinymce.init({
    selector: '#bodymailtemplate',
    plugins: 'link,code',
    link_default_target: '_blank'
    });
</script>
@endsection