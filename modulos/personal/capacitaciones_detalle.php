<?php
/*
AUTOR: Gabriel
MODIFICADO POR:
$Author: ferni $
$Revision: 1.12 $
$Date: 2006/11/21 19:53:21 $
*/

	require("../../config.php");
	require("gutils.php");
	echo $html_header; 
	cargar_calendario();
	//////////////////////////////////////////////////////////////////////////////
	$var_id=array(
		"modo"=>"",
		"locacion"=>"", 
		"dictado"=>"0", 
		"comentarios"=>"", 
		"dictado_desde"=>"", 
		"dictado_hasta"=>"" , 
		"dictado_por"=>"",
		"id_capacitacion"=>"", 
		"tema"=>"", 
	);
	variables_form_busqueda("var_capacitaciones_detalle", $var_id);
	$flag0=false;
	
	if (($parametros["dictado_por"])or($_POST["dictado_por"])){
		$dictado_por=$parametros["dictado_por"] or $dictado_por=$_POST["dictado_por"];
		$flag0=true;
	}else{
		$dictado_por="";
		$flag0=true;
	}

	if ($_POST["hcomentarios"]){
		$comentarios=$_POST["hcomentarios"];
		$flag0=true;
	}
	if ($_POST["hlocacion"]){
		$rta_consulta=sql("select * from personal.base_trabajo where nombre ilike '%".$_POST["hlocacion"]."%'", "No se pudo encontrar la locacion") or fin_pagina();
		$locacion=0;		
		if ($rta_consulta->recordCount()==1) $locacion=$rta_consulta->fields["id_base_trabajo"];
		$flag0=true;
	}
	if ($flag0) phpss_svars_set("_ses_var_capacitaciones_detalle", $_ses_var_capacitaciones_detalle);
	//////////////////////////////////////////////////////////////////////////////
?>
<script>
var warchivos=0;
function moveOver() {
	var boxLength;// = document.form1.compatibles.length;
  var prodLength = document.form1.sel_disponible.length;
  var selectedText;  // = document.choiceForm.available.options[selectedItem].text;
  var selectedValue; // = document.form1.productos.options[selectedItem].value;
  var i;
  var isNew = true;

  arrText = new Array();
  arrValue = new Array();
  var count = 0;

  for (i = 0; i < prodLength; i++) {
    if (document.form1.sel_disponible.options[i].selected) {
      arrValue[count] = document.form1.sel_disponible.options[i].value;
      arrText[count] = document.form1.sel_disponible.options[i].text;
      count++;
    }
	}
  for(j = 0; j < count; j++){
	  isNew = true;
		boxLength = document.form1.sel_afectado.length;
		selectedText=arrText[j];
 		selectedValue=arrValue[j];
		if (boxLength != 0) {
  	  for (i = 0; i < boxLength; i++) {
  		  thisitem = document.form1.sel_afectado.options[i].text;
      	if (thisitem == selectedText) {
        	isNew = false;
	      }
  	  }
	  }
  	if (isNew) {
  		newoption = new Option(selectedText, selectedValue, false, false);
	    document.form1.sel_afectado.options[boxLength] = newoption;
  	}
	  document.form1.sel_disponible.selectedIndex=-1;
  } 
}

