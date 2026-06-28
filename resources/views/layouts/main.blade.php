@extends('app')

@section('body')
    <div id="wrapper">
        @include('components.layout.side')

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                @include('components.layout.header')

                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>

            @include('components.layout.footer')
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <div class="modal fade" id="datatableProcessingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mb-0 text-gray-600">Procesando...</p>
                </div>
            </div>
        </div>
    </div>
@endsection
