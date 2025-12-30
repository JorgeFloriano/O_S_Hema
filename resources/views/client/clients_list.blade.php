@extends('layouts.o_s_form_layout')

@section('content')

@php
    use Illuminate\Support\Facades\Crypt;
@endphp
     <div class="container box">
        <div class="row">
            <div class="col">
                @if (session()->has('message'))
                <div class="alert alert-info" role="alert">
                    {{session()->get('message')}}
                </div>
                @endif
                <div id="header" class="my-2">
                    <h2>Clientes {{$msg}}</h2>
                </div>

                <hr>

                <div>
                    <a href="{{route('clients.create')}}" class="btn btn-primary me-2">Cadastrar Novo</a>
                    <a href="{{route('clients.list', ['opt' => $opt])}}" class="btn btn-outline-primary">{{$title}}</a>
                </div>

                <hr>

                @if ($clients->count() === 0)
                    <p>
                        Nenhum registro encontrado !
                    </p>
                @else
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Nº</th>
                                <th>Nome</th>
                                <th>Unidade</th>
                                @if ($opt === 0)
                                    <th>Editar</th>
                                @endif
                                <th>{{$cond}}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($clients as $client)
                                <tr>
                                    <td>{{$client->id}}</td>
                                    <td>{{$client->name}}</td>
                                    <td>{{$client->unit}}</td>
                                    @if ($opt === 0)
                                        <td>
                                            <a href="{{route('clients.edit', ['client' => Crypt::encryptString($client->id)])}}" class="btn btn-primary btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                    @endif
                                    
                                    <td>
                                        <a href="{{route($route, ['client' => Crypt::encryptString($client->id)])}}" class="btn btn-{{$btn_color}} btn-sm">
                                            <i class="fa fa-{{$icon}}" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody> 
                    </table>
                @endif
                <div class="d-flex justify-content-center">
                    {{$clients->links()}}
                </div>
            </div>
        </div>
     </div>
@endsection
