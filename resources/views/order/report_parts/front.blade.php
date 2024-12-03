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

@endsection