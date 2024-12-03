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
        <div class="container">
            <div class="row mt-5">
                <div class="col">
                    <div class="alert alert-info" role="alert">
                        Gerando Relatório, aguarde...
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
                </div>
            </div>
        </div>
        <div style="display: none">
            <a id='continue' href="{{route('orders.generate_report', ['msg' => 'Back'])}}" class="btn btn-secondary">
                Continuar carregando...
            </a>
        </div>
        @php
            redirect()->route('orders.generate_report', ['msg' => 'Back']);
        @endphp
    <script src="{{asset('assets/js/button_click.js')}}"></script>
    </body>
 </html>