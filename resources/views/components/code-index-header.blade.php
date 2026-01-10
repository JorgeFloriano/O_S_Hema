<div>
    <x-live-toast-message></x-live-toast-message>
    <div id="header" class="my-3 d-flex flex-wrap justify-content-between align-items-center">
        {{-- Título à esquerda --}}
        <div id="header" class="mb-2">
            <h2>{{$text}} {{$msg}}</h2>
        </div>
    
        {{-- Botões à direita (quando couber) --}}
        <div class="mb-2">
            <a href={{route($object.'.create')}} class="btn btn-primary me-2"><i class="fa fa-plus"></i> Cadastrar Novo</a>
            <a href={{route($object.'.list', ['opt' => $opt])}} class="btn btn-outline-primary"><i class="fa fa-exchange" aria-hidden="true"></i> {{$title}}</a>
        </div>
    </div>
    <hr>
</div>