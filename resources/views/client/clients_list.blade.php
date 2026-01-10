@extends('layouts.o_s_form_layout')

@section('content')

@php
    use Illuminate\Support\Facades\Crypt;
@endphp
     <div class="container box">
        <div class="row">
            <div class="col">
               <x-live-toast-message></x-live-toast-message>

                <div id="header" class="my-3 d-flex flex-wrap justify-content-between align-items-center">
                    {{-- Título à esquerda --}}
                    <div class="mb-2">
                        <h2 class="mb-0">Clientes {{$msg}}</h2>
                    </div>

                    {{-- Botões à direita (quando couber) --}}
                    <div class="mb-2">
                        <a href="{{route('clients.create')}}" class="btn btn-primary me-2">
                            <i class="fa fa-plus"></i> Cadastrar Novo
                        </a>
                        <a href="{{route('clients.list', ['opt' => $opt])}}" class="btn btn-outline-primary"> 
                            <i class="fa fa-{{$icon}}" aria-hidden="true"></i> {{$title}}
                        </a>
                    </div>
                </div>

                <hr>

                @if ($clients->count() === 0)
                    <p>
                        Nenhum registro encontrado !
                    </p>
                @else
                    <table class="table table-striped">
                        <thead class="table-primary">
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
                                            <a href="{{route('clients.edit', ['client' => Crypt::encryptString($client->id)])}}" class="btn btn-outline-primary btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                    @endif
                                    
                                    <td>
                                        <a href="{{route($route, ['client' => Crypt::encryptString($client->id)])}}" class="btn btn-outline-primary btn-sm">
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
