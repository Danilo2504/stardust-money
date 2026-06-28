<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Stardust Money'))</title>

    @include('base.head')
    @stack('styles')
</head>
<body id="page-top">
    @yield('body')

    @include('base.scripts')
    @stack('scripts')
</body>
</html>
