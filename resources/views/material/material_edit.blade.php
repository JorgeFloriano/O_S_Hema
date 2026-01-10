@extends('layouts.o_s_form_layout')

@section('content')
    <div class="container box">
        <div class="row mt-1">
            <div class="col-lg-8 offset-lg-2">
                
                <x-live-toast-message></x-live-toast-message>

                @if ($errors->any())
                    <div class="alert alert-warning">
                        <ul>
                            @foreach ($errors->all() as $msg)
                                <li>{{$msg}}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div id="header" class="my-2">
                    <h2>Editar Cadastro de Material nº{{$material->id}}</h2>
                </div>
                <hr>
                <main>
                
                    <form action="{{route('materials.update', ['material' => $material->id])}}" id="form" method="post" autocomplete="on">
                        @csrf
                        
                        <input type="hidden" name="_method" id="idNum" value="PUT">

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="description" name="description" maxlength="25" placeholder="Descrição" value="{{$material->description}}" required>
                            <label for="description">Descrição</label>
                        </div>

                        <div class="form-floating my-2">
                            <select class="form-select" id="unit" name="unit" aria-label="Floating label select example" required >
                                @foreach ($units as $unit)
                                    @if ($unit == $material->unit)
                                        <option selected value="{{$unit}}">{{$unit}}</option>
                                    @else
                                        <option value="{{$unit}}">{{$unit}}</option>
                                    @endif
                                @endforeach
                            </select>
                            <label for="unit">Unidade de Medida</label>
                        </div>

                        <div class="my-2">
                            <button id="submitButton" type="submit" class="btn btn-primary me-2">
                                <i class="fa fa-check"></i> Confirma
                            </button>
                            <a href="{{route('materials.index')}}" class="btn btn-outline-primary">
                                <i class="fa fa-arrow-left"></i> Voltar
                            </a>
                        </div>
                    </form>
                </main>
            </div>
        </div>
    </div>
@endsection