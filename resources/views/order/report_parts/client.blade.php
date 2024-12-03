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

        <x-a4-centered :tit="$orders->first()->client->name" tex="Atendimentos: " :nords="count($orders)" tit_size="26"></x-a4-centered>

        <div class="page-number">página {{session('page')}}</div>
        @php session()->put('page', session('page') + 1); @endphp

        <div class="page-break"></div>

        @foreach ($orders as $order)
            @php
                // Null values will be replaced by - - : - - and the time will be formatted without seconds
                $order->notes_time_format();
                $orders = $orders->sortBy('date');
            @endphp

            @foreach ($order->notes as $note)
                @include('order/o_s_dompdf_parts/header_dompdf')
    
                @include('order/o_s_dompdf_parts/client_info_dompdf')
    
                <div>
                    <strong>
                        Intervenção {{ $loop->iteration }} de {{ count($order->notes) }} (data {{date('d/m/Y',strtotime($note->date))}})
                    </strong>
                </div>
    
                @include('order/o_s_dompdf_parts/note_info_dompdf')
    
                {{-- @include('order/o_s_dompdf_parts/despesas') --}}
    
                @include('order/o_s_dompdf_parts/tec_note_dompdf')
    
                @include('order/o_s_dompdf_parts/client_sign_dompdf')
    
                @if (!$loop->last)
                    <div class="page-number">página {{session('page')}}</div>
                    @php session()->put('page', session('page') + 1); @endphp

                    <div class="page-break"></div>
                @endif
            @endforeach
            @if (!$loop->last)
                <div class="page-number">página {{session('page')}}</div>
                @php session()->put('page', session('page') + 1); @endphp

                <div class="page-break"></div>
            @endif
        @endforeach
    <div class="page-number">página {{session('page')}}</div>
    @php session()->put('page', session('page') + 1); @endphp
@endsection
