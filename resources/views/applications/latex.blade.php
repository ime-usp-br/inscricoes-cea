\documentclass[12pt, portuguese, a4paper, pdftex, fleqn]{article}
\usepackage{adjustbox}
\usepackage[portuguese]{babel}
\usepackage[scaled=.92]{helvet}
\usepackage{fancyhdr}
\usepackage{float}
\usepackage{setspace}
\usepackage[hidelinks]{hyperref}
\usepackage[svgnames,table]{xcolor}
\usepackage{booktabs, makecell, longtable}
\usepackage[a4paper,inner=1.5cm,outer=1.5cm,top=1cm,bottom=1cm, headheight=2.5cm, footskip=2cm]{geometry}
\usepackage{blindtext}
\usepackage{pdflscape}
\usepackage{spverbatim}
\usepackage{xcolor}
\geometry{textwidth=\paperwidth, textheight=\paperheight, includehead, nomarginpar, includefoot}

\renewcommand{\familydefault}{\sfdefault}

\definecolor{ultramarine}{RGB}{34,57,114}

\pagestyle{fancy}
\fancyhead{}
\renewcommand{\headrulewidth}{0pt}

\setlength\parindent{0pt}

\fancyhead{} 
\fancyhead[L]{
  \raisebox{0\height} {\includegraphics[scale=0.37]{{!! base_path() . "/storage/app/images/logo_ime.png" !!}}}
}
\fancyhead[R]{
  \raisebox{0.1\height} {\includegraphics[scale=0.38]{{!! base_path() . "/storage/app/images/logo_usp.png" !!}}}
}
\fancyfoot{}
\fancyfoot[L]{
  \spaceskip=\fontdimen3\font
  \parbox[b]{15cm}{\textcolor{ultramarine}{\footnotesize{\textbf{CENTRO DE ESTATÍSTICA APLICADA}\\Rua do Matão, 1010 $|$ Cidade Universitária $|$ São Paulo-SP $|$ CEP 05508-090\\Tel: (11) 3091.6133 $|$ cea@ime.usp.br $|$ www.ime.usp.br/cea}}}
}
\begin{document}
\pagestyle{fancy}

  \textbf{Protocolo:} {!! $application->protocol !!}

  \vspace{5pt}

  \textbf{Data da inscrição:} {!! \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $application->created_at)->format("d/m/Y H:i") !!}

  \vspace{5pt}

  \textbf{Responsável(is) pelo projeto:} {!! clear_string($application->projectResponsible) !!}

  \vspace{5pt}

  \textbf{Telefones para contato:} {!! clear_string($application->contactPhone) !!}

  \vspace{5pt}

  \textbf{CPF/CNPJ:} {!! $application->CPFCNPJ !!}

  \vspace{5pt}

  \textbf{E-mail:} {!! clear_string($application->email) !!}

  \vspace{5pt}

  \textbf{Instituição:} {!! clear_string($application->institution) !!}

  \vspace{5pt}

  \textbf{Curso:} {!! clear_string($application->course) !!}

  \vspace{5pt}

  \textbf{Vínculo com a Instituição:} {!! clear_string($application->institutionRelationship) !!}

  \vspace{5pt}

  \textbf{Colaborador(es) ou orientador:} {!! clear_string($application->mentor) !!}

  \vspace{5pt}

  \textbf{Finalidade do projeto:} {!! str_replace(",", ", ", $application->projectPurpose) !!} {!! $application->ppOther ? " - ".str_replace("_", "\_", $application->ppOther) : "" !!}

  \vspace{5pt}

\textbf{Agência financiadora do projeto:} {!! str_replace(",", ", ", $application->fundingAgency) !!} {!! $application->faOther ? " - ".str_replace("_", "\_", $application->faOther) : "" !!}

\vspace{5pt}

