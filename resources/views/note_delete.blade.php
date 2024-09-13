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
                        <div class="border-top border-dark p-1"><strong>Anotado por</strong>:  {{$writer->id.' - '.$writer->name}}</div>
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
                        <strong>{{$msg}} registro nº {{$note->id}}</strong>
                    </div>

                    <form action="{{route('notes.destroy', ['note' => $note->id])}}" id="form" method="post" autocomplete="on">
                        @csrf

                        <input type="hidden" name="_method" id="idNum" value="DELETE">

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="equipMod" disabled name="equip_mod" value="{{$note->equip_mod}}">
                            <label for="equipMod">Modelo do Equipamento</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="equipId" disabled name="equip_id" value="{{$note->equip_id}}">
                            <label for="equipId">Número de Série</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" disabled name="equip_type" class="form-control" id="equipType" value="{{$note->equip_type}}">
                            <label for="equipType">Tipo do Equipamento</label>
                        </div>

                        <div class="form-floating my-2">
                            <textarea id="situation" disabled name="situation" class='autoExpand form-control' rows='1' data-min-rows='1'>{{$note->situation}}</textarea>
                            <label for="situation">Descrição da situação encontrada</label>
                        </div>

                        <div class="form-floating my-2">
                            <textarea id="cause" disabled name="cause" class='autoExpand form-control' rows='1' data-min-rows='1'>{{$note->cause}}</textarea>
                            <label for="cause">Provável causa do problema</label>
                        </div>

                        <div class="form-floating my-2">
                            <textarea id="services" disabled name="services" class='autoExpand form-control' rows='1' data-min-rows='1'>{{$note->services}}</textarea>
                            <label for="services">Descrição dos serviços executados</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="date" class="form-control" id="date" disabled name="date" value="{{$note->date}}">
                            <label for="date">Data do Atendimento</label>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="time" class="form-control" id="goStart" disabled name="go_start" value="{{$note->go_start}}">
                                    <label for="goStart">Saída (Ida)</label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <input type="time" class="form-control" id="goEnd" disabled name="go_end" value="{{$note->go_end}}">
                                    <label for="goEnd">Chegada (Ida)</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="time" class="form-control" id="Start" disabled name="start" value="{{$note->start}}">
                                    <label for="Start">Início</label>
                                </div>
                            </div>
                            <div class="col">    
                                <div class="form-floating">
                                    <input type="time" class="form-control" id="End" disabled name="end" value="{{$note->end}}">
                                    <label for="End">Término</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="time" class="form-control" id="backStart" disabled name="back_start" value="{{$note->back_start}}">
                                    <label for="backStart">Saída (Volta)</label>
                                </div>
                            </div>
                            <div class="col"> 
                                <div class="form-floating">
                                    <input type="time" class="form-control" id="backEnd" disabled name="back_end" value="{{$note->back_end}}">
                                    <label for="goStart">Chegada (Volta)</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-floating my-2">
                            <input class="form-control" id="firstTec" disabled name="first_tec" value="{{$note->first_tec->id}} - {{$note->first_tec->name}}">
                            <label for="firstTec">Técnico 01</label>
                        </div>

                        <div class="form-floating my-2">
                            <input class="form-control" id="secondTec" disabled name="second_tec" value="{{$note->second_tec->id ?? ''}} - {{$note->second_tec->name ?? ''}}">
                            <label for="secondTec">Técnico 02</label>
                        </div>

                        <div class="my-2">

                            @if (auth()->user()->id == $note->first_tec->id)
                                <button id="submitButton" type="submit" class="btn btn-danger me-2" data-bs-dismiss="modal">
                                    Deletar
                                </button> 
                            @endif
                           
                            <a href="{{route('notes.create', ['order' => $note->order->id])}}" class="btn btn-secondary">
                                Voltar
                            </a>
                            
                        </div>
                    </form>
                </main>
            </div>
        </div>
    </div>
</body>
</html>
@endsection