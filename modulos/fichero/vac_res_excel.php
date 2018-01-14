<?php

require_once ("../../config.php");
extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

$fecha_desde=fecha_db($fecha_desde);
$fecha_hasta=fecha_db($fecha_hasta);
			if($cuie!='Todos'){
			$sql_tmp="SELECT  nacer.efe_conv.cuie,
								nacer.efe_conv.nombre as nom_efector,
								trazadoras.vac_apli.nombre as nom_vacum,
								trazadoras.dosis_apli.nombre as dosis,
								count(trazadoras.vac_apli.nombre)as cantidad
							FROM
								trazadoras.vacunas
								INNER JOIN nacer.efe_conv ON trazadoras.vacunas.cuie = nacer.efe_conv.cuie
								INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
								INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
								LEFT OUTER JOIN leche.beneficiarios on trazadoras.vacunas.id_beneficiarios= leche.beneficiarios.id_beneficiarios
								LEFT OUTER JOIN nacer.smiafiliados on trazadoras.vacunas.id_smiafiliados= nacer.smiafiliados.id_smiafiliados
							where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta')and (nacer.efe_conv.cuie='$cuie') and (trazadoras.vacunas.eliminada=0)
							GROUP BY
								nacer.efe_conv.cuie,
								nacer.efe_conv.nombre ,
								trazadoras.vac_apli.nombre,
								trazadoras.dosis_apli.nombre";
			}else {
				$sql_tmp="SELECT  nacer.efe_conv.cuie,
								nacer.efe_conv.nombre as nom_efector,
								trazadoras.vac_apli.nombre as nom_vacum,
								trazadoras.dosis_apli.nombre as dosis,
								count(trazadoras.vac_apli.nombre)as cantidad
							FROM
								trazadoras.vacunas
								INNER JOIN nacer.efe_conv ON trazadoras.vacunas.cuie = nacer.efe_conv.cuie
								INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
								INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
								LEFT OUTER JOIN leche.beneficiarios on trazadoras.vacunas.id_beneficiarios= leche.beneficiarios.id_beneficiarios
								LEFT OUTER JOIN nacer.smiafiliados on trazadoras.vacunas.id_smiafiliados= nacer.smiafiliados.id_smiafiliados
							where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and (trazadoras.vacunas.eliminada=0)
							GROUP BY
								nacer.efe_conv.cuie,
								nacer.efe_conv.nombre ,
								trazadoras.vac_apli.nombre,
								trazadoras.dosis_apli.nombre";
			}

$result=sql($sql_tmp) or fin_pagina();

excel_header("resumen_vacuna.xls");
//echo $html_header;
?>
<form name=form1 method=post action="vac_listado_excel.php">
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
    <td align=right id=mo>EFECTOR</td>
    <td align=right id=mo>VACUNA</td>
    <td align=right id=mo>DOSIS</td>
    <td align=right id=mo>CANTIDAD APLICADA</td>
  </tr>
  <?   
  while (!$result->EOF) {?>  
    <tr>     
    <td><?=$result->fields['cuie']?></td>     
     <td><?=$result->fields['nom_efector']?></td>     
     <td><?=$result->fields['nom_vacum']?></td>     
     <td><?=$result->fields['dosis']?></td>     
     <td><?=$result->fields['cantidad']?></td>  
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>