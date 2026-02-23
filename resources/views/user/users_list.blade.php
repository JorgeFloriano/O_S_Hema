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
                        <h2>Usuários Cadastrados</h2>
                    </div>

                    {{-- Botões à direita (quando couber) --}}
                    @can('manage-users')
                        <div class="mb-2">
                            <a href="{{route('users.create')}}" class="btn btn-primary" data-bs-toggle="tooltip" title="Criar novo Usuário">
                                <i class="fa fa-plus"></i> Cadastrar
                            </a>
                        </div>
                    @endcan
                </div>

                <hr>

                @if ($users->count() === 0)
                    <p>
                        Nenhum registro encontrado !
                    </p>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-primary">
                                <tr>
                                    <th>Nº</th>
                                    <th>Nome</th>
                                    <th>Função</th>
                                    <th>Empresa</i></th>
                                    @if(auth()->user()->hasPermission('users', 2))
                                        <th>Edit</th>
                                        <th>Del.</th>
                                    @else
                                        <th>Ver</th>
                                    @endif
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{$user->id}}</td>
                                        <td>{{$user->name}}</td>
                                        <td>{{$user->function}}</td>
                                        <td>
                                            @if ($user->isCli())
                                                <div class="text-primary" 
                                                    style="font-size: 0.8rem; font-weight: bold;">
                                                    {{$user->userClientCompanyName()}}
                                                </div>
                                            @else
                                                <div class="text-danger" 
                                                    style="font-size: 0.8rem; font-weight: bold;">
                                                    HEMA
                                                </div>
                                            @endif
                                        </td>
                                            @can('manage-users')
                                                <td>
                                                    <a href="{{route('users.edit', ['user' => Crypt::encryptString($user->id)])}}" class="btn btn-outline-primary btn-sm">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                </td>
                                            @endcan
                                        <td>
                                            <a href="{{route('users.show', ['user' => Crypt::encryptString($user->id)])}}" class="btn btn-outline-primary btn-sm">
                                                @if(auth()->user()->hasPermission('users', 2))
                                                    <i class="fa fa-trash"></i>
                                                @else
                                                    <i class="fa fa-eye"></i>
                                                @endif
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody> 
                        </table>
                    </div>
                @endif
                <div>
                    {{$users->links()}}
                </div>
            </div>
        </div>
     </div>
@endsection
