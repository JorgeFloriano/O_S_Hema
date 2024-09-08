@extends('layouts.o_s_form_layout')

@section('content')

</head>

<body id="body">
    <div class="container">
        <div class="row mt-1">
            <div class="col-lg-8 offset-lg-2">
                <div id="header" class="my-2">
                    <h2>Cadastro de Cliente</h2>
                </div>
                <hr>
                <main>
                
                    <form action="{{route('clients.store')}}" id="form" method="post" autocomplete="on">
                        @csrf
                        
                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Nome da Empresa">
                            <label for="name">Nome da Empresa</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="cnpj_cpf" name="cnpj_cpf" placeholder="CNPJ">
                            <label for="cnpj_cpf">CNPJ</label>
                        </div>

                        <div class="form-floating my-2">
                            <input onchange="getAddress()" type="text" class="form-control" id="cep" name="cep" placeholder="CEP (preenche o endereço automáticamente)">
                            <label for="cep">CEP (preenche o endereço automáticamente)</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="unit" name="unit" placeholder="Unidade">
                            <label for="unit">Unidade</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="address" name="address" placeholder="Endereço">
                            <label for="address">Endereço</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="email" class="form-control" id="email" name="email" placeholder="E-mail">
                            <label for="email">E-mail</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="phone" name="phone" placeholder="Telefone">
                            <label for="phone">Telefone</label>
                        </div>
                        
                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="contact" name="contact" placeholder="Nome do Contato">
                            <label for="contact">Nome do Contato</label>
                        </div>
                            <button id="submitButton" name="submit_button" type="submit" class="btn btn-primary my-2 me-2" data-bs-dismiss="modal">
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