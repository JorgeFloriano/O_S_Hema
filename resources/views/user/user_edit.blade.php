@extends('layouts.o_s_form_layout')

@section('content')
    <div class="container box">
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

                        <fieldset><legend>Selecione um ou mais perfis:</legend><br/>
                            <div id="hema_profiles" style="display: {{$hema_profiles_display}}">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" name="tec" id="tec" {{$tec_checked}}>
                                    <label class="form-check-label" for="tec">
                                        <strong>Técnico (Hema)</strong></strong>
                                    </label>
                                </div>
                                {{-- This option will not be displayed if the main administrator is editing his own registration. --}}
                                @if (auth()->user()->id !== $user->id)
                                    <div class="form-check">
                                        <input onchange="enableDisable(['cli'], '{{$user_client_checked}}')" class="form-check-input" type="checkbox" value="1" name="adm" id="adm" {{$adm_checked}} >
                                        <label class="form-check-label" for="adm">
                                            <strong>Administrador (Hema)</strong>
                                        </label>
                                
                                        <div class="form-check">
                                            <input {{$cli_disabled}} {{$cli_checked}} class="form-check-input" type="checkbox" value="1" name="cli" id="cli">
                                            <label class="form-check-label" for="cli">
                                                Acesso a Clientes e Materiais
                                            </label>
                                        </div>
                                    </div>
                                @endif
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" name="sup" id="sup" {{$sup_checked}}>
                                    <label class="form-check-label" for="sup">
                                        <strong>Supervisor (Hema)</strong>
                                    </label>
                                </div>
                            </div>
                            @if (auth()->user()->id !== $user->id)
                                <div class="form-check">
                                    <input 
                                        {{$user_client_checked}} 
                                        onchange="
                                        enableDisable(['tec', 'adm', 'sup'], '{{$user_client_checked}}'),
                                        showAndHideElement('client_select', 'hema_profiles', '{{$user_client_checked}}')" 
                                        class="form-check-input"
                                        type="checkbox"
                                        value="1"
                                        name="user_client"
                                        id="user_client">
                                    <label class="form-check-label" for="user_client">
                                        <strong>Cliente (Clientes)</strong>
                                    </label>
                                </div>
                            @endif

                            <div id="client_select" style="display: {{$client_select_display}};">
                                <x-datalist :objs="$clients" obj="client" tit="Cliente" :val="$client_selected" des="name"/>
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