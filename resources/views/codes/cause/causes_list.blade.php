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
                    <h2>Códigos de Causas {{$msg}}</h2>
                </div>
            
                <hr>
                <div>
                    <a href="{{route('causes.create')}}" class="btn btn-primary me-2">Cadastrar Novo</a>
                    <a href="{{route('causes.list', ['opt' => $opt])}}" class="btn btn-secondary">{{$title}}</a>
                </div>

                <hr>

                @if ($causes->count() === 0)
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
                            @foreach ($causes as $cause)
                                <tr>
                                    <td>{{$cause->id}}</td>

                                    <td>{{$cause->description}}</td>
                                    @if ($opt === 0)
                                        <td>
                                            <a href="{{route('causes.edit', ['cause' => $cause->id])}}" class="btn btn-primary btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                    @endif

                                    <td>
                                        <a href="{{route($route, ['cause' => $cause->id])}}" class="btn btn-sm {{$btn_color}}">
                                            <i class="fa fa-exchange"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody> 
                    </table>
                @endif
                <div>
                    {{$causes->links()}}
                </div>
            </div>
        </div>
     </div>
@endsection
