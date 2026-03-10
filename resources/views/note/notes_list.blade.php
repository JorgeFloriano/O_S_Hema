@extends('layouts.o_s_form_layout')

@section('content')
    @php
        use Illuminate\Support\Facades\Crypt;
        use App\Class\TextFormat;
        $text = new TextFormat();
    @endphp

    {{-- container-sm was altered in my bootstrap.min.css --}}
    <div class="container-sm box">
        <div class="row">
            <div class="col">
                <x-live-toast-message></x-live-toast-message>

                <div id="header" class="my-3 d-flex flex-wrap justify-content-between align-items-center">
                    {{-- Título à esquerda --}}
                    <div class="mb-2">
                        <h2>Programação (SATs)</h2>
                    </div>

                    @if (auth()->user()->tec->on_call)
                        {{-- Botões à direita (quando couber) --}}
                        <div class="mb-2">
                            <a href="{{route('orders.create')}}" class="btn btn-primary"
                            data-bs-toggle="tooltip" title="Criar novo Usuário">
                                <i class="fa fa-plus"></i> Criar Nova
                            </a>
                        </div>
                    @endif
                </div>

                <hr>

                @if ($orders->count() === 0)
                    <p>
                        Nenhum registro encontrado !
                    </p>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="notes_list">
                            <thead class="table-primary">
                                <tr>
                                    <th>SAT</th>
                                    <th>Cliente</th>
                                    <th>Equipamento</th>
                                    <th>Problema relatado</th>
                                    <th>Data</th>
                                    <th>Exec.</th>
                                    <th>Finl.</th>
                                    <th><i style="font-size: 20px;" class="fa fa-exclamation-circle"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td>{{number_format($order->id, 0, ',', '.')}}</td>
                                        <td>{{$order->client->name}}</td>
                                        <td>{{$order->equipment ?? 'Não informado'}}</td>
                                        <td>{{$text->spaceAfterPunctuation($order->req_descr)}}</td>
                                        <td>{{date('d/m/y',strtotime($order->req_date))}}</td>
                                        @if ($order->finished)
                                            <td>
                                                <a href="{{route('orders.show_pdf', ['order' => Crypt::encryptString($order->id)])}}" class="btn btn-outline-primary btn-sm">
                                                    <i class="fa fa-file-pdf-o"></i>
                                                </a>
                                            </td>
                        
                                            <td>
                                                <a class="btn btn-outline-primary btn-sm disabled">
                                                    <i class="fa fa-check-square-o"></i>
                                                </a>
                                            </td>
                                        @else
                                            <td>
                                                <a href="{{route('notes.create', ['order' => Crypt::encryptString($order->id)])}}" class="btn btn-outline-primary btn-sm">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </td>
                                            @if ($order->notes->count() > 0)
                                                <td>
                                                    <a href="{{route('orders.finish', ['order' => Crypt::encryptString($order->id)])}}" class="btn btn-primary btn-sm">
                                                        <i class="fa fa-check-square-o"></i>
                                                    </a>
                                                </td>
                                            @else
                                                <td>
                                                    <a class="btn btn-outline-primary btn-sm disabled">
                                                        <i class="fa fa-check-square-o"></i>
                                                    </a>
                                                </td>
                                            @endif
                                        @endif
                                        <td class="text-center align-middle">
                                            <x-status-badge 
                                                :status="$order->finished ? 'F' : 'P'" 
                                                :urgent="$order->is_emergency" 
                                            />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                
                <div>
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
     </div>
@endsection
