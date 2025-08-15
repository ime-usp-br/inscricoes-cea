@extends('layouts.app')

@section('content')
@parent

<div id="layout_conteudo">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-11 col-md-10 col-lg-9 col-xl-8">
            @if($semester)
                <h2 class='text-center mt-4'>FICHA DE INSCRIÇÃO PARA ASSESSORIA ESTATÍSTICA</h2>
                <h4 class='text-center pb-4'>{{ $semester->period }} de {{ $semester->year }}</h4>


                <div id="div-info">
                </div>

                <form id="form-inscricao" method="POST" action="{{ route('applications.store') }}" enctype='multipart/form-data'>
                    @csrf

                    <input type="hidden" id="isEnrollmentPeriod" value="{{ $semester->IsEnrollmentPeriod() }}">
                    <div class="row custom-form-group d-flex align-items-center">
                        <div class="col-12 col-md-auto text-md-right">
                            <label id="serviceType">Modalidade do serviço solicitado:</label>
                        </div>
                        <div class="col-12 col-md">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="serviceType" value="Consulta" onClick="rdChange(this)" required {{ old("serviceType")=="Consulta" ? "checked" : "" }}>
                                <label class="font-weight-normal">Consulta</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="serviceType" value="Projeto" onClick="rdChange(this)" required {{ old("serviceType")=="Projeto" ? "checked" : "" }}>
                                <label class="font-weight-normal">Projeto</label>
                            </div>
                        </div>
                    </div>

                    <div class="row custom-form-group d-flex align-items-center">
                        <div class="col-12 col-md-auto text-md-right">
                            <label for="projectResponsible">Nome do pesquisador:</label>
                        </div>
                        <div class="col-12 col-md">
                            <input class="custom-form-control" type="text" name="projectResponsible" id="projectResponsible" required value={{ old("projectResponsible") ?? '' }}>
                        </div>        
                    </div>

                    <div class="row custom-form-group d-flex align-items-center">
                        <div class="col-12 col-md-auto text-md-right">
                            <label for="contactPhone">Telefones para contato:</label>
                        </div>
                        
                        <div class="col col-md-auto">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="wpp" name="whatsapp" value='1' {{ old("whatsapp") ? "checked" : '' }}>
                                <label class="font-weight-normal">Whatsapp</label>
                            </div>
                        </div>
                        <div class="col-12 col-md">
                            <input class="custom-form-control" type="text" name="contactPhone" id="contactPhone" required value={{ old("contactPhone") ?? '' }}>
                        </div>
                    </div>

                    <div class="row custom-form-group d-flex align-items-center">
                        <div class="col-12 col-md-auto text-md-right">
                            <label for="CPFCNPJ">CPF/CNPJ:</label>
                        </div>
                        <div class="col-12 col-md">
                            <input class="custom-form-control masked" type="text" name="CPFCNPJ" id="CPFCNPJ" required value={{ old("CPFCNPJ") ?? '' }}>
                        </div>
                    </div>

                    <div class="row custom-form-group d-flex align-items-center">
                        <div class="col-12 col-md-auto text-md-right">
                            <label for="email">E-mail:</label>
                        </div>
                        <div class="col-12 col-md">
                            <input class="custom-form-control" type="text" name="email" id="email" required value={{ old("email") ?? '' }}>
                        </div>
                    </div>

                    <div class="row custom-form-group d-flex align-items-center">
                        <div class="col-12 col-md-auto text-md-right">
                            <label for="emailConfirmation">Repetir e-mail:</label>
                        </div>
                        <div class="col-12 col-md">
                            <input class="custom-form-control" type="text" name="emailConfirmation" id="emailConfirmation">
                        </div>
                    </div>

                    <div class="row custom-form-group d-flex align-items-center">
                        <div class="col-12 col-md-auto text-md-left">
                            <label for="institution">Instituição/Unidade:</label>
                        </div>
                        <div class="col-12 col-md">
                            <input class="custom-form-control" type="text" name="institution" id="institution" required value={{ old("institution") ?? '' }}>
                        </div>
                    </div>

                    <div class="row custom-form-group d-flex align-items-center">
                        <div class="col-12 col-md-auto text-md-left">
                            <label for="course">Curso:</label>
                        </div>
                        <div class="col-12 col-md">
                            <input class="custom-form-control" type="text" name="course" id="course" value={{ old("course") ?? '' }}>
                        </div>
                    </div>

                    <div class="row custom-form-group d-flex align-items-center">
                        <div class="col-12 col-md-auto text-md-right">
                            <label id="institutionRelationship">Vínculo com a Instituição:</label>
                        </div>
                        <div class="col-12 col-md">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="ckirStudent" name="institutionRelationship[]" value="Estudante" onClick="ckChange(this)" {{ (is_array(old("institutionRelationship")) and in_array("Estudante", old("institutionRelationship"))) ? "checked" : '' }}>
                                <label class="font-weight-normal">Estudante</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="ckirEmployee" name="institutionRelationship[]" value="Funcionário" onClick="ckChange(this)" {{ (is_array(old("institutionRelationship")) and in_array("Funcionário", old("institutionRelationship"))) ? "checked" : '' }}>
                                <label class="font-weight-normal">Funcionário</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="ckirProfessor" name="institutionRelationship[]" value="Professor" onClick="ckChange(this)" {{ (is_array(old("institutionRelationship")) and in_array("Professor", old("institutionRelationship"))) ? "checked" : '' }}>
                                <label class="font-weight-normal">Professor</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="ckirOther" name="institutionRelationship[]" value="Outro" onClick="ckChange(this)" {{ (is_array(old("institutionRelationship")) and in_array("Outro", old("institutionRelationship"))) ? "checked" : '' }}>
                                <label class="font-weight-normal">Outro</label>
                                <input class="custom-form-control ml-2" type="text" name="irOther" id="irOther" placeholder="Especifique" {{ (is_array(old("institutionRelationship")) and in_array("Outro", old("institutionRelationship"))) ? '' : 'disabled' }} value={{ old("ppOther") ?? '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="row custom-form-group d-flex align-items-center">
                        <div class="col-12 col-md-auto text-md-right">
                            <label for="mentor">Colaborador(es) ou orientador:</label>
                        </div>
                        <div class="col-12 col-md">
                            <input class="custom-form-control" id="mentor" type="text" name="mentor" id="mentor" value={{ old("mentor") ?? '' }}>
                        </div>
                    </div>

                    <div class="row custom-form-group d-flex align-items-center">
                        <div class="col-12 col-md-auto text-md-right">
                            <label id="ppLabel">Finalidade do projeto:</label>
                        </div>
                        <div class="col-12 col-md">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="ckppIniCientifica" name="projectPurpose[]" value="Iniciação Científica" onClick="ckChange(this)" {{ (is_array(old("projectPurpose")) and in_array("Iniciação Científica", old("projectPurpose"))) ? "checked" : '' }}>
                                <label class="font-weight-normal">Iniciação Científica</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="ckppMestrado" name="projectPurpose[]" value="Mestrado" onClick="ckChange(this)" {{ (is_array(old("projectPurpose")) and in_array("Mestrado", old("projectPurpose"))) ? "checked" : '' }}>
                                <label class="font-weight-normal">Mestrado</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="ckppDoutorado" name="projectPurpose[]" value="Doutorado" onClick="ckChange(this)" {{ (is_array(old("projectPurpose")) and in_array("Doutorado", old("projectPurpose"))) ? "checked" : '' }}>
                                <label class="font-weight-normal">Doutorado</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="ckppLivreDocencia" name="projectPurpose[]" value="Livre Docência" onClick="ckChange(this)" {{ (is_array(old("projectPurpose")) and in_array("Livre Docência", old("projectPurpose"))) ? "checked" : '' }}>
                                <label class="font-weight-normal">Livre Docência</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="ckppPublicacao" name="projectPurpose[]" value="Publicação" onClick="ckChange(this)" {{ (is_array(old("projectPurpose")) and in_array("Publicação", old("projectPurpose"))) ? "checked" : '' }}>
                                <label class="font-weight-normal">Publicação</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="ckppOther" name="projectPurpose[]" value="Outra" onClick="ckChange(this)" {{ (is_array(old("projectPurpose")) and in_array("Outra", old("projectPurpose"))) ? "checked" : '' }}>
                                <label class="font-weight-normal">Outra</label>
                                <input class="custom-form-control ml-2" type="text" name="ppOther" id="ppOther" placeholder="Especifique" {{ (is_array(old("projectPurpose")) and in_array("Outra", old("projectPurpose"))) ? '' : 'disabled' }} value={{ old("ppOther") ?? '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="row custom-form-group d-flex align-items-center">
                        <div class="col-12 col-md-auto text-md-right">
                            <label for="fundingAgency">Agência financiadora do projeto:</label>
                        </div>
                        <div class="col-12 col-md">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="ckfaFAPESP" name="fundingAgency[]" value="FAPESP" onClick="ckChange(this)" {{ (is_array(old("fundingAgency")) and in_array("FAPESP", old("fundingAgency"))) ? "checked" : '' }}>
                                <label class="font-weight-normal">FAPESP</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="ckfaFINEP" name="fundingAgency[]" value="FINEP" onClick="ckChange(this)" {{ (is_array(old("fundingAgency")) and in_array("FINEP", old("fundingAgency"))) ? "checked" : '' }}>
                                <label class="font-weight-normal">FINEP</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="ckfaCNPq" name="fundingAgency[]" value="CNPq" onClick="ckChange(this)" {{ (is_array(old("fundingAgency")) and in_array("CNPq", old("fundingAgency"))) ? "checked" : '' }}>
                                <label class="font-weight-normal">CNPq</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="ckfaOther" name="fundingAgency[]" value="Outra" onClick="ckChange(this)" {{ (is_array(old("fundingAgency")) and in_array("Outra", old("fundingAgency"))) ? "checked" : '' }}>
                                <label class="font-weight-normal">Outra</label>
                                <input class="custom-form-control ml-2" type="text" name="faOther" id="faOther" placeholder="Especifique" {{ (is_array(old("fundingAgency")) and in_array("Outra", old("fundingAgency"))) ? '' : 'disabled' }} value={{ old("faOther") ?? '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="row custom-form-group d-flex align-items-center">
                        <div class="col-12 col-md-auto text-md-right">
                            <label for="knowledgeArea">Área de conhecimento:</label>
                        </div>
                        <div class="col-12 col-md">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="ckkaTec" name="knowledgeArea[]" value="Tecnológica" onClick="ckChange(this)" {{ (is_array(old("knowledgeArea")) and in_array("Tecnológica", old("knowledgeArea"))) ? "checked" : '' }}>
                                <label class="font-weight-normal">Tecnológica</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="ckkaMedBio" name="knowledgeArea[]" value="Médica ou Biológica" onClick="ckChange(this)" {{ (is_array(old("knowledgeArea")) and in_array("Médica ou Biológica", old("knowledgeArea"))) ? "checked" : '' }}>
                                <label class="font-weight-normal">Médica ou Biológica</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="ckkaSocHum" name="knowledgeArea[]" value="Social ou Humana" onClick="ckChange(this)" {{ (is_array(old("knowledgeArea")) and in_array("Social ou Humana", old("knowledgeArea"))) ? "checked" : '' }}>
                                <label class="font-weight-normal">Social ou Humana</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="ckkaEco" name="knowledgeArea[]" value="Econômica" onClick="ckChange(this)" {{ (is_array(old("knowledgeArea")) and in_array("Econômica", old("knowledgeArea"))) ? "checked" : '' }}>
                                <label class="font-weight-normal">Econômica</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="ckkaOther" name="knowledgeArea[]" value="Outra" onClick="ckChange(this)" {{ (is_array(old("knowledgeArea")) and in_array("Outra", old("knowledgeArea"))) ? "checked" : '' }}>
                                <label class="font-weight-normal">Outra</label>
                                <input class="custom-form-control ml-2" type="text" name="kaOther" id="kaOther" placeholder="Especifique" {{ (is_array(old("knowledgeArea")) and in_array("Outra", old("knowledgeArea"))) ? '' : 'disabled' }} value={{ old("kaOther") ?? '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="row custom-form-group d-flex align-items-center">
                        <div class="col-12 col-md-auto text-md-right">
                            <label id="refundReceipt">Recibo para reembolso:</label>
                        </div>
                        <div class="col-12 col-md">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="rdrrSim" name="refundReceipt" value="Sim" onClick="rdChange(this)" required {{ old("refundReceipt")=="Sim" ? "checked" : "" }}>
                                <label class="font-weight-normal">Sim</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="refundReceipt" value="Não" onClick="rdChange(this)" required {{ old("refundReceipt")=="Não" ? "checked" : "" }}>
                                <label class="font-weight-normal">Não</label>
                            </div>
                        </div>
                    </div>

                    <div class="custom-form-group">
                        <textarea class="custom-form-control" type="text" name="refundReceiptData" id="refundReceiptData" placeholder="Informe os dados que devem constar no recibo" hidden>{{ old('refundReceiptData') ?? ''}}</textarea>
                    </div>

                    <hr class="my-5">

                    <div class="col my-5 text-justify">
                        <h5>
                            Dados bancarios para emissão do boleto e devolução da taxa em caso de não poder ser feita a consulta.
                        </h5>
                    </div>

                    <div class="row custom-form-group d-flex align-items-center">
                        <div class="col-12 col-md-auto text-md-right">
                            <label for="projectResponsible">Nome completo:</label>
                        </div>
                        <div class="col-12 col-md">
                            <input class="custom-form-control" type="text" name="bdName" id="bdName" required value={{ old("bdName") ?? '' }}>
                        </div>        
                    </div>

                    <div class="row custom-form-group d-flex align-items-center">
                        <div class="col-12 col-md-auto text-md-right">
                            <label for="cpf-cnpj">CPF/CNPJ:</label>
                        </div>
                        <div class="col-12 col-md">
                            <input class="custom-form-control masked" type="text" name="bdCpfCnpj" id="bdCpfCnpj" required value={{ old("bdCpfCnpj") ?? '' }}>
                        </div>
                    </div>

                    <div class="row custom-form-group d-flex align-items-center">
                        <div class="col-12 col-md-auto text-md-right">
                            <label for="projectResponsible">Nome do Banco:</label>
                        </div>
                        <div class="col-12 col-md">
                            <input class="custom-form-control" type="text" name="bdBankName" id="bdBankName" required value={{ old("bdBankName") ?? '' }}>
                        </div>        
                    </div>

                    <div class="row custom-form-group d-flex align-items-center">
                        <div class="col-12 col-md-auto text-md-right">
                            <label for="projectResponsible">Número da Agência (sem DV):</label>
                        </div>
                        <div class="col-12 col-md">
                            <input class="custom-form-control" type="text" name="bdAgency" id="bdAgency" required value={{ old("bdAgency") ?? '' }}>
                        </div>        
                    </div>

                    <div class="row custom-form-group d-flex align-items-center">
                        <div class="col-12 col-md-auto text-md-right">
                            <label for="projectResponsible">Número da Conta:</label>
                        </div>
                        <div class="col-12 col-md">
                            <input class="custom-form-control" type="text" name="bdAccount" id="bdAccount" required value={{ old("bdAccount") ?? '' }}>
                        </div>        
                    </div>

                    <div class="row custom-form-group d-flex align-items-center">
                        <div class="col-12 col-md-auto text-md-right">
                            <label for="knowledgeArea">Tipo da Conta:</label>
                        </div>
                        <div class="col-12 col-md">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="bdCorrente" name="bdType" value="Corrente" required {{ old("bdType")=="Corrente" ? "checked" : "" }}>
                                <label class="font-weight-normal">Conta Corrente</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="bdPoupanca" name="bdType" value="Poupança" required {{ old("bdType")=="Poupança" ? "checked" : "" }}>
                                <label class="font-weight-normal">Poupança</label>
                            </div>
                        </div>
                    </div>

                    <div class="col my-5 text-justify">
                        <div class="card bg-light">
                            <div class="card-body">
                                <b>A DEVOLUÇÃO DA TAXA DE INSCRIÇÃO EM CASO DE DESISTÊNCIA ESTÁ SUJEITA A ANÁLISE.<b>
                                <br> <br>
                                <b>A SUA INSCRIÇÃO SÓ SERÁ CONFIRMADA NA TRIAGEM APÓS O PAGAMENTO DO BOLETO BANCÁRIO.<b>
                            </div>
                        </div>                            
                    </div>

                    <hr class="my-5">

                    <div class="custom-form-group d-flex align-items-center">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input mr-3" type="checkbox" name="authorization" value="1" required {{ old("authorization") ? "checked" : "" }}>
                            <label class="text-justify" >
                                Autorizo a utilização dos dados para fins didáticos e/ou ilustração de métodos estatísticos em
                                artigos científicos, desde que sejam apresentados em simpósios ou publicações com maior
                                concentração na área de Estatística. Em qualquer circunstância, a fonte será citada
                                explicitamente.
                            </label>
                        </div>
                    </div>

                    <hr class="my-5">

                    <div class="custom-form-group d-flex align-items-center">
                        <div class="form-check form-check-inline d-flex align-items-center">
                            <input class="form-check-input mr-3" id="ckDeclaration" type="checkbox" name="declaration" value="1" {{ old("declaration") ? "checked" : "" }}>
                            <label class="text-justify" >
                                Declaro que estou ciente de que o(a) meu/minha orientador(a) deverá estar presente na entrevista.
                            </label>
                        </div>
                    </div>

                    <hr class="my-5">

                    <div class="col my-5 text-justify">
                        <h5>Com o intuito de facilitar o trabalho dos consultores do CEA gostaríamos que fosse apresentada
                        uma descrição sucinta do projeto, salientando os aspectos indicados a seguir (os itens 8 e 9
                        são muito importantes). Termos técnicos estatísticos devem ser evitados e aqueles pertinentes à
                        área de concentração devem ser explicados</h5>
                    </div>

                    <div class="row custom-form-group d-flex align-items-center mb-5">
                        <div class="col-12 col-md-auto text-md-right">
                            <label>Os dados já foram coletados:</label>
                        </div>
                        <div class="col-12 col-md">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="dataCollect" value="Sim" onClick="rdChange(this)" required {{ old("dataCollect")=="Sim" ? "checked" : "" }}>
                                <label class="font-weight-normal">Sim</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="dataCollect" value="Não" onClick="rdChange(this)" required {{ old("dataCollect")=="Não" ? "checked" : "" }}>
                                <label class="font-weight-normal">Não</label>
                            </div>
                        </div>
                    </div>

                    <div class="custom-form-group">
                        <label for="projectTitle">1. Título do projeto, mesmo sendo provisório:</label>
                        <input class="custom-form-control" type="text" name="projectTitle" id="projectTitle" required value={{ old("projectTitle") ?? '' }}>
                    </div>

                    <div class="custom-form-group mt-5">
                        <label for="generalAspects">2. Aspectos gerais da área de concentração, com ênfase naqueles que motivaram o projeto:</label>
                        <textarea class="custom-form-control" type="text" name="generalAspects" id="generalAspects" required>{{ old('generalAspects') ?? ''}}</textarea>
                    </div>

                    <div class="custom-form-group mt-5">
                        <label for="generalObjectives">3. Objetivos gerais:</label>
                        <textarea class="custom-form-control" type="text" name="generalObjectives" id="generalObjectives" required>{{ old('generalObjectives') ?? ''}}</textarea>
                    </div>

                    <div class="custom-form-group mt-5">
                        <label class="text-justify" for="features">4. Que características (ou variáveis) foram ou serão observadas para atingir os objetivos? Como
                            foram ou serão efetuadas as medidas dessas características (ou variáveis)? Quais as unidades
                            de medida?</label>
                        <textarea class="custom-form-control" type="text" name="features" id="features" required>{{ old('features') ?? ''}}</textarea>
                    </div>

                    <div class="custom-form-group mt-5">
                        <label class="text-justify" for="otherFeatures">5. Que outras características (ou variáveis) poderiam influenciar essas medidas? Existe possibilidade
                            destas serem controladas?</label>
                        <textarea class="custom-form-control" type="text" name="otherFeatures" id="otherFeatures" required>{{ old('otherFeatures') ?? ''}}</textarea>
                    </div>

                    <div class="custom-form-group mt-5">
                        <label class="text-justify" for="limitations">6. Como foi (ou será) conduzida a investigação para que os objetivos do item 3 sejam atingidos?
                            Quais as restrições que foram ou serão naturalmente impostas à coleta de dados? Quantas
                            unidades amostrais* foram ou serão analisadas? Indique as limitações de tempo e custo.</label>
                        <textarea class="custom-form-control" type="text" name="limitations" id="limitations" required>{{ old('limitations') ?? ''}}</textarea>
                    </div>

                    <div class="custom-form-group mt-5">
                        <label class="text-justify" for="storage">7. Como os dados estão ou serão armazenados? Existe a possibilidade de apresentá-los em mídia
                            eletrônica (CD, DVD, etc)?</label>
                        <textarea class="custom-form-control" type="text" name="storage" id="storage" required>{{ old('storage') ?? ''}}</textarea>
                    </div>

                    <div class="custom-form-group mt-5">
                        <label class="text-justify" for="conclusions">8. Supondo que os dados já tivessem sido analisados de forma apropriada, indique o tipo de
                            conclusões que seriam satisfatórias, tendo em vista seu comentário no item 3. Simule resultados
                            possíveis e comente-os.</label>
                        <textarea class="custom-form-control" type="text" name="conclusions" id="conclusions" required>{{ old('conclusions') ?? ''}}</textarea>
                    </div>

                    <div class="custom-form-group mt-5">
                        <label for="expectedHelp">9. Que tipo de ajuda você espera do CEA?</label>
                        <textarea class="custom-form-control" type="text" name="expectedHelp" id="expectedHelp" required>{{ old('expectedHelp') ?? ''}}</textarea>
                    </div>


                    <div class="custom-form-group mt-5">
                        <label class="text-justify">10. Caso seja pertinente, anexe a esta ficha de inscrição algum plano de pesquisa, relatório, 
                            resumo ou trabalho publicado que se relacione com este projeto.(Max 8 MB total)</label>

                        <div class="col-lg pt-2">
                            <div id="novos-anexos"></div>
                                <label class="font-weight-normal">Adicionar anexo</label> 
                                <input id="count-new-attachment" value=0 type="hidden" disabled>
                                <a class="btn btn-link btn-sm text-dark text-decoration-none" id="btn-addAttachment" 
                                    title="Adicionar novo anexo">
                                    <i class="fas fa-plus-circle"></i>
                                </a>
                        </div>
                    </div> 

                    <hr class="my-5">

                    <div class="custom-form-group mt-5">
                        <div class="col-12">
                            <div class="g-recaptcha" data-sitekey="6Lfe_oMqAAAAANKhCHxnvXTwzsvxOPi6MVaaawF4" required></div>
                        </div>
                    </div>

                    <div class="row custom-form-group justify-content-center mt-5">
                        <button id="btn-submit" type="submit" class="btn btn-outline-dark">
                            Enviar Inscrição
                        </button>
                    </div>
                </form>
            @else
                <div class="alert alert-danger" role="alert">
                    Nenhum semestre foi cadastrado no sistema.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('javascripts_bottom')
@parent
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script src="{{ asset('js/jquery-captcha.min.js').'?version=1' }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
    jQuery.validator.addMethod("confirmacaoEmail", function(value, element) {
        return value == document.getElementById("email").value;
    }, "E-mails não conferem.");
    jQuery.validator.addMethod("validateEmail", function(value, element) {
        var filter = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return filter.test(value);
    }, "E-mail invalido.");
    jQuery.validator.addMethod('filesize', function (value, element, param) {
        var soma = 0;
        $("input[type=file]").each(function(index, elem){
            soma = soma + elem.files[0].size
        });
        return soma <= param * 1000000;
    }, 'A soma do tamanho dos arquivos não deve ultrapassar {0} MB');
    jQuery.validator.addMethod("validateCPFCNPJ", function(value, element) {
        var valor = value.replace(/[^0-9]/g, '');
        if(valor.length == 11){
            var cpf = valor;
            var soma;
            var resto;
            soma = 0;
            if(cpf == "00000000000"){
                return false;
            }
            for(i=1; i<=9; i++){
                soma = soma + parseInt(cpf.substring(i-1, i)) * (11 - i); 
            }
            resto = (soma * 10) % 11;
            if((resto == 10) || (resto == 11)){
                resto = 0;
            }
            if(resto != parseInt(cpf.substring(9, 10))){
                return false;
            }
            soma = 0;
            for(i=1; i<=10; i++){
                soma = soma + parseInt(cpf.substring(i-1, i)) * (12 - i);
            }
            resto = (soma * 10) % 11;
            if((resto == 10) || (resto == 11)){
                resto = 0;
            }
            if(resto != parseInt(cpf.substring(10, 11))){
                return false;
            }
            return true;
        }else if(valor.length == 14){
            var cnpj = valor;
            var soma;
            var resto;
            soma = 0;
            for(i=1; i<=4; i++){
                soma = soma + parseInt(cnpj.substring(i-1, i)) * (6 - i); 
            }
            for(i=5; i<=12; i++){
                soma = soma + parseInt(cnpj.substring(i-1, i)) * (14 - i); 
            }
            resto = soma % 11;
            if((resto == 0) || (resto == 1)){
                if(parseInt(cnpj.substring(12, 13)) != 0){
                    return false;
                }
            }else{
                if(parseInt(cnpj.substring(12, 13)) != (11 - resto)){
                    return false;
                }
            }
            soma = 0;
            for(i=1; i<=5; i++){
                soma = soma + parseInt(cnpj.substring(i-1, i)) * (7 - i); 
            }
            for(i=6; i<=13; i++){
                soma = soma + parseInt(cnpj.substring(i-1, i)) * (15 - i); 
            }
            resto = soma % 11;
            if((resto == 0) || (resto == 1)){
                if(parseInt(cnpj.substring(13, 14)) != 0){
                    return false;
                }
            }else{
                if(parseInt(cnpj.substring(13, 14)) != (11 - resto)){
                    return false;
                }
            }
            return true;
        }
        return false;
    }, "Invalid number");
    $("#form-inscricao").validate({
        rules : {
            email: {
                validateEmail: true,
            },
            emailConfirmation: {
                confirmacaoEmail: true
            },
            bdCpfCnpj : {
                required: true,
                validateCPFCNPJ : true
            },
            CPFCNPJ : {
                required: true,
                validateCPFCNPJ : true
            },
            paymentVoucher:{
                filesize: 8
            }
        },
        messages : {
            "knowledgeArea[]": {
                required : "Select at least one option"
            },
            "projectPurpose[]": {
                required : "Select at least one option"
            },
        },
        errorPlacement: function(error,element){ 
            element.attr("data-toggle", "tooltip");
            element.attr("data-placement", "top");
            element.attr("title", error.text());
            $('[data-toggle="tooltip"]').tooltip();          
        },
        success: function(label,element){
            element.removeAttribute("data-toggle");
            element.removeAttribute("data-placement");
            element.removeAttribute("title");     
            element.removeAttribute("data-original-title");       
        },
        submitHandler: function (form, event) {

            if (grecaptcha.getResponse() == ""){
                alert("Prove que voce não é um robo!");
                event.preventDefault();
            } else{
                Swal.fire({
                    title: 'ATENÇÂO!!',
                    text: "O agendamento só será realizado após pagamento da taxa. \n Deseja realmente submeter o formulário?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, submeter!',
                    cancelButtonText: 'Não, cancelar!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#btn-submit").attr('disabled', true);
                        form.submit();
                    }else if(!result.value){
                        event.preventDefault();
                    }
                });
            }
        }
    });

    $("#bdCpfCnpj").keydown(function(){
        try {
            $("#bdCpfCnpj").unmask();
        } catch (e) {}

        var tamanho = $("#bdCpfCnpj").val().replace(/[^0-9]/g, '').length;

        if(tamanho < 11){
            $("#bdCpfCnpj").mask("999.999.999-99");
        } else {
            $("#bdCpfCnpj").mask("99.999.999/9999-99");
        }

        var elem = this;
        setTimeout(function(){
            elem.selectionStart = elem.selectionEnd = 10000;
        }, 0);
        var currentValue = $(this).val();
        $(this).val('');
        $(this).val(currentValue);
    });
    $("#CPFCNPJ").keydown(function(){
        try {
            $("#CPFCNPJ").unmask();
        } catch (e) {}

        var tamanho = $("#CPFCNPJ").val().replace(/[^0-9]/g, '').length;

        if(tamanho < 11){
            $("#CPFCNPJ").mask("999.999.999-99");
        } else {
            $("#CPFCNPJ").mask("99.999.999/9999-99");
        }

        var elem = this;
        setTimeout(function(){
            elem.selectionStart = elem.selectionEnd = 10000;
        }, 0);
        var currentValue = $(this).val();
        $(this).val('');
        $(this).val(currentValue);
    });
    function rdChange(ckType){
        if(ckType.name == "refundReceipt"){
            var textarea = document.getElementById("refundReceiptData");
            if(ckType.value == "Sim"){
                textarea.hidden = false;
                textarea.required = true;
            }else{
                textarea.hidden = true;
                textarea.required = false;
            }
        }else if(ckType.name == "serviceType"){
            $("#div-info").empty();
            $("#div-info").removeClass("alert alert-warning");
            if(ckType.value == "Projeto"){
                var isEnrollmentPeriod = document.getElementById("isEnrollmentPeriod");
                if(!isEnrollmentPeriod.value){
                    ckType.checked = false;
                    alert("Fora do período de inscrição para projetos.");
                }
                if($("input[name=dataCollect]:checked").val() == "Não"){
                    ckType.checked = false;
                    alert("Para solicitar um projeto é necessario já ter coletados os dados!");
                }
            }
        }else if(ckType.name == "dataCollect"){
            if(ckType.value == "Não"){
                if($("input[name=serviceType]:checked").val() == "Projeto"){
                    ckType.checked = false;
                    alert("Para solicitar um projeto é necessario já ter coletados os dados!");
                }
            }
        }
    }
    function ckChange(ckType){
        var checked = document.getElementById(ckType.id);

        if (checked.checked) {
            if(ckType.id == "ckppOther"){
                var inputText = document.getElementById("ppOther");
                inputText.disabled = false;
                inputText.required = true;
            }else if(ckType.id == "ckkaOther"){
                var inputText = document.getElementById("kaOther");
                inputText.disabled = false;
                inputText.required = true;
            }else if(ckType.id == "ckfaOther"){
                var inputText = document.getElementById("faOther");
                inputText.disabled = false;
                inputText.required = true;
            }else if(ckType.id == "ckirOther"){
                var inputText = document.getElementById("irOther");
                inputText.disabled = false;
                inputText.required = true;
            }
        }
        else {
            if(ckType.id == "ckppOther"){
                var inputText = document.getElementById("ppOther");
                inputText.disabled = true;
                inputText.required = false;
            }else if(ckType.id == "ckkaOther"){
                var inputText = document.getElementById("kaOther");
                inputText.disabled = true;
                inputText.required = false;
            }else if(ckType.id == "ckfaOther"){
                var inputText = document.getElementById("faOther");
                inputText.disabled = true;
                inputText.required = false;
            }else if(ckType.id == "ckirOther"){
                var inputText = document.getElementById("irOther");
                inputText.disabled = true;
                inputText.required = false;
            }
        }    

        var ckppIniCientifica = document.getElementById("ckppIniCientifica");
        var ckppMestrado = document.getElementById("ckppMestrado");
        var ckppDoutorado = document.getElementById("ckppDoutorado");
        var mentor = document.getElementById("mentor");
        var declaration = document.getElementById("ckDeclaration");

        if((ckppIniCientifica.checked) || (ckppMestrado.checked) || (ckppDoutorado.checked)){
            mentor.required = true;
            declaration.required = true;
        }else{
            mentor.required = false;
            declaration.required = false;
        }

        cks = document.getElementsByName(ckType.name);
        if(ckType.name != "fundingAgency[]"){
            if ($('[name="'+ckType.name+'"]').is(':checked')) {
                $.each(cks, function(index, ck){
                    ck.required = false;
                });
            }else {
                $.each(cks, function(index, ck){
                    ck.required = true;
                });
            } 
        }
    }
    $(window).on('load', function() {
        cks = document.getElementsByName("projectPurpose[]");
        if ($('[name="projectPurpose[]"]').is(':checked')) {
            $.each(cks, function(index, ck){
                ck.required = false;
            });
        }else {
            $.each(cks, function(index, ck){
                ck.required = true;
            });
        } 
        cks = document.getElementsByName("knowledgeArea[]");
        if ($('[name="knowledgeArea[]"]').is(':checked')) {
            $.each(cks, function(index, ck){
                ck.required = false;
            });
        }else {
            $.each(cks, function(index, ck){
                ck.required = true;
            });
        } 
        cks = document.getElementsByName("institutionRelationship[]");
        if ($('[name="institutionRelationship[]"]').is(':checked')) {
            $.each(cks, function(index, ck){
                ck.required = false;
            });
        }else {
            $.each(cks, function(index, ck){
                ck.required = true;
            });
        } 

        var rdrr = document.getElementById("rdrrSim");

        if(rdrr.checked){
            var textarea = document.getElementById("refundReceiptData");
            textarea.hidden = false;
            textarea.required = true;
        }

        var ckppIniCientifica = document.getElementById("ckppIniCientifica");
        var ckppMestrado = document.getElementById("ckppMestrado");
        var ckppDoutorado = document.getElementById("ckppDoutorado");
        var mentor = document.getElementById("mentor");
        var declaration = document.getElementById("ckDeclaration");

        if((ckppIniCientifica.checked) || (ckppMestrado.checked) || (ckppDoutorado.checked)){
            mentor.required = true;
            declaration.required = true;
        }else{
            mentor.required = false;
            declaration.required = false;
        }

        const $recaptcha = document.querySelector('#g-recaptcha-response');
        if ($recaptcha) {
            $recaptcha.setAttribute('required', 'required');
        }
    });
    function removeAnexo(id){   
        $('#anexoNovo'+id).rules("remove")
        document.getElementById("anexo-"+id).remove();
    }
    $('#btn-addAttachment').on('click', function(e) {
      var count = document.getElementById('count-new-attachment');
      var id = parseInt(count.value)+1;
      count.value = id;
      var html = ['<div class="row custom-form-group justify-content-start" id="anexo-new'+id+'">',
          '<div class="col-lg-auto">',
          '<a class="btn btn-link btn-sm text-dark text-decoration-none"',
          '    style="padding-left:0px"',
          '    id="btn-remove-anexo-new'+id+'"',
          '    onclick="removeAnexo(\'new'+id+'\')"',
          '>',
          '    <i class="fas fa-trash-alt"></i>',
          '</a>',
          '<input class="custom-form-input btn-sm" id="anexoNovo'+id+'" name="anexosNovos[new'+id+'][arquivo]" type="file" >',
          '<br/>',
      '</div></div>'].join("\n");
      $('#novos-anexos').append(html);
      $('#anexoNovo'+id).rules("add",{filesize:8})
    });
</script>
@endsection