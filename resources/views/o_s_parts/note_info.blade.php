<div id="Info">
    <div class="FirstInfoLine bg-t">
        <div class="InfoTitle"><strong>Dados do Equipamento</strong></div>
    </div>

    <div class="InfoLine">
        <div class="InfoFirstCollum" style="width: 33.3%"><strong>Modelo: </strong>{{$note->equip_mod ?? ''}}</div>
        <div class="InfoCollum" style="width: 33.3%"><strong>Série: </strong>{{$note->equip_id ?? ''}}</div>
        <div class="InfoCollum"><strong>Tipo: </strong>{{$note->equip_type ?? ''}}</div>
    </div>

    <div class="InfoLine bg-t">
        <div class="InfoTitle"><strong>Intervenção</strong></div>
    </div>

    <div class="InfoLine">
        <div class="InfoFirstCollum"><strong>Situação encontrada: </strong>{{$note->situation ?? ''}}</div>
    </div>

    <div class="InfoLine">
        <div class="InfoFirstCollum"><strong>Causa provável: </strong>{{$note->cause ?? ''}}</div>
    </div>

    <div class="LastInfoLine" style="height: 100px">
        <div class="InfoFirstCollum" style="overflow: initial"><strong>Serviços Realizados: </strong>{{$note->services ?? ''}}</div>
    </div>
</div>