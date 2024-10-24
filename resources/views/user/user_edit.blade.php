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

                @if (auth()->user()->id == $user->id)
                    <div id="header" class="my-2">
                        <h2>Editar Perfil </h2>
                    </div>
                @else
                    <div id="header" class="my-2">
                        <h2>Editar Cadastro do Usuário nº{{$user->id}} </h2>
                    </div>
                @endif
                <hr>

                <main>
                    <form action="{{route('users.update', ['user' => $user->id])}}" id="form" method="post" autocomplete="off">
                        @csrf

                        <input type="hidden" name="_method" id="idNum" value="PUT">
                        
                        <div class="form-floating my-2">
                            <input type="text" class="form-control" maxlength="20" id="name" name="name" value="{{$user->name}}" placeholder="Nome" required>
                            <label for="name">Nome</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" maxlength="20" id="surname" name="surname" value="{{$user->surname}}" placeholder="Sobrenome">
                            <label for="surname">Sobrenome</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" maxlength="20" id="function" name="function" value="{{$user->function}}" placeholder="Função" required>
                            <label for="function">Função</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="username" name="username" value="{{$user->username}}" placeholder="E-mail" min="10" max="100" required>
                            <label for="username">Nome de Usúario</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Senha" autocomplete="new-password">
                            <label for="password">Senha</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirmar Senha">
                            <label for="password_confirmation">Confirmar Senha</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" name="tec" {{$tec_checked}} id="tec">
                            <label class="form-check-label" for="tec">
                                Acesso de Técnico
                            </label>
                        </div>

                        {{-- This option will not be displayed if the main administrator is editing his own registration. --}}
                        @if (auth()->user()->id !== $user->id)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" name="adm" {{$adm_checked}} id="adm">
                                <label class="form-check-label" for="adm">
                                    Acesso de Administrador
                                </label>
                            </div>
                        @endif

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" name="sup" {{$sup_checked}} id="sup">
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