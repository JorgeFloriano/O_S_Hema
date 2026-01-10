@extends('layouts.o_s_form_layout')

@section('content')

@php
    use Illuminate\Support\Facades\Crypt;
@endphp
     <div class="container box">
        <div class="row">
            <div class="col">
                <x-code-index-header
                    text="Códigos de Tipos de Serviços"
                    :msg="$msg"
                    object="note_types"
                    :title="$title"
                    :opt="$opt">
                </x-code-index-header>

                @if ($note_types->count() === 0)
                    <p>Nenhum registro encontrado !</p>
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
                            @foreach ($note_types as $note_type)
                                <tr>
                                    <td>{{$note_type->id}}</td>

                                    <td>{{$note_type->description}}</td>

                                    @if ($opt === 0)
                                        <td>
                                            <a href="{{route('note_types.edit', ['note_type' => Crypt::encryptString($note_type->id)])}}" class="btn btn-outline-primary btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                    @endif

                                    <td>
                                        <a href="{{route($route, ['note_type' => Crypt::encryptString($note_type->id)])}}" class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-exchange"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody> 
                    </table>
                @endif
                <div>
                    {{$note_types->links()}}
                </div>
            </div>
        </div>
     </div>
@endsection
