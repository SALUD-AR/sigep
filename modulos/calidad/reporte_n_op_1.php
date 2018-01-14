<?php
require_once("../../config.php");
extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
echo $html_header;
?>
<script>
function control_nuevos()
{
 if(document.all.cuie.value=="-1"){
  alert('Debe Seleccionar un efector');
  return false;
 }
 if(document.all.periodo_desde.value=="-1"){
  alert('Debe Seleccionar un Periodo Desde');
  return false;
 } 
 if(document.all.periodo_hasta.value=="-1"){
	alert('Debe Seleccionar un Periodo Hasta');
	return false;
 } 
 if(document.all.patologia.value=="-1"){
	 alert('Debe Seleccionar un Diagnostico');
	 return false;
 } 
 if(document.all.periodo_desde.value>=document.all.periodo_hasta.value){
	 alert('El Periodo Hasta debe ser mayor al Periodo Desde');
	 return false;
 } 
} 
</script>
<form name=form1 action="reporte_n_op_1.php" method=POST>
<table cellspacing=2 cellpadding=2 width=50% align=center class=bordes>
<br>
    <tr id="mo">
    	<td align="center" colspan="2" class=bordes>
    		<b><font size="+1">Diagnostico Agrupado por Efector</font></b>
    	</td>    	
    </tr>
    
    <tr>
		<td align="right" class=bordes><b>Efector:</b></td>
		<td align="left" class=bordes>		          			
		<select name=cuie Style="width=400px" 
        	onKeypress="buscar_combo(this);"
			onblur="borrar_buffer();"
			onchange="borrar_buffer();document.forms[0].submit();">
			<option value=-1>Seleccione</option>
     		<?$sql= "select cuie, nombre, com_gestion from nacer.efe_conv order by nombre";
			$res_efectores=sql($sql) or fin_pagina();
			$cuie_aux=$res_efectores->fields['cuie'];
			 while (!$res_efectores->EOF){ 
			 	$cuiel=$res_efectores->fields['cuie'];
			    $nombre_efector=$res_efectores->fields['nombre'];?>
				<option value='<?=$cuiel?>' <?if ($cuie==$cuiel) echo "selected"?>><?=$cuiel." - ".$nombre_efector?></option>
			    <?$res_efectores->movenext();
			    }?>
			</select>
		</td>
	</tr>
    
    <tr>
		<td align="right" class=bordes><b>Periodo Desde:</b></td>
		<td align="left" class=bordes>		          			
			<select name=periodo_desde Style="width=400px" <?=$disabled?>>
				<option value=-1>Seleccione</option>
			  	<?$sql = "select * from facturacion.periodo order by periodo";
			  	$result=sql($sql,"No se puede traer el periodo");
			  	while (!$result->EOF) {?>
			  		<option value=<?=$result->fields['periodo']?> <?if ($periodo_desde==$result->fields['periodo']) echo "selected"?>><?=$result->fields['periodo']?></option>
				  <?$result->movenext();
			  	}?>			  
			</select>
		</td>
	</tr>
    <tr>
		<td align="right" class=bordes><b>Periodo Hasta:</b></td>
		<td align="left" class=bordes>		          			
			<select name=periodo_hasta Style="width=400px" <?=$disabled?>>
				<option value=-1>Seleccione</option>
			  	<?$sql = "select * from facturacion.periodo order by periodo";
			  	$result=sql($sql,"No se puede traer el periodo");
			  	while (!$result->EOF) {?>
			  		<option value=<?=$result->fields['periodo']?> <?if ($periodo_hasta==$result->fields['periodo']) echo "selected"?>><?=$result->fields['periodo']?></option>
				  <?$result->movenext();
			  	}?>			  
			</select>
		</td>
	</tr>
					 	<tr>
					 		<td align="right" class=bordes>					 			
					 			<b>Diagnostico: </b>
					    	</td>
					    	<td align="left" class=bordes>
					     		<select name=patologia Style="width=400px"
					 				onKeypress="buscar_combo(this);"
					 				onblur="borrar_buffer();"
					 				onchange="borrar_buffer(); document.forms[0].submit();">
				     				<option value=-1>Seleccione</option>
			                	 	<? 
			                	 	$tabla_consulta_diag="nomenclador.patologias_frecuentes";
			                	 			                	 	
			                	 	$sql= "SELECT * 
				                	 			FROM $tabla_consulta_diag 
				                	 			
				                	 			order by codigo";
			                	 	
			                 
			                	 	$res_efectores=sql($sql) or fin_pagina();
			                	 	while (!$res_efectores->EOF){ 
			                 			$descripcion=$res_efectores->fields['descripcion'];
			                 			$codigo=$res_efectores->fields['codigo'];
			                 			$color_diag=$res_efectores->fields['color'];?>
			                   			<option value=<?=$codigo;?> <?php if ($patologia==$codigo) echo "selected"?> <?=$color_diag?>> 
			                  	 		<?=$codigo." - ".$descripcion?></option>
			                 			<?$res_efectores->movenext();
			                	 	}?>
			      				</select>
			      			</td>
			      			
					 	</tr>
	
		
    <tr>
    	<td align="center" colspan="2" class=bordes id="mo">
    		<input type="submit" name="aceptar" value="Aceptar" onclick="return control_nuevos()" Style="width=200px">
    	</td>    	
    </tr>    
