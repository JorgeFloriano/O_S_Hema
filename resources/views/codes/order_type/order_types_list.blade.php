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
                    <h2>Códigos de Segmento de Serviços {{$msg}}</h2>
                </div>
            
                <hr>
                <div>
                    <a href="{{route('order_types.create')}}" class="btn btn-primary me-2">Cadastrar Novo</a>
                    <a href="{{route('order_types.list', ['opt' => $opt])}}" class="btn btn-secondary">{{$title}}</a>
                </div>

                <hr>

                @if ($order_types->count() === 0)
                    <p>
                        Nenhum registro encontrado !
                    </p>
                @else
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Nº</th>
                                <th>Descrição</th>
                                
                                @if ($opt === 0)
                                    <th>Editar</th>
                                @endif

                                <th>{{$cond}}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($order_types as $order_type)
                                <tr>
                                    <td>{{$order_type->id}}</td>

                                    <td>{{$order_type->description}}</td>

                                    @if ($opt === 0)
                                        <td>
                                            <a href="{{route('order_types.edit', ['order_type' => $order_type->id])}}" class="btn btn-primary btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                    @endif

                                    <td>
                                        <a href="{{route($route, ['order_type' => $order_type->id])}}" class="btn btn-sm {{$btn_color}}">
                                            <i class="fa fa-exchange"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody> 
                    </table>
                @endif
                <div>
                    {{$order_types->links()}}
                </div>
            </div>
        </div>
     </div>
@endsection
