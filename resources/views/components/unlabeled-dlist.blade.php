<div>
    <input list="{{$objects}}" onchange="getOptId('{{$index}}_{{$object}}','{{$index}}_{{$object}}_id', '{{$index}}_{{$object}}_option'), formSubmit('form')" class="form-control" id="{{$index}}_{{$object}}" name="{{$index}}" placeholder="{{$placeholder}}" required value="{{$value}}">

    <datalist id="{{$objects}}">
        @foreach ($objects as $item)
        @if ($subdescription != null && $subdescription != "")
            <option class="{{$index}}_{{$object}}_option" value="{{$item->$description->$subdescription}} - [{{$item->id}}]">
        @else
            <option class="{{$index}}_{{$object}}_option" value="{{$item->$description}} - [{{$item->id}}]">
        @endif
        @endforeach
    </datalist>
    
    <input type="hidden" name="ord_{{$index}}" id="{{$index}}_{{$object}}_id">
</div>