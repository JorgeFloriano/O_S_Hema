@extends('layouts.o_s_form_layout')

@section('content')

</head>

<body id="body">
    <div class="container">
        <div class="row mt-1">
            <div class="col-lg-8 offset-lg-2">

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
                    <h2>Cadastrar Código de Solução</h2>
                </div>
                <hr>
                <main>
                
                    <form action="{{route('solutions.store')}}" id="form" method="post" autocomplete="on">
                        @csrf
                        
                        <div class="form-floating my-2">
                            <input type="number" min="1" class="form-control" id="id" name="id" placeholder="Número" value="{{old('id')}}">
                            <label for="id">Número</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="description" name="description" maxlength="60" placeholder="Descrição" value="{{old('description')}}" required>
                            <label for="description">Descrição</label>
                        </div>

                        <div class="my-2">
                            <button id="submitButton" type="submit" class="btn btn-primary me-2" data-bs-dismiss="modal">
                                Confirma
                            </button>
                            <a href="{{route('solutions.index')}}" class="btn btn-secondary">
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