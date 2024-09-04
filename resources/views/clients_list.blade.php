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
                    <h2>Clientes Cadastrados</h2>
                </div>
                <hr>
                <div>
                    <a href="{{route('clients.create')}}" class="btn btn-primary">Cadastrar Novo</a>
                </div>
                <hr>
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Nº</th>
                            <th>Nome</th>
                            <th>Unidade</th>
                            <th>Edit</th>
                            <th>Del.</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($clients as $client)
                            <tr>
                                <td>{{$client->id}}</td>
                                <td>{{$client->name}}</td>
                                <td>{{$client->unit}}</td>
                                <td>
                                    <a href="{{route('clients.edit', ['client' => $client->id])}}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                                <td>
                                    <a href="{{route('clients.show', ['client' => $client->id])}}" class="btn btn-danger btn-sm">
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
