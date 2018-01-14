<?php

include("../../config.php");

echo $html_header;
$id_log=$parametros['id_log'] or $id_log=$_POST['id_log'];
if ($_POST['guardar']=='Guardar'){
	$fecha=Fecha_db($_POST['fecha']);
	$usuario=$_POST['usuario'];
	$sql="update calidad.log_pac_pap set fecha='$fecha', usuario='$usuario' where id_log_pac_pap=$id_log";
	sql($sql,'no se puede actualizar');?>
	<script>
		window.opener.location.reload();
		window.close();
	</script>
<?}?>


<form name="form1" method="post" action="pac_pap_actualiza_log.php">
<input type="hidden" value="<?=$id_log?>" name="id_log">
<br>
<table width="60%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes"> 
<tr id="mo">
    <td colspan="2">
    	Cambiar Datos de los Logs
    </td>
</tr>
 <tr>
 	<td width="20%" align="right">
 		<font size="2"><b>USUARIO</b></font>
 	</td>
	<td width="80%" align="left">
		&nbsp;
      <?
       $sql="select nombre, apellido from sistema.usuarios where visible=1 order by nombre";
	   $result=sql($sql,'no se puede traer los nombre y apellidos');
       ?>
       
       <select name="usuario"style="width:85%">
        <?
       $result->MoveFirst();
       for($i=0;!$result->EOF;$i++)
       {
        $string=$result->fields['nombre'].' '.$result->fields['apellido'];
        echo "<option value='$string'>$string</option>";
        $result->MoveNext();
       }
       ?>
      </select>
     </td>
  </tr>
  <tr>
  	<td align="right">
 		<font size="2"><b>FECHA</b></font>
 	</td>
  	<td align="left">
  	&nbsp;
  		<?cargar_calendario();?>
    	<input type=text name='fecha' value="<?=date("d/m/Y")?>" size=10 readonly>
		<?=link_calendario("fecha");?>
  	</td>
  </tr>
  <tr>
  	<td colspan="2" align="center" class="bordes">
  		<input type="submit" value="Guardar" name="guardar">
  	</td>
  </tr>
</table>
</form>
</body>
</html>
<?fin_pagina();?>