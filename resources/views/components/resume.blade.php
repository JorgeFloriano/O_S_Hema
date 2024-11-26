<div class="Info">
    <h1 class="resume-H1">Resumo</h1>

    <div class="resume-line">
        <div class="resume-c1" style="width: 73%"><strong>Cliente</strong></div>
        <div class="resume-c2"><strong>Atendimentos</strong></div>
    </div>

    @foreach ($order_by_clients as $clientId => $orders)
        <div class="resume-line">
            <div class="resume-c1" style="width: 73%">{{$orders->first()->client->name}}</div>
            <div class="resume-c2">{{count($orders)}}</div>
        </div>
        @if ($loop->iteration % 32 == 0)
            <div class="page-number">página {{$page}}</div>
            @php $page++; @endphp
            
            <div class="page-break"></div>
            <h1 class="resume-H1">Resumo <span style="font-size: 16px;font-weight: normal;"></span></h1>

            <div class="resume-line">
                <div class="resume-c1" style="width: 73%"><strong>Cliente</strong></div>
                <div class="resume-c2"><strong>Atendimentos</strong></div>
            </div>
        @endif
    @endforeach

    <div class="page-number">página {{$page}}</div>
    @php $page++; @endphp
</div>