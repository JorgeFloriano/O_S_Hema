<div class="clientSign b-t">
    <table>
        <tr>
            <th colspan="3" style="border-right: none">Cliente</th>
        </tr>

        <tr style="border-bottom: none">
            <th colspan="2" style="width: 70%">Nome Completo</th>
            <td style="width: 30%; border-right: hidden" rowspan="6">
                <img src={{$order->cl_sign ?? ''}} alt=" " width="100%">
            </td>
        </tr>

        <tr style="border-bottom: none">
            <td colspan="2" style="border-bottom: 1px solid">{{$order->cl_name ?? '-'}}</td>
        </tr>

        <tr style="border-bottom: none">
            <th colspan="2" >Função</th>
        </tr>

        <tr style="border-bottom: none">
            <td colspan="2" style="border-bottom: 1px solid">{{$order->cl_function ?? '-'}}</td>
        </tr>

        <tr style="border-bottom: none">
            <th>Contato</th>
            <th>Data</th>
        </tr>

        <tr style="border-bottom: none">
            <td style="text-align: center">{{$order->cl_contact ?? '-'}}</td>
            <td style="text-align: center">{{date('d/m/y',strtotime($order->cl_date))}}</td>
        </tr>
    </table>
</div>