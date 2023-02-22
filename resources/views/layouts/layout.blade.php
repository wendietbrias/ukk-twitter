<!DOCTYPE html>
<html lang="en">
    <head>
        <title>@isset($title) {{ $title }} @endisset</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        @vite('resources/css/app.css')

        @yield("links")
    </head>
    <body>
        @yield('content')

        @yield("js")
    </body>
</html>