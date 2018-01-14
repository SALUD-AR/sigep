<?php
/*
Author: ferni 

modificada por
$Author: ferni $
$Revision: 1.30 $
$Date: 2006/07/20 15:22:40 $
*/
require_once("../../config.php");

if (date("Y-m-d")>='2010-09-01'){
	echo "<script>location.href='nino_listado_new.php'</script>";
	exit();
}

variables_form_busqueda("nino_listado");

$orden = array(
        "default" => "1",
        "1" => "nombreefector",
        "2" => "num_doc",       
        "3" => "apellido",       
        "4" => "nombre",        
        "5" => "id_nino",        
        "6" => "fecha_control"        
       );
$filtro = array(		
		"nombreefector" => "Nombre Efector",		
		"to_char(num_doc,'99999999')" => "Documento",		
		"apellido" => "Apellido",		
		"nombre" => "Nombre",		
       );
$sql_tmp="SELECT * FROM trazadoras.nino
			left join nacer.efe_conv using (cuie)";

echo $html_header;

if (permisos_check("inicio","genera_archivo_permiso")) $permiso="";
else $permiso="disabled";
?>
<form name=form1 action="nino_listado.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	    &nbsp;&nbsp;<? $link=encode_link("nino_listado_excel.php",array());?>
        <img src="../../imagenes/excel.gif" style='cursor:hand;'  onclick="window.open('<?=$link?>')">
	  
	    &nbsp;&nbsp;<input type='button' name="nueva_nino" value='Nuevo Dato' onclick="document.location='nino_admin.php'">
	    &nbsp;&nbsp;<input type=submit name="generarnino" value='Generar Archivo' <?=$permiso?>>
	  </td>
     </tr>
</table>

<?$result = sql($sql) or die;

