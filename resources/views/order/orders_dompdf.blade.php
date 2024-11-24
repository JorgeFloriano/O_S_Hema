@extends('layouts.orders_dompdf_layout')

@section('content')

    <style media="print">
        #buttonGroup {
            display: none;
        }
        @media print {
        .clientSign {page-break-after: always;}
        }
    </style>

    @foreach ($orders as $order)
        @foreach ($order->notes as $note)
            @include('order/o_s_dompdf_parts/header_dompdf')

            @include('order/o_s_dompdf_parts/client_info_dompdf')

            <div><strong>Informações do Atendimento (data {{date('d/m/Y',strtotime($note->date))}})</strong></div>

            @include('order/o_s_dompdf_parts/note_info_dompdf')

            {{-- @include('order/o_s_dompdf_parts/despesas') --}}

            @include('order/o_s_dompdf_parts/tec_note_dompdf')

            @include('order/o_s_dompdf_parts/client_sign_dompdf')

            @if (!$loop->last)
                <div class="page-break"></div>
            @endif
        @endforeach
        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
@endsection
