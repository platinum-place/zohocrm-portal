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

                <?php if (session('errors')) : ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach (session('errors') as $error) : ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= site_url('admin/users/update/' . $user['username']) ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="PUT">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">Nombre de usuario</label>
                                <input type="text"
                                       class="form-control <?= session('errors.username') ? 'is-invalid' : '' ?>"
                                       id="username"
                                       name="username"
                                       value="<?= old('username', $user['username']) ?>" required>
                                <?php if (session('errors.username')) : ?>
                                    <div class="invalid-feedback">
                                        <?= esc(session('errors.username')) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="first_name" class="form-label">Primer Nombre</label>
                                <input type="text"
                                       class="form-control <?= session('errors.first_name') ? 'is-invalid' : '' ?>"
                                       id="first_name"
                                       name="first_name"
                                       value="<?= old('first_name', $user['first_name']) ?>" required>
                                <?php if (session('errors.first_name')) : ?>
                                    <div class="invalid-feedback">
                                        <?= esc(session('errors.first_name')) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="role_id" class="form-label">Rol</label>
                                <select class="form-select <?= session('errors.role_id') ? 'is-invalid' : '' ?>"
                                        id="role_id"
                                        name="role_id" required>
                                    <option value="">Seleccione un rol</option>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?= $role['id'] ?>"
                                            <?= old('role_id', $user['role_id'] ?? '') == $role['id'] ? 'selected' : '' ?>>
                                            <?= esc($role['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (session('errors.role_id')) : ?>
                                    <div class="invalid-feedback">
                                        <?= esc(session('errors.role_id')) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email"
                                       class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>"
                                       id="email"
                                       name="email"
                                       value="<?= old('email', $user['email']) ?>" required>
                                <?php if (session('errors.email')) : ?>
                                    <div class="invalid-feedback">
                                        <?= esc(session('errors.email')) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="last_name" class="form-label">Apellido</label>
                                <input type="text"
                                       class="form-control <?= session('errors.last_name') ? 'is-invalid' : '' ?>"
                                       id="last_name"
                                       name="last_name"
                                       value="<?= old('last_name', $user['last_name']) ?>" required>
                                <?php if (session('errors.last_name')) : ?>
                                    <div class="invalid-feedback">
                                        <?= esc(session('errors.last_name')) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="scope" class="form-label">Scope</label>
                            <textarea class="form-control <?= session('errors.scope') ? 'is-invalid' : '' ?>"
                                      id="scope"
                                      name="scope" rows="3"
                                      placeholder="Define el alcance, separado por espacios"><?= old('scope', esc($user['scope'])) ?></textarea>
                            <?php if (session('errors.scope')) : ?>
                                <div class="invalid-feedback"><?= esc(session('errors.scope')) ?></div>
                            <?php endif; ?>
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