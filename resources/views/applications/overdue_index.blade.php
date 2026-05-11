@extends('layouts.app')

@section('content')
@parent
<div id="layout_conteudo">
    <div class="justify-content-center">
        <div class="col-md-12">
            <h1 class='text-center mt-4'>Cobranças Manuais</h1>
            <h4 class='text-center pb-4'>{{ $semester->period }} de {{ $semester->year }}</h4>

            @if ($overdueApplications->count() > 0)
                <form id="bulkForm" action="{{ route('applications.sendOverdueReminders') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary" onclick="return confirm('Enviar e-mail de cobrança para os selecionados?')">
                            Enviar e-mail de cobrança para os selecionados
                        </button>
                    </div>
                </form>

                <table class="table table-bordered table-striped table-hover" style="font-size:15px;">
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>Protocolo</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Taxa(s) Vencida(s)</th>
                        <th>Valor Devido</th>
                        <th>Histórico de Cobrança</th>
                        <th>Ações</th>
                    </tr>

                    @foreach($overdueApplications as $app)
                            @php
                                $overdueFees = collect();
                                foreach ($app->allApplicationFees as $fee) {
                                    if (!str_contains($fee->relativoA, '(Substituído)') && $fee->statusBoletoBancario != 'P' && !$fee->manual_payment_confirmed && !empty($fee->dataVencimentoBoleto)) {
                                        try {
                                            $dueDate = \Carbon\Carbon::createFromFormat('d/m/Y', $fee->dataVencimentoBoleto);
                                            if ($dueDate->isPast()) {
                                                $overdueFees->push($fee);
                                            }
                                        } catch (\Exception $e) { }
                                    }
                                }
                                foreach ($app->allProjectFees as $fee) {
                                    if (!str_contains($fee->relativoA, '(Substituído)') && $fee->statusBoletoBancario != 'P' && !$fee->manual_payment_confirmed && !empty($fee->dataVencimentoBoleto)) {
                                        try {
                                            $dueDate = \Carbon\Carbon::createFromFormat('d/m/Y', $fee->dataVencimentoBoleto);
                                            if ($dueDate->isPast()) {
                                                $overdueFees->push($fee);
                                            }
                                        } catch (\Exception $e) { }
                                    }
                                }
                                $totalDue = $overdueFees->sum('valorDocumento');
                                $chargeEvents = $app->events->where('name', 'Cobrança Manual');
                            @endphp
                        <tr class="text-center">
                            <td><input type="checkbox" name="application_ids[]" value="{{ $app->id }}" form="bulkForm"></td>
                            <td>{{ $app->protocol }}</td>
                            <td>{{ $app->projectResponsible }}</td>
                            <td>{{ $app->email }}</td>
                            <td>
                                @foreach($overdueFees as $fee)
                                    {{ $fee->relativoA }} (R$ {{ number_format($fee->valorDocumento, 2, ',', '.') }})<br>
                                @endforeach
                            </td>
                            <td>R$ {{ number_format($totalDue, 2, ',', '.') }}</td>
                            <td>
                                @if($chargeEvents->count() > 0)
                                    @foreach($chargeEvents as $ev)
                                        {{ \Carbon\Carbon::parse($ev->event_date)->format('d/m/Y H:i') }}<br>
                                    @endforeach
                                @else
                                    -
                                @endif
                            </td>
                            <td style="white-space:nowrap">
                                @foreach($overdueFees as $fee)
                                    <form action="{{ route('bankslips.confirmManualPayment', $fee) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirmar pagamento manual por depósito para {{ $fee->relativoA }}?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success mb-1">Confirmar {{ $fee->relativoA }}</button>
                                    </form>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </table>
            @else
                <p class="text-center">Não há inscrições com boletos vencidos no momento.</p>
            @endif
        </div>
    </div>
</div>
@endsection

@section('javascripts_bottom')
 @parent
<script>
    $('#selectAll').on('change', function () {
        $('input[name="application_ids[]"]').prop('checked', this.checked);
    });
</script>
@endsection
