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

  \textbf{Protocolo:} {!! str_replace("_", "\_", $application->protocol) !!}

  \vspace{5pt}

  \textbf{Data da inscrição:} {!! \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $application->created_at)->format("d/m/Y H:i") !!}

  \vspace{5pt}

  \textbf{Responsável(is) pelo projeto:} {!! str_replace("_", "\_", $application->projectResponsible) !!}

  \vspace{5pt}

  \textbf{Telefones para contato:} {!! str_replace("_", "\_", $application->contactPhone) !!}

  \vspace{5pt}

  \textbf{CPF/CNPJ:} {!! $application->CPFCNPJ !!}

  \vspace{5pt}

  \textbf{E-mail:} {!! str_replace("_", "\_", $application->email) !!}

  \vspace{5pt}

  \textbf{Instituição:} {!! str_replace("_", "\_", $application->institution) !!}

  \vspace{5pt}

  \textbf{Curso:} {!! str_replace("_", "\_", $application->course) !!}

  \vspace{5pt}

  \textbf{Vínculo com a Instituição:} {!! str_replace("_", "\_", $application->institutionRelationship) !!}

  \vspace{5pt}

  \textbf{Colaborador(es) ou orientador:} {!! str_replace("_", "\_", $application->mentor) !!}

  \vspace{5pt}

  \textbf{Finalidade do projeto:} {!! str_replace(",", ", ", $application->projectPurpose) !!} {!! $application->ppOther ? " - ".str_replace("_", "\_", $application->ppOther) : "" !!}

  \vspace{5pt}

\textbf{Agência financiadora do projeto:} {!! str_replace(",", ", ", $application->fundingAgency) !!} {!! $application->faOther ? " - ".str_replace("_", "\_", $application->faOther) : "" !!}

\vspace{5pt}

\textbf{Área de conhecimento:} {!! str_replace(",", ", ", $application->knowledgeArea) !!} {!! $application->kaOther ? " - ".str_replace("_", "\_", $application->kaOther) : "" !!}

\vspace{20pt}

\hrule

\vspace{20pt}

\centerline{\textbf{Dados Bancários}}

\vspace{10pt}

\textbf{Nome completo:} {!! str_replace("_", "\_", $application->bdName) !!}

\vspace{5pt}

\textbf{CPF/CNPJ:} {!! $application->bdCpfCnpj !!}

\vspace{5pt}

\textbf{Nome do Banco:} {!! str_replace("_", "\_", $application->bdBankName) !!}

\vspace{5pt}

\textbf{Número da Agência:} {!! str_replace("_", "\_", $application->bdAgency) !!}

\vspace{5pt}

\textbf{Número da Conta:} {!! str_replace("_", "\_", $application->bdAccount) !!}

\vspace{5pt}

\textbf{Tipo da Conta:} {!! $application->bdType !!}

\vspace{5pt}

@if($application->depositReceipt)
  \textbf{Comprovante de pagamento da taxa:} \href{{!! $application->depositReceipt->link !!}}{{!! clear_string($application->depositReceipt->name) !!}}
@else
  \textbf{Comprovante de pagamento da taxa:} Inscrição feita após implementação do boleto.
@endif

\vspace{5pt}

@if($application->refundReceipt == "Sim")

    \textbf{Dados que devem constar no recibo:}\\
    @foreach(explode("\n", $application->refundReceiptData) as $line)
        {!! $line !!}\\
    @endforeach

@endif
\end{document}