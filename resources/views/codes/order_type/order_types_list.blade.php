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
                    <h2>Códigos de Segmento de Serviços Cadastrados</h2>
                </div>
            
                <hr>
                <div>
                    <a href="{{route('order_types.create')}}" class="btn btn-primary">Cadastrar Novo</a>
                </div>

                <hr>
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Nº</th>
                            <th>Descrição</th>
                            <th>Edit.</th>
                            <th>Del.</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($order_types as $order_type)
                            <tr>
                                <td>{{$order_type->id}}</td>

                                <td>{{$order_type->description}}</td>

                                <td>
                                    <a href="{{route('order_types.edit', ['order_type' => $order_type->id])}}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>

                                <td>
                                    <a href="{{route('order_types.show', ['order_type' => $order_type->id])}}" class="btn btn-danger btn-sm">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody> 
                </table>
                <div>
                    {{$order_types->links()}}
                </div>
            </div>
        </div>
     </div>
@endsection
