@extends('layouts.o_s_form_layout')

@section('content')

@php
    use Illuminate\Support\Facades\Crypt;
    use App\Models\Cli;
    use App\Class\TextFormat;
    $p = new TextFormat();
@endphp

{{-- container-sm was altered in my bootstrap.min.css --}}
<div class="container-sm box">
    <div class="row">
        <div class="col">
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

            <div id="header" class="my-3 d-flex flex-wrap justify-content-between align-items-center">
                {{-- Título à esquerda --}}
                <div class="mb-2">
                    <h2 class="mb-0">
                        {{-- Texto longo: Escondido em telas menores que 576px, visível em telas 'sm' ou maiores --}}
                        <span class="d-none d-sm-inline">Solicitações de Assistência Técnica</span>
                        
                        {{-- Texto curto: Visível em telas pequenas, escondido em telas 'sm' ou maiores --}}
                        <span class="d-inline d-sm-none">SATs</span>
                    </h2>
                </div>

                {{-- Botões à direita (quando couber) --}}
                <div class="mb-2">
                    <a href="{{route('orders.create')}}" class="btn btn-primary" data-bs-toggle="tooltip" title="Criar nova Solicitação de Assistência Técnica">
                        <i class="fa fa-plus"></i> Nova
                    </a>
                </div>
            </div>

            <form action="{{route('orders.filter')}}" id="filter_form" method="post">

                @csrf
                <div class="row g-2 mb-2">
                    <div class="col-xl-2 col-md-4 col-6">
                        <x-unlabeled-dlist :objs="$clients" obj="client" des="name" :val="$old_client ?? ''" :place="'Cliente (todos)'" onfoc="clearInputs('client', 'client_id' ,'0')"/>
                    </div>

                    <div class="col-xl-2 col-md-4 col-6">
                        <select class="form-select" size="0" id="finished" name="finished" aria-label="Floating label select example">
                            <option {{$fin_select[2] ?? ''}} value="2">SATs (todas)</option>
                            <option {{$fin_select[0] ?? ''}} value="0">Não Finalizadas</option>
                            <option {{$fin_select[1] ?? ''}} value="1">Finalizadas</option>
                        </select>
                    </div>

                    <div class="col-xl-2 col-md-4 col-6">
                        <x-unlabeled-dlist :objs="$tecs" obj="tec" des="user" :val="$old_tec ?? ''" :place="'Técnico (todos)'" subdes="name"
                        onfoc="clearInputs('tec', 'tec_id', '')"/>
                    </div>

                    <div class="col-xl-2 col-md-4 col-6">
                        <select class="form-select" id="date_type" name="date_type" aria-label="Floating label select example">
                            <option {{$order_open_select ?? ''}} value="order_open_date">Data de abertura</option>
                            <option {{$last_note_select ?? ''}} value="last_note_date">Última anotação</option>
                        </select>
                    </div>

                    <div class="col-xl-2 col-md-4 col-6">
                        <label for="Start" class="col-form-label" style="width: 20%;float: left">de</label>
                        <input type="date" class="form-control" id="Start" style="width: 80%;float: right" name="date_start" placeholder="Início" value="{{$date_s}}">
                    </div>
                    
                    <div class="col-xl-2 col-md-4 col-6">    
                        <label for="End" class="col-form-label" style="width: 20%;float: left">até</label>
                        <input type="date" class="form-control" id="End" style="width: 80%;float: right" name="date_end" placeholder="Término" value="{{$date_e}}">
                    </div>
                </div>
            </form>
            {{-- Alterado: justify-content-end para alinhar tudo à direita --}}
            <div class="my-3 d-flex flex-wrap justify-content-end align-items-center" id="buttons">
                
                @if ($orders->count() > 0)
                    @if ($adm)
                        {{-- Removido float-end (desnecessário com flexbox) e adicionado d-flex --}}
                        <div class="d-flex align-items-center">
                            <button onclick="formSubmit('filter_form')" data-bs-toggle="tooltip" title="Filtrar..." id="submitButton" type="submit" class="btn btn-outline-primary">
                                <i class="fa fa-filter"></i> Filtrar
                            </button>

                            <button type="button" data-bs-toggle="modal" data-bs-target="#reportTitle" class="btn btn-outline-primary mx-2">
                                <div data-bs-toggle="tooltip" title="Gerar relatório PDF">
                                    <i class="fa fa-file-pdf-o"></i> PDF
                                </div>
                            </button>

                            <button type="button" data-bs-toggle="tooltip" title="Gerar arquivo xlsx"
                                onclick="submitRoute('{{route('orders.orders_csv')}}', 'csv_form', '{{$able_btn ?? ''}}')"
                                class="btn btn-outline-primary">
                                <i class="fa fa-file-excel-o"></i> CSV
                            </button>
                        </div>
                    @endif

                    @if (!$adm && $sup)
                        <div>
                            <button onclick="formSubmit('filter_form')" data-bs-toggle="tooltip" title="Filtrar..." id="submitButton" type="submit" class="btn btn-outline-primary">
                                <i class="fa fa-filter"></i> Filtrar
                            </button>
                        </div>
                    @endif
                @endif

                {{-- Forms invisíveis (não afetam o alinhamento visual) --}}
                <form action="{{route('orders.orders_csv')}}" id="csv_form" method="post">
                    @csrf
                    <input type="hidden" name="csv_ids" id="csv_ids" value="{{$ids ?? '0'}}">
                </form>
                
                <form action="{{route('orders.orders_pdf')}}" id="pdf_form" method="post">
                    @csrf
                    <input type="hidden" name="ids" id="ids" value="{{$ids ?? '0'}}">
                    <div class="modal fade" id="reportTitle" tabindex="-1" aria-labelledby="reportTitleLabel" aria-hidden="true">
                        <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h5 class="modal-title" id="reportTitleLabel">Novo título da capa (opcional)</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="text" name="title" maxlength="120" class="form-control" id="title"
                                placeholder="Padrão: Relatório de Solicitações de Assistência Técnica">
                            </div>
                            <div class="modal-footer">
                            <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Fechar</button>
                            <button onclick="submitRoute('{{route('orders.orders_pdf')}}', 'pdf_form', '{{$able_btn ?? ''}}')" class="btn btn-primary">Gerar Relatório</button>
                            </div>
                        </div>
                        </div>
                    </div>
                </form>
                @if ($orders->count() === 0)
                    <p>Nenhum registro encontrado !</p>
                @endif
            </div>

            <hr>
                <div class="table-responsive" style="overflow-x: visible">
                    <table class="table table-striped table-hover" id="orders_list" style="overflow-x: visible">
                        <thead class="table-primary">
                            <tr>
                                <th>Nº</th>
                                <th>Cliente</th>
                                <th>Equipamento</th>
                                <th>Problema relatado</th>
                                <th style="min-width: 160px">Técnico</th>
                                <th>Data</th>
                                <th><i class="fa fa-bars"></i></th>
                                <th><i style="font-size: 20px;" class="fa fa-exclamation-circle"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>{{number_format($order->id, 0, ',', '.')}}</td>
                                    <td>{{$order->client->name ?? ''}}</td>
                                    <td>{{$order->equipment ?? 'Não informado'}}</td>
                                    <td>{{$p->spaceAfterPunctuation($order->req_descr) ?? ''}}</td>
                                    <td>
                                        @if ($order->finished || (!$main && !$sup))
                                            <input class="form-control" disabled id="ord_{{$order->id}}" value="{{$order->tec->user->name ?? 'Indefinido'}} - [{{$order->tec->id ?? ''}}]">
                                        @else
                                            <x-unlabeled-dlist :objs="$tecs" obj="tec" des="user" place="Não selecionado" val="{{$order->tec->user->name ?? ''}} - [{{$order->tec->id ?? ''}}]" ind="{{$order->id}}" subdes="name" onfoc="clearInputs('{{$order->id}}tec', '{{$order->id}}tec_id' ,'0'), updateOrderTec('{{$order->id}}', 0)"/>
                                        @endif
                                    </td>
                                    <td>{{date('d/m/y',strtotime($order->req_date))}}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button style="background-color: transparent; border: none;" data-bs-toggle="dropdown">
                                                <i class="fa fa-ellipsis-v" ></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <button class="dropdown-item copy-button"
                                                        style="border: none;"
                                                        data-order-id="{{$order->id}}">
                                                        <i class="fa fa-copy"></i>
                                                        Copiar Dados
                                                    </button>
                                                    <!-- Hidden input with order data -->
                                                    <input type="hidden" class="order-data" value="SAT Nº {{$order->id ?? ''}}<br>CLIENTE: {{$order->client->name ?? ''}}<br>SERVIÇO: {{$order->type->description ?? ''}}<br>SETOR: {{$order->sector ?? ''}}<br>NOME DO SOLICITANTE: {{$order->req_name ?? ''}}<br>DATA DO ACIONAMENTO: {{date('d/m/y',strtotime($order->req_date)) ?? ''}}<br>HORA DO ACIONAMENTO: {{date('H:i',strtotime($order->req_time)) ?? ''}}<br>PROBLEMA RELATADO: {{$order->req_descr ?? ''}}">
                                                </li>

                                                <li>
                                                    @if ($order->finished)
                                                        <a href="{{route('orders.show_pdf', ['order' => Crypt::encryptString($order->id)])}}" class="dropdown-item">
                                                            <i class="fa fa-file-pdf-o"></i>
                                                            Visualizar
                                                        </a>
                                                    @else
                                                        <a href="{{route('orders.edit', ['order' => Crypt::encryptString($order->id)])}}" class="dropdown-item">
                                                            @php
                                                                // Set icon,if order is created by client or user isn't main adm SAT cant be edited
                                                                $icon = 'edit';
                                                                $text = 'Editar';
                                                                $ord_creator_is_cli = Cli::where('user_id', $order->user_id)->first();
                                                                if (isset($ord_creator_is_cli) || !$main) {
                                                                    $icon = 'file-text-o';
                                                                    $text = 'Visualizar';
                                                                }
                                                            @endphp
                                                            <i class="fa fa-{{$icon}}"></i>
                                                            {{$text}}
                                                        </a>
                                                    @endif
                                                </li>

                                                @if ($main) 
                                                    <li><hr class="dropdown-divider"></li>

                                                    <li>
                                                        <a href="{{route('orders.show', ['order' => Crypt::encryptString($order->id)])}}" class="dropdown-item text-danger">
                                                            <i class="fa fa-trash"></i>
                                                            Excluir
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        @if ($order->finished)
                                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" 
                                                style="width: 20px; height: 20px; font-size: 0.8rem; font-weight: bold;" 
                                                title="Finalizada">
                                                F
                                            </div>
                                        @else
                                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center" 
                                                style="width: 20px; height: 20px; font-size: 0.8rem; font-weight: bold;background-color: #fd7e14;" 
                                                title="Pendente">
                                                P
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Clipboard.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.10/clipboard.min.js"></script>
<script src="{{asset('assets/js/copy_handler.js')}}"></script>

{{-- This will output the correct base URL --}}
<script>window.appBaseUrl = "{{ url('/') }}"</script>
@endsection