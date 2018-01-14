<?
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



$sql_tmp="SELECT * FROM expediente.transaccion left join expediente.expediente using (id_expediente) left join nacer.efe_conv using (id_efe_conv) where id_expediente='$id_expediente'";
//$sql_tmp="SELECT * FROM expediente.expediente left join nacer.efe_conv using (id_efe_conv) left join expediente.transaccion using (id_expediente) where id_expediente='$id_expediente'";
$result_his = sql($sql_tmp) or die;

if ($_POST['guardar']=="Guardar"){   
   
	$db->StartTrans();         
           
    $q_trans="select nextval('expediente.transaccion_id_transac_seq') as id_transac";
    $id_transac=sql($q_trans) or fin_pagina();
    $id_transac=$id_transac->fields['id_transac'];
    
    switch ($estado) {
    	case "A": $control=1;$id_area=4;break;
    	case "D": $control=2;$id_area=2;break;
    	case "R": $control=0;$id_area=3;break;
		};
          
   $mes=extrae_mes($fecha_alta);
   $anio=extrae_anio($fecha_alta);
   
   $fecha_alta=Fecha_db($fecha_alta);   
   
   //$cuie_nuevo=devuelve_cuie($id_efector);  
     
	//$nro_exp= "$mes$anio$id_expediente$cuie_nuevo";
	$nro_exp=$result_his->fields['nro_exp'];
	
	$fecha_mov = date("m/d/Y");
	$total=$monto-$debito+$credito; 

	$usuario=$_ses_user['name'];
		
	$query_trans="INSERT INTO expediente.transaccion
               (id_transac,
  				id_expediente,
               	id_area,
               	fecha_mov,
               	estado,
               	comentario,
               	debito,
               	credito,
               	total_pagar,
               	id_factura,
               	usuario
               	
                 	)
             VALUES
              ('$id_transac',
  				'$id_expediente',
  				'$id_area',
  				'$fecha_mov',
  				'$estado',
  				'$comentario',
  				'$debito',
  				'$credito',
  				'$total',
  				'$id_factura',
  				'$usuario'
  				)";

    
    $query_exp = "UPDATE expediente.expediente SET control='$control',monto='$total',comentario1='$comentario' WHERE id_expediente=$id_expediente";
    
    sql($query_trans, "Error al insertar la transaccion") or fin_pagina();    
    
    sql ($query_exp, "Error al Insertar el expediente") or fin_pagina();
    
    if ($id_factura) { $sql_fact="UPDATE expediente.expediente SET id_factura=$id_factura where id_expediente=$id_expediente";
    					sql($sql_fact,"Error al insertar la factura en el expediente") or fin_pagina();
    					};
    
    $accion="Se guardo la Alta";    
	 
    $db->CompleteTrans(); 

   
    
    
}//de if ($_POST['guardar']=="Guardar Expediente")





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

