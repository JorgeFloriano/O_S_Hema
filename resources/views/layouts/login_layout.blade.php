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
            background-image: linear-gradient(to left, #606d7f, #293a4e);
        }
        .card {
            /*padding: 15px;*/
            border-radius: 15px;
            box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.746);
            border: none;
        }
        .card-img-top {
            background-color: rgb(35, 33, 33);
            border-radius:15px 15px 0 0;
            padding: 40px 15px;
        }

    </style>
    <title>Sistema de Gerenciamento Hema</title>
</head>
<body>

    @yield('content')

    <div class="text-center my-2 text-white">
        <small>Created by Jorge Luis &copy; {{date('Y')}}</small>
    </div>

    <script src="{{asset('assets/boodstrap/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/js/functions.js')}}"></script>
</body>
</html>