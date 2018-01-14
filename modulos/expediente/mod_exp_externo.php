<?php
/*
Author: sebastian lohaiza

modificada por
$Author: seba $
$Revision: 1.30 $
$Date: 2009/11/01 18:25:40 $
*/
require_once ("../../config.php");


extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();


function extrae_mes($fecha) {
        list($d,$m,$a) = explode("/",$fecha);
        return $m;
}

function extrae_anio($fecha) {
        list($d,$m,$a) = explode("/",$fecha);
        $a=$a-2000;
        return $a;
}
function devuelve_cuie($id_efe_conv){
	$sql= "select * from nacer.efe_conv where id_efe_conv=$id_efe_conv ";
	$cuie_efectores=sql($sql) or fin_pagina();
	return $cuie_efectores->fields['cuie'];
}



$sql_tmp="SELECT * FROM (select * from expediente.transaccion where id_area=1 and estado='D')as tabla left join expediente.expediente  using (id_expediente) left join nacer.efe_conv using (id_efe_conv) where id_expediente='$id_expediente'";
//el problema esta en que lee el primer expediente
$result_his = sql($sql_tmp) or die;


if ($_POST['guardar']=="Guardar" ){   
   $db->StartTrans();         
    
   $q_trans="select nextval('expediente.transaccion_id_transac_seq') as id_transac";
   $id_transac=sql($q_trans) or fin_pagina();
   $id_transac=$id_transac->fields['id_transac'];
  
        
   $mes=extrae_mes($fecha_alta);
   $anio=extrae_anio($fecha_alta);
   
   $fecha_alta=Fecha_db($fecha_alta);   
   $fecha_informe=Fecha_db($fecha_informe);
   //$cuie_nuevo=devuelve_cuie($id_efector);  
     
	$nro_exp= $result_his->fields['nro_exp'];
	$nombre_efector=$result_his->fields['nombre'];
	$monto=$result_his->fields['monto'];
	$debito=$result_his->fields['debito'];
	$debito=$result_his->fields['credito'];
	$total=$result_his->fields['total_pagar'];
	$fecha_mov = date("m/d/Y");
	$nro_factura = $result_his->fields['id_factura'];
	
	switch ($estado) {
		case "A": $control=4;$id_area=1;$id_area_origen=1;break;
    	case "R": $control=0;$id_area=3;$id_area_origen=1;$num_tranf=0;break;
    	//case "E": $control=2;$id_area=2;$comentario="Expediente con Error (especificar):";break;
    };
	
    $usuario=$_ses_user['name'];
    
  	$query_trans="insert into expediente.transaccion
               (id_transac,
  				id_expediente,
               	id_area,
               	fecha_mov,
               	estado,
               	comentario,
               	debito,
               	credito,
               	num_tranf,
               	total_pagar,
               	id_factura,
               	usuario,
               	id_area_origen
              	)
             values
              ('$id_transac',
  				'$id_expediente',
  				'$id_area',
  				'$fecha_mov',
  				'$estado',
  				'$comentario',
  				'$debito',
  				'$credito',
  				'$num_tranf',
  				'$total',
  				'$nro_factura',
  				'$usuario',
  				'$id_area_origen'
  				)";
  	
  	$query_exp = "UPDATE expediente.expediente SET control='$control', comentario1='$comentario', estado='$estado' WHERE id_expediente=$id_expediente";

    sql($query_trans, "Error al insertar la transaccion") or fin_pagina();
    sql ($query_exp, "Error al Insertar el expediente") or fin_pagina();    
    
    $accion="Se guardo la Alta";    
	 
    $db->CompleteTrans();  
    
}//de if ($_POST['guardar']=="Guardar Expediente")




echo $html_header;
?>
<script>


function control_nuevos()
{ 
	if (document.all.estado.value=="R"){
		if(document.all.comentario.value==""){
		  alert('En el cado de Rechazo Debe Ingresar un comentario');
		  return false;
		 }
	  }

 	if(document.all.num_tranf.value==""){
  		alert('Debe Cargar un Numero de Expediente');
  		return false;
 		} 
}//de function control_nuevos()
</script>

