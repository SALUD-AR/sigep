<?php

require_once ("../../config.php");

$sql="SELECT 
  planillas.planillas.id_planillas,
  planillas.planillas.periodo,
  planillas.planillas.fecha_hora,
  planillas.planillas.cant_nino,
  planillas.planillas.cant_embarazada,
  planillas.planillas.motivo,
  planillas.planillas.usuario,
  planillas.planillas.tipo,
  planillas.agente_inscriptor.descripcion_agente,
  planillas.entrega.descripcion_entrega,
  planillas.recibe.descripcion_recibe,
  facturacion.smiefectores.nombreefector
FROM
  planillas.agente_inscriptor
  INNER JOIN planillas.planillas ON (planillas.agente_inscriptor.id_agente_inscriptor = planillas.planillas.id_agente_inscriptor)
  INNER JOIN planillas.entrega ON (planillas.planillas.id_entrega = planillas.entrega.id_entrega)
  INNER JOIN planillas.recibe ON (planillas.planillas.id_recibe = planillas.recibe.id_recibe)
  INNER JOIN facturacion.smiefectores ON (planillas.planillas.id_efector = facturacion.smiefectores.cuie)";

$result=sql($sql) or fin_pagina();

excel_header("planilla.xls");

?>
<form name=form1 method=post action="planilla_listado_excel.php">
<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total: </b><?=$result->RecordCount();?> 
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table> 
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  
 <tr bgcolor=#C0C0FF>
    <td align=right >Periodo</td>      	
    <td align=right >Fecha</td>      	
    <td align=right >Niño</td>
    <td align=right >Embarazada</td>
    <td align=right >Efector</td>
    <td align=right >Usuario</td>    
    <td align=right >Agente</td>
    <td align=right >Entrega</td>
    <td align=right >Recibe</td>
    <td align=right >Tipo</td>    
    <td align=right >Observaciones</td>    
  </tr>   
  <?   
  while (!$result->EOF) {?>  
    <tr <?=atrib_tr()?>>     
     <td ><?=$result->fields['periodo']?></td>
     <td ><?=fecha($result->fields['fecha_hora'])?></td>
     <td ><?=$result->fields['cant_nino']?></td>
     <td ><?=$result->fields['cant_embarazada']?></td>     
     <td ><?=$result->fields['nombreefector']?></td> 
     <td ><?=$result->fields['usuario']?></td>      
     <td ><?=$result->fields['descripcion_agente']?></td>      
     <td ><?=$result->fields['descripcion_entrega']?></td>      
     <td ><?=$result->fields['descripcion_recibe']?></td>      
     <td ><?=$result->fields['tipo']?></td>      
     <td ><?=$result->fields['motivo']?></td>      
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>