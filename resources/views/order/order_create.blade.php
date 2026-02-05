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
                    <h2>Gerar Solicitação de Assistência Técnica</h2> 
                </div>
                <hr>
                <main>
                
                    <form action="{{route(auth()->user()->isCli() ? 'client.orders.store' : 'orders.store')}}" id="form" method="post" autocomplete="on">
                        @csrf

                        @if(!auth()->user()->isCli())
                            <x-datalist :objs="$clients" obj="client" tit="Cliente" :val="old('client')" des="name" req='required'/>
                        @endif

                        <x-selected-old :tab="$types" nam="order_type_id" nom="Serviço" des="description"/>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="sector" name="sector" maxlength="30" placeholder="Setor" required value="{{old('sector')}}">
                            <label for="sector">Setor</label>
                        </div>

                        @if(!auth()->user()->isCli())
                            <div class="form-floating my-2">
                                <input type="text" class="form-control" id="req_name" name="req_name" maxlength="20" placeholder="Solicitante do Solicitante" required value={{old('req_name')}}>
                                <label for="req_name">Nome do Solicitante</label>
                            </div>

                            <div class="form-floating my-2">
                                <input type="date" class="form-control" id="req_date" name="req_date" placeholder="Data do Acionamento" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}" required>
                                <label for="req_date">Data do Acionamento</label>
                            </div>

                            <div class="form-floating my-2">
                                <input type="time" class="form-control" id="req_time" name="req_time" placeholder="Hora do Acionamento" value="{{\Carbon\Carbon::now()->format('H:i')}}" required>
                                <label for="req_time">Hora do Acionamento</label>
                            </div>
                        @endif

                        <div class="form-floating my-2">
                            <textarea id="req_descr" name="req_descr" maxlength="470" placeholder="Problema Relatado" class='autoExpand form-control' rows='1' data-min-rows='1' required>{{old('req_descr')}}</textarea>
                            <label for="req_descr">Problema Relatado</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="equipment" name="equipment" maxlength="70" placeholder="Equipamento" value="{{old('equipment')}}">
                            <label for="equipment">Equipamento</label>
                        </div>
                        @if (auth()->user()->isCli())
                            <p class="mb-2 mt-4 text-muted">Selecione o campo abaixo apenas se precisa de atendimento urgente fora do horário comercial!</p>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" name="is_emergency" id="is_emergency">
                                <label class="form-check-label" for="is_emergency">
                                    <strong>Emergencial </strong> <icon style="color: red" class="fa fa-exclamation-circle"></icon>
                                </label>
                            </div>
                        @endif

                        <div class="my-2">
                            <button id="submitButton" type="submit" class="btn btn-primary me-2" data-bs-dismiss="modal">
                                <i class="fa fa-check"></i> Confirma
                            </button>
                            @if (auth()->user()->isAdm() || auth()->user()->isSup())
                                <a href="{{route(session('reference_router_back') ?? 'orders.index')}}" class="btn btn-outline-primary">
                            @elseif (auth()->user()->isTec())
                                <a href="{{route(session('reference_router_back') ?? 'notes.index')}}" class="btn btn-outline-primary">
                            @elseif (auth()->user()->isCli())
                                <a href="{{route('client.orders.index')}}" class="btn btn-outline-primary">
                            @endif
                                <i class="fa fa-arrow-left"></i> Voltar
                            </a>
                        </div>
                    </form>
                </main>
            </div>
        </div>
    </div>
@endsection