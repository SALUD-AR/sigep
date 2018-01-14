<?

require_once ("../../config.php");


extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

if ($_POST['guardar']=="Guardar Planilla"){
   $fecha_carga=date("Y-m-d H:m:s");
   $usuario=$_ses_user['name'];
   $db->StartTrans();         
    
   $q="select nextval('planillas.planillas_id_planillas_seq') as id_planilla";
    $id_planilla=sql($q) or fin_pagina();
    $id_planilla=$id_planilla->fields['id_planilla'];
   
   $query="insert into planillas.planillas
             (id_planillas,id_agente_inscriptor,id_entrega,id_recibe,periodo,fecha_hora,cant_nino,cant_embarazada,motivo,usuario,tipo,id_efector)
             values
             ($id_planilla,$agente_inscriptor,$entrega,$recibe,'$periodo','$fecha_carga',$cant_ninos,$cant_embarazada,'$observaciones','$usuario','$tipo','$cuie')";

    sql($query, "Error al insertar la factura") or fin_pagina();
    
    $accion="Se guardo la Planilla Numero: $id_planilla";
	
    /*cargo los log*/     
	$log="insert into planillas.log_planilla
		   (id_planillas, fecha,descripcion, usuario) 
	values ($id_planilla, '$fecha_carga','Alta de Planilla', '$usuario')";
	sql($log) or fin_pagina();
	 
    $db->CompleteTrans();    
}//de if ($_POST['guardar']=="Guardar nuevo Muleto")

if ($_POST['borrar']=="Borrar"){
	$query="delete from planillas.log_planilla
			where id_planillas=$id_planilla";
	sql($query, "Error al insertar la Planilla") or fin_pagina();
	$query="delete from planillas.planillas
			where id_planillas=$id_planilla";
	sql($query, "Error al insertar la Planilla") or fin_pagina();
	$accion="Se elimino la planilla $id_planilla de Embarazadas"; 	
}

if ($id_planilla) {
$query="SELECT 
  planillas.planillas.id_planillas,
  planillas.planillas.periodo,
  planillas.planillas.fecha_hora,
  planillas.planillas.cant_nino,
  planillas.planillas.cant_embarazada,
  planillas.planillas.motivo,
  planillas.planillas.usuario,
  planillas.planillas.tipo,
  planillas.planillas.id_agente_inscriptor,
  planillas.planillas.id_entrega,
  planillas.planillas.id_recibe,
  planillas.agente_inscriptor.descripcion_agente,
  planillas.entrega.descripcion_entrega,
  planillas.recibe.descripcion_recibe,
  facturacion.smiefectores.nombreefector
FROM
  planillas.agente_inscriptor
  INNER JOIN planillas.planillas ON (planillas.agente_inscriptor.id_agente_inscriptor = planillas.planillas.id_agente_inscriptor)
  INNER JOIN planillas.entrega ON (planillas.planillas.id_entrega = planillas.entrega.id_entrega)
  INNER JOIN planillas.recibe ON (planillas.planillas.id_recibe = planillas.recibe.id_recibe)
  INNER JOIN facturacion.smiefectores ON (planillas.planillas.id_efector = facturacion.smiefectores.cuie)
  where id_planillas=$id_planilla";

$res_factura=sql($query, "Error al traer el Comprobantes") or fin_pagina();

$periodo=$res_factura->fields['periodo'];
$cant_ninos=$res_factura->fields['cant_nino'];
$cant_embarazada=$res_factura->fields['cant_embarazada'];
$tipo=$res_factura->fields['tipo'];
$id_agente_inscriptor=$res_factura->fields['id_agente_inscriptor'];
$id_entrega=$res_factura->fields['id_entrega'];
$id_recibe=$res_factura->fields['id_recibe'];
$observaciones=$res_factura->fields['motivo'];
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
 if(document.all.agente_inscriptor.value=="-1"){
  alert('Debe Seleccionar un agente inscriptor');
  return false;
 }
 if(document.all.entrega.value=="-1"){
  alert('Debe Seleccionar el que entrega');
  return false;
 }
 
 if(document.all.recibe.value=="-1"){
  alert('Debe Seleccionar el que recibe');
  return false;
 }
 
 if(document.all.tipo.value=="-1"){
  alert('Debe Seleccionar un tipo');
  return false;
 }
 
 if(document.all.periodo.value=="-1"){
  alert('Debe Seleccionar un Periodo');
  return false;
 } 
 
 if(document.all.cant_ninos.value==""){
  alert('Debe Ingresar cantidad de nños');
  return false;
 } 
 
 if(document.all.cant_embarazada.value==""){
  alert('Debe Ingresar cantidad de embarazada');
  return false;
 } 
 
}//de function control_nuevos()

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

