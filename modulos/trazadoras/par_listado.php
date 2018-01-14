<?php
/*
Author: ferni 

modificada por
$Author: gaby $
$Revision: 1.30 $
$Date: 2012/05/20 15:22:40 $
*/
require_once("../../config.php");
cargar_calendario();
variables_form_busqueda("par_listado");

$orden = array(
        "default" => "1",
        "1" => "nombreefector",
        "2" => "num_doc",       
        "3" => "apellido",       
        "4" => "nombrepers",        
        "5" => "id_par",        
        "6" => "fecha_parto"        
       );
$filtro = array(		
		"efe_conv.nombre" => "Nombre Efector",		
		"to_char(num_doc,'99999999')" => "Documento",		
		"apellido" => "Apellido",		
		"partos.nombre" => "Nombre",			
       );
       
 $fecha_hoy=date("Y-m-d H:i:s");
$fecha_hoy=fecha($fecha_hoy);

if ($_POST['muestra']=="Muestra"){
	$cuie=$_POST['cuie'];
	$fecha_desde=$_POST['fecha_desde'];
	$fecha_hasta=$_POST['fecha_hasta'];
	
	$link=encode_link("par_listado_excel.php",array("cuie"=>$cuie,"fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));?>
	<script>
	window.open('<?=$link?>')
	</script>
<?}      
       
$sql_tmp="SELECT *, efe_conv.nombre as nombreefector, partos.nombre as nombrepers
			FROM trazadoras.partos
			left join nacer.efe_conv using (CUIE)";

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
<form name=form1 action="par_listado.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	    &nbsp;&nbsp;<input type='button' name="nueva_par" value='Nuevo Dato' onclick="document.location='par_admin.php'">
	    &nbsp;&nbsp;||&nbsp;&nbsp;
	    <?if (permisos_check("inicio","genera_archivo_permiso")) $permiso="";
		else $permiso="disabled";
	    if ($permiso==""){?>
	    <b>Desde: <input type=text id=fecha_desde_t name=fecha_desde_t value='<?=fecha($fecha_desde_t)?>' size=15 readonly>
		<?=link_calendario("fecha_desde_t");?>
		Hasta: <input type=text id=fecha_hasta_t name=fecha_hasta_t value='<?=fecha($fecha_hasta_t)?>' size=15 readonly>
		<?=link_calendario("fecha_hasta_t");?> 
		<input type=text id=nom_arch name=nom_arch value='P12201200000001.txt' size=20>
		<input type=submit name="generarpar" value='Generar Archivo' <?=$permiso?>>
		</b>
		<?}?>
	 </td>
     </tr>
     <tr align="center">
	  <td>
	  <br>
	    	  <b>Desde: <input type=text id=fecha_desde name=fecha_desde value='<?=fecha($fecha_desde)?>' size=15 readonly>
				<?=link_calendario("fecha_desde");?>
		
			  Hasta: <input type=text id=fecha_hasta name=fecha_hasta value='<?=fecha($fecha_hasta)?>' size=15 readonly>
				<?=link_calendario("fecha_hasta");?> 
				
			  Efector: </b>
			 <select name=cuie Style="width=257px" 
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer();"
				onchange="borrar_buffer();" >
				<option value='todos' selected>Todos</option>
				<?$sql01= "select * from nacer.efe_conv 
			 		order by nombre";
				$res_efectores01=sql($sql01) or fin_pagina();
				while (!$res_efectores01->EOF){ 
					$cuiel_1=$res_efectores01->fields['cuie'];
					$nombre_efector_1=$res_efectores01->fields['nombre'];?>
				<option value='<?=$cuiel_1?>' <?if ($cuie==$cuiel_1) echo "selected"?> ><?=$nombre_efector_1?></option>
			    <?$res_efectores01->movenext();
			    }?>
			</select>
			
			<input type="submit" name="muestra" value='Muestra' onclick="return control_muestra()" >
    	  </b>     
	  <td>
	  </td>
     </tr>
</table>

<?$result = sql($sql) or die;

if ($_POST['generarpar']){
	$nom_arch=$_POST['nom_arch'];
	$fecha_desde_t=$_POST['fecha_desde_t'];
	$fecha_hasta_t=$_POST['fecha_hasta_t'];
	$fecha_desde_t= fecha_db($fecha_desde_t);
	$fecha_hasta_t= fecha_db($fecha_hasta_t);
	
	$filename = $nom_arch;	

	  	if (!$handle = fopen($filename, 'w+')) {
        	 echo "No se Puede abrir ($filename)";
         	exit;
    	}
		$sql_tmp_arch=$sql_tmp." where fecha_parto >= '$fecha_desde_t' and fecha_parto <= '$fecha_hasta_t'";
		$result1=sql($sql_tmp_arch) or die;
    	$result1->movefirst();
    	while (!$result1->EOF) {
    		$contenido=$result1->fields['cuie'];
    		$contenido.=chr(9);
    		$contenido.=chr(9);
    		$contenido.=$result1->fields['tipo_doc'];
    		$contenido.=chr(9);
    		$contenido.=number_format($result1->fields['num_doc'],0,'','');
    		$contenido.=chr(9);
    		$contenido.=trim($result1->fields['apellido']);
    		$contenido.=chr(9);
    		$contenido.=trim($result1->fields['nombrepers']);
    		$contenido.=chr(9);
    		$contenido.=$result1->fields['fecha_parto'];
    		$contenido.=chr(9);
    		$contenido.=number_format($result1->fields['apgar'],0,"","");
    		$contenido.=chr(9);
    		$contenido.=number_format($result1->fields['peso'],3,".","");
    		$contenido.=chr(9);
    		$contenido.=$result1->fields['vdrl'];
    		$contenido.=chr(9);
    		$contenido.=$result1->fields['antitetanica'];
    		$contenido.=chr(9);
    		if ($result1->fields['fecha_conserjeria']!="1980-01-01") $contenido.=$result1->fields['fecha_conserjeria'];    		
    		else $contenido.=$result1->fields['fecha_parto'];
    		$contenido.="\n";
    		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
    		$result1->MoveNext();
    	}
    	echo "<b>El Archivo ($filename) se genero con exito</b>";
    
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
    <td align=right id=mo><a id=mo href='<?=encode_link("par_listado.php",array("sort"=>"5","up"=>$up))?>'>ID</a></td>      	    
    <td align=right id=mo><a id=mo href='<?=encode_link("par_listado.php",array("sort"=>"6","up"=>$up))?>'>Fecha Parto</a></td>      	    
    <td align=right id=mo><a id=mo href='<?=encode_link("par_listado.php",array("sort"=>"1","up"=>$up))?>'>Efector</a></td>      	    
    <td align=right id=mo><a id=mo href='<?=encode_link("par_listado.php",array("sort"=>"2","up"=>$up))?>'>Documento</a></td>      	    
    <td align=right id=mo><a id=mo href='<?=encode_link("par_listado.php",array("sort"=>"3","up"=>$up))?>'>Apellido</a></td>      	    
    <td align=right id=mo><a id=mo href='<?=encode_link("par_listado.php",array("sort"=>"4","up"=>$up))?>'>Nombre</a></td>      	    
  </tr>
 <?
   while (!$result->EOF) {
   	$ref = encode_link("par_admin.php",array("id_planilla"=>$result->fields['id_par']));
    $onclick_elegir="location.href='$ref'";?>
  
    <tr <?=atrib_tr()?>>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['id_par']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=Fecha($result->fields['fecha_parto'])?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nombreefector']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=number_format($result->fields['num_doc'],0,'','')?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['apellido']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nombrepers']?></td>     
    </tr>
	<?$result->MoveNext();
    }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>
