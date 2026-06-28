<x-guest-layout>
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12 col-md-9">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-lg-6 d-none d-lg-block bg-password-image"></div>
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">¿Olvidaste tu contraseña?</h1>
                                    <p class="small text-gray-600 mb-4">
                                        No hay problema. Indícanos tu correo electrónico y te enviaremos un enlace para restablecerla.
                                    </p>
                                </div>

                                @if (session('status'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('status') }}
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('password.email') }}" class="user">
                                    @csrf

                                    <div class="form-group">
                                        <input type="email"
                                               class="form-control form-control-user @error('email') is-invalid @enderror"
                                               id="email"
                                               name="email"
                                               value="{{ old('email') }}"
                                               placeholder="Correo electrónico"
                                               required
                                               autofocus>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        Enviar enlace
                                    </button>
                                </form>

                                <hr>

                                <div class="text-center">
                                    <a class="small" href="{{ route('login') }}">Volver al login</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
