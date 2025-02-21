<div class="form-floating my-2">
    <input list="{{$objects}}" onchange="getOptId('{{$object}}','{{$object}}_id', '{{$object}}_option'){{$onchange}}" type="text" class="form-control" id="{{$object}}" name="{{$object}}" placeholder="{{$title}}" {{$required}} value="{{$value}}" onfocus="{{$onfocus}}">
    <label for="{{$object}}">{{$title}}</label>

    <datalist id="{{$objects}}">
        @foreach ($objects as $item)
            <option id="{{$object}}_option_{{$item->id}}" class="{{$object}}_option" value="{{$item->$description}} - [{{$item->id}}]">
                @if ($type == 'list')
                    <input type="hidden" class="{{$object}}-option-id" name="{{$item->id}}_id" id="{{$item->id}}_id" value="{{$item->id}}">
                    <input type="hidden" name="{{$item->id}}_unit" id="{{$item->id}}_unit" value="{{$item->unit}}">
                @endif
        @endforeach
    </datalist>
    
    <input type="hidden" name="{{$object}}_id" id="{{$object}}_id" value="{{$input_hidden_value}}">

    @if ($type == 'list')
        <input type="hidden" name="{{$object}}_ids_array" id="{{$object}}_ids_array">
        <div id="material_list">

        </div>
    @endif
</div>