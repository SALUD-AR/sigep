<?

require_once ("../../config.php");


extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();


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
$observaciones=$res_factura->fields['observaciones'];
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

var img_ext='<?=$img_ext='../../imagenes/rigth2.gif' ?>';//imagen extendido
var img_cont='<?=$img_cont='../../imagenes/down2.gif' ?>';//imagen contraido
function muestra_tabla(obj_tabla,nro){
 oimg=eval("document.all.imagen_"+nro);//objeto tipo IMG
 if (obj_tabla.style.display=='none'){
 	obj_tabla.style.display='inline';
    oimg.show=0;
    oimg.src=img_ext;
 }
 else{
 	obj_tabla.style.display='none';
    oimg.show=1;
	oimg.src=img_cont;
 }
}
/**********************************************************/
//funciones para busqueda abreviada utilizando teclas en la lista que muestra los clientes.
var digitos=10; //cantidad de digitos buscados
var puntero=0;
var buffer=new Array(digitos); //declaración del array Buffer
var cadena="";

function buscar_combo(obj)
{
   var letra = String.fromCharCode(event.keyCode)
   if(puntero >= digitos)
   {
       cadena="";
       puntero=0;
   }   
   //sino busco la cadena tipeada dentro del combo...
   else
   {
       buffer[puntero]=letra;
       //guardo en la posicion puntero la letra tipeada
       cadena=cadena+buffer[puntero]; //armo una cadena con los datos que van ingresando al array
       puntero++;

       //barro todas las opciones que contiene el combo y las comparo la cadena...
       //en el indice cero la opcion no es valida
       for (var opcombo=1;opcombo < obj.length;opcombo++){
          if(obj[opcombo].text.substr(0,puntero).toLowerCase()==cadena.toLowerCase()){
          obj.selectedIndex=opcombo;break;
          }
       }
    }//del else de if (event.keyCode == 13)
   event.returnValue = false; //invalida la acción de pulsado de tecla para evitar busqueda del primer caracter
}//de function buscar_op_submit(obj)

//-----------------Editar factura-------------------

function editar_campos()
{	
	document.all.periodo.disabled=false;
	document.all.observaciones.disabled=false;
	document.all.periodo_actual.disabled=false;
		
	document.all.guardar_editar.disabled=false;
	document.all.cancelar_editar.disabled=false;

	return true;
}

//-----------------Fin de Editar Factura------------

</script>

<form name='form1' action='notificacion_det.php' method='POST'>
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
    	  
    	<? }
        else {
        ?>
        <font size=+1><b>Detalle de Notificacion </b></font> &nbsp;&nbsp;&nbsp;
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
         	  <b>Dias de mora segun cierra:</b>
         	</td>         	
            <td align='left'>
              <input type="text" name="monto_prefactura" value="<?=$dias_mora?>" style="width=250px">&nbsp;&nbsp;                             
            </td>
         	</tr>	
			                
        </table>
      </td>      
     </tr>
     
  	
<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=10 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       
      </tr>
    </table>
    </td>
  </tr>
    
<tr>
    <td align=right id=mo><a id=mo >Fecha de Notificacion</a></td>
    <td align=right id=mo><a id=mo >Mail`s</a></td>
    <td align=right id=mo><a id=mo >Via de Comunicacion</a></td>
    <td align=right id=mo><a id=mo >Comentario</a></td>
    <td align=right id=mo><a id=mo >Usuario</a></td>  
   
     
   
  </tr>


<?
$consulta_notif="select * from facturacion.notificacion where id_factura='$id_factura' order by (fecha_notif) DESC";
$result_not=sql($consulta_notif,"Error en consultar el registro") or fin_pagina();
  

while (!$result_not->EOF) {
   	
   /*	$ref = encode_link("mod_exp.php",array("nro_exp"=>$result->fields['nro_exp'],"id_expediente"=>$result->fields['id_expediente'],"nombre_efector"=>$result->fields['nombre'],"fecha_ing"=>$result->fields['fecha_ing'],"monto"=>$result->fields['monto']));
    $onclick_elegir="location.href='$ref'";*/
   	?>
  
    <tr <?=atrib_tr()?>>     
      <td  align="center" > <?=$result_not->fields['fecha_notif']?></td>
      <td align="center" ><?=$result_not->fields['mail_efe']?></td>
      <td align="center" ><?=$result_not->fields['via_comunicacion']?></td>
      <td><?=$result_not->fields['comentario']?></td>
      <td><?=$result_not->fields['usuario']?></td>
    </tr>    
    </tr>
	<?$result_not->MoveNext();
    }?>
 
 <table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
     <input type=button aling ="center" name="volver" value="Volver" onclick="document.location='listado_factura_nuevo.php'"title="Volver al Listado" style="width=150px">     
   </td>
  </tr>
 </table>
    
</table>
     
 </table>          
 </table>
 </form>
 
 <?=fin_pagina();// aca termino ?>