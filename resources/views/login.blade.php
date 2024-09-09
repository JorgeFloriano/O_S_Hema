@extends('layouts/login_layout')

@section('content')
    <div class="container-fluid">
        <div class="row mx-4 my-5">
            <div class="col-lg-4 offset-lg-4 col-md-6 offset-md-3 col-sm-8 offset-sm-2">
                <div class="card">
                    <div class="text-center">
                        <img class="my-3" src="{{ asset('assets/img/talha_color.png')}}" width="60%" alt="logo cmk">
                        <h2>EQUIPAMENTOS</h2>
                        <hr>
                    </div>
                    {{-- form --}}
                    <form action="{{route('login.store')}}" method="post">
                        @csrf
                        <div class="form-group my-3">
                            <label>User:</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="form-group my-3">
                            <label>Password:</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="form-group my-3">
                            <input type="submit" value="ENTER" class="btn btn-primary">
                        </div>
                    {{-- /form --}}
                    </form>
                    {{-- validation errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $msg)
                                <div>{{$msg}}</div>
                            @endforeach
                        </div>
                    @endif
                                {{-- login errors --}}
                                {{-- @error('error')
                    <div class="alert alert-danger text-center">{{$message}}</div>
                                @enderror --}}
                </div>
            </div>
        </div>
    </div>
@endsection