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
            <td style="text-align: center">{{number_format($note->food, 2, '.', '') ?? '00.00'}}</td>
            <td style="text-align: center">{{number_format($note->km_start, 2, '.', '') ?? '00.00'}}</td>
            <td style="text-align: center">{{number_format($note->km_end, 2, '.', '') ?? '00.00'}}</td>
            <td style="text-align: center; border-right: none;">{{number_format($note->expense, 2, '.', '') ?? '00.00'}}</td>
        </tr>
    </table>
</div>

<div class="LastInfoLine" style="height: 50px; margin-bottom: 15px">
    <div class="InfoFirstCollum" style="overflow: initial"><strong>Observações: </strong>{{$note->obs ?? ' Sem observações'}}</div>
</div>