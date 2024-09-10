<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/font-awesome.min.css')}}" type="text/css">
    <link rel="stylesheet" href="{{ asset('assets/boodstrap/bootstrap.min.css')}}" type="text/css">
    <style>
        body {
            background-image: linear-gradient(to left, rgb(159, 156, 156), rgb(86, 84, 84));
        }
        .card {
            padding: 15px;
            border-radius: 15px;
            box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.529);
        }
        #logo {
            background-color: rgb(221, 216, 216);
            padding: 10px;
            border-radius: 5px;
        }
    </style>
    <title>Login</title>
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