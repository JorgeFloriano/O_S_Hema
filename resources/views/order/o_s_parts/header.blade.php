<div id="header">
    <div id="header1">
        <img src="{{ asset('assets/img/'.env('LOGO2'))}}" width="100%" alt="logo hema">
    </div>

    <div id="header2">
        MONITORAMENTO IP<br>
        CABEAMENTO ESTRUTURADO<br>
        SINALIZAÇÃO SEMAFÓRICA<br>
        <strong>
            Fone: (15) 3243-4707<br>
            <span>e-mail: atendimento@hema.com.br</span>
        </strong>
    </div>

    <div id="header3">
        <div>SAT - Solicitação de Assistência Técnica</div>

        @if ($order->is_emergency)
            <div id='emergency'>EMERGENCIAL</div>
        @endif
       
        <div id="osId">{{number_format($order->id, 0, ',', '.')}}</div>
    </div>
</div>