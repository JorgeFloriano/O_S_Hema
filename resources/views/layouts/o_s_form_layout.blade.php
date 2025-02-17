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
    <title>Sistema de Gerenciamento Hema</title>
</head>
<body>

    @include('navbar')
    @yield('content')

    <script src="{{asset('assets/boodstrap/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/js/functions.js')}}"></script>
    {{-- @vite(['resources/js/alpine/start.js', 'resources/js/app.js']) --}}
    <script src="{{asset('assets/js/start-BYHTzsLu.js')}}"></script>
</body>
</html>