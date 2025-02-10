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
                    <h2>Cadastro de Materiais {{$msg}}</h2>
                </div>
            
                <hr>
                <div>
                    <a href="{{route('materials.create')}}" class="btn btn-primary me-2">Cadastrar Novo</a>
                    <a href="{{route('materials.list', ['opt' => $opt])}}" class="btn btn-secondary">{{$title}}</a>
                </div>

                <hr>

                @if ($materials->count() === 0)
                    <p>
                        Nenhum registro encontrado !
                    </p>
                @else
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Nº</th>
                                <th>Descrição</th>
                                <th>Unidade de Medida</th>
                                @if ($opt === 0)
                                    <th>Editar</th>
                                @endif
                                <th>{{$cond}}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($materials as $material)
                                <tr>
                                    <td>{{$material->id}}</td>

                                    <td>{{$material->description}}</td>

                                    <td>{{$material->unit}}</td>

                                    @if ($opt === 0)
                                        <td>
                                            <a href="{{route('materials.edit', ['material' => Crypt::encryptString($material->id)])}}" class="btn btn-primary btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                    @endif

                                    <td>
                                        <a href="{{route($route, ['material' => Crypt::encryptString($material->id)])}}" class="btn btn-sm {{$btn_color}}">
                                            <i class="fa fa-exchange"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody> 
                    </table>
                @endif
                <div>
                    {{$materials->links()}}
                </div>
            </div>
        </div>
     </div>
@endsection
