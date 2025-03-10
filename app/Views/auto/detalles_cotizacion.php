<h5 class="d-flex justify-content-center bg-primary text-white">COBERTURAS</h5>
<div class="card-group border">
    <div class="card border-0">
        <div class="card-body">
            <img src="<?= base_url("img/espacio.png") ?>" height="50" width="150">

            <p style="font-size: 12px;">
                <b>DAÑOS PROPIOS</b> <br>
                Riesgos Comprensivos <br>
                Riesgos Compr. (Deducible) <br>
                Rotura de Cristales (Deducible) <br>
                Colisión y Vuelco <br>
                Incendio y Robo
            </p>

            <p style="font-size: 12px;">
                <b>RESPONSABILIDAD CIVIL</b> <br>
                Daños Propiedad Ajena <br>
                Lesiones/Muerte 1 Pers <br>
                Lesiones/Muerte más de 1 Pers <br>
                Lesiones/Muerte 1 Pasajero <br>
                Lesiones/Muerte más de 1 Pas
            </p>

            <p style="font-size: 12px;">
                <b>RIESGOS CONDUCTOR</b> <br>
                <b>FIANZA JUDICIAL</b>
            </p>

            <p style="font-size: 12px;">
                <b>COBERTURAS ADICIONALES</b> <br>
                Asistencia Vial <br>
                Renta Vehí­culo <br>
                En Caso de Accidente
            </p>

            <p style="font-size: 12px;">
                <b>PRIMA NETA <?= ($cotizacion->getFieldValue("Plan") == "Mensual Full") ? "MENSUAL" : "ANUAL" ?></b> <br>
                <b>ISC</b> <br>
                <b>PRIMA TOTAL <?= ($cotizacion->getFieldValue("Plan") == "Mensual Full") ? "MENSUAL" : "ANUAL" ?></b> <br>
            </p>
        </div>
    </div>

    <?php foreach ($cotizacion->getLineItems() as $lineItem) : ?>
        <?php if ($lineItem->getNetTotal() > 0) : ?>
            <?php
            $plan = $libreria->getRecord("Products", $lineItem->getProduct()->getEntityId());
            $riesgo_compresivo = $cotizacion->getFieldValue('Suma_asegurada') * ($plan->getFieldValue('Riesgos_comprensivos') / 100);
            $colision = $cotizacion->getFieldValue('Suma_asegurada') * ($plan->getFieldValue('Colisi_n_y_vuelco') / 100);
            $incendio = $cotizacion->getFieldValue('Suma_asegurada') * ($plan->getFieldValue('Incendio_y_robo') / 100);
            ?>
            <div class="card border-0">
                <div class="card-body">
                    <img src="<?= base_url("img/aseguradoras/" . $plan->getFieldValue("Vendor_Name")->getLookupLabel() . ".png") ?>" height="50" width="150">

                    <p style="font-size: 12px;">
                        <br>
                        RD$<?= number_format($riesgo_compresivo) ?><br>
                        <?= $plan->getFieldValue('Riesgos_comprensivos_deducible')  ?><br>
                        <?= $plan->getFieldValue('Rotura_de_cristales_deducible')  ?><br>
                        RD$ <?= number_format($colision) ?><br>
                        RD$ <?= number_format($incendio) ?>
                    </p>

                    <p style="font-size: 12px;">
                        <br>
                        RD$ <?= number_format($plan->getFieldValue('Da_os_propiedad_ajena')) ?> <br>
                        RD$ <?= number_format($plan->getFieldValue('Lesiones_muerte_1_pers')) ?> <br>
                        RD$ <?= number_format($plan->getFieldValue('Lesiones_muerte_m_s_1_pers')) ?> <br>
                        RD$ <?= number_format($plan->getFieldValue('Lesiones_muerte_1_pas')) ?> <br>
                        RD$ <?= number_format($plan->getFieldValue('Lesiones_muerte_m_s_1_pas')) ?>
                    </p>

                    <p style="font-size: 12px;">
                        RD$ <?= number_format($plan->getFieldValue('Riesgos_conductor')) ?> <br>
                        RD$ <?= number_format($plan->getFieldValue('Fianza_judicial')) ?>
                    </p>

                    <p style="font-size: 12px;">
                        <br>
                        <?php
                        if ($plan->getFieldValue('Asistencia_vial') == 1) {
                            if (
                                preg_match('/\bpesado\b/i', $cotizacion->getFieldValue("Tipo_veh_culo"))
                                or
                                $cotizacion->getFieldValue("Tipo_veh_culo") == "Camión"
                            ) {
                                echo 'No aplica <br>';
                            } else {
                                if ($plan->getFieldValue('Valor_asistencia_vial')) {
                                    echo "Aplica (RD$" . number_format($plan->getFieldValue('Valor_asistencia_vial')) . ") <br>";
                                } else {
                                    echo 'Aplica <br>';
                                }
                            }
                        } else {
                            echo 'No aplica <br>';
                        }

                        /**
                         * 
                         * 
                         */
                        if ($plan->getFieldValue('Renta_veh_culo') == 1) {
                            if (
                                preg_match('/\bpesado\b/i', $cotizacion->getFieldValue("Tipo_veh_culo"))
                                or
                                $cotizacion->getFieldValue("Tipo_veh_culo") == "Camión"
                            ) {
                                echo 'No aplica <br>';
                            } else {
                                echo 'Aplica <br>';
                            }
                        } else {
                            echo 'No aplica <br>';
                        }
                        if (!empty($plan->getFieldValue('En_caso_de_accidente'))) {
                            echo $plan->getFieldValue('En_caso_de_accidente');
                        } else {
                            echo 'No aplica';
                        }
                        ?>
                    </p>

                    <p style="font-size: 12px;">
                        RD$ <?= number_format($lineItem->getNetTotal() / 1.16, 2) ?> <br>
                        RD$ <?= number_format($lineItem->getNetTotal() - $lineItem->getNetTotal() / 1.16, 2) ?> <br>
                        RD$ <?= number_format($lineItem->getNetTotal(), 2) ?>
                    </p>
                </div>
            </div>
        <?php endif ?>
    <?php endforeach ?>
</div>