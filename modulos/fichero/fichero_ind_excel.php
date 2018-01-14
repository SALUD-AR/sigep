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
							fichero.fichero.cuie,
							nacer.efe_conv.nombre as nom_efe,
							fichero.fichero.fecha_control,
							fichero.fichero.nom_medico,
							fichero.fichero.fecha_pcontrol
							FROM
							fichero.fichero
							LEFT OUTER JOIN nacer.smiafiliados ON fichero.fichero.id_smiafiliados = nacer.smiafiliados.id_smiafiliados
							LEFT OUTER JOIN leche.beneficiarios ON leche.beneficiarios.id_beneficiarios = fichero.fichero.id_beneficiarios
							INNER JOIN nacer.efe_conv ON nacer.efe_conv.cuie = fichero.fichero.cuie
						where (fichero.fichero.fecha_pcontrol BETWEEN '$fecha_desde' and '$fecha_hasta') and (nacer.efe_conv.cuie='$cuie')  and (fecha_pcontrol_flag=1)";
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
								fichero.fichero.cuie,
								nacer.efe_conv.nombre as nom_efe,
								fichero.fichero.fecha_control,
								fichero.fichero.nom_medico,
								fichero.fichero.fecha_pcontrol
								FROM
								fichero.fichero
								LEFT OUTER JOIN nacer.smiafiliados ON fichero.fichero.id_smiafiliados = nacer.smiafiliados.id_smiafiliados
								LEFT OUTER JOIN leche.beneficiarios ON leche.beneficiarios.id_beneficiarios = fichero.fichero.id_beneficiarios
								INNER JOIN nacer.efe_conv ON nacer.efe_conv.cuie = fichero.fichero.cuie
							where (fichero.fichero.fecha_pcontrol BETWEEN '$fecha_desde' and '$fecha_hasta') and (fecha_pcontrol_flag=1)";
							
			}
			
$res_comprobante=sql($sql_tmp) or fin_pagina();

excel_header("fichero_ind_excel.xls");
?>
<form name=form1 method=post action="fichero_ind_excel.php">
<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total: </b><?=$res_comprobante->RecordCount();?> 
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table> 
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor=#C0C0FF>   	
    <td align=right id=mo>Documento</td>
    <td align=right id=mo>Nombre</td> 
  	<td align=right id=mo>Apellido</td>      	
    <td align=right id=mo>Sexo</td>
    <td align=right id=mo>Fecha Nacimiento</td>        
    <td align=right id=mo>Domicilio</td> 
    <td align=right id=mo>Fecha de Control</td> 
    <td align=right id=mo>Fecha Proximo Control</td> 
    <td align=right id=mo>Medico Tratante</td> 
    <td align=right id=mo>Efector</td>      
  </tr>
  <?   
  while (!$res_comprobante->EOF) {?>  
    <tr>     
     <td ><?=($res_comprobante->fields['afidni']!='')?$res_comprobante->fields['afidni']:$res_comprobante->fields['documento'];?></td>    
	     <td ><?=($res_comprobante->fields['afinombre']!='')?$res_comprobante->fields['afinombre']:$res_comprobante->fields['nombre'];?></td>    
	     <td ><?=($res_comprobante->fields['afiapellido']!='')?$res_comprobante->fields['afiapellido']:$res_comprobante->fields['apellido'];?></td>  
	     <td ><?=($res_comprobante->fields['afisexo']!='')?$res_comprobante->fields['afisexo']:$res_comprobante->fields['sexo'];?></td>  
	     <td ><?=($res_comprobante->fields['afifechanac']!='')?fecha($res_comprobante->fields['afifechanac']):fecha($res_comprobante->fields['fecha_nac']);?></td>  
	     <td ><?=($res_comprobante->fields['afidomlocalidad']!='')?$res_comprobante->fields['afidomlocalidad']:$res_comprobante->fields['domicilio'];?></td>  
		 <td ><?=fecha($res_comprobante->fields['fecha_control'])?></td>     
	     <td ><?if($res_comprobante->fields['fecha_pcontrol']=='1000-01-01')echo  "&nbsp";else echo fecha($res_comprobante->fields['fecha_pcontrol']);?></td>         
	     <td ><?=$res_comprobante->fields['nom_medico']?></td>    
	     <td ><?=$res_comprobante->fields['nom_efe'].'-'.$res_comprobante->fields['cuie']?></td>    
    </tr>
	<?$res_comprobante->MoveNext();
    }?>
 </table>
 </form>
 <?echo fin_pagina()?>
