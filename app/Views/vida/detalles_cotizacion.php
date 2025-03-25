<h5 class="d-flex justify-content-center bg-primary text-white">PRIMA MENSUAL</h5>
<div class="card-group border">
    <div class="card border-0">
        <div class="card-body">
            <img src="<?= base_url("img/espacio.png") ?>" height="50" width="150">

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

    <?php foreach ($cotizacion->getLineItems() as $lineItem) : ?>
        <?php if ($lineItem->getNetTotal() > 0) : ?>
            <?php $plan = $libreria->getRecord("Products", $lineItem->getProduct()->getEntityId()); ?>
            <div class="card border-0">
                <div class="card-body">
                    <img src="<?= base_url("img/aseguradoras/" . $plan->getFieldValue("Vendor_Name")->getLookupLabel() . ".png") ?>" height="50" width="150">

                    <p style="font-size: 15px;">
                        <?= $cotizacion->getFieldValue("Fecha_de_nacimiento") ?>

                        <?php if (!empty($cotizacion->getFieldValue("Fecha_de_nacimiento_codeudor"))) {
                            echo "<br>" . $cotizacion->getFieldValue("Fecha_de_nacimiento_codeudor");
                        } ?>
                    </p>

                    <p style="font-size: 15px;">
                        RD$ <?= number_format($cotizacion->getFieldValue("Suma_asegurada"), 2) ?> <br>
                        <?= $cotizacion->getFieldValue("Plazo") ?> meses
                    </p>

                    <p style="font-size: 15px;">
                        <?php
                        echo "RD$ " . number_format($lineItem->getNetTotal() / 1.16, 2);
                        echo "<br>RD$ " . number_format($lineItem->getNetTotal() - $lineItem->getNetTotal() / 1.16, 2);
                        echo "<br>RD$ " . number_format($lineItem->getNetTotal(), 2);

                        if (session()->get('cuenta_id') == "3222373000005967119") {
                            $data = json_decode($lineItem->getDescription(), true);
                            echo "<br>RD$ " . number_format($data['monto_mantenimiento'], 2);
                            echo "<br>RD$ " . number_format($lineItem->getNetTotal() + $data['monto_mantenimiento'], 2);
                        }
                        ?>
                    </p>
                </div>
            </div>
        <?php endif ?>
    <?php endforeach ?>
</div>

<div class="col-12">
    &nbsp;
</div>

<div class="card-group border">
    <div class="card border-0">
        <div class="card-body">
            <h6 class="card-title text-center">REQUISITOS DEL DEUDOR</h6>

            <?php foreach ($cotizacion->getLineItems() as $lineItem) : ?>
                <?php if ($lineItem->getNetTotal() > 0) : ?>
                    <?php
                    $plan = $libreria->getRecord("Products", $lineItem->getProduct()->getEntityId());
                    $requisitos = $plan->getFieldValue("Requisitos_deudor");
                    ?>

                    <ul>
                        <li>
                            <b><?= $plan->getFieldValue("Vendor_Name")->getLookupLabel() ?></b>:
                            <?php foreach ($requisitos as $posicion => $requisito) : ?>
                                <?= $requisito  ?>

                                <?php if ($requisito === end($requisitos)) : ?>
                                    .
                                <?php else : ?>
                                    ,
                                <?php endif ?>
                            <?php endforeach ?>
                        </li>
                    </ul>
                <?php endif ?>
            <?php endforeach ?>
        </div>
    </div>

    <?php if (!empty($cotizacion->getFieldValue("Fecha_de_nacimiento_codeudor"))) : ?>
        <div class="card border-0">
            <div class="card-body">
                <h6 class="card-title text-center">REQUISITOS DEL CODEUDOR</h6>

                <?php foreach ($cotizacion->getLineItems() as $lineItem) : ?>
                    <?php if ($lineItem->getNetTotal() > 0) : ?>
                        <?php
                        $plan = $libreria->getRecord("Products", $lineItem->getProduct()->getEntityId());
                        $requisitos = $plan->getFieldValue("Requisitos_codeudor");
                        ?>

                        <ul>
                            <li>
                                <b><?= $plan->getFieldValue("Vendor_Name")->getLookupLabel() ?></b>:
                                <?php foreach ($requisitos as $posicion => $requisito) : ?>
                                    <?= $requisito  ?>

                                    <?php if ($requisito === end($requisitos)) : ?>
                                        .
                                    <?php else : ?>
                                        ,
                                    <?php endif ?>
                                <?php endforeach ?>
                            </li>
                        </ul>
                    <?php endif ?>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>
</div>