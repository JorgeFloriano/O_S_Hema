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
                <div>
                    <a href="{{route('orders.create')}}" class="btn btn-primary">Criar nova</a>
                </div>
                <hr>
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Nº</th>
                            <th>Cliente</th>
                            <th>Técnico</th>
                            <th>Data</th>
                            <th>Edit</th>
                            <th>Del.</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>{{$order->id}}</td>
                                <td>{{$order->client->name}}</td>
                                <td>{{$order->user->name ?? ''}}</td>
                                <td>{{ date('d/m/Y',strtotime($order->req_date))}}</td>
                                <td>
                                    <a href="{{route('orders.edit', ['order' => $order->id])}}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                                <td>
                                    <a href="{{route('orders.show', ['order' => $order->id])}}" class="btn btn-danger btn-sm">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody> 
                </table>
            </div>
        </div>
     </div>
@endsection
