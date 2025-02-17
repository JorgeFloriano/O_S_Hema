<div>
    <input list="{{$index.$object}}_list" onchange="getOptId('{{$index.$object}}','{{$index.$object}}_id', '{{$index.$object}}_option', '{{$index}}')" onfocus="{{$onfocus}}" class="form-control" id="{{$index.$object}}" name="{{$index.$object}}" placeholder="{{$placeholder}}" required value="{{$value}}">

    <datalist id="{{$index.$object}}_list">

        @if ($object == 'tec')
            <option class="{{$index.$object}}_option" value="Não selecionado - [0]"></option>

            @if ($index == '')
                <option class="{{$index.$object}}_option" value="Todos - [0]"></option>
            @endif
        @endif

        @foreach ($objects as $item)
        @if ($subdescription != null && $subdescription != "")
            <option class="{{$index.$object}}_option" value="{{$item->$description->$subdescription}} - [{{$item->id}}]"></option>
        @else
            <option class="{{$index.$object}}_option" value="{{$item->$description}} - [{{$item->id}}]"></option>
        @endif
        @endforeach
    </datalist>
    
    <input type="hidden" name="{{$input_hidden_name}}" id="{{$index.$object}}_id" value="{{$input_hidden_value}}">

    @if ($index != '')
        <input type="hidden" name="order_id_{{$index}}" id="order_id_{{$index}}" value="{{$index}}">
    @endif
</div>