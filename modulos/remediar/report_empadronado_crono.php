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

/*if ($_POST['generar_excel']=="Generar Excel"){
	$periodo=$_POST['periodo'];
	$link=encode_link("fichero_ind_excel.php",array("fecha_desde"=>$_POST['fecha_desde'],"fecha_hasta"=>$_POST['fecha_hasta'],"cuie"=>$_POST['cuie']));
	?>
	<script>
	window.open('<?=$link?>')
	</script>	
<?}*/

if ($_POST['muestra']=="Muestra"){
	$cuie=$_POST['cuie'];
	$fecha_desde=Fecha_db($_POST['fecha_desde']);
	$fecha_hasta=Fecha_db($_POST['fecha_hasta']);
		if($cuie!='Todos'){
			$sql_tmp="SELECT 
						beneficiarios.id_beneficiarios, 
						remediar_x_beneficiario.id_r_x_b,
						beneficiarios.clave_beneficiario,
						beneficiarios.numero_doc,
						beneficiarios.apellido_benef, 
						beneficiarios.nombre_benef, 
						beneficiarios.fecha_nacimiento_benef, 
						beneficiarios.sexo, 
						beneficiarios.estado_envio, 
						remediar_x_beneficiario.nroformulario,
						remediar_x_beneficiario.fechaempadronamiento,
						remediar_x_beneficiario.fecha_carga,
						remediar_x_beneficiario.enviado,
			      		formulario.puntaje_final					
						FROM uad.remediar_x_beneficiario
						inner join uad.beneficiarios ON (remediar_x_beneficiario.clavebeneficiario=beneficiarios.clave_beneficiario)
			      		inner join remediar.formulario ON (remediar_x_beneficiario.nroformulario=formulario.nroformulario)
						where (centro_inscriptor='$cuie') and (puntaje_final>=4)and (fechaempadronamiento BETWEEN '$fecha_desde' and '$fecha_hasta')";
}else {
			$sql_tmp="SELECT 
						beneficiarios.id_beneficiarios, 
						remediar_x_beneficiario.id_r_x_b,
						beneficiarios.clave_beneficiario,
						beneficiarios.numero_doc,
						beneficiarios.apellido_benef, 
						beneficiarios.nombre_benef, 
						beneficiarios.fecha_nacimiento_benef, 
						beneficiarios.sexo, 
						beneficiarios.estado_envio, 
						remediar_x_beneficiario.nroformulario,
						remediar_x_beneficiario.fechaempadronamiento,
						remediar_x_beneficiario.fecha_carga,
						remediar_x_beneficiario.enviado,
			      		formulario.puntaje_final					
						FROM uad.remediar_x_beneficiario
						inner join uad.beneficiarios ON (remediar_x_beneficiario.clavebeneficiario=beneficiarios.clave_beneficiario)
			      		inner join remediar.formulario ON (remediar_x_beneficiario.nroformulario=formulario.nroformulario)
			      		where (puntaje_final>=4) and (fechaempadronamiento BETWEEN '$fecha_desde' and '$fecha_hasta')";
							
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

<form name=form1 action="report_empadronado_crono.php" method=POST>
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
			<!--<input type="submit" value="Generar Excel" name="generar_excel">-->
    	  </b>     
	  </td>
     </tr>
</table>

<?if ($_POST['muestra']){?>
<table border=0 width=90% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=10 align=left id=ma>
     <table width=90%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$res_comprobante->recordCount()?> </td>       
      </tr>
    </table>
   </td>
  </tr>
    <td align=right id=mo>Clave Beneficiario</td>    	    
    <td align=right id=mo>Documento</td>      	    
    <td align=right id=mo>Apellido</td>      	    
    <td align=right id=mo>Nombre</td>
    <td align=right id=mo>F NAC</td>
    <td align=right id=mo>F Emp R+R</td>
    <td align=right id=mo>F Carga R+R</td>
    <td align=right id=mo>Score</td>	 			 		

  </tr>
 <?while (!$res_comprobante->EOF) {?>  	
      <tr <?=atrib_tr()?> >     
		 		<td onclick="<?=$onclick_elegir?>"><?=$res_comprobante->fields['clave_beneficiario']?></td>
			     <td onclick="<?=$onclick_elegir?>"><?=$res_comprobante->fields['numero_doc']?></td>        
			     <td onclick="<?=$onclick_elegir?>"><?=$res_comprobante->fields['apellido_benef']?></td>     
			     <td onclick="<?=$onclick_elegir?>"><?=$res_comprobante->fields['nombre_benef']?></td>     
			     <td onclick="<?=$onclick_elegir?>"><?=fecha($res_comprobante->fields['fecha_nacimiento_benef'])?></td>
			     <td onclick="<?=$onclick_elegir?>"><?=fecha($res_comprobante->fields['fechaempadronamiento'])?></td>
			     <td onclick="<?=$onclick_elegir?>"><?=fecha($res_comprobante->fields['fecha_carga'])?></td>
			     <td onclick="<?=$onclick_elegir?>"><?=$res_comprobante->fields['puntaje_final']?></td>
		</tr>
	<?$res_comprobante->MoveNext();
   }?>
    
    
</table>
<?}?>
<br>
	
</td>
</table>

</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>
