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
			$sql_tmp="SELECT seguimiento_remediar.*, nacer.smiafiliados.afiapellido, nacer.smiafiliados.afinombre, nacer.smiafiliados.afidni 
						FROM trazadoras.seguimiento_remediar
						INNER JOIN nacer.smiafiliados ON nacer.smiafiliados.clavebeneficiario = trazadoras.seguimiento_remediar.clave_beneficiario
						where (fecha_comprobante_proximo BETWEEN '$fecha_desde' and '$fecha_hasta') and (efector='$cuie')";
}else {
				$sql_tmp="SELECT seguimiento_remediar.*, nacer.smiafiliados.afiapellido, nacer.smiafiliados.afinombre, nacer.smiafiliados.afidni  
						FROM trazadoras.seguimiento_remediar
						INNER JOIN nacer.smiafiliados ON nacer.smiafiliados.clavebeneficiario = trazadoras.seguimiento_remediar.clave_beneficiario
						where (fecha_comprobante_proximo BETWEEN '$fecha_desde' and '$fecha_hasta')";

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
<form name=form1 action="report_seguimiento_crono.php" method=POST>
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
	 		<td >DNI</td>
	 		<td >Apellido</td>
	 		<td >Nombre</td>
	 		<td >Fecha</td>
	 		<td >Fecha Prox</td>
	 		<td >dtm2</td>
	 		<td >hta</td>
	 		<td >ta_sist</td>
	 		<td >ta_diast</td>
	 		<td >tabaquismo</td>
	 		<td >col_tot</td>
	 		<td >gluc</td>
	 		<td >peso</td>
	 		<td >talla</td>
		    <td >imc </td>
		    <td >hba1c </td>
		    <td >ecg </td>
		    <td >fondodeojo </td>
		    <td >examendepie </td>
	        <td >microalbuminuria </td>
		    <td >hdl </td>
		    <td >ldl </td>
		    <td >tags </td>
	        <td >creatininemia </td>
		    <td >esp1 </td>
		    <td >esp2 </td>
		    <td >esp3 </td>
  	        <td >esp4 </td>
            <td >riesgo_global </td>
            <td >riesgo_globala </td>
            <td >fecha_carga </td>
            <td >usuario</td>
            <td >Comentario</td>    
  </tr>
 <?while (!$res_comprobante->EOF) {?>  	
      <tr <?=atrib_tr()?> >     
		 		<td><?=$res_comprobante->fields['efector']?></td>	
		 		<td><?=$res_comprobante->fields['afidni']?></td>	
		 		<td><?=$res_comprobante->fields['afiapellido']?></td>	
		 		<td><?=$res_comprobante->fields['afinombre']?></td>	
		 		<td><?=fecha($res_comprobante->fields['fecha_comprobante'])?></td>		 		
		 		<td><?=fecha($res_comprobante->fields['fecha_comprobante_proximo'])?></td>		 		
		 		<td><?=$res_comprobante->fields['dtm2']?></td>		 		
		 		<td><?=$res_comprobante->fields['hta']?></td>		 		
		 		<td><?=$res_comprobante->fields['ta_sist']?></td>		 		
		 		<td><?=$res_comprobante->fields['ta_diast']?></td>		 		
		 		<td><?=$res_comprobante->fields['tabaquismo']?></td>		
		 		<td><?=$res_comprobante->fields['col_tot']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['gluc']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['peso']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['talla']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['imc']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['hba1c']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['ecg']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['fondodeojo']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['examendepie']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['microalbuminuria']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['hdl']?></td>	
		 		<td><?=$res_comprobante->fields['ldl']?></td>	
		 		<td><?=$res_comprobante->fields['tags']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['creatininemia']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['esp1']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['esp2']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['esp3']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['esp4']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['riesgo_global']?></td>	
		 		<td><?=$res_comprobante->fields['riesgo_globala']?></td>		 	 		
		 		<td><?=fecha($res_comprobante->fields['fecha_carga'])?></td>		 		
		 		<td><?=$res_comprobante->fields['usuario']?></td>
		 		<td><?=$res_comprobante->fields['comentario']?></td>     
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