<form name='form1' action='mod_exp_coord.php' method='POST'>
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
        $sql_exp="SELECT * FROM expediente.expediente WHERE id_expediente=$id_expediente";
    	$result_exp= sql ($sql_exp, "Error al abrir el registro") or fin_pagina();
        $fecha_ing=$result_exp->fields['fecha_ing'];
    	$fecha_fin=$result_exp->fields['plazo_para_pago'];
        $id_expediente_fact=$result_exp->fields['id_factura'];
    	?>
        <font size=+1><b>EXPEDIENTE EN AREA DE COORDINACION</b></font>   
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
		    	 <?php ?>
		    	 <input type=text id=fecha_alta name=fecha_alta value='<?=fecha($fecha_ing);?>' size=15 readonly>
		    	 				    	 
		    </td>		    
		</tr>
		
		 <tr>
			<td align="right">
				<b>Fecha de Plazo para Pago:</b>
			</td>
		    <td align="left">		    	
		    	 <?php ?>
		    	 <input type=text id=fecha_fin name=fecha_fin value='<?=fecha($fecha_fin);?>' size=15 readonly>
		    	 				    	 
		    </td>		    
		</tr>
		         
         <tr>
         	<td align="right">
         	  <b>Monto de Facturacion:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=number_format($monto,2,'.','')?>" name="monto" readonly>
            </td>
         </tr>
         
         <tr>
         	<td align="right">
         	  <b>debito:</b>
         	</td>         	
            <td align='left'>
              <?php 
					if (!$id_transac) {	              
              
              		$sql_debito="SELECT * FROM facturacion.debito WHERE id_factura='$id_expediente_fact'";
					$result_debito = sql($sql_debito) or die;
					$debito=0;
					while (!$result_debito -> EOF) {
					$debito=$debito+($result_debito->fields['monto'] * $result_debito->fields['cantidad']) ;
					$result_debito->MoveNext();
					};
					if (!$debito) $debito=0;}
					
					else {
					$sql_tran="SELECT * FROM expediente.transaccion WHERE id_transac=$id_transac";
    				$result_tran= sql ($sql_tran, "Error al abrir el registro") or fin_pagina();
    				$sql_exp="SELECT * FROM expediente.expediente WHERE id_expediente=$id_expediente";
    				$result_exp= sql ($sql_exp, "Error al abrir el registro") or fin_pagina();
    				$debito=$result_tran->fields['debito'];
    				}
					?>
              <input type="text" size="40" value="<?= number_format($debito,2,'.','')?>" name="debito" readonly>
            </td>
         </tr>
         
<tr>
         	<td align="right">
         	  <b>credito:</b>
         	</td>         	
            <td align='left'>
              <?php if (!$id_transac) {	
              
              		$sql_credito="SELECT * FROM facturacion.credito WHERE id_factura='$id_expediente_fact'";
					$result_credito = sql($sql_credito) or die;
					$credito=0;
					while (!$result_credito -> EOF) {
					$credito=$credito+($result_credito->fields['monto'] * $result_credito->fields['cantidad']);
					$result_credito->MoveNext();
					};
					if (!$credito) $credito=0;}
					
					else 
					$credito=$result_tran->fields['credito'];
    				
					?>
              
              <input type="text" size="40" value="<?=number_format($credito,2,'.','')?>" name="credito" readonly>
            </td>
         </tr>         
<tr>
         	<td align="right">
         	  <b>Total:</b>
         	</td>         	
            <td align='left'>
              <?php if (!$id_transac) $total=$monto-$debito+$credito;
              
              else $total=$result_tran->fields['total_pagar'];
    			?>
              
              
              <input type="text" size="40" value="<?=number_format($total,2,'.','')?>" name="total" readonly>
            </td>
         </tr>     
<tr>
         	<td align="right">
         	  <b>Estado:</b>
         	</td>         	
            <td align='left'>
              <select size="1" name="estado">
              <option value=-1>Seleccione</option>
              <option value='<?=$estado='A'?>'>Aceptado</option>
              <option value='<?=$estado='D'?>'>Derivado</option>
             <option value='<?=$estado='R'?>'>Rechazado</option>
                                         
              </select>
            </td>
         </tr> 
 
   <tr>
         	<td align="right">
         	  <b>Comentario:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="200" value="<?=""?>" name="comentario" >
            </td>
         </tr>
   

   <?if (($id_expediente)){?>
	 
	 <tr id="mo">
  		<td align=center colspan="2">
  			<b>Guarda Modificacion del Expediente</b>
  		</td>
  	</tr>  
      <tr align="center">
       <td>
        <input type='submit' name='guardar' value='Guardar' onclick="return control_nuevos()"
         title="Guardar datos del Expediente">
       </td>
      </tr>
     
     <?}?>
     
    
     
 </table>           
<br>

 
 
<tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
     <input type=button name="volver" value="Volver" onclick="document.location='expediente_coord.php'"title="Volver al Listado" style="width=150px">     
   </td>
  </tr>
 </table></td></tr>
 
    
 </table></td></tr>
 
 </table>
 
 
 </form>
 
 <?=fin_pagina();// aca termino ?>