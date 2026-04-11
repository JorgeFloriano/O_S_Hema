<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{asset('assets/fontawesome/font-awesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/bootstrap/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/form.css')}}">
    <link rel="shortcut icon" href="{{asset('favicon.ico')}}">
    <!-- Add this to your layout file's <head> -->
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"> --}}

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
                margin: 0;
                padding: 0;
                
                /* Impede a rolagem lateral horizontal */
                overflow-x: hidden; 
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

    <script src="{{asset('assets/bootstrap/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/js/functions.js')}}"></script>
    <script src="{{asset('assets/js/start-BYHTzsLu.js')}}"></script>
</body>
</html>