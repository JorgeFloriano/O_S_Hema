@extends('layouts.o_s_form_layout')

@section('content')
    
     <div class="container">
        <div class="row">
            <div class="col">
                @if (session()->has('message'))
                    {{session()->get('message')}}
                @endif
                <div id="header" class="my-2">
                    <h2>Programação</h2>
                </div>
                <hr>
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>O.S.</th>
                            <th>Cliente</th>
                            <th>Data</th>
                            <th>Exec.</th>
                            <th>Apag.</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>{{$order->id}}</td>
                                <td>{{$order->client->name}}</td>
                                <td>{{date('d/m/y',strtotime($order->req_date))}}</td>
                                <td>
                                    <a href="{{route('notes.edit', ['note' => $order->id])}}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                                <td>
                                    <a href="{{route('notes.destroy', ['note' => $order->id])}}" class="btn btn-danger btn-sm">
                                        <i class="fa fa-eraser" aria-hidden="true"></i>
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
