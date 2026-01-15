<div class="clientSign b-t">
    <table>
        <tr style="border-bottom: none">
            <th colspan="2">Dados do Cliente</th>
            <td style="width: 18%; border-right: hidden" rowspan="4">
                @if(isset($order->cl_sign_path))
                    <img src="storage/{{$order->cl_sign_path}}"
                         alt="-------" 
                         style="width: 100%; max-height: 80px; object-fit: contain;">
                @else
                    @if (isset($order->cl_sign))
                        <img src="{{$order->cl_sign}}"
                            alt="-------" 
                            style="width: 100%; max-height: 80px; object-fit: contain;">
                    @endif
                @endif
            </td>
        </tr>

        <tr style="border-bottom: none">
            <td colspan="2" style="width: 70%; border-bottom: 1px solid"><strong>Nome Completo:</strong> {{$order->cl_name ?? ''}}</td>
        </tr>

        <tr style="border-bottom: none">
            <td colspan="2" style="border-bottom: 1px solid"><strong>Função: </strong>{{$order->cl_function ?? ''}}</td>
        </tr>

        <tr style="border-bottom: none">
            <td><strong>Contato: </strong>{{$order->cl_contact ?? ''}}</td>
            <td><strong>Data: </strong>{{date('d/m/Y',strtotime($order->cl_date))}}</td>
        </tr>
    </table>
</div>