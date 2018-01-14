<?php

require_once ("../../config.php");

require_once ("../../config.php");
extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

	$fecha_desde=Fecha_db($fecha_desde);
	$fecha_hasta=Fecha_db($fecha_hasta);
if($cuie!='Todos'){
			$sql_tmp="SELECT 
				leche.beneficiarios.apellido,
				leche.beneficiarios.nombre,
				leche.beneficiarios.documento,
				leche.beneficiarios.sexo,
				leche.beneficiarios.fecha_nac,
				leche.beneficiarios.domicilio,
				nacer.smiafiliados.afiapellido,
				nacer.smiafiliados.afinombre,
				nacer.smiafiliados.afidni,
				nacer.smiafiliados.afisexo,
				nacer.smiafiliados.afifechanac,
				nacer.smiafiliados.afidomlocalidad,	  
				trazadoras.vacunas.fecha_vac,
				trazadoras.vacunas.fecha_vac_prox,
				trazadoras.vacunas.nom_resp,
				trazadoras.vacunas.lote,
				trazadoras.vacunas.email,
				nacer.efe_conv.cuie,
				nacer.efe_conv.nombre as nom_efector,
				trazadoras.vac_apli.nombre as nom_vacum,
				trazadoras.dosis_apli.nombre as dosis
			FROM
				trazadoras.vacunas
				INNER JOIN nacer.efe_conv ON trazadoras.vacunas.cuie = nacer.efe_conv.cuie
				INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
				INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
				LEFT OUTER JOIN leche.beneficiarios on trazadoras.vacunas.id_beneficiarios= leche.beneficiarios.id_beneficiarios
				LEFT OUTER JOIN nacer.smiafiliados on trazadoras.vacunas.id_smiafiliados= nacer.smiafiliados.id_smiafiliados
			where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and (nacer.efe_conv.cuie='$cuie') and (trazadoras.vacunas.eliminada=0)";
}else {
				$sql_tmp="SELECT 
								  leche.beneficiarios.apellido,
								  leche.beneficiarios.nombre,
								  leche.beneficiarios.documento,
								  leche.beneficiarios.sexo,
								  leche.beneficiarios.fecha_nac,
								  leche.beneficiarios.domicilio,
								  nacer.smiafiliados.afiapellido,
								  nacer.smiafiliados.afinombre,
								  nacer.smiafiliados.afidni,
								  nacer.smiafiliados.afisexo,
								  nacer.smiafiliados.afifechanac,
								  nacer.smiafiliados.afidomlocalidad,								  
								  trazadoras.vacunas.fecha_vac,
								  trazadoras.vacunas.fecha_vac_prox,
								  trazadoras.vacunas.nom_resp,
								  trazadoras.vacunas.lote,
								  trazadoras.vacunas.email,
								  nacer.efe_conv.cuie,
								  nacer.efe_conv.nombre as nom_efector,
								  trazadoras.vac_apli.nombre as nom_vacum,
								  trazadoras.dosis_apli.nombre as dosis
						FROM
							trazadoras.vacunas
							INNER JOIN nacer.efe_conv ON trazadoras.vacunas.cuie = nacer.efe_conv.cuie
							INNER JOIN trazadoras.vac_apli ON trazadoras.vacunas.id_vac_apli = trazadoras.vac_apli.id_vac_apli
							INNER JOIN trazadoras.dosis_apli ON trazadoras.vacunas.id_dosis_apli = trazadoras.dosis_apli.id_dosis_apli
							LEFT OUTER JOIN leche.beneficiarios on trazadoras.vacunas.id_beneficiarios= leche.beneficiarios.id_beneficiarios
							LEFT OUTER JOIN nacer.smiafiliados on trazadoras.vacunas.id_smiafiliados= nacer.smiafiliados.id_smiafiliados
							where (trazadoras.vacunas.fecha_vac BETWEEN '$fecha_desde' and '$fecha_hasta') and (trazadoras.vacunas.eliminada=0)";
							
			}			
$result=sql($sql_tmp,"<br>Error al traer los datos<br>") or fin_pagina();
excel_header("vacuna_ind_excel.xls");

?>
<form name=form1 method=post action="vac_res_detallado.php">
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
    <td align=right id=mo>Apellido</td>      	
    <td align=right id=mo>Nombre</td>      	
    <td align=right id=mo>Documento</td>
    <td align=right id=mo>Sexo</td>
    <td align=right id=mo>Fecha Nacimiento</td>        
    <td align=right id=mo>Domicilio</td> 
    <td align=right id=mo>Vacuna</td> 
    <td align=right id=mo>Dosis</td> 
    <td align=right id=mo>Fecha Vacunacion</td> 
    <td align=right id=mo>Fecha Prox Vac</td> 
    <td align=right id=mo>Lote</td> 
    <td align=right id=mo>Mail</td> 
    <td align=right id=mo>Responsable</td> 
    <td align=right id=mo>Efector</td>      
  </tr>
  <?   
  while (!$result->EOF) {?>  
    <tr>     
     <td ><?=($result->fields['afiapellido']!='')?$result->fields['afiapellido']:$result->fields['apellido'];?></td>  
	 <td ><?=($result->fields['afinombre']!='')?$result->fields['afinombre']:$result->fields['nombre'];?></td>    
     <td ><?=($result->fields['afidni']!='')?$result->fields['afidni']:$result->fields['documento'];?></td>    
     <td ><?if($result->fields['afisexo']=='') echo $result->fields['sexo']; else echo $result->fields['afisexo']?></td>
	 <td ><?=($result->fields['afifechanac']!='')?$result->fields['afifechanac']:$result->fields['fecha_nac'];?></td>  
	 <td ><?=($result->fields['afidomlocalidad']!='')?$result->fields['afidomlocalidad']:$result->fields['domicilio'];?></td>  
     <td ><?=$result->fields['nom_vacum']?></td>
     <td ><?=$result->fields['dosis']?></td>
     <td ><?=fecha($result->fields['fecha_vac'])?></td>         
     <td ><?=fecha($result->fields['fecha_vac_prox'])?></td>         
     <td ><?=$result->fields['lote']?></td>    
     <td ><?=$result->fields['email']?></td>    
     <td ><?=$result->fields['nom_resp']?></td>    
     <td ><?=$result->fields['nom_efector'].'-'.$res_comprobante->fields['cuie']?></td>  
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>
