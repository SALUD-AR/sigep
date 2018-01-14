<?
/*
Author: ferni

modificada por
$Author: ferni $
$Revision: 1.42 $
$Date: 2006/05/23 13:53:00 $
*/

require_once ("../../config.php");


extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

if ($_POST['guardar_editar']=="Guardar"){
   $db->StartTrans();
      
   $fecha_defuncion=Fecha_db($fecha_defuncion);      
   $fecha_auditoria=Fecha_db($fecha_auditoria); 
   if ($fecha_par_int!="")$fecha_par_int=Fecha_db($fecha_par_int);
   else $fecha_par_int="1980-01-01";  
   if ($fecha_nac!="")$fecha_nac=Fecha_db($fecha_nac);
   else $fecha_nac="1980-01-01";
   
   $fecha_carga=date("Y-m-d H:m:s");
   $usuario=$_ses_user['name'];
   
   $query="update trazadoras.mu set 
             cuie='$cuie',                           
             tipo_doc='$tipo_doc', 
             num_doc='$num_doc',
             apellido='$apellido',
             nombre='$nombre',
             fecha_defuncion='$fecha_defuncion',
  			 fecha_auditoria='$fecha_auditoria',
  			 fecha_par_int='$fecha_par_int',
  			 fecha_nac='$fecha_nac',         
             observaciones='$observaciones',
             fecha_carga='$fecha_carga',
             usuario='$usuario',
             clase_doc='$clase_doc'            
             
             where id_mu=$id_planilla";

   sql($query, "Error al insertar/actualizar el muleto") or fin_pagina();
    
    
	 
    $db->CompleteTrans();    
   $accion="Los datos se actualizaron";  
}

if ($_POST['guardar']=="Guardar Planilla"){
   $fecha_carga=date("Y-m-d H:m:s");
   $usuario=$_ses_user['name'];
   $db->StartTrans();         
    
   $q="select nextval('trazadoras.mu_id_mu_seq') as id_planilla";
    $id_planilla=sql($q) or fin_pagina();
    $id_planilla=$id_planilla->fields['id_planilla'];
   
   $fecha_defuncion=Fecha_db($fecha_defuncion);      
   $fecha_auditoria=Fecha_db($fecha_auditoria); 
   if ($fecha_par_int!="")$fecha_par_int=Fecha_db($fecha_par_int);
   else $fecha_par_int="1980-01-01";  
   if ($fecha_nac!="")$fecha_nac=Fecha_db($fecha_nac);
   else $fecha_nac="1980-01-01";     
           
    $query="insert into trazadoras.mu
             (id_mu,cuie,tipo_doc,num_doc,apellido,nombre,fecha_defuncion,
  				fecha_auditoria,fecha_par_int,fecha_nac,observaciones,fecha_carga,usuario,clase_doc)
             values
             ('$id_planilla','$cuie','$tipo_doc','$num_doc','$apellido',
             '$nombre','$fecha_defuncion','$fecha_auditoria','$fecha_par_int',
  			 '$fecha_nac','$observaciones','$fecha_carga','$usuario','$clase_doc')";

    sql($query, "Error al insertar la Planilla") or fin_pagina();
    
    $accion="Se guardo la Planilla";    
	 
    $db->CompleteTrans();    
}//de if ($_POST['guardar']=="Guardar nuevo Muleto")

if ($_POST['borrar']=="Borrar"){
	$query="delete from trazadoras.mu
			where id_mu=$id_planilla";
	sql($query, "Error al insertar la Planilla") or fin_pagina();
	$accion="Se elimino la planilla $id_planilla de Muertes"; 	
}

