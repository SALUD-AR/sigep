<?php

require_once("../../config.php");

if ($parametros['cambia_traba']=="si"){   
   $traba=$parametros['traba'];
   $id_factura=$parametros['id_factura'];
   $fecha_carga=date("Y-m-d");
   $db->StartTrans();    
   $query="update facturacion.factura set 
   				traba='$traba'   				
   			where id_factura=$id_factura";
   sql($query, "Error al trabar la factura") or fin_pagina();
       
   /*cargo los log*/ 
    $usuario=$_ses_user['name'];
	$log="insert into facturacion.log_factura
		   (id_factura, fecha, tipo, descripcion, usuario) 
	values ($id_factura, '$fecha_carga','$traba : Trabo la Factura','La Factura $id_factura', '$usuario')";
	sql($log) or fin_pagina();
	 
    $db->CompleteTrans();    
	
}

variables_form_busqueda("listado_fact");

$fecha_hoy=date("Y-m-d H:i:s");
$fecha_hoy=fecha($fecha_hoy);

if ($cmd == "")  $cmd="A";

$orden = array(
        "default" => "1",
        "default_up" => "0",
        "1" => "id_factura",
        "2" => "cuie",
        "3" => "nombre",
        "4" => "fecha_factura",
        "5" => "fecha_carga",
        "6" => "periodo",
        "7" => "observaciones",
        "8" => "estado",
		"9" => "periodo_actual",
       );
$filtro = array(
		"to_char(factura.id_factura,'999999')" => "Nro. Factura",
		"cuie" => "CUIE",
        "periodo" => "Periodo",                
       );
$datos_barra = array(
     array(
        "descripcion"=> "Abiertas",
        "cmd"        => "A"
     ),
     array(
        "descripcion"=> "Cerradas",
        "cmd"        => "C"
     ),
     array(
        "descripcion"=> "Anuladas",
        "cmd"        => "X"
     ),
     array(
        "descripcion"=> "Todas",
        "cmd"        => "T"
     )
);

generar_barra_nav($datos_barra);

$sql_tmp="SELECT 
  facturacion.factura.cuie,
  facturacion.factura.fecha_carga,
  facturacion.factura.fecha_factura,
  facturacion.factura.periodo,
  facturacion.factura.periodo_actual,
  facturacion.factura.estado,
  facturacion.factura.observaciones,
  facturacion.factura.id_factura,
  facturacion.factura.traba,
  facturacion.factura.online,
  facturacion.factura.alta_comp,
  nacer.efe_conv.nombre
FROM
  facturacion.factura
  LEFT JOIN nacer.efe_conv using (cuie)";

$user_login1=substr($_ses_user['login'],0,6);

if ($cmd=="A"){	
    if (es_cuie($_ses_user['login']))
    $where_tmp=" (factura.estado='A') and cuie='$user_login1'";
    else
    $where_tmp=" (factura.estado='A')";    
}

if ($cmd=="C"){
	if (es_cuie($_ses_user['login']))
    $where_tmp=" (factura.estado='C') and cuie='$user_login1'";
    else
    $where_tmp=" (factura.estado='C')";
}

if ($cmd=="X"){
     if (es_cuie($_ses_user['login']))
    $where_tmp=" (factura.estado='X') and cuie='$user_login1'";
    else
    $where_tmp=" (factura.estado='X')";
}
if ($cmd=="T"){
     if (es_cuie($_ses_user['login']))
    $where_tmp=" cuie='$user_login1'";
}

echo $html_header;
?>
<form name=form1 action="listado_factura.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	    &nbsp;&nbsp;<input type='button' name="nueva_factura" value='Nueva Factura' onclick="document.location='factura_admin.php'">
	  </td>
     </tr>
</table>

<?$result = sql($sql) or die;?>

