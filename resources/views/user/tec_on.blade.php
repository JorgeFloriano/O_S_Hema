@extends('layouts.o_s_form_layout')

@section('content')
     <div class="container box">
        <div class="row">
            <div class="col">
                @if (session()->has('message'))
                <div class="alert alert-info" role="alert">
                    {{session()->get('message')}}
                </div>
                @endif
                <div id="header" class="my-2">
                    <h2>Colaboradores de Sobreaviso Emergencial</h2>
                </div>
                <hr>

                <a href="{{route('tec_on_stop_all_notifications')}}" class="btn btn-primary me-2" data-bs-toggle="tooltip" title="Para todos os ciclos de notificações de emergência ativos">
                    <i class="fa fa-stop-circle me-3"></i>Parar Alertas
                </a>

                @if (session('main') == auth()->user()->id)
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalBusinessHours">
                            <i class="fa fa-clock-o me-3"></i> Configurar Horário
                    </button>

                    <div class="modal fade" id="modalBusinessHours" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <form action="{{ route('update_business_hours') }}" method="POST">
                                @csrf
                                <div class="modal-content text-dark">
                                    <div class="modal-header bg-light">
                                        <h5 class="modal-title font-weight-bold">Configuração de Horário Comercial</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="text-muted small mb-4">
                                            Defina o início e fim do expediente. Fora destes horários, o sistema ativará o modo <strong>Emergencial</strong> automaticamente.
                                        </p>
                                        
                                        <div class="table-responsive">
                                            <table class="table table-borderless align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Dia da Semana</th>
                                                        <th>Início Expediente</th>
                                                        <th>Fim Expediente</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $days = [
                                                            0 => 'Domingo', 1 => 'Segunda-feira', 2 => 'Terça-feira', 
                                                            3 => 'Quarta-feira', 4 => 'Quinta-feira', 5 => 'Sexta-feira', 6 => 'Sábado'
                                                        ];
                                                        // Carregue os horários do banco ou inicie vazio
                                                        $currentSchedules = DB::table('business_hours')->get()->keyBy('day_of_week');
                                                    @endphp

                                                    @foreach($days as $index => $dayName)
                                                        <tr>
                                                            <td class="fw-bold">{{ $dayName }}</td>
                                                            <td>
                                                                <input type="time" name="hours[{{$index}}][start]" class="form-control" 
                                                                    value="{{ $currentSchedules[$index]->start_time ?? '00:00' }}">
                                                            </td>
                                                            <td>
                                                                <input type="time" name="hours[{{$index}}][end]" class="form-control" 
                                                                    value="{{ $currentSchedules[$index]->end_time ?? '00:00' }}">
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-success">Atualizar Horários</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
                
                <hr>

                <form action="{{route('tec_on_update')}}" id="form" method="post">

                    @csrf
                    <input type="hidden" name="_method" id="idNum" value="PUT">
                
                    <table class="table table-striped">
                        <thead class="table-dark">
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
                                            <input onchange="form.submit()" class="form-check-input" name="tec{{$tec->id}}" checked type="checkbox" value="1" id="tec{{$tec->id}}">
                                        @else
                                            <input onchange="form.submit()" class="form-check-input" name="tec{{$tec->id}}" type="checkbox" value="1" id="tec{{$tec->id}}">
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalClients{{$tec->id}}">
                                            Vincular Clientes ({{$tec->emergencyClients->count()}})
                                        </button>

                                        <div class="modal fade" id="modalClients{{$tec->id}}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content text-dark">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Clientes de {{$tec->user->name}}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body" style="max-height: 680px; overflow-y: auto;">
                                                        <div class="row">
                                                            <div class="modal-body">
                                                                <div class="alert alert-primary d-flex align-items-center mb-3" role="alert">
                                                                    <i class="fa fa-info-circle me-4 fs-4"></i> <div>
                                                                        Para melhor gerenciamento dos atendimentos e performance do sistema, recomenda-se vincular 1 ou 2, no máximo <strong>3 clientes</strong> por técnico.
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    @foreach($clients as $client)
                                                                        @endforeach
                                                                </div>
                                                            </div>

                                                            @foreach($clients as $client)
                                                                <div class="col-lg-4 col-md-6 col-12 mb-2 text-start">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" 
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
                                        @if (!$tec->emergency_order_id)
                                            Disponível
                                        @else
                                            Ocupado
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
                    <div>
                        {{$tecs->links()}}
                    </div>
                </form>
            </div>
        </div>
     </div>
@endsection
