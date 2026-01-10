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
                    <h2>Usuários Cadastrados</h2>
                </div>
                <hr>

                <div>
                    <a href="{{route('users.create')}}" class="btn btn-primary"><i class="fa fa-plus"></i> Cadastrar Novo</a>
                </div>
                <hr>

                @if ($users->count() === 0)
                    <p>
                        Nenhum registro encontrado !
                    </p>
                @else
                    <table class="table table-striped">
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
                                        <a href="{{route('users.edit', ['user' => Crypt::encryptString($user->id)])}}" class="btn btn-outline-primary btn-sm">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{route('users.show', ['user' => Crypt::encryptString($user->id)])}}" class="btn btn-outline-primary btn-sm">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody> 
                    </table>
                @endif
                <div>
                    {{$users->links()}}
                </div>
            </div>
        </div>
     </div>
@endsection