function removeMe() {
  var boxLength = document.form1.sel_afectado.length;
  arrSelected = new Array();
  var count = 0;
  for (i = 0; i < boxLength; i++) {
    if (document.form1.sel_afectado.options[i].selected) {
      arrSelected[count] = document.form1.sel_afectado.options[i].value;
    }
    count++;
  }
  var x;
  for (i = 0; i < boxLength; i++) {
    for (x = 0; x < arrSelected.length; x++) {
      if (document.form1.sel_afectado.options[i].value == arrSelected[x]) {
        document.form1.sel_afectado.options[i] = null;
      }
    }
    boxLength = document.form1.sel_afectado.length;
  }
}
function val_text(){
	var a=new Array();
  var largo=document.form1.sel_afectado.length;
  var i=0;
  
  for(i;i<largo;i++){
  	a[i]=document.form1.sel_afectado.options[i].value;
  }
	document.form1.afectadosValues.value=a;
	document.form1.hguardar.value='sip';
	if ((typeof(document.form1.tcomentarios.value)!="undefined")&&(document.form1.tcomentarios.value!="")) document.form1.hcomentarios.value=document.form1.tcomentarios.value;
	else document.form1.hcomentarios.value=" ";
}
function update_disponibles(sel){
	var backup_value=new Array();
	var backup_text=new Array();
	var obj=document.form1.sel_disponible;
	
	document.form1.hlocacion.value=sel.options[sel.selectedIndex].text;

	if (document.form1.hbck_text.value==''){
		for(i=obj.length-1; i>=0; i--){
			backup_value[i]=obj.options[i].value+"|";
			backup_text[i]=obj.options[i].text+"|";
		}
		document.form1.hbck_value.value=backup_value;
		document.form1.hbck_text.value=backup_text;
	}
	
	var str_value= new String(document.form1.hbck_value.value);
	var str_text= new String(document.form1.hbck_text.value);
	backup_value=str_value.split("|");
	backup_text=str_text.split("|");
	for (i=obj.length-1; i>=0; i--) obj.options[i]=null;
	for (i=0, j=0; i<backup_text.length; i++){
		if ((backup_text[i].indexOf(document.form1.hlocacion.value)!=-1)||(document.form1.hlocacion.value==" ")){
			if (i!=0){
				var strt=backup_text[i].substring(1, backup_text[i].length);
				var strv=backup_value[i].substring(1, backup_value[i].length);
			}else{
				var strt=backup_text[i];
				var strv=backup_value[i];
			}
			newoption = new Option(strt, strv, false, false);
		  obj.options[j++] = newoption;
		}
	}	
}
function clearfields(){
	document.form1.afectadosValues.value="";
	document.form1.hlocacion.value="";
	document.form1.hbck_value.value="";
	document.form1.hbck_text.value="";
	document.form1.hguardar.value="";
}
</script>
<?
	if($parametros['accion']!=""){ Aviso($parametros['accion']);}
	//////////////////////////////////////////////////////////////////////////////
	?>
	<form name="form1" method="POST" action="capacitaciones_detalle.php">
		<input type='hidden' name='afectadosValues' value=''>
		<input type='hidden' name='hlocacion' value='<?=$_POST["hlocacion"]?>'>
		<input type='hidden' name='hbck_value' value=''>
		<input type='hidden' name='hbck_text' value=''>
		<input type='hidden' name='hguardar' value=''>
		<input type='hidden' name='id_capacitacion' value=''>
		<input type='hidden' name='hcomentarios' value=''>
	<?
	if ($modo=="borrar_archivo") {
		$id=$parametros["id_archivo"];
		$filename=$parametros["filename"];
		$db->beginTrans();
		$query="delete from archivos_capacitaciones where id=$id and id_capacitacion=".$parametros["id_capacitacion"];
		sql($query, "Error al eliminar el Archivo ".$filename) or fin_pagina();
		if ((!$error)&&(unlink(UPLOADS_DIR."/archivos/$filename"))) $db->commitTrans();
		else{
			$db->Rollback();
			echo "<script>alert('No se pudo borrar el archivo')</script>";
		}
		$new="";
		$modo="modif";
	}
	if ($modo=="nuevo"){
		$dictado_desde=" ";
		$dictado_hasta=" ";
		$tema="";
		$comentarios="";
		$locacion="";
		$dictado=0;
		$titulo_tabla="Curso de capacitación nuevo";
		$rta=sql("select nextval('capacitaciones_id_capacitacion_seq') as id_cap");
		$id_capacitacion=$rta->fields["id_cap"];
		phpss_svars_set("_ses_var_capacitaciones", $_ses_var_capacitaciones);
		$new="disabled";
		echo "<script>clearfields(); document.form1.id_capacitacion.value='$id_capacitacion'</script>";
	}
	if (($_POST["bguardar"])||($_POST["hguardar"])){
		if (($dictado_desde==" ")||($dictado_desde=="?")) $dictado_desde_cad="null";
		else $dictado_desde_cad="'".Fecha_db($dictado_desde)."'";
		if (($dictado_hasta==" ")||($dictado_hasta=="?")) $dictado_hasta_cad="null";
		else $dictado_hasta_cad="'".Fecha_db($dictado_hasta)."'";
		if ($_POST["chdictado"]=="check") $dictado=1;
		else $dictado=0;
		$temporal=sql("select * from capacitaciones where id_capacitacion=$id_capacitacion") or fin_pagina();
		if ($temporal->recordCount()==0){
			$sql="insert into capacitaciones (id_capacitacion, tema, dictado_desde, dictado_hasta, comentarios, locacion, dictado, dictado_por)";
			$sql.="values ($id_capacitacion, '$tema', ".$dictado_desde_cad.", ".$dictado_hasta_cad.", '".$comentarios."', '".$locacion."', $dictado, '".$dictado_por."')";
		}else{
			$sql="update capacitaciones set tema='".$tema."', dictado_desde=".$dictado_desde_cad.", dictado_hasta=".$dictado_hasta_cad.", comentarios='".$comentarios."', locacion='".$locacion."', dictado=$dictado, dictado_por='".$dictado_por."' ";
			$sql.=" where id_capacitacion=".$id_capacitacion;
		}
		sql($sql, "Error al agregar/actualizar registro") or fin_pagina();
		if ($_POST["afectadosValues"]){
			sql("delete from capacitados where id_capacitacion=$id_capacitacion") or fin_pagina();
			$array=explode(",", $_POST["afectadosValues"]);
  		$tam=count($array);
	  	for($i=0; $i<$tam; $i++){
		    $id_legajo=$array[$i];
  		  $query="insert into capacitados(id_capacitacion, id_legajo) values(".$id_capacitacion.",".$id_legajo.")";
    		sql($query) or fin_pagina();
			}
		}
	}
	if ($modo=="modif"){
		$titulo_tabla="Datos del curso ".(($dictado==0)?"(aún no fue dictado)":"(ya fue dictado)");
		$new="";
	}
	$sql_archivos="select * from archivos_capacitaciones where id_capacitacion=$id_capacitacion";
	$sql_disponibles="select id_legajo, l.nombre, apellido, d.nombre as ubicacion from legajos l
		join personal.base_trabajo d on(ubicacion=id_base_trabajo) where activo=1 order by apellido, nombre";
	$sql_afectados="select l.apellido, l.nombre, d.nombre as ubicacion, l.id_legajo from capacitados c join legajos l using (id_legajo) 
		join personal.base_trabajo d on (id_base_trabajo=ubicacion) ";
	$sql_afectados.="where c.id_capacitacion=$id_capacitacion order by l.apellido, l.nombre";
	echo "<input type='hidden' name='modo' value='modif'>";
	$modo="modif";
	if ((!$dictado_desde)||($dictado_desde=='?')) $dictado_desde='';
	if ((!$dictado_hasta)||($dictado_hasta=='?')) $dictado_hasta='';
	?>
		<table border="1" cellspacing="0" bgcolor="<?=$bgcolor2?>" width="90%" align="center">
			<colgroup width="20%" id="mo" align="left"></colgroup>
			<th align="center" colspan="3"><?=$titulo_tabla?></th>
			<tr>
				<td>Tema:</td>
				<td colspan="2">
					<input type="text" name="tema" value="<?=$tema?>" size="100" style="width:100%"></input>
				</td>
			</tr>
			<tr>
				<td>Período de dictado:</td>
				<td align="center">Desde <input type="text" name="dictado_desde" value="<?=$dictado_desde?>" readonly></input>&nbsp;<?=link_calendario("dictado_desde")?></td>
				<td align="center">Hasta <input type="text" name="dictado_hasta" value="<?=$dictado_hasta?>" readonly></input>&nbsp;<?=link_calendario("dictado_hasta")?></td>
			</tr>
			<tr>
				<td >Instalaciones:</td>
				<td colspan="1">
					<?
						$rta_sql=sql("select * from personal.base_trabajo") or fin_pagina();
						$i=1;
						$pcias_id[0]="0";
						$pcias_nombre[0]=" ";
						while (!$rta_sql->EOF){
							$pcias_id[$i]=$rta_sql->fields["id_base_trabajo"];
							$pcias_nombre[$i++]=$rta_sql->fields["nombre"];
							$rta_sql->movenext();
						}
						g_draw_value_select("sel_locacion", $locacion, $pcias_id, $pcias_nombre, 1, "onchange='update_disponibles(this);'");?>
				</td>
				<td>
					Dictado por: <input type="text" name="dictado_por" value="<?=$dictado_por?>">
				</td>
			</tr>
			<tr>
				<td>Comentarios:</td>
				<td colspan="2">
					<textarea rows="2" name="tcomentarios" cols="80" style="width:100%"><?=$comentarios?></textarea>
				</td>
			</tr>
			<tr>
				<td>Dictado:</td>
				<td colspan="2">
				<?
					if ($dictado!=0) $checked="checked";
				?>
					<input type="checkbox" name="chdictado" value="check" <?=$checked?>>
						(marcar para indicar que el curso se dictó)
					</input>
				</td>
			</tr>
		</table>
		<table border="1" cellspacing="0" bgcolor="<?=$bgcolor3?>" width="90%" align="center">
			<tr><td align="center" colspan="3" id="mo">Personal</td></tr>
			<tr id="mo"><td width="45%">Disponibles</td><td>&nbsp;</td><td width="45%">Afectados al curso</td></tr>
			<tr>
				<td>
					<?
					$rta_disponibles=sql($sql_disponibles) or fin_pagina();
					while (!$rta_disponibles->EOF){
						$datos[]=$rta_disponibles->fields["apellido"].", ".$rta_disponibles->fields["nombre"]." (".$rta_disponibles->fields["ubicacion"].")";
						$datos_ver[]=$rta_disponibles->fields["apellido"].", ".$rta_disponibles->fields["nombre"];
						$datos_id[]=$rta_disponibles->fields["id_legajo"];
						$rta_disponibles->movenext();
					}
					g_draw_value_select("sel_disponible", $datos[0], $datos_id, $datos, 10, "multiple style='width:100%'");
					?>
					<script>update_disponibles(document.form1.sel_locacion);</script>
				</td>
				<td align="center">
					<input type="button" name="add" value=">>" onclick="moveOver();"><br>
					<input type="button" name="sub" value="<<" onclick="removeMe();"><br>
				</td>
				<td>
					<?
					$rta_afectados=sql($sql_afectados) or fin_pagina();
					while (!$rta_afectados->EOF){
						$datos2[]=$rta_afectados->fields["apellido"].", ".$rta_afectados->fields["nombre"]." (".$rta_afectados->fields["ubicacion"].")";
						$datos_id2[]=$rta_afectados->fields["id_legajo"];
						$rta_afectados->movenext();
					}
					g_draw_value_select("sel_afectado", $datos2[0], $datos_id2, $datos2, 10, "multiple style='width:100%'");
					?>
				</td>
			</tr>
		</table>
		<table border="0" cellspacing="0" bgcolor="<?=$bgcolor2?>" width="90%" align="center">
			<tr>
				<td align="center">
				
					<input type="button" name="bguardar" value="Guardar cambios" onclick="val_text(); window.document.form1.submit();">
					
					<input type="button" name="volver" value="Volver" onclick="document.location='capacitaciones.php'">
				</td>
			</tr>
		</table>
		<br>
		<table border="1" cellspacing="0" bgcolor="<?=$bgcolor3?>" width="90%" align="center">
			<tr>
				<td align="center" colspan="5">
				<?$rta_archivos=sql($sql_archivos) or fin_pagina();?>
					Archivos (<?=$rta_archivos->recordcount()?> en total)
				<?
				