<form name='form1' action='mod_exp_externo.php' method='POST'>
<input type="hidden" value="<?=$id_expediente?>" name="id_expediente">
<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>
<table width="60%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
    	<?
    	if (!$id_expediente) {
    	?>  
    	<font size=+1><b>Ingreso Datos de Expediente</b></font>   
    	<? }
        else {
        ?>
        <font size=+1><b>EXPEDIENTE EN AREA ADMINISTRATIVA</b></font>   
        <? } ?>
       
    </td>
 </tr>
 <tr><td align="center">
  <table width=80% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b>Datos Basico</b>
      </td>
     </tr>
     <tr>
       <td align="center">
        <table align="center">
         <tr>	           
           <td align="center" colspan="2">
            <b> Número del Expediente: <font size="+1" color="Red"><?=($id_expediente)? $nro_exp : "Nuevo Dato"?></font> </b>
           </td>
         </tr>
    
          <tr>
         	<td align="right">
				<b>Efector: </b>
			</td>
			<td align="left">		    	
		    	 <input type=text id=nombre_efector name=nombre_efector value='<?=$nombre_efector;?>' size=40 readonly>			    	 
		    </td>
         </tr>
         
        <tr>
         	<td align="right">
				<b>Numero de Factura: </b>
			</td>
			<td align="left">		    	
		    	 <input type=text id=id_factura name=id_factura value='<?=$id_expediente_fact;?>' size=40 >
		    	 				    	 
		    </td>
         </tr>
         
         <tr>
			<td align="right">
				<b>Fecha Alta:</b>
			</td>
		    <td align="left">		    	
		    	 <input type=text id=fecha_alta name=fecha_alta value='<?=fecha($fecha_ing);?>' size=15 readonly>
		    	 				    	 
		    </td>		    
		</tr>
			         
         <tr>
         	<td align="right">
         	  <b>Monto de Facturacion($):</b>
         	</td>         	
            <? 
              if ($id_expediente_fact){
              $sql_tmp1="select monto_prefactura from facturacion.factura where id_factura='$id_expediente_fact'";
              $result_his1= sql($sql_tmp1) or die;
              $monto=$result_his1->fields['monto_prefactura'];}
              else {$monto=$result_his->fields['monto'];}?>
            <td align='left'>
              <input type="text" size="40" value="<?=number_format($monto,2,'.','');?>" name="monto" readonly>
            </td>
         </tr>
         
         <tr>
         	<td align="right">
         	  <b>Debito($):</b>
         	</td>         	
            <td align='left'>
              
              <input type="text" size="40" value="<?=number_format($result_his->fields['debito'],2,'.','');?>" name="debito" readonly>
            </td>
         </tr>
         <tr>
         	<td align="right">
         	  <b>Credito($):</b>
         	</td>         	
            <td align='left'>
              
              <input type="text" size="40" value="<?=number_format($result_his->fields['credito'],2,'.','');?>" name="credito" readonly>
            </td>
         </tr>
         <tr>
         	<td align="right">
         	  <b>Total a pagar($):</b>
         	</td>         	
            <td align='left'>
              
              <input type="text" size="40" value="<?=number_format($result_his->fields['total_pagar'],2,'.','');?>" name="total" readonly>
            </td>
         </tr>
         
         <tr>
         	<td align="right">
         	  <b>Nro.de Expediente Provincial:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$num_tranf?>" name="num_tranf" >
            </td>
         </tr>
         
         <tr>
        <td align="right">
         	  <b>Estado:</b>
         		</td>         	
            	<td align='left'>
             	<select size="1" name="estado">
        <? switch ($estado_exp){
			case 'D':{?>
             		 <option value=-1>Seleccione</option>
             		 <option value='<?=$estado='A'?>'>Aceptado</option>
            	     <option value='<?=$estado='R'?>'>Rechazo</option>
              		 <? };break;
         							
		}?>
		 		</select>
          	</td>
         </tr> 
 
		<tr>
         	<td align="right">
         	  <b>Comentario:</b>
         	</td>         	
            <td align='left'>
				<textarea cols='39' rows='4' name='comentario'></textarea>
            </td>           
         </tr>
   
   <?if (($id_expediente)){?>	 
	 <tr id="mo">
  		<td align=center colspan=8>
  			<b>Guarda Modificacion del Expediente</b>
  		</td>
  	</tr>  
      <tr >
       <td align="center" colspan=8>
        <input type='submit' align ="center" name='guardar' value='Guardar' Style="width=250px;height=30px;background:#82FA58" onclick="return control_nuevos()"
         title="Guardar datos del Expediente">
       </td>
      </tr>
     
     <?}?>
     
     <table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
     <input type=button aling ="center" name="volver" value="Volver" onclick="document.location='exp_pendientes.php'"title="Volver al Listado" style="width=150px">     
   </td>
  </tr>
 </table>
     
 </table>           
 </table>           
<br>

 </form>
 
 <?=fin_pagina();// aca termino ?>
