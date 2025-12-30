@extends('layouts.o_s_form_layout')

@section('content')

@php
    use Illuminate\Support\Facades\Crypt;
@endphp
    <div class="container box">
        <div class="row mt-1">
            <div class="col-lg-6 offset-lg-3">

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

                <div id="header" class="text-center my-2">
                    <strong>Solicitação de Assistência Técnica nº <span style="color: red">{{number_format($order->id, 0, ',', '.')}}</span></strong>
                </div>
                <main>
                    
                    <div class="mx-0 my-2 border border-dark rounded">
                        <div class="p-1"><strong>Cliente: </strong>{{$order->client->name}}</div>
                        <div class="border-top border-dark p-1"><strong>Unidade: </strong>{{$order->client->unit}}</div>
                        <div class="border-top border-dark p-1"><strong>Endereço: </strong>{{$order->client->address}}</div>
                        <div class="border-top border-dark p-1"><strong>Contato: </strong>{{$order->req_name}}</div>
                        <div class="border-top border-dark p-1"><strong>Setor: </strong>{{$order->sector}}</div>
                        <div class="border-top border-dark p-1"><strong>Anotado por: </strong>{{$order->user->name ?? ''}}</div>
                    </div>

                    <div class="mx-0 my-2 border border-dark rounded">
                        <div class="p-1"><strong>Data do Acionamento: </strong>{{date('d/m/Y',strtotime($order->req_date))}}</div>
                    </div>
                     
                    <div class="mx-0 my-2 border border-dark rounded">
                        <div class="p-1"><strong>Hora do Acionamento: </strong>{{date('H:i',strtotime($order->req_time))}}</div>
                    </div>
                    
                    <div class="mx-0 my-2 border border-dark rounded">
                        <div class="p-1"><strong>Problema Relatado: </strong>{{$order->req_descr}}</div>
                        <div class="border-top border-dark p-1"><strong>Equipamento: </strong>{{$order->equipment ?? ''}}</div>
                    </div>

                    @if(count($order->notes) > 0)
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            Mostrar serviços anteriores
                        </button>
                        
                        <div class="mt-3">
                            <strong>Registrar informações da atividade</strong>
                        </div>
                        
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
                                                <div>Registro nº {{$note->id ?? ''}}, Téc. {{$note->tecs->first()->id ?? ''}}-{{$note->tecs->first()->user->name ?? ''}},    {{date('d/m/Y',strtotime($note->date ?? ''))}}</div>
                                                    <div class="mt-2"> 
                                                        @if ($note->tecs->first()->user_id ?? 0 == auth()->user()->id)
                                                            <a href="{{route('notes.edit', [
                                                                'note' => Crypt::encryptString($note->id),
                                                            ])}}" class="btn btn-primary btn-sm">
                                                                Editar
                                                            </a>
                                                            <a href="{{route('notes.show', [
                                                                'note' => Crypt::encryptString($note->id),
                                                            ])}}" class="btn btn-danger btn-sm">
                                                                Excluir
                                                            </a>
                                                        @else
                                                            <a href="{{route('notes.show', [
                                                                'note' => Crypt::encryptString($note->id),
                                                            ])}}" class="btn btn-info btn-sm">
                                                                Exibir
                                                            </a>
                                                        @endif
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

                    <form action="{{route('notes.store')}}" id="form" method="post">
                        @csrf
                        
                        <input type="hidden" name="order_id" id="order_id" value="{{$order->id}}">

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="equipMod" name="equip_mod" value="{{old('equip_mod')}}" placeholder="Modelo do Equipamento" maxlength="20" required>
                            <label for="equipMod">Modelo do Equipamento</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="equipId" name="equip_id" value="{{old('equip_id')}}" placeholder="Número de Série" maxlength="20" required>
                            <label for="equipId">Número de Identificação</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" name="equip_type" class="form-control" id="equipType" value="{{old('equip_type')}}" placeholder="Tipo" maxlength="20" required>
                            <label for="equipType">Tipo do Equipamento</label>
                        </div>

                        <x-selected-old nam="note_type_id" :tab="$types" des="description" nom="Tipo de Atendimento"/>

                        <x-selected-old nam="defect_id" :tab="$defects" des="description" nom="Defeito"/>

                        <x-selected-old nam="cause_id" :tab="$causes" des="description" nom="Causa"/>
                        
                        <x-selected-old nam="solution_id" :tab="$solutions" des="description" nom="Solução"/>

                        {{-- <x-datalist :objs="$materials" obj="material" tit="Selecionar Materiais utilizados" :val="old('material')" des="description" type="list" req=""/> --}}

                        <select class="form-select" onchange="manageList('material_id')" name="material_id" id="material_id" aria-label="Default select example">
                            <option selected>Registrar Materiais utilizados</option>
                            @foreach ($materials as $material)
                                <option value="{{$material->id}}" data-unit="{{$material->unit}}">{{$material->description}}</option>
                            @endforeach
                        </select>

                        <input hidden name="material_ids_array" id="material_ids_array">
                        <div id="material_id_list"></div>

                        <div id="serv" class="form-floating my-2">
                            <textarea id="services" name="services" placeholder="Serviços executados" maxlength="1290" class='autoExpand form-control' rows='1' data-min-rows='1' required>{{old('services')}}</textarea>
                            <label for="services">Descrição dos Serviços Executados</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="date" class="form-control" id="date" required name="date" placeholder="Data do Atendimento" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}">
                            <label for="date">Data do Atendimento</label>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="time" class="form-control" id="goStart" name="go_start" placeholder="Saída (Ida)">
                                    <label for="goStart">Saída (Ida)</label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <input type="time" class="form-control" id="goEnd" name="go_end" placeholder="Chegada (Ida)">
                                    <label for="goEnd">Chegada (Ida)</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="time" class="form-control" id="Start" required name="start" placeholder="Início" value={{\Carbon\Carbon::now()->format('H:i')}}>
                                    <label for="Start">Início</label>
                                </div>
                            </div>
                            <div class="col">    
                                <div class="form-floating">
                                    <input type="time" class="form-control" id="End" required name="end" placeholder="Término" value={{\Carbon\Carbon::now()->format('H:i')}}>
                                    <label for="End">Término</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="time" class="form-control" id="backStart" name="back_start" placeholder="Saída (Volta)">
                                    <label for="backStart">Saída (Volta)</label>
                                </div>
                            </div>
                            <div class="col"> 
                                <div class="form-floating">
                                    <input type="time" class="form-control" id="backEnd" name="back_end" placeholder="Chegada (Volta)">
                                    <label for="goStart">Chegada (Volta)</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-floating my-2">
                            <select class="form-select" id="firstTec" required name="first_tec" aria-label="Floating label select example">
                                @foreach ($tecs as $tec)
                                    @if (auth()->user()->tec->id == $tec->id)
                                        <option selected value="{{$tec->id}}">{{$tec->id}} - {{$tec->user->name}}</option>
                                    @else
                                        <option value="{{$tec->id}}">{{$tec->id}} - {{$tec->user->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                            <label for="firstTec">Técnico 01</label>
                        </div>

                        <button onclick="scrollToBottom()" class="btn btn-info" id="pen1" href="#" class="signature-button" data-bs-toggle="modal"     data-bs-target="#signature1Modal"><i class="fa fa-pencil" aria-hidden="true"></i>Assinar
                        </button>

                        <!-- Modal signature 01-->
                        <div style="position: fixed" class="modal fade" id="signature1Modal" tabindex="-1" aria-labelledby="signature1ModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                    <h5 class="modal-title" id="signature1ModalLabel">Assinatura do Técnico 01</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body signature">
                                        <canvas height="200" width="320" class="signature-pad" id="canv1"></canvas>
                                    </div>

                                    <div class="modal-footer">
                                        <a id="okSign1" href="#" class="signature-button" data-bs-dismiss="modal">
                                            <i class="fa fa-check" aria-hidden="true" ></i>Ok
                                        </a>
                                        <a id="clear1" href="#" class="signature-button">
                                            <i class="fa fa-eraser" aria-hidden="true"></i>Apagar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="sign_t_1" id="idSignTec1">

                        <div class="form-floating my-2">
                            <select class="form-select" id="secondTec" name="second_tec" aria-label="Floating label select example">
                                <option value="0">Selecione o Técnico 02</option>
                                @foreach ($tecs as $tec)
                                    <option value="{{$tec->id}}">{{$tec->id}} - {{$tec->user->name}}</option>
                                @endforeach
                            </select>
                            <label for="secondTec">Técnico 02</label>
                        </div>

                        <button class="btn btn-info mb-2" id="pen2" href="#" class="signature-button" data-bs-toggle="modal" data-bs-target="#signature2Modal"><i class="fa fa-pencil" aria-hidden="true"></i>Assinar
                        </button>

                        <!-- Modal signature 02-->
                        <div class="modal fade" id="signature2Modal" tabindex="-1" aria-labelledby="signature2ModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                    <h5 class="modal-title" id="signature2ModalLabel">Assinatura do Técnico 02</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    
                                    <div class="modal-body signature">
                                        <canvas height="200" width="320" class="signature-pad" id="canv2" aria-placeholder="assine aqui"></canvas>
                                        <input type="hidden" name="sign_t_2" id="idSignTec2">
                                    </div>

                                    <div class="modal-footer">
                                    <a id="okSign2" href="#" class="signature-button" data-bs-dismiss="modal"><i class="fa fa-check" aria-hidden="true"></i>Ok </a>
                                    <a id="clear2" href="#" class="signature-button"><i class="fa fa-eraser" aria-hidden="true"></i>Apagar </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="my-2">
                            <div class="form-check">
                                <input required class="form-check-input" onchange="toggleClientFields()" type="radio" name="finished" id="save" value="0">
                                <label class="form-check-label" for="save"><i class="fa fa-floppy-o" aria-hidden="true"></i>Salvar (atendimento pendente)</label>
                            </div>
                              
                            <div class="form-check">
                                <input required class="form-check-input" onchange="toggleClientFields()" type="radio" name="finished" id="finished" value="1">
                                <label class="form-check-label" for="finished"><i class="fa fa-check-square-o" aria-hidden="true"></i>Concluir (atendimento finalizado)</label>
                            </div>
                        </div>

                        <div id='clientFields' style="display: none">
                            <div class="form-floating my-2">
                                <input type="text" name="cl_name" class="form-control" id="clName" value="{{old('cl_name')}}" placeholder="Cliente" maxlength="40">
                                <label for="clName">Nome do Cliente:</label>
                            </div>
                            <div class="form-floating my-2">
                                <input type="text" name="cl_function" class="form-control" id="clFunction" value="{{old('cl_function')}}" placeholder="Função" maxlength="40">
                                <label for="clFunction">Função:</label>
                            </div>
                            <div class="form-floating my-2">
                                <input type="text" name="cl_contact" class="form-control" id="clContact" value="{{old('cl_contact')}}" placeholder="Contato" maxlength="40">
                                <label for="clContact">Contato:</label>
                            </div>
                            <button class="btn btn-info mb-2" id="pen3" href="#" class="signature-button" data-bs-toggle="modal" data-bs-target="#signature3Modal"><i class="fa fa-pencil" aria-hidden="true"></i>Assinatura do Cliente
                            </button>
                            <!-- Modal signature 03-->
                            <div class="modal fade" id="signature3Modal" tabindex="-1" aria-labelledby="signature3ModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h5 class="modal-title" id="signature3ModalLabel">Assinatura do Cliente</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                            
                                        <div class="modal-body signature">
                                            <canvas height="200" width="320" class="signature-pad" id="canv3" aria-placeholder="assine aqui"></canvas>
                                            <input type="hidden" name="cl_sign" id="idSignCl">
                                        </div>
                                        <div class="modal-footer">
                                        <a id="okSign3" href="#" class="signature-button" data-bs-dismiss="modal"><i class="fa fa-check" aria-hidden="true"></i>Ok </a>
                                        <a id="clear3" href="#" class="signature-button"><i class="fa fa-eraser" aria-hidden="true"></i>Apagar </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="my-2">
                            <button id="submitButton" type="button" class="btn btn-primary me-2" data-bs-dismiss="modal">
                                Confirma
                            </button>
                            <a href="{{route('notes.index')}}" class="btn btn-outline-primary">
                                Voltar
                            </a>
                        </div>
                    </form>
                    <script src="{{asset('assets/js/signature.js')}}"></script>
                    <script src="{{asset('assets/js/signature2.js')}}"></script>
                    <script src="{{asset('assets/js/signature3.js')}}"></script>
                </main>
            </div>
        </div>
    </div>
@endsection