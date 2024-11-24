@extends('layouts.o_s_form_layout')

@section('content')

@php
    use Illuminate\Support\Facades\Crypt;
@endphp
     <div class="container">
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
                    <h2>Ordens de Serviço</h2>
                </div>
                <hr>

                @if ($adm)
                    <div class="my-2">
                        <a href="{{route('orders.create')}}" class="btn btn-primary me-2">Criar nova</a>

                        <button onclick="formSubmit('filter_form')" id="submitButton" type="submit" class="btn btn-secondary">
                            <i class="fa fa-filter" aria-hidden="true"></i> Filtrar
                        </button>

                        <a href="{{route('orders.orders_pdf', ['ids' => $order_ids])}}" class="btn-outline-danger">
                            <button class="btn btn-outline-danger" style="float: right" {{$show_pdf_btn ?? ''}}>
                                <i class="fa fa-file-pdf-o"></i> Gerar PDF
                            </button>
                        </a>
                    </div>
                @else
                    <div class="my-2" style="height: 42px">
                        <a href="{{route('orders.create')}}" class="btn btn-primary">Criar nova</a>
                        <button onclick="formSubmit('filter_form')" id="submitButton" style="float: right" type="submit" class="btn btn-secondary">
                            <i class="fa fa-filter" aria-hidden="true"></i> Filtrar
                        </button>
                    </div>
                @endif

                <form action="{{route('orders.filter', ['order' => 1])}}" id="filter_form" method="get">

                    @csrf
                    <div class="row g-2 mb-2">
                        <div class="col-md-3 col-12">
                            <div class="form-floating">
                                <select class="form-select" id="client" name="client" aria-label="Floating label select example">
                                    <option value="0">Todos</option>

                                    @foreach ($clients as $client)
                                        @if ($client->id == $old_client)
                                            <option selected value="{{$client->id}}">{{$client->name}}</option>
                                        @else
                                            <option value="{{$client->id}}">{{$client->name}}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <label for="client">Cliente</label>
                            </div>
                        </div>

                        <div class="col-md-2 col-6">
                            <div class="form-floating">
                                <select class="form-select" id="finished" name="finished" aria-label="Floating label select example">
                                    <option {{$fin_select[2] ?? ''}} value="2">Todas</option>
                                    <option {{$fin_select[0] ?? ''}} value="0">Não Finalizadas</option>
                                    <option {{$fin_select[1] ?? ''}} value="1">Finalizadas</option>
                                </select>
                                <label for="finished">Ordens</label>
                            </div>
                        </div>

                        <div class="col-md-3 col-6">
                            <div class="form-floating">
                                <select class="form-select" id="date_type" name="date_type" aria-label="Floating label select example">
                                    <option {{$order_open_select ?? ''}} value="order_open_date">Abertura</option>
                                    <option {{$last_note_select ?? ''}} value="last_note_date">Última anotação</option>
                                </select>
                                <label for="date_type">Considerar data da:</label>
                            </div>
                        </div>
    
                        <div class="col-md-2 col-6">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="Start" name="date_start" placeholder="Início" value="{{$date_s}}">
                                <label for="Start">De:</label>
                            </div>
                        </div>
                       
                        <div class="col-md-2 col-6">    
                            <div class="form-floating">
                                <input type="date" class="form-control" id="End" name="date_end" placeholder="Término" value="{{$date_e}}">
                                <label for="End">Até:</label>
                            </div>
                        </div>
                    </div>
                </form>

                @if ($orders->count() === 0)
                    <p>
                        Nenhum registro encontrado !
                    </p>
                @else
                    <form action="{{route('ord_tec_update')}}" id="form" method="post">

                        @csrf
                        <input type="hidden" name="_method" id="idNum" value="PUT">
                        <table class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nº</th>
                                    <th>Cliente</th>
                                    <th>Técnico</th>
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
                                        <td>
                                            @if ($order->finished || (!$main && !$sup))
                                                <input class="form-control" disabled id="ord_{{$order->id}}" value="{{$order->tec->id ?? 0}} - {{$order->tec->user->name ?? 'Indefinido'}}">
                                            @else
                                                <select onchange="formSubmit('form')" class="form-select" id="ord_{{$order->id}}" name="ord_{{$order->id}}" aria-label="Floating label select example">
                                                    @if (!isset($order->tec->id))
                                                        <option selected value="0">0 - Indefinido</option>
                                                    @else
                                                        <option  value="0">0 - Indefinido</option>
                                                    @endif
                                                    
                                                    @foreach ($tecs as $tec)
                                                        @if (isset($order->tec->id))
                                                            @if ($order->tec->id === $tec->id)
                                                                <option selected value="{{$tec->id}}">{{$tec->id}} - {{$tec->user->name}}</option>
                                                            @endif
                                                        @endif

                                                        @if (isset($order->tec->id))
                                                            @if (($order->tec->id !== $tec->id))
                                                                <option value="{{$tec->id}}">{{$tec->id}} - {{$tec->user->name}}</option>
                                                            @endif
                                                        @else
                                                            <option value="{{$tec->id}}">{{$tec->id}} - {{$tec->user->name}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
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
                    </form>
                @endif
            </div>
        </div>
     </div>
@endsection