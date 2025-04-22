<h5 class="d-flex justify-content-center bg-primary text-white">COBERTURAS</h5>
<div class="card-container d-flex overflow-auto">
    <!-- Fixed first column -->
    <div class="card border-0 flex-shrink-0" style="min-width: 180px; max-width: 180px;">
        <div class="card-body d-flex flex-column p-2">
            <!-- Logo section -->
            <div class="section-logo mb-3" style="height: 40px;">
                <img src="<?= base_url("img/espacio.png") ?>" height="40" width="120">
            </div>

            <!-- Damage section -->
            <div class="section-damage mb-3">
                <p style="font-size: 11px; margin-bottom: 0;">
                    <b>DAÑOS PROPIOS</b> <br>
                    Riesgos Comprensivos <br>
                    Riesgos Compr. (Deducible) <br>
                    Rotura de Cristales (Deducible) <br>
                    Colisión y Vuelco <br>
                    Incendio y Robo
                </p>
            </div>

            <!-- Civil liability section -->
            <div class="section-civil mb-3">
                <p style="font-size: 11px; margin-bottom: 0;">
                    <b>RESPONSABILIDAD CIVIL</b> <br>
                    Daños Propiedad Ajena <br>
                    Lesiones/Muerte 1 Pers <br>
                    Lesiones/Muerte más de 1 Pers <br>
                    Lesiones/Muerte 1 Pasajero <br>
                    Lesiones/Muerte más de 1 Pas
                </p>
            </div>

            <!-- Risk section -->
            <div class="section-risk mb-3">
                <p style="font-size: 11px; margin-bottom: 0;">
                    <b>RIESGOS CONDUCTOR</b> <br>
                    <b>FIANZA JUDICIAL</b>
                </p>
            </div>

            <!-- Additional coverages -->
            <div class="section-additional mb-3">
                <p style="font-size: 11px; margin-bottom: 0;">
                    <b>COBERTURAS ADICIONALES</b> <br>
                    Asistencia Vial <br>
                    Renta Vehí­culo <br>
                    En Caso de Accidente
                </p>
            </div>

            <!-- Pricing -->
            <div class="section-pricing">
                <p style="font-size: 11px; margin-bottom: 0;">
                    <b>PRIMA NETA <?= ($cotizacion->getFieldValue("Plan") == "Mensual Full") ? "MENSUAL" : "ANUAL" ?></b> <br>
                    <b>ISC</b> <br>
                    <b>PRIMA TOTAL <?= ($cotizacion->getFieldValue("Plan") == "Mensual Full") ? "MENSUAL" : "ANUAL" ?></b>
                </p>
            </div>
        </div>
    </div>

    <!-- Scrollable area for the dynamic columns -->
    <div class="dynamic-columns d-flex flex-nowrap">
        <?php foreach ($cotizacion->getLineItems() as $lineItem) : ?>
            <?php if ($lineItem->getNetTotal() > 0) : ?>
                <?php
                $plan = $libreria->getRecord("Products", $lineItem->getProduct()->getEntityId());
                $riesgo_compresivo = $cotizacion->getFieldValue('Suma_asegurada') * ($plan->getFieldValue('Riesgos_comprensivos') / 100);
                $colision = $cotizacion->getFieldValue('Suma_asegurada') * ($plan->getFieldValue('Colisi_n_y_vuelco') / 100);
                $incendio = $cotizacion->getFieldValue('Suma_asegurada') * ($plan->getFieldValue('Incendio_y_robo') / 100);
                ?>
                <div class="card border-0 flex-shrink-0" style="min-width: 150px; max-width: 150px;">
                    <div class="card-body d-flex flex-column p-2">
                        <!-- Logo section -->
                        <div class="section-logo mb-3" style="height: 40px;">
                            <img src="<?= base_url("img/aseguradoras/" . $plan->getFieldValue("Vendor_Name")->getLookupLabel() . ".png") ?>" height="40" width="120">
                        </div>

                        <!-- Damage section -->
                        <div class="section-damage mb-3">
                            <p style="font-size: 11px; margin-bottom: 0;">
                                <br>
                                RD$<?= number_format($riesgo_compresivo) ?><br>
                                <?= $plan->getFieldValue('Riesgos_comprensivos_deducible')  ?><br>
                                <?= $plan->getFieldValue('Rotura_de_cristales_deducible')  ?><br>
                                RD$ <?= number_format($colision) ?><br>
                                RD$ <?= number_format($incendio) ?>
                            </p>
                        </div>

                        <!-- Civil liability section -->
                        <div class="section-civil mb-3">
                            <p style="font-size: 11px; margin-bottom: 0;">
                                <br>
                                RD$ <?= number_format($plan->getFieldValue('Da_os_propiedad_ajena')) ?> <br>
                                RD$ <?= number_format($plan->getFieldValue('Lesiones_muerte_1_pers')) ?> <br>
                                RD$ <?= number_format($plan->getFieldValue('Lesiones_muerte_m_s_1_pers')) ?> <br>
                                RD$ <?= number_format($plan->getFieldValue('Lesiones_muerte_1_pas')) ?> <br>
                                RD$ <?= number_format($plan->getFieldValue('Lesiones_muerte_m_s_1_pas')) ?>
                            </p>
                        </div>

                        <!-- Risk section -->
                        <div class="section-risk mb-3">
                            <p style="font-size: 11px; margin-bottom: 0;">
                                RD$ <?= number_format($plan->getFieldValue('Riesgos_conductor')) ?> <br>
                                RD$ <?= number_format($plan->getFieldValue('Fianza_judicial')) ?>
                            </p>
                        </div>

                        <!-- Additional coverages -->
                        <div class="section-additional mb-3">
                            <p style="font-size: 11px; margin-bottom: 0;">
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
                        </div>

                        <!-- Pricing -->
                        <div class="section-pricing">
                            <p style="font-size: 11px; margin-bottom: 0;">
                                RD$ <?= number_format($lineItem->getNetTotal() / 1.16, 2) ?> <br>
                                RD$ <?= number_format($lineItem->getNetTotal() - $lineItem->getNetTotal() / 1.16, 2) ?> <br>
                                RD$ <?= number_format($lineItem->getNetTotal(), 2) ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif ?>
        <?php endforeach ?>
    </div>
</div>

<style>
    .card-container {
        border: 1px solid #dee2e6;
        overflow-x: auto;
    }

    .dynamic-columns {
        overflow-x: auto;
    }

    /* Ensure all cards have the same height */
    .card-body {
        height: 100%;
    }

    /* Fixed heights for each section to ensure alignment */
    .section-logo {
        height: 40px;
        display: flex;
        align-items: center;
    }

    .section-damage {
        min-height: 115px;
    }

    .section-civil {
        min-height: 115px;
    }

    .section-risk {
        min-height: 45px;
    }

    .section-additional {
        min-height: 85px;
    }

    .section-pricing {
        min-height: 65px;
    }
</style>