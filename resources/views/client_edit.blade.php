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
                    <h2>Editar Cadastro do Cliente nº{{$client->id}}</h2>
                </div>
                <hr>
                <main>
                
                    <form action="{{route('clients.update', ['client' => $client->id])}}" id="form" method="post" autocomplete="on">
                        @csrf
                        
                        <input type="hidden" name="_method" id="idNum" value="PUT">

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="name" name="name" maxlength="20" placeholder="Nome da Empresa" value="{{$client->name}}">
                            <label for="name">Nome da Empresa</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="cnpj_cpf" name="cnpj_cpf" maxlength="40" placeholder="CNPJ" value="{{$client->cnpj_cpf}}">
                            <label for="cnpj_cpf">CNPJ</label>
                        </div>

                        <div class="form-floating my-2">
                            <input onchange="getAddress()" type="text" class="form-control" id="cep" name="cep" maxlength="20" placeholder="CEP (preenche o endereço automáticamente)" value="{{$client->cep}}">
                            <label for="cep">CEP (autopreenche o endereço)</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="unit" name="unit" maxlength="20" placeholder="Unidade" value="{{$client->unit}}">
                            <label for="unit">Unidade</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="address" name="address" maxlength="80" placeholder="Endereço" value="{{$client->address}}">
                            <label for="address">Endereço</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="email" class="form-control" id="email" name="email" maxlength="50" placeholder="E-mail" value="{{$client->email}}">
                            <label for="email">E-mail</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="phone" name="phone" maxlength="20" placeholder="Telefone" value="{{$client->phone}}">
                            <label for="phone">Telefone</label>
                        </div>
                        
                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="contact" name="contact" maxlength="20" placeholder="Nome do Contato" value="{{$client->contact}}">
                            <label for="contact">Nome do Contato</label>
                        </div>
                        <div class="my-2">
                            <button id="submitButton" type="submit" class="btn btn-primary me-2">
                                Confirma
                            </button>
                            <a href="{{route('clients.index')}}" class="btn btn-secondary">
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