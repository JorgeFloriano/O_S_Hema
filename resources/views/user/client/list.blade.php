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
                    <div class="mb-2">
                        <a href="{{route('client.users.create')}}" class="btn btn-primary" data-bs-toggle="tooltip" title="Criar novo Usuário">
                            <i class="fa fa-plus"></i> Cadastrar
                        </a>
                    </div>
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
                                    <th>Edit</th>
                                    <th>Del.</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{$user->id}}</td>
                                        <td>{{$user->name}}</td>
                                        <td>{{$user->function}}</td>
                                        <td>
                                            <a href="{{route('client.users.edit', ['user' => Crypt::encryptString($user->id)])}}" class="btn btn-outline-primary btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{route('client.users.show', ['user' => Crypt::encryptString($user->id)])}}" class="btn btn-outline-primary btn-sm">
                                                <i class="fa fa-trash"></i>
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
