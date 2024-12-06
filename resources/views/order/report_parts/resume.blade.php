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

    <x-resume :orders="session('order_client_ids')" :page="session('page')"></x-resume>

@endsection