<div class="modal fade" id="tabla_resultados" tabindex="-1" aria-labelledby="tabla_resultados" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tabla_resultados">¡Cotización exitosa! Presiona "Continuar" para descargar la cotización y emitir.</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Aseguradoras</th>
                            <th scope="col">Prima</th>
                            <th scope="col">Comentario</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $cont = 0 ?>
                        <?php foreach ($cotizacion->planes as $plan) : ?>
                            <tr>
                                <td><?= $plan["aseguradora"] ?></td>
                                <td>RD$
                                    <?php
                                    if (!empty($plan["monto_mantenimiento"]) and session()->get('cuenta_id') == "3222373000005967119" and $plan["monto_mantenimiento"] > 0) {
                                       echo number_format($plan["total"] + $plan["monto_mantenimiento"], 2);
                                    } else {
                                        echo number_format($plan["total"], 2);
                                    }
                                    ?>
                                </td>
                                <td><?= $plan["comentario"] ?></td>
                            </tr>
                            <?php
                            if ($plan["total"] > 0) {
                                $cont++;
                            }
                            ?>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <?php if ($cont > 0) : ?>
                    <button type="button" class="btn btn-primary" onclick="cerrar()">Continuar</button>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>