<?php
/*
$Author: gaby $
$Revision: 1.0 $
$Date: 2010/10/20 15:22:40 $
*/
require_once("../../config.php");
cargar_calendario();

function ultimoDia($mes,$ano){ 
    if ($mes=='02')$ultimo_dia=28;
	else if ($mes=='04' or $mes=='06'or $mes=='09' or $mes=='11') $ultimo_dia=30;
	else $ultimo_dia=31;
    while (checkdate($mes,$ultimo_dia + 1,$ano)){ 
       $ultimo_dia++; 
    } 
    return $ultimo_dia; 
}

$fecha_hoy=date("Y-m-d H:i:s");
$fecha_hoy=fecha($fecha_hoy);

if ($_POST['generar_excel']=="Generar Excel"){
	$periodo=$_POST['periodo'];
	$link=encode_link("listado_factura_dep_excel.php",array("periodo"=>$periodo));?>
	<script>
	window.open('<?=$link?>')
	</script>	
<?}

if ($_POST['muestra']=="Muestra"){
	$cuie=$_POST['cuie'];
	$fecha_desde=$_POST['fecha_desde'];
	$fecha_hasta=$_POST['fecha_hasta'];
	
	$link=encode_link("listado_factura_dep_pcia.php",array("cuie"=>$cuie,"fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));?>
	<script>
	window.open('<?=$link?>')
	</script>
<?}
$sql_tmp=" SELECT			Sum(facturacion.prestacion.cantidad) AS cantidad,
			facturacion.nomenclador.codigo,
			facturacion.nomenclador.descripcion,
			facturacion.nomenclador.catas,
			facturacion.nomenclador.priori,			
			facturacion.prestacion.precio_prestacion,
			facturacion.prestacion.precio_prestacion*sum(facturacion.prestacion.cantidad) AS precio_total
			FROM
			
				(SELECT * from facturacion.factura
				WHERE id_factura in 
					(SELECT DISTINCT
						factura.id_factura
					FROM facturacion.factura 
					INNER JOIN expediente.expediente ON facturacion.factura.id_factura = expediente.expediente.id_factura
					INNER JOIN expediente.transaccion ON expediente.transaccion.id_expediente = expediente.expediente.id_expediente
					WHERE
						(expediente.transaccion.id_area = 1 AND expediente.transaccion.estado = 'D') AND
						(expediente.transaccion.num_tranf is null) 
					))as cons1
			INNER JOIN facturacion.comprobante USING (id_factura)
			INNER JOIN facturacion.prestacion USING (id_comprobante)
			INNER JOIN facturacion.nomenclador USING (id_nomenclador)
			GROUP BY
			facturacion.nomenclador.id_nomenclador,
			facturacion.nomenclador.codigo,
			facturacion.nomenclador.descripcion,
			facturacion.nomenclador.catas,
			facturacion.nomenclador.priori,
			facturacion.prestacion.precio_prestacion
			ORDER BY codigo DESC";
			
if (($_POST['Filtrar']=="Filtrar")||($_POST['generar_excel']=="Generar Excel")){
	$periodo_actual=$_POST['periodo'];
	$anio=substr($periodo_actual,0,4);
	$mes=substr($periodo_actual,5,2);	
	$fecha_desde=ereg_replace('/','-',$periodo_actual).'-01';	
	$fecha_hasta=ereg_replace('/','-',$periodo_actual).'-'.ultimoDia($mes,$anio);

$sql_tmp=" SELECT			
			Sum(facturacion.prestacion.cantidad) AS cantidad,
			facturacion.nomenclador.codigo,
			facturacion.nomenclador.descripcion,
			facturacion.nomenclador.catas,
			facturacion.nomenclador.priori,
			facturacion.prestacion.precio_prestacion,
			facturacion.prestacion.precio_prestacion*sum(facturacion.prestacion.cantidad) AS precio_total
			FROM
			
				(SELECT * from facturacion.factura
				WHERE id_factura in 
					(SELECT DISTINCT
						factura.id_factura
					FROM facturacion.factura 
					INNER JOIN expediente.expediente ON facturacion.factura.id_factura = expediente.expediente.id_factura
					INNER JOIN expediente.transaccion ON expediente.transaccion.id_expediente = expediente.expediente.id_expediente
					WHERE
						(expediente.transaccion.id_area = 1 AND expediente.transaccion.estado = 'D') AND
						(expediente.transaccion.num_tranf is null) AND
						(expediente.transaccion.fecha_mov BETWEEN '$fecha_desde' AND '$fecha_hasta') 
					))as cons1
			INNER JOIN facturacion.comprobante USING (id_factura)
			INNER JOIN facturacion.prestacion USING (id_comprobante)
			INNER JOIN facturacion.nomenclador USING (id_nomenclador)
			GROUP BY
			facturacion.nomenclador.id_nomenclador,
			facturacion.nomenclador.codigo,
			facturacion.nomenclador.descripcion,
			facturacion.nomenclador.catas,
			facturacion.nomenclador.priori,
			facturacion.prestacion.precio_prestacion
			ORDER BY codigo DESC";
			
$sql_fact="SELECT 			
			factura.* 
			FROM facturacion.factura
				WHERE id_factura in 
					(SELECT DISTINCT
						factura.id_factura
					FROM facturacion.factura 
					INNER JOIN expediente.expediente ON facturacion.factura.id_factura = expediente.expediente.id_factura
					INNER JOIN expediente.transaccion ON expediente.transaccion.id_expediente = expediente.expediente.id_expediente
					WHERE
						(expediente.transaccion.id_area = 1 AND expediente.transaccion.estado = 'D') AND
						(expediente.transaccion.num_tranf is null) AND
						(expediente.transaccion.fecha_mov BETWEEN '$fecha_desde' AND '$fecha_hasta') 
					)";
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
 if(document.all.fecha_hasta.value<document.all.fecha_desde.value){
  alert('La Fecha HASTA debe ser MAYOR 0 IGUAL a la Fecha DESDE');
  return false;
 }
return true;
}
</script>
<form name=form1 action="listado_factura_dep.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<b>Periodo para DDJJ:	        			
			 <select name=periodo Style="width=100px">&nbsp;
			  <?
			  $sql1 = "SELECT
							*
							FROM
							facturacion.periodo
							order by  periodo";
			  $result1=sql($sql1,"No se puede traer el periodo");
			  while (!$result1->EOF) {?>	  
			  <option value=<?=$result1->fields['periodo']?> <?if ($result1->fields['periodo']==$_POST['periodo']) echo "selected"?>><?=$result1->fields['periodo']?></option>
			  <?
			  $result1->movenext();
			  }
			  ?>			  
			  </select>
			  <input type="submit" value="Filtrar" name="Filtrar">	
	    	  <input type="submit" value="Generar Excel" name="generar_excel">&nbsp &nbsp &nbsp	
	    	  <input type=button name="ddjj_por_periodo" value="DDJJ Por Periodo" onclick="window.open('<?=encode_link("../facturacion/listado_factura_dep_periodo.php",array())?>','DDJJ','dependent:yes,width=1000,height=700,top=1,left=60,scrollbars=yes');" title="DDJJ" >
		     
	    	  <BR>
	    	  Desde: <input type=text id=fecha_desde name=fecha_desde value='<?=fecha($fecha_desde)?>' size=15 readonly>
				<?=link_calendario("fecha_desde");?>
		
			  Hasta: <input type=text id=fecha_hasta name=fecha_hasta value='<?=fecha($fecha_hasta)?>' size=15 readonly>
				<?=link_calendario("fecha_hasta");?> 
				
			  Efector: 
			 <select name=cuie Style="width=257px" 
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer();"
				onchange="borrar_buffer();" 
				<?if ($id_planilla) echo "disabled"?> >
			 <?
			 $sql= "select * from nacer.efe_conv 
			 		order by nombre";
			 $res_efectores=sql($sql) or fin_pagina();
			 while (!$res_efectores->EOF){ 
			 	$cuiel=$res_efectores->fields['cuie'];
			    $nombre_efector=$res_efectores->fields['nombre'];
			    
			    ?>
				<option value='<?=$cuiel?>' <?if ($cuie==$cuiel) echo "selected"?> ><?=$nombre_efector?></option>
			    <?
			    $res_efectores->movenext();
			    }?>
			</select>
			
			<input type="submit" name="muestra" value='Muestra' onclick="return control_muestra()" >
    	  </b>     
	  </td>
     </tr>
</table>

<?$result = sql($sql_tmp) or die;?>

<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=10 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$result->recordCount()?> Filtro: <?=$_POST['periodo'];?></td>       
      </tr>
    </table>
   </td>
  </tr>
  
<td align=right id=mo>Codigo</td>
    <td align=right id=mo>Descripcion</td>
    <td align=right id=mo>Cantidad</td>
    <td align=right id=mo>P Unitario</td>
    <td align=right id=mo>P Total</td>
  </tr>
 <? $t=0;
 	$s=0;
   while (!$result->EOF) {  	
	if ($result->fields['catas']=='1'){
			$color_catas='#FA58AC'; 
			$title_catas='Prestacion Catastrofica';
	   }else{
			$color_catas=''; 
			$title_catas='Prestacion Normal';
	}
	if ($result->fields['priori']=='1'){
			$color_priori='#00FF00'; 
			$title_priori='Prestacion Priorizada';
	   }else{
			$color_priori=''; 
			$title_priori='Prestacion Normal';
	}
	
	?>
    <tr <?=atrib_tr()?> >     
     <td bgcolor="<?=$color_catas?>" title="<?=$title_catas?>"><?=$result->fields['codigo']?></td>     
     <td bgcolor="<?=$color_priori?>" title="<?=$title_priori?>"><?=$result->fields['descripcion']?></td>     
     <td><?=$result->fields['cantidad']?></td>     
     <td><?=number_format($result->fields['precio_prestacion'],2,',','.')?></td>     
     <td><?=number_format($result->fields['precio_total'],2,',','.')?></td>   
     <?$t=$t+$result->fields['precio_total'];
     	$s=$s+$result->fields['cantidad']
     ?>  
    </tr>
	<?$result->MoveNext();
   }?>
    <tr>    	  
     	<td>Monto Total</td> 
     	<td><b><?=number_format($t,2,',','.')?></b></td> 
    </tr>
    <tr>    	
     	<td>Total Prestaciones</td> 
     	<td><b><?=number_format($s,0,',','.')?></b></td> 
    </tr> 
    
</table>

<br>
	

</td>
</table>
<?if ($_POST['Filtrar']=="Filtrar"){
$result = sql($sql_fact) or die;?>

<table border=0 width=70% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=10 align=left id=ma>
     <table width=80%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$result->recordCount()?> Filtro: <?=$_POST['periodo'];?></td>       
      </tr>
    </table>
   </td>
  </tr>
    <td align=right id=mo>Num Factura</td>
    <td align=right id=mo>CUIE</td>
    <td align=right id=mo>Fecha Carga</td>
    <td align=right id=mo>Fecha Factura</td>
  </tr>
 <? $t=0;
 	$s=0;
   while (!$result->EOF) {
	   
	  if ($result->fields['alta_comp']=='SI'){
			$color_1='#A9BCF5'; 
			$title_1='FACTURA ALTA COMPLEJIDAD';
	   }else{
			$color_1=''; 
			$title_1='FACTURA PDSPS';
		}
		   
	   ?>  	
  
    <tr <?=atrib_tr()?> >
	 <?$id_factura=$result->fields['id_factura'];
	 $ref = encode_link("./factura_admin.php",array("id_factura"=>$id_factura));?> 	  
     <td bgcolor="<?=$color_1?>" title="<?=$title_1?>"><a href="<?=$ref?>" target="_blank"><?=$result->fields['id_factura']?></a></td>     
     <td bgcolor="<?=$color_1?>" title="<?=$title_1?>"><?=$result->fields['cuie']?></td>     
     <td bgcolor="<?=$color_1?>" title="<?=$title_1?>"><?=fecha($result->fields['fecha_carga'])?></td>     
     <td bgcolor="<?=$color_1?>" title="<?=$title_1?>"><?=fecha($result->fields['fecha_factura'])?></td>       
    </tr>
	<?$result->MoveNext();
   }?>   
</table>
<?}?>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>
