@extends('layouts.app')

@section('content')
@parent
<div id="layout_conteudo">
    <div class="justify-content-center">
        <div class="col-md-12">
            <h1 class="text-center mt-4">Relatório Financeiro</h1>
            <h4 class="text-center pb-4">{{ $semester->period }} de {{ $semester->year }}</h4>

            <p class="text-right">
                <a href="{{ route('financial-reports.index', ['format' => 'excel']) }}" class="btn btn-outline-success btn-export" id="btn-export-excel">Exportar Excel</a>
                <a href="{{ route('financial-reports.index', ['format' => 'csv']) }}" class="btn btn-outline-secondary btn-export" id="btn-export-csv">Exportar CSV</a>
            </p>

            @if (count($applications) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" style="font-size:15px;" id="financial-report-table">
                        <thead>
                            <tr>
                                <th>Protocolo</th>
                                <th>Modalidade</th>
                                <th>Pesquisador</th>
                                <th>CPF</th>
                                <th>E-mail</th>
                                <th>Dados Bancários</th>
                                <th>Recibo Reembolso</th>
                                <th>Dados Reembolso</th>
                                <th>Taxa de Inscrição</th>
                                <th>Taxa de Projeto</th>
                                <th>Complemento</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $badgeClasses = [
                                    'Pago' => 'badge-success',
                                    'Emitido' => 'badge-primary',
                                    'Não Emitido' => 'badge-secondary',
                                ];
                                $defaultBadge = 'badge-warning';
                            @endphp
                            @foreach($applications as $app)
                                @php
                                    $inscStatus = $app->getAggregatedInscriptionFeeStatus();
                                    $projStatus = $app->getAggregatedProjectFeeStatus();
                                    $compStatus = $app->complementaryFee ? $app->complementaryFee->getStatus() : '—';

                                    $needsSync = ($inscStatus !== 'Pago' && $inscStatus !== 'Não Emitido')
                                              || ($projStatus !== 'Pago' && $projStatus !== 'Não Emitido')
                                              || ($compStatus !== '—' && $compStatus !== 'Pago' && $compStatus !== 'Não Emitido');
                                @endphp
                                <tr class="text-center" data-app-id="{{ $app->id }}" @if($needsSync) data-needs-sync="true" @endif>
                                    <td>{{ $app->protocol }}</td>
                                    <td>{{ $app->serviceType }}</td>
                                    <td>{{ $app->projectResponsible }}</td>
                                    <td>{{ $app->CPFCNPJ }}</td>
                                    <td>{{ $app->email }}</td>
                                    <td class="text-left" style="white-space: nowrap;">
                                        <strong>Nome:</strong> {{ $app->bdName }}<br>
                                        <strong>CPF/CNPJ:</strong> {{ $app->bdCpfCnpj }}<br>
                                        <strong>Banco:</strong> {{ $app->bdBankName }}<br>
                                        <strong>Agência:</strong> {{ $app->bdAgency }}<br>
                                        <strong>Conta:</strong> {{ $app->bdAccount }}<br>
                                        <strong>Tipo:</strong> {{ $app->bdType }}
                                    </td>
                                    <td>{{ $app->refundReceipt ?? '—' }}</td>
                                    <td>{{ $app->refundReceiptData ?? '—' }}</td>
                                    <td class="cell-inscription">
                                        @if($needsSync && $inscStatus !== 'Pago' && $inscStatus !== 'Não Emitido')
                                            <span class="badge badge-info sync-pending">
                                                Atualizando <i class="fas fa-spinner fa-spin"></i>
                                            </span>
                                        @else
                                            <span class="badge {{ $badgeClasses[$inscStatus] ?? $defaultBadge }}">{{ $inscStatus }}</span>
                                        @endif
                                    </td>
                                    <td class="cell-project">
                                        @if($needsSync && $projStatus !== 'Pago' && $projStatus !== 'Não Emitido')
                                            <span class="badge badge-info sync-pending">
                                                Atualizando <i class="fas fa-spinner fa-spin"></i>
                                            </span>
                                        @else
                                            <span class="badge {{ $badgeClasses[$projStatus] ?? $defaultBadge }}">{{ $projStatus }}</span>
                                        @endif
                                    </td>
                                    <td class="cell-complementary">
                                        @if($needsSync && $compStatus !== '—' && $compStatus !== 'Pago' && $compStatus !== 'Não Emitido')
                                            <span class="badge badge-info sync-pending">
                                                Atualizando <i class="fas fa-spinner fa-spin"></i>
                                            </span>
                                        @else
                                            <span class="badge {{ ($compStatus === '—') ? 'badge-secondary' : ($badgeClasses[$compStatus] ?? $defaultBadge) }}">{{ $compStatus }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center">Não há inscrições no semestre atual.</p>
            @endif
        </div>
    </div>
</div>
@endsection

@section('javascripts_bottom')
@parent
<script>
    $(document).ready(function () {
        var badgeClasses = {
            'Pago': 'badge-success',
            'Emitido': 'badge-primary',
            'Não Emitido': 'badge-secondary',
        };
        var defaultBadge = 'badge-warning';

        var $exportButtons = $('.btn-export');
        var $rowsToSync = $('tr[data-needs-sync="true"]');
        var pendingRequests = $rowsToSync.length;

        if (pendingRequests > 0) {
            $exportButtons.addClass('disabled').attr('aria-disabled', 'true').click(function(e) {
                e.preventDefault();
            });
        }

        function checkAllDone() {
            pendingRequests--;
            if (pendingRequests <= 0) {
                $exportButtons.removeClass('disabled').removeAttr('aria-disabled').off('click');
            }
        }

        $rowsToSync.each(function () {
            var $row = $(this);
            var appId = $row.data('app-id');
            var url = "{{ route('financial-reports.sync', ['application' => ':id']) }}".replace(':id', appId);

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                dataType: 'json',
                success: function (data) {
                    var inscClass = badgeClasses[data.inscription] || defaultBadge;
                    var projClass = badgeClasses[data.project] || defaultBadge;
                    var compClass = (data.complementary === '—') ? 'badge-secondary' : (badgeClasses[data.complementary] || defaultBadge);

                    $row.find('.cell-inscription').html('<span class="badge ' + inscClass + '">' + data.inscription + '</span>');
                    $row.find('.cell-project').html('<span class="badge ' + projClass + '">' + data.project + '</span>');
                    $row.find('.cell-complementary').html('<span class="badge ' + compClass + '">' + data.complementary + '</span>');
                },
                error: function () {
                    $row.find('.sync-pending').each(function () {
                        $(this).removeClass('badge-info').addClass('badge-danger').html('Erro');
                    });
                },
                complete: function () {
                    checkAllDone();
                }
            });
        });
    });
</script>
@endsection
