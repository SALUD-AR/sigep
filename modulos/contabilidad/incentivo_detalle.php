<?

require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

if ($_POST['activar']=="Aceptar orden de Incentivos"){
	
$usuario=$_ses_user['name'];	
$fecha_hoy=date("Y-m-d");
	
$sql_inc_det="select * from contabilidad.incentivo where cuie='$cuie' and fecha_prefactura between '$fecha_desde' and '$fecha_hasta'";
$res_inc_det=sql($sql_inc_det,"No se puede abrir la base de datos") or die;



$total_inc=0;
while (!$res_inc_det->EOF) {
	$id_incentivo=$res_inc_det->fields['id_incentivo'];
	$sql_update="update contabilidad.incentivo set cumple=1,fecha_autorizacion='$fecha_hoy',usuario='$usuario' where id_incentivo=$id_incentivo";
	$res_sql_update=sql($sql_update) or die;
	
//borra los montos de los egresos en la tabla de contabilidad.egreso
//asi mismo quedan indicados en la tabla contabilidad.incentivos
	
	$id_egreso=$res_inc_det->fields['id_egreso'];
	$sql_id_egreso="update contabilidad.egreso set monto_egreso=0,monto_egre_comp=0 where id_egreso='$id_egreso'";
	$res_id_egreso=sql($sql_id_egreso,"no se pudo modificar el registro de egreso") or die;
	
	$total_inc=$total_inc+$res_inc_det->fields['monto_incentivo'];
	$res_inc_det->MoveNext();
	};

//codigo para la insercion de un nuevo egreso en la tabla contabilidad.egreso con las 
//sumas de los incentivos por periodo 

	$id_egreso_nuevo="select nextval('contabilidad.egreso_id_egreso_seq') as id_egreso";
	$res_egreso_nuevos= sql($id_egreso_nuevo) or die;
	$id_egreso_incentivos=$res_egreso_nuevos->fields['id_egreso'];
	
	$comentario="Suma de Incentivo correspondiente en semestre $semestre del $anio";
	
	$update_egreso="insert into contabilidad.egreso (id_egreso,cuie,monto_egreso,fecha_egreso,comentario,usuario,fecha,id_servicio,id_inciso,monto_egre_comp,fecha_egre_comp)
	 values ('$id_egreso_incentivos','$cuie','$total_inc','$fecha_hoy','$comentario','$usuario','$fecha_hoy',1,1,'$total_inc','$fecha_hoy')";
	//echo $update_egreso;
	$res_egreso=sql($update_egreso,"no se pudo insertar el registro de egreso") or die;
	
}


if ($_POST['desactivar']=="Denegar orden de Incentivo"){
	
$usuario=$_ses_user['name'];	
$fecha_hoy=date("Y-m-d");
	
$sql_inc_det="select * from contabilidad.incentivo where cuie='$cuie' and fecha_prefactura between '$fecha_desde' and '$fecha_hasta'";
$res_inc_det=sql($sql_inc_det,"No se puede abrir la base de datos") or die;

while (!$res_inc_det->EOF) {
	$id_incentivo=$res_inc_det->fields['id_incentivo'];
	$sql_update="update contabilidad.incentivo set cumple=0,fecha_autorizacion='$fecha_hoy',usuario='$usuario' where id_incentivo=$id_incentivo";
	$res_sql_update=sql($sql_update) or die;
	
	//borra los montos de los egresos en la tabla de contabilidad.egreso
	//asi mismo quedan indicados en la tabla contabilidad.incentivos
	
	$id_egreso=$res_inc_det->fields['id_egreso'];
	$sql_id_egreso="update contabilidad.egreso set monto_egreso=0,monto_egre_comp=0 where id_egreso='$id_egreso'";
	$res_id_egreso=sql($sql_id_egreso,"no se pudo modificar el registro de egreso") or die;
	
	$res_inc_det->MoveNext();
	};	
	
	
	
}

