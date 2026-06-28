<section>
    <p class="small text-gray-600 mb-4">
        Asegúrate de usar una contraseña larga y aleatoria para mantener tu cuenta segura.
    </p>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label">Contraseña actual</label>
            <input type="password"
                   id="update_password_current_password"
                   name="current_password"
                   class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                   autocomplete="current-password">
            @error('current_password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label">Nueva contraseña</label>
            <input type="password"
                   id="update_password_password"
                   name="password"
                   class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                   autocomplete="new-password">
            @error('password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label">Confirmar contraseña</label>
            <input type="password"
                   id="update_password_password_confirmation"
                   name="password_confirmation"
                   class="form-control"
                   autocomplete="new-password">
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">Guardar</button>

            @if (session('status') === 'password-updated')
                <span class="text-success small">Guardado.</span>
            @endif
        </div>
    </form>
</section>
