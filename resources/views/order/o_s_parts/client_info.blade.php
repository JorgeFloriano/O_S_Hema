<div class="Info">
    <div class="FirstInfoLine">
        <div class="InfoFirstCollum" style="width: 40%"><strong>Cliente</strong>: {{$order->client->name}}</div>
        <div class="InfoCollum" style="width: 40%"><strong>Unidade: </strong>{{$order->client->unit}}</div>
        <div class="InfoCollum"><strong>Cód:</strong></div>
    </div>

    <div class="InfoLine">
        <div class="InfoFirstCollum"><strong>Endereço: </strong>{{$order->client->address}}</div>
    </div>

    <div class="InfoLine">
        <div class="InfoFirstCollum" style="width: 50%"><strong>Contato: </strong>{{$order->req_name}}</div>
        <div class="InfoCollum"><strong>Setor: </strong>{{$order->sector}}</div>
    </div>

    <div class="InfoLine">
        <div class="InfoFirstCollum" style="width: 60%"><strong>Anotado por: </strong> {{$order->user->name ?? ''}}</div>
        <div class="InfoCollum" style="width: 20%">
            <strong>Data: </strong>{{date('d/m/y',strtotime($order->req_date))}}
        </div>
        <div class="InfoCollum">
            <strong>Hora: </strong>{{date('H:i',strtotime($order->req_time))}}
        </div>
    </div>

    <div class="InfoLine">
        <div class="InfoFirstCollum" style="width: 50%"><strong>Tipo de Serviço: </strong>CFTV</div>
        <div class="InfoCollum"><strong>Equipamento : </strong>{{$order->equipment ?? ''}}</div>
    </div>

    <div class="LastInfoLine" style="height: 113px">
        <div class="InfoFirstCollum LongText"><p><strong>Problema relatado: </strong>{{$order->req_descr ?? ''}}</p></div>
    </div>
</div>