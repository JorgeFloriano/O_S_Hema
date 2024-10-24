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

                @if ($errors->any())
                    <div class="alert alert-warning">
                        <ul>
                            @foreach ($errors->all() as $msg)
                                <li>{{$msg}}</li>
                            @endforeach
                        </ul>
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
                        <div class="border-top border-dark p-1"><strong>Contato: </strong>{{$note->order->req_name}}</div>
                        <div class="border-top border-dark p-1"><strong>Setor: </strong>{{$note->order->sector}}</div>
                        <div class="border-top border-dark p-1"><strong>Anotado por: </strong>{{$note->order->user->name ?? ''}}</div>
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
                            <input type="text" class="form-control" id="equipMod" name="equip_mod" maxlength="20" value="{{$note->equip_mod}}" required>
                            <label for="equipMod">Modelo do Equipamento</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="equipId" name="equip_id" maxlength="20" value="{{$note->equip_id}}" required>
                            <label for="equipId">Número de Série</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" name="equip_type" class="form-control" id="equipType" maxlength="20" value="{{$note->equip_type}}" required>
                            <label for="equipType">Tipo do Equipamento</label>
                        </div>

                        <div class="form-floating my-2">
                            <select class="form-select" id="note_type_id" name="note_type_id" aria-label="Floating label select example" required>
                                @foreach ($types as $type)
                                    @if ($type->id == $note->note_type_id)
                                        <option value="{{$type->id}}" selected>{{$type->id}} - {{$type->description}}</option>
                                    @else
                                        <option value="{{$type->id}}">{{$type->id}} - {{$type->description}}</option>
                                    @endif
                                @endforeach
                            </select>
                            <label for="note_type_id">Tipo de Atendimento</label>
                        </div>

                        <div class="form-floating my-2">
                            <select class="form-select" id="defect_id" name="defect_id" aria-label="Floating label select example" required>
                                @foreach ($defects as $defect)
                                    @if ($defect->id == $note->defect_id)
                                        <option value="{{$defect->id}}" selected>{{$defect->id}} - {{$defect->description}}</option>
                                    @else
                                        <option value="{{$defect->id}}">{{$defect->id}} - {{$defect->description}}</option>
                                    @endif
                                @endforeach
                            </select>
                            <label for="defect_id">Defeito</label>
                        </div>

                        <div class="form-floating my-2">
                            <select class="form-select" id="cause_id" name="cause_id" aria-label="Floating label select example" required>
                                @foreach ($causes as $cause)
                                    @if ($cause->id == $note->cause_id)
                                        <option value="{{$cause->id}}" selected>{{$cause->id}} - {{$cause->description}}</option>
                                    @else
                                        <option value="{{$cause->id}}">{{$cause->id}} - {{$cause->description}}</option>
                                    @endif
                                @endforeach
                            </select>
                            <label for="cause_id">Causa</label>
                        </div>

                        <div class="form-floating my-2">
                            <select class="form-select" id="solution_id" name="solution_id" aria-label="Floating label select example" required>
                                @foreach ($solutions as $solution)
                                    @if ($solution->id == $note->solution_id)
                                        <option value="{{$solution->id}}" selected>{{$solution->id}} - {{$solution->description}}</option>
                                    @else
                                        <option value="{{$solution->id}}">{{$solution->id}} - {{$solution->description}}</option>
                                    @endif
                                @endforeach
                            </select>
                            <label for="solution_id">Solução</label>
                        </div> 

                        <div class="form-floating my-2">
                            <textarea id="services" name="services" maxlength="850" class='autoExpand form-control' rows='1' data-min-rows='1'>{{$note->services}}</textarea>
                            <label for="services">Descrição dos serviços executados</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="date" class="form-control" id="date" name="date" value="{{$note->date}}" required>
                            <label for="date">Data do Atendimento</label>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="time" class="form-control" id="goStart" name="go_start" value="{{$note->go_start}}" required>
                                    <label for="goStart">Saída (Ida)</label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <input type="time" class="form-control" id="goEnd" name="go_end" value="{{$note->go_end}}" required>
                                    <label for="goEnd">Chegada (Ida)</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="time" class="form-control" id="Start" name="start" value="{{$note->start}}" required>
                                    <label for="Start">Início</label>
                                </div>
                            </div>
                            <div class="col">    
                                <div class="form-floating">
                                    <input type="time" class="form-control" id="End" name="end" value="{{$note->end}}" required>
                                    <label for="End">Término</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="time" class="form-control" id="backStart" name="back_start" value="{{$note->back_start}}" required>
                                    <label for="backStart">Saída (Volta)</label>
                                </div>
                            </div>
                            <div class="col"> 
                                <div class="form-floating">
                                    <input type="time" class="form-control" id="backEnd" name="back_end" value="{{$note->back_end}}" required>
                                    <label for="goStart">Chegada (Volta)</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="kmStart" step="0.01" max="9999.99" min="0" name="km_start" value="{{$note->km_start}}" placeholder="Km inicial">
                                    <label for="kmStart">Km inicial</label>
                                </div>
                            </div>
                            <div class="col">    
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="kmEnd" step="0.01" max="9999.99" min="0" name="km_end" value="{{$note->km_end}}}" placeholder="Km final">
                                    <label for="kmEnd">Km final</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="food" step="0.01" max="9999.99" min="0" name="food" value="{{$note->food}}" placeholder="Alimantação (R$)">
                                    <label for="food">Alimentação (R$)</label>
                                </div>
                            </div>
                            <div class="col"> 
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="expense" step="0.01" max="9999.99" min="0" name="expense" value="{{$note->expense}}" placeholder="Outras Despesas (R$)">
                                    <label for="expense">Outros (R$)</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="obs" name="obs" value="{{$note->obs}}" placeholder="Observações"  maxlength="40">
                            <label for="obs">Observações</label>
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

                        <input type="hidden" name="finished" id="idFinished" value="0">

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