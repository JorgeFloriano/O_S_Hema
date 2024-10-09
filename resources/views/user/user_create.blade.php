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

                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $msg)
                            <div>{{$msg}}</div>
                        @endforeach
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
                            <input type="text" class="form-control" maxlength="20" id="name" name="name" placeholder="Nome" required value="{{old('name')}}">
                            <label for="name">Nome</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" maxlength="20" id="surname" name="surname" placeholder="Sobrenome" value="{{old('surname')}}">
                            <label for="surname">Sobrenome</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" maxlength="20" id="function" name="function" placeholder="Função" required value="{{old('function')}}">
                            <label for="function">Função</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="email" class="form-control" id="email" name="email" value="@hema.com.br" placeholder="E-mail" required>
                            <label for="email">E-mail</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="password" class="form-control" id="password" minlength="5" maxlength="100" name="password" placeholder="Senha" required>
                            <label for="password">Senha</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="password" class="form-control" id="confirm_pass" minlength="5" maxlength="100" name="confirm_pass" placeholder="Confirmar Senha" required>
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

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" name="sup" id="sup">
                            <label class="form-check-label" for="sup">
                                Acesso de Supervisor
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