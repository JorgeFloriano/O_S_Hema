@extends('layouts.o_s_form_layout')

@section('content')

</head>

<body id="body">
    <div class="container">
        <div class="row mt-1">
            <div class="col-lg-8 offset-lg-2">

                @if (session()->has('message'))
                    <div class="alert alert-info" role="alert">
                        {{session()->get('message')}}
                    </div>
                @endif

                <div id="header" class="my-2">
                    <h2>{{$msg}}Ordem de Serviço Nº {{$order->id}}</h2> 
                </div>
                <hr>
                <main>
                
                    <form action="{{route('orders.update', ['order' => $order->id])}}" id="form" method="post" autocomplete="on">
                        @csrf
                        
                        <input type="hidden" name="_method" id="idNum" value="PUT">

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" disabled id="adm_id" name="adm_id" placeholder="Editada por" value="{{$user->name ?? ''}}">
                            <label for="adm_id">Editada por</label>
                        </div>

                        <div class="form-floating">
                            <select class="form-select" {{$disabled}} id="client_id" name="client_id" aria-label="Floating label select example">
                                <option value="0" selected>Selecione o Cliente</option>
                                @foreach ($clients as $client)
                                    @if ($client->id == $order->client_id)
                                        <option selected value="{{$client->id}}">{{$client->name}}</option>
                                    @else
                                        <option value="{{$client->id}}">{{$client->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                            <label for="client_id">Cliente</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" {{$disabled}} id="sector" name="sector" maxlength="30" placeholder="Setor" value="{{$order->sector}}">
                            <label for="sector">Setor</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" {{$disabled}} id="req_name" name="req_name" maxlength="20" placeholder="Solicitante do Solicitante" value="{{$order->req_name}}">
                            <label for="req_name">Nome do Solicitante</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="date" class="form-control" {{$disabled}} id="req_date" name="req_date" placeholder="Data do Acionamento" value="{{$order->req_date}}">
                            <label for="req_date">Data do Acionamento</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="time" class="form-control" {{$disabled}} id="req_time" name="req_time" placeholder="Hora do Acionamento" value="{{$order->req_time}}">
                            <label for="req_time">Hora do Acionamento</label>
                        </div>

                        <div class="form-floating my-2">
                            <textarea id="req_descr" name="req_descr" {{$disabled}} maxlength="70" placeholder="Problema Relatado" class='autoExpand form-control' rows='1' data-min-rows='1' required>{{$order->req_descr}}</textarea>
                            <label for="req_descr">Problema Relatado</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" {{$disabled}} id="equipment" name="equipment" maxlength="70" placeholder="Equipamento" value="{{$order->equipment}}">
                            <label for="equipment">Equipamento</label>
                        </div>

                        @if(count($order->notes) > 0)
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            Mostrar serviços executados
                        </button>
                        
                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Registros anteriores</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div>
                                            @foreach ($order->notes as $note)
                                            <div>
                                                <div>Registro nº {{$note->id}}, Téc. {{$note->tecs->first()->id}}-{{$note->tecs->first()->user->name}},    {{date('d/m/Y',strtotime($note->date))}}</div>
                                                    <div class="mt-2"> 
                                                        <a href="{{route('notes.show', [
                                                            'order' => $order->id,
                                                            'note' => $note->id,
                                                        ])}}" class="btn btn-info btn-sm">
                                                            Exibir
                                                        </a>
                                                        
                                                        @if (!$loop->last)
                                                            <hr> 
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                        <div class="my-2">
                            @if (auth()->user()->adm()->first())
                                <button id="submitButton" type="submit" class="btn btn-primary me-2" data-bs-dismiss="modal">
                                    Confirma
                                </button>
                            @endif
                            <a href="{{route('orders.index')}}" class="btn btn-secondary">
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