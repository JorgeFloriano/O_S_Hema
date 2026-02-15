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

                        
                        <div class="my-4">
                            <h5 class="form-label">Tipo de Cadastro:</h5>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input"
                                 type="radio" name="type_user" id="hema_user" value="1">
                                <label class="form-check-label" for="hema_user">Usuário Hema</label>
                            </div>
                              
                            <div class="form-check form-check-inline">
                                <input class="form-check-input"
                                 type="radio" name="type_user" id="client_user" value="2">
                                <label class="form-check-label" for="client_user">Usuário Cliente</label>
                            </div>
                        </div>

                        <div class="mt-4" id="hema_field">
                            <h5 class="form-label">Selecionar permissões:</h5>
                            
                            <div class="row g-0 mb-3">
                                <div class="col-md-4">
                                    <button class="btn btn-outline-primary w-100 my-1 first-group-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAdm">
                                        Administrador 
                                    </button>
                                    <div class="collapse multi-collapse" id="collapseAdm">
                                        <div class="card card-body shadow-sm mx-1 my-2">
                                            <h6>Módulos Administrativos</h6>
                                            <hr>
                                            
                                            @php
                                                $modulos = [
                                                    'sats' => 'SATs',
                                                    'materials' => 'Materiais',
                                                    'clients' => 'Clientes',
                                                    'codes' => 'Códigos'
                                                    ];
                                                if (auth()->user()->isMainAdm()) {
                                                    $modulos['users'] = 'Usuários';
                                                }
                                            @endphp
                                            
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

                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="compl_sup_access" name="permissions[compl_sup_access]" value="2">
                                                <label class="form-check-label" for="compl_sup_access">Acesso Completo</label>
                                            </div>

                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input sup-acess" type="checkbox" id="attach_tec" name="permissions[attach_tec]" value="2">
                                                <label class="form-check-label" for="attach_tec">Vincular SAT / Técnico</label>
                                            </div>

                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input sup-acess" type="checkbox" id="manager_on_call" name="permissions[manager_on_call]" value="2">
                                                <label class="form-check-label" for="manager_on_call">Gerenciar Sobreaviso</label>
                                            </div>

                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input sup-acess" type="checkbox" id="reopen_sat" 
                                                    name="permissions[reopen_sat]" value="2">
                                                <label class="form-check-label" for="reopen_sat">Reabrir SAT</label>
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
                                                <input class="form-check-input" type="checkbox" id="tech_access" 
                                                    name="permissions[tech_access]" value="2">
                                                <label class="form-check-label" for="tech_access">Habilitar Acesso</label>
                                            </div>
                                            <p class="small text-muted mt-2">O técnico visualiza apenas sua própria programação de serviços.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id='client_field'>
                            <div id="client_select">
                                <x-datalist :objs="$clients" obj="client" tit="Selecionar Cliente" :val="old('client')" des="name"/>
                            </div>
                        </div>
                        
                        <div class="my-2">
                            <button id="submitButton" type="submit" class="btn btn-primary me-2" data-bs-dismiss="modal">
                                <i class="fa fa-check"></i> Confirma
                            </button>
                            <a href="{{route('users.index')}}" class="btn btn-outline-primary">
                                <i class="fa fa-arrow-left"></i> Voltar
                            </a>
                        </div>
                    </form>
                </main>
            </div>
        </div>
    </div>
@endsection

<script>
    // Executa após o carregamento do DOM
    document.addEventListener('DOMContentLoaded', function() {
        // Ativa a lógica para o grupo de Supervisor
        setupPermissionGroup('compl_sup_access', 'sup-acess');
        
        // Se no futuro tiver um grupo de Admin, basta adicionar uma linha:
        // setupPermissionGroup('compl_adm_access', 'adm-acess');

        // Nova lógica de alternância de tipo de usuário
        setupUserTypeToggle();
    });
</script>