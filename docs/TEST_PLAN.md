# Plano de Testes: Inscrições CEA

Este documento descreve os casos de teste essenciais para validar o sistema Inscrições CEA, cobrindo fluxos felizes (Happy Paths) e casos de borda (Edge Cases). Além disso, fornece scripts JavaScript para automatizar a navegação e preenchimento de formulários via console do navegador (Google Chrome), facilitando a validação pelo agente Antigravity.

## 1. Estratégia de Testes
Os testes focam em validação End-to-End (E2E) dos principais fluxos de negócio:
1.  **Inscrição Pública**: Garantir que usuários externos consigam se inscrever e anexar arquivos.
2.  **Gestão Administrativa**: Garantir que admins consigam visualizar, editar e gerenciar boletos.
3.  **Financeiro**: Validar a lógica complexa de geração, regeneração e recálculo de boletos nas trocas de modalidade.

---

## 2. Casos de Teste (Test Cases)

### TC01: Inscrição de Projeto (Happy Path)
*   **Ator**: Usuário Público.
*   **Pré-condição**: Semestre ativo com inscrições abertas.
*   **Ação**: Acessar home, selecionar "Projeto", preencher todos os campos obrigatórios, anexar arquivo PDF e submeter.
*   **Resultado Esperado**:
    *   Redirecionamento para Home com mensagem de sucesso ("Sua inscrição foi efetuada...").
    *   E-mail de confirmação enviado ao usuário e ao CEA.
    *   Boleto de R$ 80,00 gerado e salvo no banco.

### TC02: Inscrição de Consulta (Happy Path)
*   **Ator**: Usuário Público.
*   **Pré-condição**: Semestre ativo com inscrições abertas.
*   **Ação**: Acessar home, selecionar "Consulta", preencher campos, submeter.
*   **Resultado Esperado**:
    *   Mensagem de sucesso.
    *   Boleto de R$ 140,00 gerado.

### TC03: Login Administrativo
*   **Ator**: Admin/Secretaria.
*   **Ação**: Acessar `/login`, autenticar via Senha Única (mockada em env local).
*   **Resultado Esperado**: Redirecionamento para Dashboard/Lista de Inscrições.

### TC04: Mudança de Modalidade (Projeto -> Consulta) com Pagamento Pendente
*   **Ator**: Admin.
*   **Cenário**: Inscrição de Projeto (R$ 80) não paga.
*   **Ação**: Clicar em "Alternar para Consulta".
*   **Resultado Esperado**:
    *   Modalidade atualizada para "Consulta".
    *   Boleto de R$ 80 cancelado/substituído.
    *   Novo boleto de R$ 140 gerado (Taxa de Inscrição integral).

### TC05: Mudança de Modalidade (Projeto -> Consulta) com Pagamento Realizado
*   **Ator**: Admin.
*   **Cenário**: Inscrição de Projeto (R$ 80) **PAGA**.
*   **Ação**: Clicar em "Alternar para Consulta".
*   **Resultado Esperado**:
    *   Boleto original mantido como "Pago".
    *   Boleto de "Complemento de Taxa" de R$ 60,00 gerado.

### TC06: Regeneração de Boleto
*   **Ator**: Admin.
*   **Ação**: Na ficha de inscrição, clicar em "Regerar Boleto".
*   **Resultado Esperado**:
    *   Mensagem de sucesso.
    *   Novo boleto listado na seção financeira da ficha.
    *   Email de notificação enviado ao usuário.

### TC07: Bloqueio de Inscrição Fora de Prazo (Edge Case)
*   **Pré-condição**: Alterar data de `end_date` do semestre no banco para o passado.
*   **Ação**: Tentar submeter formulário de Projeto.
*   **Resultado Esperado**: Erro "Fora do período de inscrição para projetos" e redirecionamento para home.

---

## 3. Scripts de Automação (Browser Console / Antigravity)

Estes scripts podem ser executados no console do navegador (F12) ou injetados pela ferramenta `browser_subagent` para preencher formulários rapidamente.

### Script 01: Preencher Formulário de Projeto (Dados Mock)
Use este script na página `/` (Home) ou `/criar-inscricao`.

