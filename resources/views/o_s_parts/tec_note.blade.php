<div class="b-t">
    <table>
        <tr>
            <th colspan="6" style="border-right: none">Apontamento dos Técnicos - {{date('d/m/Y',strtotime($order->notes[0]->date))}}</th>
        </tr>

        <tr>
            <th colspan="2" style="width: 33.3%">Ida</th>
            <th colspan="2" style="width: 33.3%">Intervenção</th>
            <th colspan="2" style="border-right: none">Volta</th>
        </tr>

        <tr>
            <th style="width: 16.6%">Sáida</th>
            <th>Chegada</th>
            <th>Início</th>
            <th>Término</th>
            <th>Saída</th>
            <th style="border-right: none">Chegada</th>
        </tr>

        <tr>
            <td style="text-align: center; width: 16.6%">{{date('H:i',strtotime($order->notes[0]->go_start))}}</td>
            <td style="text-align: center">{{date('H:i',strtotime($order->notes[0]->go_end))}}</td>
            <td style="text-align: center">{{date('H:i',strtotime($order->notes[0]->start))}}</td>
            <td style="text-align: center">{{date('H:i',strtotime($order->notes[0]->end))}}</td>
            <td style="text-align: center">{{date('H:i',strtotime($order->notes[0]->back_start))}}</td>
            <td style="text-align: center; border-right: none">{{date('H:i',strtotime($order->notes[0]->back_end))}}</td>
        </tr>

        <tr>
            <th colspan="2">Técnico 01</th>

            <td style="width: 16.6%" rowspan="3">
                <img src={{$order->notes[0]->tecs[0]->pivot->signature}} alt="" width="100%">
            </td>

            <th colspan="2" style="width: 33.33%">Técnico 02</th>

            <td style="width: 16.6%; border-right: none" rowspan="3">
                <img src={{$order->notes[0]->tecs[1]->pivot->signature ?? ''}} alt="" width="100%">
            </td>
        </tr>

        <tr>
            <td style="width: 33.3%" colspan="2">Nome: {{$order->notes[0]->tecs[0]->user->name}}</td>
            <td style="width: 33.3%" colspan="2">Nome: {{$order->notes[0]->tecs[1]->user->name ?? '--------------------------------------------'}}</td>
        </tr>
        
        <tr  style="border-bottom: none">
            <td colspan="2">Função: {{$order->notes[0]->tecs[0]->user->function}}</td>
            <td colspan="2">Função: {{$order->notes[0]->tecs[1]->user->function ?? '------------------------------------------'}}</td>
        </tr>
    </table>
</div>