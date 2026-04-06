@extends('layouts.o_s_form_layout')

@section('content')

@php
    use Illuminate\Support\Facades\Crypt;
    $auth = auth()->user();
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
                        @can('check-permission', ['clients', 2])
                            <a href="{{route('clients.create')}}" class="btn btn-primary me-2">
                                <i class="fa fa-plus"></i> Cadastrar Novo
                            </a>
                        @endcan
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
                    <table class="table table-striped table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>Nº</th>
                                <th>Nome</th>
                                <th>Unidade</th>

                                @if ($opt === 0 && $auth->isMainAdm())
                                    <th>Editar</th>
                                @else
                                    @can('check-permission', ['clients', 1])
                                        <th>Visualizar</th>
                                    @endcan
                                @endif

                                @can('check-permission', ['clients', 2])
                                    <th>{{$cond}}</th>
                                @endcan
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($clients as $client)
                                <tr>
                                    <td>{{$client->id}}</td>
                                    <td>{{$client->name}}</td>
                                    <td>{{$client->unit}}</td>

                                    @if ($opt === 0 && $auth->isMainAdm())
                                        <td>
                                            <a href="{{route('clients.edit', ['client' => Crypt::encryptString($client->id)])}}" class="btn btn-outline-primary btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                    @else
                                        @can('check-permission', ['clients', 1])
                                            <td>
                                                <a href="{{route('clients.show', ['client' => Crypt::encryptString($client->id)])}}" class="btn btn-outline-primary btn-sm">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </td>
                                        @endcan
                                    @endif
                                    
                                    @can('check-permission', ['clients', 2])
                                        <td>
                                            <a href="{{route($route, ['client' => Crypt::encryptString($client->id)])}}" class="btn btn-outline-primary btn-sm">
                                                <i class="fa fa-{{$icon}}" aria-hidden="true"></i>
                                            </a>
                                        </td>
                                    @endcan
                                </tr>
                            @endforeach
                        </tbody> 
                    </table>
                @endif
                <div>
                    {{$clients->links()}}
                </div>
            </div>
        </div>
     </div>
@endsection