<form name='form1' action='planilla_admin.php' method='POST'>
<input type="hidden" value="<?=$id_planilla?>" name="id_planilla">
<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>
<table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
    	<?
    	if (!$id_planilla) {
    	?>  
    	<font size=+1><b>Nueva Planilla</b></font>   
    	<? }
        else {
        ?>
        <font size=+1><b>Planilla</b></font>   
        <? } ?>
       
    </td>
 </tr>
 <tr><td>
  <table width=70% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b> Descripción de la PLANILLA</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
         <tr>	           
           <td align="center" colspan="2">
            <b> Número de Planilla: <font size="+1" color="Red"><?=($id_planilla)? $id_planilla : "Nueva Planilla"?></font> </b>
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
				<?if ($id_planilla) echo "disabled"?>>
			 <option value=-1>Seleccione</option>
			 <?
			 $sql= "select * from nacer.efe_conv order by nombre";
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
         
         <td align="right">
				<b>Agente Inscriptor:</b>
			</td>
			<td align="left">			 	
			 <select name=agente_inscriptor Style="width=450px" <?if ($id_planilla) echo "disabled"?>>
			 <option value=-1>Seleccione</option>
			 <?
			 $sql= "select * from planillas.agente_inscriptor";
			 $res_efectores=sql($sql) or fin_pagina();
			 while (!$res_efectores->EOF){ 
			 	$id_agente1=$res_efectores->fields['id_agente_inscriptor'];
			    $nombre_efector=$res_efectores->fields['descripcion_agente'];
			    
			    ?>
				<option value='<?=$id_agente1?>' <?if ($id_agente==$id_agente1) echo "selected"?> ><?=$nombre_efector?></option>
			    <?
			    $res_efectores->movenext();
			    }?>
			</select>
			</td>
         </tr>
         
         <td align="right">
				<b>Entrega:</b>
			</td>
			<td align="left">			 	
			 <select name=entrega Style="width=450px" <?if ($id_planilla) echo "disabled"?>>
			 <option value=-1>Seleccione</option>
			 <?
			 $sql= "select * from planillas.entrega";
			 $res_efectores=sql($sql) or fin_pagina();
			 while (!$res_efectores->EOF){ 
			 	$id_entrega1=$res_efectores->fields['id_entrega'];
			    $nombre_efector=$res_efectores->fields['descripcion_entrega'];
			    
			    ?>
				<option value='<?=$id_entrega1?>' <?if ($id_entrega==$id_entrega1) echo "selected"?> ><?=$nombre_efector?></option>
			    <?
			    $res_efectores->movenext();
			    }?>
			</select>
			</td>
         </tr>
         
         <td align="right">
				<b>Recibe:</b>
			</td>
			<td align="left">			 	
			 <select name=recibe Style="width=450px" <?if ($id_planilla) echo "disabled"?>>
			 <option value=-1>Seleccione</option>
			 <?
			 $sql= "select * from planillas.recibe";
			 $res_efectores=sql($sql) or fin_pagina();
			 while (!$res_efectores->EOF){ 
			 	$id_recibe1=$res_efectores->fields['id_recibe'];
			    $nombre_efector=$res_efectores->fields['descripcion_recibe'];
			    
			    ?>
				<option value='<?=$id_recibe1?>' <?if ($id_recibe==$id_recibe1) echo "selected"?> ><?=$nombre_efector?></option>
			    <?
			    $res_efectores->movenext();
			    }?>
			</select>
			</td>
         </tr>
         <tr>
         	<td align="right">
				<b>Tipo:</b>
			</td>
			<td align="left">		          			
			 <select name=tipo Style="width=450px" <? if ($id_planilla) echo "disabled"?>>
			 <option value=-1>Seleccione</option>
			  <option value=Entrega <?if ($tipo=='Devolulcion') echo "selected"?>>Devolucion de fichas para CORRECCION a Inscriptor</option>
			  <option value=Recepcion <?if ($tipo=='Recepcion') echo "selected"?>>RECEPCION de Fichas Para su CARGA</option>
			  <option value=Blanco <?if ($tipo=='Blanco') echo "selected"?>>En Blanco para los CAPS</option>			  
			  <option value=Trazadoras <?if ($tipo=='Trazadoras') echo "selected"?>>Planillas de TRAZADORAS para CARGAR</option>			  
			  <option value=Trazadoras_Inmu <?if ($tipo=='Trazadoras_Inmu') echo "selected"?>>Planillas de TRAZADORAS Inmunizaciones para CARGAR</option>			  
			  <option value=Trazadoras_Par <?if ($tipo=='Trazadoras_Par') echo "selected"?>>Planillas de TRAZADORAS Partos para CARGAR</option>			  
			  </select>
			</td>
         </tr>
		<tr>
         	<td align="right">
				<b>Periodo:</b>
			</td>
			<td align="left">		          			
			 <select name=periodo Style="width=450px" <? if ($id_planilla) echo "disabled"?>>
			 <option value=-1>Seleccione</option>
			  <?
			  $sql = "select * from facturacion.periodo order by periodo";
			  $result=sql($sql,"No se puede traer el periodo");
			  while (!$result->EOF) {?>
			  			  
			  <option value=<?=$result->fields['periodo']?> <?if ($periodo==$result->fields['periodo']) echo "selected"?>><?=$result->fields['periodo']?></option>
			  <?
			  $result->movenext();
			  }
			  ?>			
			  </select>
			</td>
         </tr>
         	
         <tr>
         	<td align="right">
         	  <b>Cantidad de Niños:</b>
         	</td>         	
            <td align='left'>
              <input type="text" value="<?=$cant_ninos?>" name="cant_ninos" <? if ($id_planilla) echo "readonly"?>><font color="Red">Inmunizaciones o Partos Colocar la CANTIDAD.</font>
            </td>
         </tr>     
         
         <tr>
         	<td align="right">
         	  <b>Cantidad de Embarazada:</b>
         	</td>         	
            <td align='left'>
              <input type="text" value="<?=$cant_embarazada?>" name="cant_embarazada" <? if ($id_planilla) echo "readonly"?>>
            </td>
         </tr>              			
         			 
         <tr>
         	<td align="right">
         	  <b>Observaciones:</b>
         	</td>         	
            <td align='left'>
              <textarea cols='70' rows='7' name='observaciones' <? if ($id_planilla) echo "readonly"?>><?=$observaciones;?></textarea>
            </td>
         </tr>              
        </table>
      </td>      
     </tr> 
   

   <?if (!($id_planilla)){?>
	 
	 <tr id="mo">
  		<td align=center colspan="2">
  			<b>Guarda Planilla</b>
  		</td>
  	</tr>  
      <tr align="center">
       <td>
        <input type='submit' name='guardar' value='Guardar Planilla' onclick="return control_nuevos()"
         title="Guardar datos de la Planilla">
       </td>
      </tr>
     
     <?}?>
     
 </table>           
 
 <?if ($id_planilla){?>
<table class="bordes" align="center" width="100%">
		 <tr align="center" id="sub_tabla">
		 	<td>	
		 		Eliminar DATO
		 	</td>
		 </tr>
		 
		 <tr>
		    <td align="center">	
		    <?if ($_POST['borrar']!="Borrar"){?>
		      <input type="submit" name="borrar" value="Borrar" style="width=130px" >
		     <?}?>
		    </td>
		 </tr> 
	 </table>	
	 <br>
	 <?}?>
	 
 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
     <input type=button name="volver" value="Volver" onclick="document.location='planilla_listado.php'"title="Volver al Listado" style="width=150px">     
   </td>
  </tr>
 </table></td></tr>
 
 
 </table>
 </form>
 
 <?=fin_pagina();// aca termino ?>
