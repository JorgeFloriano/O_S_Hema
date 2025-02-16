<div class="Info">
    <div class="FirstInfoLine bg-t">
        <div class="InfoTitle"><strong>Dados do Equipamento</strong></div>
    </div>

    <div class="InfoLine">
        <div class="InfoFirstCollum center" style="width: 33.3%"><strong>Modelo: </strong>{{$note->equip_mod ?? ''}}</div>
        <div class="InfoCollum center" style="width: 33.3%"><strong>Série: </strong>{{$note->equip_id ?? ''}}</div>
        <div class="InfoCollum center" style="width: 33.3%"><strong>Tipo: </strong>{{$note->equip_type ?? ''}}</div>
    </div>

    <div class="InfoLine bg-t">
        <div class="InfoTitle"><strong>Dados da Atividade Realizada</strong></div>
    </div>

    <div class="InfoLine">
        <div class="InfoFirstCollum center" style="width: 25%"><strong>Tipo: </strong>{{$note->type->id}}</div>
        <div class="InfoCollum center" style="width: 25%"><strong>Defeito: </strong>{{$note->defect->id}}</div>
        <div class="InfoCollum center" style="width: 25%"><strong>Causa: </strong>{{$note->cause->id}}</div>
        <div class="InfoCollum center" style="width: 25%"><strong>Solução: </strong>{{$note->solution->id}}</div>
    </div>

    <div class="InfoLine">
        <div class="InfoFirstCollum center" style="width: 25%">{{$note->type->description}}</div>
        <div class="InfoCollum center" style="width: 25%">{{$note->defect->description}}</div>
        <div class="InfoCollum center" style="width: 25%">{{$note->cause->description}}</div>
        <div class="InfoCollum center" style="width: 25%">{{$note->solution->description}}</div>
    </div>

    {{-- <div class="InfoLine">
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
    </div> --}}

    <div class="LastInfoLine" style="height: 250px">
        <div class="InfoFirstCollum LongText">
            <p>
                @if($note->materials->count() > 0)
                    <strong>Descrição dos Materiais Utilizados: </strong><br>
                    @foreach ($note->materials as $material)
                        {{$material->description.' ('.$material->pivot->quantity.' '.$material->unit.')'}}
                        @if ($loop->last)
                            . 
                        @else
                            , 
                        @endif
                    @endforeach
                    <br>
                @endif
                <strong>Descrição dos Serviços Executados: </strong><br>{{$note->services ?? ''}}
            </p>
        </div>
    </div>
</div>