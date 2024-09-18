@extends('layouts.o_s_form_layout')

@section('content')
    
     <div class="container">
        <div class="row">
            <div class="col">
                @if (session()->has('message'))
                <div class="alert alert-info" role="alert">
                    {{session()->get('message')}}
                </div>
                @endif
                <div id="header" class="my-2">
                    <h2>Gerenciar Técnicos de plantão</h2>
                </div>
                <hr>

                <form action="{{route('tec_on_update')}}" id="form" method="post">

                @csrf
                <input type="hidden" name="_method" id="idNum" value="PUT">
                
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Nº</th>
                                <th>Nome</th>
                                <th>Função</th>
                                <th>Plantão</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($tecs as $tec)
                                <tr>
                                    <td>{{$tec->id}}</td>
                                    <td>{{$tec->user->name}}</td>
                                    <td>{{$tec->user->function}}</td>
                                    <td>
                                        @if ($tec->on_call)
                                            <input class="form-check-input" name="tec{{$tec->id}}" checked type="checkbox" value="1" id="tec{{$tec->id}}">
                                        @else
                                            <input class="form-check-input" name="tec{{$tec->id}}" type="checkbox" value="1" id="tec{{$tec->id}}">
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody> 
                    </table>
                    <button id="submitButton" type="submit" class="btn btn-primary me-2">
                        Salvar
                    </button>
                </form>
            </div>
        </div>
     </div>
@endsection
