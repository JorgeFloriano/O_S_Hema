@extends('layouts.o_s_form_layout')

@section('content')
    <div class="container box">
        <div class="row mt-1">
            <div class="col-lg-8 offset-lg-2">

                <div id="header" class="my-2">
                    <h2>Cadastro do Cliente {{$client->id}}</h2>
                </div>
                <hr>
                <main>
                
                    {{-- <form action="{{route('clients.destroy', ['client' => $client->id])}}" id="form" method="post">
                        @csrf 
                        
                        <input type="hidden" name="_method" id="idNum" value="DELETE">--}}

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="name" name="name" value="{{$client->name}}" disabled>
                            <label for="name">Nome da Empresa</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="cnpj_cpf" name="cnpj_cpf" value="{{$client->cnpj_cpf}}" disabled>
                            <label for="cnpj_cpf">CNPJ</label>
                        </div>

                        <div class="form-floating my-2" x-data>
                            <input type="text" class="form-control" id="cep" name="cep" maxlength="20" placeholder="CEP" value="{{$client->cep}}" x-mask="99.999-999" disabled>
                            <label for="cep">CEP</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="unit" name="unit" value="{{$client->unit}}" disabled>
                            <label for="unit">Unidade</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="address" name="address" value="{{$client->address}}" disabled>
                            <label for="address">Endereço</label>
                        </div>

                        <div class="form-floating my-2">
                            <input type="email" class="form-control" id="email" name="email" value="{{$client->email}}" disabled>
                            <label for="email">E-mail</label>
                        </div>

                        <div class="form-floating my-2" x-data>
                            <input type="text" class="form-control" id="phone" name="phone" maxlength="20" placeholder="Telefone" value="{{$client->phone}}" x-mask="(99) 99999-9999" disabled>
                            <label for="phone">Telefone</label>
                        </div>
                        
                        <div class="form-floating my-2">
                            <input type="text" class="form-control" id="contact" name="contact" maxlength="20" placeholder="Nome do Contato" value="{{$client->contact}}" disabled>
                            <label for="contact">Nome do Contato</label>
                        </div>

                        {{-- <div class="alert alert-danger mb-2">
                            Ao deleter o cadastro todos os dados deste cliente serão perdidos!
                        </div>

                        <div class="my-2">
                            <button type="submit" class="btn btn-outline-danger me-2"><i class="fa fa-trash"></i> Deletar</button> --}}
                            <a href="{{route('clients.index')}}" class="btn btn-outline-primary">
                                <i class="fa fa-arrow-left"></i> Voltar
                            </a>
                        </div>
                    </form>
                </main>
            </div>
        </div>
    </div>
@endsection