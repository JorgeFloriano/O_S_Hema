@extends('layouts.order_pdf_layout')

@section('content')

    <style media="print">
        #buttonGroup {
            display: none;
        }
    </style>

    @include('o_s_parts/header')

    @include('o_s_parts/client_info')

    @include('o_s_parts/note_info')

@endsection
