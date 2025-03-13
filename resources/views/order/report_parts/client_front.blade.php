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
    
    <div style="margin-top: 30px">
        <img src="assets/img/{{env('LOGO2')}}" width="10%" alt="logo hema">
    </div>

    <x-a4-centered :tit="$client_name" tex="Atendimentos: " :nords="$count_orders" tit_size="26"></x-a4-centered>

    <div class="page-number">página {{session('page')}}</div>
    @php session()->put('page', session('page') + 1); @endphp

@endsection