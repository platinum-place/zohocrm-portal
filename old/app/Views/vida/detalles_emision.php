<h5 class="d-flex justify-content-center bg-primary text-white">PRIMA MENSUAL</h5>
<div class="card-group border">
    <div class="card border-0">
        <div class="card-body">
            <p style="font-size: 15px;">
                <b>Fecha Deudor</b>

                <?php if (!empty($cotizacion->getFieldValue("Fecha_de_nacimiento_codeudor"))) : ?>
                    <br> <b>Fecha Codeudor</b>
                <?php endif ?>
            </p>

            <p style="font-size: 15px;">
                <b>Suma Asegurada</b> <br>
                <b>Plazo</b>
            </p>

            <p style="font-size: 15px;">
                <?php
                if (session()->get('cuenta_id') == "3222373000005967119") {
                    echo "<b>Prima Neta</b> <br>";
                    echo "<b>Impuestos</b> <br>";
                    echo "<b>Prima total con imp. incluidos</b><br>";
                    echo "<b>Cargos mantenimiento de cuenta</b><br>";
                    echo "<b>Gran total a pagar</b";
                } else {
                    echo "<b>Prima Neta</b> <br>";
                    echo "<b>ISC</b> <br>";
                    echo "<b>Prima Total</b>";
                }
                ?>
            </p>
        </div>
    </div>

    <div class="card border-0">
        <div class="card-body">
            <p style="font-size: 15px;">
                <?= $cotizacion->getFieldValue("Fecha_de_nacimiento") ?>

                <?php if (!empty($cotizacion->getFieldValue("Fecha_de_nacimiento_codeudor"))) : ?>
                    <br> <?= $cotizacion->getFieldValue("Fecha_de_nacimiento_codeudor") ?>
                <?php endif ?>
            </p>

            <p style="font-size: 15px;">
                RD$ <?= number_format($cotizacion->getFieldValue("Suma_asegurada")) ?> <br>
                <?= $cotizacion->getFieldValue("Plazo") ?> meses
            </p>

            <p style="font-size: 15px;">
                <?php
                echo "RD$ " . number_format($cotizacion->getFieldValue('Prima_neta'), 2);
                echo "<br>RD$ " . number_format($cotizacion->getFieldValue('ISC'), 2);
                echo "<br>RD$ " . number_format($cotizacion->getFieldValue('Prima'), 2);

                if (session()->get('cuenta_id') == "3222373000005967119") {
                    echo "<br>RD$ " . number_format($cotizacion->getFieldValue('Monto_mantenimiento'), 2);
                    echo "<br>RD$ " . number_format($cotizacion->getFieldValue('Prima') + $cotizacion->getFieldValue('Monto_mantenimiento'), 2);
                }
                ?>
            </p>
        </div>
    </div>
</div>

<div class="col-12">
    &nbsp;
</div>

<div class="card-group">
    <div class="card">
        <div class="card-body">
            <h6 class="card-title text-center">REQUISITOS DEL DEUDOR</h6>
            <?php $requisitos = $plan->getFieldValue("Requisitos_deudor"); ?>
            <ul>
                <?php foreach ($requisitos as $posicion => $requisito) : ?>
                    <li><?= $requisito  ?></li>
                <?php endforeach ?>
            </ul>
        </div>
    </div>

    <?php if (!empty($cotizacion->getFieldValue("Fecha_de_nacimiento_codeudor"))) : ?>
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-center">REQUISITOS DEL CODEUDOR</h6>
                <?php $requisitos = $plan->getFieldValue("Requisitos_codeudor"); ?>
                <ul>
                    <?php foreach ($requisitos as $posicion => $requisito) : ?>
                        <li><?= $requisito  ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        </div>
    <?php endif ?>
</div>