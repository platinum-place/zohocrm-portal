<?= $this->extend('components/templates/admin') ?>

<?= $this->section('content') ?>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Crear Usuario</h1>
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-user-plus me-1"></i>
                Registrar un nuevo usuario
            </div>

            <div class="card-body">
                <?php if (session('error')) : ?>
                    <div class="alert alert-danger">
                        <?= session('error') ?>
                    </div>
                <?php endif ?>

                <?= form_open('admin/users/store', ['method' => 'post']) ?>
                <?= csrf_field() ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="username" class="form-label">Nombre de usuario *</label>
                            <input type="text"
                                   class="form-control <?= session('errors.username') ? 'is-invalid' : '' ?>"
                                   id="username"
                                   name="username"
                                   value="<?= set_value('username') ?>"
                            >
                            <?php if (session('errors.username')) : ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.username') ?>
                                </div>
                            <?php endif ?>
                        </div>

                        <div class="mb-3">
                            <label for="first_name" class="form-label">Primer Nombre *</label>
                            <input type="text"
                                   class="form-control <?= session('errors.first_name') ? 'is-invalid' : '' ?>"
                                   id="first_name"
                                   name="first_name"
                                   value="<?= set_value('first_name') ?>"
                            >
                            <?php if (session('errors.first_name')) : ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.first_name') ?>
                                </div>
                            <?php endif ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico *</label>
                            <input type="email"
                                   class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>"
                                   id="email"
                                   name="email"
                                   value="<?= set_value('email') ?>"
                            >
                            <?php if (session('errors.email')) : ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.email') ?>
                                </div>
                            <?php endif ?>
                        </div>

                        <div class="mb-3">
                            <label for="last_name" class="form-label">Apellido *</label>
                            <input type="text"
                                   class="form-control <?= session('errors.last_name') ? 'is-invalid' : '' ?>"
                                   id="last_name"
                                   name="last_name"
                                   value="<?= set_value('last_name') ?>"
                            >
                            <?php if (session('errors.last_name')) : ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.last_name') ?>
                                </div>
                            <?php endif ?>
                        </div>
                    </div>

                    <!-- Nuevo campo de contraseña -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña *</label>
                            <input type="password"
                                   class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>"
                                   id="password"
                                   name="password"
                            >
                            <?php if (session('errors.password')) : ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.password') ?>
                                </div>
                            <?php endif ?>
                        </div>
                    </div>

                </div>

                <div class="d-flex justify-content-end mt-3">
                    <a href="<?= site_url('admin/users') ?>" class="btn btn-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-success">Crear Usuario</button>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>