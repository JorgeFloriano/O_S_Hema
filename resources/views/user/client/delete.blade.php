@extends('layouts.o_s_form_layout')

@section('content')
    <div class="container box">
        <div class="row mt-1">
            <div class="col-lg-8 offset-lg-2">

                <div id="header" class="my-2">
                    <h2>Deletar Cadastro do Usuário {{$user->id}}</h2>
                </div>
                <hr>
                <main>
                
                    <form action="{{route('client.users.destroy', ['user' => $user->id])}}" id="form" method="post">
                        @csrf
                        
                        <input type="hidden" name="_method" id="idNum" value="DELETE">

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="name" name="name" value="{{$user->name.' '.$user->surname}}" disabled>
                            <label for="name">Nome</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="function" name="function" value="{{$user->function}}" disabled>
                            <label for="name">Função</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="username" name="username" value="{{$user->username}}" disabled>
                            <label for="username">Nome de Usúario</label>
                        </div>

                        <div class="alert alert-danger mb-2">
                            Ao deleter o cadastro, todos os dados deste usuário serão perdidos!
                        </div>

                        <div class="my-2">
                            <button type="submit" class="btn btn-outline-danger me-2"><i class="fa fa-trash"></i> Deletar</button>
                            <a href="{{route('client.users.index')}}" class="btn btn-outline-primary ms-2" ><i class="fa fa-arrow-left"></i> Voltar</a>
                        </div>
                    </form>
                </main>
            </div>
        </div>
    </div>
@endsection