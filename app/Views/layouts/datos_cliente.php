<table class="small-print-table" style="border:none; font-size:smaller; width:100%; border-collapse:collapse;">
    <tr><td style="width:15%"><b>Nombre:</b></td><td style="width:35%"><?=$cotizacion->getFieldValue("Nombre")." ".$cotizacion->getFieldValue("Apellido")?></td><td style="width:15%"><b>Tel. Residencia:</b></td><td style="width:35%"><?=$cotizacion->getFieldValue("Tel_Residencia")?></td></tr>
    <tr><td style="width:15%"><b>RNC/Cédula:</b></td><td style="width:35%"><?=$cotizacion->getFieldValue("RNC_C_dula")?></td><td style="width:15%"><b>Tel. Celular:</b></td><td style="width:35%"><?=$cotizacion->getFieldValue("Tel_Celular")?></td></tr>
    <tr><td style="width:15%"><b>Email:</b></td><td style="width:35%"><?=$cotizacion->getFieldValue("Correo_electr_nico")?></td><td style="width:15%"><b>Tel. Trabajo:</b></td><td style="width:35%"><?=$cotizacion->getFieldValue("Tel_Trabajo")?></td></tr>
    <tr><td style="width:15%"><b>Fecha Nac.:</b></td><td style="width:35%"><?=$cotizacion->getFieldValue("Fecha_de_nacimiento")?></td><td style="width:15%"><b>Dirección:</b></td><td style="width:35%"><?=$cotizacion->getFieldValue("Direcci_n")?></td></tr>
    <tr>
        <td style="width:15%"><?php if($cotizacion->getFieldValue("Plan")=="Vida"):?><b>Garante:</b><?php else:?><b>&nbsp;</b><?php endif?></td>
        <td style="width:35%"><?php if($cotizacion->getFieldValue("Plan")=="Vida"):?><?=($cotizacion->getFieldValue("Garante"))?"Si":"No"?><?php else:?>&nbsp;<?php endif?></td>
        <td style="width:15%"><?php if(!empty($cotizacion->getFieldValue("Tipo_de_pago"))):?><b>Tipo de pago:</b><?php else:?><b>&nbsp;</b><?php endif?></td>
        <td style="width:35%"><?php if(!empty($cotizacion->getFieldValue("Tipo_de_pago"))):?><?=$cotizacion->getFieldValue("Tipo_de_pago")?><?php else:?>&nbsp;<?php endif?></td>
    </tr>
</table>