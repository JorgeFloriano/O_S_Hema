<div class="Info">
    <div class="header">
        <span class="header1">
            <img src="assets/img/logo2_hema.png" width="100%" alt="logo hema">
        </span>
    
        <span class="resume-H1">
            RESUMO
        </span>
    </div>

    <div class="resume-line">
        <div class="resume-c1" style="width: 73%"><strong>Cliente</strong></div>
        <div class="resume-c2"><strong>Atendimentos</strong></div>
    </div>

    @foreach ($order_by_clients as $clientId => $client)
        <div class="resume-line">
            <div class="resume-c1" style="width: 73%">{{$client['name']}}</div>
            <div class="resume-c2">{{count($client['orders'])}}</div>
        </div>
        @if ($loop->iteration % 30 == 0)
            <div class="page-number">página {{session('page')}}</div>
            @php session()->put('page', session('page') + 1); @endphp
            
            <div class="page-break"></div>
            <div class="header">
                <span class="header1">
                    <img src="assets/img/logo2_hema.png" width="100%" alt="logo hema">
                </span>
            
                <span class="resume-H1">
                    RESUMO
                </span>
            </div>

            <div class="resume-line">
                <div class="resume-c1" style="width: 73%"><strong>Cliente</strong></div>
                <div class="resume-c2"><strong>Atendimentos</strong></div>
            </div>
        @endif
    @endforeach

    <div class="page-number">página {{session('page')}}</div>
</div>