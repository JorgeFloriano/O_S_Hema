@extends('layouts.o_s_form_layout')

@section('content')
    <div class="container box">
        <div class="row mt-1">
            <div class="col-lg-8 offset-lg-2">

                <div id="header" class="my-2">
                    <h2>Deletar Código de Solução Nº {{$solution->id}}</h2>
                </div>
                <hr>
                <main>
                
                    <form action="{{route('solutions.destroy', ['solution' => $solution->id])}}" id="form" method="post">
                        @csrf
                        
                        <input type="hidden" name="_method" id="idNum" value="DELETE">

                        <div class="form-floating my-2">
                            <input type="number" class="form-control" id="id" name="id" placeholder="Número" value="{{$solution->id}}" disabled>
                            <label for="id">Número</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="description" name="description" maxlength="25" placeholder="Descrição" value="{{$solution->description}}" disabled>
                            <label for="description">Descrição</label>
                        </div>

                        <div class="my-2">
                            <button type="submit" class="btn btn-danger me-2">Deletar</button>
                            <a href="{{url()->previous()}}" class="btn btn-secondary ms-2" >Voltar</a>
                        </div>
                    </form>
                </main>
            </div>
        </div>
    </div>
@endsection