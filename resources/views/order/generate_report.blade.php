<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="{{asset('assets/boodstrap/bootstrap.min.css')}}">
        <link rel="shortcut icon" href="{{asset('favicon.ico')}}">
        
        <title>Sistema de Gerenciamento Hema</title>
    </head>

    <body>

        @include('userbar')

        <div class="container">
            <div class="row mt-5">
                <div class="col">
                    <div id="header" class="my-2">
                        <h2>
                            Gerando Relatório!
                        </h2>
                        <hr>
                    </div>
                   
                    <div class="alert alert-info text-center" role="alert">
                        <div>Carregando informações das Solicitações de Serviço, aguarde {{session('dot')}}</div>
                        <br>  
                        <div style="text-align: center">{{$percentage ?? ''}} %</div>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-warning">
                            <ul>
                                @foreach ($errors->all() as $msg)
                                    <li>{{$msg}}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div style="display: none">
                        <a id='continue' href="{{route('orders.generate_report', ['msg' => 'continue'])}}" class="btn btn-secondary">
                            Continuar carregando...
                        </a>
                    </div>
            
                    <div>
                        <a id='cancel' href="{{route('orders.index')}}" class="btn btn-secondary">
                            Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <script src="{{asset('assets/js/button_click.js')}}"></script>
    </body>
 </html>