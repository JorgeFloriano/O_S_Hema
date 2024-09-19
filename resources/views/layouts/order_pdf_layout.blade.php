<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="{{asset('assets/fontawesome/font-awesome.min.css')}}">
        <link rel="stylesheet" href="{{asset('assets/boodstrap/bootstrap.min.css')}}">
        <link rel="stylesheet" href="{{asset('assets/css/order.css')}}">
        <script src="{{asset('assets/boodstrap/bootstrap.bundle.min.js')}}"></script>
        <script src="{{asset('assets/js/pdf.js')}}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <style media="print">

            #buttonGroup {
                display: none;
            }
        </style>
        <title>Ordem de Serviços</title>
    </head>

    <body>
        <section id="print">
            @yield('content')
        </section>
    </body>

    <div id="buttonGroup" class="mt-2">
        <button id="btnPdf" class="btn btn-info">BAIXAR PDF</button>
        <a href="{{route('notes.index')}}" class="btn btn-secondary ms-2">VOLTAR</a>
    </div>
</html>