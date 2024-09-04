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

                        <div class="form-floating">
                            <select class="form-select" id="client_id" name="client_id" aria-label="Floating label select example">
                                <option selected>Selecione o Cliente</option>
                                @foreach ($clients as $client)
                                    <option value="{{$client->id}}">{{$client->name}}</option>
                                @endforeach
                            </select>
                            <label for="client_id">Cliente</label>
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
                            <textarea id="req_descr" name="req_descr" placeholder="Problema Relatado" class='autoExpand form-control' rows='1' data-min-rows='1'>{{$order->req_descr}}</textarea>
                            <label for="req_descr">Problema Relatado</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="equipment" name="equipment" placeholder="Equipamento" value="{{$order->equipment}}">
                            <label for="equipment">Equipamento</label>
                        </div>

                        <button id="submit_button" type="submit" class="btn btn-primary my-2 me-2" data-bs-dismiss="modal">
                            Confirma
                        </button>

                        </div>
                    </form>
                </main>
            </div>
        </div>
    </div>
</body>
</html>
@endsection