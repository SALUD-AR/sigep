<?php

require_once("../../config.php");

cargar_calendario();

$id_ingreso=$parametros['id_ingreso'] or $id_ingreso=$_POST['id_ingreso'];
$cuie=$parametros['cuie'] or $cuie=$_POST['cuie'];
$numero_factura=$parametros['numero_factura'];

if($_POST["aceptar"]=="Aceptar"){
   $id_ingreso=$_POST["id_ingreso"]; 
   $fecha_deposito=Fecha_db($_POST['fecha_deposito']); 
   $fecha_notificacion=Fecha_db($_POST['fecha_notificacion']); 
   $monto_deposito=$_POST['monto_deposito'];
    
   $db->StartTrans();
   
   $query="update contabilidad.ingreso 
   			set 
   				fecha_deposito='$fecha_deposito',
   				fecha_notificacion='$fecha_notificacion',
   				monto_deposito='$monto_deposito'
   			where id_ingreso=$id_ingreso";
   sql($query, "Error al vincular comprobante") or fin_pagina();
      
   $db->CompleteTrans();
   
   $ref = encode_link("ingre_egre_admin.php",array("cuie"=>$cuie));                      
   echo "<script>   			
   			location.href='$ref';
   		</script>";    	
 }

echo $html_header;
?>
<script>
//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos()
{
 if(document.all.monto_deposito.value==""){
  alert('Debe Ingresar un monto');
  return false;
 }
 
 if (confirm('Esta Seguro que Desea Agregar Deposito?'))return true;
 else return false;	
}
</script>
<form name=form1 action="carga_deposito.php" method=POST>
<input type="hidden" name="id_ingreso" value="<?=$id_ingreso?>">
<input type="hidden" name="cuie" value="<?=$cuie?>">

<table cellspacing=2 cellpadding=2 width=40% align=center class=bordes>
<br>
    <tr id="mo">
    	<td align="center" colspan="2">
    		<b>Carga de Deposito</b>
    	</td>    	
    </tr>
    <tr>
         <?	$query_t="SELECT sum 
			(facturacion.prestacion.cantidad*facturacion.prestacion.precio_prestacion) as total
			FROM
			  facturacion.factura
			  INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
			  INNER JOIN facturacion.prestacion ON (facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante)
			  where factura.id_factura=$numero_factura";
		$total=sql($query_t,"NO puedo calcular el total");
		$query_t1="SELECT sum 
			(nomenclador.prestaciones_n_op.precio) as total1
			FROM
			  facturacion.factura
			  INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
			  INNER JOIN nomenclador.prestaciones_n_op using (id_comprobante)
			  where factura.id_factura=$numero_factura";
		$total1=sql($query_t1,"NO puedo calcular el total");
		$total=$total->fields['total']+$total1->fields['total1'];;?>
		<td align="right" class=bordes><b>Monto de la Factura:</b></td>
		<td align="left" class=bordes>		          			
			<b><font color="Maroon" size="+1"><?=number_format($total,2,',','.')?></font></b>
		</td>
	</tr>
    <tr>
		<td align="right" class=bordes><b>Monto del Deposito:</b></td>
		<td align="left" class=bordes>		          			
			<input type="text" name="monto_deposito" value="<?=$total?>" size=20 align="right">
		</td>
	</tr>
	<tr>
		<td align="right" class=bordes><b>Fecha del Deposito:</b></td>
		<td align="left" class=bordes>
			<?$fecha_deposito=date("d/m/Y");?>
			<input type=text id=fecha_deposito name=fecha_deposito value='<?=$fecha_deposito;?>' size=15 readonly>
        	 <?=link_calendario("fecha_deposito");?>					    	 
		</td>		    
	</tr>
	<tr>
		<td align="right" class=bordes><b>Fecha de Notificacion!!:</b></td>
		<td align="left" class=bordes>
			<?$fecha_notificacion=date("d/m/Y");?>
			<input type=text id=fecha_notificacion name=fecha_notificacion value='<?=$fecha_notificacion;?>' size=15 readonly>
        	 <?=link_calendario("fecha_notificacion");?>					    	 
		</td>		    
	</tr>
    <tr>
    	<td align="center" colspan="2" class=bordes id="mo">
    		<input type="submit" name="aceptar" value="Aceptar" onclick="return control_nuevos()">
    		&nbsp;&nbsp;&nbsp;
    		<input type="button" name="cerrar" value="Volver" onclick="document.location='ing_egre_listado.php'" >
    	</td>    	
    </tr>    
</table>
<br>
<br>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>