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
                    <h2>Códigos de Causas Cadastrados</h2>
                </div>
            
                <hr>
                <div>
                    <a href="{{route('causes.create')}}" class="btn btn-primary">Cadastrar Novo</a>
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
                        @foreach ($causes as $cause)
                            <tr>
                                <td>{{$cause->id}}</td>

                                <td>{{$cause->description}}</td>

                                <td>
                                    <a href="{{route('causes.edit', ['cause' => $cause->id])}}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>

                                <td>
                                    <a href="{{route('causes.show', ['cause' => $cause->id])}}" class="btn btn-danger btn-sm">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody> 
                </table>
                <div>
                    {{$causes->links()}}
                </div>
            </div>
        </div>
     </div>
@endsection
