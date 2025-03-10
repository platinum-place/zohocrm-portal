<h6>DETALLES DEL CLIENTE</h6>
<div class="row" style="font-size: small;">
    <div class="col-6">
        <table class="table table-sm table-bordered">
            <tbody>
                <tr>
                    <th scope="col">Nombre</th>
                    <td><?= $cliente->getFieldValue("First_Name") . " " . $cliente->getFieldValue("Last_Name") ?></td>
                </tr>

                <tr>
                    <th scope="col">RNC/Cédula</th>
                    <td><?= $cliente->getFieldValue("RNC_C_dula") ?></td>
                </tr>

                <tr>
                    <th scope="col">Fecha de Nacimiento</th>
                    <td><?= $cliente->getFieldValue("Fecha_de_nacimiento") ?></td>
                </tr>

                <tr>
                    <th scope="col">Corredor</th>
                    <td><?= $tua->getFieldValue("Account_Name")->getLookupLabel() ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="col-6">
        <table class="table table-sm table-bordered">
            <tbody>
                <tr>
                    <th scope="col">Tel.</th>
                    <td><?= $cliente->getFieldValue("Phone") ?></td>
                </tr>

                <tr>
                    <th scope="col">Email</th>
                    <td><?= $cliente->getFieldValue("Email") ?></td>
                </tr>

                <tr>
                    <th scope="col">Dirección</th>
                    <td><?= $cliente->getFieldValue("Street") ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="col-12">
    &nbsp;
</div>

<h6>DETALLES TUA</h6>
<div class="row" style="font-size: small;">
    <div class="col-6">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th scope="col">Producto</th>
                    <td><?= $tua->getFieldValue("Type") ?></td>
                </tr>

                <tr>
                    <th scope="col">Vigencia Desde</th>
                    <td><?= date('d/m/Y', strtotime($tua->getFieldValue("Fecha_de_inicio"))) ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="col-6">
        <table class="table table-sm table-bordered">
            <tbody>
                <tr>
                    <th scope="col">Número TUA</th>
                    <td><?= $tua->getFieldValue("Deal_Name") ?></td>
                </tr>

                <tr>
                    <th scope="col">Vigencia Hasta</th>
                    <td><?= date('d/m/Y', strtotime($tua->getFieldValue("Closing_Date"))) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>