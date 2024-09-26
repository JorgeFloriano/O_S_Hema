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
                    <h2>Editar Ordem de Serviço Nº {{$order->id}}</h2> 
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
                            <select class="form-select" id="client_id" name="client_id" aria-label="Floating label select example">
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
                            <input type="text" class="form-control" id="sector" name="sector" maxlength="30" placeholder="Setor" value="{{$order->sector}}">
                            <label for="sector">Setor</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="req_name" name="req_name" maxlength="20" placeholder="Solicitante do Solicitante" value="{{$order->req_name}}">
                            <label for="req_name">Nome do Solicitante</label>
                        </div>

                        <div class="form-floating my-2">
                            <select class="form-select" id="tec_id" name="tec_id" aria-label="Floating label select example">
                                <option selected value="0">Selecione o Técnico</option>
                                @foreach ($tecs as $tec)
                                    @if ($tec->id == $order->tec_id)
                                        <option selected value="{{$tec->id}}">{{$tec->id}} - {{$tec->user->name}}</option>  
                                    @else
                                        <option value="{{$tec->id}}">{{$tec->id}} - {{$tec->user->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                            <label for="tec_id">Técnico</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="date" class="form-control" id="req_date" name="req_date" placeholder="Data do Acionamento" value="{{$order->req_date}}">
                            <label for="req_date">Data do Acionamento</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="time" class="form-control" id="req_time" name="req_time" placeholder="Hora do Acionamento" value="{{$order->req_time}}">
                            <label for="req_time">Hora do Acionamento</label>
                        </div>

                        <div class="form-floating my-2">
                            <textarea id="req_descr" name="req_descr" maxlength="70" placeholder="Problema Relatado" class='autoExpand form-control' rows='1' data-min-rows='1' required>{{$order->req_descr}}</textarea>
                            <label for="req_descr">Problema Relatado</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="equipment" name="equipment" maxlength="70" placeholder="Equipamento" value="{{$order->equipment}}">
                            <label for="equipment">Equipamento</label>
                        </div>

                        <div class="my-2">
                            <button id="submitButton" type="submit" class="btn btn-primary me-2" data-bs-dismiss="modal">
                                Confirma
                            </button>
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