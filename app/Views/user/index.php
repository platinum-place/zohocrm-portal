<?= $this->extend('components/templates/admin') ?>

<?= $this->section('content') ?>
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mt-4">
            <h1>Usuarios</h1>
            <a href="<?= site_url('admin/users/create') ?>" class="btn btn-success">
                <i class="fas fa-user-plus"></i> Crear Usuario
            </a>
        </div>
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
                Lista de Usuarios
            </div>
            <div class="card-body">
                <table id="datatablesSimple" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Correo Electrónico</th>
                        <th>Nombre de usuario</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (isset($users) && is_array($users)) : ?>
                        <?php foreach ($users as $key => $user) : ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= esc($user['first_name']) . ' ' . esc($user['last_name']) ?></td>
                                <td><?= esc($user['email']) ?></td>
                                <td><?= esc($user['username']) ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-primary btn-sm"
                                                onclick="editUser('<?= esc($user['username']) ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        |
                                        <form action="<?= site_url('admin/users/delete/' . esc($user['username'])) ?>" method="post" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <?= form_hidden('_method', 'delete') ?>
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de que desea eliminar este usuario?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        |
                                        <button type="button" class="btn btn-warning btn-sm"
                                                onclick="resetPassword('<?= esc($user['username']) ?>')">
                                            <i class="fas fa-key"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6" class="text-center">No se encontraron usuarios</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
    <script>
        function editUser(id) {
            window.location.href = '<?= site_url('admin/users/edit/') ?>' + id;
        }

        function resetPassword(username) {
            if (confirm('¿Está seguro de que desea resetear la contraseña de este usuario?')) {
                window.location.href = '<?= site_url('admin/users/reset-password/') ?>' + username;
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const datatablesSimple = document.getElementById('datatablesSimple');
            if (datatablesSimple) {
                new simpleDatatables.DataTable(datatablesSimple);
            }
        });
    </script>
<?= $this->endSection() ?>