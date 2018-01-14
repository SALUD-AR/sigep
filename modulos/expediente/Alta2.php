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

if ($_POST['guardar']=="Guardar E Imprimir"){   
   $db->StartTrans();         
    
    $q="select nextval('expediente.expediente_id_expediente_seq') as id_expediente";
    $id_expediente=sql($q) or fin_pagina();
     
    $id_expediente=$id_expediente->fields['id_expediente'];
    
    $comentario="Expediente en mesa de entrada";
   
   $mes=extrae_mes($fecha_alta);
   $anio=extrae_anio($fecha_alta);
   
   $fecha_alta=Fecha_db($fecha_alta);
   $dias=50;
   $plazo_para_pago=date("Y-m-d", strtotime ("+$dias days"));
   
       
   $fecha_informe=Fecha_db($fecha_informe);
   $cuie_nuevo=devuelve_cuie($id_efector);  
     
	$nro_exp= "$mes$anio$id_expediente$cuie_nuevo";
     
    $query="select * from facturacion.factura where cuie=$cuie_nuevo";

    $result = sql($query) or die;

    
    //sql($query, "Error al consultar el Expediente") or fin_pagina();
     
    $accion="Se guardo la Alta";    
	 
    $db->CompleteTrans();  
    

?>    
<form name=form2 action="expediente.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
    
</table>

<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=10 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$total_muletos?></td>       
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  
<tr>
    <td align=right id=mo><a id=mo href='<?=encode_link("expediente.php",array("sort"=>"1","up"=>$up))?>'>id_factura</a></td>
     <td align=right id=mo><a id=mo href='<?=encode_link("expediente.php",array("sort"=>"6","up"=>$up))?>'>cuie</a></td> 
     <td align=right id=mo><a id=mo href='<?=encode_link("expediente.php",array("sort"=>"6","up"=>$up))?>'>periodo</a></td>     	
    <td align=right id=mo><a id=mo href='<?=encode_link("expediente.php",array("sort"=>"2","up"=>$up))?>'>nro_exp</a></td>      	
    <td align=right id=mo><a id=mo href='<?=encode_link("expediente.php",array("sort"=>"2","up"=>$up))?>'>Monto prefactura</a></td>
  
     
   
  </tr>


<?
   while (!$result->EOF) {
   	
   	?>
  
    <tr <?=atrib_tr()?>>     
      <td><?=$result->fields['id_factura']?></td>
     <td>"><?=$result->fields['cuie']?></td>
      <td><?=$result->fields['periodo']?></td> 
     <td>" align="center" > <?=$result->fields['nro_exp']?></td>
    <td>$<?=$result->fields['monto_prefactura']?></td>
         
            
    </tr>    
    </tr>
	<?$result->MoveNext();
    }?>
    
</table>
</form>
<?php }//de if ($_POST['guardar']=="Guardar Expediente")




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

<form name='form1' action='alta2.php' method='POST'>
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
				onchange="borrar_buffer();" 
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
       
   <?if (!($id_expediente)){?>
	 
	  <tr align="center">
       <td>
        <input type='submit' name='guardar' value='Busqueda de Facturas' onclick="return control_nuevos()"
         title="Guardar datos del Expediente">
       </td>
      </tr>
     
     <?}?>
     
 </table>           
<br>

 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
     <input type=button name="volver" value="Volver" onclick="document.location='alta2.php'"title="Volver al Listado" style="width=150px">     
   </td>
  </tr>
 </table></td></tr>
 
    
 </table></td></tr>
 
 </table>
 </form>
 
 <?=fin_pagina();// aca termino ?>
