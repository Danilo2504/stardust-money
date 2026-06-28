<section>
    <p class="small text-gray-600 mb-4">
        Una vez eliminada tu cuenta, todos sus recursos y datos se borrarán permanentemente. Antes de eliminarla, descarga cualquier dato que quieras conservar.
    </p>

    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
        Eliminar cuenta
    </button>

    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmUserDeletionModalLabel">¿Eliminar tu cuenta?</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        <p class="small text-gray-600">
                            Una vez eliminada tu cuenta, todos sus recursos y datos se borrarán permanentemente. Ingresa tu contraseña para confirmar.
                        </p>

                        <div class="mb-3">
                            <label for="delete-password" class="form-label visually-hidden">Contraseña</label>
                            <input type="password"
                                   id="delete-password"
                                   name="password"
                                   class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                                   placeholder="Contraseña">
                            @error('password', 'userDeletion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar cuenta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