if ($cuie) {
$query="SELECT * FROM nacer.efe_conv where cuie='$cuie'";

$res_factura=sql($query, "Error al traer el Efector") or fin_pagina();

$cuie=$res_factura->fields['cuie'];
$nombre=$res_factura->fields['nombre'];
$domicilio=$res_factura->fields['domicilio'];
$departamento=$res_factura->fields['dpto_nombre'];
$localidad=$res_factura->fields['localidad'];
$cod_pos=$res_factura->fields['cod_pos'];
$cuidad=$res_factura->fields['cuidad'];
$referente=$res_factura->fields['referente'];
$tel=$res_factura->fields['tel'];

$sql_inc="select count(id_incentivo) as cantidad from contabilidad.incentivo where cuie='$cuie' and fecha_prefactura between '$fecha_desde' and '$fecha_hasta'";
$res_inc=sql($sql_inc,"No se puede abrir la base de datos") or die;
$cantidad=$res_inc->fields['cantidad'];

$sql_inc_total="select sum(monto_incentivo) as total from contabilidad.incentivo where cuie='$cuie' and fecha_prefactura between '$fecha_desde' and '$fecha_hasta'";
$res_inc_total=sql($sql_inc_total,"No se puede abrir la base de datos") or die;
$total=$res_inc_total->fields['total'];

$sql_inc_total="select sum(parcial) as total from contabilidad.incentivo where cuie='$cuie' and fecha_prefactura between '$fecha_desde' and '$fecha_hasta'";
$res_inc_total=sql($sql_inc_total,"No se puede abrir la base de datos") or die;
$total_parcial=$res_inc_total->fields['total'];

$sql_inc_det="select * from contabilidad.incentivo where cuie='$cuie' and fecha_prefactura between '$fecha_desde' and '$fecha_hasta'";
$res_inc_det=sql($sql_inc_det,"No se puede abrir la base de datos") or die;



}
echo $html_header;
?>


<!--<form name='form1' action='incentivo_detalle.php' method='POST'>-->
<form name='form1' action='incentivo_detalle.php' method='POST' enctype='multipart/form-data'>
<input type="hidden" value="<?=$id_efe_conv?>" name="id_efe_conv">
<input type="hidden" value="<?=$cuie?>" name="cuie">
<input type="hidden" value="<?=$fecha_desde?>" name="fecha_desde">
<input type="hidden" value="<?=$fecha_hasta?>" name="fecha_hasta">
<input type="hidden" value="<?=$semestre?>" name="semestre">
<input type="hidden" value="<?=$anio?>" name="anio">
<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		
	    
	  </td>
       
     </tr>
     
</table>
<table width="85%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
    	<font size=+1><b>Efector: <?echo $cuie.". Desde: ".$fecha_desde." Hasta: ".$fecha_hasta?> </b></font>        
    </td>
 </tr>
 <tr><td>
  <table width=90% align="center" class="bordes">
     <tr>
      <td id=mo colspan="5">
       <b> Descripción del Efector</b>
      </td>
     </tr>
     <tr>
       <td>
        <table align="center">
                
         <td align="right">
				<b>Nombre:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$nombre?>" name="nombre" readonly>
            </td>
         </tr>
         
         <tr>	           
           
         <tr>
         <td align="right">
				<b>Domicilio:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$domicilio?>" name="domicilio" readonly>
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Departamento:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$departamento?>" name="departamento" readonly>
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Localidad:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$localidad?>" name="localidad" readonly>
            </td>
         </tr>
        </table>
      </td>      
      <td>
        <table align="center">        
         <tr>
         <td align="right">
				<b>Codigo Postal:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$cod_pos?>" name="cod_pos" readonly>
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Cuidad:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$cuidad?>" name="cuidad" readonly>
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Referente:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$referente?>" name="referente" readonly>
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Telefono:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$tel?>" name="tel" readonly>
            </td>
         </tr>          
        </table>
      </td>  
       
     </tr> 
           
 </table>           

