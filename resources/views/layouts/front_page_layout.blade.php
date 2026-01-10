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