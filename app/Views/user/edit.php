<?php
/** @var array $user */
/** @var array $roles */
?>
<?= $this->extend('components/templates/admin') ?>

<?= $this->section('content') ?>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Editar Usuario</h1>
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-user-edit me-1"></i>
                Editar detalles del usuario
            </div>

            <div class="card-body">
                <form action="<?= site_url('admin/users/update/' . $user['username']) ?>" method="post">
                    <?= csrf_field() ?>
                    <?= form_hidden('_method', 'put') ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">Nombre de usuariop</label>
                                <input type="text" class="form-control" id="username" name="username"
                                       value="<?= set_value('username', $user['username']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="first_name" class="form-label">Primer Nombre</label>
                                <input type="text" class="form-control" id="first_name" name="first_name"
                                       value="<?= set_value('first_name', $user['first_name']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">Rol</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">Seleccione un rol</option>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?= $role['id'] ?>" <?= isset($user['role_id']) && $user['role_id'] === $role['id'] ? 'selected' : '' ?>>
                                            <?= esc($role['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?= set_value('email', $user['email']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="last_name" class="form-label">Apellido</label>
                                <input type="text" class="form-control" id="last_name" name="last_name"
                                       value="<?= set_value('last_name', $user['last_name']) ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <a href="<?= site_url('admin/users') ?>" class="btn btn-secondary me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>