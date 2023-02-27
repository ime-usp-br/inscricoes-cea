<div class="modal fade" id="scheduleConsultationMeetingModal">
   <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Agendar Reunião de Consulta</h4>
            </div>
            <form id="scheduleConsultationMeetingForm" method="POST"
            enctype="multipart/form-data"
            >

            @csrf
            <input type="hidden" name="applicationID" id="hiddenApplicationID"/>

            <div class="modal-body">
                <div class="row custom-form-group align-items-center">
                    <div class="col-12 col-md-5 text-lg-right">
                        <label>Data:</label>   
                    </div> 

                    <div class="col-12 col-md-7" style="white-space: nowrap;">
                        <input class="custom-form-control custom-datepicker" style="max-width:200px;"
                            type="text" name="date" id="date-cm" autocomplete="off"
                        />
                    </div>
                </div>
                <div class="row custom-form-group align-items-center">
                    <div class="col-12 col-md-5 text-lg-right">
                        <label>Hora:</label>   
                    </div> 

                    <div class="col-12 col-md-7" style="white-space: nowrap;">
                        <input class="custom-form-control" style="max-width:100px;"
                            type="time" name="hour" id="hour" autocomplete="off"
                        />
                    </div>
                </div>

                <div class="row custom-form-group d-flex align-items-center">
                    <div class="col-12 col-md-5 text-md-right">
                        <label id="serviceType">Modo de Reunião:</label>
                    </div>
                    <div class="col-12 col-md-7">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="meetingMode" value="Online" onClick="rdChange(this)" required>
                            <label class="font-weight-normal">Online</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="meetingMode" value="Presencial" onClick="rdChange(this)" required>
                            <label class="font-weight-normal">Presencial</label>
                        </div>
                    </div>
                </div>

                <div id="div-reuniao-cm">
                </div>
            </div>
            <div class="modal-footer">
                <button id="btn-scheduleConsultationMeeting" class="btn btn-default" type="submit">Agendar</button>
                <button class="btn btn-default" type="button" data-dismiss="modal">Fechar</button>
            </div>
            </form>
        </div>
    </div>
</div>