<?php
require_once("../../config.php");
echo $html_header;
cargar_calendario();

if ($_POST['guardar_queja']=='Guardar Queja'){

$fech=date("Y-m-d H:i:s",mktime()); //fecha actual
$query_insert="insert into Quejas (fecha,nbre_cl,mail,descripcion,tipo_queja) values ('".fecha_db($_POST['fecha_queja'])."','".$_POST['nombre_cl']."','".$_POST['mail']."','".$_POST['descripcion']."','".$_POST['tipo']."') ";
sql($query_insert) or fin_pagina();
$query_max="select id_queja from Quejas where fecha='".fecha_db($_POST['fecha_queja'])."' and nbre_cl='".$_POST['nombre_cl']."' and mail='".$_POST['mail']."' and descripcion='".$_POST['descripcion']."' and tipo_queja='".$_POST['tipo']."'";
$res=sql($query_max) or fin_pagina();
$id_queja=$res->fields['id_queja'];
$query_insert="insert into log_quejas (usuario,fecha_log,id_queja,tipo) values ('".$_ses_user['name']."','$fech',$id_queja,'insert')";
sql ($query_insert) or fin_pagina();
}

?>
<form name="form_queja" action="calidad_quejas.php" method="post">
<!--<div align="right">
  <img src='<?php// echo "$html_root/imagenes/ayuda.gif" ?>' border="0" alt="ayuda" onClick="abrir_ventana('<?php// echo "$html_root/modulos/ayuda/remitos/rem_listar.htm" ?>', 'LISTAR REMITOS')" >
</div> -->
<br><br><br>
<? 
if ($parametros) {
$query="select * from Quejas join log_quejas using(id_queja) where Quejas.id_queja=".$parametros['id_queja'];
$result=sql($query) or fin_pagina();
}
?>
<table align="center" border="1">
 <tr>
   <td colspan="2" align="center" id="mo">QUEJAS</td>
 </tr>
 <tr>
   <td colspan="2" align="right">Fecha: <input name="fecha_queja" type="text" value="<?=Fecha($result->fields['fecha']) ?>">&nbsp;&nbsp;<?php echo link_calendario("fecha_queja"); ?></td>
 </tr>
 <tr>
   <td>Nombre Cliente:&nbsp;&nbsp;<input name="nombre_cl" type="text" size="50" value=<?=$result->fields['nbre_cl'] ?>></td>
 <tr>  
   <td>E-mail: &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;<input name="mail" type="text" size="30" value="<?=$result->fields['mail'] ?>"></td>
 </tr>
</tr>
<tr><td colspan="2">Descripción:<br>&nbsp;&nbsp;<textarea name="descripcion" cols="60" rows="4"><?=$result->fields['descripcion'] ?></textarea></td></tr>
<tr><td colspan="2">Tipo: &nbsp;&nbsp;<select name="tipo">
	   <option value='Queja' <? if ($result->fields['tipo_queja'] == 'Queja') echo 'selected';?>>Queja</option>
	   <option value='Consulta' <? if ($result->fields['tipo_queja'] == 'Consulta') echo 'selected';?> >Consulta</option>
	  </select>
	
	 </td>
	 </tr>
</table>
<br>
<div align="center">
<? if ($parametros) {?>
<input name="Volver" type="button" value="Volver" Onclick="location.href='listar_quejas.php'">
<? } else {?>
<input name="guardar_queja" type="submit" value="Guardar Queja">
<? } ?>
</div>
</form>