@extends('app')

@section('body')
    <div class="min-h-screen flex flex-col">

        @include('components.layout.header')
        @include('components.layout.side')

        <main class="flex-1">
            @yield('content')
        </main>

        @include('components.layout.footer')
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('sidebar-overlay').classList.toggle('active');
        }
    </script>
@endsection