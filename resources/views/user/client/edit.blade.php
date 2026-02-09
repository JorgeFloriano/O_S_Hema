@extends('layouts.o_s_form_layout')

@section('content')
    <div class="container box">
        <div class="row mt-1">
            <div class="col-lg-8 offset-lg-2">

                <x-live-toast-message></x-live-toast-message>

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
                        <p class="text-muted">A atualização de dados como nome e sobrenome será refletida em todos os campos onde aparecem, incluindo SATs antigas e novas.</p>
                    </div>
                @endif
                <hr>

                <main>
                    <form action="{{route('client.users.update', ['user' => $user->id])}}" id="form" method="post" autocomplete="off">
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
                            <input type="email" class="form-control" maxlength="50" id="email" name="email" placeholder="E-mail" required value="{{$user->email}}">
                            <label for="email">E-mail</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" maxlength="20" id="function" name="function" value="{{$user->function}}" placeholder="Função">
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

                        @if (auth()->user()->id !== $user->id)
                            <fieldset><legend>Selecione as autorizações de acesso:</legend><br/>
                                <div id="client_access">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" name="can_create_sat" id="can_create_sat" {{$can_create_sat_checked}}>
                                        <label class="form-check-label" for="can_create_sat">
                                            <strong>Criar solicitações</strong>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" name="can_see_sat" id="can_see_sat" {{$can_see_sat_checked}}>
                                        <label class="form-check-label" for="can_see_sat">
                                            <strong>Visualizar solicitações</strong>
                                        </label>
                                    </div>
                                </div>
                            </fieldset>
                        @endif

                        <div class="my-2">
                            <button id="submitButton" type="submit" class="btn btn-primary me-2" data-bs-dismiss="modal">
                                <i class="fa fa-check"></i> Confirma
                            </button>
                            <a href="{{route('client.users.index')}}" class="btn btn-outline-primary">
                                <i class="fa fa-arrow-left"></i> Voltar
                            </a>
                        </div>
                    </form>
                </main>
            </div>
        </div>
    </div>
@endsection