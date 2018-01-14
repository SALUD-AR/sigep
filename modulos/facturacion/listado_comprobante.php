<?php

require_once("../../config.php");
extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);


variables_form_busqueda("listado_comprobante");

if ($cmd == "")  $cmd="F";


$orden = array(
        "default" => "8",
        "default_up" => "0",
        "1" => "afiapellido",
        "2" => "afinombre",
        "3" => "afidni",        
        "8" => "id_comprobante",
        "9" => "id_factura",
        "11" => "comprobante.fecha_comprobante"
       );
$filtro = array(
		"afidni" => "DNI",
        "afiapellido" => "Apellido",
        "afinombre" => "Nombre",        
        "to_char(id_comprobante,'999999')"=>"Nro. Comprobante",
        "to_char(id_factura,'999999')"=>"Nro. Factura"                
       );
       
$datos_barra = array(
     array(
        "descripcion"=> "Facturados",
        "cmd"        => "F"
     ),
     array(
        "descripcion"=> "No Facturados",
        "cmd"        => "NF"
     ),
     array(
        "descripcion"=> "Todos",
        "cmd"        => "todos"
     )
);

generar_barra_nav($datos_barra);

$sql_tmp="select afiapellido,afinombre,afidni,id_comprobante,id_factura,comprobante.fecha_comprobante 
	 from facturacion.comprobante	 
	 left join nacer.smiafiliados using (id_smiafiliados)
	 left join facturacion.factura using (id_factura)";
	 
if ($cmd=="F")
    $where_tmp=" (comprobante.id_factura is not null)";
    

if ($cmd=="NF")
    $where_tmp=" (comprobante.id_factura is null)";
    
