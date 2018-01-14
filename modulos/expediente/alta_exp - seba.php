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

if ($_POST['guardar']=="Guardar E Imprimir"){   
   $db->StartTrans();         
    
    $q="select nextval('expediente.expediente_id_expediente_seq') as id_expediente";
    $id_expediente=sql($q) or fin_pagina();
     
    $id_expediente=$id_expediente->fields['id_expediente'];
    
    /*$q_trans="select nextval('expediente.transaccion_id_transac_seq') as id_transac";
    $id_transac=sql($q_trans) or fin_pagina();
    $id_transac=$id_transac->fields['id_transac'];
    $id_area=1;
    $estado='A';*/
    $comentario="Expediente en mesa de entrada";
   
   $mes=extrae_mes($fecha_alta);
   $anio=extrae_anio($fecha_alta);
   
   $fecha_alta=Fecha_db($fecha_alta);
   $dias=50;
   $plazo_para_pago=date("Y-m-d", strtotime ("+$dias days"));
   
       
   $fecha_informe=Fecha_db($fecha_informe);
   $cuie_nuevo=devuelve_cuie($id_efector);  
     
	$nro_exp= "$mes$anio$id_expediente$cuie_nuevo";
     
    $query="insert into expediente.expediente
               (id_expediente,
  				id_efe_conv,
               	fecha_ing,
  				monto,
  				nro_exp,
  				plazo_para_pago,
  				comentario1
  				)
             values
              ('$id_expediente',
  				'$id_efector',
  				'$fecha_alta',
  				'$monto',
  				'$nro_exp',
  				'$plazo_para_pago',
  				'$comentario'
  				)";

    
	/*$query_trans="insert into expediente.transaccion
               (id_transac,
  				id_expediente,
               	id_area,
  				fecha_mov  				
  				)
             values
              ('$id_transac',
  				'$id_expediente',
  				'$id_area',
  				'$fecha_alta'
  				)";*/

    
    //echo ($query);
    
    sql($query, "Error al insertar el Expediente") or fin_pagina();
    //sql($query_trans, "Error al insertar el Expediente") or fin_pagina();    
    
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



<form name='form1' action='alta_exp.php' method='POST' enctype='multipart/form-data'>
<input type="hidden" value="<?=$id_expediente?>" name="id_expediente">
<input type="hidden" value="<?=$id_efector?>" name="id_efector">
<input type="hidden" value="<?=$id_factura?>" name="id_factura">


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
        <font size=+1><b>Caratula</b></font>   
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
				<b>Efector que da el Alta:</b>
			</td>
         	
			<td align="left">			 	
			 <select name=id_efector Style="width=257px" 
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer();"
				onchange="borrar_buffer();document.forms[0].submit()" 
				<?if ($id_expediente) echo "disabled"?> >
			 <option value=-1>Seleccione</option>
			 <?
			 $sql= "select * from nacer.efe_conv order by nombre";
			 $res_efectores=sql($sql) or fin_pagina();
			 while (!$res_efectores->EOF){ 
			 	$cuiel=$res_efectores->fields['cuie'];
			    $nombre_efector=$res_efectores->fields['nombre'];
			    $id_efector=$res_efectores->fields['id_efe_conv'];
			    
			    ?>
				<option value='<?=$id_efector?>' <?if ($cuie==$cuiel) echo "selected"?> ><?=$nombre_efector?></option>
			    <?
			    $res_efectores->movenext();
			    }?>
			    </select>
			   </td>
			 </tr>
             
       	<tr>
	         	<td align="right">
	         	 <?$ref = $ref = encode_link("efector.php",array("id_efector"=>$id_efector,$pagina=>"alta_exp.php"));?>
			    	<b><A HREF="<?=$ref?>" target="_blank">Numero de Factura:</A></b>
	         	</td>
				<td align="left">		
				<select name=id_factura Style="width=200px" 
		        		onKeypress="buscar_combo(this);"
						onblur="borrar_buffer();"
						onchange="borrar_buffer();document.forms[0].submit()" 
						<?if ($id_expediente) echo "disabled"?>>
						<option value=-1>Seleccione</option>
						<?
						if($id_efector){
					 		$sql_factura= "SELECT * FROM facturacion.factura 
					 					   INNER JOIN nacer.efe_conv ON facturacion.factura.cuie=nacer.efe_conv.cuie 
									    	where id_efe_conv='$id_efector' order by id_factura";
							 $res_factura=sql($sql_factura) or fin_pagina();
							 while (!$res_factura->EOF){ 
							 	$id_factura_tem=$res_factura->fields['id_factura'];
							    $monto_temp=$res_factura->fields['monto_prefactura'];
							    ?>
								<option value='<?=$id_factura_tem?>'<? if ($id_factura_tem==$id_factura) echo "selected"?> ><?=$id_factura_tem?></option>
							    <?
							    $res_factura->movenext();
							    }
						}//fin de if $ed_efector?>

					</select>	
				</td>
			</tr> 
         <tr>
			<td align="right">
				<b>Fecha Alta:</b>
			</td>
		    <td align="left">		    	
		    	 <input type=text id=fecha_alta name=fecha_alta value='<?=fecha($fecha_alta);?>' size=15 readonly>
		    	 <?=link_calendario("fecha_alta");?>					    	 
		    </td>		    
		</tr>
		
		         
         <tr>
         	<td align="right">
         	  <b>Monto de Facturacion ($):</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value=" <?=$monto?>" name="monto" <? if ($id_expediente) echo "readonly"?>><font color="Red">Use Punto para centavos</font>
            </td>
         </tr>
         
                     
        
   

   <?if (!($id_expediente)){?>
	 
	 <tr id="mo">
  		<td align=center colspan="2">
  			<b>Guarda Expediente E Imprimir Caratula</b>
  		</td>
  	</tr>  
      <tr align="center">
       <td>
        <input type='submit' name='guardar' value='Guardar E Imprimir' onclick="return control_nuevos()"
         title="Guardar datos del Expediente">
       </td>
      </tr>
     
     <?}?>
     
 </table>           
<br>

 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
     <input type=button name="volver" value="Volver" onclick="document.location='alta_exp.php'"title="Volver al Listado" style="width=150px">     
   </td>
  </tr>
 </table></td></tr>
 
    
 </table></td></tr>
 
 </table>
 </form>
 
 <?=fin_pagina();// aca termino ?>