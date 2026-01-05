<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Aviso de Inscrição CEA</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        h2 { color: #2c3e50; }
        .footer { margin-top: 30px; font-size: 0.9em; color: #777; border-top: 1px solid #eee; padding-top: 10px; }
        a { color: #3490dc; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Olá, {{ explode(' ', trim($application->bdName))[0] }}!</h2>

        <p>
            Informamos que houve uma falha técnica no envio automático do seu boleto de inscrição para o curso/projeto do CEA.
            Por conta disso, o boleto enviado anteriormente (caso tenha recebido) pode ter vencido ou não ter sido entregue.
        </p>

        <p>
            Para corrigir essa situação, geramos um <strong>NOVO boleto</strong> com nova data de vencimento, que segue em anexo a este e-mail.
        </p>

        <h3>Instruções Importantes:</h3>
        <ul>
            <li>
                <strong>Se você JÁ REALIZOU o pagamento</strong> do boleto anterior:
                <br>
                Por favor, <strong>entre em contato com o CEA</strong> imediatamente enviando o comprovante de pagamento para o e-mail 
                <a href="mailto:{{ env('MAIL_CEA') ?? 'cea@ime.usp.br' }}">{{ env('MAIL_CEA') ?? 'cea@ime.usp.br' }}</a>.
                <br>
                <small>(Como um novo boleto foi gerado, o anterior perde a referência no nosso sistema, mas seu pagamento é válido mediante comprovação).</small>
            </li>
            <li>
                <strong>Se você AINDA NÃO pagou:</strong>
                <br>
                Utilize o boleto em anexo para efetuar o pagamento e regularizar sua inscrição. Desconsidere qualquer boleto anterior.
            </li>
        </ul>

        <p>
            Pedimos desculpas pelo transtorno.
        </p>

        <div class="footer">
            Atenciosamente,<br>
            <strong>Centro de Estatística Aplicada (CEA) - IME USP</strong><br>
            <a href="mailto:{{ env('MAIL_CEA') ?? 'cea@ime.usp.br' }}">{{ env('MAIL_CEA') ?? 'cea@ime.usp.br' }}</a>
        </div>
    </div>
</body>
</html>
