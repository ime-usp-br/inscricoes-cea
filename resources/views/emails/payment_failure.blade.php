@extends('main')

@section('content')

<h2>Olá, {{ $application->bdName }}!</h2>

<p>
    Informamos que houve uma falha técnica no envio automático do seu boleto de inscrição para o curso/projeto do CEA.
    Por conta disso, o boleto enviado anteriormente (caso tenha recebido) pode ter vencido.
</p>

<p>
    Para corrigir essa situação, geramos um <strong>NOVO boleto</strong> com nova data de vencimento, que segue em anexo a este e-mail.
</p>

<h3>Instruções Importantes:</h3>
<ul>
    <li>
        <strong>Se você JÁ REALIZOU o pagamento</strong> do boleto anterior:
        <br>
        Por favor, <strong>DESCONSIDERE</strong> este e-mail e o boleto anexo. Seu pagamento será processado normalmente.
    </li>
    <li>
        <strong>Se você AINDA NÃO pagou:</strong>
        <br>
        Utilize o boleto em anexo para efetuar o pagamento e regularizar sua inscrição. Desconsidere qualquer boleto anterior.
    </li>
</ul>

<p>
    Pedimos desculpas pelo transtorno e estamos à disposição para dúvidas.
</p>

<p>
    Atenciosamente,<br>
    Centro de Estatística Aplicada (CEA) - IME USP<br>
    <a href="mailto:cea@ime.usp.br">cea@ime.usp.br</a>
</p>

@endsection
