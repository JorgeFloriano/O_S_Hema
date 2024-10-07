@extends('layouts.o_s_form_layout')

@section('content')
    
     <div class="container">
        <div class="row">

            @if (session()->has('message'))
                <div class="alert alert-info" role="alert">
                    {{session()->get('message')}}
                </div>
            @endif

            <div class="col">
                <div id="header" class="my-2">
                    <h2>Programação</h2>
                </div>
                <hr>

                @if (auth()->user()->tec->on_call)
                    <a href="{{route('orders.create')}}" class="btn btn-primary">Gerar O.S.</a>
                    <hr>
                @endif
                
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>O.S.</th>
                            <th>Cliente</th>
                            <th>Data</th>
                            <th>Exec.</th>
                            <th>Encerr.</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>{{$order->id}}</td>
                                <td>{{$order->client->name}}</td>
                                <td>{{date('d/m/y',strtotime($order->req_date))}}</td>

                                @if ($order->finished)
                                    <td>
                                        <a href="{{route('orders.show_pdf', ['order' => $order->id])}}" class="btn btn-outline-danger btn-sm">
                                            <i class="fa fa-file-pdf-o"></i>
                                        </a>
                                    </td>
                                    
                                    <td>
                                        <a class="btn btn-secondary btn-sm disabled">
                                            <i class="fa fa-check-square-o"></i>
                                        </a>
                                    </td>
                                @else
                                    <td>
                                        <a href="{{route('notes.create', ['order' => $order->id])}}" class="btn btn-info btn-sm">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td>

                                    @if ($order->notes->count() > 0)
                                        <td>
                                            <a href="{{route('orders.finish', ['order' => $order->id])}}" class="btn btn-primary btn-sm">
                                                <i class="fa fa-check-square-o"></i>
                                            </a>
                                        </td>
                                    @else
                                        <td>
                                            <a class="btn btn-secondary btn-sm disabled">
                                                <i class="fa fa-check-square-o"></i>
                                            </a>
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
            </div>
        </div>
     </div>
@endsection