\textbf{Área de conhecimento:} {!! str_replace(",", ", ", $application->knowledgeArea) !!} {!! $application->kaOther ? " - ".str_replace("_", "\_", $application->kaOther) : "" !!}

\vspace{30pt}

\hrule

\vspace{30pt}

\centerline{\textbf{Dados Bancários}}

\vspace{10pt}

\textbf{Nome completo:} {!! clear_string($application->bdName) !!}

\vspace{5pt}

\textbf{CPF/CNPJ:} {!! $application->bdCpfCnpj !!}

\vspace{5pt}

\textbf{Nome do Banco:} {!! clear_string($application->bdBankName) !!}

\vspace{5pt}

\textbf{Número da Agência:} {!! clear_string($application->bdAgency) !!}

\vspace{5pt}

\textbf{Número da Conta:} {!! clear_string($application->bdAccount) !!}

\vspace{5pt}

\textbf{Tipo da Conta:} {!! $application->bdType !!}

\vspace{5pt}

\textbf{Comprovante de pagamento da taxa:} \href{{!! $application->depositReceipt->link !!}}{{!! str_replace("_", "\_", $application->depositReceipt->name) !!}}

\vspace{30pt}

\hrule

\vspace{30pt}

\centerline{\textbf{A SER PREENCHIDO PELO CEA}}

\vspace{10pt}

\textbf{Data da 1ª reunião:}

\vspace{5pt}

\textbf{Decisão:}

\pagebreak

\textbf{1. Título do projeto, mesmo sendo provisório:}\\
{!! clear_string($application->projectTitle) !!}

\vspace{15pt}

\textbf{2. Aspectos gerais da área de concentração, com ênfase naqueles que motivaram o projeto:}\\
{!! clear_string($application->generalAspects) !!}

\vspace{15pt}

\textbf{3. Objetivos gerais:}\\
{!! clear_string($application->generalObjectives) !!}

\vspace{15pt}

\textbf{4. Que características (ou variáveis) foram ou serão observadas para atingir os objetivos? Como foram ou serão efetuadas as medidas dessas características (ou variáveis)? Quais as unidades de medida?}\\
{!! clear_string($application->features) !!}

\vspace{15pt}

\textbf{5. Que outras características (ou variáveis) poderiam influenciar essas medidas? Existe possibilidade destas serem controladas?}\\
{!! clear_string($application->otherFeatures) !!}

\vspace{15pt}

\textbf{6. Como foi (ou será) conduzida a investigação para que os objetivos do item 3 sejam atingidos? Quais as restrições que foram ou serão naturalmente impostas à coleta de dados? Quantas unidades amostrais* foram ou serão analisadas? Indique as limitações de tempo e custo.}\\
{!! clear_string($application->limitations) !!}

\vspace{15pt}

\textbf{7. Como os dados estão ou serão armazenados? Existe a possibilidade de apresentá-los em mídia eletrônica (CD, DVD, etc)?}\\
{!! clear_string($application->storage) !!}

\vspace{15pt}

\textbf{8. Supondo que os dados já tivessem sido analisados de forma apropriada, indique o tipo de conclusões que seriam satisfatórias, tendo em vista seu comentário no item 3. Simule resultados possíveis e comente-os.}\\
{!! clear_string($application->conclusions) !!}

\vspace{15pt}

\textbf{9. Que tipo de ajuda você espera do CEA?}\\
{!! clear_string($application->expectedHelp) !!}

\vspace{15pt}

\textbf{10. Caso seja pertinente, anexe a esta ficha de inscrição algum plano de pesquisa, relatório, resumo ou trabalho publicado que se relacione com este projeto.}

\vspace{5pt}

@if(!$application->attachments->isEmpty())
  @foreach($application->attachments as $attachment)
    \href{{!! $attachment->link !!}}{{!! str_replace("_", "\_", $attachment->name) !!}}

    \vspace{5pt}
  @endforeach
@else
  Não foram feitos anexos.
@endif




\end{document}