<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="shortcut icon" href="{{asset('favicon.ico')}}">
        <style>
            :root {
                --blue1: #e2eaee;
            }

            * {
                font-family: DejaVu Sans, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
                padding: 0px;
                margin: 0px;
                box-sizing: border-box;
            }

            body {
                min-height: auto;
                padding: 15px;
                margin: auto;
            }

            .header {
            margin: 15px 0px;
            height: 108px;
            width: 100%;
            display: flex;
            flex-direction: row;
            }

            .header1 {
                padding-top: 20px;
                float: left;
                width: 70px;
            }

            .header2 {
                float: left;
                text-align: center;
                font-weight: bold;
                font-size: 15px;
                width: 302px;
            }

            div#header3 {
                float: right;
                height: 80px; /* Você PRECISA definir uma altura fixa aqui */
                width: 400px; /* Defina uma largura adequada */
                position: relative; /* Referência para o conteúdo interno */
            }

            .header-content-wrapper {
                position: absolute;
                top: 60%; /* Joga o topo do elemento no meio da div pai */
                left: 0;
                right: 0;
                /* O segredo do dompdf: transformar o elemento para compensar a própria altura */
                /* Como o dompdf às vezes ignora 'transform', usamos uma margem negativa se a altura for conhecida */
                /* Mas tentaremos o transform primeiro: */
                transform: translateY(-50%); 
                
                text-align: center;
                font-size: 17px;
                font-weight: bold;
            }

            #osId {
                text-align: center;
                font-size: 25px;
                color: rgb(211, 21, 21);
            }

            #emergency {
                font-size: 14px;
                color: rgb(211, 21, 21);
                margin: 0;
            }

            div.Info {
                margin: 5px 0px;
                font-size: 13px;
            }

            .center {
                text-align: center;
                float: none;
                width: 33.3%;
                padding: 0px;
            }

            .FirstInfoLine {
                height: 22px;
                width: 100%;
                overflow: hidden;
                overflow-wrap: break-word;
                border-radius:6px 6px 0px 0px;
                border-top: 1px solid black;
                border-left: 1px solid black;
                border-right: 1px solid black;
            }

            .InfoLine {
                height: 22px;
                width: 100%;
                overflow: hidden;
                overflow-wrap: break-word;
                border-top: 1px solid black;
                border-left: 1px solid black;
                border-right: 1px solid black;
            }

            .LastInfoLine {
                height: 23px;
                width: 100%;
                overflow: hidden;
                overflow-wrap: break-word;
                border: 1px solid black;
                border-radius:0px 0px 6px 6px;
            }

            .InfoFirstCollum {
                overflow: hidden;
                padding: 0 6px 0 6px;
                float: left;
                height: 22px;
                width: 100%;
            }

            .InfoTitle {
                overflow: hidden;
                padding: 0 6px 0 6px;
                text-align: center;
                height: 22px;
            }

            .InfoCollum {
                overflow: hidden;
                padding: 0 6px 0 6px;
                float: left;
                height: 22px;
                border-left: 1px solid black;
            }

            .ThirdCollum {
                text-align: center;
                overflow: hidden;
                float: left;
                padding: 0px;
                height: 22px;
                width: 33.3%;
            }

            .QuarterCollum {
                text-align: center;
                overflow: hidden;
                float: left;
                padding: 0px;
                height: 22px;
                width: 25%;
            }

            .b-l {
                border-left: 1px solid black;
            }

            .LongText {
                overflow: hidden;
                height: 100%;
                width: 99%;
            }

            .bg-t {
                background-color: var(--blue1);
            }

            table {
                font-size: 13px;
                width: 100%;
                overflow:hidden;
                border-collapse:collapse;
                -webkit-border-radius: 6px;
                    -moz-border-radius: 6px;
                        border-radius: 6px;
            }

            .w3-striped tbody tr:nth-child(even){ 
                background-color:#ececf8
                }

            table.z tr:nth-child(odd) {
                background-color:#fff
            }

            table.z tr:nth-child(even){
                background-color:#f0f0f3b2
            }

            .w3-hoverable tbody tr:hover,.w3-ul.w3-hoverable li:hover {
                background-color:#ccc
            }

            .w3-centered tr th,.w3-centered tr td, thead, th {
                text-align:center
            }

            table th {
                overflow: hidden;
                background-color: var(--blue1);
                border-right:1px solid black;
                border-top: none;
                border-bottom:1px solid black;

            }
            table td {
                overflow: hidden;
                border-right:1px solid black;
                padding-left: 5px;
            }

            tr {
                border-bottom:1px solid black;
            }

            .b-t {
                overflow: hidden;
                border:1px solid black !important;
                border-radius: 6px;
                margin: 15px 0px 0px;
            }

            .b-half-table {
                overflow: hidden;
                border:1px solid black !important;
                border-radius: 6px 6px 0px 0px;
                margin-bottom: -1px;
            }

            .page-break {
                page-break-after: always;
            }

            .resume-H1 {
                float: left;
                text-align: center;
                font-weight: bold;
                width: 600px;
                padding-top: 40px;
                font-size: 20px;
            }

            .resume-line {
                font-size: 18px;
                height: 23px;
                width: 600px;
                margin: auto;
                padding-top: 4px;
                overflow-wrap: break-word;
                border: hidden;
                border-bottom: 1.5px solid black;
            }

            .resume-c1 {
                width: 50%;
                overflow: hidden;
                float: left;
                height: 23px;
                width: 100%;
            }

            .resume-c2 {
                overflow: hidden;
                float: left;
                height: 23px;
            }

            .page-number {
                position: absolute;
                bottom: 10;
                right: 10;
                font-size: 16px;
                color: #3b3939;
            }
            
            /* Container da grade */
            .photo-grid {
                width: 100%;
                display: block;
                clear: both;
            }

            /* Cada item da grade */
            .photo-item {
                width: 33%; 
                height: 330px; /* Altura total do quadrado */
                border: 0.5px solid #ccc;
                float: left; /* Garante o alinhamento horizontal */
                box-sizing: border-box;
                position: relative;
            }

            /* Centralização Vertical e Horizontal (Tabela de apoio) */
            .image-wrapper {
                display: table; /* Simula uma tabela para centralizar o conteúdo */
                width: 100%;
                height: 300px; /* Espaço reservado para a imagem (descontando o subtítulo) */
            }

            .image-cell {
                display: table-cell;
                vertical-align: middle; /* Centraliza verticalmente */
                text-align: center;    /* Centraliza horizontalmente */
                width: 100%;
                height: 300px;
            }

            .image-cell img {
                max-width: 90%;
                max-height: 280px; /* Limite para não sobrepor o texto */
                display: inline-block;
            }

            /* Subtítulo na base */
            .file-subtitle {
                position: absolute;
                bottom: 5px;
                left: 0;
                width: 100%;
                text-align: center;
                font-size: 11px;
                color: #666;
                height: 30px;
                line-height: 1.2;
                word-wrap: break-word;
            }

            /* Limpeza do float para não quebrar o restante do PDF */
            .clearfix::after {
                content: "";
                display: table;
                clear: both;
            }
        </style>
        <title>Sistema de Gerenciamento Hema</title>
    </head>

    <body>
        <section id="print">
            @yield('content')
        </section>
    </body>
</html>