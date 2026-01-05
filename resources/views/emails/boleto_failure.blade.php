<p>O boleto para a inscrição <strong>{{ $application->id }}</strong> ({{ $application->name }}) falhou ao ser gerado via SOAP.</p>

<p>Por favor, acesse o painel administrativo e tente gerar o boleto novamente para esta inscrição.</p>

<p>Dados técnicos:<br>
Protocolo: {{ $application->protocol }}<br>
CPF: {{ $application->CPFCNPJ }}
</p>
