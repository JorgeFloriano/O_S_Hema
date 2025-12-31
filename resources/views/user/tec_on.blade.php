@extends('layouts.o_s_form_layout')

@section('content')
     <div class="container box">
        <div class="row">
            <div class="col">
                @if (session()->has('message'))
                <div class="alert alert-info" role="alert">
                    {{session()->get('message')}}
                </div>
                @endif
                <div id="header" class="my-2">
                    <h2>Gerenciar Colaboradores de Sobreaviso</h2>
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
                                <th>Ativo</th>
                                <th>Clientes</th>
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
                                            <input onchange="form.submit()" class="form-check-input" name="tec{{$tec->id}}" checked type="checkbox" value="1" id="tec{{$tec->id}}">
                                        @else
                                            <input onchange="form.submit()" class="form-check-input" name="tec{{$tec->id}}" type="checkbox" value="1" id="tec{{$tec->id}}">
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalClients{{$tec->id}}">
                                            Vincular Clientes ({{$tec->emergencyClients->count()}})
                                        </button>

                                        <div class="modal fade" id="modalClients{{$tec->id}}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content text-dark">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Clientes de {{$tec->user->name}}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            @foreach(App\Models\Client::orderBy('name')->get() as $client)
                                                                <div class="col-md-6 mb-2 text-start">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" 
                                                                            name="clients[{{$tec->id}}][]" 
                                                                            value="{{$client->id}}" 
                                                                            id="client{{$tec->id}}_{{$client->id}}"
                                                                            {{ $tec->emergencyClients->contains($client->id) ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="client{{$tec->id}}_{{$client->id}}">
                                                                            {{$client->name}}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Fechar</button>
                                                        <button type="submit" class="btn btn-primary">Salvar Todos</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody> 
                    </table>
                    <div>
                        {{$tecs->links()}}
                    </div>
                </form>
            </div>
        </div>
     </div>
@endsection
