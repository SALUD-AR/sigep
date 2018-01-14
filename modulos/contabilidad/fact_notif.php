<?
/*
Author: seba
Date: 2011/10/19 16:24:00 $
*/
require_once ("../../config.php");


extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

if ($_POST['guardar']=="Guardar Notificacion"){
	
	  $fecha_hoy=date("Y-m-d");
	  $usuario=$_ses_user['name'];
	  $via_com="Telefonica";
	  $str_null="";
	
   	  $q="select nextval('facturacion.id_notif_seq') as id_notif";
      $id_notif=sql($q) or fin_pagina();
      $id_notificacion=$id_notif->fields ['id_notif'];
      $str_sql="INSERT into facturacion.notificacion (id_notif,id_factura,usuario,fecha_notif,
mail_efe,via_comunicacion,comentario) values ('$id_notificacion',
												   '$id_factura',
												   '$usuario',
												   '$fecha_hoy',
												   '$str_null',
												   '$via_com',
												   '$observaciones')";
$intro_reg=sql($str_sql,"Error al insertar el Expediente") or fin_pagina();

$accion="SE GUARDO LA NOTIFICACION DE LA COMUNICACION TELEFONICO";
	
}//de if ($_POST['guardar']=="Guardar nuevo Muleto")

if ($id_factura) {
$query="SELECT 
  *
FROM
  facturacion.factura
  where id_factura=$id_factura";
$res_factura=sql($query, "Error al traer el Comprobantes") or fin_pagina();

$cuie=$res_factura->fields['cuie'];
$fecha_factura=$res_factura->fields['fecha_factura'];
$periodo=$res_factura->fields['periodo'];
$periodo_actual=$res_factura->fields['periodo_actual'];
//$observaciones=$res_factura->fields['observaciones'];
$estado=$res_factura->fields['estado'];
$mes_fact_d_c=$res_factura->fields['mes_fact_d_c'];
$monto_prefactura=$res_factura->fields['monto_prefactura'];
$fecha_control=$res_factura->fields['fecha_control'];
$nro_exp=$res_factura->fields['nro_exp'];
$traba=$res_factura->fields['traba'];
}

echo $html_header;
?>
<script>
//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos()
{
 if(document.all.cuie.value=="-1"){
  alert('Debe Seleccionar un efector');
  return false;
 }
 if(document.all.periodo.value=="-1"){
  alert('Debe Seleccionar un Periodo');
  return false;
 } 
 if(document.all.periodo_actual.value=="-1"){
	  alert('Debe Seleccionar un Periodo Actual');
	  return false;
	 } 
 if(document.all.fecha_factura.value==""){
  alert('Debe Ingresar una fecha de factura');
  return false;
 } 
 
}//de function control_nuevos()

</script>

<form name='form1' action='fact_notif.php' method='POST'>
<input type="hidden" value="<?=$id_factura?>" name="id_factura">
<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>

<input type="hidden" name="id_factura" value="<?=$id_factura?>">
<table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
    	<?
    	if (!$id_factura) {
    	?>  
    	<font size=+1><b>Nueva Factura</b></font>&nbsp;&nbsp;&nbsp;
    	<img src='<?php echo "$html_root/imagenes/ayuda.gif" ?>' style="cursor:hand" border="0" alt="Ayuda" onClick="abrir_ventana('<?php echo "$html_root/modulos/ayuda/facturacion/nueva_factura.htm" ?>', 'Agregar Factura')" >   
    	<? }
        else {
        ?>
        <font size=+1><b>Notificacion Telefonica </b></font> &nbsp;&nbsp;&nbsp;
         <? } ?>
       
    </td>
 </tr>
 <tr><td>
  <table width=70% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b> Descripción de la FACTURA</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
         <tr>	           
           <td align="center" colspan="2">
            <b> Número de Factura: <font size="+1" color="Red"><?=($id_factura)? $id_factura : "Nueva Factura"?></font> </b>
           </td>
         </tr>
         <tr>
         	<td align="right">
				<b>Efector:</b>
			</td>
			<td align="left">			 	
			 <select name=cuie Style="width=450px" 
        	onKeypress="buscar_combo(this);"
			onblur="borrar_buffer();"
			onchange="borrar_buffer();"
        	<?if ($id_factura) echo "disabled"?>>
			 
			 <?
			  $user_login1=substr($_ses_user['login'],0,6);
			  if (es_cuie($_ses_user['login']))
			  $sql= "select cuie, nombre, com_gestion from nacer.efe_conv where cuie='$user_login1' order by nombre";	
			  else{
			  	echo"<option value=-1>Seleccione</option>";
			  	$sql= "select cuie, nombre, com_gestion from nacer.efe_conv order by nombre";
			 	 }
			 
			 $res_efectores=sql($sql) or fin_pagina();
			 while (!$res_efectores->EOF){ 
			 	$cuiel=$res_efectores->fields['cuie'];
			    $nombre_efector=$res_efectores->fields['nombre'];
			    
			    ?>
				<option value='<?=$cuiel?>' <?if ($cuie==$cuiel) echo "selected"?> ><?=$cuiel." - ".$nombre_efector?></option>
			    <?
			    $res_efectores->movenext();
			    }?>
			</select>
			</td>
         </tr>
         <tr>
			<td align="right">
				<b>Fecha Factura:</b>
			</td>
		    <td align="left">
		    	<?$fecha_comprobante=date("d/m/Y");?>
		    	 <input type=text id=fecha_factura name=fecha_factura value='<?=fecha($fecha_factura);?>' size=15 <? if ($id_factura) echo "readonly"?>>
		    	 		    	 
		    </td>		    
		</tr>
		<tr>
         	<td align="right">
				<b>Periodo Actual:</b>
			</td>
			<td align="left">
			<input type=text id=periodo name=periodo value='<?=$periodo;?>' size=15 <? if ($id_factura) echo "readonly"?>>
			</td>
         </tr>
         
         <tr>
         	<td align="right">
				<b>Periodo Prestación:</b>
			</td>
			 <td align="left">
			<input type=text id=periodo_actual name=periodo_actual value='<?=$periodo_actual;?>' size=15 <? if ($id_factura) echo "readonly"?>>
			</td>
			
         </tr>
         <tr>
         	<td align="right">
         	  <b>Monto Prefactura:</b>
         	</td>         	
            <td align='left'>
              <input type="text" name="monto_prefactura" value="$ <?=number_format($monto_prefactura,2,'.','')?>" style="width=250px">&nbsp;&nbsp;                             
            </td>
         	</tr>							 
         <tr>
         	<td align="right">
         	  <b>Comentario sobre la comunicacion:</b>
         	</td>         	
            <td align='left'>
              <textarea cols='70' rows='7' name='observaciones' ><?=$observaciones;?></textarea>
            </td>
         </tr>
			<?if ($estado=='C'){?>
		   
         	
         	<?($traba=='si')?$disabled="disabled":$disabled=""?>
         	
                                  	 
   		<?}?> 
                 
        </table>
      </td>      
     </tr>
     
  	
   <?if ($id_factura){?>
	 
	 <tr id="mo">
  		<td align=center colspan="2">
  			<b>Guarda Notificacion</b>
  		</td>
  	</tr>  
      <tr align="center">
       <td>
        <input type='submit' name='guardar' value='Guardar Notificacion' onclick="document.location='listado_factura_nuevo.php'"
         title="Guardar datos de la Factura">
       </td>
      </tr>
     
     <?}?>
     
 </table>          
 </table>
 </form>
 
 <?=fin_pagina();// aca termino ?>