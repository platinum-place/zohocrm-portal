<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<!-- Opciones para cotizar -->
<div class="card-group">
    <?php if (!empty($plan["Auto"])) : ?>
        <div class="card text-center">
            <div class="card-header">
                AUTO
            </div>
            <div class="card-body">
                <img src="<?= base_url('img/auto.png') ?>" height="200" width="150">
                <a class="stretched-link" href="#" data-bs-toggle="modal" data-bs-target="#cotizar_auto"></a>
            </div>
        </div>
    <?php endif ?>

    <?php if (!empty($plan["Vida"])) : ?>
        <div class="card text-center">
            <div class="card-header">
                VIDA
            </div>
            <div class="card-body">
                <img src="<?= base_url('img/vida.png') ?>" height="200" width="150">
                <a class="stretched-link" href="#" data-bs-toggle="modal" data-bs-target="#cotizar_vida"></a>
            </div>
        </div>
    <?php endif ?>

    <?php if (!empty($plan["Desempleo"])) : ?>
        <div class="card text-center">
            <div class="card-header">
                VIDA/DESEMPLEO
            </div>
            <div class="card-body">
                <img src="<?= base_url('img/desempleo.png') ?>" height="200" width="150">
                <a class="stretched-link" href="#" data-bs-toggle="modal" data-bs-target="#cotizar_desempleo"></a>
            </div>
        </div>
    <?php endif ?>

    <?php if (!empty($plan["Incendio"])) : ?>
        <div class="card text-center">
            <div class="card-header">
                SEGURO INCENDIO HIPOTECARIO
            </div>
            <div class="card-body">
                <img src="<?= base_url('img/incendio.png') ?>" height="200" width="150">
                <a class="stretched-link" href="#" data-bs-toggle="modal" data-bs-target="#cotizar_incendio"></a>
            </div>
        </div>
    <?php endif ?>

    <?php if (!empty($plan["Leasing"])) : ?>
        <div class="card text-center">
            <div class="card-header">
                SEGURO INCENDIO LEASING
            </div>
            <div class="card-body">
                <img src="<?= base_url('img/incendio 2.png') ?>" height="200" width="150">
                <a class="stretched-link" href="#" data-bs-toggle="modal" data-bs-target="#cotizar_incendio_leasing"></a>
            </div>
        </div>
    <?php endif ?>
</div>

<?= $this->endSection() ?>


<!-- Formulario para cotizar -->
<?= $this->section('modal') ?>
<?php if (!empty($cotizacion->planes)) : ?>
    <?= $this->include('modals/tabla_resultados') ?>
    <?= $this->include('modals/completar_cotizacion') ?>
<?php endif ?>

<?= $this->include('auto/cotizar') ?>
<?= $this->include('vida/cotizar') ?>
<?= $this->include('desempleo/cotizar') ?>
<?= $this->include('incendio/cotizar') ?>
<?= $this->include('incendio/cotizar_leasing') ?>

<?= $this->endSection() ?>


<?= $this->section('js') ?>
<script>
    //representan los modals
    var tabla_resultados = new bootstrap.Modal(document.getElementById('tabla_resultados'), {});
    var completar_cotizacion = new bootstrap.Modal(document.getElementById('completar_cotizacion'), {});

    //mostrar los resultados
    tabla_resultados.show();

    //cerrar los resultados y mostrar el formulario
    function cerrar() {
        tabla_resultados.hide();
        completar_cotizacion.show();
    }

    //Funcion para cargar una url con codigo php cuando hagan una solicitud con ajax
    function modelosAJAX(val) {
        $.ajax({
            type: 'ajax',
            url: "<?= site_url('cotizaciones/lista_modelos') ?>",
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            method: "POST",
            data: {
                marcaid: val.value
            },
            success: function(response) {
                //agrega el codigo php en el select
                document.getElementById("modelos").innerHTML = response;
                //refresca solo el select para actualizar la interfaz del select
                $('.selectpicker').selectpicker('refresh');
            },
            error: function(data) {
                console.log(data);
            }
        });
    }
</script>
<!-- Latest compiled and minified JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/js/bootstrap-select.min.js"></script>
<?= $this->endSection() ?>


<?= $this->section('css') ?>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/css/bootstrap-select.min.css">
<!-- hace que el rango de clic del campo de fecha sea mas grande -->
<style>
    #fecha::-webkit-calendar-picker-indicator {
        padding-left: 50%;
    }

    #deudor::-webkit-calendar-picker-indicator {
        padding-left: 50%;
    }

    #codeudor::-webkit-calendar-picker-indicator {
        padding-left: 50%;
    }
</style>
<?= $this->endSection() ?>