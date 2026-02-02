<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/font-awesome.min.css')}}" type="text/css">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/bootstrap.min.css')}}" type="text/css">
    <link rel="shortcut icon" href="{{asset('favicon.ico')}}">
    <style>
        /* Garante que o body ocupe toda a tela para permitir a centralização vertical */
        html, body {
            height: 100%;
        }

        /* Criamos uma classe para o container principal ocupar toda a altura disponível */
        .full-height {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Estilos gerais */
        body {
            position: relative;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            /* Impede a rolagem lateral horizontal */
            overflow-x: hidden;
        }

        /* Camada da Imagem */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw; /* Use vw (viewport width) para garantir 100% da largura da janela */
            height: 100vh; /* Use vh (viewport height) para 100% da altura */
            background-image: url("{{ asset('assets/img/bg_image.jpeg') }}"); 
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.3; 
            z-index: -1;
            /* Garante que o pseudo-elemento não capture cliques ou crie áreas fantasmas */
            pointer-events: none;
            align-items: center;
        }

        .card {
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
<body class="d-flex flex-column min-vh-100">

    <main class="flex-grow-1 d-flex align-items-center">
        @yield('content')
    </main>

    <div class="text-center my-2">
        <small>Created by Jorge Luis &copy; 2024</small>
    </div>

    <script src="{{asset('assets/bootstrap/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/js/functions.js')}}"></script>
</body>
</html>