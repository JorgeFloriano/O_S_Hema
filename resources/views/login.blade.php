@extends('layouts/login_layout')

@section('content')
    <div class="container-fluid">
        <div class="row mx-2 my-5">
            <div class="col-lg-4 offset-lg-4 col-md-8 offset-md-2 col-sm-10 offset-sm-1">
                <div class="card">
                    <div class="card-img-top">

                        <img src="{{ asset('assets/img/logo_hema.png')}}" width="100%" alt="logo hema">
                    </div>
                    
                    {{-- form --}}
                    <div class="card-body p-3">
                        <form action="{{route('login.store')}}" method="post">
                        
                            @csrf
                            <h2>Sistema de Gerenciamento</h2>
                            <div class="form-floating my-3">
                                <input type="text" name="username" id="username" class="form-control" placeholder="Usúario" required value="{{old('username')}}">
                                <label for="username">Usúario</label>
                            </div>
                            <div class="form-floating my-3">
                                <input type="password" name="password" id="password" class="form-control" placeholder="Senha" required value="{{old('password')}}">
                                <label for="password">Senha</label>
                            </div>
                            <div class="form-group my-3">
                                <input type="submit" value="ENTRAR" class="btn btn-primary">
                            </div>
                        {{-- /form --}}
                        </form>
                        
                        {{-- validation errors --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $msg)
                                        <li>{{$msg}}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                                
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection