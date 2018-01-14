<?

require_once ("../../config.php");

$usuario1=$_ses_user['id'];
 $efector=$_POST['efector'];
 $periodo=$_POST['periodo'];

if (($efector != '')&&($periodo != '')){
		$sql="SELECT   DISTINCT
				  nacer.smiafiliados.afiapellido AS a,
				  nacer.smiafiliados.afinombre AS b,
				  nacer.smiafiliados.afidni AS c,
				  nacer.smiafiliados.afifechanac AS d,
				  nacer.smiafiliados.afidomlocalidad AS e,  
				  leche.motivo.desc_motivo,
				  leche.producto.desc_producto,
				  detalle_leche.cantidad,
				  leche.detalle_leche.comentario,
				  nacer.efe_conv.cuie as cuie_efe,
				  nacer.efe_conv.nombre as nom_efe
				FROM
				  nacer.smiafiliados
				  INNER JOIN leche.detalle_leche ON (nacer.smiafiliados.id_smiafiliados = leche.detalle_leche.id_smiafiliados)
				  INNER JOIN leche.periodo ON (leche.detalle_leche.id_periodo = leche.periodo.id_periodo)
				  INNER JOIN leche.producto ON (leche.detalle_leche.id_producto = leche.producto.id_producto)
				  INNER JOIN leche.motivo ON (leche.detalle_leche.id_motivo = leche.motivo.id_motivo)
				  INNER JOIN nacer.efe_conv ON nacer.efe_conv.cuie = leche.detalle_leche.cuie
				  LEFT OUTER JOIN sistema.usu_efec ON nacer.efe_conv.cuie = leche.detalle_leche.cuie AND sistema.usu_efec.cuie = nacer.efe_conv.cuie
				  LEFT OUTER JOIN sistema.usuarios ON sistema.usuarios.id_usuario = sistema.usu_efec.id_usuario";
		if (!es_cuie($_ses_user['login']) and $efector == 'todos') $sql.=" where periodo.periodo='$periodo' and sistema.usu_efec.id_usuario=$usuario1";
		else $sql.=" where periodo.periodo='$periodo' and detalle_leche.cuie='$efector'";
		
		$res_comprobante1=sql($sql, "Error al traer los Comprobantes") or fin_pagina();
		
		$a=$res_comprobante1->fields['a'];
		$b=$res_comprobante1->fields['b'];
		$c=$res_comprobante1->fields['c'];
		$d=$res_comprobante1->fields['d'];
		$e=$res_comprobante1->fields['e'];
		$d=$res_comprobante1->fields['d'];
		$e=$res_comprobante1->fields['e'];
		$cuie_efe=$res_comprobante1->fields['cuie_efe'];
		$nom_efe=$res_comprobante1->fields['nom_efe'];
		
		$sql2="
		SELECT DISTINCT
		  leche.beneficiarios.apellido AS a,
		  leche.beneficiarios.nombre AS b,
		  leche.beneficiarios.documento AS c,
		  leche.beneficiarios.fecha_nac AS d,
		  leche.beneficiarios.domicilio AS e,  
		  leche.motivo.desc_motivo,
		  leche.producto.desc_producto,
		  detalle_leche.cantidad,
		  leche.detalle_leche.comentario,
		  nacer.efe_conv.cuie as cuie_efe,
		  nacer.efe_conv.nombre as nom_efe
		FROM
		  leche.beneficiarios
		  INNER JOIN leche.detalle_leche ON (leche.beneficiarios.id_beneficiarios = leche.detalle_leche.id_beneficiarios)
		  INNER JOIN leche.periodo ON (leche.detalle_leche.id_periodo = leche.periodo.id_periodo)
		  INNER JOIN leche.producto ON (leche.detalle_leche.id_producto = leche.producto.id_producto)
		  INNER JOIN leche.motivo ON (leche.detalle_leche.id_motivo = leche.motivo.id_motivo)
		  INNER JOIN nacer.efe_conv ON nacer.efe_conv.cuie = leche.detalle_leche.cuie
		  LEFT OUTER JOIN sistema.usu_efec ON nacer.efe_conv.cuie = leche.detalle_leche.cuie AND sistema.usu_efec.cuie = nacer.efe_conv.cuie
		  LEFT OUTER JOIN sistema.usuarios ON sistema.usuarios.id_usuario = sistema.usu_efec.id_usuario";
			if (!es_cuie($_ses_user['login']) and $efector == 'todos') $sql2.=" where periodo.periodo='$periodo' and sistema.usu_efec.id_usuario=$usuario1";
			else $sql2.=" where periodo.periodo='$periodo' and detalle_leche.cuie='$efector'";
		$res_comprobante2=sql($sql2, "Error al traer los Comprobantes") or fin_pagina();
		
		$a=$res_comprobante2->fields['a'];
		$b=$res_comprobante2->fields['b'];
		$c=$res_comprobante2->fields['c'];
		$d=$res_comprobante2->fields['d'];
		$e=$res_comprobante2->fields['e'];
		$cuie_efe=$res_comprobante2->fields['cuie_efe'];
		$nom_efe=$res_comprobante2->fields['nom_efe'];
}



echo $html_header;
?>
<form name='form1' action='reporte_beneficiarios.php' method='POST'>
<table width="80%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 					    
				 		<tr>
					    <td align="right">
					    	<b>Efector:</b>
					    </td>
					    <td align="left">		          			
				 			<select name=efector Style="width=450px"
				 			onKeypress="buscar_combo(this);"
				 			onblur="borrar_buffer();"
				 			onchange="borrar_buffer();"
           					> 
				 			<?if (es_cuie($_ses_user['login'])){
								$cuie_cons=$_ses_user['login'];
								$sql= "select cuie, nombre, com_gestion from nacer.efe_conv where cuie='$cuie_cons' order by nombre";
									   }									
									  else{
			                		$usuario1=$_ses_user['id'];
									$sql= "select nacer.efe_conv.nombre, nacer.efe_conv.cuie, com_gestion 
											from nacer.efe_conv 
											join sistema.usu_efec on (nacer.efe_conv.cuie = sistema.usu_efec.cuie) 
											join sistema.usuarios on (sistema.usu_efec.id_usuario = sistema.usuarios.id_usuario) 
											where sistema.usuarios.id_usuario = '$usuario1'
											order by nombre";
									?>
							<option value=todos>Todos</option>	
							<?}
			                 $res_efectores=sql($sql) or fin_pagina();
			                 while (!$res_efectores->EOF){ 								
			                 	$cuie1=$res_efectores->fields['cuie'];
			                 	$nombre_efector=$res_efectores->fields['nombre'];								
			                 ?>
			                   <option value=<?=$cuie1;?> <?if ($efector==$cuie1) echo "selected"?>><?=$cuie1." - ".$nombre_efector?></option>
			                 <?
			                 $res_efectores->movenext();
			                 }
			                 ?>
			      			</select>
					    </td>
					 </tr>	 
					 					 
		<tr>
         	<td align="right">
				<b>Periodo:</b>
			</td>
			<td align="left">		          			
			 <select name=periodo Style="width=450px" >
			 <option value=-1>Seleccione</option>
			  <?
			  $sql = "select * from leche.periodo order by periodo";
			  $result=sql($sql,"No se puede traer el periodo");
			  while (!$result->EOF) {?>
			  			  
			  <option value=<?=$result->fields['periodo']?> <?if ($periodo==$result->fields['periodo']) echo "selected"?>><?=$result->fields['periodo']?></option>
			  <?
			  $result->movenext();
			  }
			  ?>			
			  </select>
			</td>
         </tr>  
         
          <tr>
					    <td align="center" colspan="2">
					    	<input type="submit" value="Buscar" name="Buscar">
					    	&nbsp;&nbsp;&nbsp;
					    	<? $link=encode_link("reporte_beneficiarios_excel.php",array("periodo"=>$periodo,"efector"=>$efector));?>
        					<img src="../../imagenes/excel.gif" style='cursor:hand;'  onclick="window.open('<?=$link?>')">
					    </td>
					    
					 </tr>	
	 </table>	
 
 
<br>
<tr><td><table width="100%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes" >
	<?
	if (($efector != '')&&($periodo != '')){
	if (($res_comprobante1->RecordCount()==0)&&($res_comprobante2->RecordCount()==0)){?>
	 <tr>
	  <td align="center">
	   <font size="3" color="Red"><b>No existen beneficiarios para este periodo y CAPS</b></font>
	  </td>
	 </tr>
	 <?}
	 else{	 	
	 	?>
	 	<tr id="sub_tabla">	 
	 		<td >Efector</td>	    
	 		<td >Apellido</td>
	 		<td >Nombre</td>
	 		<td >DNI</td>
	 		<td >Fecha Nacimiento</td>
	 		<td >Localidad</td>
	 		<td >Motivo</td>
	 		<td >Producto</td>
	 		<td >Cantidad</td>
	 		<td >Comentario</td>
	 	</tr>
	 	<?
	 	$res_comprobante1->movefirst();
	 		while (!$res_comprobante1->EOF){?>
	 		<tr <?=atrib_tr()?>>	 		
	 			<td><?=$res_comprobante1->fields['cuie_efe'].'-'.$res_comprobante1->fields['nom_efe']?></td>		 		
		 		<td><?=$res_comprobante1->fields['a']?></td>		
		 		<td><?=$res_comprobante1->fields['b']?></td>		 			 		
		 		<td><?=$res_comprobante1->fields['c']?></td>		 		
		 		<td><?=Fecha($res_comprobante1->fields['d'])?></td>		 		
		 		<td><?=$res_comprobante1->fields['e']?></td>		 	 		
		 		<td><?=$res_comprobante1->fields['desc_motivo']?></td>		 		
		 		<td><?=$res_comprobante1->fields['desc_producto']?></td>		 				
		 		<td><?=$res_comprobante1->fields['cantidad']?></td>		 				
		 		<td><?=$res_comprobante1->fields['comentario']?></td>		 				
		 	</tr>	
		 	
	 		<?$res_comprobante1->movenext();
	 	 }
	 	 $res_comprobante2->movefirst();
	 		while (!$res_comprobante2->EOF){?>
	 		<tr <?=atrib_tr()?>>	 			
	 			<td><?=$res_comprobante2->fields['cuie_efe'].'-'.$res_comprobante2->fields['nom_efe']?></td>	
		 		<td><?=$res_comprobante2->fields['a']?></td>		 		
		 		<td><?=$res_comprobante2->fields['b']?></td>		 		
		 		<td><?=$res_comprobante2->fields['c']?></td>		 		
		 		<td><?=Fecha($res_comprobante2->fields['d'])?></td>		 		
		 		<td><?=$res_comprobante2->fields['e']?></td>		 	 		
		 		<td><?=$res_comprobante2->fields['desc_motivo']?></td>		 		
		 		<td><?=$res_comprobante2->fields['desc_producto']?></td>		 				
		 		<td><?=$res_comprobante2->fields['cantidad']?></td>		
		 		<td><?=$res_comprobante1->fields['comentario']?></td>	 				
		 	</tr>	
		 	
	 		<?$res_comprobante2->movenext();
	 	 }
	 	}
	}
	 ?>
	 
</table></td></tr>

<br>
<tr><td><table width="100%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes" >
	<?
	if (($efector != '')&&($periodo != '')){
	if (($res_comprobante1->RecordCount()==0)&&($res_comprobante2->RecordCount()==0)){?>
	 <tr>
	  <td align="center">
	   <font size="3" color="Red"><b>No existen beneficiarios para este periodo y CAPS</b></font>
	  </td>
	 </tr>
	 <?}
	 else{	
	 	$sql="SELECT
				Sum(leche.detalle_leche.cantidad) AS total
				FROM
				leche.detalle_leche
				INNER JOIN leche.periodo ON (leche.detalle_leche.id_periodo = leche.periodo.id_periodo)
				";
			if (!es_cuie($_ses_user['login']) and $efector == 'todos') $sql.=" INNER JOIN sistema.usu_efec ON sistema.usu_efec.cuie = leche.detalle_leche.cuie
																			  INNER JOIN sistema.usuarios ON sistema.usuarios.id_usuario = sistema.usu_efec.id_usuario
																			  where leche.periodo.periodo='$periodo' and sistema.usu_efec.id_usuario=$usuario1";
			else $sql.=" where leche.periodo.periodo='$periodo' and leche.detalle_leche.cuie='$efector'";
			
			$res_comprobante1=sql($sql, "Error al traer los Comprobantes") or fin_pagina(); 	
			
			$sql="SELECT
					Sum(leche.detalle_leche.cantidad) AS total,
					leche.producto.desc_producto
					FROM
					leche.producto
					INNER JOIN leche.detalle_leche ON leche.producto.id_producto = leche.detalle_leche.id_producto
					INNER JOIN leche.periodo ON leche.detalle_leche.id_periodo = leche.periodo.id_periodo
					";
			if (!es_cuie($_ses_user['login']) and  $efector == 'todos') $sql.=" INNER JOIN sistema.usu_efec ON sistema.usu_efec.cuie = leche.detalle_leche.cuie
																				INNER JOIN sistema.usuarios ON sistema.usuarios.id_usuario = sistema.usu_efec.id_usuario 
																				where periodo.periodo='$periodo' and sistema.usu_efec.id_usuario=$usuario1 group by desc_producto ";
			else $sql.=" where periodo.periodo='$periodo' and detalle_leche.cuie='$efector' group by desc_producto";
			$total_por_producto=sql($sql, "Error al traer los Comprobantes") or fin_pagina(); 	
	 	?>
	 	<tr id="sub_tabla">	 	    
	 		<td colspan="2" >Resumen Agrupado por Producto (Cuenta Cantidad de Cajas de Leche)</td>	 		
	 	</tr>
	 	<?
		$total_por_producto->movefirst();
	 	while (!$total_por_producto->EOF){?>
	 	<tr <?=atrib_tr()?>>	 	    
	 		<td ><?=$total_por_producto->fields['desc_producto']?></td>
	 		<td ><?=$total_por_producto->fields['total']?></td>	 		
	 	</tr>
	 	<?$total_por_producto->movenext();
	 	}?>
	 	<tr <?=atrib_tr()?>>	 	    
	 		<td >Total</td>
	 		<td ><?=$res_comprobante1->fields['total']?></td>	 		
	 	</tr>
	 	
	 	<?	 	
	 	}
	}
	 ?>
	 
</table></td></tr>

<br>
<tr><td><table width="100%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes" >
	<?
	if (($efector != '')&&($periodo != '')){
	if (($res_comprobante1->RecordCount()==0)&&($res_comprobante2->RecordCount()==0)){?>
	 <tr>
	  <td align="center">
	   <font size="3" color="Red"><b>No existen beneficiarios para este periodo y CAPS</b></font>
	  </td>
	 </tr>
	 <?}
	 else{	
	 	$sql="
			SELECT   
			  sum(detalle_leche.cantidad)as total
			  FROM
				leche.detalle_leche
				INNER JOIN leche.periodo ON (leche.detalle_leche.id_periodo = leche.periodo.id_periodo)
				";
			if (!es_cuie($_ses_user['login']) and $efector == 'todos') $sql.=" INNER JOIN sistema.usu_efec ON sistema.usu_efec.cuie = leche.detalle_leche.cuie
																				INNER JOIN sistema.usuarios ON sistema.usuarios.id_usuario = sistema.usu_efec.id_usuario
																				where leche.periodo.periodo='$periodo' and sistema.usu_efec.id_usuario=$usuario1";
			else $sql.=" where leche.periodo.periodo='$periodo' and leche.detalle_leche.cuie='$efector'";
			$res_comprobante1=sql($sql, "Error al traer los Comprobantes") or fin_pagina(); 	
			
			$sql="SELECT  
					desc_motivo,sum (cantidad)as total
			  		FROM
					leche.motivo
					INNER JOIN leche.detalle_leche ON leche.motivo.id_motivo = leche.detalle_leche.id_motivo
					INNER JOIN leche.periodo ON leche.detalle_leche.id_periodo = leche.periodo.id_periodo
					";
			if (!es_cuie($_ses_user['login']) and  $efector == 'todos') $sql.=" INNER JOIN sistema.usu_efec ON sistema.usu_efec.cuie = leche.detalle_leche.cuie
					INNER JOIN sistema.usuarios ON sistema.usuarios.id_usuario = sistema.usu_efec.id_usuario where periodo.periodo='$periodo' and sistema.usu_efec.id_usuario=$usuario1 group by desc_motivo ";
			else $sql.=" where periodo.periodo='$periodo' and detalle_leche.cuie='$efector' group by desc_motivo";
			$total_por_producto=sql($sql, "Error al traer los Comprobantes") or fin_pagina(); 	
	 	?>
	 	<tr id="sub_tabla">	 	    
	 		<td colspan="2" >Resumen Agrupado por Motivo (Cuenta Cantidad de Cajas de Leche)</td>	 		
	 	</tr>
	 	<?
		$total_por_producto->movefirst();
	 	while (!$total_por_producto->EOF){?>
	 	<tr <?=atrib_tr()?>>	 	    
	 		<td ><?=$total_por_producto->fields['desc_motivo']?></td>
	 		<td ><?=$total_por_producto->fields['total']?></td>	 		
	 	</tr>
	 	<?$total_por_producto->movenext();
	 	}?>
	 	<tr <?=atrib_tr()?>>	 	    
	 		<td >Total</td>
	 		<td ><?=$res_comprobante1->fields['total']?></td>	 		
	 	</tr>
	 	
	 	<?	 	
	 	}
	}
	 ?>
	 
</table></td></tr>


<br>
<tr><td><table width="100%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes" >
	<?
	if (($efector != '')&&($periodo != '')){
	if (($res_comprobante1->RecordCount()==0)&&($res_comprobante2->RecordCount()==0)){?>
	 <tr>
	  <td align="center">
	   <font size="3" color="Red"><b>No existen beneficiarios para este periodo y CAPS</b></font>
	  </td>
	 </tr>
	 <?}
	 else{	
	 	$sql="
			SELECT   
			  count(detalle_leche.cantidad)as total
			FROM
			  leche.detalle_leche 
			  INNER JOIN leche.periodo ON (leche.detalle_leche.id_periodo = leche.periodo.id_periodo)
			  ";
			if (!es_cuie($_ses_user['login']) and  $efector == 'todos') $sql.=" INNER JOIN sistema.usu_efec ON sistema.usu_efec.cuie = leche.detalle_leche.cuie
			  INNER JOIN sistema.usuarios ON sistema.usuarios.id_usuario = sistema.usu_efec.id_usuario where periodo.periodo='$periodo' and sistema.usu_efec.id_usuario=$usuario1";
			else $sql.=" where periodo.periodo='$periodo' and detalle_leche.cuie='$efector'";
	
			$res_comprobante1=sql($sql, "Error al traer los Comprobantes") or fin_pagina(); 	
			
			$sql="SELECT  
					desc_motivo,count (cantidad)as total
				FROM
			  		leche.motivo 
               	INNER JOIN leche.detalle_leche  using (id_motivo)			  
			  	INNER JOIN leche.periodo using (id_periodo)
			  	";
			if (!es_cuie($_ses_user['login']) and  $efector == 'todos') $sql.=" INNER JOIN sistema.usu_efec ON sistema.usu_efec.cuie = leche.detalle_leche.cuie
			INNER JOIN sistema.usuarios ON sistema.usuarios.id_usuario = sistema.usu_efec.id_usuario where periodo.periodo='$periodo' and sistema.usu_efec.id_usuario=$usuario1 group by desc_motivo";
			else $sql.=" where periodo.periodo='$periodo' and detalle_leche.cuie='$efector' group by desc_motivo";
			$total_por_producto=sql($sql, "Error al traer los Comprobantes") or fin_pagina(); 	
	 	?>
	 	<tr id="sub_tabla">	 	    
	 		<td colspan="2" >Resumen Agrupado por Motivo (Cuenta Cantidad de FAMILIAS que Recibieron Prestacion)</td>	 		
	 	</tr>
	 	<?
		$total_por_producto->movefirst();
	 	while (!$total_por_producto->EOF){?>
	 	<tr <?=atrib_tr()?>>	 	    
	 		<td ><?=$total_por_producto->fields['desc_motivo']?></td>
	 		<td ><?=$total_por_producto->fields['total']?></td>	 		
	 	</tr>
	 	<?$total_por_producto->movenext();
	 	}?>
	 	<tr <?=atrib_tr()?>>	 	    
	 		<td >Total</td>
	 		<td ><?=$res_comprobante1->fields['total']?></td>	 		
	 	</tr>
	 	
	 	<?	 	
	 	}
	}
	 ?>
	 
</table></td></tr>

</table>

</form>
<?=fin_pagina();// aca termino ?>