if ($id_planilla) {
$query="SELECT 
  *
FROM
  trazadoras.mu  
  where id_mu=$id_planilla";

$res_factura=sql($query, "Error al traer el Comprobantes") or fin_pagina();

$cuie=$res_factura->fields['cuie'];
$tipo_doc=$res_factura->fields['tipo_doc'];
$num_doc=number_format($res_factura->fields['num_doc'],0,'.','');
$apellido=$res_factura->fields['apellido'];
$nombre=$res_factura->fields['nombre'];
$fecha_defuncion=$res_factura->fields['fecha_defuncion'];
$fecha_auditoria=$res_factura->fields['fecha_auditoria'];
$fecha_par_int=$res_factura->fields['fecha_par_int'];
$fecha_nac=$res_factura->fields['fecha_nac'];
$observaciones=$res_factura->fields['observaciones'];
$fecha_carga=$res_factura->fields['fecha_carga'];
$usuario=$res_factura->fields['usuario'];
$clase_doc=$res_factura->fields['clase_doc'];
}
echo $html_header;
?>
<script>
//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos()
{ 
 if(document.all.cuie.value=="-1"){
  alert('Debe Seleccionar un Efector');
  return false;
 } 
 if(document.all.tipo_doc.value=="-1"){
  alert('Debe Seleccionar un Tipo de Documento');
  return false; 
 }   
 if(document.all.num_doc.value==""){
  alert('Debe Ingresar un Documento');
  return false;
 }
 if(document.all.apellido.value==""){
  alert('Debe Ingresar un apellido');
  return false;
 } 
 if(document.all.nombre.value==""){
  alert('Debe Ingresar un nombre');
  return false;
 } 
 if(document.all.fecha_defuncion.value==""){
  alert('Debe Ingresar una Fecha de Defuncion');
  return false;
 }
 if(document.all.fecha_auditoria.value==""){
  alert('Debe Ingresar una Fecha de Auditoria');
  return false;
 }
  if(document.all.observaciones.value==""){
  alert('Debe Ingresar en Observaciones el motivo del Ovito ');
  return false;
 }
  
}//de function control_nuevos()

function editar_campos()
{	
	document.all.cuie.disabled=false;	
	document.all.tipo_doc.disabled=false;
	document.all.num_doc.readOnly=false;
	document.all.apellido.readOnly=false;
	document.all.nombre.readOnly=false;	
	document.all.observaciones.readOnly=false;
	
	document.all.cancelar_editar.disabled=false;
	document.all.guardar_editar.disabled=false;
	document.all.editar.disabled=true;
 	return true;
}//de function control_nuevos()


</script>

