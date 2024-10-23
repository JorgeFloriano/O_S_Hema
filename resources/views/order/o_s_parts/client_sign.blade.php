<div class="clientSign b-t">
    <table>
        <tr style="border-bottom: none">
            <th colspan="2">Dados do Cliente</th>
            <td style="width: 18%; border-right: hidden" rowspan="4">
                <img src={{$order->cl_sign ?? ''}} alt=" "width="100%">
            </td>
        </tr>

        <tr style="border-bottom: none">
            <td colspan="2" style="width: 70%; border-bottom: 1px solid"><strong>Nome Completo:</strong> {{$order->cl_name ?? ''}}</td>
        </tr>

        <tr style="border-bottom: none">
            <td colspan="2" style="border-bottom: 1px solid"><strong>Função: </strong>{{$order->cl_function ?? '-'}}</td>
        </tr>

        <tr style="border-bottom: none">
            <td><strong>Contato: </strong>{{$order->cl_contact ?? ''}}</td>
            <td><strong>Data: </strong>{{date('d/m/Y',strtotime($order->cl_date))}}</td>
        </tr>
    </table>
</div>