if ($_POST['muestra']=="Muestra"){
	
	$fecha_desde=$_POST['fecha_desde'];
	$fecha_hasta=$_POST['fecha_hasta'];
	$link=encode_link("comprobante_excel.php",array("fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));?>
	<script>
	window.open('<?=$link?>')
	</script>	
<?}

if ($_POST['importar']=="Importar"){	
	$fecha_desde=$_POST['fecha_desde'];
	$fecha_hasta=$_POST['fecha_hasta'];
	
//------------------------------------------------------------------------------------------------------------------------	 
$filename = 'SUMAR-SIRGE-12-COMPROBANTES.txt';	
	  	if (!$handle = fopen($filename, 'w+')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}
    	$sql1="SELECT 
				  facturacion.factura.fecha_carga::date,
				  facturacion.factura.cuie as efector,
				  facturacion.factura.id_factura as numero_comprobante,
				  'FC' AS tipo_comprobante,				  
				  facturacion.factura.fecha_factura::date as fecha_comprobante,
				  facturacion.factura.estado_envio,			
				  contabilidad.ingreso.fecha_notificacion as fecha_notificacion,
				  contabilidad.ingreso.fecha_deposito as fecha_debito_bancario,
				  contabilidad.ingreso.monto_factura as importe,
				  contabilidad.ingreso.monto_factura as importe_pagado,
				  '' as factura_debitada,
				  '' as concepto,
				  '201212-001' as nomenclador
				FROM
				  facturacion.factura
				  INNER JOIN contabilidad.ingreso ON (facturacion.factura.id_factura = contabilidad.ingreso.numero_factura)
				 Where
				 (factura.fecha_factura between '$fecha_desde' and '$fecha_hasta') and factura.estado='C' 
				 --and (factura.estado_envio is null or factura.estado_envio='n')
				 ORDER BY id_factura";
    	    	
		$result1=sql($sql1) or die;
    	$result1->movefirst();
    	
    	$contenido="efector;numero_comprobante;tipo_comprobante;fecha_comprobante;fecha_recepcion;fecha_notificacion;fecha_liquidacion;fecha_debito_bancario;importe;importe_pagado;factura_debitada;concepto\r\n";			
    	if (fwrite($handle, $contenido) === FALSE) {
        	echo "No se Puede escribir  ($filename)";
        	exit;
    	}    	
    	
    	while (!$result1->EOF) {
    		
    		$id_factura=$result1->fields['numero_comprobante'];   		
			$sql2="select distinct id_factura, fecha_mov as fecha_liquidacion
					from expediente.transaccion 
					where id_factura = '$id_factura' and estado = 'C'";
			$result2=sql($sql2) or die;
		    $result2->movefirst();
		    
		    $sql3="SELECT fecha_ing as fecha_recepcion
				FROM
				  expediente.expediente			  
				 Where
				  id_factura='$id_factura'";
			$result3=sql($sql3) or die;
		    $result3->movefirst();
    		
    		$contenido=$result1->fields['efector'];
			$contenido.=";";
    		$contenido.=$result1->fields['numero_comprobante'];
			$contenido.=";";
			$contenido.=$result1->fields['tipo_comprobante'];
			$contenido.=";";
			$contenido.=$result1->fields['fecha_comprobante'];
			$contenido.=";";
			if ($result3->fields['fecha_recepcion']==''){				
				$contenido.=$result1->fields['fecha_carga'];
				$contenido.=";";
				}
			else {
				$contenido.=$result3->fields['fecha_recepcion'];
				$contenido.=";";
				}			
			if ($result1->fields['fecha_notificacion']==''){
				$contenido.='1900-01-01';
				$contenido.=";";
			}
			else{
				$contenido.=$result1->fields['fecha_notificacion'];
				$contenido.=";";
			}			
			if ($result2->fields['fecha_liquidacion']==''){	
				$sql4="SELECT date (log_factura.fecha) as fechaliquidacion
				FROM
				  facturacion.log_factura				  
				 Where
				  id_factura='$id_factura' and tipo='Cerrar Factura'
				 ORDER BY fechaliquidacion DESC";
				$result4=sql($sql4) or die;
				$result4->movefirst();
				if ($result4->fields['fechaliquidacion']==''){
					$contenido.='1900-01-01';
					$contenido.=";";
					}			
				else{
					$contenido.=$result4->fields['fechaliquidacion'];
					$contenido.=";";
				}
				}
			else {
				$contenido.=$result2->fields['fecha_liquidacion'];
				$contenido.=";";
				}			
			$contenido.=$result1->fields['fecha_debito_bancario'];
			$contenido.=";";
			$contenido.=$result1->fields['importe'];
			$contenido.=";";
			$contenido.=$result1->fields['importe_pagado'];
			$contenido.=";";
			$contenido.=$result1->fields['factura_debitada'];
			$contenido.=";";
			$contenido.=$result1->fields['concepto'];
			$contenido.="\r\n";
			    					
			/*$id_factura=$result1->fields['numero_comprobante'];
			$sql_mod="update facturacion.factura set estado_envio='e' where factura.id_factura='$id_factura'";
			$sql_res=sql($sql_mod,"No se pudo actualizar el estado de la factura");*/
			
			if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		
			}
    		$result1->MoveNext();
    	}
    	echo "El Archivo ($filename) se genero con exito <br>";
       	fclose($handle);  	
//------------------------------------------------------------------------------------------------------------------------
    	$filename = 'SUMAR-SIRGE-12-PRESTACIONES.txt';	
	  	if (!$handle = fopen($filename, 'w+')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}
    	$sql1="select tabla.operacion,tabla.estado,tabla.numero_comprobante,tabla.subcodigo_prestacion,tabla.precio_unitario,tabla.fecha_prestacion,tabla.clave_beneficiario,tabla.tipo_documento,
tabla.clase_documento,tabla.numero_documento,tabla.id_dato_reportable,tabla.dato_reportable,tabla.efector,
row_number() OVER (PARTITION BY tabla.numero_comprobante,tabla.fecha_prestacion,tabla.codigo_prestacion,tabla.subcodigo_prestacion,tabla.clave_beneficiario) orden,
tabla.efector,tabla.codigo_prestacion,tabla.estado_envio from (
SELECT 'A' as operacion,
	'L' as estado,
	facturacion.comprobante.id_factura as numero_comprobante,
	facturacion.anexo.numero as subcodigo_prestacion, --CUANDO DEVUELVE '0' EN EL ARCHIVO VA VACIO
	facturacion.prestacion.precio_prestacion as precio_unitario,
	facturacion.prestacion.id_nomenclador,
	facturacion.comprobante.fecha_comprobante::date as fecha_prestacion,
	nacer.smiafiliados.clavebeneficiario as clave_beneficiario,
	nacer.smiafiliados.afitipodoc as tipo_documento,
	nacer.smiafiliados.aficlasedoc as clase_documento,
	nacer.smiafiliados.afidni as numero_documento,
	' ' as id_dato_reportable,
	' ' as dato_reportable,
	comprobante.cuie as efector,			  
	CASE WHEN facturacion.nomenclador_detalle.modo_facturacion='1' THEN facturacion.nomenclador.codigo
	    WHEN facturacion.nomenclador_detalle.modo_facturacion<>'1' THEN facturacion.nomenclador.grupo||facturacion.nomenclador.codigo||facturacion.prestacion.diagnostico
	    END as codigo_prestacion,
	facturacion.factura.estado_envio as estado_envio
	FROM
	facturacion.prestacion
	inner JOIN facturacion.comprobante ON (facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante)				  
	left JOIN facturacion.nomenclador ON (facturacion.prestacion.id_nomenclador = facturacion.nomenclador.id_nomenclador)
	left JOIN facturacion.nomenclador_detalle ON (facturacion.nomenclador.id_nomenclador_detalle=facturacion.nomenclador_detalle.id_nomenclador_detalle)
	left JOIN nacer.smiafiliados ON (facturacion.comprobante.id_smiafiliados = nacer.smiafiliados.id_smiafiliados)
	left JOIN facturacion.factura ON (comprobante.id_factura = factura.id_factura)
	INNER JOIN contabilidad.ingreso ON (facturacion.factura.id_factura = contabilidad.ingreso.numero_factura)
	left join facturacion.anexo on (prestacion.id_anexo = anexo.id_anexo)
	Where
	(fecha_comprobante between '$fecha_desde' and '$fecha_hasta') and factura.estado='C' and prestacion.precio_prestacion > 0 and nomenclador.codigo <> ''
	--and (facturacion.factura.estado_envio is null or facturacion.factura.estado_envio='n')
	order by comprobante.id_factura, comprobante.fecha_comprobante, nomenclador.codigo, anexo.numero, smiafiliados.clavebeneficiario) as tabla";

		$result1=sql($sql1) or die;
    	$result1->movefirst();  
    	 			
    	$contenido="operacion;estado;numero_comprobante;codigo_prestacion;subcodigo_prestacion;precio_unitario;fecha_prestacion;clave_beneficiario;tipo_documento;clase_documento;numero_documento;id_dato_reportable_1;dato_reportable_1;id_dato_reportable_2;dato_reportable_2;id_dato_reportable_3;dato_reportable_3;id_dato_reportable_4;dato_reportable_4;orden;cuie\r\n";			
    	if (fwrite($handle, $contenido) === FALSE) {
        	echo "No se Puede escribir  ($filename)";
        	exit;
    	}
    	while (!$result1->EOF) {    		
			$contenido=$result1->fields['operacion'];
			$contenido.=";";			
    		$contenido.=$result1->fields['estado'];
			$contenido.=";"; 
			$contenido.=$result1->fields['numero_comprobante'];
			$contenido.=";"; 
			$contenido.=str_replace(" ", "",$result1->fields['codigo_prestacion']);
			$contenido.=";"; 
			if ($result1->fields['subcodigo_prestacion']<>0) $contenido.=$result1->fields['subcodigo_prestacion'];
			$contenido.=";"; 
			$contenido.=$result1->fields['precio_unitario'];
			$contenido.=";"; 
			$contenido.=$result1->fields['fecha_prestacion'];
			$contenido.=";"; 
			$contenido.=$result1->fields['clave_beneficiario'];
			$contenido.=";"; 
			$contenido.=$result1->fields['tipo_documento'];
			$contenido.=";"; 
			$contenido.=trim($result1->fields['clase_documento']);
			$contenido.=";"; 
			$contenido.=$result1->fields['numero_documento'];
			$contenido.=";"; 
			$contenido.=$result1->fields['id_dato_reportable'];
			$contenido.=";"; 
			$contenido.=$result1->fields['dato_reportable'];
			$contenido.=";"; 
			$contenido.=$result1->fields['id_dato_reportable'];
			$contenido.=";"; 
			$contenido.=$result1->fields['dato_reportable'];
			$contenido.=";";
			$contenido.=$result1->fields['id_dato_reportable'];
			$contenido.=";"; 
			$contenido.=$result1->fields['dato_reportable'];
			$contenido.=";";
			$contenido.=$result1->fields['id_dato_reportable'];
			$contenido.=";"; 
			$contenido.=$result1->fields['dato_reportable'];
			$contenido.=";";			
			$contenido.=$result1->fields['orden'];
			$contenido.=";"; 
    		$contenido.=$result1->fields['efector'];			
			$contenido.="\r\n";			
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
			$result1->MoveNext();			
    	}
    	echo "El Archivo ($filename) se genero con exito <br>";    
    	fclose($handle);
 //------------------------------------------------------------------------------------------------------------------------
    	$filename = 'SUMAR-SIRGE-12-PRESTACIONES(para sistema de padrones CEB).txt';	
	  	if (!$handle = fopen($filename, 'w+')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}
    	$sql1="SELECT id_prestacion,
				'A' as operacion,
				'L' as estado,
				facturacion.comprobante.id_factura as numero_comprobante,
				facturacion.anexo.numero as subcodigo_prestacion, --CUANDO DEVUELVE '0' EN EL ARCHIVO VA VACIO
				facturacion.prestacion.precio_prestacion as precio_unitario,
				facturacion.comprobante.fecha_comprobante::date as fecha_prestacion,
				nacer.smiafiliados.clavebeneficiario as clave_beneficiario,
				nacer.smiafiliados.afitipodoc as tipo_documento,
				nacer.smiafiliados.aficlasedoc as clase_documento,
				nacer.smiafiliados.afidni as numero_documento,
				' ' as id_dato_reportable,
				' ' as dato_reportable,
				'1' as orden, --VER ESTE CAMPO LOS VALORES POSIBLES Y PONER VALOR CORRECTO
				comprobante.cuie as efector,			  
				CASE WHEN facturacion.nomenclador_detalle.modo_facturacion='1' THEN facturacion.nomenclador.codigo
					 WHEN facturacion.nomenclador_detalle.modo_facturacion<>'1' THEN facturacion.nomenclador.grupo||facturacion.nomenclador.codigo||facturacion.prestacion.diagnostico
					END as codigo_prestacion
					FROM
					facturacion.prestacion
					left JOIN facturacion.comprobante ON (facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante)				  
					left JOIN facturacion.nomenclador ON (facturacion.prestacion.id_nomenclador = facturacion.nomenclador.id_nomenclador)
					left JOIN facturacion.nomenclador_detalle ON (facturacion.nomenclador.id_nomenclador_detalle=facturacion.nomenclador_detalle.id_nomenclador_detalle)
					left JOIN nacer.smiafiliados ON (facturacion.comprobante.id_smiafiliados = nacer.smiafiliados.id_smiafiliados)
					left JOIN facturacion.factura ON (comprobante.id_factura = factura.id_factura)
					left join facturacion.anexo on (prestacion.id_anexo = anexo.id_anexo)
				Where
					(fecha_comprobante between '$fecha_desde' and '$fecha_hasta') and factura.estado='C' and prestacion.precio_prestacion > 0
					--and (prestacion.estado_envio is null or prestacion.estado_envio='n')
				order by comprobante.id_comprobante";

		$result1=sql($sql1) or die;
    	$result1->movefirst();  
    	 			
    	$contenido="operacion;estado;numero_comprobante;codigo_prestacion;subcodigo_prestacion;precio_unitario;fecha_prestacion;clave_beneficiario;tipo_documento;clase_documento;numero_documento;id_dato_reportable_1;dato_reportable_1;id_dato_reportable_2;dato_reportable_2;id_dato_reportable_3;dato_reportable_3;id_dato_reportable_4;dato_reportable_4;orden;cuie\r\n";			
    	if (fwrite($handle, $contenido) === FALSE) {
        	echo "No se Puede escribir  ($filename)";
        	exit;
    	}
    	while (!$result1->EOF) {    		
			$contenido=$result1->fields['operacion'];
			$contenido.=";";			
    		$contenido.=$result1->fields['estado'];
			$contenido.=";"; 
			$contenido.=$result1->fields['numero_comprobante'];
			$contenido.=";"; 
			$contenido.=str_replace(" ", "",$result1->fields['codigo_prestacion']);
			$contenido.=";"; 
			if ($result1->fields['subcodigo_prestacion']<>0) $contenido.=$result1->fields['subcodigo_prestacion'];
			$contenido.=";"; 
			$contenido.=$result1->fields['precio_unitario'];
			$contenido.=";"; 
			$contenido.=$result1->fields['fecha_prestacion'];
			$contenido.=";"; 
			$contenido.=$result1->fields['clave_beneficiario'];
			$contenido.=";"; 
			$contenido.=$result1->fields['tipo_documento'];
			$contenido.=";"; 
			$contenido.=trim($result1->fields['clase_documento']);
			$contenido.=";"; 
			$contenido.=$result1->fields['numero_documento'];
			$contenido.=";"; 
			$contenido.=$result1->fields['id_dato_reportable'];
			$contenido.=";"; 
			$contenido.=$result1->fields['dato_reportable'];
			$contenido.=";"; 
			$contenido.=$result1->fields['id_dato_reportable'];
			$contenido.=";"; 
			$contenido.=$result1->fields['dato_reportable'];
			$contenido.=";";
			$contenido.=$result1->fields['id_dato_reportable'];
			$contenido.=";"; 
			$contenido.=$result1->fields['dato_reportable'];
			$contenido.=";";
			$contenido.=$result1->fields['id_dato_reportable'];
			$contenido.=";"; 
			$contenido.=$result1->fields['dato_reportable'];
			$contenido.=";";
			$contenido.=$result1->fields['orden'];
			$contenido.=";"; 
    		$contenido.=$result1->fields['efector'];			
			$contenido.="\r\n";			
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
			/*$id_factura=$result1->$result1->fields['numero_comprobante'];
    		$sql_update="update facturacion.factura set estado_envio='e' where id_factura='$id_factura'";
			$sql_res_update=sql($sql_update,"No se pudo actualizar la informacion de facturacion.factura");
			$id_prestacion=$result1->fields['id_prestacion'];
			$sql_update2="update facturacion.prestacion set estado_envio='e' where id_prestacion='$id_prestacion'";
			$sql_res_update2=sql($sql_update2,"No se pudo actualizar facturacion.prestacion");*/
    		$result1->MoveNext();			
    	}
    	echo "El Archivo ($filename) se genero con exito <br>";    
    	fclose($handle);   	
//------------------------------------------------------------------------------------------------------------------------    	
    	$filename = 'SUMAR-SIRGE-12-APLICACION.txt';
	  	if (!$handle = fopen($filename, 'w+')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}
    	$sql1="SELECT 
				  contabilidad.egreso.cuie as efector,
				  contabilidad.egreso.fecha_egreso::date as fecha_gasto,
				  contabilidad.egreso.fecha_egreso::date as periodo,
				  contabilidad.egreso.id_egreso as numero_comprobante_gasto,
				  '' as efector_cesion,
				  contabilidad.egreso.monto_egreso as monto,
				  contabilidad.inciso.cod_gasto as codigo_gasto,				  
				  contabilidad.inciso.ins_nombre as concepto
				FROM
				  contabilidad.egreso
				  left JOIN contabilidad.inciso ON (contabilidad.egreso.id_inciso = contabilidad.inciso.id_inciso)
				where
				 (fecha_egreso between '$fecha_desde' and '$fecha_hasta') and monto_egreso <> 0
				 order by id_egreso";   	
		$result1=sql($sql1) or die;
    	$result1->movefirst();
    	
    	$contenido="efector;fecha_gasto;periodo;numero_comprobante_gasto;codigo_gasto;efector_cesion;monto;concepto\r\n";			
    	if (fwrite($handle, $contenido) === FALSE) {
        	echo "No se Puede escribir  ($filename)";
        	exit;
    	}   
    	
    	while (!$result1->EOF) {
			$contenido=$result1->fields['efector'];
			$contenido.=";";			    		
			$contenido.=$result1->fields['fecha_gasto'];
			$contenido.=";";
			$contenido.=substr($result1->fields['periodo'],0,7);;
			$contenido.=";";
			$contenido.=$result1->fields['numero_comprobante_gasto'];
			$contenido.=";";
			$contenido.=$result1->fields['codigo_gasto'];
			$contenido.=";";
			$contenido.=$result1->fields['efector_cesion'];
			$contenido.=";";
			$contenido.=$result1->fields['monto'];
			$contenido.=";";
			$contenido.=$result1->fields['concepto'];
			$contenido.="\r\n";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$result1->MoveNext();
    	}
    	echo "El Archivo ($filename) se genero con exito <br>";
    
    	fclose($handle);
}
    
    
echo $html_header;
?>
<form name=form1 action="listado_comprobante.php" method=POST>
<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	    
	    <?if (permisos_check('inicio','importa_rendicion_cuentas')){?>
	    <b>
	    &nbsp;&nbsp;&nbsp; || &nbsp;&nbsp;&nbsp;
	    Desde: <input type="text" name="fecha_desde" value="2012-01-01" maxlength="10" size="12">
	    Hasta: <input type="text" name="fecha_hasta" value="aaaa-mm-dd" maxlength="10" size="12">
	    <input type="submit" name="importar" value='Importar'>
	    &nbsp;&nbsp;&nbsp;
	    <input type="submit" name="muestra" value='Muestra'>
	    </b>
	    <?}?>
	  </td>
     </tr>
</table>
<?$result = sql($sql) or die;?>
<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=15 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$total_muletos?></td>       
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  
  <tr>
    <td id=mo width=1%>&nbsp;</td>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_comprobante.php",array("sort"=>"8","up"=>$up))?>' >Nro Comp</a></td>      	
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_comprobante.php",array("sort"=>"11","up"=>$up))?>' >Fecha Comp</a></td>      	
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_comprobante.php",array("sort"=>"9","up"=>$up))?>' >Nro Factura</a></td>      	
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_comprobante.php",array("sort"=>"1","up"=>$up))?>' >Apellido</a></td>      	
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_comprobante.php",array("sort"=>"2","up"=>$up))?>'>Nombre</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_comprobante.php",array("sort"=>"3","up"=>$up))?>'>DNI</a></td>   
  </tr>
 <?
   while (!$result->EOF) {   	
    $id_tabla="tabla_".$result->fields['id_comprobante'];	
	$onclick_check=" javascript:(this.checked)?Mostrar('$id_tabla'):Ocultar('$id_tabla')";?>
  
    <tr <?=atrib_tr()?>>
     <td>
	  <input type=checkbox name=check_prestacion value="" onclick="<?=$onclick_check?>" class="estilos_check">
	 </td>     
     <td><?=$result->fields['id_comprobante']?></td>
     <td><?=Fecha($result->fields['fecha_comprobante'])?></td>
     <td ><?=$result->fields['id_factura']?></td>
     <td ><?=$result->fields['afiapellido']?></td>
     <td ><?=$result->fields['afinombre']?></td>
     <td ><?=$result->fields['afidni']?></td> 
    </tr>    
    <tr>
	          <td colspan=10>
	
	                  <?
	                  $sql=" select *
								from facturacion.prestacion 
								left join facturacion.nomenclador using (id_nomenclador)							
								where id_comprobante=". $result->fields['id_comprobante']." order by id_prestacion DESC";
	                  $result_items=sql($sql) or fin_pagina();
	                  ?>
	                  <div id=<?=$id_tabla?> style='display:none'>
	                  <table width=90% align=center class=bordes>
	                  			<?
	                  			$cantidad_items=$result_items->recordcount();
	                  			if ($cantidad_items==0){?>
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
		                            <?while (!$result_items->EOF){?>
			                            <tr>
			                            	 <td class="bordes"><?=$result_items->fields["cantidad"]?></td>			                                 
			                                 <td class="bordes"><?=$result_items->fields["codigo"]?></td>
			                                 <td class="bordes"><?=$result_items->fields["descripcion"]?></td>
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
<br>
	<table align='center' border=1 bordercolor='#000000' bgcolor='#FFFFFF' width='80%' cellspacing=0 cellpadding=0>
     <tr>
      <td colspan=10 bordercolor='#FFFFFF'><b>Colores de Referencia para la Columna Número de Comprobante:</b></td>
     <tr>
     <td width=30% bordercolor='#FFFFFF'>
      <table border=1 bordercolor='#FFFFFF' cellspacing=0 cellpadding=0 width=100%>
       
       <tr>        
        <td width=30 bgcolor='AA888' bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Anulado</td>
       </tr>
      </table>
     </td>
    </table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>
