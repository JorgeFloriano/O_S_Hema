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
        <div class="InfoFirstCollum" style="width: 25%; border-right: hidden"><strong>Tipo de Atendimento: </strong></div>
        <div class="InfoCollum" style="width: 75%; border-left: hidden">{{$note->type->id.' - '.$note->type->description}}</div>
    </div>

    <div class="InfoLine">
        <div class="InfoFirstCollum" style="width: 25%; border-right: hidden"><strong>Defeito: </strong></div>
        <div class="InfoCollum" style="width: 75%; border-left: hidden">{{$note->defect->id.' - '.$note->defect->description}}</div>
    </div>

    <div class="InfoLine">
        <div class="InfoFirstCollum" style="width: 25%; border-right: hidden"><strong>Causa: </strong></div>
        <div class="InfoCollum" style="width: 75%; border-left: hidden">{{$note->cause->id.' - '.$note->cause->description}}</div>
    </div>

    <div class="InfoLine">
        <div class="InfoFirstCollum" style="width: 25%; border-right: hidden"><strong>Solução: </strong></div>
        <div class="InfoCollum" style="width: 75%; border-left: hidden">{{$note->solution->id.' - '.$note->solution->description}}</div>
    </div>

    <div class="LastInfoLine" style="height: 75px">
        <div class="InfoFirstCollum" style="overflow: initial"><strong>Observações: </strong>{{$note->services ?? ''}}</div>
    </div>
</div>