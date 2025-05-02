<?php
/** @var array $client */
/** @var array $users */
?>
<?= $this->extend('components/templates/admin') ?>

<?= $this->section('content') ?>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Editar Cliente</h1>
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-user-edit me-1"></i>
                Editar detalles del cliente
            </div>

            <div class="card-body">
                <form action="<?= site_url('admin/clients/update/' . $client['client_id']) ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="PUT">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role" class="form-label">Rol</label>
                                <select class="form-select" id="user_id" name="user_id">
                                    <option value="">Seleccione un usuario</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= $user['username'] ?>" <?= isset($client['user_id']) && $client['user_id'] === $user['username'] ? 'selected' : '' ?>>
                                            <?= esc($user['username']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <a href="<?= site_url('admin/clients') ?>" class="btn btn-secondary me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>