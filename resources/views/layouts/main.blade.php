@extends('app')

@section('body')
    <div class="min-h-screen flex flex-col">

        @include('components.layout.header')

        <main class="flex-1">
            @yield('content')
        </main>

        @include('components.layout.footer')
    </div>
@endsection