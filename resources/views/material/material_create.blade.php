@extends('layouts.o_s_form_layout')

@section('content')

</head>

<body id="body">
    <div class="container box">
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
                    <h2>Cadastrar Material</h2>
                </div>
                <hr>
                <main>
                
                    <form action="{{route('materials.store')}}" id="form" method="post" autocomplete="on">
                        @csrf
                        
                        <div class="form-floating my-2">
                            <input type="number" min="1" class="form-control" id="id" name="id" placeholder="Número" value="{{old('id')}}">
                            <label for="id">Número</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="description" name="description" maxlength="25" placeholder="Descrição" value="{{old('description')}}" required>
                            <label for="description">Descrição</label>
                        </div>

                        <div class="form-floating my-2">
                            <select class="form-select" id="unit" name="unit" aria-label="Floating label select example" required >
                                <option value="1">Un</option>
                                <option value="2">M</option>
                                <option value="3">Kg</option>
                                <option value="4">L</option>
                                <option value="5">M²</option>
                                <option value="6">M³</option>
                                <option value="7">Kit</option>
                            </select>
                            <label for="unit">Unidade de Medida</label>
                        </div>

                        <div class="my-2">
                            <button id="submitButton" type="submit" class="btn btn-primary me-2">
                                Confirma
                            </button>
                            <a href="{{route('materials.index')}}" class="btn btn-secondary">
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