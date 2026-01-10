<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/font-awesome.min.css')}}" type="text/css">
    <link rel="stylesheet" href="{{ asset('assets/boodstrap/bootstrap.min.css')}}" type="text/css">
    <link rel="shortcut icon" href="{{asset('favicon.ico')}}">
    <style>
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
            
            opacity: 0.3; 
            z-index: -1; 
        }

        .card {
            /*padding: 15px;*/
            border-radius: 15px;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.557);
            border: none;
        }

        .card-img-top {
            background-color: #1b0363ff;
            border-radius:15px 15px 0 0;
            padding: 40px 15px;
        }
    </style>
    <title>Sistema de Gerenciamento Hema</title>
</head>
<body>

    @yield('content')

    <div class="text-center my-2">
        <small>Created by Jorge Luis &copy; 2024</small>
    </div>

    <script src="{{asset('assets/boodstrap/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/js/functions.js')}}"></script>
</body>
</html>