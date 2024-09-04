@extends('layouts.o_s_form_layout')

@section('content')

</head>

<body id="body">
    <div class="container">
        <div class="row mt-1">
            <div class="col-lg-8 offset-lg-2">

                <div id="header" class="my-2">
                    <h2>Deletar Cadastro do Cliente {{$client->id}}</h2>
                </div>
                <hr>
                <main>
                
                    <form action="{{route('clients.destroy', ['client' => $client->id])}}" id="form" method="post">
                        @csrf
                        
                        <input type="hidden" name="_method" id="idNum" value="DELETE">

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="name" name="name" value="{{$client->name}}" disabled>
                            <label for="name">Nome da Empresa</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="cnpj_cpf" name="cnpj_cpf" value="{{$client->cnpj_cpf}}" disabled>
                            <label for="cnpj_cpf">CNPJ</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="unit" name="unit" value="{{$client->unit}}" disabled>
                            <label for="unit">Unidade</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="address" name="address" value="{{$client->address}}" disabled>
                            <label for="address">Endereço</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="email" class="form-control" id="email" name="email" value="{{$client->email}}" disabled>
                            <label for="email">E-mail</label>
                        </div>

                        <div class="alert alert-danger">
                            Ao deleter o cadastro todos os dados deste cliente serão perdidos perdidos!
                        </div>
                        <div style="margin:10px 0;">

                        <button type="submit" class="btn btn-danger">DELETAR</button>

                        </div>
                    </form>
                </main>
            </div>
        </div>
    </div>
</body>
</html>
@endsection