@extends('layouts.o_s_form_layout')

@section('content')

</head>

<body id="body">
    <div class="container">
        <div class="row mt-1">
            <div class="col-lg-6 offset-lg-3">

                @if (session()->has('message'))
                    <div class="alert alert-info" role="alert">
                        {{session()->get('message')}}
                    </div>
                @endif

                <div id="header" class="text-center my-2">
                    <strong>Solicitação de Assistência Técnica nº <span style="color: red">{{$note->order->id}}</span></strong>
                </div>
                <main>
                    
                    <div class="mx-0 my-2 border border-dark rounded">
                        <div class="p-1"><strong>Cliente: </strong>{{$note->order->client->name}}</div>
                        <div class="border-top border-dark p-1"><strong>Unidade: </strong>{{$note->order->client->unit}}</div>
                        <div class="border-top border-dark p-1"><strong>Endereço: </strong>{{$note->order->client->address}}</div>
                        <div class="border-top border-dark p-1"><strong>Contato: </strong>{{$note->order->client->contact}}</div>
                        <div class="border-top border-dark p-1"><strong>Órgao Solicitante</strong>: SUP</div>
                        <div class="border-top border-dark p-1"><strong>Anotado por</strong>: {{$note->order->user->name}}</div>
                    </div>

                    <div class="mx-0 my-2 border border-dark rounded">
                        <div class="p-1"><strong>Data do Acionamento: </strong>{{date('d/m/Y',strtotime($note->order->req_date))}}</div>
                    </div>
                     
                    <div class="mx-0 my-2 border border-dark rounded">
                        <div class="p-1"><strong>Hora do Acionamento: </strong>{{date('H:i',strtotime($note->order->req_time))}}</div>
                    </div>
                    
                    <div class="mx-0 my-2 border border-dark rounded">
                        <div class="p-1"><strong>Problema Relatado: </strong>{{$note->order->req_descr}}</div>
                        <div class="border-top border-dark p-1"><strong>Equipamento: </strong>{{$note->order->equipment}}</div>
                    </div>

                    <div class="mt-3">
                        <strong>Editar registro nº {{$note->id}}</strong>
                    </div>

                    <form action="{{route('notes.update', ['note' => $note->id])}}" id="form" method="post" autocomplete="on">
                        @csrf

                        <input type="hidden" name="_method" id="idNum" value="PUT">
                        
                        <input type="hidden" name="order_id" id="order_id" value="{{$note->order->id}}">

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="equipMod" name="equip_mod" value="{{$note->equip_mod}}">
                            <label for="equipMod">Modelo do Equipamento</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="equipId" name="equip_id" value="{{$note->equip_id}}">
                            <label for="equipId">Número de Série</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" name="equip_type" class="form-control" id="equipType" value="{{$note->equip_type}}">
                            <label for="equipType">Tipo do Equipamento</label>
                        </div>

                        <div class="form-floating my-2">
                            <textarea id="situation" name="situation" class='autoExpand form-control' rows='1' data-min-rows='1'>{{$note->situation}}</textarea>
                            <label for="situation">Descrição da situação encontrada</label>
                        </div>

                        <div class="form-floating my-2">
                            <textarea id="cause" name="cause" class='autoExpand form-control' rows='1' data-min-rows='1'>{{$note->cause}}</textarea>
                            <label for="cause">Provável causa do problema</label>
                        </div>

                        <div class="form-floating my-2">
                            <textarea id="services" name="services" class='autoExpand form-control' rows='1' data-min-rows='1'>{{$note->services}}</textarea>
                            <label for="services">Descrição dos serviços executados</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="date" class="form-control" id="date" name="date" value="{{$note->date}}">
                            <label for="date">Data do Atendimento</label>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="time" class="form-control" id="goStart" name="go_start" value="{{$note->go_start}}">
                                    <label for="goStart">Saída (Ida)</label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <input type="time" class="form-control" id="goEnd" name="go_end" value="{{$note->go_end}}">
                                    <label for="goEnd">Chegada (Ida)</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="time" class="form-control" id="Start" name="start" value="{{$note->start}}">
                                    <label for="Start">Início</label>
                                </div>
                            </div>
                            <div class="col">    
                                <div class="form-floating">
                                    <input type="time" class="form-control" id="End" name="end" value="{{$note->end}}">
                                    <label for="End">Término</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="time" class="form-control" id="backStart" name="back_start" value="{{$note->back_start}}">
                                    <label for="backStart">Saída (Volta)</label>
                                </div>
                            </div>
                            <div class="col"> 
                                <div class="form-floating">
                                    <input type="time" class="form-control" id="backEnd" name="back_end" value="{{$note->back_end}}">
                                    <label for="goStart">Chegada (Volta)</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-floating my-2">
                            <select class="form-select" id="firstTec" name="first_tec" aria-label="Floating label select example">
                                <option selected value="{{$note->first_tec->id}}">{{$note->first_tec->id.' - '.$note->first_tec->user->name}}</option>
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
                                        <input type="hidden" name="sign_t_1" id="idSignTec1" value="{{$note->sign_t_1}}">
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

                                @if (!isset($note->second_tec->id))
                                    <option selected value="0">Selecionar Técnico 02</option>
                                @endif

                                @foreach ($tecs as $tec)
                                    @if (isset($note->second_tec->id))
                                        @if ($note->second_tec->id == $tec->id)
                                            <option selected value="{{$tec->id}}">{{$tec->id}} - {{$tec->user->name}}</option>
                                        @else
                                            <option value="{{$tec->id}}">{{$tec->id}} - {{$tec->user->name}}</option>
                                        @endif
                                    @else
                                        <option value="{{$tec->id}}">{{$tec->id}} - {{$tec->user->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                            <label for="tec_id">Técnico 02</label>
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

                        <div class="mb-3">
                            <button id="submitButton" type="button" class="btn btn-primary me-2" data-bs-dismiss="modal">
                                Salvar
                            </button>
                           
                            <a href="{{route('notes.create', ['order' => $note->order->id])}}" class="btn btn-secondary">
                                Voltar
                            </a>
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