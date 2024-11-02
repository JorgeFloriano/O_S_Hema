<div class="form-floating my-2">
    <select class="form-select" id={{$name}} name={{$name}} aria-label="Floating label select example" required >

        <option value=""> Selecionar {{$nome}}</option>

        @foreach ($table as $object)
            @if(old($name) == $object->id)
                <option selected value={{$object->id}}>{{$object->id}} - {{$object->$description}}</option>
            @else
                <option value={{$object->id}}>{{$object->id}} - {{$object->$description}}</option>
            @endif
        @endforeach
    </select>
    <label for={{$name}}>{{$nome}}</label>
</div>