?>
					<input type="button" name="bagregar" value="Agregar" onclick="if (typeof(warchivos)=='object' && warchivos.closed || warchivos==false) warchivos=window.open('<?= encode_link($html_root.'/modulos/archivos/archivos_subir.php',array("id_capacitacion"=>$id_capacitacion, "user"=>$_ses_user["name"], "onclickaceptar"=>"window.self.focus();", "proc_file"=>"../personal/orden_file_proc.php")) ?>','','toolbar=0,location=0,directories=0,status=0, menubar=0,scrollbars=1'); else warchivos.focus()" <?=$new?>>
					
				</td>	
			</tr>
<?
			if ($rta_archivos->recordcount()>0){
?>
			<tr>
				<td align=right id=mo>Archivo</td>
				<!--<td align=right id=mo>Fecha</td>-->
				<td align=right id=mo>Subido por</td>
				<td align=right id=mo>Tamaño</td>
				<td align=center id=mo>&nbsp;</td>
			</tr>
<?
				while (!$rta_archivos->EOF){
					echo "<tr style='font-size: 9pt'><td align=center>";
 					if (is_file("../../uploads/archivos/".$rta_archivos->fields["nombre"])) echo "<a target=_blank href='".encode_link("../archivos/archivos_lista.php", array ("file" =>$rta_archivos->fields["nombre"],"size" => $rta_archivos->fields["size"],"cmd" => "download"))."'>";
				  echo $rta_archivos->fields["nombre"]."</a></td>";
		?>    
  	  			<!--<td align=center>&nbsp;<?//=Fecha($rta_archivos->fields["fecha"])?></td>-->
				    <td align=center>&nbsp;<?= $rta_archivos->fields["creadopor"] ?></td>
				    <td align=center>&nbsp;<?= $size=number_format($rta_archivos->fields["size"] / 1024); ?> Kb</td>
	    			<td align=center>
		<?    
					$lnk=encode_link("$_SERVER[PHP_SELF]",Array("id_capacitacion"=>$id_capacitacion,"id_archivo"=>$rta_archivos->fields["id"],"filename"=>$rta_archivos->fields["nombre"],"modo"=>"borrar_archivo"));
		      
		      	echo "<a href='$lnk'><img src='../../imagenes/close1.gif' border=0 alt='Eliminar el archivo: \"". $rta_archivos->fields["nombre"] ."\"'></a>";
		      
	  	    echo "</td></tr>";
					$rta_archivos->movenext();
				}
			}
?>		
		</table>
<?
	echo "</form>";
	fin_pagina();
?>
