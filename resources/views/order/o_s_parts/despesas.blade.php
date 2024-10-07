<div class="b-half-table">
    <table>
        <tr>
            <th colspan="4" style="border-right: none">Despesas</th>
        </tr>

        <tr>
            <th style="width: 25%">Alimentação (R$)</th>
            <th style="width: 25%">Km Inicial</th>
            <th style="width: 25%">Km Final</th>
            <th style="width: 25%; border-right: none">Outros (R$)</th>
        </tr>

        <tr style="border-bottom: none">
            <td style="text-align: center">{{$note->food ?? '00'}}</td>
            <td style="text-align: center">{{$note->km_start ?? '00'}}</td>
            <td style="text-align: center">{{$note->km_end ?? '00'}}</td>
            <td style="text-align: center; border-right: none;">{{$note->expense ?? '00'}}</td>
        </tr>
    </table>
</div>

<div class="LastInfoLine" style="height: 50px; margin-bottom: 15px">
    <div class="InfoFirstCollum" style="overflow: initial"><strong>Observações: </strong>{{$note->obs ?? ''}}</div>
</div>