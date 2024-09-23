


<div id="Info">
    <div class="FirstInfoLine bg-t">
        <div class="InfoTitle"><strong>Informações do Equipamento</strong></div>
    </div>

    <div class="InfoLine">
        <div class="InfoFirstCollum" style="width: 33.3%"><strong>Modelo: </strong>{{$order->notes[0]->equip_mod}}</div>
        <div class="InfoCollum" style="width: 33.3%"><strong>Série: </strong>{{$order->notes[0]->equip_id}}</div>
        <div class="InfoCollum"><strong>Tipo: </strong>{{$order->notes[0]->equip_type}}</div>
    </div>

    <div class="InfoLine bg-t">
        <div class="InfoTitle"><strong>Informações da Intervenção</strong></div>
    </div>

    <div class="InfoLine">
        <div class="InfoFirstCollum"><strong>Situação encontrada: </strong>{{$order->notes[0]->situation}}</div>
    </div>

    <div class="InfoLine">
        <div class="InfoFirstCollum"><strong>Provável causa: </strong>{{$order->notes[0]->cause}}</div>
    </div>

    <div class="InfoLine" style="height: 100px">
        <div class="InfoFirstCollum"><strong>Serviços Realizados: </strong>{{$order->notes[0]->services}}</div>
    </div>

    <div class="InfoLine">
        <div class="InfoFirstCollum" style="width: 50%">
            <strong>Data do acionamento: </strong>{{date('d/m/y',strtotime($order->req_date))}}
        </div>
        <div class="InfoCollum">
            <strong>Hora do acionamento: </strong>{{date('H:i',strtotime($order->req_time))}}
        </div>
    </div>

    <div class="InfoLine">
        <div class="InfoFirstCollum"><strong>Equipamento relatado: </strong> {{$order->equipment ?? ''}}</div>
    </div>

    <div class="LastInfoLine">
        <div class="InfoFirstCollum"><strong>Problema relatado: </strong> {{$order->req_descr ?? ''}}</div>
    </div>
</div>