```javascript
(function fillProjectForm() {
    // Helper para definir valores
    function setVal(selector, value) {
        let el = document.querySelector(selector);
        if (el) {
            el.value = value;
            el.dispatchEvent(new Event('input', { bubbles: true }));
            el.dispatchEvent(new Event('change', { bubbles: true }));
        } else {
            console.warn('Element not found:', selector);
        }
    }

    // Helper para marcar checkboxes/radios
    function check(selector) {
        let el = document.querySelector(selector);
        if (el) { 
            el.checked = true; 
            el.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    // Dados Pessoais
    setVal('input[name="bdName"]', "Teste Automatizado Silva");
    setVal('input[name="bdCpfCnpj"]', "123.456.789-00");
    setVal('input[name="email"]', "teste@example.com");
    setVal('input[name="contactPhone"]', "(11) 99999-9999");
    setVal('input[name="institution"]', "IME-USP");
    setVal('input[name="course"]', "Estatística");
    
    // Checkboxes (Arrays)
    check('input[name="institutionRelationship[]"][value="Aluno de graduação"]');
    check('input[name="institutionRelationship[]"][value="Funcionário"]');

    // Dados do Projeto
    check('input[name="serviceType"][value="Projeto"]');
    setVal('input[name="projectTitle"]', "Análise de Dados Automatizada via Selenium");
    setVal('textarea[name="projectPurpose[]"]', "Trabalho de Conclusão de Curso"); // Adapte se for checkbox
    
    setVal('textarea[name="generalAspects"]', "Aspectos gerais de teste...");
    setVal('textarea[name="generalObjectives"]', "Objetivo é validar o form.");
    setVal('textarea[name="features"]', "Features testadas.");
    setVal('textarea[name="limitations"]', "Sem limitações.");
    setVal('textarea[name="storage"]', "Banco de dados MySQL.");
    setVal('textarea[name="conclusions"]', "Conclusão esperada é sucesso.");
    setVal('textarea[name="expectedHelp"]', "Ajuda na automação.");

    // Dados Bancários (para devolução)
    if(document.querySelector('input[name="bdBankName"]')) {
        setVal('input[name="bdBankName"]', "Banco do Brasil");
        setVal('input[name="bdAgency"]', "1234-5");
        setVal('input[name="bdAccount"]', "54321-X");
    }

    // Selecionar "Não" para reembolso se existir
    check('input[name="refundReceipt"][value="Não"]');

    console.log("Formulário preenchido via script!");
})();
```

### Script 02: Login como Admin (Ambiente Local)
Se o ambiente estiver configurado com login de desenvolvedor (comumente `/users/loginas` em dev).

```javascript
(function loginAsAdmin() {
    window.location.href = "/users/loginas?user_id=1"; // Ajuste o ID conforme o seed do banco
})();
```

### Script 03: Navegar para Ficha Específica
```javascript
(function goToApplication(protocol) {
    // Procura na tabela da dashboard o link para o protocolo
    let link = Array.from(document.querySelectorAll('a')).find(el => el.textContent.includes(protocol));
    if (link) {
        link.click();
    } else {
        console.error("Protocolo não encontrado na lista: " + protocol);
    }
})('000001234'); // Exemplo de protocolo
```

### Script 04: Disparar Mudança de Modalidade
```javascript
(function changeServiceType() {
    let btn = document.querySelector('button[class*="btn-outline-danger"]');
    if (btn && btn.textContent.includes('Alternar para')) {
        // Sobrescrever confirm para não bloquear a automação
        window.confirm = () => true;
        btn.click();
        console.log("Botão de alterar modalidade clicado.");
    } else {
        console.error("Botão de mudança de modalidade não encontrado.");
    }
})();
```

---

## 4. Cobertura de Código Sugerida (PHPUnit)
Além dos testes de interface, recomenda-se garantir cobertura nas seguintes classes:

1.  **`ApplicationControllerTest`**:
    *   `testStoreProject`: Valida criação correta.
    *   `testStoreOutsidePeriod`: Valida rejeição de data.
    *   `testChangeServiceTypeCalculatesFee`: Mockar `BankSlip` e verificar se o método correto é chamado (integral vs complemento).
    
2.  **`BankSlipTest`**:
    *   Validar integração SOAP (usando Mocks).
    *   Testar regras de `getAggregatedInscriptionFeeStatus`.
