<?php

require_once("../../config.php");

cargar_calendario();

$id_egreso=$parametros['id_egreso'] or $id_ingreso=$_POST['id_egreso'];
$cuie=$parametros['cuie'] or $cuie=$_POST['cuie'];
$monto_egreso=$parametros['monto_egreso'];
$monto_egre_comp=$parametros['monto_egreso_comp'];

if($_POST["aceptar"]=="Aceptar"){
   $id_egreso=$_POST["id_egreso"];    
   $monto_egreso=$_POST['monto_egreso'];
   $monto_egre_comp=$_POST['monto_egre_comp'];
	$fecha_egreso=Fecha_db($_POST['fecha_egreso']);
	$fecha_egre_comp=Fecha_db($_POST['fecha_egre_comp']);
	$fecha_deposito=Fecha_db($_POST['fecha_deposito']);
   $usuario_mod=$_ses_user['name'];	
   $fecha_mod=date("Y-m-d");
   $db->StartTrans();
   
   $query="update contabilidad.egreso 
   			set    				
   				monto_egreso='$monto_egreso',
   				monto_egre_comp='$monto_egre_comp',
   				fecha_egreso='$fecha_egreso',
   				fecha_egre_comp='$fecha_egre_comp',
   				fecha_deposito='$fecha_deposito',
   				usuario_mod='$usuario_mod',
   				fecha_mod='$fecha_mod'
   			where id_egreso=$id_egreso";
   sql($query, "Error al vincular comprobante") or fin_pagina();
      
   $db->CompleteTrans();
   
   $ref = encode_link("ingre_egre_admin.php",array("cuie"=>$cuie));                      
   echo "<script>   			
   			location.href='$ref';
   		</script>";    	
 }

$sql = "select * from contabilidad.egreso where id_egreso='$id_egreso'";
$result=sql($sql,"no se puede ejecutar");
 echo $html_header;
?>
<script>
//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos()
{
 if(document.all.monto_egre_comp.value==""){
  alert('Debe Ingresar un monto COMPROMETIDO (0 si no hay monto)');
  return false;
 }
 
 if(document.all.monto_egreso.value==""){
  alert('Debe Ingresar un monto EGRESO (0 si no hay monto)');
  return false;
 }
 
 if(document.all.fecha_egre_comp.value==document.all.fecha_egreso.value){
  alert('No puede Ser Igual la Fecha de Egreso Comprometido a la Fecha de Egreso VERIFICAR!!');
  return false;
 }
 
  if(document.all.fecha_egre_comp.value==document.all.fecha_deposito.value){
  alert('No puede Ser Igual la Fecha de Egreso Comprometido a la Fecha de Deposito VERIFICAR!!');
  return false;
 }
 
 if(document.all.fecha_egreso.value!=document.all.fecha_deposito.value){
  alert('La Fecha de Egreso Debe ser IGUAL a la Fecha de Deposito VERIFICAR!!');
  return false;
 }
 
 
 if (confirm('Esta Seguro que Desea Agregar Deposito?'))return true;
 else return false;	
 

}
</script>
<form name=form1 action="modifica_egreso.php" method=POST>
<input type="hidden" name="id_egreso" value="<?=$id_egreso?>">
<input type="hidden" name="cuie" value="<?=$cuie?>">

<table cellspacing=2 cellpadding=2 width=40% align=center class=bordes>
<br>
    <tr id="mo">
    	<td align="center" colspan="2" class=bordes>
    		<b><font size="+1">Modifica Egreso</font></b>
    	</td>    	
    </tr>
    
    <tr>
		<td align="right" class=bordes><b>Fecha Egreso Comprometido:</b></td>
		<td align="left" class=bordes>		          			
		<?php $fecha_egre_comp=fecha($result->fields['fecha_egre_comp'])?>
			<input type=text id=fecha_egre_comp name=fecha_egre_comp value='<?=$fecha_egre_comp;?>' size=15 readonly>
		<?=link_calendario("fecha_egre_comp");?>
		</td>
	</tr>
    
    <tr>
		<td align="right" class=bordes><b>Monto Egreso Comprometido:</b></td>
		<td align="left" class=bordes>		          			
			<b><font color="Maroon" size="+1"><?=number_format($monto_egre_comp,2,',','.')?></font></b>
		</td>
	</tr>
    <tr>
		<td align="right" class=bordes><b>Modificar Egreso Comprometido:</b></td>
		<td align="left" class=bordes>		          			
			<input type="text" name="monto_egre_comp" value="<?=$monto_egre_comp?>" size=20 align="right">
		</td>
	</tr>
	
	<tr>
		<td align="right" class=bordes><b>Fecha Egreso:</b></td>
		<td align="left" class=bordes>		          			
		<?php $fecha_egreso=fecha($result->fields['fecha_egreso'])?>
			<input type=text id=fecha_egreso name=fecha_egreso value='<?=$fecha_egreso;?>' size=15 readonly>
		<?=link_calendario("fecha_egreso");?>
		</td>
	</tr>
	
	<tr>
		<td align="right" class=bordes><b>Monto Egreso:</b></td>
		<td align="left" class=bordes>		          			
			<b><font color="Maroon" size="+1"><?=number_format($monto_egreso,2,',','.')?></font></b>
		</td>
	</tr>
    <tr>
		<td align="right" class=bordes><b>Modificar Egreso:</b></td>
		<td align="left" class=bordes>		          			
			<input type="text" name="monto_egreso" value="<?=$monto_egreso?>" size=20 align="right">
		</td>
	</tr>
	
	<tr>
		<td align="right" class=bordes><b>Fecha Deposito:</b></td>
		<td align="left" class=bordes>		          			
		<?php ($result->fields['fecha_deposito']=='')?$fecha_deposito=$fecha_egreso:$fecha_deposito=fecha($result->fields['fecha_deposito'])?>
			<input type=text id=fecha_deposito name=fecha_deposito value='<?=$fecha_deposito;?>' size=15 readonly>
		<?=link_calendario("fecha_deposito");?>
		</td>
	</tr>
	
    <tr>
    	<td align="center" colspan="2" class=bordes id="mo">
    		<input type="submit" name="aceptar" value="Aceptar" onclick="return control_nuevos()" Style="width=200px">
    		&nbsp;&nbsp;&nbsp;
    		<input type="button" name="cerrar" value="Volver" onclick="document.location='ing_egre_listado.php'" Style="width=200px">
    	</td>    	
    </tr>    
</table>
<br>
<br>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>