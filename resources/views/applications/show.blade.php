@extends('layouts.app')

@section('content')
@parent
<div id="layout_conteudo">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-11 col-md-10 col-lg-9 col-xl-8">
            <h1 class='text-center mt-4'>Ficha de Inscrição</h1>
            <h4 class='text-center pb-4'>{{ $application->semester->period }} de {{ $application->semester->year }}</h4>



            <div class="row custom-form-group d-flex align-items-center">
                <div class="col-12 col-md-auto text-md-right">
                    <label for="protocol">Protolo:</label>
                </div>
                <div class="col-12 col-md">
                    {{ $application->protocol }}
                </div>        
            </div>

            <div class="row custom-form-group d-flex align-items-center">
                <div class="col-12 col-md-auto text-md-right">
                    <label for="serviceType">Modalidade do serviço solicitado:</label>
                </div>
                <div class="col-12 col-md">
                    {{ $application->serviceType }}
                </div>        
            </div>

            <div class="row custom-form-group d-flex align-items-center">
                <div class="col-12 col-md-auto text-md-left">
                    <label for="institution">Coletou os dados:</label>
                </div>
                <div class="col-12 col-md">
                    {{ $application->dataCollect }}
                </div>
            </div>

            <div class="row custom-form-group d-flex align-items-center">
                <div class="col-12 col-md-auto text-md-right">
                    <label for="projectResponsible">Responsável(is) pelo projeto:</label>
                </div>
                <div class="col-12 col-md">
                    {{ $application->projectResponsible }}
                </div>        
            </div>

            <div class="row custom-form-group d-flex align-items-center">
                <div class="col-12 col-md-auto text-md-right">
                    <label for="contactPhone">Telefones para contato:</label>
                </div>
                <div class="col-12 col-md">
                    {{ $application->contactPhone }}
                </div>
            </div>

            @if(Auth::user()->hasRole(["Administrador","Secretaria"]))
                <div class="row custom-form-group d-flex align-items-center">
                    <div class="col-12 col-md-auto text-md-right">
                        <label for="cpf-cnpj">CPF/CNPJ:</label>
                    </div>
                    <div class="col-12 col-md">
                        {{ $application->CPFCNPJ }}
                    </div>
                </div>
            @endif

            <div class="row custom-form-group d-flex align-items-center">
                <div class="col-12 col-md-auto text-md-right">
                    <label for="email">E-mail:</label>
                </div>
                <div class="col-12 col-md">
                    {{ $application->email }}
                </div>
            </div>

            <div class="row custom-form-group d-flex align-items-center">
                <div class="col-12 col-md-auto text-md-left">
                    <label for="institution">Instituição:</label>
                </div>
                <div class="col-12 col-md">
                    {{ $application->institution }}
                </div>
            </div>

            <div class="row custom-form-group d-flex align-items-center">
                <div class="col-12 col-md-auto text-md-left">
                    <label for="course">Curso:</label>
                </div>
                <div class="col-12 col-md">
                    {{ $application->course }}
                </div>
            </div>

            <div class="row custom-form-group d-flex align-items-center">
                <div class="col-12 col-md-auto text-md-right">
                    <label for="institutionRelationship">Vínculo com a Instituição:</label>
                </div>
                <div class="col-12 col-md">
                    {{ $application->institutionRelationship }}
                </div>
            </div>

            <div class="row custom-form-group d-flex align-items-center">
                <div class="col-12 col-md-auto text-md-right">
                    <label for="mentor">Colaborador(es) ou orientador:</label>
                </div>
                <div class="col-12 col-md">
                    {{ $application->mentor }}
                </div>
            </div>

            <div class="row custom-form-group d-flex align-items-center">
                <div class="col-12 col-md-auto text-md-right">
                    <label id="ppLabel">Finalidade do projeto:</label>
                </div>
                <div class="col-12 col-md">
                    {{ str_replace(",", ", ", $application->projectPurpose) }} {{ $application->ppOther ? " - ".$application->ppOther : "" }}
                </div>
            </div>

            <div class="row custom-form-group d-flex align-items-center">
                <div class="col-12 col-md-auto text-md-right">
                    <label for="fundingAgency">Agência financiadora do projeto:</label>
                </div>
                <div class="col-12 col-md">
                    {{ str_replace(",", ", ", $application->fundingAgency) }} {{ $application->faOther ? " - ".$application->faOther : "" }}
                </div>
            </div>

            <div class="row custom-form-group d-flex align-items-center">
                <div class="col-12 col-md-auto text-md-right">
                    <label for="knowledgeArea">Área de conhecimento:</label>
                </div>
                <div class="col-12 col-md">
                    {{ str_replace(",", ", ", $application->knowledgeArea) }} {{ $application->kaOther ? " - ".$application->kaOther : "" }}
                </div>
            </div>
            
            @if(Auth::user()->hasRole(["Administrador","Secretaria"]))

                <div class="row custom-form-group d-flex align-items-center">
                    <div class="col-12 col-md-auto text-md-right">
                        <label>Comprovante de pagamento da taxa:</label>
                    </div>
                    @if($application->depositReceipt)
                        <div class="col-12 col-md">                    
                            <a href="{{ route('receipts.download', $application->depositReceipt) }}">{{ $application->depositReceipt->name }}</a>
                        </div>     
                    @else
                        <div class="col-12 col-md">                    
                            Inscrição feita após implementação do boleto.
                        </div>     

                    @endif   
                </div>

                <hr class="my-2">

                <div class="row custom-form-group d-flex align-items-center">
                    <div class="col-12 col-md-auto text-md-right">
                        <label>Boleto taxa de inscrição:</label>
                    </div>
                    @if($application->applicationFee)
                        <div class="col-12 col-md">      
                            <label>Status:</label> {{$application->applicationFee->getStatus()}}<br>
                            <label>Valor do Documento:</label> {{$application->applicationFee->valorDocumento}}<br>
                            <label>Data do Vencimento:</label> {{$application->applicationFee->dataVencimentoBoleto}}<br>
                            <label>Valor Pago:</label> {{$application->applicationFee->valorEfetivamentePago}}<br>
                            <label>Data do Pagamento:</label> {{$application->applicationFee->dataEfetivaPagamento ?? "Não foi pago"}}<br>
                        </div>     
                    @else
                        <div class="col-12 col-md">                    
                            Não Emitido.
                        </div>     

                    @endif   
                </div>

                <hr class="my-2">

                <div class="row custom-form-group d-flex align-items-center">
                    <div class="col-12 col-md-auto text-md-right">
                        <label>Boleto taxa de projeto:</label>
                    </div>
                    @if($application->projectfee)
                        <div class="col-12 col-md">      
                            <label>Status:</label> {{$application->projectfee->getStatus()}}<br>
                            <label>Valor do Documento:</label> {{$application->projectfee->valorDocumento}}<br>
                            <label>Data do Vencimento:</label> {{$application->projectfee->dataVencimentoBoleto}}<br>
                            <label>Valor Pago:</label> {{$application->projectfee->valorEfetivamentePago}}<br>
                            <label>Data do Pagamento:</label> {{$application->projectfee->dataEfetivaPagamento ?? "Não foi pago"}}<br>
                        </div>     
                    @else
                        <div class="col-12 col-md">                    
                            Não Emitido.
                        </div>     

                    @endif   
                </div>

                <hr class="my-2">
                
                <div class="row custom-form-group d-flex align-items-center">
                    <div class="col-12 col-md-auto text-md-right">
                        <label for="refundReceipt">Recibo para reembolso:</label>
                    </div>
                    <div class="col-12 col-md">
                        {{ $application->refundReceipt }}
                    </div>
                </div>

                @if($application->refundReceipt == "Sim")
                    <div class="custom-form-group">
                        <label class="text-justify" for="otherFeatures">Dados que devem constar no recibo:</label>
                        <div class="col-12 col-md text-justify">
                            {!! nl2br($application->refundReceiptData) !!}
                        </div>
                    </div>
                @endif

                <hr class="my-5">

                <div class="col my-5 text-justify">
                    <h5>
                        Dados bancarios
                    </h5>
                </div>

                <div class="row custom-form-group d-flex align-items-center">
                    <div class="col-12 col-md-auto text-md-right">
                        <label for="projectResponsible">Nome completo:</label>
                    </div>
                    <div class="col-12 col-md">
                        {{ $application->bdName }}
                    </div>        
                </div>

                <div class="row custom-form-group d-flex align-items-center">
                    <div class="col-12 col-md-auto text-md-right">
                        <label for="cpf-cnpj">CPF/CNPJ:</label>
                    </div>
                    <div class="col-12 col-md">
                        {{ $application->bdCpfCnpj }}
                    </div>
                </div>

                <div class="row custom-form-group d-flex align-items-center">
                    <div class="col-12 col-md-auto text-md-right">
                        <label for="projectResponsible">Nome do Banco:</label>
                    </div>
                    <div class="col-12 col-md">
                        {{ $application->bdBankName }}
                    </div>        
                </div>

                <div class="row custom-form-group d-flex align-items-center">
                    <div class="col-12 col-md-auto text-md-right">
                        <label for="projectResponsible">Número da Agência:</label>
                    </div>
                    <div class="col-12 col-md">
                        {{ $application->bdAgency }}
                    </div>        
                </div>

                <div class="row custom-form-group d-flex align-items-center">
                    <div class="col-12 col-md-auto text-md-right">
                        <label for="projectResponsible">Número da Conta:</label>
                    </div>
                    <div class="col-12 col-md">
                        {{ $application->bdAccount }}
                    </div>        
                </div>

                <div class="row custom-form-group d-flex align-items-center">
                    <div class="col-12 col-md-auto text-md-right">
                        <label for="knowledgeArea">Tipo da Conta:</label>
                    </div>
                    <div class="col-12 col-md">
                        {{ $application->bdType }}
                    </div>
                </div>
            @endif

            <hr class="my-5">

            <div class="custom-form-group">
                <label for="projectTitle">1. Título do projeto, mesmo sendo provisório:</label>
                <div class="col-12 col-md text-justify">
                    {{ $application->projectTitle }}
                </div>
            </div>

            <div class="custom-form-group mt-5">
                <label for="generalAspects">2. Aspectos gerais da área de concentração, com ênfase naqueles que motivaram o projeto:</label>
                <div class="col-12 col-md text-justify">
                    {!! nl2br($application->generalAspects) !!}
                </div>
            </div>

            <div class="custom-form-group mt-5">
                <label for="generalObjectives">3. Objetivos gerais:</label>
                <div class="col-12 col-md text-justify">
                    {!! nl2br($application->generalObjectives) !!}
                </div>
            </div>

            <div class="custom-form-group mt-5">
                <label class="text-justify" for="features">4. Que características (ou variáveis) foram ou serão observadas para atingir os objetivos? Como
                    foram ou serão efetuadas as medidas dessas características (ou variáveis)? Quais as unidades
                    de medida?</label>
                <div class="col-12 col-md text-justify">
                    {!! nl2br($application->features) !!}
                </div>
            </div>

            <div class="custom-form-group mt-5">
                <label class="text-justify" for="otherFeatures">5. Que outras características (ou variáveis) poderiam influenciar essas medidas? Existe possibilidade
                    destas serem controladas?</label>
                <div class="col-12 col-md text-justify">
                    {!! nl2br($application->otherFeatures) !!}
                </div>
            </div>

            <div class="custom-form-group mt-5">
                <label class="text-justify" for="limitations">6. Como foi (ou será) conduzida a investigação para que os objetivos do item 3 sejam atingidos?
                    Quais as restrições que foram ou serão naturalmente impostas à coleta de dados? Quantas
                    unidades amostrais* foram ou serão analisadas? Indique as limitações de tempo e custo.</label>
                <div class="col-12 col-md text-justify">
                    {!! nl2br($application->limitations) !!}
                </div>
            </div>

            <div class="custom-form-group mt-5">
                <label class="text-justify" for="storage">7. Como os dados estão ou serão armazenados? Existe a possibilidade de apresentá-los em mídia
                    eletrônica (CD, DVD, etc)?</label>
                <div class="col-12 col-md text-justify">
                    {!! nl2br($application->storage) !!}
                </div>
            </div>

            <div class="custom-form-group mt-5">
                <label class="text-justify" for="conclusions">8. Supondo que os dados já tivessem sido analisados de forma apropriada, indique o tipo de
                    conclusões que seriam satisfatórias, tendo em vista seu comentário no item 3. Simule resultados
                    possíveis e comente-os.</label>
                <div class="col-12 col-md text-justify">
                    {!! nl2br($application->conclusions) !!}
                </div>
            </div>

            <div class="custom-form-group mt-5">
                <label for="expectedHelp">9. Que tipo de ajuda você espera do CEA?</label>
                <div class="col-12 col-md text-justify">
                    {!! nl2br($application->expectedHelp) !!}
                </div>
            </div>


            <div class="custom-form-group mt-5">
                <label class="text-justify">10. Caso seja pertinente, anexe a esta ficha de inscrição algum plano de pesquisa, relatório, 
                    resumo ou trabalho publicado que se relacione com este projeto.</label>

                    @if(!$application->attachments->isEmpty())
                        @foreach($application->attachments as $attachment)
                            <div class="col-12 pt-2">
                                <a href="{{ route('attachments.download', $attachment) }}">{{ $attachment->name }}</a>
                            </div>
                        @endforeach
                    @else
                        <div class="col-12 pt-2">
                            Não foram feitos anexos.
                        </div>
                    @endif
            </div>
        </div>
    </div>
</div>

@endsection