<?php

require_once ("../../config.php");
extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

	$fecha_desde=Fecha_db($fecha_desde);
	$fecha_hasta=Fecha_db($fecha_hasta);
if($cuie!='Todos'){
			$sql_tmp="SELECT 
							nacer.efe_conv.cuie,
							nacer.efe_conv.nombre as nom_efector,
							fichero.fichero.fecha_control,
							count(fichero.fichero.id_fichero)as cantidad
							FROM
							fichero.fichero
							LEFT OUTER JOIN nacer.smiafiliados ON fichero.fichero.id_smiafiliados = nacer.smiafiliados.id_smiafiliados
							LEFT OUTER JOIN leche.beneficiarios ON leche.beneficiarios.id_beneficiarios = fichero.fichero.id_beneficiarios
							INNER JOIN nacer.efe_conv ON nacer.efe_conv.cuie = fichero.fichero.cuie
						where (fichero.fichero.fecha_control BETWEEN '$fecha_desde' and '$fecha_hasta') and (nacer.efe_conv.cuie='$cuie')
						GROUP BY
								nacer.efe_conv.cuie,
								nacer.efe_conv.nombre,
								fichero.fichero.fecha_control";
}else {
				$sql_tmp="SELECT 
							nacer.efe_conv.cuie,
							nacer.efe_conv.nombre as nom_efector,
							fichero.fichero.fecha_control,
							count(fichero.fichero.id_fichero)as cantidad
								FROM
								fichero.fichero
								LEFT OUTER JOIN nacer.smiafiliados ON fichero.fichero.id_smiafiliados = nacer.smiafiliados.id_smiafiliados
								LEFT OUTER JOIN leche.beneficiarios ON leche.beneficiarios.id_beneficiarios = fichero.fichero.id_beneficiarios
								INNER JOIN nacer.efe_conv ON nacer.efe_conv.cuie = fichero.fichero.cuie
							where (fichero.fichero.fecha_control BETWEEN '$fecha_desde' and '$fecha_hasta') 
							GROUP BY
								nacer.efe_conv.cuie,
								nacer.efe_conv.nombre,
								fichero.fichero.fecha_control";
							
			}					
$result=sql($sql_tmp) or fin_pagina();

excel_header("fichero_gral_excel.xls");

?>
<form name=form1 method=post action="fichero_gral_excel.php">
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
   	<td align=right id=mo>CUIE</td>
    <td align=right id=mo>Efector</td>
    <td align=right id=mo>Fecha de Control</td>
    <td align=right id=mo>Cantidad de Prestaciones</td>
  </tr>
  <?   
  while (!$result->EOF) {?>  
    <tr>     
  		<td ><?=$result->fields['cuie']?></td>         
	     <td ><?=$result->fields['nom_efector']?></td>    
	     <td ><?=fecha($result->fields['fecha_control'])?></td>   
	     <td ><?=$result->fields['cantidad']?></td> 
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>