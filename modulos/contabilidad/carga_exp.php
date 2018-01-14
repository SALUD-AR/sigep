<?php

require_once("../../config.php");

cargar_calendario();

$id_factura=$parametros['id_factura'] or $id_factura=$_POST['id_factura'];
$nro_exp_ext=$parametros['nro_exp_ext'] or $nro_exp_ext=$_POST['nro_exp_ext'];
$fecha_exp_ext=$parametros['fecha_exp_ext'] or $fecha_exp_ext=$_POST['fecha_exp_ext'];
$periodo_contable=$parametros['periodo_contable'] or $periodo_contable=$_POST['periodo_contable'];

if($_POST["aceptar"]=="Aceptar"){
     
   $fecha_exp_ext=Fecha_db($fecha_exp_ext);     
   $db->StartTrans();
   
   $query="update facturacion.factura 
   			set 
   				nro_exp_ext='$nro_exp_ext',
   				fecha_exp_ext='$fecha_exp_ext',
   				periodo_contable='$periodo_contable'
   			where id_factura='$id_factura'";
   sql($query, "Error al vincular comprobante") or fin_pagina();
      
   $db->CompleteTrans();
           
   echo "<script>   			
   			window.opener.location.reload();
			window.close();
   		</script>";    	
 }

echo $html_header;
?>
<script>
//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos()
{
 if(document.all.nro_exp_ext.value==""){
  alert('Debe Ingresar un expediente');
  return false;
 }
 if(document.all.fecha_exp_ext.value==""){
  alert('Debe Ingresar una Fecha Expediente');
  return false;
 }
 return true;
}
</script>
<form name=form1 action="carga_exp.php" method=POST>
<input type="hidden" name="id_factura" value="<?=$id_factura?>">

<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
    <tr>
    	<td align="center" colspan="2">
    		<b><font color="Blue" size="+2">Carga Datos de Expediente Externo</font></b>
    	</td>    	
    </tr>
    
    <tr>
    	<td align="center" colspan="2">
    		&nbsp;
    	</td>    	
    </tr>
    
    <tr>
		<td align="right"><font size="+1"><b>Exp. Externo:</b></font></td>
		<td align="left">		          			
			<input type="text" name="nro_exp_ext" value="<?=$nro_exp_ext?>" size=20 align="right">
			<font color="Red">Ej: 2010-000003 , 2009-012345</font>
		</td>
	</tr>	
	<tr>
		<td align="right"><font size="+1"><b>Fecha de Expediente:</b></font></td>
		<td align="left">
			<input type=text id=fecha_exp_ext name=fecha_exp_ext value='<?=fecha($fecha_exp_ext);?>' size=15 readonly>
        	 <?=link_calendario("fecha_exp_ext");?>					    	 
		</td>		    
	</tr>
	
	<tr>
         	<td align="right">
				<font size="+1"><b>Periodo de Prestacion:</b></font>
			</td>
			<td align="left">
			 <select name=periodo_contable Style="width=110px">
			 <option value=-1>Seleccione</option>
			  <?
			  $sql = "select * from facturacion.periodo order by periodo";
			  $result=sql($sql,"No se puede traer el periodo");			  
			  while (!$result->EOF) {
			  	$periodo=$result->fields['periodo'];?>			  			  
			  <option value=<?=$result->fields['periodo']?> <?if ($periodo==$periodo_contable)echo "selected"?>><?=$result->fields['periodo']?></option>
			  <?
			  $result->movenext();
			  }
			  ?>			  
			  </select>
			</td>
         </tr>
         
    <tr>
    	<td align="center" colspan="2">
    		<input type="submit" name="aceptar" value="Aceptar" onclick="return control_nuevos()">
    		&nbsp;&nbsp;&nbsp;
    		<input type="button" name="cerrar" value="Cerrar" onclick="window.close();" >
    	</td>    	
    </tr>  
    
    
           
</table>
<br>
<br>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>