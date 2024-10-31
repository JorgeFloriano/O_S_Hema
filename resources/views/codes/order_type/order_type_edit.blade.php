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
                    <h2>Editar Cadastro de Segmento de Serviço nº{{$order_type->id}}</h2>
                </div>
                <hr>
                <main>
                
                    <form action="{{route('order_types.update', ['order_type' => $order_type->id])}}" id="form" method="post" autocomplete="on">
                        @csrf
                        
                        <input type="hidden" name="_method" id="idNum" value="PUT">

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="description" name="description" maxlength="60" placeholder="Descrição" value="{{$order_type->description}}" required>
                            <label for="description">Descrição</label>
                        </div>

                        <div class="my-2">
                            <button id="submitButton" type="submit" class="btn btn-primary me-2">
                                Confirma
                            </button>
                            <a href="{{route('order_types.index')}}" class="btn btn-secondary">
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