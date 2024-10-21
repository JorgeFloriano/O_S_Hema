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
                    <h2>Códigos de Tipo de Serviço Cadastrados</h2>
                </div>
            
                <hr>
                <div>
                    <a href="{{route('note_types.create')}}" class="btn btn-primary">Cadastrar Novo</a>
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
                        @foreach ($note_types as $note_type)
                            <tr>
                                <td>{{$note_type->id}}</td>

                                <td>{{$note_type->description}}</td>

                                <td>
                                    <a href="{{route('note_types.edit', ['note_type' => $note_type->id])}}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>

                                <td>
                                    <a href="{{route('note_types.show', ['note_type' => $note_type->id])}}" class="btn btn-danger btn-sm">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody> 
                </table>
                <div>
                    {{$note_types->links()}}
                </div>
            </div>
        </div>
     </div>
@endsection