<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=12 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$total_muletos?></td>       
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  

  <tr>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_factura.php",array("sort"=>"1","up"=>$up))?>'>Nro Factura</a></td>      	
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_factura.php",array("sort"=>"2","up"=>$up))?>'>CUIE</a></td>      	
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_factura.php",array("sort"=>"3","up"=>$up))?>'>Efector</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_factura.php",array("sort"=>"4","up"=>$up))?>'>Fecha Factura</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_factura.php",array("sort"=>"5","up"=>$up))?>'>Fecha Carga</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_factura.php",array("sort"=>"6","up"=>$up))?>'>Periodo Actual</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_factura.php",array("sort"=>"9","up"=>$up))?>'>Periodo Prestacion</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_factura.php",array("sort"=>"7","up"=>$up))?>'>Observaciones</a></td>
    <?if ($cmd=="C"){?>
    <td align=right id=mo>Total</td>
    <td align=right id=mo>Excel</td>
    <td align=right id=mo>PRN</td>
    <td align=right id=mo>RES</td>
    <!--<td align=right id=mo>Traba</td>-->
    <?}?>
    <?if ($cmd=="T"){?>
    <td align=right id=mo><a id=mo href='<?=encode_link("listado_factura.php",array("sort"=>"8","up"=>$up))?>'>Estado</a></td>
    <?}?>
  </tr>
 <?
   while (!$result->EOF) {
   	$ref = encode_link("factura_admin.php",array("id_factura"=>$result->fields['id_factura'],"cuie"=>$cuie));
    $onclick_elegir="location.href='$ref'";?>
  
    <tr <?=atrib_tr()?>>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['id_factura']?></td>
     <?$cuie=$result->fields['cuie'];
     	$query="select count (cuie) as total from nacer.efe_conv where com_gestion = 'VERDADERO'and cuie='$cuie'";
		$convenio=sql($query,"error en efe_conv");
		$convenio=$convenio->fields['total'];
		if ($convenio==0){
			$color='red'; 
			$title='NO TIENE CONVENIO';
		}
		else {
			$color=''; 
			$title='';
		} 
		
		if ($result->fields['online']=='SI'){
			$color='#00FF00'; 
			$title='FACTURA GENERADA FUERA DE PLAN NACER';
		}
		else {
			$color=''; 
			$title='';
		} 
		if ($result->fields['alta_comp']=='SI'){
			$color_1='#A9BCF5'; 
			$title_1='FACTURA ALTA COMPLEJIDAD';
		}
		else {
			$color_1=''; 
			$title_1='';
		} 
		?>          
     <td onclick="<?=$onclick_elegir?>" bgcolor="<?=$color?>" title="<?=$title?>"><?=$result->fields['cuie']?></td>
     <td onclick="<?=$onclick_elegir?>" bgcolor="<?=$color_1?>" title="<?=$title_1?>"><?=$result->fields['nombre']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=fecha($result->fields['fecha_factura'])?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=Fecha($result->fields['fecha_carga'])?></td> 
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['periodo']?></td> 
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['periodo_actual']?></td> 
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['observaciones']?></td> 
     <?if ($cmd=="C"){
     	$id_factura=$result->fields['id_factura'];
     	$query_t="SELECT sum 
			(facturacion.prestacion.cantidad*facturacion.prestacion.precio_prestacion) as total
			FROM
			  facturacion.factura
			  INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
			  INNER JOIN facturacion.prestacion ON (facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante)
			  where factura.id_factura=$id_factura";
		$total=sql($query_t,"NO puedo calcular el total");
		$query_t1="SELECT sum 
			(nomenclador.prestaciones_n_op.precio) as total1
			FROM
			  facturacion.factura
			  INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
			  INNER JOIN nomenclador.prestaciones_n_op using (id_comprobante)
			  where factura.id_factura=$id_factura";
		$total1=sql($query_t1,"NO puedo calcular el total");
		$total=$total->fields['total']+$total1->fields['total1'];?>
       <td align="center">
       		<?=number_format($total,2,',','.');?>
       </td>
       <td align="center">
       	 <?$link=encode_link("factura_excel.php", array("id_factura"=>$result->fields['id_factura']));	
		   echo "<a target='_blank' href='".$link."' title='Imprime Factura'><IMG src='$html_root/imagenes/excel.gif' height='20' width='20' border='0'></a>";?>
       </td>
       <td align="center">
       	 <?$link=encode_link("factura_pdf.php", array("id_factura"=>$result->fields['id_factura']));?>	
		   <a target='_blank' href='<?=$link?>' title='Imprime Factura' onclick="return confirm('Recuerde que puede Imprimir el Resumen de Prestaciones para presentar al Plan Nacer y evitar el gasto de impresiones. El Resumen se obtiene haciendo clic en el icono que esta justo al lado. ¿Esta Seguro que Desea Continuar? ')"><IMG src='<?=$html_root?>/imagenes/pdf_logo.gif' height='20' width='20' border='0'></a>
       </td>
       <td align="center">
       	 <?$link=encode_link("factura_pdf_res.php", array("id_factura"=>$result->fields['id_factura']));	
		   echo "<a target='_blank' href='".$link."' title='Imprime Factura'><IMG src='$html_root/imagenes/pdf_logo.gif' height='20' width='20' border='0'></a>";?>
       </td>
       <?/*if ($result->fields['traba']=="si"){ ?>
       <td align="center">
       	  <?if (permisos_check('inicio','permiso_destraba_factura')){
       	   		$link=encode_link("listado_factura.php", array("cambia_traba"=>"si","traba"=>"no","id_factura"=>$id_factura));	?>
		   		<a href="<?=$link?>" title="Destraba Factura" onclick="return confirm('Se DESTRABARA la Factura. Esta Seguro?')"><IMG src='<?=$html_root?>/imagenes/candado1.gif' height='20' width='20' border='0'></a>
		   <?}
		   else{?>
		   		<a title="Destraba Factura" onclick="return alert('No tiene Permisos')"><IMG src='<?=$html_root?>/imagenes/candado1.gif' height='20' width='20' border='0'></a>
		   <?}?>
       </td>
       <?}
       else{?>
       <td align="center">
        <?if (permisos_check('inicio','permiso_destraba_factura')){
       	 	$link=encode_link("listado_factura.php", array("cambia_traba"=>"si","traba"=>"si","id_factura"=>$id_factura));?>
		   <a href="<?=$link?>" title="Traba Factura" onclick="return confirm('Se TRABARA la Factura (No se Podran Realizar mas Cambios). Esta Seguro?')"><IMG src='<?=$html_root?>/imagenes/restaurar_usr.gif' height='20' width='20' border='0'></a>
        <?}
		   else{?>
		   		<a title="Destraba Factura" onclick="return alert('No tiene Permisos')"><IMG src='<?=$html_root?>/imagenes/restaurar_usr.gif' height='20' width='20' border='0'></a>
		   <?}?>
		
		</td>
       <?}*/?>
    <?}?>
    <?if ($cmd=="T"){ 
    	if ($result->fields['estado']=='A') $estado_aux='Abierta'; 
    	if ($result->fields['estado']=='C') $estado_aux='Cerrada';
    	if ($result->fields['estado']=='X') $estado_aux='Anulada'?> 
    	<td onclick="<?=$onclick_elegir?>"><?=$estado_aux?></td>
    <?}?>    
    </tr>
	<?$result->MoveNext();
    }?>
    
