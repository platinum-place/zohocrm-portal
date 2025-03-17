<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="card mb-4">
    <div class="card-body">
        <table id="datatablesSimple">
            <thead>
                <tr>
                    <th>Vigencia Desde</th>
                    <th>Cliente</th>
                    <th>RNC/Cédula</th>
                    <th>Plan</th>
                    <th>Referidor</th>
                    <th>Pendiente</th>
                    <th>Opciones</th>
                </tr>
            </thead>

            <tfoot>
                <tr>
                    <th>Vigencia Desde</th>
                    <th>Cliente</th>
                    <th>RNC/Cédula</th>
                    <th>Plan</th>
                    <th>Referidor</th>
                    <th>Pendiente</th>
                    <th>Opciones</th>
                </tr>
            </tfoot>
            <tbody>
                <?php foreach ((array)$cotizaciones as $cotizacion) : ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($cotizacion->getCreatedTime())) ?></td>
                        <td>
                            <?= $cotizacion->getFieldValue('Nombre') . ' buscar_emisiones.php' . $cotizacion->getFieldValue('Apellido') ?>
                        </td>
                        <td><?= $cotizacion->getFieldValue('RNC_C_dula') ?></td>
                        <td><?= $cotizacion->getFieldValue('Plan') ?> </td>
                        <td><?= ($cotizacion->getFieldValue('Contact_Name')) ? $cotizacion->getFieldValue('Contact_Name')->getLookupLabel() : "" ?></td>
                        <td><?=  ($cotizacion->getFieldValue('Pendiente')) ? "Si" : "No" ; ?></td>
                        <td>
                            <a href="<?= site_url("cotizaciones/adjuntar/" . $cotizacion->getEntityId()) ?>" title="Adjuntar">
                                <i class="fas fa-upload"></i>
                            </a>
                            |
                            <a href="<?= site_url("cotizaciones/descargar/" . $cotizacion->getEntityId()) ?>" title="Descargar" target="__blank">
                                <i class="fas fa-download"></i>
                            </a>
                            |
                            <a href="#" title="Marcar" onclick="alerta_procesar('<?= $cotizacion->getEntityId() ?>')">
                                <i class="fas fa-check"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function alerta_procesar(id) {
        $.ajax({
            type: 'ajax',
            url: '<?= site_url("cotizaciones/marcar") ?>/' + id,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            method: "POST",
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: "Cambios aplicados",
                    text: response,
                });
            },
            error: function(data) {
                console.log(data);
                Swal.fire({
                    icon: 'error',
                    title: "Algo salio mal",
                });
            }
        });
    }
</script>


<?= $this->endSection() ?>