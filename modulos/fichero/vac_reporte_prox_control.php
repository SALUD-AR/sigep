<?php
/*
$Author: gaby $
$Revision: 1.0 $
$Date: 2012/10/20 15:22:40 $
*/
require_once("../../config.php");
extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

if ($_POST['generar_excel']=="Generar Excel"){
	$periodo=$_POST['periodo'];
	$link=encode_link("vac_reporte_prox_control_excel.php",array("fecha_desde"=>$_POST['fecha_desde'],"fecha_hasta"=>$_POST['fecha_hasta'],"cuie"=>$_POST['cuie']));
	?>
	<script>
	window.open('<?=$link?>')
	</script>	
<?}

if ($_POST['muestra']=="Muestra"){
	$cuie=$_POST['cuie'];
	$fecha_desde=Fecha_db($_POST['fecha_desde']);
	$fecha_hasta=Fecha_db($_POST['fecha_hasta']);
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
			where (trazadoras.vacunas.fecha_vac_prox BETWEEN '$fecha_desde' and '$fecha_hasta') and (nacer.efe_conv.cuie='$cuie') and (trazadoras.vacunas.eliminada=0)  and (fecha_pcontrol_flag=1)";
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
							where (trazadoras.vacunas.fecha_vac_prox BETWEEN '$fecha_desde' and '$fecha_hasta') and (trazadoras.vacunas.eliminada=0) and (fecha_pcontrol_flag=1)";
							
			}
$res_comprobante=sql($sql_tmp,"<br>Error al traer los datos<br>") or fin_pagina();

}
echo $html_header;?>

<script>
function control_muestra()
{ 
 if(document.all.fecha_desde.value==""){
  alert('Debe Ingresar una Fecha DESDE');
  return false;
 } 
 if(document.all.fecha_hasta.value==""){
  alert('Debe Ingresar una Fecha HASTA');
  return false;
 } 

return true;
}
</script>
<form name=form1 action="vac_reporte_prox_control.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
	    	  <b>
			<?if ($fecha_desde=='')$fecha_desde=date("Y-m-d",time()-(30*24*60*60));
			  if ($fecha_hasta=='')$fecha_hasta=date("Y-m-d");?>
	    	  Desde: <input type=text id=fecha_desde name=fecha_desde value='<?=fecha($fecha_desde)?>' size=15 readonly>
				<?=link_calendario("fecha_desde");?>
		
			  Hasta: <input type=text id=fecha_hasta name=fecha_hasta value='<?=fecha($fecha_hasta)?>' size=15 readonly>
				<?=link_calendario("fecha_hasta");?> 
				
			  Efector: 
			 <select name=cuie Style="width=257px" 
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer();"
				onchange="borrar_buffer();">
			 <?$user_login1=substr($_ses_user['login'],0,6);
								  if (es_cuie($_ses_user['login'])){
									$sql1= "select cuie, nombre, com_gestion from nacer.efe_conv where cuie='$user_login1' order by nombre";
								   }									
								  else{
									$usuario1=$_ses_user['id'];
									$sql1= "select nacer.efe_conv.nombre, nacer.efe_conv.cuie, com_gestion 
											from nacer.efe_conv 
											join sistema.usu_efec on (nacer.efe_conv.cuie = sistema.usu_efec.cuie) 
											join sistema.usuarios on (sistema.usu_efec.id_usuario = sistema.usuarios.id_usuario) 
											where sistema.usuarios.id_usuario = '$usuario1'
										 order by nombre";
								   }			 			   
								 $res_efectores=sql($sql1) or fin_pagina();
							 
							 while (!$res_efectores->EOF){ 
								$com_gestion=$res_efectores->fields['com_gestion'];
								$cuie1=$res_efectores->fields['cuie'];
								$nombre_efector=$res_efectores->fields['nombre'];
								if($com_gestion=='FALSO')$color_style='#F78181'; else $color_style='';
								?>
								<option value='<?=$cuie1;?>' Style="background-color: <?=$color_style;?>" <?if ($cuie1==$cuie)echo "selected"?>><?=$cuie1." - ".$nombre_efector?></option>
								<?
								$res_efectores->movenext();
								}?>
			  <option value='Todos' <?if ("Todos"==$cuie)echo "selected"?>>Todos</option>
			</select>
			
			<input type="submit" name="muestra" value='Muestra' onclick="return control_muestra()" >
			<input type="submit" value="Generar Excel" name="generar_excel">
		</b>     
	  </td>
     </tr>
</table>

<?if ($_POST['muestra']){?>
<table border=0 width=90% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=20 align=left id=ma>
     <table width=90%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$res_comprobante->recordCount()?> </td>       
      </tr>
    </table>
   </td>
  </tr>
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
 <? $t=0;
 	$s=0;
   while (!$res_comprobante->EOF) {?>  	
    <tr <?=atrib_tr()?> >     
	 <td ><?=($res_comprobante->fields['afiapellido']!='')?$res_comprobante->fields['afiapellido']:$res_comprobante->fields['apellido'];?></td>  
	 <td ><?=($res_comprobante->fields['afinombre']!='')?$res_comprobante->fields['afinombre']:$res_comprobante->fields['nombre'];?></td>    
     <td ><?=($res_comprobante->fields['afidni']!='')?$res_comprobante->fields['afidni']:$res_comprobante->fields['documento'];?></td>    
     <td ><?if($res_comprobante->fields['afisexo']=='') echo $res_comprobante->fields['sexo']; else echo $res_comprobante->fields['afisexo']?></td>
	 <td ><?=($res_comprobante->fields['afifechanac']!='')?$res_comprobante->fields['afifechanac']:$res_comprobante->fields['fecha_nac'];?></td>  
	 <td ><?=($res_comprobante->fields['afidomlocalidad']!='')?$res_comprobante->fields['afidomlocalidad']:$res_comprobante->fields['domicilio'];?></td>  
     <td ><?=$res_comprobante->fields['nom_vacum']?></td>
     <td ><?=$res_comprobante->fields['dosis']?></td>
     <td ><?=fecha($res_comprobante->fields['fecha_vac'])?></td>         
     <td ><?=fecha($res_comprobante->fields['fecha_vac_prox'])?></td>         
     <td ><?=$res_comprobante->fields['lote']?></td>    
     <td ><?=$res_comprobante->fields['email']?></td>    
     <td ><?=$res_comprobante->fields['nom_resp']?></td>    
     <td ><?=$res_comprobante->fields['nom_efector'].'-'.$res_comprobante->fields['cuie']?></td>    
    </tr>
	<?$res_comprobante->MoveNext();
   }?>
    
    
</table>

<br>
<?}?>
</td>
</table>

</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>
