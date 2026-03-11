<div class="header">
    <span class="header1">
        <img src="assets/img/{{env('LOGO2')}}" width="100%" alt="logo hema">
    </span>

    <span class="header2">
        MONITORAMENTO IP<br>
        CABEAMENTO ESTRUTURADO<br>
        SINALIZAÇÃO SEMAFÓRICA<br>
        Fone: (15) 3243-4707<br>
        e-mail: atendimento@hema.com.br
    </span>

    <span class="header3">
        <div>SAT - Solicitação de Assistência Técnica</div>

        @if ($order->is_emergency)
            <div id='emergency'>EMERGENCIAL</div>
        @endif

        <div id="osId">{{number_format($order->id, 0, ',', '.')}}</div>
    </span>
</div>