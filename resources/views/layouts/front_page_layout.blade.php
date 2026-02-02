<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        
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
        <section id="print">
            @yield('content')
        </section>
    </body>
</html>