<div id="clientInfo">
    <div class="clientInfoLine">
        <div class="clientInfoFirstCollum" style="width: 80%"><strong>Cliente</strong>: {{$order->client->name}}</div>
        <div class="clientInfoCollum"><strong>Cód:</strong></div>
    </div>

    <div class="clientInfoLine">
        <div class="clientInfoFirstCollum" style="width: 70%"><strong>Unidade: </strong>{{$order->client->unit}}</div>
        <div class="clientInfoCollum"><strong>Cód: </strong></div>
    </div>

    <div class="clientInfoLine">
        <div class="clientInfoFirstCollum"><strong>Endereço: </strong>{{$order->client->address}}</div>
    </div>

    <div class="clientInfoLine">
        <div class="clientInfoFirstCollum" style="width: 50%"><strong>Contato: </strong>{{$order->client->contact}}</div>
        <div class="clientInfoCollum"><strong>Orgão Solicitante: </strong>Sup</div>
    </div>

    <div class="clientLastInfoLine">
        <div class="clientInfoFirstCollum"><strong>Anotado por: </strong> {{$order->user->name ?? ''}}</div>
    </div>
</div>