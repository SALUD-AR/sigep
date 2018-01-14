<?php

require_once ("../../config.php");

$fecha_hasta=$parametros["fecha_hasta"];


$sql="SELECT 
  nacer.efe_conv.cuie,
  nacer.efe_conv.nombre,
  sum(contabilidad.ingreso.monto_deposito) AS ingreso
FROM
  nacer.efe_conv
  INNER JOIN contabilidad.ingreso ON (nacer.efe_conv.cuie = contabilidad.ingreso.cuie)
WHERE
  (ingreso.fecha_deposito <= '$fecha_hasta')
GROUP BY
  nacer.efe_conv.cuie,
  nacer.efe_conv.nombre
ORDER BY
  nacer.efe_conv.cuie";
$ing=sql($sql) or fin_pagina();

$sql="SELECT 
  nacer.efe_conv.cuie,
  nacer.efe_conv.nombre,
  contabilidad.inciso.ins_nombre,
  sum (contabilidad.egreso.monto_egreso) as monto
  
FROM
  nacer.efe_conv
  INNER JOIN contabilidad.egreso ON (nacer.efe_conv.cuie = contabilidad.egreso.cuie)
  INNER JOIN contabilidad.inciso ON (contabilidad.egreso.id_inciso = contabilidad.inciso.id_inciso)
WHERE
  contabilidad.egreso.fecha_deposito <= '$fecha_hasta' and egreso.monto_egre_comp <> 0
GROUP BY
  contabilidad.inciso.ins_nombre,
  nacer.efe_conv.cuie,
  nacer.efe_conv.nombre
  
ORDER BY
  nacer.efe_conv.cuie,inciso.ins_nombre";
$egre=sql($sql) or fin_pagina();

excel_header("ingreso_egreso.xls");

?>
<form name=form1 method=post action="ingre_egre_excel.php">
<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total Ingresos por CAPS: </b><?=$ing->RecordCount();?> 
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table> 
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor=#C0C0FF>
    <td align=right id=mo>CUIE</td>      	
    <td align=right id=mo>Efector</td>
    <td align=right id=mo>Total</td>      
  </tr>
  <?   
  while (!$ing->EOF) {?>  
    <tr>     
     <td ><?=$ing->fields['cuie']?></td>
     <td ><?=$ing->fields['nombre']?></td>
     <td ><?=number_format($ing->fields['ingreso'],'2',',','.')?></td>     
    </tr>
	<?$ing->MoveNext();
    }?>
 </table>
  <br>
  <br>
  <br>
 <table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total Egresos por Inciso: </b><?=$egre->RecordCount();?> 
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table> 
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor=#C0C0FF>
    <td align=right id=mo>CUIE</td>      	
    <td align=right id=mo>Efector</td>
    <td align=right id=mo>Inciso</td>
    <td align=right id=mo>Total</td>      
  </tr>
  <?   
  while (!$egre->EOF) {?>  
    <tr>     
     <td ><?=$egre->fields['cuie']?></td>
     <td ><?=$egre->fields['nombre']?></td>
     <td ><?=$egre->fields['ins_nombre']?></td>
     <td ><?=number_format($egre->fields['monto'],'2',',','.')?></td>     
    </tr>
	<?$egre->MoveNext();
    }?>
 </table>
 
 </form>
