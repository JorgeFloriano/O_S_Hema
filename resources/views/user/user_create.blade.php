@extends('layouts.o_s_form_layout')

@section('content')
    <div class="container box">
        <div class="row mt-1">
            <div class="col-lg-8 offset-lg-2">

                @if (session()->has('message'))
                    <div class="alert alert-warning" role="alert">
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

                        <fieldset><legend>Selecione um ou mais perfis:</legend>
                            <div id="hema_profiles">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" name="tec" id="tec">
                                    <label class="form-check-label" for="tec">
                                        <strong>Técnico</strong>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input onchange="enableDisable(['cli'])" class="form-check-input" type="checkbox" value="1" name="adm" id="adm">
                                    <label class="form-check-label" for="adm">
                                        <strong>Administrador</strong>
                                    </label>
                                
                                    <div class="form-check">
                                        <input disabled class="form-check-input" type="checkbox" value="1" name="cli" id="cli">
                                        <label class="form-check-label" for="cli">
                                            Acesso a Clientes e Materiais
                                        </label>
                                    </div>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" name="sup" id="sup">
                                    <label class="form-check-label" for="sup">
                                        <strong>Supervisor</strong>
                                    </label>
                                </div>
                            </div>

                            <div class="form-check">
                                <input onchange="enableDisable(['tec', 'adm', 'sup']), showAndHideElement('client_optios', 'hema_profiles')" class="form-check-input" type="checkbox" value="1" name="user_client" id="user_client">
                                <label class="form-check-label" for="user_client">
                                    <strong>Usuário para Clientes</strong>
                                </label>
                            </div>

                            <div id="client_optios" style="display: none">
                                <x-datalist :objs="$clients" obj="client" tit="Cliente" :val="old('client')" des="name" req='required'/>
                            </div>
                        </fieldset>

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
@endsection