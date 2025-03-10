<h6>¡Cotización emitida correctamente!</h6>

<p>La emisión de <b><?= $cliente ?></b> está siendo depurada. Mientras, puedes hacer clic en las siguientes acciones:</p>

<ul>
    <li>
        <a href="<?= site_url("cotizaciones/adjuntar/$id") ?>" title="Adjuntar" target="__blank">
            <i class="fas fa-upload"></i>
            <b>Adjuntar:</b>
        </a>

        Agregar más documentos a la emisión.
    </li>

    <li>
        <a href="<?= site_url("cotizaciones/descargar/$id") ?>" title="Descargar" target="__blank">
            <i class="fas fa-download"></i>
            <b>Constancia:</b>
        </a>

        Descargar la <b>constancia de emisión</b> en formato PDF.
    </li>
    
        <li>
        <a href="<?= site_url("cotizaciones/condicionado/$plan") ?>" title="Condicionado" target="__blank">
            <i class="fas fa-file-pdf"></i>
            <b>Condicionado:</b>
        </a>

        Descargar el condicionado de <b><?= $aseguradora ?></b> en formato PDF.
    </li>
</ul>