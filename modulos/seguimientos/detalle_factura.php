<?php

require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

$sql_tmp="select * from (
select id_factura,id_expediente,nro_exp,fecha_ing,monto,periodo_fact,
case when extract (month from fecha_ing)=1 then (extract (year from fecha_ing)-1)||'/'||'12'
else extract (year from fecha_ing)||'/'||regexp_replace(to_char((extract (month from fecha_ing))-1,'00'),' ','','g') end ::text as periodo_ingreso  from (

select * from (
select id_efe_conv from nacer.efe_conv where cuie='$cuie') as cuie_efector
left join (select * from expediente.expediente where fecha_ing between '$fecha_desde' and '$fecha_hasta') as facturas_periodo using (id_efe_conv)
 
) as efector
left join (select periodo_actual as periodo_fact,id_factura from facturacion.factura) as too1 using (id_factura)
) as factura_efector where periodo_fact=periodo_ingreso and (extract (day from fecha_ing) between 1 and 12)";
$result=sql($sql_tmp) or fin_pagina();

$sql_factura="select count (*) as cantidad from (
select * from (
select id_factura,id_expediente,nro_exp,fecha_ing,monto,periodo_fact,
case when extract (month from fecha_ing)=1 then (extract (year from fecha_ing)-1)||'/'||'12'
else extract (year from fecha_ing)||'/'||regexp_replace(to_char((extract (month from fecha_ing))-1,'00'),' ','','g') end ::text as periodo_ingreso  from (

select * from (
select id_efe_conv from nacer.efe_conv where cuie='$cuie') as cuie_efector
left join (select * from expediente.expediente where fecha_ing between '$fecha_desde' and '$fecha_hasta') as facturas_periodo using (id_efe_conv)
 
) as efector
left join (select periodo_actual as periodo_fact,id_factura from facturacion.factura) as too1 using (id_factura)
) as factura_efector where periodo_fact=periodo_ingreso and (extract (day from fecha_ing) between 1 and 12)
) as cantidad";
$res_factura= sql($sql_factura) or die;
$cantidad=$res_factura->fields['cantidad'];

echo $html_header;
?>
<form name=form1 method=post action="detalle_factura.php">
<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total de Facturas: <?=$result->RecordCount();?>  --- Total de facturas en el periodo: <?=$cantidad;?></b>
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table> 
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor=#C0C0FF>
    <td align=right id=mo>Id_factura</td>      	
    <td align=right id=mo>Id_Expediente</td>      	
    <td align=right id=mo>nro_expediente</td>      	
    <td align=right id=mo>Fecha de ingreso</td>      	
    <td align=right id=mo>Monto</td>
	<td align=right id=mo>Periodo de Factura</td>
  </tr>
  <?   
  while (!$result->EOF) {?>  
    <tr>     
     <td><?=$result->fields['id_factura']?></td>
     <td><?=$result->fields['id_expediente']?></td>
     <td><?=$result->fields['nro_exp']?></td>
     <td><?=fecha ($result->fields['fecha_ing'])?></td>
	 <td><?=$result->fields['monto']?></td> 
	 <td><?=$result->fields['periodo_fact']?></td> 
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>
