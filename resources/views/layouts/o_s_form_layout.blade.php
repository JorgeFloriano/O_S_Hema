<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{asset('assets/fontawesome/font-awesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/boodstrap/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/form.css')}}">
    <link rel="shortcut icon" href="{{asset('favicon.ico')}}">
    <style>
            :root {
                --blue1: #e2eaee;
            }

            * {
                font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
                padding: 0px;
                margin: 0px;
                box-sizing: border-box;
            }

            body {
                position: relative;
                min-height: 100vh;
                margin: 0;
                background-color: #f8f9fa; /* Cor de fundo caso a imagem falhe */
            }

            /* Camada da Imagem */
            body::before {
                content: "";
                position: fixed; 
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                /* Aspa simples adicionada corretamente abaixo: */
                background-image: url("{{ asset('assets/img/bg_image.jpg') }}"); 
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                
                opacity: 0.05; 
                z-index: -1; 
            }
            
            .logo {
                position: absolute;
                top: 40%; left: 50%; 
                transform: translate(-50%, -50%); 
                text-align: center; 
                font-size: 19px;
            }
        </style>
    <title>Sistema de Gerenciamento Hema</title>
</head>
<body>

    @include('navbar')
    @yield('content')

    <script src="{{asset('assets/boodstrap/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/js/functions.js')}}"></script>
    <script src="{{asset('assets/js/start-BYHTzsLu.js')}}"></script>
</body>
</html>