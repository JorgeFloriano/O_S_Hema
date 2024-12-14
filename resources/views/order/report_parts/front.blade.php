@extends('layouts.front_page_layout')

@section('content')

    <style media="print">
        #buttonGroup {
            display: none;
        }
        @media print {
        .clientSign {page-break-after: always;}
        }
    </style>

    <div class="logo">
        <img src="assets/img/logo_hema.png" width="60%" alt="logo hema">
    </div>

    <x-a4-centered :tit="$title" :tex="date('d/m/Y')" tit_size="26"></x-a4-centered>

@endsection