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
                    <h2>Códigos de Tipos de Serviços {{$msg}}</h2>
                </div>
            
                <hr>
                <div>
                    <a href="{{route('note_types.create')}}" class="btn btn-primary me-2">Cadastrar Novo</a>
                    <a href="{{route('note_types.list', ['opt' => $opt])}}" class="btn btn-secondary">{{$title}}</a>
                </div>

                <hr>

                @if ($note_types->count() === 0)
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
                            @foreach ($note_types as $note_type)
                                <tr>
                                    <td>{{$note_type->id}}</td>

                                    <td>{{$note_type->description}}</td>

                                    @if ($opt === 0)
                                        <td>
                                            <a href="{{route('note_types.edit', ['note_type' => $note_type->id])}}" class="btn btn-primary btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                    @endif

                                    <td>
                                        <a href="{{route($route, ['note_type' => $note_type->id])}}" class="btn btn-sm {{$btn_color}}">
                                            <i class="fa fa-exchange"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody> 
                    </table>
                @endif
                <div>
                    {{$note_types->links()}}
                </div>
            </div>
        </div>
     </div>
@endsection
