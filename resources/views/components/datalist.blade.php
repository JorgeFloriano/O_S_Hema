<div class="form-floating my-2">
    <input list="{{$objects}}" onchange="getOptId('{{$object}}','{{$object}}_id', '{{$object}}_option')" class="form-control" id="{{$object}}" name="{{$object}}" placeholder="{{$title}}" required value="{{$value}}">
    <label for="$oject">{{$title}}</label>

    <datalist id="{{$objects}}">
        @foreach ($objects as $item)
            <option class="{{$object}}_option" value="{{$item->name}} - [{{$item->id}}]">
        @endforeach
    </datalist>
    
    <input type="hidden" name="{{$object}}_id" id="{{$object}}_id">
</div>