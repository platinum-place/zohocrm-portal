<?= $this->extend('components/templates/main') ?>

<?= $this->section('content') ?>

    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <?php if (session('error')) : ?>
                                <div class="alert alert-danger">
                                    <?= session('error') ?>
                                </div>
                            <?php endif ?>

                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header d-flex justify-content-center">
                                    <img src="<?= base_url('images/logo.png') ?>" alt="Logo IT" width="250"
                                         height="250">
                                </div>

                                <div class="card-body">
                                    <?= form_open('admin/login',['method' => 'post']) ?>
                                    <?= csrf_field() ?>
                                    <div class="form-floating mb-3">
                                        <input class="form-control <?= session('errors.username') ? 'is-invalid' : '' ?>"
                                               id="inputEmail"
                                               type="text"
                                               name="username"
                                               value="<?= old('username') ?>"/>
                                        <label for="inputEmail">Usuario</label>
                                        <?php if (session('errors.username')) : ?>
                                            <div class="invalid-feedback">
                                                <?= session('errors.username') ?>
                                            </div>
                                        <?php endif ?>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <input class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>"
                                               id="inputPassword"
                                               type="password"
                                               name="password"/>
                                        <label for="inputPassword">Contraseña</label>
                                        <?php if (session('errors.password')) : ?>
                                            <div class="invalid-feedback">
                                                <?= session('errors.password') ?>
                                            </div>
                                        <?php endif ?>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                        <button type="submit" class="btn btn-primary">Ingresar</button>
                                    </div>
                                    <?= form_close() ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <?= $this->include('components/footer') ?>
    </div>

<?= $this->endSection() ?>