</table>

<br>
	<table align='center' border=1 bordercolor='#000000' bgcolor='#FFFFFF' width='80%' cellspacing=0 cellpadding=0>
     <tr>
      <td colspan=10 bordercolor='#FFFFFF'><b>Colores de Referencia para la Columna CUIE:</b></td>
     <tr>
     <td width=30% bordercolor='#FFFFFF'>
      <table border=1 bordercolor='#FFFFFF' cellspacing=0 cellpadding=0 width=100%>
       <tr>
        <td width=30 bgcolor='red' bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Efector no Tiene Convenio</td>
       </tr> 
       <tr>
        <td width=30 bgcolor='#00FF00' bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Factura Generada Fuera de Plan Nacer</td>
       </tr>       
      </table>
     </td>
     
     <tr>
      <td colspan=10 bordercolor='#FFFFFF'><b>Colores de Referencia para la Columna EFECTOR:</b></td>
     <tr>
	 <td width=30% bordercolor='#FFFFFF'>
      <table border=1 bordercolor='#FFFFFF' cellspacing=0 cellpadding=0 width=100%>
       <tr>
        <td width=30 bgcolor='#A9BCF5' bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Factura con prestaciones de Alta Complejidad</td>
       </tr>              
      </table>
     </td>
    </table>
    
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>
