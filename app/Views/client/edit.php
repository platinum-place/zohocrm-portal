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
                <?php if (session('errors')) : ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach (session('errors') as $error) : ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= site_url('admin/clients/update/' . $client['client_id']) ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="PUT">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="client_id" class="form-label">Client ID</label>
                                <input type="text" class="form-control <?= session('errors.client_id') ? 'is-invalid' : '' ?>" id="client_id" name="client_id"
                                       value="<?= esc($client['client_id']) ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="client_secret" class="form-label">Client Secret</label>
                                <input type="text" class="form-control <?= session('errors.client_secret') ? 'is-invalid' : '' ?>" id="client_secret" name="client_secret"
                                       value="<?= old('client_secret', esc($client['client_secret'])) ?>">
                                <?php if (session('errors.client_secret')) : ?>
                                    <div class="invalid-feedback"><?= esc(session('errors.client_secret')) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="redirect_uri" class="form-label">Redirect URI</label>
                                <input type="url" class="form-control <?= session('errors.redirect_uri') ? 'is-invalid' : '' ?>" id="redirect_uri" name="redirect_uri"
                                       value="<?= old('redirect_uri', esc($client['redirect_uri'])) ?>"
                                       placeholder="https://example.com/callback">
                                <?php if (session('errors.redirect_uri')) : ?>
                                    <div class="invalid-feedback"><?= esc(session('errors.redirect_uri')) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="grant_types" class="form-label">Grant Types</label>
                                <input type="text" class="form-control <?= session('errors.grant_types') ? 'is-invalid' : '' ?>" id="grant_types" name="grant_types"
                                       value="<?= old('grant_types', esc($client['grant_types'])) ?>"
                                       placeholder="e.g., authorization_code, client_credentials">
                                <?php if (session('errors.grant_types')) : ?>
                                    <div class="invalid-feedback"><?= esc(session('errors.grant_types')) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="scope" class="form-label">Scope</label>
                                <textarea class="form-control <?= session('errors.scope') ? 'is-invalid' : '' ?>" id="scope" name="scope" rows="3"
                                          placeholder="Define el alcance, separado por espacios"><?= old('scope', esc($client['scope'])) ?></textarea>
                                <?php if (session('errors.scope')) : ?>
                                    <div class="invalid-feedback"><?= esc(session('errors.scope')) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="user_id" class="form-label">Usuario</label>
                                <select class="form-select <?= session('errors.user_id') ? 'is-invalid' : '' ?>" id="user_id" name="user_id">
                                    <option value="">Seleccione un usuario</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= $user['username'] ?>"
                                            <?= old('user_id', $client['user_id'] ?? '') === $user['username'] ? 'selected' : '' ?>>
                                            <?= esc($user['username']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (session('errors.user_id')) : ?>
                                    <div class="invalid-feedback"><?= esc(session('errors.user_id')) ?></div>
                                <?php endif; ?>
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