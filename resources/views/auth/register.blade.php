<x-guest-layout>
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12 col-md-9">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-lg-6 d-none d-lg-block bg-register-image"></div>
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">Crear cuenta</h1>
                                </div>

                                <form method="POST" action="{{ route('register') }}" class="user">
                                    @csrf

                                    <div class="form-group">
                                        <input type="text"
                                               class="form-control form-control-user @error('name') is-invalid @enderror"
                                               id="name"
                                               name="name"
                                               value="{{ old('name') }}"
                                               placeholder="Nombre"
                                               required
                                               autofocus
                                               autocomplete="name">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <input type="email"
                                               class="form-control form-control-user @error('email') is-invalid @enderror"
                                               id="email"
                                               name="email"
                                               value="{{ old('email') }}"
                                               placeholder="Correo electrónico"
                                               required
                                               autocomplete="username">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <input type="password"
                                               class="form-control form-control-user @error('password') is-invalid @enderror"
                                               id="password"
                                               name="password"
                                               placeholder="Contraseña"
                                               required
                                               autocomplete="new-password">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <input type="password"
                                               class="form-control form-control-user"
                                               id="password_confirmation"
                                               name="password_confirmation"
                                               placeholder="Confirmar contraseña"
                                               required
                                               autocomplete="new-password">
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        Registrarse
                                    </button>
                                </form>

                                <hr>

                                <div class="text-center">
                                    <a class="small" href="{{ route('login') }}">¿Ya tienes cuenta? Inicia sesión</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
