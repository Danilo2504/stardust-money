<x-guest-layout>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg my-5">
                <div class="card-body p-5">
                    <div class="text-center">
                        <h1 class="h4 text-gray-900 mb-4">Confirmar contraseña</h1>
                    </div>

                    <p class="small text-gray-600 mb-4">
                        Esta es un área segura de la aplicación. Por favor confirma tu contraseña antes de continuar.
                    </p>

                    <form method="POST" action="{{ route('password.confirm') }}" class="user">
                        @csrf

                        <div class="form-group">
                            <input type="password"
                                   class="form-control form-control-user @error('password') is-invalid @enderror"
                                   id="password"
                                   name="password"
                                   placeholder="Contraseña"
                                   required
                                   autocomplete="current-password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-user btn-block">
                            Confirmar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
