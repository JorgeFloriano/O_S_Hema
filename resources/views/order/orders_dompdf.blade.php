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

    <x-a4-centered :tit="$title" :tex="date('d/m/Y')" tit_size="26"></x-a4-centered>

    <div class="page-break"></div>

    @php
        $ordersByClient = $orders->groupBy('client_id');
        $page = 1;
    @endphp

    <div class="page-number">página {{$page}}</div>
    @php $page++; @endphp

    <div class="page-break"></div>
    
    <x-resume :orders="$ordersByClient" :page="$page"></x-resume>
@endsection
