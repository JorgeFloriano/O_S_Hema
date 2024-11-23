<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="{{asset('favicon.ico')}}">
        <style>
            :root {
            --blue1: #e2eaee;
            }

            * {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            padding: 0px;
            margin: 0px;
            box-sizing: border-box;
            }

            body {
            min-height: auto;
            padding: 10px;
            margin: auto;
            }

            div#header {
            margin: 15px 0px;
            height: 108px;
            }

            div#header1 {
            padding-top: 20px;
            float: left;
            width: 70px;
            }

            div#header2 {
            float: left;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            width: 302px;
            }

            div#header3 {
            padding: 20px;
            float: right;
            font-size: 17px;
            font-weight: bold;
            }

            div#osId {
            text-align: center;
            font-size: 25px;
            color: rgb(211, 21, 21);
            }

            div.Info {
            margin: 5px 0px;
            font-size: 15px;
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
            overflow-wrap: break-word;
            border-radius:6px 6px 0px 0px;
            border-top: 1px solid black;
            border-left: 1px solid black;
            border-right: 1px solid black;
            }

            .InfoLine {
            height: 22px;
            width: 100%;
            overflow-wrap: break-word;
            border-top: 1px solid black;
            border-left: 1px solid black;
            border-right: 1px solid black;
            }

            .LastInfoLine {
            height: 23px;
            width: 100%;
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
            font-size: 15px;
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
        </style>
        <title>Sistema de Gerenciamento Hema</title>
    </head>

    <body>
        <section id="print">
            @yield('content')
        </section>
    </body>
</html>