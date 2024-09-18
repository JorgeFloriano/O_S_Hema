@extends('layouts.o_s_form_layout')

@section('content')

</head>

<body id="body">
    <div class="container">
        <div class="row mt-1">
            <div class="col-lg-8 offset-lg-2">

                <div id="header" class="my-2">
                    <h2>Mostrar teste pdf {{$order->id}}</h2>
                </div>
                <hr>
                <main>
                
                    <form action="{{route('orders.destroy', ['order' => $order->id])}}" id="form" method="post">
                        @csrf
                        
                        <input type="hidden" name="_method" id="idNum" value="DELETE">

                        <div class="form-floating my-2">
                            <textarea id="req_descr" name="req_descr" disabled class='autoExpand form-control' rows='1' data-min-rows='1'>{{$order->req_descr}}</textarea>
                            <label for="req_descr">Problema Relatado</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="client_id" name="client_id" disabled value="{{$order->client_id.' - '. $order->client->name}}">
                            <label for="client_id">Cliente</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="equipment" name="equipment" disabled value="{{$order->equipment}}">
                            <label for="equipment">Equipamento</label>
                        </div>

                        <div class="alert alert-warning">
                            Atenção, as informações desta ordem de serviço serão perdidas após a mesma ser deletada!
                        </div>

                        <div class="my-2">
                            <button id="submitButton" type="submit" class="btn btn-danger me-2" data-bs-dismiss="modal">
                                Delete
                            </button>
                            <a href="{{route('notes.index')}}" class="btn btn-secondary">
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