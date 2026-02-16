@extends('layouts.o_s_form_layout')

@section('content')

@php
    use Illuminate\Support\Facades\Crypt;
    $auth = auth()->user();
@endphp
     <div class="container box">
        <div class="row">
            <div class="col">
                <x-code-index-header
                    text="Cadastro de Materiais"
                    :msg="$msg"
                    object="materials"
                    :title="$title"
                    :opt="$opt"
                    permission="materials">
                </x-code-index-header>

                @if ($materials->count() === 0)
                    <p>Nenhum registro encontrado !</p>
                @else
                    <table class="table table-striped table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>Nº</th>
                                <th>Descrição</th>
                                <th>Unidade</th>

                                @if ($opt === 0 && $auth->isMainAdm())
                                    <th>Editar</th>
                                @endif

                                @can('check-permission', ['materials', 2])
                                    <th>{{$cond}}</th>
                                @endcan
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($materials as $material)
                                <tr>
                                    <td>{{$material->id}}</td>

                                    <td>{{$material->description}}</td>

                                    <td>{{$material->unit}}</td>

                                    @if ($opt === 0 && $auth->isMainAdm())
                                        <td>
                                            <a href="{{route('materials.edit', ['material' => Crypt::encryptString($material->id)])}}" class="btn btn-outline-primary btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                    @endif

                                    @can('check-permission', ['materials', 2])
                                        <td>
                                            <a href="{{route($route, ['material' => Crypt::encryptString($material->id)])}}" class="btn btn-sm btn-outline-primary">
                                                <i class="fa fa-exchange"></i>
                                            </a>
                                        </td>
                                    @endcan
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
