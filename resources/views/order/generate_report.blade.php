<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="{{asset('assets/boodstrap/bootstrap.min.css')}}">
        <link rel="shortcut icon" href="{{asset('favicon.ico')}}">

        <style>
            body {
                background: rgb(239, 242, 249);
            }
            .card {
                margin-top: 20vh;
                padding: 15px;
                border-radius: 15px;
                box-shadow: 0px 0px 14px 0px rgba(0, 0, 0, 0.346);
                background: white;
            }

            .progress-bar {
                margin: 10px 0;
                padding: 2px;
                background-color: white;
                overflow: hidden;
                border-radius: 5px;
                height: 30px;
                width: 100%;
                border: solid 1px rgb(165, 160, 160);
            }

            .loaded {
                height: 100%;
                border-radius: 2px 0 0 2px;
                background: #198754;
            }
        </style>
        
        <title>Sistema de Gerenciamento Hema</title>
    </head>

    <body>

        <div class="container box">
            <div class="row">
                <div class="col-md-8 offset-md-2 col-sm-10 offset-sm-1">
                    <div class="card">
                        <div class="col-4 py-2">
                            <img src="{{ asset('assets/img/logo_hema.png')}}" width="100px" alt="logo hema">
                        </div>
                        <div class="text-center my-3">
                            <h2>
                                Gerando Relatório de Solicitações de Assistência Técnica.
                            </h2>
                            <hr>
                        </div>
                        <div class="my-3">
                            Carregando informações...<br>
                            Cliente: {{session('order_client_ids')[session('order_count_client_ids')]['name'] ?? 'Finalizando!'}}<br>
                            SAT número {{session('order_client_ids')[session('order_count_client_ids')]['orders'][session('order_index')] ?? ''}}

                            <div class="text-center">{{$percentage ?? ''}} %</div>

                            <div class="progress-bar">
                                <div class="loaded" style="width: {{$percentage ?? ''}}%"></div>
                            </div>
                        
                            Por favor, aguarde!
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
                                Voltar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <script src="{{asset('assets/js/button_click.js')}}"></script>
    </body>
 </html>