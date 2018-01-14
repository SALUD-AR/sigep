<?php
/*
AUTOR: Gabriel
MODIFICADO POR:
$Author: ferni $
$Revision: 1.8 $
$Date: 2006/11/15 19:41:46 $
*/
	require("../../config.php");
	require_once("../personal/gutils.php");
	
	$modo=$parametros["modo"] or $modo=$_POST["modo"];
	$id_auditoria_calidad=$parametros["id_auditoria_calidad"] or $id_auditoria_calidad=$_POST["id_auditoria_calidad"];
	$titulo=$parametros["titulo"] or $titulo=$_POST["t_titulo"];
	$tipo=$parametros["tipo"] or $tipo=$_POST["sel_tipo"];
	$planta=$parametros["planta"] or $planta=$_POST["sel_planta"];
	$fecha_desde=$parametros["fecha_desde"] or $fecha_desde=Fecha_db($_POST["fecha_desde"]);
	$fecha_hasta=$parametros["fecha_hasta"] or $fecha_hasta=Fecha_db($_POST["fecha_hasta"]);
	$estado=$parametros["estado_auditoria"] or $estado=$_POST["estado"];
	$cambio_estado=$parametros["cambio_estado"] or $cambio_estado=$_POST["cambio_estado"];
	$afectadosValues=$_POST["afectadosValues"];	
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$sels=array("plantas"=>"", "departamentos"=>"");
	$rta_consulta=sql("select id_base_trabajo as key, nombre as label from personal.base_trabajo order by nombre", "c25") or fin_pagina();
	$i=0;
	while (!$rta_consulta->EOF){
		$sels["plantas"][$i++]=$rta_consulta->fetchRow();
	}
	$rta_consulta=sql("select id_departamento_empresa as key, nombre_departamento as label from general.departamentos_empresa order by nombre_departamento", "c27") or fin_pagina();
	$i=0;
	while (!$rta_consulta->EOF){
		$sels["departamentos"][$i++]=$rta_consulta->fetchRow();
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if ($_POST["estado"]=="p") $estado="p";
	elseif ($_POST["estado"]=="h") $estado="h";
	else  $estado="a";	
	if ($modo=="borrar_archivo"){
		$id=$parametros["id_archivo"];
		$filename=$parametros["filename"];
		$db->beginTrans();
		$query="delete from calidad.auditorias_calidad_archivos where id=$id and id_auditoria_calidad=".$id_auditoria_calidad;
		$db->Execute($query);
		if ((!$error)&&(unlink(UPLOADS_DIR."/archivos/$filename"))){
			$db->commitTrans();
		}else{
			$db->Rollback();
			echo "<script>alert('No se pudo borrar el archivo')</script>";
		}
		$new="";
		$modo="modif";
	}
	if ($modo=="nuevo"){
		$rta=sql("select nextval('auditorias_calidad_id_auditoria_calidad_seq') as id_audit");
		$id_auditoria_calidad=$rta->fields["id_audit"];
		$titulo_tabla="Auditoría nueva nro. ".$id_auditoria_calidad;
		$tipo="interna";
		$planta=$sels["plantas"][0]["key"];
		if ($estado=="a") $titulo_tabla.=" (por autorizar)";
		elseif ($estado=="p") $titulo_tabla.=" (pendiente)";
		else  $titulo_tabla.=" (historial)";
		$modo="modif";
		$new="disabled";
	}elseif ($modo=="modif"){
		$titulo_tabla="Modificación de la auditoría nro. ".$id_auditoria_calidad;
		if ($estado=="a") $titulo_tabla.=" (por autorizar)";
		elseif ($estado=="p") $titulo_tabla.=" (pendiente)";
		else  $titulo_tabla.=" (historial)";
	}else fin_pagina();
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if ($_POST["bguardar"]){
		$temporal=sql("select * from calidad.auditorias_calidad where id_auditoria_calidad=".$id_auditoria_calidad, "c65") or fin_pagina();
		if ($temporal->recordCount()==0){
			$sql="insert into calidad.auditorias_calidad (id_auditoria_calidad, tipo, fecha_desde, fecha_hasta, planta, estado_auditoria, titulo)";
			$sql.="values ($id_auditoria_calidad, '$tipo', ".(($fecha_desde)?"'".$fecha_desde."'":"null").", ".(($fecha_hasta)?"'".$fecha_hasta."'":"null").", '".$planta."', '".$estado."', '$titulo')";
		}else{
			$sql="update calidad.auditorias_calidad set tipo='".$tipo."', fecha_desde=".(($fecha_desde)?"'".$fecha_desde."'":"null").", fecha_hasta=".(($fecha_hasta)?"'".$fecha_hasta."'":"null").", planta='".$planta."', estado_auditoria='".$estado."', titulo='$titulo' ";
			$sql.=" where id_auditoria_calidad=".$id_auditoria_calidad;
		}
		sql($sql, "Error al agregar/actualizar registro") or fin_pagina();
		/////////////////////////// log /////////////////////////////////////////
		sql("insert into calidad.auditorias_calidad_log(id_auditoria_calidad, fecha, nuevo_estado, id_usuario)
			values(".$id_auditoria_calidad.", '".date("Y-m-d H:m")."', '".$estado."', ".$_ses_user["id"].")", "c98")or fin_pagina();
		/////////////////////////////////////////////////////////////////////////
		if ($_POST["afectadosValues"]){
			sql("delete from calidad.departamentos_afectados where id_auditoria_calidad=$id_auditoria_calidad", "c75") or fin_pagina();
			$array=explode(",", $_POST["afectadosValues"]);
  		$tam=count($array);
	  	for($i=0; $i<$tam; $i++){
  		  $query="insert into calidad.departamentos_afectados (id_auditoria_calidad, id_departamento_empresa) values(".$id_auditoria_calidad.",".$array[$i].")";
    		sql($query, "c81*") or fin_pagina();
			}
		}
	}	
	//////////////////////////////////////////////////////////////////////////////////////////
	if ($_POST["cambio_estado"]=="p" or $_POST["cambio_estado"]=="h"){		
		$sql="update calidad.auditorias_calidad set estado_auditoria='$estado' where id_auditoria_calidad= $id_auditoria_calidad ";
		sql($sql, "No se puede modificar el estado") or fin_pagina();
		$link=encode_link('calidad_auditorias.php',array());
    	header("Location:$link") or die("No se encontró la página destino");	
	}
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$sql_archivos="select * from calidad.auditorias_calidad_archivos where id_auditoria_calidad=".$id_auditoria_calidad;
	
	$sql_afectados="select nombre_departamento as label, id_departamento_empresa as key
		from general.departamentos_empresa de 
			join calidad.departamentos_afectados da using (id_departamento_empresa) 
		where da.id_auditoria_calidad=".$id_auditoria_calidad." order by de.nombre_departamento";
	$rta_consulta=sql($sql_afectados, "c39") or fin_pagina();
	$i=0;
	while (!$rta_consulta->EOF){
		$departamentos_afectados[$i++]=$rta_consulta->fetchRow();
	}
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	cargar_calendario();
	echo($html_header);
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
}
</script>
<?
	if($parametros['accion']!=""){ Aviso($parametros['accion']);}
	//////////////////////////////////////////////////////////////////////////////
	?>
	<form name="form1" method="POST" action="calidad_auditorias_detalle.php">
		<input type="hidden" name="modo" value="<?=$modo?>">
		<input type='hidden' name='id_auditoria_calidad' value='<?=$id_auditoria_calidad?>'>
		<input type='hidden' name='afectadosValues' value='<?=$afectadosValues?>'>
		<input type='hidden' name='first_time' value='<?=$first_time?>'>
		<input type='hidden' name='estado' value='<?=$estado?>'>
		<input type='hidden' name='cambio_estado' value='<?=$cambio_estado?>'>

		<table border="1" cellspacing="0" bgcolor="<?=$bgcolor2?>" width="90%" align="center">
			<th align="center" colspan="4" id="mo"><?=$titulo_tabla?></th>
			<tr>
				<td id="mo">
					Título:
				</td>
				<td colspan="3">
					<input type="text" name="t_titulo" id="t_titulo" value="<?=$titulo?>" style="width:'100%'">
				</td>
			</tr>
			<tr>
				<td id="mo">Tipo:</td>
				<td>
					<?=g_draw_mix_select("sel_tipo", $tipo, array("0"=>array("key"=>"interna", "label"=>"interna"), "1"=>array("key"=>"externa", "label"=>"externa")));?>
				</td>
				<td id="mo">Planta:</td>
				<td>
					<?=g_draw_mix_select("sel_planta", $planta, $sels["plantas"]);?>
				</td>
			</tr>
			<tr>
				<td id="mo">Período:</td>
				<td colspan="3" align="left">
					desde <input type="text" name="fecha_desde" id="fecha_desde" value="<?=Fecha($fecha_desde)?>" readonly></input>&nbsp;<?=link_calendario("fecha_desde")?>
					hasta <input type="text" name="fecha_hasta" id="fecha_hasta" value="<?=Fecha($fecha_hasta)?>" readonly></input>&nbsp;<?=link_calendario("fecha_hasta")?>
				</td>
			</tr>
		</table>
		<table border="1" cellspacing="0" bgcolor="<?=$bgcolor3?>" width="90%" align="center">
			<tr><td align="center" colspan="3" id="mo">Departamentos</td></tr>
			<tr id="mo"><td width="45%">Disponibles</td><td>&nbsp;</td><td width="45%">Afectados a la auditoría</td></tr>
			<tr>
				<td>
					<?
					g_draw_mix_select("sel_disponible", $sels["departamentos"][0]["key"], $sels["departamentos"], 10, "multiple style='width:100%'");
					?>
				</td>
				<td align="center">
					<input type="button" name="add" value=">>" onclick="moveOver();"><br>
					<input type="button" name="sub" value="<<" onclick="removeMe();"><br>
				</td>
				<td>
					<?
					g_draw_mix_select("sel_afectado", $departamentos_afectados[0]["key"], $departamentos_afectados, 10, "multiple style='width:100%'");
					?>
				</td>
			</tr>
		</table>
		<table border="0" cellspacing="0" bgcolor="<?=$bgcolor2?>" width="90%" align="center">
			<tr>
				<td align="center">
				
					<input type="button" name="pendiente" value="a Pendiente" onclick="document.all.estado.value='p'; document.all.cambio_estado.value='p'; window.document.form1.submit();">
					<input type="button" name="historial" value="a Historial" onclick="document.all.estado.value='h'; document.all.cambio_estado.value='h'; window.document.form1.submit();">
					<input type="submit" name="bguardar" value="Guardar cambios" onclick="val_text();">

					<input type="button" name="volver" value="Volver" onclick="document.location='calidad_auditorias.php'">
				</td>
			</tr>
		</table>
		<br>
		<table border="1" cellspacing="0" bgcolor="<?=$bgcolor3?>" width="90%" align="center">
			<tr>
				<td align="center" colspan="5">
				<?$rta_archivos=sql($sql_archivos) or fin_pagina();?>
					Archivos (<?=$rta_archivos->recordcount()?> en total)

					<input type="button" name="bagregar" value="Agregar" onclick="if (typeof(warchivos)=='object' && warchivos.closed || warchivos==false) warchivos=window.open('<?= encode_link($html_root.'/modulos/archivos/archivos_subir.php',array("id_auditoria_calidad"=>$id_auditoria_calidad, "user"=>$_ses_user["name"], "onclickaceptar"=>"window.self.focus();", "proc_file"=>"../calidad/calidad_auditorias_archivos.php")) ?>','','toolbar=0,location=0,directories=0,status=0, menubar=0,scrollbars=1'); else warchivos.focus()" <?=$new?>>
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
  	  			<!--<td align=center>&nbsp;<?//=Fecha($rta_archivos->fields["fecha"]) ?></td>-->
				    <td align=center>&nbsp;<?= $rta_archivos->fields["creadopor"] ?></td>
				    <td align=center>&nbsp;<?= $size=number_format($rta_archivos->fields["size"] / 1024); ?> Kb</td>
	    			<td align=center>
		<?    
					$lnk=encode_link("$_SERVER[PHP_SELF]",Array("id_auditoria_calidad"=>$id_auditoria_calidad,"id_archivo"=>$rta_archivos->fields["id"],"filename"=>$rta_archivos->fields["nombre"],"modo"=>"borrar_archivo"));
		      if (permisos_check("inicio", "botones_nuevo_guardar_agregar_auditoria")){
		      	echo "<a href='$lnk'><img src='../../imagenes/close1.gif' border=0 alt='Eliminar el archivo: \"". $rta_archivos->fields["nombre"] ."\"'></a>";
		      }
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
