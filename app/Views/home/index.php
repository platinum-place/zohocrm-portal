<?= $this->extend('components/app') ?>

<?= $this->section('content') ?>

<h1 class="mt-4">Panel de Control</h1>
<hr>

<?php if (session()->getFlashdata('alert')) : ?>
    <div class="alert alert-success" role="alert">
        <?= session()->getFlashdata('alert') ?>
    </div>
<?php endif ?>

<div class="alert alert-success" role="alert">
    <h3 class="alert-heading">¡Bienvenido al Insurance Tech de Grupo Nobe!</h3>
    <p>
        Desde su panel de control podrás ver la información necesaria para manejar sus pólizas y cotizaciones.
    </p>
</div>

<div class="row">
    <div class="col-xl-3 col-md-4">
        <div class="card bg-success text-white mb-4">
            <div class="card-body">
                Pólizas emitidas este mes<br>
                <?= $quotes_count ?? 0 ?>
            </div>
        </div>
    </div>
</div>

<!--<div class="card mb-4">-->
<!--    <div class="card-body">-->
<!--        <table class="table">-->
<!--            <thead>-->
<!--            <tr>-->
<!--                <th>Aseguradora</th>-->
<!--                <th>Cantidad</th>-->
<!--            </tr>-->
<!--            </thead>-->
<!---->
<!--            <tbody>-->
<!--            --><?php //foreach ($lista as $aseguradora => $cantidad) : ?>
<!--                <tr>-->
<!--                    <td>--><?php //= $aseguradora ?><!--</td>-->
<!--                    <td>--><?php //= $cantidad ?><!--</td>-->
<!--                </tr>-->
<!--            --><?php //endforeach ?>
<!--            </tbody>-->
<!--        </table>-->
<!--    </div>-->
<!--</div>-->

<?= $this->endSection() ?>
