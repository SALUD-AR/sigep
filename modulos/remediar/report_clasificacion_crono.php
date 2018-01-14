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
						nacer.efe_conv.cuie,
						nacer.efe_conv.nombre,
						trazadoras.clasificacion_remediar2.num_doc,
						trazadoras.clasificacion_remediar2.apellido,
						trazadoras.clasificacion_remediar2.nombre AS nom_per,
						trazadoras.clasificacion_remediar2.fecha_nac,
						trazadoras.clasificacion_remediar2.fecha_control,
						trazadoras.clasificacion_remediar2.fecha_prox_seguimiento
						FROM
						trazadoras.clasificacion_remediar2
						INNER JOIN nacer.efe_conv ON trazadoras.clasificacion_remediar2.cuie = nacer.efe_conv.cuie
						where (fecha_prox_seguimiento BETWEEN '$fecha_desde' and '$fecha_hasta') and (efe_conv.cuie='$cuie')";
}else {
			$sql_tmp="SELECT 
						nacer.efe_conv.cuie,
						nacer.efe_conv.nombre,
						trazadoras.clasificacion_remediar2.num_doc,
						trazadoras.clasificacion_remediar2.apellido,
						trazadoras.clasificacion_remediar2.nombre AS nom_per,
						trazadoras.clasificacion_remediar2.fecha_nac,
						trazadoras.clasificacion_remediar2.fecha_control,
						trazadoras.clasificacion_remediar2.fecha_prox_seguimiento
						FROM
						trazadoras.clasificacion_remediar2
						INNER JOIN nacer.
						where (fecha_prox_seguimiento BETWEEN '$fecha_desde' and '$fecha_hasta')";
							
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
<form name=form1 action="report_clasificacion_crono.php" method=POST>
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
    		<td >Efec</td>
    		<td >Nombre Efector</td>
	 		<td >Nro. Doc</td>
	 		<td >Apellido</td>
	 		<td >Nombre</td>
	 		<td >Fecha Clasificacion</td>
	 		<td >Fecha Seguimeinto</td>	 			 		

  </tr>
 <?while (!$res_comprobante->EOF) {?>  	
      <tr <?=atrib_tr()?> >     
		 		<td><?=$res_comprobante->fields['cuie']?></td>	
		 		<td><?=$res_comprobante->fields['nombre']?></td>
		 		<td><?=$res_comprobante->fields['num_doc']?></td>		 				 	 		
		 		<td><?=$res_comprobante->fields['apellido']?></td>		 		
		 		<td><?=$res_comprobante->fields['nom_per']?></td>
		 		<td><?=fecha($res_comprobante->fields['fecha_control'])?></td>		 		
		 		<td><?=fecha($res_comprobante->fields['fecha_prox_seguimiento'])?></td>			 				 		

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