<?if ($cuie){?>
	 
<table width="90%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
		<tr align="center" id="sub_tabla">
		 	<td colspan=6 title="Criterio de filtro: por fecha de ingreso al sistema de expediente">	
		 		Detalle sobre Incentivos <BR>(Criterio de Filtro: Fecha de prefactura)
		 	</td>
		 </tr>
		
		 <tr>
		    <td align="left">
				Cantidad de Incentivos en el semestre: <b><?=$cantidad?></b>
            </td>
            <td align="left">
				Total de Incentivos en el semestre: <b>$<?=number_format($total,2,',','.')?></b>
            </td>
			<td align="left">
				Parcial de Incentivos en el semestre: <b>$<?=number_format($total_parcial,2,',','.')?></b>
            </td>
		 </tr> 
</table>	 
<table width="90%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">		 
		 <tr align="center" id="sub_tabla">
		 	<td colspan=10>	
		 		Detalle sobre Incentivos en el Semestre <BR>(Criterio de Filtro: Fecha Prefactura)
		 	</td>
		 </tr>
		 
		 <tr>
			<td align=right id=mo><a id=mo>Numero de Incentivo</a></td>
			<td align=right id=mo><a id=mo>Numero de Ingreso</a></td> 
			<td align=right id=mo><a id=mo>Numero de Factura</a></td>  
			<td align=right id=mo><a id=mo>Fecha Ingreso(exp)</a></td>       	
			<td align=right id=mo><a id=mo>Monto de Factura</a></td>
			<td align=right id=mo><a id=mo>Monto Incentivo</a></td>
			<td align=right id=mo><a id=mo>Observaciones</a></td>
			<td align=right id=mo><a id=mo>Fecha Autorizacion</a></td>
			<td align=right id=mo><a id=mo>Usuario</a></td>
			</tr>
 <?
  if  ($res_inc_det) {
  $cumple=$res_inc_det->fields['cumple'];
  	while (!$res_inc_det->EOF) {
  	?>
    <? if ($res_inc_det->fields['cumple']==0) $tr=atrib_tr2();
       if ($res_inc_det->fields['cumple']==1) $tr=atrib_tr3();
       if ($res_inc_det->fields['cumple']==2) $tr=atrib_tr1();
	   if ($res_inc_det->fields['cumple']==3) $tr=atrib_tr8()?>
    
    <tr <?=$tr?>> 
       
     <td align=center><?=$res_inc_det->fields['id_incentivo'];?></td>
     <td align=center><?=$res_inc_det->fields['id_ingreso'];?></td>
     <td align=center><?=$res_inc_det->fields['id_factura'];?></td>
     <td align=left><?=fecha($res_inc_det->fields['fecha_prefactura']);?></td>    
     <td align=left><?=number_format($res_inc_det->fields['monto_factura'],2,',','.');?></td>
     <td align=left><?=number_format($res_inc_det->fields['monto_incentivo'],2,',','.');?></td>
      <td align=left><?=($res_inc_det->fields['cumple']==0)?"Incentivo Rechazados":($res_inc_det->fields['cumple']==1)?"Incentivos Aceptados":($res_inc_det->fields['cumple']==3)?"Incentivos Aceptados Parcialmente":"Incentivos Pendientes"?></td>
      <td align=left><?=fecha($res_inc_det->fields['fecha_autorizacion']);?></td>
     <td align=left><?=$res_inc_det->fields['usuario'];?></td>
     
	<?$res_inc_det->MoveNext();
    }    
  }?>   
<?}?>
</table>
<tr><td><table width=90% align="center" class="bordes">
  <tr align="center">
   <td>
     <input type='submit' name="activar" value="Aceptar orden de Incentivos" <? if ($cumple!=2) echo "disabled"?> onclick="return confirm('Se van a Autorizar los pagos de Incentivos del CAPS. ¿Esta Seguro?');" style="width=200px">  
   </td>
	<td>
    <? $ref = encode_link("detalle_pago_parcial.php",array("cuie"=>$cuie,"fecha_desde"=>$fecha_desde,"fecha_hasta"=>$fecha_hasta));
					//$onclick_elegir="location.href='$ref' target='_blank'";
					$onclick_elegir="window.open('$ref' , '_blank');";
					?>
	 <input type='submit' name="activar_parcial" value="Aceptar Parcialmente Incentivos" <? if ($cumple!=2) echo "disabled"?> onclick="<?=$onclick_elegir?>" style="width=200px">  
   </td>
    <td>
     <input type='submit' name="desactivar" value="Denegar orden de Incentivo" <? if ($cumple!=2) echo "disabled"?> onclick="return confirm('Se van a Rechazar los pagos de Incentivos del CAPS. ¿Esta Seguro?');" style="width=200px">     
   </td>
  </tr>
 </table></td></tr>

 <tr><td><table width=90% align="center" class="bordes">
  <tr align="center">
   <td>
     <input type=button name="volver" value="Volver" onclick="document.location='incentivo_listado.php'"title="Volver al Listado" style="width=150px">     
   </td>
  </tr>
 </table></td></tr>
 
  
 <table align='center' border=1 bordercolor='#000000' bgcolor='#FFFFFF' width='80%' cellspacing=0 cellpadding=0>
     <tr>
      <td colspan=10 bordercolor='#FFFFFF'><b>Efectos sobre los incentivos:</b></td>
     <tr>
     <td width=30% bordercolor='#FFFFFF'>
      <table border=1 bordercolor='#FFFFFF' cellspacing=0 cellpadding=0 width=100%>
              <tr>
       	<td>
       	 &nbsp;
       	</td>
       </tr>
       <tr>        
        <td width=30 bgcolor='D7D5FA' bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Incentivos Aceptados ------- Modifica los Egreso y el Saldo (por modificarce el Saldo del incentivo) y Saldo Real No se Modifica</td>
       </tr> 
       <tr>
       	<td>
       	 &nbsp;
       	</td>
       </tr>
       <tr>        
        <td width=30 bgcolor='#00FF00' bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Incentivos Aceptados Parcialmente ------- Modifica el Saldo Real (por modificarce el Saldo Comprom.del incentivo)</td>
       </tr> 
		 <tr>
       	<td>
       	 &nbsp;
       	</td>
       </tr>
       <tr>        
        <td width=30 bgcolor='#F78181' bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Incentivos Rechazados ------- Modifica el Saldo Real (por modificarce el Saldo Comprom.del incentivo)</td>
       </tr>
      </table>
     </td>
    </table>
 
 
 
 </table>
 </form>
 
 <?=fin_pagina();// aca termino ?>
