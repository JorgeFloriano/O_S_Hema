@extends('layouts.o_s_form_layout')

@section('content')
     <div class="container box">
        <div class="row">
            <div class="col">
                <x-live-toast-message></x-live-toast-message>

                <div id="header" class="my-3 d-flex flex-wrap justify-content-between align-items-center">
                    {{-- Título à esquerda --}}
                    <div id="header" class="my-2">
                        <h2>Colaboradores de Sobreaviso Emergencial</h2>
                    </div>

                    {{-- Botões à direita (quando couber) --}}
                    <div class="mb-2">
                        {{-- @if (session('main') == auth()->user()->id) --}}
                            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#modalStopAlerts">
                                    <i class="fa fa-stop-circle me-3"></i>Parar Alertas
                            </button>

                            <div class="modal fade" id="modalStopAlerts" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content text-dark">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Parar Notificações de Alerta!</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="modal-body">
                                                    <div class="alert alert-warning d-flex align-items-center mb-3" role="alert">
                                                        <i class="fa fa-exclamation-triangle me-4 fs-5" aria-hidden="true"></i> <div>
                                                            Este recurso deve ser usado com cautela, pois ele irá cancelar as notificações e indicativos de SAT emergencial para todos os técnicos.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Fechar</button>
                                            <a href="{{route('tec_on_stop_all_notifications')}}" class="btn btn-primary me-2" data-bs-toggle="tooltip" title="Para todos os ciclos de notificações de emergência ativos">
                                                <i class="fa fa-stop-circle me-3"></i>Parar Alertas
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalBusinessHours">
                                    <i class="fa fa-clock-o me-3"></i> Configurar Horário
                            </button>

                            <div class="modal fade" id="modalBusinessHours" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('update_business_hours') }}" method="POST">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Configuração de Horário Comercial</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="alert alert-primary d-flex align-items-center mb-3" role="alert">
                                                    <i class="fa fa-info-circle me-4 fs-4"></i> <div>
                                                        Qualquer SAT aberta fora do horário comercial, será considerada emergencial e o sistema iniciará o ciclo de notificações de alerta para os respectivos técnicos.
                                                    </div>
                                                </div>

                                                <div class="table-responsive">
                                                    <table class="table align-middle table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>Dia</th>
                                                                <th>Fechado</th>
                                                                <th>Abertura</th>
                                                                <th>Fechamento</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $days = [0 => 'Dom', 1 => 'Seg', 2 => 'Ter', 3 => 'Qua', 4 => 'Qui', 5 => 'Sex', 6 => 'Sáb'];
                                                                $currentSchedules = DB::table('business_hours')->get()->keyBy('day_of_week');
                                                            @endphp

                                                            @foreach($days as $index => $dayName)
                                                                @php 
                                                                    $isClosed = !isset($currentSchedules[$index]) || ($currentSchedules[$index]->is_closed ?? false);
                                                                @endphp
                                                                <tr>
                                                                    <td class="fw-bold">{{ $dayName }}</td>
                                                                    <td>
                                                                        <div class="form-check form-switch">
                                                                            <input class="form-check-input closed-toggle" type="checkbox" 
                                                                                name="hours[{{$index}}][is_closed]" value="1" 
                                                                                id="closed_{{$index}}" {{ $isClosed ? 'checked' : '' }}>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <input type="time" name="hours[{{$index}}][start]" 
                                                                            class="form-control time-input-{{$index}}" 
                                                                            value="{{ $currentSchedules[$index]->start_time ?? '08:00' }}"
                                                                            {{ $isClosed ? 'disabled' : '' }}>
                                                                    </td>
                                                                    <td>
                                                                        <input type="time" name="hours[{{$index}}][end]" 
                                                                            class="form-control time-input-{{$index}}" 
                                                                            value="{{ $currentSchedules[$index]->end_time ?? '18:00' }}"
                                                                            {{ $isClosed ? 'disabled' : '' }}>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Fechar</button>
                                                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        {{-- @endif --}}
                    </div>
                </div>


                <p>Solicitações de Assistência Técnica abertas fora do horário comercial serão consideredas emergenciais e o sistema iniciará o ciclo de notificações de alerta atravéz do aplicativo. Para que a informação chegue ao técnico desejado este dever estar com a opção de <strong>Ativo</strong> habilitado, estar vinculado ao <strong>Cliente</strong> que solicitou o serviço e sua <strong>Condição</strong> estar como disponível ( não está ocupado em outra SAT emergencial no momento ).</p>

                <hr>

                <form action="{{route('tec_on_update')}}" id="form" method="post">

                    @csrf
                    <input type="hidden" name="_method" id="idNum" value="PUT">
                
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-primary">
                                <tr>
                                    <th>Nº</th>
                                    <th>Nome</th>
                                    <th>Função</th>
                                    <th>Ativo</th>
                                    <th>Clientes</th>
                                    <th>Condição</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tecs as $tec)
                                    <tr>
                                        <td>{{$tec->id}}</td>
                                        <td>{{$tec->user->name}}</td>
                                        <td>{{$tec->user->function}}</td>
                                        <td>
                                            @if ($tec->on_call)
                                                <div class="form-check form-switch">
                                                    <input onchange="form.submit()" class="form-check-input" name="tec{{$tec->id}}" checked type="checkbox" value="1" id="tec{{$tec->id}}">
                                                </div>
                                            @else
                                                <div class="form-check form-switch">
                                                    <input onchange="form.submit()" class="form-check-input" name="tec{{$tec->id}}" type="checkbox" value="1" id="tec{{$tec->id}}">
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalClients{{$tec->id}}">
                                                Vincular Clientes ({{$tec->emergencyClients->count()}})
                                            </button>
                                            <div class="modal fade" id="modalClients{{$tec->id}}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content text-dark">
                                                        <div class="modal-header d-flex flex-column align-items-start">
                                                            <div class="d-flex justify-content-between w-100">
                                                                <h5 class="modal-title">Clientes de {{$tec->user->name}}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <button type="button" 
                                                                    class="btn btn-outline-secondary mt-2" 
                                                                    onclick="toggleSelectAll({{$tec->id}})">
                                                                Selecionar Todos
                                                            </button>
                                                        </div>
                                                        
                                                        <div class="modal-body" style="max-height: 680px; overflow-y: auto;">
                                                            <div class="row">
                                                                @foreach($clients as $client)
                                                                    <div class="col-lg-4 col-md-6 col-12 mb-2 text-start">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input client-checkbox-{{$tec->id}}" 
                                                                                type="checkbox"
                                                                                name="clients[{{$tec->id}}][]"
                                                                                value="{{$client->id}}"
                                                                                id="client{{$tec->id}}_{{$client->id}}"
                                                                                {{ $tec->emergencyClients->contains($client->id) ? 'checked' : '' }}>
                                                                            <label class="form-check-label" for="client{{$tec->id}}_{{$client->id}}">
                                                                                {{$client->name}}
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Fechar</button>
                                                            <button type="submit" class="btn btn-primary">Salvar Todos</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if (!$tec->busy)
                                                Disponível
                                            @else
                                                <a href="{{route('orders.edit', ['order' => Crypt::encryptString($tec->emergency_order_id)])}}">
                                                    <button type="button" class="btn btn-sm btn-outline-primary">
                                                        SAT {{$tec->emergency_order_id}}
                                                    </button>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div>
                        {{$tecs->links()}}
                    </div>
                </form>
            </div>
        </div>
     </div>

     <script>
        // Script para desativar os campos de hora quando o switch estiver ligado
        document.querySelectorAll('.closed-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const dayIndex = this.id.split('_')[1];
                const inputs = document.querySelectorAll('.time-input-' + dayIndex);
                inputs.forEach(input => input.disabled = this.checked);
            });
        });
    </script>
@endsection
