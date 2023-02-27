<div class="modal fade" id="decisionModal">
   <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Informar Resultado</h4>
            </div>
            <form id="decisionConsultationMeetingForm" method="POST"
            enctype="multipart/form-data"
            >

            @csrf
            @method('patch')
            <div class="modal-body">
                <div class="row custom-form-group align-items-center">
                    <div class="col-12 col-md-4 text-lg-right">
                        <label>Resultado:</label>
                    </div>
                    <div class="col-12 col-md-8" style="white-space: nowrap;">
                        <select class="custom-form-control" type="text" name="decision" id="decision"
                        >
                                <option value="" selected></option>

                            @foreach ([
                                        "Aprovado como Consulta",
                                        "Aprovado como projeto",
                                        "Não aprovado",
                                    ] as $decision)
                                <option value='{{$decision}}'>{{ $decision }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row custom-form-group align-items-center">
                    <div class="col-12 col-md-4 text-lg-right">
                        <label>Observação:</label>   
                    </div> 

                    <div class="col-12 col-md-8" style="white-space: nowrap;">
                        <textarea class="custom-form-control" name="note" id="note"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="btn-decisionConsultationMeeting" class="btn btn-default" type="submit">Submeter</button>
                <button class="btn btn-default" type="button" data-dismiss="modal">Fechar</button>
            </div>
            </form>
        </div>
    </div>
</div>