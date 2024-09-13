@extends('layouts.o_s_form_layout')

@section('content')

</head>

<body id="body">
    <div class="container">
        <div class="row mt-1">
            <div class="col-lg-8 offset-lg-2">
                <div id="header" class="my-2">
                    <h2>Gerar Ordem de Serviço</h2> 
                </div>
                <hr>
                <main>
                
                    <form action="{{route('orders.store')}}" id="form" method="post" autocomplete="on">
                        @csrf

                        <div class="form-floating">
                            <select class="form-select" id="client_id" name="client_id" aria-label="Floating label select example">
                                <option selected value="0">Selecione o Cliente</option>
                                @foreach ($clients as $client)
                                    <option value="{{$client->id}}">{{$client->id}} - {{$client->name}}</option>
                                @endforeach
                            </select>
                            <label for="client_id">Cliente</label>
                        </div>

                        <div class="form-floating my-2">
                            <select class="form-select" id="tec_id" name="user_id" aria-label="Floating label select example">
                                <option selected value="0">Selecione o Técnico</option>
                                @foreach ($tecs as $tec)
                                    <option value="{{$tec->id}}">{{$tec->id}} - {{$tec->name}}</option>
                                @endforeach
                            </select>
                            <label for="tec_id">Técnico</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="date" class="form-control" id="req_date" name="req_date" placeholder="Data do Acionamento" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}">
                            <label for="req_date">Data do Acionamento</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="time" class="form-control" id="req_time" name="req_time" placeholder="Hora do Acionamento" value="{{\Carbon\Carbon::now()->format('H:i')}}">
                            <label for="req_time">Hora do Acionamento</label>
                        </div>

                        <div class="form-floating my-2">
                            <textarea id="req_descr" name="req_descr" placeholder="Problema Relatado" class='autoExpand form-control' rows='1' data-min-rows='1'></textarea>
                            <label for="req_descr">Problema Relatado</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="equipment" name="equipment" placeholder="Equipamento">
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