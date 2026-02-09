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

                <div id="header" class="my-2">
                    <h2>Cadastrar Usuário</h2>
                </div>
                <hr>

                <main>
                    <form action="{{route('client.users.store')}}" id="form" method="post" autocomplete="on">
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
                            <input type="email" class="form-control" maxlength="50" id="email" name="email" placeholder="E-mail" required value="{{old('email')}}">
                            <label for="email">E-mail</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" maxlength="20" id="function" name="function" placeholder="Função" value="{{old('function')}}">
                            <label for="function">Função</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="username" name="username" value="{{old('username')}}" placeholder="Nome de Usúario" minlength="10" maxlength="100" required>
                            <label for="username">Nome de Usúario</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="password" class="form-control" id="password" minlength="5" maxlength="25" name="password" autocomplete="new-password" placeholder="Senha" required>
                            <label for="password">Senha</label>
                        </div>

                        <div class="form-floating my-2" id="div_password_confirmation">
                            <input type="password" class="form-control" id="password_confirmation" minlength="5" maxlength="25" name="password_confirmation" placeholder="Confirmar Senha" required>
                            <label for="password_confirmation">Confirmar Senha</label>
                        </div>

                        <fieldset><legend>Selecione as autorizações de acesso:</legend><br/>
                            <div id="client_access">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" name="can_create_sat" id="can_create_sat">
                                    <label class="form-check-label" for="can_create_sat">
                                        <strong>Criar solicitações</strong>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" name="can_see_sat" id="can_see_sat">
                                    <label class="form-check-label" for="can_see_sat">
                                        <strong>Visualizar solicitações</strong>
                                    </label>
                                </div>
                            </div>
                        </fieldset>

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