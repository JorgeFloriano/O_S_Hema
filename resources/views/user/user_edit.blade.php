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

                        @if (auth()->id() == $user->id && auth()->user()->isMainAdm() )
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" name="main_adm_tec_access" id="main_adm_tec_access" {{ $user->isTec() ? 'checked' : '' }}>
                                <label class="form-check-label" for="main_adm_tec_access">
                                    <strong>Acesso de Técnico</strong></strong>
                                </label>
                            </div>
                        @else
                            @if (!$user->isCli())
                                <div class="mt-4" id="permissoes">
    <h5 class="form-label fw-bold">Gerenciar permissões:</h5>
    
    <div class="row g-0 mb-3">
        <div class="col-md-5">
            <button class="btn btn-outline-primary w-100 my-1 first-group-btn collapsed d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAdm">
                <span><i class="fa fa-cogs me-1"></i> Adm.:</span>
                <span class="small text-muted">
                    @if ($user->hasPermission('sats')) <i class="fa fa-file-text-o ms-1"></i> @endif
                    @if ($user->hasPermission('users')) <i class="fa fa-user-o ms-1"></i> @endif
                    @if ($user->hasPermission('clients')) <i class="fa fa-handshake-o ms-1"></i> @endif
                    @if ($user->hasPermission('materials')) <i class="fa fa-hdd-o ms-1"></i> @endif
                    @if ($user->hasPermission('codes')) <i class="fa fa-bars ms-1"></i> @endif
                </span>
            </button>
            <div class="collapse multi-collapse" id="collapseAdm">
                <div class="card card-body shadow-sm mx-1 my-2">
                    <h6>Módulos Administrativos</h6>
                    <hr>
                    @php
                        $modulos = [
                            'sats' => ['label' => 'SATs', 'icon' => 'fa-file-text-o'],
                            'materials' => ['label' => 'Materiais', 'icon' => 'fa-hdd-o'],
                            'clients' => ['label' => 'Clientes', 'icon' => 'fa-handshake-o'],
                            'codes' => ['label' => 'Códigos', 'icon' => 'fa-bars'],
                            'users' => ['label' => 'Usuários', 'icon' => 'fa-user-o']
                        ];
                    @endphp
                    
                    @foreach($modulos as $key => $data)
                        @php
                            $currentPerm = $user->permissions->where('name', $key)->first();
                            $currentLevel = $currentPerm ? $currentPerm->pivot->access_level : 0;
                        @endphp
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <label class="small fw-bold mb-0 text-nowrap" for="permission_{{ $key }}">
                                <i class="fa {{ $data['icon'] }} me-1 text-muted"></i> {{ $data['label'] }}
                            </label>
                            <select name="permissions[{{ $key }}]" class="form-select form-select-sm ms-2" style="max-width: 120px;" id="permission_{{ $key }}">
                                <option value="0" {{ $currentLevel == 0 ? 'selected' : '' }}>Nenhum</option>
                                <option value="1" {{ $currentLevel == 1 ? 'selected' : '' }}>Leitura</option>
                                <option value="2" {{ $currentLevel == 2 ? 'selected' : '' }}>Completo</option>
                            </select>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <button class="btn btn-outline-primary w-100 my-1 mid-group-btn collapsed d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSup">
                <span><i class="fa fa-eye me-1"></i> Sup.:</span>
                <span class="small text-muted">
                    @if ($user->hasPermission('attach_tec')) <i class="fa fa-file-text-o ms-1"></i> @endif
                    @if ($user->hasPermission('manager_on_call')) <i class="fa fa-bell-o ms-1"></i> @endif
                    @if ($user->hasPermission('reopen_sat')) <i class="fa fa-rotate-left ms-1"></i> @endif
                </span>
            </button>
            <div class="collapse multi-collapse" id="collapseSup">
                <div class="card card-body shadow-sm mx-1 my-2">
                    <h6>Ações de Supervisão</h6>
                    <hr>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="compl_sup_access" name="permissions[compl_sup_access]" value="2">
                        <label class="form-check-label small fw-bold" for="compl_sup_access">Acesso Completo</label>
                    </div>

                    @php
                        $acoesSup = [
                            'attach_tec' => ['label' => 'Vincular SAT', 'icon' => 'fa-file-text-o'],
                            'manager_on_call' => ['label' => 'Sobreaviso', 'icon' => 'fa-bell-o'],
                            'reopen_sat' => ['label' => 'Reabrir SAT', 'icon' => 'fa-rotate-left']
                        ];
                    @endphp

                    @foreach($acoesSup as $key => $data)
                        <div class="form-check form-switch mb-2 d-flex justify-content-between align-items-center ps-0">
                            <label class="small fw-bold mb-0" for="{{ $key }}">
                                <i class="fa {{ $data['icon'] }} me-1 text-muted"></i> {{ $data['label'] }}
                            </label>
                            <input class="form-check-input sup-acess ms-0" type="checkbox" id="{{ $key }}" name="permissions[{{ $key }}]" value="2"
                            {{ $user->hasPermission($key, 2) ? 'checked' : '' }}>
                        </div>
                    @endforeach

                    <small class="text-muted">* Visualizar SATs é padrão para supervisores.</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <button class="btn btn-outline-primary w-100 my-1 last-group-btn collapsed d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTec">
                <span><i class="fa fa-wrench me-1"></i> Téc.:</span>
                @if ($user->isTec())
                    <span class="small text-muted">
                        <i class="fa fa-check" id='tec-icon'></i>
                    </span>
                @endif
            </button>
            <div class="collapse multi-collapse" id="collapseTec">
                <div class="card card-body shadow-sm mx-1 my-2">
                    <h6>Perfil Técnico</h6>
                    <hr>
                    <div class="form-check d-flex justify-content-between align-items-center ps-0">
                        <label class="small fw-bold mb-0" for="tech_access">
                            <i class="fa fa-check me-1 text-muted"></i> Habilitar Acesso
                        </label>
                        <input class="form-check-input ms-0" type="checkbox" id="tech_access" 
                            name="permissions[tech_access]" value="2"
                            {{ $user->isTec() ? 'checked' : '' }}>
                    </div>
                    <p class="small text-muted mt-2">O técnico visualiza apenas sua própria programação de serviços.</p>
                </div>
            </div>
        </div>
    </div>
</div>
                            @endif
                        @endif

                        @if ($user->isCli() && !$user->isHemaTeam())
                            <div class="form-floating my-2">
                                <input type="text" class="form-control" value="{{$user->clientStringForUnlabeledDList()}}"  disabled>
                                <label>Cliente</label>
                            </div>
                        @endif

                        <div class="my-2">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fa fa-check"></i> Confirma
                            </button>
                            <a href="{{route(auth()->id() == $user->id ? 'orders.index' : 'users.index')}}" class="btn btn-outline-primary">
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