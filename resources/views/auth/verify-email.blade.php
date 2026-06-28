<x-guest-layout>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg my-5">
                <div class="card-body p-5">
                    <div class="text-center">
                        <h1 class="h4 text-gray-900 mb-4">Verifica tu correo</h1>
                    </div>

                    <p class="small text-gray-600 mb-4">
                        Gracias por registrarte. Antes de comenzar, ¿podrías verificar tu correo electrónico haciendo clic en el enlace que te enviamos? Si no lo recibiste, con gusto te enviaremos otro.
                    </p>

                    @if (session('status') == 'verification-link-sent')
                        <div class="alert alert-success" role="alert">
                            Se ha enviado un nuevo enlace de verificación al correo que proporcionaste.
                        </div>
                    @endif

                    <div class="d-flex align-items-center justify-content-between mt-4">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                Reenviar correo
                            </button>
                        </form>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-link text-gray-600 small">
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
