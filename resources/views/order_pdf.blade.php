@extends('layouts.order_pdf_layout')

@section('content')

    <style media="print">
        #buttonGroup {
            display: none;
        }
        @media print {
        .clientSign {page-break-after: always;}
        }
    </style>

    @foreach ($order->notes as $note)
        @include('o_s_parts/header')

        @include('o_s_parts/client_info')

        <div class="mt-4"><strong>Informações do Atendimento (data {{date('d/m/Y',strtotime($note->date))}})</strong></div>

        @include('o_s_parts/note_info')

        @include('o_s_parts/despesas')

        @include('o_s_parts/tec_note')

        @include('o_s_parts/client_sign')

        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
@endsection
