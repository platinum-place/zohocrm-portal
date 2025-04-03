<form method="POST" action="<?= site_url("cotizaciones") ?>">
    <input type="text" hidden value="Seguro Incendio Leasing" name="plan">

    <div class="modal fade" id="cotizar_incendio_leasing" tabindex="-1" aria-labelledby="cotizar_incendio_leasing" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cotizar_incendio_leasing">Cotizar Seguro Incendio Leasing</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="mb-3 mb-md-0">
                                <label class="form-label">Valor de la Propiedad</label>
                                <input type="number" class="form-control" name="suma" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3 mb-md-0">
                                <label class="form-label">Valor del Préstamo</label>
                                <input type="number" class="form-control" name="prestamo" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3 mb-md-0">
                                <label class="form-label">Plazo</label>
                                <input type="number" class="form-control" name="plazo" required>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3 mb-md-0">
                                <label class="form-label">Tipo de Equipo</label>
                                <select class="form-select" name="tipo_equipo">
                                <option value="Maquinaria">Maquinaria</option>
                                <option value="Equipos varios">Equipos varios</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" class="form-control" name="direccion" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Cotizar</button>
                </div>
            </div>
        </div>
    </div>
</form>