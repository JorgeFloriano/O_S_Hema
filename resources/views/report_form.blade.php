@extends('layouts.o_s_form_layout')

@section('content')

</head>

<body id="body">
    <div class="container">
        <div class="row mt-1">
            <div class="col-lg-6 offset-lg-3">
                <div id="header" class="text-center my-2">
                    <div class="row">
                        <div class="col">
                            <img src="{{asset('assets/img/logo_hema.png')}}" alt="logo cmk" width="120%">
                        </div>
                        <div class="col-6">
                            <strong>Solicitação de Assistência Técnica</strong>
                        </div>
                        <div class="col">
                            Nº <span style="color: red">01</span>
                        </div>
                      </div>
                </div>
                <main>
                    
                    <div class="mx-0 my-2 border border-dark rounded">
                        <div class="p-1"><strong>Cliente:</strong> Village Ipanema</div>
                        <div class="border-top border-dark p-1"><strong>Unidade:</strong> 01</div>
                        <div class="border-top border-dark p-1"><strong>Endereço:</strong> Est. Mun. Fazenda Nacional Ipanema, S/N - Rio Verde, Araçoiaba da Serra - SP</div>
                        <div class="border-top border-dark p-1"><strong>Contato:</strong> Ricardo</div>
                        <div class="border-top border-dark p-1"><strong>Órgao Solicitante</strong>: SUP</div>
                        <div class="border-top border-dark p-1"><strong>Anotado por</strong>: Gabriel</div>
                    </div>

                    <div class="mx-0 my-2 border border-dark rounded">
                        <div class="p-1"><strong>Data do Acionamento:</strong> 15/08/2024</div>
                    </div>
                     
                    <div class="mx-0 my-2 border border-dark rounded">
                        <div class="p-1"><strong>Hora do Acionamento:</strong> 17:14</div>
                    </div>

                    <div class="mx-0 my-2 border border-dark rounded">
                        <div class="p-1"><strong>Problema Relatado:</strong> Câmera sem gravação</div>
                        <div class="border-top border-dark p-1"><strong>Equipamento:</strong> Câmera 77</div>
                    </div>

                    <form action="{{route('orders.update', 1 )}}" id="form" method="post" autocomplete="on">
                        @csrf
                        <input type="hidden" name="_method" id="idNum" value="PUT">

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="equipMod" name="equip_mod" placeholder="Modelo do Equipamento">
                            <label for="equipMod">Modelo do Equipamento</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="equipId" name="equip_id" placeholder="Número de Série">
                            <label for="equipId">Número de Série</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" name="equip_type" class="form-control" id="equipType" placeholder="Tipo">
                            <label for="equipType">Tipo do Equipamento</label>
                        </div>

                        <div class="form-floating my-2">
                            <textarea id="situation" name="situation" placeholder="Descrição da situação encontrada" class='autoExpand form-control' rows='1' data-min-rows='1'></textarea>
                            <label for="situation">Descrição da situação encontrada</label>
                        </div>

                        <div class="form-floating my-2">
                            <textarea id="cause" name="cause" placeholder="Provável causa do problema" class='autoExpand form-control' rows='1' data-min-rows='1'></textarea>
                            <label for="cause">Provável causa do problema</label>
                        </div>

                        <div class="form-floating my-2">
                            <textarea id="services" name="services" placeholder="Serviços executados" class='autoExpand form-control' rows='1' data-min-rows='1'></textarea>
                            <label for="services">Descrição dos serviços executados</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="date" class="form-control" id="date" name="date" placeholder="Data do Atendimento" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}">
                            <label for="date">Data do Atendimento</label>
                        </div>

                        <div class="row g-2">
                            <div class="col">
                                <div class="form-floating my-2">
                                    <input type="time" class="form-control" id="goStart" name="go_start" placeholder="Saída (Ida)" value="{{\Carbon\Carbon::now()->format('H:i')}}">
                                    <label for="goStart">Saída (Ida)</label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating my-2">
                                    <input type="time" class="form-control" id="goEnd" name="go_end" placeholder="Chegada (Ida)">
                                    <label for="goEnd">Chegada (Ida)</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col">
                                <div class="form-floating my-2">
                                    <input type="time" class="form-control" id="Start" name="start" placeholder="Início">
                                    <label for="Start">Início</label>
                                </div>
                            </div>
                            <div class="col">    
                                <div class="form-floating my-2">
                                    <input type="time" class="form-control" id="End" name="end" placeholder="Término">
                                    <label for="End">Término</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col">
                                <div class="form-floating my-2">
                                    <input type="time" class="form-control" id="backStart" name="back_start" placeholder="Saída (Volta)">
                                    <label for="backStart">Saída (Volta)</label>
                                </div>
                            </div>
                            <div class="col"> 
                                <div class="form-floating my-2">
                                    <input type="time" class="form-control" id="backEnd" name="back_end" placeholder="Chegada (Volta)">
                                    <label for="goStart">Chegada (Volta)</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-floating my-2">
                            <select class="form-select" id="firstTec" name="first_tec" aria-label="Floating label select example">
                                <option selected>Selecionar Técnico 01</option>
                                <option value="1">Paulo</option>
                                <option value="2">João</option>
                                <option value="3">Pedro</option>
                            </select>
                            <label for="firstTec">Técnico 01</label>
                        </div>
                        <button onclick="scrollToBottom()" class="btn btn-info" id="pen1" href="#" class="signature-button" data-bs-toggle="modal"     data-bs-target="#signature1Modal"><i class="fa fa-pencil" aria-hidden="true"></i>ASSINAR
                        </button>

                        <!-- Modal signature 01-->
                        <div style="position: fixed" class="modal fade" id="signature1Modal" tabindex="-1" aria-labelledby="signature1ModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                    <h5 class="modal-title" id="signature1ModalLabel">Assinatura do Técnico 01</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body signature">
                                        <canvas height="200" width="320" class="signature-pad" id="canv1"></canvas>
                                        <input type="hidden" name="sign_t_1" id="idSignTec1">
                                    </div>

                                    <div class="modal-footer">
                                        <a id="okSign1" href="#" class="signature-button" data-bs-dismiss="modal">
                                            <i class="fa fa-check" aria-hidden="true" ></i>Ok
                                        </a>
                                        <a id="clear1" href="#" class="signature-button">
                                            <i class="fa fa-eraser" aria-hidden="true"></i>Apagar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="form-floating my-2">
                            <select class="form-select" id="secondTec" name="second_tec" aria-label="Floating label select example">
                                <option selected>Selecionar Técnico 02</option>
                                <option value="1">Paulo</option>
                                <option value="2">João</option>
                                <option value="3">Pedro</option>
                            </select>
                            <label for="secondTec">Técnico 02</label>
                        </div>
                        <button class="btn btn-info mb-2" id="pen2" href="#" class="signature-button" data-bs-toggle="modal" data-bs-target="#signature2Modal"><i class="fa fa-pencil" aria-hidden="true"></i>ASSINAR
                        </button>

                        <!-- Modal signature 02-->
                        <div class="modal fade" id="signature2Modal" tabindex="-1" aria-labelledby="signature2ModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                    <h5 class="modal-title" id="signature2ModalLabel">Assinatura do Técnico 02</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    
                                    <div class="modal-body signature">
                                        <canvas height="200" width="320" class="signature-pad" id="canv2" aria-placeholder="assine aqui"></canvas>
                                        <input type="hidden" name="sign_t_2" id="idSignTec2">
                                    </div>

                                    <div class="modal-footer">
                                    <a id="okSign2" href="#" class="signature-button" data-bs-dismiss="modal"><i class="fa fa-check" aria-hidden="true"></i>Ok </a>
                                    <a id="clear2" href="#" class="signature-button"><i class="fa fa-eraser" aria-hidden="true"></i>Apagar </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="my-3">
                            <button id="submit_button" type="submit" class="btn btn-primary my-2 me-2" data-bs-dismiss="modal">
                                Confirma
                            </button>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="finished" id="save" value="0" checked>
                                <label class="form-check-label" for="save"><i class="fa fa-floppy-o" aria-hidden="true"></i>Salvar</label>
                            </div>
                              
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="finished" id="finished" value="1">
                                <label class="form-check-label" for="finished"><i class="fa fa-check-square-o" aria-hidden="true"></i>Concluir</label>
                              </div>
                        </div>

                        <div class="mt-4">
                            
                        </div>

                    </form>
                    <script src="{{asset('assets/js/signature.js')}}"></script>
                    <script src="{{asset('assets/js/signature2.js')}}"></script>
                </main>
            </div>
        </div>
    </div>
</body>
</html>
@endsection