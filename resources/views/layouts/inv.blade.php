<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Workflow Management System') }}</title>

    <style>
        body, html {
            background: #fff;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #222;
        }
        .invoice-main {
            max-width: 900px;
            margin: 24px auto;
            padding: 24px;
            background: #fff;
        }
        h2, h5 {
            margin-bottom: 0.5em;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5em;
        }
        th, td {
            border: 1px solid #bbb;
            padding: 8px 10px;
        }
        th {
            background: #f8f9fa;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .no-border {
            border: none !important;
        }
        .no-print {
            display: block;
        }
        .invoice-btn {
            background: #e3f0fb;
            color: #155fa0;
            border: 1px solid #b6d4ef;
            padding: 12px 28px;
            font-size: 1.1em;
            border-radius: 6px;
            margin-right: 10px;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
            font-weight: 600;
        }
        .invoice-btn:hover, .invoice-btn:focus {
            background: #1976d2;
            color: #fff;
            border-color: #1976d2;
            outline: none;
        }
        @media print {
            html, body {
                background: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
            .invoice-main {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
                page-break-inside: avoid !important;
            }
            .no-print, .btn, form, nav, .alert {
                display: none !important;
            }
            @page {
                size: A4;
                margin: 16mm 12mm 16mm 12mm;
            }
        }
    </style>
    @livewireStyles
</head>
<body>
    <main class="invoice-main">
        @yield('content')
    </main>
    @livewireScripts
</body>
</html>