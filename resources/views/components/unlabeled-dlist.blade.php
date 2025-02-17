<div>
    <input list="{{$objects}}" onchange="getOptId('{{$index.$object}}','{{$index.$object}}_id', '{{$index.$object}}_option'){{$onchange}}" onfocus="{{$onfocus}}" class="form-control" id="{{$index.$object}}" name="{{$index}}" placeholder="{{$placeholder}}" required value="{{$value}}">

    <datalist id="{{$objects}}">

        @if ($object == 'tec')
            <option class="{{$index.$object}}_option" value="Não selecionado - [0]">

            @if ($index == '')
                <option class="{{$index.$object}}_option" value="Todos - [0]">
            @endif
        @endif

        @foreach ($objects as $item)
        @if ($subdescription != null && $subdescription != "")
            <option class="{{$index.$object}}_option" value="{{$item->$description->$subdescription}} - [{{$item->id}}]">
        @else
            <option class="{{$index.$object}}_option" value="{{$item->$description}} - [{{$item->id}}]">
        @endif
        @endforeach
    </datalist>
    
    <input name="{{$input_hidden_name}}" id="{{$index.$object}}_id" value="{{$input_hidden_value}}">
</div>