<form name='form1' action='mu_admin.php' method='POST'>
<input type="hidden" value="<?=$id_planilla?>" name="id_planilla">
<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>
<table width="85%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
    	<?
    	if (!$id_planilla) {
    	?>  
    	<font size=+1><b>Nuevo Dato</b></font>   
    	<? }
        else {
        ?>
        <font size=+1><b>Dato</b></font>   
        <? } ?>
       
    </td>
 </tr>
 <tr><td>
  <table width=90% align="center" class="bordes">
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
            <b> Número del Dato: <font size="+1" color="Red"><?=($id_planilla)? $id_planilla : "Nuevo Dato"?></font> </b>
           </td>
         </tr>
         <tr>	           
           <td align="center" colspan="2">
             <b><font size="2" color="Red">Nota: Los valores numericos se ingresan SIN separadores de miles, y con "." como separador DECIMAL</font> </b>
           </td>
         </tr>
         
         </tr>
         
         <tr>
         	<td align="right">
				<b>Efector:</b>
			</td>
			<td align="left">			 	
			 <select name=cuie Style="width=257px" <?if ($id_planilla) echo "disabled"?>>
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
				<b>Clase de Documento:</b>
			</td>
			<td align="left">			 	
			 <select name=clase_doc Style="width=257px" <?if ($id_planilla) echo "disabled"?>>
			  <option value=-1>Seleccione</option>
			  <option value=R <?if ($clase_doc=='R') echo "selected"?>>Propio</option>
			  <option value=M <?if ($clase_doc=='M') echo "selected"?>>Madre</option>
			  <option value=P <?if ($clase_doc=='P') echo "selected"?>>Padre</option>
			  <option value=T <?if ($clase_doc=='T') echo "selected"?>>Tutor</option>
			 </select>
			</td>
         </tr>
                          
         <td align="right">
				<b>Tipo de Documento:</b>
			</td>
			<td align="left">			 	
			 <select name=tipo_doc Style="width=257px" <?if ($id_planilla) echo "disabled"?>>
			  <option value=-1>Seleccione</option>
			  <option value=DNI <?if ($tipo_doc=='DNI') echo "selected"?>>Documento Nacional de Identidad</option>
			  <option value=LE <?if ($tipo_doc=='LE') echo "selected"?>>Libreta de Enrolamiento</option>
			  <option value=LC <?if ($tipo_doc=='LC') echo "selected"?>>Libreta Civica</option>
			  <option value=PA <?if ($tipo_doc=='PA') echo "selected"?>>Pasaporte Argentino</option>
			  <option value=CM <?if ($tipo_doc=='CM') echo "selected"?>>Certificado Migratmuo</option>
			 </select>
			</td>
         </tr>
         
         <tr>
         	<td align="right">
         	  <b>Número de Documento:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$num_doc?>" name="num_doc" <? if ($id_planilla) echo "readonly"?>><font color="Red">Sin Puntos</font>
            </td>
         </tr> 
         
         <tr>
         	<td align="right">
         	  <b>Apellido:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$apellido?>" name="apellido" <? if ($id_planilla) echo "readonly"?>>
            </td>
         </tr> 
         
         <tr>
         	<td align="right">
         	  <b>Nombre:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$nombre?>" name="nombre" <? if ($id_planilla) echo "readonly"?>>
            </td>
         </tr>                        
        <tr>
			<td align="right">
				<b>Fecha de Defuncion:</b>
			</td>
		    <td align="left">
		    	<?$fecha_comprobante=date("d/m/Y");?>
		    	 <input type=text id=fecha_defuncion name=fecha_defuncion value='<?=fecha($fecha_defuncion);?>' size=15 readonly>
		    	 <?=link_calendario("fecha_defuncion");?>					    	 
		    </td>		    
		</tr>	
		<tr>
			<td align="right">
				<b>Fecha de Auditoria de la Muerte:</b>
			</td>
		    <td align="left">
		    	<?$fecha_comprobante=date("d/m/Y");?>
		    	 <input type=text id=fecha_auditoria name=fecha_auditoria value='<?=fecha($fecha_auditoria);?>' size=15 readonly>
		    	 <?=link_calendario("fecha_auditoria");?>					    	 
		    </td>		    
		</tr>				
		<tr>
			<td align="right">
				<b>Fecha de Parto o Interrupcion del Embarazo:</b>
			</td>
		    <td align="left">
		    	<?$fecha_comprobante=date("d/m/Y");?>
		    	 <input type=text id=fecha_par_int name=fecha_par_int value='<?=fecha($fecha_par_int);?>' size=15 readonly>
		    	 <?=link_calendario("fecha_par_int");?>					    	 
		    </td>		    
		</tr>
		<tr>
			<td align="right">
				<b>Fecha de Nacimiento:</b>
			</td>
		    <td align="left">
		    	<?$fecha_comprobante=date("d/m/Y");?>
		    	 <input type=text id=fecha_nac name=fecha_nac value='<?=fecha($fecha_nac);?>' size=15 readonly>
		    	 <?=link_calendario("fecha_nac");?>					    	 
		    </td>		    
		</tr>
		
         <tr>
         	<td align="right">
         	  <b>Observaciones:</b>
         	</td>         	
            <td align='left'>
              <textarea cols='40' rows='4' name='observaciones' <? if ($id_planilla) echo "readonly"?>><?=$observaciones;?></textarea>
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
<br>
<?if ($id_planilla){?>
<table class="bordes" align="center" width="100%">
		 <tr align="center" id="sub_tabla">
		 	<td>	
		 		Editar DATO
		 	</td>
		 </tr>
		 
		 <tr>
		    <td align="center">
		      <input type=button name="editar" value="Editar" onclick="editar_campos()" title="Edita Campos" style="width=130px"> &nbsp;&nbsp;
		      <input type="submit" name="guardar_editar" value="Guardar" title="Guarda Muleto" disabled style="width=130px" onclick="return control_nuevos()">&nbsp;&nbsp;
		      <input type="button" name="cancelar_editar" value="Cancelar" title="Cancela Edicion de Muletos" disabled style="width=130px" onclick="document.location.reload()">		      
		      <?if (permisos_check("inicio","permiso_borrar")) $permiso="";
			  else $permiso="disabled";?>
		      <input type="submit" name="borrar" value="Borrar" style="width=130px" <?=$permiso?>>
		    </td>
		 </tr> 
	 </table>	
	 <br>
	 <?}?>
 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
     <input type=button name="volver" value="Volver" onclick="document.location='mu_listado.php'"title="Volver al Listado" style="width=150px">     
   </td>
  </tr>
 </table></td></tr>
 
 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
   	<font color="Black" size="3"> <b>En esta pantalla se miden 1 (UNA) TRAZADORAS y los datos minimos a cargar por Trazadora son:</b></font>
   </td>
  </tr>
  <tr align="left">
   <td>
   	<font size="2">Trazadora V (Defunciones): Todos los campos. "Fecha de Parto o Interrupcion del Embarazo" o "Fecha de Nacimiento".</font>
   </td>
  </tr>  
 </table></td></tr>
 
 </table>
 </form>
 
 <?=fin_pagina();// aca termino ?>