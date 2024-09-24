<div id="Info">
    <div class="FirstInfoLine bg-t">
        <div class="InfoTitle"><strong>Informações do Equipamento</strong></div>
    </div>

    <div class="InfoLine">
        <div class="InfoFirstCollum" style="width: 33.3%"><strong>Modelo: </strong>{{$order->notes[0]->equip_mod ?? ''}}</div>
        <div class="InfoCollum" style="width: 33.3%"><strong>Série: </strong>{{$order->notes[0]->equip_id ?? ''}}</div>
        <div class="InfoCollum"><strong>Tipo: </strong>{{$order->notes[0]->equip_type ?? ''}}</div>
    </div>

    <div class="InfoLine bg-t">
        <div class="InfoTitle"><strong>Informações da Intervenção</strong></div>
    </div>

    <div class="InfoLine">
        <div class="InfoFirstCollum"><strong>Situação encontrada: </strong>{{$order->notes[0]->situation ?? ''}}</div>
    </div>

    <div class="InfoLine">
        <div class="InfoFirstCollum"><strong>Causa provável: </strong>{{$order->notes[0]->cause ?? ''}}</div>
    </div>

    <div class="LastInfoLine" style="height: 100px">
        <div class="InfoFirstCollum"><strong>Serviços Realizados: </strong>{{$order->notes[0]->services ?? ''}}</div>
    </div>
</div>