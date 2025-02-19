@extends('layouts.o_s_form_layout')

@section('content')

@php
    use Illuminate\Support\Facades\Crypt;
@endphp

<div class="container box">
    <div class="row">
        <div class="col">
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

            <div id="header" class="my-2">
                <h2>
                    Ordens de Serviço
                </h2>
            </div>
            <hr>

            <form action="{{route('orders.filter')}}" id="filter_form" method="post">

                @csrf
                <div class="row g-2 mb-2">
                    <div class="col-xl-2 col-md-4 col-6">
                        <x-unlabeled-dlist :objs="$clients" obj="client" des="name" :val="$old_client ?? ''" :place="'Cliente (todos)'" onfoc="clearInputs('client', 'client_id' ,'0')"/>
                    </div>

                    <div class="col-xl-2 col-md-4 col-6">
                        <select class="form-select" id="finished" name="finished" aria-label="Floating label select example">
                            <option {{$fin_select[2] ?? ''}} value="2">Ordens (todas)</option>
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
            @if ($adm)
                <a href="{{route('orders.create')}}" class="btn btn-primary" data-bs-toggle="tooltip" title="Criar nova ordem de serviço">
                    Criar nova
                </a>
                
                <div class="float-end">
                    <button onclick="formSubmit('filter_form')" data-bs-toggle="tooltip" title="Filtrar ordens de serviço conforme opções   selecionadas" id="submitButton" type="submit" class="btn btn-secondary">
                        <i class="fa fa-filter"></i>
                    </button>

                    <!-- Button trigger modal -->
                    <button type="button" data-bs-toggle="modal" data-bs-target="#reportTitle" class="btn btn-danger mx-1">
                        <i class="fa fa-file-pdf-o" data-bs-toggle="tooltip" title="Gerar relatório PDF das ordens de serviço filtradas"></i>
                    </button>

                    <button type="button" data-bs-toggle="tooltip" title="Gerar arquivo xlsx (Excel) das ordens de serviço filtradas" 
                        onclick="submitRoute('{{route('orders.orders_csv')}}', 'csv_form', '{{$able_btn ?? ''}}')" 
                        class="btn btn-success">
                        <i class="fa fa-file-excel-o"></i>
                    </button>
                </div>
            @endif
            <hr>

            <form action="{{route('orders.orders_csv')}}" id="csv_form" method="post">
                @csrf
                <input type="hidden" name="ids" id="ids" value="{{$ids ?? '0'}}">
            </form>
            
            <!-- Modal -->
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
                            <input type="text" name="title" class="form-control" maxlength="120" id="title" 
                            placeholder="Padrão: Relatório de Solicitações de Assistência Técnica">
                        </div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Gerar Relatório</button>
                        </div>
                    </div>
                    </div>
                </div>
            </form>

            @if ($orders->count() === 0)
                <p>
                    Nenhum registro encontrado !
                </p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="orders_list">
                        <thead class="table-dark">
                            <tr>
                                <th>Nº</th>
                                <th style="min-width: 150px">Cliente</th>
                                <th>Problema relatado</th>
                                <th style="min-width: 170px">Técnico</th>
                                <th>Data</th>
                                @if ($adm)
                                    <th>Edit</th>
                                    <th>Del.</th>
                                @else
                                    <th>Ver</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>{{number_format($order->id, 0, ',', '.')}}</td>
                                    <td>{{$order->client->name ?? ''}}</td>
                                    <td>{{$order->req_descr}}</td>
                                    <td>
                                        @if ($order->finished || (!$main && !$sup))
                                            <input class="form-control" disabled id="ord_{{$order->id}}" value="{{$order->tec->user->name ?? 'Indefinido'}} - [{{$order->tec->id ?? ''}}]">
                                        @else
                                            <x-unlabeled-dlist :objs="$tecs" obj="tec" des="user" place="Não selecionado" val="{{$order->tec->user->name ?? ''}} - [{{$order->tec->id ?? ''}}]" ind="{{$order->id}}" subdes="name" onfoc="clearInputs('{{$order->id}}tec', '{{$order->id}}tec_id' ,'0'), updateOrderTec('{{$order->id}}', 0)"/>
                                        @endif
                                    </td>
                                    <td>{{date('d/m/y',strtotime($order->req_date))}}</td>
                                    @if ($order->finished)
                                        <td>
                                            <a href="{{route('orders.show_pdf', ['order' => Crypt::encryptString($order->id)])}}" class="btn btn-outline-danger btn-sm">
                                                <i class="fa fa-file-pdf-o"></i>
                                            </a>
                                        </td>
                                        @if ($main)
                                            <td>
                                                <a href="{{route('orders.show', ['order' => Crypt::encryptString($order->id)])}}" class="btn btn-danger btn-sm">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        @else
                                            @if ($adm)
                                                <td>
                                                    <a class="btn btn-danger btn-sm disabled">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </td>
                                            @endif
                                        @endif
                                    @else
                                        @if ($adm)
                                            <td>
                                                <a href="{{route('orders.edit', ['order' => Crypt::encryptString($order->id)])}}" class="btn btn-primary btn-sm">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </td>
                                        @else
                                            <td>
                                                <a href="{{route('orders.edit', ['order' => Crypt::encryptString($order->id)])}}" class="btn btn-primary btn-sm">
                                                    <i class="fa fa-file-text"></i>
                                                </a>
                                            </td>
                                        @endif
                                        @if ($order->notes->count() > 0)
                                            @if ($main)
                                                <td>
                                                    <a href="{{route('orders.show', ['order' => Crypt::encryptString($order->id)])}}" class="btn btn-danger btn-sm">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </td>
                                            @else
                                                @if ($adm)
                                                    <td>
                                                        <a class="btn btn-danger btn-sm disabled">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </td>
                                                @endif
                                            @endif
                                        @else
                                            <td>
                                                @if ($adm)
                                                    <a href="{{route('orders.show', ['order' => Crypt::encryptString($order->id)])}}" class="btn btn-danger btn-sm">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        @endif
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection