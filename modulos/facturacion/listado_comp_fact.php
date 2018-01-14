<?php
require_once("../../config.php");

$cuie=$parametros['cuie'] or $cuie=$_POST['cuie'];
$id_factura=$parametros['id_factura'] or $id_factura=$_POST['id_factura'];
$periodo_actual=$parametros['periodo_actual'] or $periodo_actual=$_POST['periodo_actual'];
$alta_comp=$parametros['alta_comp'] or $alta_comp=$_POST['alta_comp'];

$sql_parametro="select valor from nacer.parametros where parametro='periodo_vinculacion_efectores'";
$res_parametro=sql($sql_parametro, "Error") or fin_pagina();
$res_parametro=$res_parametro->fields['valor'];

$sql_parametro1="select valor from nacer.parametros where parametro='limite_prestacion'";
$res_parametro1=sql($sql_parametro1, "Error") or fin_pagina();
$res_parametro1=$res_parametro1->fields['valor'];

$sql_fecha_factura="select fecha_factura from facturacion.factura where id_factura='$id_factura'";
$res_fecha_factura=sql($sql_fecha_factura, "Error") or fin_pagina();
$res_fecha_factura=$res_fecha_factura->fields['fecha_factura'];

if($parametros["vincula_compro"]=="ok"){
   $id_comprobante=$parametros["id_comprobante"];
   $fecha_carga=date("Y-m-d");
   $db->StartTrans();
   
   $query="update facturacion.comprobante set id_factura='$id_factura'
   			where id_comprobante=$id_comprobante";
   sql($query, "Error al vincular comprobante") or fin_pagina();
      
   /*cargo los log*/ 
    $usuario=$_ses_user['name'];
	$log="insert into facturacion.log_factura
		   (id_factura, fecha, tipo, descripcion, usuario) 
	values ($id_factura, '$fecha_carga','Vinculacion','Se vinculo comrobante: $id_comprobante', '$usuario')";
	sql($log) or fin_pagina();
	 
    $db->CompleteTrans();    	
	$accion="Se Vinculo el Comrobante $id_comprobante a la Factura $id_factura";
	
}

if($_POST["vincular_todo"]=="Vincular Todo"){
   $fecha_carga=date("Y-m-d");
   $db->StartTrans();
   
    $query="update facturacion.comprobante set id_factura='$id_factura' ";
   
  // if (es_cuie($_ses_user['login'])&&($res_parametro=='si')){
	$anio=substr($periodo_actual,0,4);
	$mes=substr($periodo_actual,5,2);	
	$fecha_desde=ereg_replace('/','-',$periodo_actual).'-01';	
	$fecha_hasta=ereg_replace('/','-',$periodo_actual).'-'.ultimoDia($mes,$anio);	
	/*-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
	$query.=" where 
	(comprobante.id_factura is null)
	and (comprobante.cuie='$cuie') 
	and (marca=0 or marca is NULL)
	and (fecha_comprobante between '$fecha_desde' and '$fecha_hasta')
	and (alta_comp='$alta_comp' or alta_comp is NULL)
	and (comprobante.activo='S' or comprobante.activo is NULL) 
	and ((comprobante.fecha_comprobante + '$res_parametro1 days') > '$res_fecha_factura') ";
	/*-----------------------en la query anterior se agrega "or alta_comp is NULL" debido a que no trae los comprovantes ya que esta variable estaba siempre vacia y no era validada por la consuta-----------------------------------*/
  //}
  // else{
//	$query.=" where comprobante.id_factura is null and comprobante.cuie='$cuie' and marca=0 and (comprobante.activo='S' or comprobante.activo is NULL) and ((comprobante.fecha_comprobante + '$res_parametro1 days') > '$res_fecha_factura') ";
 //  }
    
	sql($query, "Error al vincular comprobante") or fin_pagina();
   
    /*cargo los log*/ 
    $usuario=$_ses_user['name'];
	$log="insert into facturacion.log_factura
		   (id_factura, fecha, tipo, descripcion, usuario) 
	values ($id_factura, '$fecha_carga','Vinculacion','Vinculacion Masiva de Comprobantes', '$usuario')";
	sql($log) or fin_pagina();
	 
    $db->CompleteTrans();    
    $ref = encode_link("factura_admin.php",array("id_factura"=>$id_factura));
    ?>    
    <script>
	window.opener.location.href='<?=$ref?>';window.close();
	</script>
<?}

variables_form_busqueda("listado_comp_fact");
function ultimoDia($mes,$ano){ 
    $ultimo_dia=28; 
    while (checkdate($mes,$ultimo_dia + 1,$ano)){ 
       $ultimo_dia++; 
    } 
    return $ultimo_dia; 
} 

$orden = array(
        "default" => "1",
        "1" => "afiapellido",
        "2" => "afinombre",
        "3" => "afidni",
        "5" => "fecha_comprobante",
        "8" => "id_comprobante"
       );
$filtro = array(
		"afidni" => "DNI",
        "afiapellido" => "Apellido",
        "afinombre" => "Nombre"                    
       );
$sql_tmp="select * 
	 from facturacion.comprobante	 
	 left join nacer.smiafiliados using (id_smiafiliados)";

//if (es_cuie($_ses_user['login'])&&($res_parametro=='si')){
	$anio=substr($periodo_actual,0,4);
	$mes=substr($periodo_actual,5,2);	
	$fecha_desde=ereg_replace('/','-',$periodo_actual).'-01';	
	$fecha_hasta=ereg_replace('/','-',$periodo_actual).'-'.ultimoDia($mes,$anio);	
	$where_tmp=" 
	(comprobante.id_factura is null) 
	and (comprobante.cuie='$cuie') 
	and (marca=0 or marca is NULL)
	and (fecha_comprobante between '$fecha_desde' and '$fecha_hasta')
	and (alta_comp='$alta_comp'or alta_comp is NULL)
	and (comprobante.activo='S' or comprobante.activo is NULL) and ((comprobante.fecha_comprobante + '$res_parametro1 days') >= '$res_fecha_factura') ";
	$accion="Muestra SOLO los comprobantes del mismo PERIODO de PRESTACION de la Factura";
/*-----------------------en la query anterior se agrega "or alta_comp is NULL" debido a que no trae los comprovantes ya que esta variable estaba siempre vacia y no era validada por la consuta-----------------------------------*/
	/*}
else{
	$where_tmp=" comprobante.id_factura is null and comprobante.cuie='$cuie' and marca=0 and (comprobante.activo='S' or comprobante.activo is NULL) and ((comprobante.fecha_comprobante + '$res_parametro1 days') > '$res_fecha_factura') ";
}
*/

echo $html_header;
?>
<form name=form1 action="listado_comp_fact.php" method=POST>
<input type="hidden" name="cuie" value="<?=$cuie?>">
<input type="hidden" name="id_factura" value="<?=$id_factura?>">
<input type="hidden" name="periodo_actual" value="<?=$periodo_actual?>">
<input type="hidden" name="alta_comp" value="<?=$alta_comp?>">
<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	    <?$ref = encode_link("factura_admin.php",array("id_factura"=>$id_factura));?>
	    &nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="volver" value='Volver' onclick="window.opener.location.href='<?=$ref?>';window.close();" >
	  </td>
     </tr>
</table>

<?$result = sql($sql) or die;?>

<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=10 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left>
		   <input type="submit" value="Vincular Todo" name="vincular_todo" onclick="return confirm ('¿Esta seguro que desea Vincular TODO?')">&nbsp;
		   <b>Total:</b> <?=$total_muletos?>-Muestra Los primeros 50 Resultados, se puede fitrar usando busquedas.
		</td> 
      </tr>
    </table>
   </td>
  </tr>
  

  <tr>
    <td id=mo width=1%>&nbsp;</td>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_comp_fact.php",array("sort"=>"8","up"=>$up,"cuie"=>$cuie))?>'>Nro Comp</a></td>      	
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_comp_fact.php",array("sort"=>"1","up"=>$up,"cuie"=>$cuie))?>'>Apellido</a></td>      	
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_comp_fact.php",array("sort"=>"2","up"=>$up,"cuie"=>$cuie))?>'>Nombre</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_comp_fact.php",array("sort"=>"3","up"=>$up,"cuie"=>$cuie))?>'>DNI</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_comp_fact.php",array("sort"=>"5","up"=>$up,"cuie"=>$cuie))?>'>Fecha Prestación</a></td>       
    <td align=right id=mo>Total Prestaciones</td>
  </tr>
 <?
   while (!$result->EOF) {
   	$ref = encode_link("listado_comp_fact.php",array("id_comprobante"=>$result->fields['id_comprobante'],"id_factura"=>$id_factura,"cuie"=>$cuie,"periodo_actual"=>$periodo_actual,"vincula_compro"=>"ok"));
    $onclick_elegir="location.href='$ref'";
    
    $id_tabla="tabla_".$result->fields['id_comprobante'];	
	$onclick_check=" javascript:(this.checked)?Mostrar('$id_tabla'):Ocultar('$id_tabla')";
	
	//consulta para saber si tiene pretaciones el comprobante
	$sql=" select count(id_prestacion) as cant_prestaciones from facturacion.prestacion 								
			where id_comprobante=". $result->fields['id_comprobante'];
	$cant_prestaciones=sql($sql,"no se puede traer la contidad de prestaciones") or die();
	$cant_prestaciones=$cant_prestaciones->fields['cant_prestaciones'];
	?>
  
    <tr <?=atrib_tr()?>>
     <td>
	  <input type=checkbox name=check_prestacion value="" onclick="<?=$onclick_check?>" class="estilos_check">
	 </td>     
     <td onclick="if (confirm ('Esta Seguro que desea Vincular el Comprobante seleccionado')){<?=$onclick_elegir?>}"><?=$result->fields['id_comprobante']?></td>
     <td onclick="if (confirm ('Esta Seguro que desea Vincular el Comprobante seleccionado')){<?=$onclick_elegir?>}"><?=$result->fields['afiapellido']?></td>
     <td onclick="if (confirm ('Esta Seguro que desea Vincular el Comprobante seleccionado')){<?=$onclick_elegir?>}"><?=$result->fields['afinombre']?></td>
     <td onclick="if (confirm ('Esta Seguro que desea Vincular el Comprobante seleccionado')){<?=$onclick_elegir?>}"><?=$result->fields['afidni']?></td>     
     <td onclick="if (confirm ('Esta Seguro que desea Vincular el Comprobante seleccionado')){<?=$onclick_elegir?>}"><?=Fecha($result->fields['fecha_comprobante'])?></td>             
     <td onclick="if (confirm ('Esta Seguro que desea Vincular el Comprobante seleccionado')){<?=$onclick_elegir?>}"><?=$cant_prestaciones?></td> 
    </tr>    
    <tr>
	          <td colspan=10>
	
	                  <?
	                  $sql=" select prestacion.*,nomenclador.*, patologias.codigo as cod_diag, patologias.descripcion as desc_diag
								from facturacion.prestacion 
								left join facturacion.nomenclador using (id_nomenclador)
								LEFT JOIN nomenclador.patologias ON (prestacion.diagnostico=patologias.codigo)																		
								where id_comprobante=". $result->fields['id_comprobante']." order by id_prestacion DESC";
	                  $result_items=sql($sql) or fin_pagina();
	                  
	                  $sql=" select * from nomenclador.prestaciones_n_op							
							 where id_comprobante=". $result->fields['id_comprobante']." order by id_prestaciones_n_op DESC";
					  $result_items1=sql($sql) or fin_pagina();
	                  ?>
	                  <div id=<?=$id_tabla?> style='display:none'>
	                  <table width=90% align=center class=bordes>
	                  			<?
	                  			$cantidad_items=$result_items->recordcount();
	                  			$cantidad_items1=$result_items1->recordcount();
	                  			if (($cantidad_items==0)&&($cantidad_items1==0)){?>
		                            <tr>
		                            	<td colspan="10" align="center">
		                            		<b><font color="Red" size="+1">NO HAY PRESTACIONES PARA ESTE COMPROBANTE</font></b>
		                            	</td>	                                
			                        </tr>	                               
								<?}
								else{?>
		                           <tr id=ma>		                               
		                               <td>Cantidad</td>
		                               <td>Codigo</td>
		                               <td>Descripción</td>
		                               <td>Precio</td>
		                               <td>Total</td>	                               
		                            </tr>
		                            <?while (!$result_items1->EOF){?>
							           					<tr>
							           						 <?
							           						 $query="select categoria from nomenclador.grupo_prestacion where codigo='".$result_items1->fields['prestacion']."';";
							           						 $res_i=sql($query) or fin_pagina();
							           						 $query="select categoria from nomenclador.grupo_prestacion where codigo='".$result_items1->fields['tema']."';";
							           						 $res_j=sql($query) or fin_pagina();
							           						 $query="select descripcion from nomenclador.patologias where codigo='".$result_items1->fields['patologia']."';";
							           						 $res_k=sql($query) or fin_pagina();
							           						 $query="select categoria from nomenclador.grupo_prestacion where codigo='".$result_items1->fields['profesional']."';";
							           						 $res_l=sql($query) or fin_pagina();
							           						 $descripcion='<b>Prestacion: </b>'.$res_i->fields["categoria"].' <b>Objeto: </b>'.$res_j->fields["categoria"].' <b>Diagnostico: </b>'.$res_k->fields["descripcion"];
							           						 $descripcion_amp='Prestacion: '.$res_i->fields["categoria"].' Objeto: '.$res_j->fields["categoria"].' Diagnostico: '.$res_k->fields["descripcion"].' Profesional: '.$res_l->fields["categoria"];
							           						 ?>
							                            	 <td class="bordes">1</td>			                                 
							                                 <td class="bordes" title="<?=$result_items1->fields["codigo"]?>"><?=substr($result_items1->fields["codigo"],42)?></td>
							                                 <td class="bordes" title="<?=$descripcion_amp?>"><?=$descripcion?></td>			                                 
							                                 <td class="bordes"><?=number_format($result_items1->fields["precio"],2,',','.')?></td>
							                                 <td class="bordes"><?=number_format($result_items1->fields["precio"],2,',','.')?></td>
							                            </tr>
						                            	<?$result_items1->movenext();
						     
           							}//del while?>
		                            <?while (!$result_items->EOF){?>
			                            <tr>
			                            	 <td class="bordes"><?=$result_items->fields["cantidad"]?></td>			                                 
			                                 <td class="bordes"><?=$result_items->fields["codigo"]?></td>
			                                 <td class="bordes"><?=$result_items->fields["descripcion"]. ' | Diagnostico: '.$result_items->fields["cod_diag"].'-'.$result_items->fields["desc_diag"]?></td>
			                                 <td class="bordes"><?=number_format($result_items->fields["precio_prestacion"],2,',','.')?></td>
			                                 <td class="bordes"><?=number_format($result_items->fields["cantidad"]*$result_items->fields["precio_prestacion"],2,',','.')?></td>
			                            </tr>
		                            	<?$result_items->movenext();
		                            }//del while
								}//del else?>
	                            	                            
	               </table>
	               </div>
	
	         </td>
	      </tr>  	
	<?$result->MoveNext();
    }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>
