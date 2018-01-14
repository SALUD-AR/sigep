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



//$sql_tmp="SELECT * FROM (select * from expediente.transaccion where num_tranf is not null) as tabla left join expediente.expediente  using (id_expediente) left join nacer.efe_conv using (id_efe_conv) where id_expediente='$id_expediente'";
$sql_tmp="SELECT * FROM (select * from expediente.transaccion where id_area=1 and estado='D' and num_tranf is not null) as tabla left join expediente.expediente  using (id_expediente) left join nacer.efe_conv using (id_efe_conv) where id_expediente='$id_expediente'";
//el problema esta en que lee el primer expediente
$result_his = sql($sql_tmp) or die;
$num_tranf=$result_his->fields['num_tranf'];


echo $html_header;
?>
<script>
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
</script>

<form name='form1' action='mod_acred_exp.php' method='POST'>
<input type="hidden" value="<?=$id_expediente?>" name="id_expediente">
<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>
<table width="85%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
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
 <tr><td>
  <table width=90% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b>Datos Basico</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
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
		    	 <input type=text id=nombre_efector name=nombre_efector value='<?=$nombre_efector;?>' size=50 readonly>
		    	 				    	 
		    </td>
         </tr>
         
        <tr>
         	<td align="right">
				<b>Numero de Factura: </b>
			</td>
			<td align="left">		    	
		    	 <input type=text id=id_factura name=id_factura value='<?=$id_expediente_fact;?>' size=50 readonly>
		    	 				    	 
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
              <?php $total=$monto-$debito+$credito?>
              <input type="text" size="40" value="<?=number_format($total,2,'.','');?>" name="total" readonly>
            </td>
         </tr>
         
         <tr>
         	<td align="right">
         	  <b>Nro.de Expediente Provincial:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$num_tranf?>" name="num_tranf" readonly>
            </td>
         </tr>
         
         <tr>
			<td align="right">
				<b>Fecha de Ingreso a Casa de Gobierno:</b>
			</td>
		    <td align="left">		    	
		    	 <? $fecha_mov=$result_his->fields['fecha_mov']?>
		    	 <input type=text id=fecha_informe name=fecha_informe value='<?=fecha($fecha_mov);?>' size=15 readonly>
		    	 			    	 
		    </td>		    
		</tr>
               

<tr>
         	
         </tr> 
 
   
   

   
     
     <table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
     <input type=button aling ="center" name="volver" value="Volver" onclick="document.location='acreditacion_exp.php'"title="Volver al Listado" style="width=150px">     
   </td>
  </tr>
 </table>
     
 </table>           
<br>

 </form>
 
 <?=fin_pagina();// aca termino ?>