if ($_POST['generarnino']){
	$filename = 'N12200900000001.txt';	

	  	if (!$handle = fopen($filename, 'a')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}
		$result1=sql($sql_tmp) or die;
    	$result1->movefirst();
    	while (!$result1->EOF) {
			$contenido=$result1->fields['cuie'];
			$contenido.=chr(9);
    		$contenido.=chr(9);
    		$contenido.=$result1->fields['clase_doc'];
			$contenido.=chr(9);
    		$contenido.=$result1->fields['tipo_doc'];
			$contenido.=chr(9);
    		$contenido.=number_format($result1->fields['num_doc'],0,'','');
			$contenido.=chr(9);
    		$contenido.=trim($result1->fields['apellido']);
			$contenido.=chr(9);
    		$contenido.=trim($result1->fields['nombre']);
			$contenido.=chr(9);
    		$contenido.=$result1->fields['fecha_nac'];
			$contenido.=chr(9);
    		$contenido.=$result1->fields['fecha_control'];
			$contenido.=chr(9);
			if ($result1->fields['peso']=="0.000000")$peso="";
    		else $peso=number_format($result1->fields['peso'],3,".","");
			$contenido.=$peso;
			$contenido.=chr(9);			
    		if ($result1->fields['talla']=="0.000000")$talla="";
    		else $talla=number_format($result1->fields['talla'],0,"","");
			$contenido.=$talla;
			$contenido.=chr(9);
			if ($result1->fields['perim_cefalico']=="0.000000")$perim_cefalico="";
    		else $perim_cefalico=number_format($result1->fields['perim_cefalico'],3,".","");
			$contenido.=$perim_cefalico;
    		$contenido.=chr(9);
    		$contenido.=$result1->fields['percen_peso_edad'];
			$contenido.=chr(9);
    		if ($result1->fields['percen_talla_edad']=="-1")$percen_talla_edad="";
    		else $percen_talla_edad=$result1->fields['percen_talla_edad'];			
    		$contenido.=$percen_talla_edad;
			$contenido.=chr(9);
    		if ($result1->fields['percen_perim_cefali_edad']=="-1")$percen_perim_cefali_edad="";
    		else $percen_perim_cefali_edad=$result1->fields['percen_perim_cefali_edad'];
    		$contenido.=$percen_perim_cefali_edad;
			$contenido.=chr(9);
    		if ($result1->fields['percen_peso_talla']=="-1")$percen_peso_talla="";
    		else $percen_peso_talla=$result1->fields['percen_peso_talla'];
    		$contenido.=$percen_peso_talla;
			$contenido.=chr(9);
    		if ($result1->fields['triple_viral']!="1980-01-01") $contenido.=$result1->fields['triple_viral'];
			else $contenido.="";
			$contenido.="\n";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$result1->MoveNext();
    	}
		
		//comienzo a grabar archivo de facturacion
    	$result2="SELECT 
					  facturacion.comprobante.cuie,
					  nacer.smiafiliados.afidni,
					  nacer.smiafiliados.afiapellido,
					  nacer.smiafiliados.afinombre,
					  nacer.smiafiliados.afifechanac,
					  facturacion.comprobante.fecha_comprobante
					FROM
					  nacer.smiafiliados
					  INNER JOIN facturacion.comprobante ON (nacer.smiafiliados.id_smiafiliados = facturacion.comprobante.id_smiafiliados)
					  INNER JOIN facturacion.prestacion ON (facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante)
					  INNER JOIN facturacion.nomenclador ON (facturacion.prestacion.id_nomenclador = facturacion.nomenclador.id_nomenclador)
					WHERE
					  (facturacion.nomenclador.codigo = 'NPE 41') AND 
					  (facturacion.comprobante.fecha_comprobante >= '2009-01-01')";
    	$result2=sql($result2,"Error en la inmunizacion de facturacion");
    	$result2->movefirst();
    	while (!$result2->EOF) {
			$contenido=$result2->fields['cuie'];
			$contenido.=chr(9);
    		$contenido.=chr(9);
    		$contenido.="P";
			$contenido.=chr(9);
    		$contenido.="DNI";
			$contenido.=chr(9);
    		$contenido.=number_format($result2->fields['afidni'],0,'','');
			$contenido.=chr(9);
    		$contenido.=trim($result2->fields['afiapellido']);
			$contenido.=chr(9);
    		$contenido.=trim($result2->fields['afinombre']);
			$contenido.=chr(9);
    		$contenido.=$result2->fields['afifechanac'];
			$contenido.=chr(9);
    		$contenido.=Fecha_db(Fecha($result2->fields['fecha_comprobante']));
			$contenido.=chr(9);
    		$contenido.=chr(9);
			$contenido.=chr(9);
			$contenido.=chr(9);
			$contenido.=chr(9);
    		$contenido.=chr(9);
    		$contenido.=chr(9);
    		$contenido.=chr(9);
    		$contenido.=Fecha_db(Fecha($result2->fields['fecha_comprobante']));
			$contenido.="\n";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$result2->MoveNext();
    	}
		
		
    	echo "El Archivo ($filename) se genero con exito";
    
    	fclose($handle);
	
}?>

<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=10 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$total_muletos?></td>       
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  

  <tr>
    <td align=right id=mo><a id=mo href='<?=encode_link("nino_listado.php",array("sort"=>"5","up"=>$up))?>'>ID</a></td>      	    
    <td align=right id=mo><a id=mo href='<?=encode_link("nino_listado.php",array("sort"=>"6","up"=>$up))?>'>Fecha Control</a></td>      	    
    <td align=right id=mo><a id=mo href='<?=encode_link("nino_listado.php",array("sort"=>"1","up"=>$up))?>'>Efector</a></td>      	    
    <td align=right id=mo><a id=mo href='<?=encode_link("nino_listado.php",array("sort"=>"2","up"=>$up))?>'>Documento</a></td>      	    
    <td align=right id=mo><a id=mo href='<?=encode_link("nino_listado.php",array("sort"=>"3","up"=>$up))?>'>Apellido</a></td>      	    
    <td align=right id=mo><a id=mo href='<?=encode_link("nino_listado.php",array("sort"=>"4","up"=>$up))?>'>Nombre</a></td>      	    
  </tr>
 <?
   while (!$result->EOF) {
   	$ref = encode_link("nino_admin.php",array("id_planilla"=>$result->fields['id_nino']));
    $onclick_elegir="location.href='$ref'";?>
  
    <tr <?=atrib_tr()?>>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['id_nino']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=Fecha($result->fields['fecha_control'])?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nombreefector']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=number_format($result->fields['num_doc'],0,'','')?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['apellido']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nombre']?></td>     
    </tr>
	<?$result->MoveNext();
    }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>