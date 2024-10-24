@extends('layouts.o_s_form_layout')

@section('content')
    
     <div class="container">
        <div class="row">
            <div class="col">
                @if (session()->has('message'))
                    <div class="alert alert-info" role="alert">
                        {{session()->get('message')}}
                    </div>
                @endif

                <div id="header" class="my-2">
                    <h2>Ordens de Serviço</h2>
                </div>
                <hr>

                @if ($adm)
                    <div>
                        <a href="{{route('orders.create')}}" class="btn btn-primary">Criar nova</a>
                    </div>
                    <hr>
                @endif

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
                                        <td>{{$order->id}}</td>
                                        <td>{{$order->client->name ?? ''}}</td>
                                        <td>
                                            @if ($order->finished || (!$main && !$sup))
                                                <input class="form-control" disabled id="ord_{{$order->id}}" value="{{$order->tec->id ?? 0}} - {{$order->tec->user->name ?? 'Indefinido'}}">
                                            @else
                                                <select onchange="formUpdate()" class="form-select" id="ord_{{$order->id}}" name="ord_{{$order->id}}" aria-label="Floating label select example">
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
                                                <a href="{{route('orders.show_pdf', ['order' => $order->id])}}" class="btn btn-outline-danger btn-sm">
                                                    <i class="fa fa-file-pdf-o"></i>
                                                </a>
                                            </td>

                                            @if ($main)
                                                <td>
                                                    <a href="{{route('orders.show', ['order' => $order->id])}}" class="btn btn-danger btn-sm">
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
                                                    <a href="{{route('orders.edit', ['order' => $order->id])}}" class="btn btn-primary btn-sm">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                </td>
                                            @else
                                                <td>
                                                    <a href="{{route('orders.edit', ['order' => $order->id])}}" class="btn btn-primary btn-sm">
                                                        <i class="fa fa-file-text"></i>
                                                    </a>
                                                </td>
                                            @endif
                                            @if ($order->notes->count() > 0)
                                                @if ($main)
                                                    <td>
                                                        <a href="{{route('orders.show', ['order' => $order->id])}}" class="btn btn-danger btn-sm">
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
                                                        <a href="{{route('orders.show', ['order' => $order->id])}}" class="btn btn-danger btn-sm">
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
                        <div>
                            {{ $orders->links() }}
                        </div>
                    </form>
                @endif
            </div>
        </div>
     </div>
@endsection