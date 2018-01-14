<?php

require_once("../../config.php");

$id_seguimiento_remediar=$parametros['id_seguimiento_remediar'] or $id_seguimiento_remediar=$_POST['id_seguimiento_remediar'];

if($_POST["aceptar"]=="Aceptar"){
   $fecha_comprobante=Fecha_db($_POST['fecha_comprobante']); 
   $fecha_comprobante_proximo=Fecha_db($_POST['fecha_comprobante_proximo']); 
   
   $db->StartTrans();
   $query="update trazadoras.seguimiento_remediar
   			set 
   				fecha_comprobante='$fecha_comprobante',
   				fecha_comprobante_proximo='$fecha_comprobante_proximo'
   			where id_seguimiento_remediar=$id_seguimiento_remediar";
   sql($query, "Error al vincular comprobante") or fin_pagina();      
   $db->CompleteTrans();
   
   echo "<script>   			
   			windows.close();
   		</script>";    	
 }
echo $html_header;
cargar_calendario();
?>
<script>
//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos()
{
 if (confirm('Esta Seguro que Desea Modificar Fechas?'))return true;
 else return false;	
}
</script>
<form name=form1 action="modifica_seguimiento.php" method=POST>
<input type="hidden" name="id_seguimiento_remediar" value="<?=$id_seguimiento_remediar?>">

<table cellspacing=2 cellpadding=2 width=40% align=center class=bordes>
<br>
    <tr id="mo">
    	<td align="center" colspan="2">
    		<b>Editar Fechas</b>
    	</td>    	
    </tr>
     <?	$query=" select * from trazadoras.seguimiento_remediar
			  where id_seguimiento_remediar=$id_seguimiento_remediar";
		$result=sql($query,"error query");
	$fecha_comprobante=$result->fields['fecha_comprobante'];
	$fecha_comprobante_proximo=$result->fields['fecha_comprobante_proximo'];
	?>
		
	<tr>
		<td align="right" class=bordes><b>Fecha del Seguimiento:</b></td>
		<td align="left" class=bordes>
			<input type=text id=fecha_comprobante name=fecha_comprobante value='<?=fecha($fecha_comprobante);?>' size=15 readonly>
        	 <?=link_calendario("fecha_comprobante");?>					    	 
		</td>		    
	</tr>
	<tr>
		<td align="right" class=bordes><b>Fecha del Proximo Seguimiento:</b></td>
		<td align="left" class=bordes>
			<input type=text id=fecha_comprobante_proximo name=fecha_comprobante_proximo value='<?=fecha($fecha_comprobante_proximo);?>' size=15 readonly>
        	 <?=link_calendario("fecha_comprobante_proximo");?>					    	 
		</td>		    
	</tr>
    <tr>
    	<td align="center" colspan="2" class=bordes id="mo">
    		<input type="submit" name="aceptar" value="Aceptar" onclick="return control_nuevos()">
    		&nbsp;&nbsp;&nbsp;
    		<input type="button" name="cerrar" value="Cerar" onclick="window.close()" >
    	</td>    	
    </tr>    
</table>
<br>
<br>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>