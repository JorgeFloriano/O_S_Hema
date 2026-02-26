<div class="Info">
    <div class="FirstInfoLine bg-t">
        <div class="InfoTitle"><strong>Dados do Equipamento</strong></div>
    </div>

    <div class="InfoLine">
        <div class="ThirdCollum"><strong>Modelo: </strong>{{$note->equip_mod ?? ''}}</div>
        <div class="ThirdCollum b-l"><strong>Série: </strong>{{$note->equip_id ?? ''}}</div>
        <div class="ThirdCollum b-l"><strong>Tipo: </strong>{{$note->equip_type ?? ''}}</div>
    </div>

    <div class="InfoLine bg-t">
        <div class="InfoTitle"><strong>Dados da Atividade Realizada</strong></div>
    </div>

    <div class="InfoLine">
        <div class="QuarterCollum"><strong>Tipo: </strong>{{$note->type->id ?? '------'}}</div>
        <div class="QuarterCollum b-l"><strong>Defeito: </strong>{{$note->defect->id ?? '------'}}</div>
        <div class="QuarterCollum b-l"><strong>Causa: </strong>{{$note->cause->id ?? '------'}}</div>
        <div class="QuarterCollum b-l"><strong>Solução: </strong>{{$note->solution->id ?? '------'}}</div>
    </div>

    <div class="InfoLine">
        <div class="QuarterCollum">{{$note->type->description ?? '------'}}</div>
        <div class="QuarterCollum b-l">{{$note->defect->description ?? '------'}}</div>
        <div class="QuarterCollum b-l">{{$note->cause->description ?? '------'}}</div>
        <div class="QuarterCollum b-l">{{$note->solution->description ?? '------'}}</div>
    </div>

    <div class="LastInfoLine" style="height: 250px">
        <div class="InfoFirstCollum LongText">
            <p>
                @if($note->materials->count() > 0)
                    <strong>Descrição dos Materiais Utilizados: </strong><br>
                    @foreach ($note->materials as $material)
                        @if ($loop->last)
                            {{$material->completeDescription().' - ('.$material->pivot->quantity.' '.$material->unit.'). '}}
                        @else
                            {{$material->completeDescription().' - ('.$material->pivot->quantity.' '.$material->unit.'), '}}
                        @endif
                    @endforeach
                    <br>
                @endif
                <strong>Descrição dos Serviços Executados: </strong><br>{{$note->services ?? ''}}
            </p>
        </div>
    </div>
</div>