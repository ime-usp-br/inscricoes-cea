@extends('layouts.app')

@section('content')
@parent

<div id="layout_conteudo px-2">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-11 col-md-10 col-lg-9 col-xl-8">
            @if($semester)
                @if($semester->IsEnrollmentPeriod())
                    <h2 class='text-center'>FICHA DE INSCRIÇÃO PARA ASSESSORIA ESTATÍSTICA</h2>
                    <h4 class='text-center pb-4'>{{ $semester->period }} de {{ $semester->year }}</h4>



                    <form method="POST" action="{{ route('applications.store') }}" enctype='multipart/form-data'>
                        @csrf

                        <div class="row custom-form-group d-flex align-items-center">
                            <div class="col-12 col-md-auto text-md-right">
                                <label for="projectResponsible">Responsável(is) pelo projeto:</label>
                            </div>
                            <div class="col-12 col-md">
                                <input class="custom-form-control" type="text" name="projectResponsible" id="projectResponsible" />
                            </div>
                        </div>

                        <div class="row custom-form-group d-flex align-items-center">
                            <div class="col-12 col-md-auto text-md-right">
                                <label for="contactPhone">Telefones para contato:</label>
                            </div>
                            <div class="col-12 col-md">
                                <input class="custom-form-control" type="text" name="contactPhone" id="contactPhone" />
                            </div>
                        </div>

                        <div class="row custom-form-group d-flex align-items-center">
                            <div class="col-12 col-md-auto text-md-right">
                                <label for="cpf-cnpj">CPF/CNPJ:</label>
                            </div>
                            <div class="col-12 col-md">
                                <input class="custom-form-control" type="text" name="cpf-cnpj" id="cpf-cnpj" />
                            </div>
                        </div>

                        <div class="row custom-form-group d-flex align-items-center">
                            <div class="col-12 col-md-auto text-md-right">
                                <label for="email">E-mail:</label>
                            </div>
                            <div class="col-12 col-md">
                                <input class="custom-form-control" type="text" name="email" id="email" />
                            </div>
                        </div>

                        <div class="row custom-form-group d-flex align-items-center">
                            <div class="col-12 col-md-auto text-md-left">
                                <label for="institution">Instituição:</label>
                            </div>
                            <div class="col-12 col-md">
                                <input class="custom-form-control" type="text" name="institution" id="institution" />
                            </div>
                        </div>

                        <div class="row custom-form-group d-flex align-items-center">
                            <div class="col-12 col-md-auto text-md-right">
                                <label for="institutionRelationship">Vínculo com a Instituição:</label>
                            </div>
                            <div class="col-12 col-md">
                                <input class="custom-form-control" type="text" name="institutionRelationship" id="institutionRelationship" />
                            </div>
                        </div>

                        <div class="row custom-form-group d-flex align-items-center">
                            <div class="col-12 col-md-auto text-md-right">
                                <label for="mentor">Colaborador(es) ou orientador:</label>
                            </div>
                            <div class="col-12 col-md">
                                <input class="custom-form-control" type="text" name="mentor" id="mentor"/>
                            </div>
                        </div>

                        <div class="row custom-form-group d-flex align-items-center">
                            <div class="col-12 col-md-auto text-md-right">
                                <label for="projectPurpose">Finalidade do projeto:</label>
                            </div>
                            <div class="col-12 col-md">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="projectPurpose[]" value="Mestrado"/>
                                    <label class="font-weight-normal">Mestrado</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="projectPurpose[]" value="Doutorado"/>
                                    <label class="font-weight-normal">Doutorado</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="projectPurpose[]" value="Livre Docência"/>
                                    <label class="font-weight-normal">Livre Docência</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="projectPurpose[]" value="Publicação"/>
                                    <label class="font-weight-normal">Publicação</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="projectPurpose[]" value="Outra"/>
                                    <label class="font-weight-normal">Outra</label>
                                    <input class="custom-form-control ml-2" type="text" name="projectPurposeOther" id="projectPurposeOther" placeholder="Especifique" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row custom-form-group d-flex align-items-center">
                            <div class="col-12 col-md-auto text-md-right">
                                <label for="fundingAgency">Agência financiadora do projeto:</label>
                            </div>
                            <div class="col-12 col-md">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="fundingAgency" value="FAPESP"/>
                                    <label class="font-weight-normal">FAPESP</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="fundingAgency" value="FINEP"/>
                                    <label class="font-weight-normal">FINEP</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="fundingAgency" value="CNPq"/>
                                    <label class="font-weight-normal">CNPq</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="fundingAgency" value="Outra"/>
                                    <label class="font-weight-normal">Outra</label>
                                    <input class="custom-form-control ml-2" type="text" name="fundingAgencyOther" id="fundingAgencyOther" placeholder="Especifique" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row custom-form-group d-flex align-items-center">
                            <div class="col-12 col-md-auto text-md-right">
                                <label for="knowledgeArea">Área de conhecimento:</label>
                            </div>
                            <div class="col-12 col-md">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="knowledgeArea[]" value="Tecnológica"/>
                                    <label class="font-weight-normal">Tecnológica</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="knowledgeArea[]" value="Médica ou Biológica"/>
                                    <label class="font-weight-normal">Médica ou Biológica</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="knowledgeArea[]" value="Social ou Humana"/>
                                    <label class="font-weight-normal">Social ou Humana</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="knowledgeArea[]" value="Econômica"/>
                                    <label class="font-weight-normal">Econômica</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="projectPurpose[]" value="Outra"/>
                                    <label class="font-weight-normal">Outra</label>
                                    <input class="custom-form-control ml-2" type="text" name="projectPurposeOther" id="projectPurposeOther" placeholder="Especifique" disabled>
                                </div>
                            </div>
                        </div>

                        <hr class="my-5">

                        <div class="col my-5 text-justify">
                            <h5>Com o intuito de facilitar o trabalho dos consultores do CEA gostaríamos que fosse apresentada
                            uma descrição sucinta do projeto, salientando os aspectos indicados a seguir (os itens 8 e 9
                            são muito importantes). Termos técnicos estatísticos devem ser evitados e aqueles pertinentes à
                            área de concentração devem ser explicados</h5>
                        </div>


                        <div class="custom-form-group">
                            <label for="projectTitle">1. Título do projeto, mesmo sendo provisório:</label>
                            <input class="custom-form-control" type="text" name="projectTitle" id="projectTitle"/>
                        </div>

                        <div class="custom-form-group mt-5">
                            <label for="generalAspects">2. Aspectos gerais da área de concentração, com ênfase naqueles que motivaram o projeto:</label>
                            <textarea class="custom-form-control" type="text" name="generalAspects" id="generalAspects">{{ old('generalAspects') ?? ''}}</textarea>
                        </div>

                        <div class="custom-form-group mt-5">
                            <label for="generalObjectives">3. Objetivos gerais:</label>
                            <textarea class="custom-form-control" type="text" name="generalObjectives" id="generalObjectives">{{ old('generalObjectives') ?? ''}}</textarea>
                        </div>

                        <div class="custom-form-group mt-5">
                            <label class="text-justify" for="features">4. Que características (ou variáveis) foram ou serão observadas para atingir os objetivos? Como
                                foram ou serão efetuadas as medidas dessas características (ou variáveis)? Quais as unidades
                                de medida?</label>
                            <textarea class="custom-form-control" type="text" name="features" id="features">{{ old('features') ?? ''}}</textarea>
                        </div>

                        <div class="custom-form-group mt-5">
                            <label class="text-justify" for="otherFeatures">5. Que outras características (ou variáveis) poderiam influenciar essas medidas? Existe possibilidade
                                destas serem controladas?</label>
                            <textarea class="custom-form-control" type="text" name="otherFeatures" id="otherFeatures">{{ old('otherFeatures') ?? ''}}</textarea>
                        </div>

                        <div class="custom-form-group mt-5">
                            <label class="text-justify" for="limitations">6. Como foi (ou será) conduzida a investigação para que os objetivos do item 3 sejam atingidos?
                                Quais as restrições que foram ou serão naturalmente impostas à coleta de dados? Quantas
                                unidades amostrais* foram ou serão analisadas? Indique as limitações de tempo e custo.</label>
                            <textarea class="custom-form-control" type="text" name="limitations" id="limitations">{{ old('limitations') ?? ''}}</textarea>
                        </div>

                        <div class="custom-form-group mt-5">
                            <label class="text-justify" for="storage">7. Como os dados estão ou serão armazenados? Existe a possibilidade de apresentá-los em mídia
                                eletrônica (CD, DVD, etc)?</label>
                            <textarea class="custom-form-control" type="text" name="storage" id="storage">{{ old('storage') ?? ''}}</textarea>
                        </div>

                        <div class="custom-form-group mt-5">
                            <label class="text-justify" for="conclusions">8. Supondo que os dados já tivessem sido analisados de forma apropriada, indique o tipo de
                                conclusões que seriam satisfatórias, tendo em vista seu comentário no item 3. Simule resultados
                                possíveis e comente-os.</label>
                            <textarea class="custom-form-control" type="text" name="conclusions" id="conclusions">{{ old('conclusions') ?? ''}}</textarea>
                        </div>

                        <div class="custom-form-group mt-5">
                            <label for="expectedHelp">9. Que tipo de ajuda você espera do CEA?</label>
                            <textarea class="custom-form-control" type="text" name="expectedHelp" id="expectedHelp">{{ old('expectedHelp') ?? ''}}</textarea>
                        </div>

                        <hr class="my-5">

                        <div class="custom-form-group d-flex align-items-center">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input mr-3" type="checkbox" name="fundingAgency" value="FAPESP"/>
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
                                <input class="form-check-input mr-3" type="checkbox" name="fundingAgency" value="FAPESP"/>
                                <label class="text-justify" >
                                Declaro que estou ciente de que o(a) meu/minha orientador(a) deverá estar presente na entrevista.
                                </label>
                            </div>
                        </div>
                    </form>
                @elseif($semester->getStartDateEnrollments() > \Carbon\Carbon::now())
                    <div class="alert alert-info" role="alert">
                        As submissões de projetos para análise no {{ $semester->period }} de {{ $semester->year }} ocorrerão de {{ $semester->start_date_enrollments }} a {{ $semester->end_date_enrollments }}, 
                        por meio do preenchimento de formulário neste mesmo endereço.
                    </div>
                @elseif($semester->getEndDateEnrollments() < \Carbon\Carbon::now())
                    <div class="alert alert-info" role="alert">
                        As submissões de projetos para análise no {{ $semester->period }} de {{ $semester->year }} ocorreram de {{ $semester->start_date_enrollments }} a {{ $semester->end_date_enrollments }}, 
                        aguarde divulgação do período de submissão do próximo semestre.
                    </div>
                @endif
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
<script>
    tinymce.init({
    selector: '#generalAspects',
    plugins: 'link,code',
    menubar:false,
    toolbar: "undo redo | bold italic underline strikethrough | alignleft aligncenter alignright  | blockquote | formatselect | link",
    link_default_target: '_blank'
    });
    tinymce.init({
    selector: '#generalObjectives',
    plugins: 'link,code',
    menubar:false,
    toolbar: "undo redo | bold italic underline strikethrough | alignleft aligncenter alignright  | blockquote | formatselect | link",
    link_default_target: '_blank'
    });
    tinymce.init({
    selector: '#features',
    plugins: 'link,code',
    menubar:false,
    toolbar: "undo redo | bold italic underline strikethrough | alignleft aligncenter alignright  | blockquote | formatselect | link",
    link_default_target: '_blank'
    });
    tinymce.init({
    selector: '#otherFeatures',
    plugins: 'link,code',
    menubar:false,
    toolbar: "undo redo | bold italic underline strikethrough | alignleft aligncenter alignright  | blockquote | formatselect | link",
    link_default_target: '_blank'
    });
    tinymce.init({
    selector: '#limitations',
    plugins: 'link,code',
    menubar:false,
    toolbar: "undo redo | bold italic underline strikethrough | alignleft aligncenter alignright  | blockquote | formatselect | link",
    link_default_target: '_blank'
    });
    tinymce.init({
    selector: '#storage',
    plugins: 'link,code',
    menubar:false,
    toolbar: "undo redo | bold italic underline strikethrough | alignleft aligncenter alignright  | blockquote | formatselect | link",
    link_default_target: '_blank'
    });
    tinymce.init({
    selector: '#conclusions',
    plugins: 'link,code',
    menubar:false,
    toolbar: "undo redo | bold italic underline strikethrough | alignleft aligncenter alignright  | blockquote | formatselect | link",
    link_default_target: '_blank'
    });
    tinymce.init({
    selector: '#expectedHelp',
    plugins: 'link,code',
    menubar:false,
    toolbar: "undo redo | bold italic underline strikethrough | alignleft aligncenter alignright  | blockquote | formatselect | link",
    link_default_target: '_blank'
    });
</script>
@endsection