<?php

require_once("../../config.php");


$cuie=$parametros['cuie'] or $cuie=$_POST['cuie'];
$fecha_desde=$parametros['fecha_desde'] or $fecha_desde=$_POST['fecha_desde'];
$fecha_hasta=$parametros['fecha_hasta'] or $fecha_hasta=$_POST['fecha_hasta'];


$sql_inc_total="select sum(monto_incentivo) as total from contabilidad.incentivo where cuie='$cuie' and fecha_prefactura between '$fecha_desde' and '$fecha_hasta'";
$res_inc_total=sql($sql_inc_total,"No se puede abrir la base de datos") or die;
$total=$res_inc_total->fields['total'];

if($_POST["aceptar"]=="Aceptar"){
	$usuario=$_ses_user['name'];	
	$fecha_hoy=date("Y-m-d");
	$porcentaje=$_POST['porcentaje'];
	
	$sql_inc_det="select * from contabilidad.incentivo where cuie='$cuie' and fecha_prefactura between '$fecha_desde' and '$fecha_hasta'";
	$res_inc_det=sql($sql_inc_det,"No se puede abrir la base de datos") or die;
	$db->StartTrans();
   
   $total_parcial=0;
   while (!$res_inc_det->EOF) {
	$id_incentivo=$res_inc_det->fields['id_incentivo'];
	$valor=$res_inc_det->fields['monto_incentivo'];
	$parcial=$valor*($porcentaje/100);
	$sql_update="update contabilidad.incentivo set cumple=3,porcentaje=$porcentaje,parcial=$parcial,fecha_autorizacion='$fecha_hoy',usuario='$usuario' where id_incentivo=$id_incentivo";
	$res_sql_update=sql($sql_update) or die;
	
	//borra los montos de los egresos en la tabla de contabilidad.egreso
	//asi mismo quedan indicados en la tabla contabilidad.incentivos
	
	$id_egreso=$res_inc_det->fields['id_egreso'];
	$sql_id_egreso="update contabilidad.egreso set monto_egreso=0,monto_egre_comp=0 where id_egreso='$id_egreso'";
	$res_id_egreso=sql($sql_id_egreso,"no se pudo modificar el registro de egreso") or die;
	
	$total_parcial=$total_parcial+$parcial;
	
	$res_inc_det->MoveNext();
	};	
      
	//codigo para la insercion de un nuevo egreso en la tabla contabilidad.egreso con las 
	//sumas de los incentivos por periodo 

	$id_egreso_nuevo="select nextval('contabilidad.egreso_id_egreso_seq') as id_egreso";
	$res_egreso_nuevos= sql($id_egreso_nuevo) or die;
	$id_egreso_incentivos=$res_egreso_nuevos->fields['id_egreso'];
	
	$comentario="Suma de Incentivo correspondiente en semestre $semestre del $anio";
	
	$update_egreso="insert into contabilidad.egreso (id_egreso,cuie,monto_egreso,fecha_egreso,comentario,usuario,fecha,id_servicio,id_inciso,monto_egre_comp,fecha_egre_comp)
	 values ('$id_egreso_incentivos','$cuie','$total_parcial','$fecha_hoy','$comentario','$usuario','$fecha_hoy',1,1,'$total_parcial','$fecha_hoy')";
	//echo $update_egreso;
	$res_egreso=sql($update_egreso,"no se pudo insertar el registro de egreso") or die;
   
   
   $db->CompleteTrans();
   
  
   //$ref = encode_link("incentivo_detalle.php",array("cuie"=>$cuie,"fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));                      
   //echo "<script>location.href='$ref';</script>"; 
echo "<script> window.close('$ref' , '_blank');</script>";  
 
 }

echo $html_header;
?>
<script>

function control_nuevos()
{
 if(document.all.porcentaje.value==""){
  alert('Debe Ingresar un valor para el porcentaje');
  return false;
 }
 
  if (confirm('Esta Seguro que Desea Modificar el monto Parcial del Incentivo?'))return true;
 else return false;	
 

}

function  calculo_pago_parcial() 
{
var total= "<? echo $total; ?>"
var porcentaje = document.all.porcentaje.value;

document.all.pago_parcial.readonly = true;
document.all.pago_parcial.value=total*(document.all.porcentaje.value/100);
document.all.pago_parcial.readonly = false;


}
</script>
<!--<form name=form1 action="detalle_pago_parcial.php" method=POST>-->
<form name='form1' action='detalle_pago_parcial.php' method='POST' enctype='multipart/form-data'>
<input type="hidden" name="cuie" value="<?=$cuie?>">
<input type="hidden" name="fecha_desde" value="<?=$fecha_desde?>">
<input type="hidden" name="fecha_hasta" value="<?=$fecha_hasta?>">

<table cellspacing=2 cellpadding=2 width=40% align=center class=bordes>
<br>
    <tr id="mo">
    	<td align="center" colspan="2" class=bordes>
    		<b><font size="+1">Modifica Incentivo Pago Parcial</font></b>
    	</td>    	
    </tr>
    
     <tr>
		<td align="right" class=bordes><b>Monto Incentivo Actual:</b></td>
		<td align="left" class=bordes>		          			
			<b><font color="Maroon" size="+1"><?=number_format($total,2,',','.')?></font></b>
		</td>
	</tr>
    <tr>
		<td align="right" class=bordes><b>Porcentaje para Pago Parcial:</b></td>
		<td align="left" class=bordes>		          			
			<input type="text" name="porcentaje" value="<?=$porcentaje?>" OnBlur = "calculo_pago_parcial()" size=20 align="right">
		</td>
	</tr>
	
	<tr>
		<td align="right" class=bordes><b>Monto Parcial del Incentivo:</b></td>
		<td align="left" class=bordes>		          			
			<input type="text" name="pago_parcial" value='' size=20 align="right" readonly>
		</td>
	</tr>
    
	<tr>
    	<td align="center" colspan="2" class=bordes id="mo">
    		<input type="submit" name="aceptar" value="Aceptar" onclick="return control_nuevos()" Style="width=200px">
    		
    		<input type="button" name="cerrar" value="Cerrar" onclick="window.close('$ref' , '_blank');" Style="width=200px">
    	</td>    	
    </tr>    
</table>
<br>
<br>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>