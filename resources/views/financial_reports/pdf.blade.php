<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório Financeiro</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .badge-success { color: #155724; background: #d4edda; padding: 2px 6px; border-radius: 4px; }
        .badge-primary { color: #004085; background: #cce5ff; padding: 2px 6px; border-radius: 4px; }
        .badge-secondary { color: #383d41; background: #e2e3e5; padding: 2px 6px; border-radius: 4px; }
        .badge-warning { color: #856404; background: #fff3cd; padding: 2px 6px; border-radius: 4px; }
        .badge-info { color: #0c5460; background: #d1ecf1; padding: 2px 6px; border-radius: 4px; }
        .badge-danger { color: #721c24; background: #f8d7da; padding: 2px 6px; border-radius: 4px; }
    </style>
</head>
<body>
    <h2>Relatório Financeiro — {{ $semester->period }} de {{ $semester->year }}</h2>
    <table>
        <thead>
            <tr>
                <th>Protocolo</th>
                <th>Modalidade</th>
                <th>Pesquisador</th>
                <th>CPF</th>
                <th>E-mail</th>
                <th>Nome (Boleto)</th>
                <th>CPF/CNPJ (Boleto)</th>
                <th>Banco</th>
                <th>Agência</th>
                <th>Conta</th>
                <th>Tipo</th>
                <th>Recibo Reembolso</th>
                <th>Dados Reembolso</th>
                <th>Taxa de Inscrição</th>
                <th>Taxa de Projeto</th>
                <th>Complemento</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    <td>{{ $row['Protocolo'] }}</td>
                    <td>{{ $row['Modalidade'] }}</td>
                    <td>{{ $row['Pesquisador'] }}</td>
                    <td>{{ $row['CPF'] }}</td>
                    <td>{{ $row['E-mail'] }}</td>
                    <td>{{ $row['Nome (Boleto)'] }}</td>
                    <td>{{ $row['CPF/CNPJ (Boleto)'] }}</td>
                    <td>{{ $row['Banco'] }}</td>
                    <td>{{ $row['Agência'] }}</td>
                    <td>{{ $row['Conta'] }}</td>
                    <td>{{ $row['Tipo'] }}</td>
                    <td>{{ $row['Recibo Reembolso'] }}</td>
                    <td>{{ $row['Dados Reembolso'] }}</td>
                    <td>{{ $row['Taxa de Inscrição'] }}</td>
                    <td>{{ $row['Taxa de Projeto'] }}</td>
                    <td>{{ $row['Complemento'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
