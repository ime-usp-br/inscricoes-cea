# Documentação do Projeto: Inscrições CEA

## 1. Visão Geral
O sistema **Inscrições CEA** é uma aplicação web desenvolvida para gerenciar o processo de inscrição, triagem e agendamento de serviços oferecidos pelo Centro de Estatística Aplicada (CEA) do IME-USP. O sistema permite que interessados submetam projetos acadêmicos ou solicitem consultas estatísticas, gerindo todo o fluxo desde a inscrição inicial e pagamento de taxas até a triagem e agendamento.

## 2. Tecnologias Utilizadas
*   **Framework**: Laravel 8
*   **Linguagem**: PHP 7.4+
*   **Banco de Dados**: MySQL / MariaDB
*   **Front-end**: Blade Templates com integração `uspdev/laravel-usp-theme` (Bootstrap).
*   **Autenticação**: OAuth via Senha Única USP (`uspdev/senhaunica-socialite`).
*   **Integrações**: 
    *   `uspdev/replicado` (Dados institucionais USP).
    *   SOAP Client (Geração de Boletos Bancários).
    *   Google reCAPTCHA (Validação de formulários).
*   **Geração de Documentos**: `ismaelw/laratex` (Geração de PDFs via LaTeX).

## 3. Perfis e Permissões
O sistema opera com dois níveis principais de acesso:

### 3.1. Público (Externo)
*   **Acesso**: Livre (não requer login).
*   **Permissões**:
    *   Visualizar página inicial.
    *   Realizar nova inscrição (preenchimento de formulário extenso com detalhes do projeto/consulta).
    *   Realizar upload de anexos durante a inscrição.

### 3.2. Administrativo (Interno)
*   **Quem**: Usuários com papéis `Administrador`, `Secretaria` ou `Docente`.
*   **Acesso**: Requer login via Senha Única USP.
*   **Permissões**: Acesso total ao painel gerenciamento, visualização de inscritos, e ações sobre processos.

## 4. Funcionalidades Detalhadas

### 4.1. Gestão de Semestres (`SemesterController`)
O sistema organiza as inscrições por períodos (semestres).
*   **Cadastro de Semestres**: Definição de datas de início e fim para inscrições.
*   **Controle de Abertura**: O sistema valida automaticamente se a data atual está dentro do período de inscrição ativo para permitir novos cadastros.

### 4.2. Processo de Inscrição (`ApplicationController`)
O núcleo do sistema.
*   **Formulário de Inscrição**: Coleta dados pessoais, institucionais (vínculo USP ou externo), e detalhes do projeto/pesquisa (Objetivos, Método, Coleta de Dados, etc.).
*   **Protocolo**: Geração automática de um número de protocolo único (9 dígitos).
*   **Anexos**: Upload de múltiplos arquivos relacionados ao projeto.
*   **Validação**: Integração com Google reCAPTCHA para evitar spam.
*   **Notificações**: Envio automático de e-mail de confirmação para o inscrito e para a administração do CEA, utilizando templates customizáveis.

### 4.3. Financeiro e Boletos (`BankSlip`)
Automatização da cobrança de taxas.
*   **Geração Automática**: Ao finalizar a inscrição, o sistema comunica-se via SOAP com o sistema bancário para gerar um boleto registrado.
    *   *Projetos*: Taxa de R$ 80,00.
    *   *Consultas*: Taxa de R$ 140,00.
*   **Regeneração**: Funcionalidade para administradores gerarem novos boletos caso o original expire ou haja falha na comunicação inicial.
*   **Mudança de Serviço (Upgrade/Downgrade)**:
    *   O sistema permite alterar uma inscrição de "Projeto" para "Consulta" (e vice-versa).
    *   Calcula automaticamente a diferença de valores.
    *   Gera boletos de "Complemento de Taxa" (ex: R$ 60,00 de diferença) ou cancela o boleto anterior e gera um novo integral, dependendo do status do pagamento.
*   **Download**: Download do PDF do boleto (decodificado de Base64 retornado pelo SOAP).

### 4.4. Gestão de Inscrições
Painel administrativo para acompanhamento.
*   **Listagem**: Visualização de todas as fichas do semestre ativo.
*   **Filtros**: Por status, semestre, tipos.
*   **PDF da Ficha**: Geração de PDF completo da inscrição utilizando templates LaTeX (`applications.latex`).
*   **Lixeira**: Soft-delete (exclusão lógica) e restauração de inscrições.

### 4.5. Fluxo de Triagem (`TriageController`)
Aplicável para inscrições do tipo **Projeto**.
*   **Agendamento**: Marcar data e horário para triagem.
*   **Parecer**: Registro de feedback e decisão sobre a aceitação do projeto.
*   **Status**: Evolução do status (ex: "Aguardando triagem", "Em análise").

### 4.6. Fluxo de Consultas (`ConsultationMeetingController`)
Aplicável para inscrições do tipo **Consulta**.
*   **Agendamento**: Gestão de reuniões de consulta.
*   **Parecer/Feedback**: Registro do resultado da consulta.

### 4.7. Comunicação (`MailTemplateController`)
Sistema flexível de emails.
*   **Templates**: Interface para criar e editar textos dos emails enviados pelo sistema (confirmação, cobrança, agendamento).
*   **Gatilhos**: Associação de templates a eventos do sistema (ex: "Ao inscrever-se", "Ao gerar novo boleto").

### 4.8. Eventos e Auditoria (`EventController`)
*   Registro automático de ações importantes (Inscrição realizada, Status alterado) para histórico e auditoria do processo.

## 5. Integrações Externas
*   **USP Senha Única**: O sistema delega a autenticação para os servidores da USP, garantindo que apenas pessoas autorizadas (base `users` local sincronizada ou verificada) acessem a área administrativa.
*   **Web Services Bancários (SOAP)**: Conexão crítica para a emissão de boletos registrados em tempo real. O sistema possui tratamento de erros (try/catch) para notificar administradores em caso de falha na comunicação bancária.
