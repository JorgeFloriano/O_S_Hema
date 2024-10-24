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

                @if ($errors->any())
                    <div class="alert alert-warning">
                        <ul>
                            @foreach ($errors->all() as $msg)
                                <li>{{$msg}}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div id="header" class="my-2">
                    <h2>Gerar Ordem de Serviço</h2> 
                </div>
                <hr>
                <main>
                
                    <form action="{{route('orders.store')}}" id="form" method="post" autocomplete="on">
                        @csrf

                        <div class="form-floating">
                            <select class="form-select" id="client_id" name="client_id" aria-label="Floating label select example" required>
                                <option value="0">Selecione o Cliente</option>
                                @foreach ($clients as $client)
                                    <option value="{{$client->id}}">{{$client->id}} - {{$client->name}}</option>
                                @endforeach
                            </select>
                            <label for="client_id">Cliente</label>
                        </div>

                        <div class="form-floating my-2">
                            <select class="form-select" id="type_id" name="order_type_id" aria-label="Floating label select example" required>
                                <option value="0">Selecione o Tipo do Serviço</option>
                                @foreach ($types as $type)
                                    <option value="{{$type->id}}">{{$type->id}} - {{$type->description}}</option>
                                @endforeach
                            </select>
                            <label for="order_type_id">Tipo</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="sector" name="sector" maxlength="30" placeholder="Setor" required value={{old('sector')}}>
                            <label for="sector">Setor</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="req_name" name="req_name" maxlength="20" placeholder="Solicitante do Solicitante" value={{old('req_name')}}>
                            <label for="req_name">Nome do Solicitante</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="date" class="form-control" id="req_date" name="req_date" placeholder="Data do Acionamento" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}" required>
                            <label for="req_date">Data do Acionamento</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="time" class="form-control" id="req_time" name="req_time" placeholder="Hora do Acionamento" value="{{\Carbon\Carbon::now()->format('H:i')}}" required>
                            <label for="req_time">Hora do Acionamento</label>
                        </div>

                        <div class="form-floating my-2">
                            <textarea id="req_descr" name="req_descr" maxlength="470" placeholder="Problema Relatado" class='autoExpand form-control' rows='1' data-min-rows='1' required>{{old('req_descr')}}</textarea>
                            <label for="req_descr">Problema Relatado</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="equipment" name="equipment" maxlength="70" placeholder="Equipamento" value="{{old('equipment')}}">
                            <label for="equipment">Equipamento</label>
                        </div>

                        <div class="my-2">
                            <button id="submitButton" type="submit" class="btn btn-primary me-2" data-bs-dismiss="modal">
                                Confirma
                            </button>
                            @if (isset(auth()->user()->tec))
                                <a href="{{route('notes.index')}}" class="btn btn-secondary">
                            @else
                                <a href="{{route('orders.index')}}" class="btn btn-secondary">
                            @endif
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