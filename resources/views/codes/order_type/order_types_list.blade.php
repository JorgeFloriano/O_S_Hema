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
                    text="Códigos de Segmentos de Serviços"
                    :msg="$msg"
                    object="order_types"
                    :title="$title"
                    :opt="$opt">
                </x-code-index-header>

                @if ($order_types->count() === 0)
                    <p>
                        Nenhum registro encontrado !
                    </p>
                @else
                    <table class="table table-striped table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>Nº</th>
                                <th>Descrição</th>

                                @if ($opt === 0 && $auth->isMainAdm())
                                    <th>Editar</th>
                                @endif

                                @can('check-permission', ['codes', 2])
                                    <th>{{$cond}}</th>
                                @endcan
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($order_types as $order_type)
                                <tr>
                                    <td>{{$order_type->id}}</td>

                                    <td>{{$order_type->description}}</td>

                                    @if ($opt === 0 && $auth->isMainAdm())
                                        <td>
                                            <a href="{{route('order_types.edit', ['order_type' => Crypt::encryptString($order_type->id)])}}" class="btn btn-outline-primary btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                    @endif

                                    @can('check-permission', ['codes', 2])
                                        <td>
                                            <a href="{{route($route, ['order_type' => Crypt::encryptString($order_type->id)])}}" class="btn btn-sm btn-outline-primary">
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
                    {{$order_types->links()}}
                </div>
            </div>
        </div>
     </div>
@endsection
