<h5 class="d-flex justify-content-center bg-primary text-white">COBERTURAS</h5>
<div class="card-group border" style="font-size: small;">
    <div class="card border-0">
        <div class="card-body">
        <p style="font-size: 15px;">
        <b>DAÑOS PROPIOS</b> <br>
                Riesgos Comprensivos <br>
                Riesgos Compr. (Deducible) <br>
                Rotura de Cristales (Deducible) <br>
                Colisión y Vuelco <br>
                Incendio y Robo
            </p>

            <p style="font-size: 15px;">
                <b>RESPONSABILIDAD CIVIL</b> <br>
                Daños Propiedad Ajena <br>
                Lesiones/Muerte 1 Pers <br>
                Lesiones/Muerte más de 1 Pers <br>
                Lesiones/Muerte 1 Pasajero <br>
                Lesiones/Muerte más de 1 Pas
            </p>

            <p style="font-size: 15px;">
                <b>RIESGOS CONDUCTOR</b> <br>
                <b>FIANZA JUDICIAL</b>
            </p>

            <p style="font-size: 15px;">
                <b>COBERTURAS ADICIONALES</b> <br>
                Asistencia Vial <br>
                Renta Vehí­culo <br>
                En Caso de Accidente
            </p>

            <p style="font-size: 15px;">
                <b>PRIMA NETA <?= ($cotizacion->getFieldValue("Plan") == "Mensual Full") ? "MENSUAL" : "ANUAL" ?></b> <br>
                <b>ISC</b> <br>
                <b>PRIMA TOTAL <?= ($cotizacion->getFieldValue("Plan") == "Mensual Full") ? "MENSUAL" : "ANUAL" ?></b> <br>
            </p>
        </div>
    </div>

    <div class="card border-0">
        <div class="card-body">
            <?php
            $riesgo_compresivo = $cotizacion->getFieldValue('Suma_asegurada') * ($plan->getFieldValue('Riesgos_comprensivos') / 100);
            $colision = $cotizacion->getFieldValue('Suma_asegurada') * ($plan->getFieldValue('Colisi_n_y_vuelco') / 100);
            $incendio = $cotizacion->getFieldValue('Suma_asegurada') * ($plan->getFieldValue('Incendio_y_robo') / 100);
            ?>
            <p style="font-size: 15px;">
            <br>
                RD$<?= number_format($riesgo_compresivo, 2) ?><br>
                <?= $plan->getFieldValue('Riesgos_comprensivos_deducible')  ?><br>
                <?= $plan->getFieldValue('Rotura_de_cristales_deducible')  ?><br>
                RD$ <?= number_format($colision, 2) ?><br>
                RD$ <?= number_format($incendio, 2) ?>
            </p>

            <p style="font-size: 15px;">
                <br>
                RD$ <?= number_format($plan->getFieldValue('Da_os_propiedad_ajena'), 2) ?> <br>
                RD$ <?= number_format($plan->getFieldValue('Lesiones_muerte_1_pers'), 2) ?> <br>
                RD$ <?= number_format($plan->getFieldValue('Lesiones_muerte_m_s_1_pers'), 2) ?> <br>
                RD$ <?= number_format($plan->getFieldValue('Lesiones_muerte_1_pas'), 2) ?> <br>
                RD$ <?= number_format($plan->getFieldValue('Lesiones_muerte_m_s_1_pas'), 2) ?>
            </p>

            <p style="font-size: 15px;">
                RD$ <?= number_format($plan->getFieldValue('Riesgos_conductor'), 2) ?> <br>
                RD$ <?= number_format($plan->getFieldValue('Fianza_judicial'), 2) ?>
            </p>

            <p style="font-size: 15px;">
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

            <p style="font-size: 15px;">
                RD$ <?= number_format($cotizacion->getFieldValue('Prima_neta'), 2) ?> <br>
                RD$ <?= number_format($cotizacion->getFieldValue('ISC'), 2) ?> <br>
                RD$ <?= number_format($cotizacion->getFieldValue('Prima'), 2) ?>
            </p>
        </div>
    </div>
</div>

<div class="page-break"></div>

<div class="row" style="font-size: 15px;">
    <div class="col-6 border">
        <img src="<?= base_url("img/aseguradoras/" . $plan->getFieldValue("Vendor_Name")->getLookupLabel() . ".png") ?>" width="150" height="50">

        <table class="table table-sm table-borderless">
            <tbody>
                <tr>
                    <th scope="col">Póliza</th>
                    <td><?= $plan->getFieldValue('P_liza') ?></td>
                </tr>

                <tr>
                    <th scope="col">Marca</th>
                    <td><?= $cotizacion->getFieldValue('Marca')->getLookupLabel() ?></td>
                </tr>

                <tr>
                    <th scope="col">Modelo</th>
                    <td><?= $cotizacion->getFieldValue('Modelo')->getLookupLabel() ?></td>
                </tr>

                <tr>
                    <th scope="col">Chasis</th>
                    <td><?= $cotizacion->getFieldValue("Chasis") ?></td>
                </tr>

                <tr>
                    <th scope="col">Placa</th>
                    <td><?= $cotizacion->getFieldValue("Placa") ?></td>
                </tr>


                <tr>
                    <th scope="col">Año</th>
                    <td><?= $cotizacion->getFieldValue("A_o") ?></td>
                </tr>

                <tr>
                    <th scope="col">Desde</th>
                    <td><?= date("d/m/Y", strtotime($cotizacion->getCreatedTime()))  ?></td>
                </tr>


                <tr>
                    <th scope="col">Hasta</th>
                    <td><?= date("d/m/Y", strtotime($cotizacion->getFieldValue('Valid_Till'))) ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="col-6 border">
        <center><b>EN CASO DE ACCIDENTE</b></center>

        Realiza el levantamiento del acta policial y obténga la siguente cotización:

        <ul>
            <li>
                Nombre, dirección y teléfonos del conductor, los lesionados, del propietario y de los testigos.
            </li>
            <li>Número de placa y póliza del vehí­culo involucrado, nombre de la aseguradora.</li>
        </ul>

        <b>EN CASO DE ROBO:</b> Notifica de inmediato a la policí­a y a la aseguradora.

        <center><b>RESERVE SU DERECHO</b></center>

        <table class="table table-sm table-borderless">
            <tbody>
                <tr>
                    <td>
                        <b>Aseguradora:</b> Tel. <?= $plan->getFieldValue('Tel_aseguradora') ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="table table-sm table-borderless">
            <tbody>
                <tr>
                    <?php if ($plan->getFieldValue('En_caso_de_accidente')) : ?>
                        <td>
                            <b><?= $plan->getFieldValue('En_caso_de_accidente') ?></b> <br>
                            Tel. Sto. Dgo <?= $plan->getFieldValue('Tel_santo_domingo') ?> <br>
                            Tel. Santiago <?= $plan->getFieldValue('Tel_santiago') ?>
                        </td>
                    <?php endif ?>

                    <?php if ($plan->getFieldValue('Asistencia_vial') == 1) : ?>
                        <td>
                            <b>Asistencia vial 24 horas</b> <br>
                            Tel. <?= $plan->getFieldValue('Tel_asistencia_vial')  ?>
                        </td>
                    <?php endif ?>
                </tr>
            </tbody>
        </table>
    </div>
</div>