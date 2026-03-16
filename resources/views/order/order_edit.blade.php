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
                    <h2>{{$title}} SAT 
                        @if ($order->is_emergency) Emergencial @endif (OS) Nº {{number_format($order->id, 0, ',', '.')}}</h2> 
                </div>
                <hr>
                <main>
                    <form action="{{route('orders.update', ['order' => $order->id])}}" id="form" method="post" autocomplete="on">
                        @csrf
                        
                        <input type="hidden" name="_method" id="idNum" value="PUT">

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" disabled id="adm_id" name="adm_id" placeholder="Editada por" value="{{$user->name ?? ''}}" required>
                            <label for="adm_id">Editada por</label>
                        </div>

                        <x-datalist :objs="$clients" obj="client" tit="Cliente" :val="$order->client->name.' - ['.$order->client->id.']'" des="name" req='required' :disabl="$inputs_prop"/>

                        <div class="form-floating my-2">
                            @if ($inputs_prop == 'readonly')
                                <input type="text" class="form-control" disabled value="{{$order->type->id.' - '.$order->type->description}}">
                                <input hidden type="text" class="form-control" id="type_id" name="order_type_id" value="{{$order->type->id}}">
                            @else
                                <select class="form-select" {{$inputs_prop}} id="type_id" name="order_type_id" aria-label="Floating label select example">
                                    @foreach ($types as $type)
                                        @if ($type->id == $order->order_type_id)
                                            <option selected value="{{$type->id}}">{{$type->id.' - '.$type->description}}</option>
                                        @else
                                            <option value="{{$type->id}}">{{$type->id.' - '.$type->description}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            @endif
                            <label for="type_id">Tipo</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" {{$inputs_prop}} id="sector" name="sector" maxlength="30" placeholder="Setor" value="{{$order->sector ?? ''}}" required>
                            <label for="sector">Setor</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" {{$inputs_prop}} id="req_name" name="req_name" maxlength="20" placeholder="Solicitante do Solicitante" value="{{$order->req_name ?? ''}}">
                            <label for="req_name">Nome do Solicitante</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="date" max="{{now()->format('Y-m-d')}}" class="form-control" {{$inputs_prop}} id="req_date" name="req_date" placeholder="Data do Acionamento" value="{{$order->req_date}}" required>
                            <label for="req_date">Data do Acionamento</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="time" class="form-control" {{$inputs_prop}} id="req_time" name="req_time" placeholder="Hora do Acionamento" value="{{$order->req_time}}" required>
                            <label for="req_time">Hora do Acionamento</label>
                        </div>

                        <div class="form-floating my-2">
                            @if ($inputs_prop == 'readonly')
                                <textarea id="req_descr" name="req_descr" hidden maxlength="470" placeholder="Problema Relatado" class='autoExpand form-control' rows='1' data-min-rows='1'>{{$order->req_descr}}</textarea>

                                <textarea disabled maxlength="470" placeholder="Problema Relatado" class='autoExpand form-control' rows='1' data-min-rows='1'>{{$order->req_descr}}</textarea>
                            @else
                                <textarea id="req_descr" name="req_descr" {{$inputs_prop}} maxlength="470" placeholder="Problema Relatado" class='autoExpand form-control' rows='1' data-min-rows='1' required>{{$order->req_descr}}</textarea>
                            @endif

                            <label for="req_descr">Problema Relatado</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" {{$equipment_prop}} id="equipment" name="equipment" maxlength="70" placeholder="Equipamento" value="{{$order->equipment}}">
                            <label for="equipment">Equipamento</label>
                        </div>

                        @if(count($order->notes) > 0)
                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                Mostrar serviços executados
                            </button>
                            
                            <!-- Modal -->
                            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Registros anteriores</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div>
                                                @foreach ($order->notes as $note)
                                                <div>
                                                    <div>Registro nº {{$note->id}}, Téc. {{$note->tecs->first()->id}}-{{$note->tecs->first()->user->name}},    {{date('d/m/Y',strtotime($note->date))}}</div>
                                                        <div class="mt-2"> 
                                                            <a href="{{route('notes.show', [
                                                                'note' => Crypt::encryptString($note->id),
                                                            ])}}" class="btn btn-outline-primary btn-sm">
                                                                Exibir
                                                            </a>
                                                            
                                                            @if (!$loop->last)
                                                                <hr> 
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Fechar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="my-2">
                            @if ($confirm_button ?? false)
                                <button id="submitButton" type="submit" class="btn btn-primary me-2" data-bs-dismiss="modal">
                                    <i class="fa fa-check"></i> Confirma
                                </button>
                            @endif
                            <a href="{{route(session('reference_router_back') ?? 'orders.index')}}" class="btn btn-outline-primary">
                                <i class="fa fa-arrow-left"></i> Voltar
                            </a>
                        </div>
                    </form>
                </main>
            </div>
        </div>
    </div>
@endsection