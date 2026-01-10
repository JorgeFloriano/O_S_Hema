@extends('layouts.o_s_form_layout')

@section('content')

@php
    use Illuminate\Support\Facades\Crypt;
@endphp
     <div class="container box">
        <div class="row">
            <div class="col">
                <x-code-index-header
                    text="Códigos de Defeitos"
                    :msg="$msg"
                    object="defects"
                    :title="$title"
                    :opt="$opt">
                </x-code-index-header>

                @if ($defects->count() === 0)
                    <p>
                        Nenhum registro encontrado !
                    </p>
                @else
                    <table class="table table-striped">
                        <thead class="table-primary">
                            <tr>
                                <th>Nº</th>
                                <th>Descrição</th>

                                @if ($opt === 0)
                                    <th>Editar</th>
                                @endif

                                <th>{{$cond}}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($defects as $defect)
                                <tr>
                                    <td>{{$defect->id}}</td>

                                    <td>{{$defect->description}}</td>
                                    @if ($opt === 0)
                                        <td>
                                            <a href="{{route('defects.edit', ['defect' => Crypt::encryptString($defect->id)])}}" class="btn btn-outline-primary btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                    @endif

                                    <td>
                                        <a href="{{route($route, ['defect' => Crypt::encryptString($defect->id)])}}" class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-exchange"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody> 
                    </table>
                @endif
                <div>
                    {{$defects->links()}}
                </div>
            </div>
        </div>
     </div>
@endsection
