<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">

        <meta name="application-name" content="{{ config('app.name') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }}</title>

        <style>
            .text-danger-500 {
                --tw-text-opacity:1;
                color: #d70b0b;
            }

            [x-cloak=''],
            [x-cloak='x-cloak'],
            [x-cloak='1'] {
                display: none !important;
            }

            @media (max-width: 1023px) {
                [x-cloak='-lg'] {
                    display: none !important;
                }
            }

            @media (min-width: 1024px) {
                [x-cloak='lg'] {
                    display: none !important;
                }
            }
        </style>
        @filamentStyles
        @vite('resources/css/app.css', 'vendor/web-installer/build')
        @vite('resources/js/app.js', 'vendor/web-installer/build')
    </head>

    <body  class="min-h-screen overscroll-y-none bg-gray-50 font-normal text-gray-950 antialiased dark:bg-gray-950 dark:text-white">
        {{ $slot }}

        @filamentScripts(withCore: true)

        @stack('scripts')
    </body>
</html>
