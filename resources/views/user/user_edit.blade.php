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

                        @if (auth()->id() == $user->id)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" name="tec" id="tec" {{$tec_checked}}>
                                <label class="form-check-label" for="tec">
                                    <strong>Acesso de Técnico</strong></strong>
                                </label>
                            </div>
                        @else
                            @if (!$user->isCli())
                                <div class="mt-4" id="permissoes">
                                    <h5 class="form-label fw-bold">Gerenciar permissões:</h5>
                                    
                                    <div class="row g-0 mb-3">
                                        <div class="col-md-4">
                                            <button class="btn btn-outline-primary w-100 my-1 first-group-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAdm">
                                                Administrador
                                            </button>
                                            <div class="collapse multi-collapse" id="collapseAdm">
                                                <div class="card card-body shadow-sm mx-1 my-2">
                                                    <h6>Módulos Administrativos</h6>
                                                    <hr>
                                                    @php $modulos = ['sats' => 'SATs', 'users' => 'Usuários', 'materials' => 'Materiais', 'clients' => 'Clientes', 'codes' => 'Códigos']; @endphp
                                                    
                                                    @foreach($modulos as $key => $label)
                                                        <div class="mb-2 d-flex justify-content-between align-items-center">
                                                            <label class="small fw-bold mb-0 text-nowrap" for="permission_{{ $key }}">{{ $label }}</label>
                                                            <select name="permissions[{{ $key }}]" class="form-select form-select-sm ms-2" style="max-width: 160px;" id="permission_{{ $key }}">
                                                                <option value="0">Sem Acesso</option>
                                                                <option value="1">Somente Leitura</option>
                                                                <option value="2">Acesso Completo</option>
                                                            </select>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <button class="btn btn-outline-primary w-100 my-1 mid-group-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSup">
                                                Supervisor
                                            </button>
                                            <div class="collapse multi-collapse" id="collapseSup">
                                                <div class="card card-body shadow-sm mx-1 my-2">
                                                    <h6>Ações de Supervisão</h6>
                                                    <hr>
                                                    <div class="form-check form-switch mb-2">
                                                        <input class="form-check-input" type="checkbox" id="reopen_sat" name="permissions[reopen_sat]" value="2">
                                                        <label class="form-check-label" for="reopen_sat">Reabrir SAT</label>
                                                    </div>
                                                    <div class="form-check form-switch mb-2">
                                                        <input class="form-check-input" type="checkbox" id="attach_tec" name="permissions[attach_tec]" value="2">
                                                        <label class="form-check-label" for="attach_tec">Vincular SAT / Técnico</label>
                                                    </div>
                                                    <div class="form-check form-switch mb-2">
                                                        <input class="form-check-input" type="checkbox" id="manager_on_call" name="permissions[manager_on_call]" value="2">
                                                        <label class="form-check-label" for="manager_on_call">Gerenciar Sobreaviso</label>
                                                    </div>
                                                    <small class="text-muted">* Visualizar SATs é padrão para supervisores.</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <button class="btn btn-outline-primary w-100 my-1 last-group-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTec">
                                                Técnico
                                            </button>
                                            <div class="collapse multi-collapse" id="collapseTec">
                                                <div class="card card-body shadow-sm mx-1 my-2">
                                                    <h6>Perfil Técnico</h6>
                                                    <hr>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="tech_access" name="permissions[tech_access]" value="2">
                                                        <label class="form-check-label" for="tech_access">Habilitar Acesso Técnico</label>
                                                    </div>
                                                    <p class="small text-muted mt-2">O técnico visualiza apenas sua própria programação de serviços.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                        <div class="my-2">
                            <button id="submitButton" type="submit" class="btn btn-primary me-2" data-bs-dismiss="modal">
                                <i class="fa fa-check"></i> Confirma
                            </button>
                            <a href="{{route(auth()->id() == $user->id ? 'orders.index' : 'users.index')}}" class="btn btn-outline-primary">
                                <i class="fa fa-arrow-left"></i> Voltar
                            </a>
                        </div>

                        <div id="client_select" style="display: {{$client_select_display}};">
                            <x-datalist :objs="$clients" obj="client" tit="Cliente" :val="$client_selected" des="name" disabl="disabled"/>
                        </div>

                    </form>
                </main>
            </div>
        </div>
    </div>
@endsection


{{-- <fieldset><legend>Selecione um ou mais perfis:</legend><br/>
    <div id="hema_profiles" style="display: {{$hema_profiles_display}}">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" name="tec" id="tec" {{$tec_checked}}>
            <label class="form-check-label" for="tec">
                <strong>Técnico (Hema)</strong></strong>
            </label>
        </div>

       


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
</fieldset> --}}