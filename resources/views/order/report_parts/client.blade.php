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

        @php
            // Null values will be replaced by - - : - - and the time will be formatted without seconds
            $order->notes_time_format();
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

            @include('order/o_s_dompdf_parts/tec_note_dompdf')

            @include('order/o_s_dompdf_parts/client_sign_dompdf')

            {{-- Verifica se houver ao menos uma imagem para exibir (PDFs nao serao exibidos agora) --}}
            @if($note->files->count() > 0 && $note->files->contains(fn($file) => !Str::endsWith(strtolower($file->path), '.pdf')))
                @if (session('page'))
                    <div class="page-number">página {{session('page')}}</div>
                    @php session()->put('page', session('page') + 1); @endphp
                @endif

                @include('order/o_s_dompdf_parts/note_files_dompdf')
            @endif

            @if (!$loop->last)
                @if (session('page'))
                    <div class="page-number">página {{session('page')}}</div>
                    @php session()->put('page', session('page') + 1); @endphp
                @endif

                <div class="page-break"></div>
            @endif
        @endforeach
    @if (session('page'))
        <div class="page-number">página {{session('page')}}</div>
        @php session()->put('page', session('page') + 1); @endphp
    @endif
@endsection
