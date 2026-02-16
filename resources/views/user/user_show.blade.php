@extends('layouts.o_s_form_layout')

@section('content')
    <div class="container box">
        <div class="row mt-1">
            <div class="col-lg-8 offset-lg-2">

                <div id="header" class="my-2">
                    <h2>Detalhes do Usuário nº{{$user->id}}</h2>
                    <p class="text-muted">Visualização completa do perfil e permissões atribuídas.</p>
                </div>
                <hr>

                <main>
                    <div class="form-floating my-2">
                        <input type="text" class="form-control" value="{{$user->name.' '.$user->surname}}" readonly>
                        <label class="fw-bold">Nome Completo</label>
                    </div>

                    <div class="form-floating my-2">
                        <input type="text" class="form-control" value="{{$user->function}}" readonly>
                        <label class="fw-bold">Função</label>
                    </div>

                    <div class="form-floating my-2">
                        <input type="text" class="form-control" value="{{$user->username}}" readonly>
                        <label class="fw-bold">Nome de Usuário</label>
                    </div>

                    <div class="mt-4">
                        <h5 class="form-label fw-bold">Permissões Ativas:</h5>
                        
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <div class="card h-100 shadow-sm border-primary">
                                    <div class="card-header bg-primary text-white py-2">
                                        <i class="fa fa-cogs"></i> Administrador
                                    </div>
                                    <div class="card-body p-2">
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
                                                $perm = $user->permissions->where('name', $key)->first();
                                                $level = $perm ? $perm->pivot->access_level : 0;
                                            @endphp
                                            <div class="d-flex justify-content-between align-items-center mb-1 border-bottom pb-1">
                                                <small><i class="fa {{ $data['icon'] }} text-muted"></i> {{ $data['label'] }}</small>
                                                <span class="badge {{ $level == 2 ? 'bg-success' : ($level == 1 ? 'bg-info' : 'bg-secondary text-white-50') }}">
                                                    {{ $level == 2 ? 'Total' : ($level == 1 ? 'Leitura' : 'Nenhum') }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card h-100 shadow-sm border-primary">
                                    <div class="card-header bg-primary text-white py-2">
                                        <i class="fa fa-eye"></i> Supervisor
                                    </div>
                                    <div class="card-body p-2">
                                        @php
                                            $sup_perms = [
                                                'attach_tec' => ['label' => 'Vincular SAT', 'icon' => 'fa-file-text-o'],
                                                'manager_on_call' => ['label' => 'Sobreaviso', 'icon' => 'fa-bell-o'],
                                                'reopen_sat' => ['label' => 'Reabrir SAT', 'icon' => 'fa-rotate-left']
                                            ];
                                        @endphp

                                        @foreach($sup_perms as $key => $data)
                                            <div class="d-flex justify-content-between align-items-center mb-1 border-bottom pb-1">
                                                <small><i class="fa {{ $data['icon'] }} text-muted"></i> {{ $data['label'] }}</small>
                                                @if($user->hasPermission($key, 2))
                                                    <i class="fa fa-check-circle text-success"></i>
                                                @else
                                                    <i class="fa fa-times-circle text-light"></i>
                                                @endif
                                            </div>
                                        @endforeach
                                        <div class="mt-2 text-center">
                                            <small class="text-muted" style="font-size: 0.7rem;">* Acesso padrão de leitura a SATs</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card h-100 shadow-sm border-primary">
                                    <div class="card-header bg-primary text-white py-2">
                                        <i class="fa fa-wrench"></i> Técnico
                                    </div>
                                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                        @if($user->isTec())
                                            <i class="fa fa-check-square-o fa-3x text-success"></i>
                                            <p class="fw-bold mt-2 mb-0">Habilitado</p>
                                            <small class="text-muted">Acesso à agenda técnica</small>
                                        @else
                                            <i class="fa fa-square-o fa-3x text-muted"></i>
                                            <p class="text-muted mt-2 mb-0">Desabilitado</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
@endsection