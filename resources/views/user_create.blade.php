@extends('layouts.o_s_form_layout')

@section('content')

</head>

<body id="body">
    <div class="container">
        <div class="row mt-1">
            <div class="col-lg-8 offset-lg-2">

                @if (session()->has('message'))
                    <div class="alert alert-warning" role="alert">
                        {{session()->get('message')}}
                    </div>
                @endif

                <div id="header" class="my-2">
                    <h2>Cadastrar Usuário</h2>
                </div>
                <hr>

                <main>
                    <form action="{{route('users.store')}}" id="form" method="post" autocomplete="on">
                        @csrf
                        
                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Nome" required>
                            <label for="name">Nome</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="surname" name="surname" placeholder="Sobrenome">
                            <label for="surname">Sobrenome</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="function" name="function" placeholder="Função" required>
                            <label for="function">Função</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="email" class="form-control" id="email" name="email" value="@hema.com" placeholder="E-mail" required>
                            <label for="email">E-mail</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Senha" required>
                            <label for="password">Senha</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="password" class="form-control" id="confirm_pass" name="confirm_pass" placeholder="Confirmar Senha" required>
                            <label for="confirm_pass">Confirmar Senha</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" name="tec" id="tec" checked>
                            <label class="form-check-label" for="tec">
                                Acesso de Técnico
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" name="adm" id="adm">
                            <label class="form-check-label" for="adm">
                                Acesso de Administrador
                            </label>
                        </div>

                        <div class="my-2">
                            <button id="submitButton" type="submit" class="btn btn-primary me-2" data-bs-dismiss="modal">
                                Confirma
                            </button>
                            <a href="{{route('users.index')}}" class="btn btn-secondary">
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