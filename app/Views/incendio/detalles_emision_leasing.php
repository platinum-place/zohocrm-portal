<h5 class="d-flex justify-content-center bg-primary text-white">PRIMA MENSUAL</h5>
<div class="card-group border">
    <div class="card border-0">
        <div class="card-body">
            <p style="font-size: 15px;">
                <b>Valor de la Propiedad</b> <br>
                <b>Valor del Préstamo</b> <br>
                <b>Plazo</b>
            </p>

            <p style="font-size: 15px;">
                <b>Tipo Equipo</b> <br>
                <!-- <b>Direción</b> -->
            </p>

            <p style="font-size: 15px;">
                <b>Prima Neta</b> <br>
                <b>ISC</b> <br>
                <b>Prima Mensual</b>
            </p>
        </div>
    </div>

    <div class="card border-0">
        <div class="card-body">
            <p style="font-size: 15px;">
                RD$ <?= number_format($cotizacion->getFieldValue("Suma_asegurada")) ?> <br>
                RD$ <?= number_format($cotizacion->getFieldValue("Prestamo")) ?> <br>
                <?= $cotizacion->getFieldValue("Plazo") ?> meses
            </p>

            <p style="font-size: 15px;">
                <?= $cotizacion->getFieldValue("Tipo_equipo") ?> <br>
                <!-- <?= $cotizacion->getFieldValue("Direcci_n") ?> -->
            </p>

            <p style="font-size: 15px;">
                RD$ <?= number_format($cotizacion->getFieldValue('Prima_neta'), 2) ?> <br>
                RD$ <?= number_format($cotizacion->getFieldValue('ISC'), 2) ?> <br>
                RD$ <?= number_format($cotizacion->getFieldValue('Prima'), 2) ?>
            </p>
        </div>
    </div>
</div>