</table>
<br>
<?if($_POST["aceptar"]=="Aceptar"){?>
<table border=1 width="95%" align="center" cellpadding="3" cellspacing='0' bgcolor=<?=$bgcolor3?>>
<div align="center">
	<tr>
    	<td align="center" colspan="2" class=bordes id="ma">  
    		<?$link_excel_1=encode_link("reporte_n_op_1_excel_1.php",array("cuie"=>$cuie, "periodo_desde"=>$periodo_desde, "periodo_hasta"=>$periodo_hasta, "patologia"=>$patologia));
    		  $link_excel_2=encode_link("reporte_n_op_1_excel_2.php",array("cuie"=>$cuie, "periodo_desde"=>$periodo_desde, "periodo_hasta"=>$periodo_hasta, "patologia"=>$patologia));
			  echo "Reporte Detallado: <a target='_blank' href='".$link_excel_1."' title='Las prestaciones que tiene Numero de Factura son a BENEFICIARIOS y estan PAGADAS, las demas son prestaciones a pacientes NO Activos o NO Empadronados'><IMG src='$html_root/imagenes/excel.gif' height='35' width='35' border='0'></a>";
		      echo "&nbsp&nbsp Reporte Agrupado: <a target='_blank' href='".$link_excel_2."' title='Reporte Agrupado'><IMG src='$html_root/imagenes/excel.gif' height='35' width='35' border='0'></a>";?> 
			
		</td>    	
    </tr>  
    
    <tr>
    	<td align="center" colspan="2" class=bordes id="ma">  
    		<?
    		$query="SELECT periodo FROM facturacion.periodo 
			WHERE ('$periodo_desde' <= periodo AND periodo <= '$periodo_hasta')
			order by periodo";
			$periodos=sql($query) or fin_pagina();
			
			$i=0;
			$periodos->Movefirst();
			while (!$periodos->EOF){
				$periodos_label_array[$i]=sprintf("%s",$periodos->fields['periodo']);
				
				$periodo_aux=str_replace('/','-',$periodos->fields['periodo']);				
				$query="SELECT count (comprobante.id_comprobante) as total_periodo  
						FROM
						  facturacion.comprobante
						LEFT JOIN nomenclador.prestaciones_n_op ON (facturacion.comprobante.id_comprobante = nomenclador.prestaciones_n_op.id_comprobante)
						LEFT JOIN nomenclador.patologias ON (nomenclador.prestaciones_n_op.patologia = nomenclador.patologias.codigo)
						LEFT JOIN nacer.efe_conv ON (comprobante.cuie = nacer.efe_conv.cuie)
						WHERE
						  (nomenclador.prestaciones_n_op.patologia='$patologia')and(nacer.efe_conv.cuie='$cuie')
						  and (facturacion.comprobante.fecha_comprobante :: varchar ilike '$periodo_aux%')";
				$total_periodo_res=sql($query) or fin_pagina();
				$periodos_array[$i]=abs($total_periodo_res->fields['total_periodo']);
				
				$periodo_aux_ant_aux=str_replace('/','-',$periodos->fields['periodo']);
				$periodo_aux_ant=(substr($periodo_aux_ant_aux,0,4)-1).substr($periodo_aux_ant_aux,4,3);
				$query_ant="SELECT count (comprobante.id_comprobante) as total_periodo  
						FROM
						  facturacion.comprobante
						LEFT JOIN nomenclador.prestaciones_n_op ON (facturacion.comprobante.id_comprobante = nomenclador.prestaciones_n_op.id_comprobante)
						LEFT JOIN nomenclador.patologias ON (nomenclador.prestaciones_n_op.patologia = nomenclador.patologias.codigo)
						LEFT JOIN nacer.efe_conv ON (comprobante.cuie = nacer.efe_conv.cuie)
						WHERE
						  (nomenclador.prestaciones_n_op.patologia='$patologia')and(nacer.efe_conv.cuie='$cuie')
						  and (facturacion.comprobante.fecha_comprobante :: varchar ilike '$periodo_aux_ant%')";
				$total_periodo_res_ant=sql($query_ant) or fin_pagina();
				$periodos_array_ant[$i]=abs($total_periodo_res_ant->fields['total_periodo']);
				
				$i++;
				$periodos->MoveNext();
			}
								
    		$link_s=encode_link("reporte_n_op_1_grp_small_3.php",array("periodos_array_ant"=>$periodos_array_ant,"periodos_label_array"=>$periodos_label_array,"periodos_array"=>$periodos_array));
			$link_l=encode_link("reporte_n_op_1_grp_large_3.php",array("periodos_array_ant"=>$periodos_array_ant,"periodos_label_array"=>$periodos_label_array,"periodos_array"=>$periodos_array));
			//ACA IMPRIME EL GRAFICO EN LA PAGINA ACTUAL REDIRECCIONA EL LINK CON AL GRAFICO CHICO Y LO IMPRIME
			echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";?> 	    
		</td>    	
    </tr> 
    
    <tr>
    	<td align="center" colspan="2" class=bordes id="ma">  
    		<?
    		$query="SELECT periodo FROM facturacion.periodo 
			WHERE ('$periodo_desde' <= periodo AND periodo <= '$periodo_hasta')
			order by periodo";
			$periodos=sql($query) or fin_pagina();
			
			$i=0;
			$periodos->Movefirst();
			while (!$periodos->EOF){
				$periodos_label_array[$i]=sprintf("%s",$periodos->fields['periodo']);
				
				$periodo_aux=str_replace('/','-',$periodos->fields['periodo']);				
				$query="SELECT count (comprobante.id_comprobante) as total_periodo  
						FROM
						  facturacion.comprobante
						LEFT JOIN nomenclador.prestaciones_n_op ON (facturacion.comprobante.id_comprobante = nomenclador.prestaciones_n_op.id_comprobante)
						LEFT JOIN nomenclador.patologias ON (nomenclador.prestaciones_n_op.patologia = nomenclador.patologias.codigo)
						LEFT JOIN nacer.efe_conv ON (comprobante.cuie = nacer.efe_conv.cuie)
						WHERE
						  (comprobante.activo='S')and
						  (nomenclador.prestaciones_n_op.patologia='$patologia')and(nacer.efe_conv.cuie='$cuie')and 
						  (facturacion.comprobante.fecha_comprobante :: varchar ilike '$periodo_aux%')";
				$total_periodo_res=sql($query) or fin_pagina();
				$periodos_array[$i]=abs($total_periodo_res->fields['total_periodo']);
				
				$query_ant="SELECT count (comprobante.id_comprobante) as total_periodo  
						FROM
						  facturacion.comprobante
						LEFT JOIN nomenclador.prestaciones_n_op ON (facturacion.comprobante.id_comprobante = nomenclador.prestaciones_n_op.id_comprobante)
						LEFT JOIN nomenclador.patologias ON (nomenclador.prestaciones_n_op.patologia = nomenclador.patologias.codigo)
						LEFT JOIN nacer.efe_conv ON (comprobante.cuie = nacer.efe_conv.cuie)
						WHERE
						  ((comprobante.activo='N')or(comprobante.activo is NULL))and
						  (nomenclador.prestaciones_n_op.patologia='$patologia')and(nacer.efe_conv.cuie='$cuie')
						  and (facturacion.comprobante.fecha_comprobante :: varchar ilike '$periodo_aux%')";
				$total_periodo_res_ant=sql($query_ant) or fin_pagina();
				$periodos_array_ant[$i]=abs($total_periodo_res_ant->fields['total_periodo']);
				
				$i++;
				$periodos->MoveNext();
			}
								
    		$link_s=encode_link("reporte_n_op_1_grp_small_4.php",array("periodos_array_ant"=>$periodos_array_ant,"periodos_label_array"=>$periodos_label_array,"periodos_array"=>$periodos_array));
			$link_l=encode_link("reporte_n_op_1_grp_large_4.php",array("periodos_array_ant"=>$periodos_array_ant,"periodos_label_array"=>$periodos_label_array,"periodos_array"=>$periodos_array));
			//ACA IMPRIME EL GRAFICO EN LA PAGINA ACTUAL REDIRECCIONA EL LINK CON AL GRAFICO CHICO Y LO IMPRIME
			echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";?> 	    
		</td>    	
    </tr> 
    
    <tr>
    	<td align="center" colspan="2" class=bordes id="ma">  
    		<?
    		function ultimoDia($mes,$ano){ 
			    $ultimo_dia=28; 
			    $ultimo_dia=date("t", mktime(0, 0, 0, intval($mes), 1, intval($ano)));
			    return $ultimo_dia; 
			} 
			$fecha_desde=ereg_replace('/','-',$periodo_desde."/01");
			$fecha_hasta=ereg_replace('/','-',$periodo_hasta."/".ultimoDia(substr($periodo_hasta,5,2),substr($periodo_hasta,0,4)));
			
			$query1="SELECT 
			  b.categoria as obj_pres,
			  count (comprobante.id_comprobante) as total_obj_pres
			FROM
			  facturacion.comprobante
			  LEFT JOIN nomenclador.prestaciones_n_op ON (facturacion.comprobante.id_comprobante = nomenclador.prestaciones_n_op.id_comprobante)
			  LEFT JOIN nomenclador.grupo_prestacion b ON (nomenclador.prestaciones_n_op.tema = b.codigo)
			  LEFT JOIN nomenclador.patologias ON (nomenclador.prestaciones_n_op.patologia = nomenclador.patologias.codigo)
			  LEFT JOIN nacer.efe_conv ON (comprobante.cuie = nacer.efe_conv.cuie)
			WHERE
			  ( nomenclador.prestaciones_n_op.patologia='$patologia') and (nacer.efe_conv.cuie='$cuie')
			   and ('$fecha_desde' <= facturacion.comprobante.fecha_comprobante and facturacion.comprobante.fecha_comprobante <= '$fecha_hasta')
			GROUP BY b.categoria,prestaciones_n_op.tema
			ORDER BY obj_pres";
			$f_res1=sql($query1);
			
			$i=0;
			$f_res1->Movefirst();
			while (!$f_res1->EOF){
				$desc1[$i]=substr($f_res1->fields['obj_pres'],0,33);
				$val[$i]=$f_res1->fields['total_obj_pres'];
				$i++;
				$f_res1->movenext();
			}
								
    		$link_s=encode_link("reporte_n_op_1_grp_small_1.php",array("desc1"=>$desc1,"val"=>$val));
			$link_l=encode_link("reporte_n_op_1_grp_large_1.php",array("desc1"=>$desc1,"val"=>$val));
			//ACA IMPRIME EL GRAFICO EN LA PAGINA ACTUAL REDIRECCIONA EL LINK CON AL GRAFICO CHICO Y LO IMPRIME
			echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";?> 	    
		</td>    	
    </tr>
     
    <tr>
    	<td align="center"class=bordes id="ma">   	
			<?
			$query2="SELECT 
						a.categoria as nom_pres,			  
						count (comprobante.id_comprobante) as total_pres
					FROM
						facturacion.comprobante
					LEFT JOIN nomenclador.prestaciones_n_op ON (facturacion.comprobante.id_comprobante = nomenclador.prestaciones_n_op.id_comprobante)
					LEFT JOIN nomenclador.grupo_prestacion a ON (nomenclador.prestaciones_n_op.prestacion = a.codigo)
					LEFT JOIN nomenclador.patologias ON (nomenclador.prestaciones_n_op.patologia = nomenclador.patologias.codigo)
					LEFT JOIN nacer.efe_conv ON (comprobante.cuie = nacer.efe_conv.cuie)
					WHERE
					  ( nomenclador.prestaciones_n_op.patologia='$patologia') and (nacer.efe_conv.cuie='$cuie')
					   and ('$fecha_desde' <= facturacion.comprobante.fecha_comprobante and facturacion.comprobante.fecha_comprobante <= '$fecha_hasta')
					GROUP BY a.categoria,prestaciones_n_op.prestacion
					ORDER BY nom_pres";
			$f_res2=sql($query2);
			
			$i=0;
			$f_res2->Movefirst();
			while (!$f_res2->EOF){
				$desc_completa[$i]=$f_res2->fields['nom_pres'];
				$val_pres[$i]=$f_res2->fields['total_pres'];
				$i++;
				$f_res2->movenext();
			}
			//genera dos link a la pagina donde se genera el grafico (uno chico y otro grande)
			$link_s=encode_link("reporte_n_op_1_grp_small_2.php",array("desc_completa"=>$desc_completa,"val_pres"=>$val_pres));
			$link_l=encode_link("reporte_n_op_1_grp_large_2.php",array("desc_completa"=>$desc_completa,"val_pres"=>$val_pres));
			//ACA IMPRIME EL GRAFICO EN LA PAGINA ACTUAL REDIRECCIONA EL LINK CON AL GRAFICO CHICO Y LO IMPRIME
			echo "<a href='$link_l' target='_blank'><img src='$link_s'  border=0 align=top></a>\n";	?>
		</td>    	
    </tr>  
</div>
</table> <!--finalizo la tabla que contiene el grafico-->
<?}?>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>
