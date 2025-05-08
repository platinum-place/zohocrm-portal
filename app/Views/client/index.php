<?= $this->extend('components/templates/admin') ?>

<?= $this->section('content') ?>
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mt-4">
            <h1>Clientes</h1>
<!--            <a href="--><?php //= site_url('admin/clients/create') ?><!--" class="btn btn-success">-->
<!--                <i class="fas fa-user-plus"></i> Crear Cliente-->
<!--            </a>-->
        </div>
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
                Lista de Clientes
            </div>
            <div class="card-body">
                <table id="datatablesSimple" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Client ID</th>
                        <th>Usuario</th>
                        <th>Client Secret</th>
                        <th>Redirect URI</th>
                        <th>Grant Types</th>
                        <th>Scope</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (isset($clients) && is_array($clients)) : ?>
                        <?php foreach ($clients as $key => $client) : ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= esc($client['client_id']) ?></td>
                                <td><?= esc($client['user_id']) ?></td>
                                <td><?= esc($client['client_secret']) ?></td>
                                <td><?= esc($client['redirect_uri']) ?></td>
                                <td><?= esc($client['grant_types']) ?></td>
                                <td><?= esc($client['scope']) ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-primary btn-sm"
                                                onclick="editClient('<?= esc($client['client_id']) ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        |
                                        <form action="<?= site_url('admin/clients/delete/' . esc($client['client_id'])) ?>"
                                              method="post" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('¿Está seguro de que desea eliminar este cliente?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="8" class="text-center">No se encontraron clientes</td>
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
        function editClient(clientId) {
            window.location.href = '<?= site_url('admin/clients/edit/') ?>' + clientId;
        }

        document.addEventListener('DOMContentLoaded', function () {
            const datatablesSimple = document.getElementById('datatablesSimple');
            if (datatablesSimple) {
                new simpleDatatables.DataTable(datatablesSimple);
            }
        });
    </script>
<?= $this->endSection() ?>