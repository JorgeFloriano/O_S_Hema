<div class="b-t">
    <table>
        <tr>
            <th colspan="6" style="border-right: none">Apontamento de horas dos Técnicos</th>
        </tr>

        <tr>
            <th style="width: 16.6%">Saída (ida)</th>
            <th style="width: 16.6%">Chegada (ida)</th>
            <th style="width: 16.6%">Início</th>
            <th style="width: 16.6%">Término</th>
            <th style="width: 16.6%">Saída (volta)</th>
            <th style="border-right: none">Chegada (volta)</th>
        </tr>

        <tr>
            <td style="text-align: center; width: 16.6%">{{$note->go_start}}</td>
            <td style="text-align: center">{{$note->go_end}}</td>
            <td style="text-align: center">{{$note->start}}</td>
            <td style="text-align: center">{{$note->end}}</td>
            <td style="text-align: center">{{$note->back_start}}</td>
            <td style="text-align: center; border-right: none">{{$note->back_end}}</td>
        </tr>

        <tr style="border-bottom: none">
            <th colspan="2">Técnico 01</th>

            {{-- Technician 1 Signature --}}
            <td style="width: 16.6%" rowspan="3">
                @if(isset($note->tecs[0]) && $note->tecs[0]->pivot->signature_path && Storage::disk('public')->exists($note->tecs[0]->pivot->signature_path))
                    <img src="{{ asset('storage/' . $note->tecs[0]->pivot->signature_path ?? '') }}" 
                        alt="------" 
                        style="width: 100%; max-height: 80px; object-fit: contain;">
                @else
                    <img src={{$note->tecs[0]->pivot->signature ?? ''}} 
                        alt="------" 
                        style="width: 100%; max-height: 80px; object-fit: contain;">
                @endif
            </td>

            <th colspan="2" style="width: 33.33%">Técnico 02</th>

            {{-- Technician 2 Signature --}}
            <td style="width: 16.6%; border-right: hidden" rowspan="3">
                @if(isset($note->tecs[1]) && $note->tecs[1]->pivot->signature_path && Storage::disk('public')->exists($note->tecs[1]->pivot->signature_path))
                    <img src="{{ asset('storage/' . $note->tecs[1]->pivot->signature_path) }}" 
                        alt="------" 
                        style="width: 100%; max-height: 80px; object-fit: contain;">
                @else
                    @if (isset($note->tecs[1]))
                        <img src={{$note->tecs[1]->pivot->signature}} 
                            alt="------" 
                            style="width: 100%; max-height: 80px; object-fit: contain;">
                    @endif
                @endif
            </td>
        </tr>

        <tr style="border-bottom: none">
            <td style="width: 33.3%;border-bottom:1px solid black;" colspan="2">
                <strong>Nome: </strong>{{$note->tecs[0]->user->name ?? '---------------------'}}
            </td>
            <td style="width: 33.3%;border-bottom:1px solid black;" colspan="2">
                <strong>Nome: </strong>{{$note->tecs[1]->user->name ?? '---------------------'}}
            </td>
        </tr>
        
        <tr style="border-bottom: none">
            <td colspan="2">
                <strong>Função: </strong>{{$note->tecs[0]->user->function ?? '---------------------'}}
            </td>
            <td colspan="2">
                <strong>Função: </strong>{{$note->tecs[1]->user->function ?? '---------------------'}}
            </td>
        </tr>
    </table>
</div>