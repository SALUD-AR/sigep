<?
/*
$Author: seba $
$Revision: 1.00 $
$Date: 2011/05/31 19:12:40 $
*/

require_once ("../../config.php");


extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

if ($_POST['guardar_editar']=="Guardar"){
   $fecha_carga=date("Y-m-d");
   $usuario=$_ses_user['name'];
   $db->StartTrans();   
   $fecha_control=Fecha_db($_POST['fecha_control']); 
   $fum=Fecha_db($_POST['fum']);
   $fpp=Fecha_db($_POST['fpp']);
   $edad_gestacional=$_POST['edad_gestacional']; 
   $comentario=$_POST['observaciones']; 
   $peso=$_POST['peso'];
   $tension_arterial_M=$_POST['tension_arterial_M'];
   $tension_arterial_m=$_POST['tension_arterial_m'];
   $maxima=str_pad($tension_arterial_M,3,"0",STR_PAD_LEFT);
   $minima=str_pad($tension_arterial_m,3,"0",STR_PAD_LEFT);
   $tension_arterial="$maxima"."/"."$minima";
   if ($entidad_alta=='na') $id_beneficiarios=0;
   else $id_smiafiliados=0;   
   
   $query_1="update trazadorassps.trazadora_1 set 
             cuie='$cuie',
             id_smiafiliados='$id_smiafiliados',
             id_beneficiarios='$id_beneficiarios',
             fecha_control_prenatal='$fecha_ctrl_prenatal',
			       fum='$fum',
			       fpp='$fpp',
             edad_gestacional='$edad_gestacional',
             fecha_carga='$fecha_carga',
             usuario='$usuario',
             comentario='$comentario'            
             
             where id_trz1=$id_planilla";

   sql($query, "Error al insertar/actualizar el muleto") or fin_pagina();
   
   $query_2="update trazadorassps.trazadora_2 set 
             cuie='$cuie',
             id_smiafiliados='$id_smiafiliados',
             id_beneficiarios='$id_beneficiarios',
             fecha_control_prenatal='$fecha_ctrl_prenatal',
             edad_gestacional='$edad_gestacional',
             peso='$peso',
             tension_arterial='$tension_arterial',
             fecha_carga='$fecha_carga',
             usuario='$usuario',
             comentario='$comentario'            
             
             where id_trz2=$id_planilla";

   sql($query, "Error al insertar/actualizar el muleto") or fin_pagina();
    
    
	 
    $db->CompleteTrans();    
   $accion="Los datos se actualizaron";  
   
   //valida si esta captado
    $q="select * from nacer.smiafiliados where afidni='$num_doc'";
    $res_captado=sql($q) or fin_pagina();
    if ($res_captado->RecordCount()==0)
    {
    	$accion2="La Persona NO esta Captada por el SUMAR";
    }
    else
    {
    	$accion2="";
    }
}

if ($_POST['guardar']=="Guardar Planilla"){
   $fecha_carga=date("Y-m-d");
   $usuario=$_ses_user['name'];
   $db->StartTrans();   
   $fecha_control=Fecha_db($_POST['fecha_control']);
   $fum=Fecha_db($_POST['fum']);   
   $fpp=Fecha_db($_POST['fpp']);
   $edad_gestacional=$_POST['edad_gestacional']; 
   $comentario=$_POST['observaciones']; 
   $peso=$_POST['peso'];
   
   $tension_arterial_M=$_POST['tension_arterial_M'];
   $tension_arterial_m=$_POST['tension_arterial_m'];
   $maxima=str_pad($tension_arterial_M,3,"0",STR_PAD_LEFT);
   $minima=str_pad($tension_arterial_m,3,"0",STR_PAD_LEFT);
   $tension_arterial="$maxima"."/"."$minima";    
    
   if ($entidad_alta=='na'){
   $sql_cons="select * from trazadorassps.trazadora_1 where id_smiafiliados=$id_smiafiliados";
   $res_sql=sql($sql_cons,"no se pudo ejecutar la consulta sobre embarazada");
   $id_beneficarios=0;
    }
    else {
      $sql_cons="select * from trazadorassps.trazadora_1 where id_beneficiarios=$id_beneficiarios";
      $res_sql=sql($sql_cons,"no se pudo ejecutar la consulta sobre embarazada");
      $id_smiafiliados=0;

    }
   
   if ($res_sql->RecordCount()==0){
   
   	$q="select nextval('trazadorassps.seq_id_trz1') as id_planilla";
   $id_planilla=sql($q) or fin_pagina();
   $id_planilla=$id_planilla->fields['id_planilla'];
      
    
   	$query="insert into trazadorassps.trazadora_1	
             (id_trz1,cuie,id_smiafiliados,fecha_control_prenatal,fum,fpp,edad_gestacional
             ,fecha_carga,usuario,comentario,id_beneficiarios)
             values
             ('$id_planilla','$cuie','$id_smiafiliados','$fecha_control','$fum','$fpp',
             '$edad_gestacional','$fecha_carga','$usuario','$comentario',$id_beneficarios)";

    sql($query, "Error al insertar la Planilla") or fin_pagina();
    
    $q="select nextval('trazadorassps.seq_id_trz2') as id_planilla";
   $id_planilla=sql($q) or fin_pagina();
   $id_planilla=$id_planilla->fields['id_planilla'];
      
   $id_beneficarios=0;
   	   $query="insert into trazadorassps.trazadora_2	
             (id_trz2,cuie,id_smiafiliados,fecha_control,edad_gestacional
             ,peso,tension_arterial,fecha_carga,usuario,comentario,id_beneficiarios)
             values
             ('$id_planilla','$cuie','$id_smiafiliados','$fecha_control',
             '$edad_gestacional','$peso','$tension_arterial','$fecha_carga','$usuario','$comentario',$id_beneficarios)";

   	 sql($query, "Error al insertar la Planilla") or fin_pagina();
    
   	 $accion="Se guardo la Planilla";    
	 
   	 $db->CompleteTrans();    
    
     echo "<script>window.close()</script>"; 
   }
   
   else {
   
      $q="select nextval('trazadorassps.seq_id_trz2') as id_planilla";
   $id_planilla=sql($q) or fin_pagina();
   $id_planilla=$id_planilla->fields['id_planilla'];
      
   $query="insert into trazadorassps.trazadora_2	
             (id_trz2,cuie,id_smiafiliados,fecha_control,edad_gestacional
             ,peso,tension_arterial,fecha_carga,usuario,comentario,id_beneficiarios)
             values
             ('$id_planilla','$cuie','$id_smiafiliados','$fecha_control',
             '$edad_gestacional','$peso','$tension_arterial','$fecha_carga','$usuario','$comentario',$id_beneficarios)";

   	 sql($query, "Error al insertar la Planilla") or fin_pagina();
    
   	 $accion="Se guardo la Planilla";    
	 
   	 $db->CompleteTrans(); 
    
     echo "<script>window.close()</script>";  
   }
}//de if ($_POST['guardar']=="Guardar nuevo Muleto")

if (($pagina=='prestacion_admin.php')&&($pagina_viene!="comprobante_admin_total.php")){
	
	if ($entidad_alta=="na") {
  $sql="select * from nacer.smiafiliados	  
	 where id_smiafiliados=$id_smiafiliados";
	$res_extra=sql($sql, "Error al traer el beneficiario") or fin_pagina();
	
	$clave=$res_extra->fields['clavebeneficiario'];
	$tipo_doc=$res_extra->fields['afitipodoc'];
	$num_doc=number_format($res_extra->fields['afidni'],0,'.','');
	$apellido=$res_extra->fields['afiapellido'];
	$nombre=$res_extra->fields['afinombre'];
	
	$fecha_control=$fecha_comprobante;
	$fpcp=$fecha_comprobante;
}
else {
  $sql="select * from leche.beneficiarios where id_beneficiarios=$id_beneficiarios";
  $res_extra=sql($sql, "Error al traer los datos leche.beneficiarios") or fin_pagina();
  
  $tipo_doc="DNI";
  $num_doc=$res_extra->fields['documento'];
  $apellido=$res_extra->fields['apellido'];
  $nombre=$res_extra->fields['nombre'];
  
  $fecha_control=$fecha_comprobante;
  $fpcp=$fecha_comprobante;
  }
}

if ($id_planilla) {
  if ($entidad_alta=="na") {
    $sql="select * from nacer.smiafiliados
	   left join nacer.efe_conv on (cuieefectorasignado=cuie)
	   where id_smiafiliados=$id_smiafiliados";
    $res_comprobante=sql($sql, "Error al traer los Comprobantes") or fin_pagina();
    
    $apellido=trim($res_comprobante->fields['afiapellido']);
	 $nombre=trim($res_comprobante->fields['afinombre']);
	$num_doc=number_format($res_comprobante->fields['afidni'],0,'.','');
	$localidad=$res_comprobante->fields['afidomlocalidad'];
	$fecha_nac=$res_comprobante->fields['afifechanac'];
	$sexo=$res_comprobante->fields['afisexo'];
  }
  else {
    $sql="select * from leche.beneficiarios where id_beneficiarios=$id_beneficiarios";
    $res_comprobante=sql($sql, "Error al traer los datos leche.beneficiarios") or fin_pagina();
    
  $apellido=trim($res_comprobante->fields['apellido']);
  $nombre=trim($res_comprobante->fields['nombre']);
  $num_doc=$res_comprobante->fields['documento'];
  $fecha_nac=$res_comprobante->fields['fecha_nac'];
  $sexo=$res_comprobante->fields['sexo'];
  }
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
 if(document.all.fecha_control.value==""){
	  alert('Debe Seleccionar la Fecha del Control');
	  return false;
	 }
if(document.all.fum.value==""){
	  alert('Debe Seleccionar la Fecha del Control');
	  return false;
	 }
if(document.all.fpp.value==""){
	  alert('Debe Seleccionar la Fecha del Control');
	  return false;
	 }
 if(document.all.edad_gestacional.value==""){
		  alert('Debe Ingresar la Edad Gestacional');
		  return false;
		 }else{
 		var edad_gestacional=document.all.edad_gestacional.value;
		if(isNaN(edad_gestacional)){
			alert('El dato de la Edad Gestacional debe ser un Numero Entero');
			document.all.edad_gestacional.focus();
			return false;
		}
	}
if (document.all.peso.value==""){ 
		alert('Debe ingresar el Peso');
		document.all.peso.focus();
		return false;
		}
	
	if (isNaN(document.all.peso.value)) {
		alert('El dato del Peso debe ser un Numero Real');
		document.all.peso.focus();
		return false;
		}
	
	if (document.all.peso.value>300) {
		alert ('El Peso debe ser menor de 300 Kg');
		document.all.peso.focus();
		return false;
		}
		
	if (document.all.peso.value<0) {
		alert ('El Peso debe ser mayor de 0 Kg');
		document.all.peso.focus();
		return false;
		}
		
if(document.all.tension_arterial_M.value==""){
	 alert("Debe completar el campo Tension Arterial MAXIMA");
	 document.all.tension_arterial_M.focus();
	 return false;
	 }else{
 		var tension_arterial_M=document.all.tension_arterial_M.value;
		if(isNaN(tension_arterial_M)){
			alert('El dato de la tension Arterial MAXIMA debe ser un Numero Entero');
			document.all.tension_arterial_M.focus();
			return false;
		}
	}
	
	if(document.all.tension_arterial_m.value==""){
	 alert("Debe completar el campo de Tension Arterial MINIMA");
	 document.all.tension_arterial_m.focus();
	 return false;
	 }else{
 		var tension_arterial_m=document.all.tension_arterial_m.value;
		if(isNaN(tension_arterial_m)){
			alert('El dato de la tension Arterial MINIMA debe ser un Numero Entero');
			document.all.tension_arterial_m.focus();
			return false;
		}
	}

 
}//de function control_nuevos()

function editar_campos()
{	
	document.all.cuie.disabled=false;	
	document.all.tipo_doc.disabled=false;
	document.all.num_doc.readOnly=false;
	document.all.apellido.readOnly=false;
	document.all.nombre.readOnly=false;
	document.all.sem_gestacion.readOnly=false;	
	document.all.observaciones.readOnly=false;
	document.all.fecha_control.readOnly=false;
	document.all.fum.readOnly=false;
	document.all.fpp.readOnly=false;
	document.all.fpcp.readOnly=false;
		
	document.all.cancelar_editar.disabled=false;
	document.all.guardar_editar.disabled=false;
	document.all.editar.disabled=true;
 	return true;
}//de function control_nuevos()

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

<form name='form1' action='trazadora_1_2.php' method='POST'>
<input type="hidden" value="<?=$id_planilla?>" name="id_planilla">
<input type="hidden" value="<?=$pagina?>" name="pagina">
<input type="hidden" value="<?=$id_smiafiliados?>" name="id_smiafiliados">

<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>
<?echo "<center><b><font size='+1' color='Blue'>$accion2</font></b></center>";?>
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
       
    v3.3</td>
 </tr>
 <tr><td>
  <table width=90% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b> Descripción de la PLANILLA</b></td>
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
         
         <tr>
         	<td align="right">
         	  <b>Número de Documento:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$num_doc?>" name="num_doc" <? if ($id_planilla) echo "readonly"?>>
              </td>
         </tr> 
         
         <tr>
         	<td align="right">
				<b>Efector:</b>
			</td>
			<td align="left">			 	
			 <select name=cuie Style="width=257px" 
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer();"
				onchange="borrar_buffer();" 
				<?if ($id_planilla) echo "disabled"?> >
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
         
         <tr>
         	<td align="right">
         	  <b>Clave Beneficiario:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$clave?>" name="clave" <? if ($id_planilla) echo "readonly"?>> 
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
			  <option value=CM <?if ($tipo_doc=='CM') echo "selected"?>>Certificado Migratorio</option>
			 </select>
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
				<b>Fecha del control :</b>
			</td>
		    <td align="left">
		    	<?$fecha_control=date("d/m/Y");?>
		    	 <input type=text id=fecha_control name=fecha_control value='<?=$fecha_control;?>' size=15 >
		    	 <?=link_calendario("fecha_control");?>					    	 
		    </td>		    
		</tr>
		<tr>
           <td align="right">
         	  <b> Edad gestacional:
         	</td> 
           <td >
             <input type='text' name='edad_gestacional' value='<?=$edad_gestacional;?>' size=60 align='right'></b>
           </td>
          </tr>	
			<tr>
			<td align="right">
				<b>Fecha de Ultima Mestruacion (FUM) :</b>
			</td>
		    <td align="left">
		    	 <input type=text id=fum name=fum value='<?=$fum;?>' size=15 >
		    	 <?=link_calendario("fum");?>					    	 
		    </td>		    
		</tr>
		<tr>
			<td align="right">
				<b>Fecha Probable de Parto (FPP) :</b>
			</td>
		    <td align="left">
		    	 <input type=text id=fpp name=fpp value='<?=$fpp;?>' size=15 >
		    	 <?=link_calendario("fpp");?>					    	 
		    </td>		    
		</tr>
		 <tr>
         <tr>
           <td align="right">
         	  <b> Peso:
         	</td> 
           <td >
             <input type='text' name='peso' value='<?=$peso;?>' size=60 align='right'></b></b><font color="Red">Kg (Kilogramos)</font>
           </td>
          </tr>	
          <tr>
           <td align="right">
         	  <b> Tensión Arterial	<font color="Red">		MAXIMA:</b></font>
         	</td> 
           <td >
             <input type='number' name='tension_arterial_M' value='<?=$tension_arterial_M;?>' size=10 align='right'>	<b><font color="Red">/ MINIMA</font></b> <input type='number' name='tension_arterial_m' value='<?=$tension_arterial_m;?>' size=10 align='right'></b>
			<font color="Red">Los Valores son Numeros Enteros</font>
		   </td>
          </tr>		
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
   	<font color="Black" size="3"> <b>En esta pantalla se miden 2 (DOS) TRAZADORAS y los datos minimos a cargar por Trazadora son:</b></font>
   </td>
  </tr>
  <tr align="left">
   <td>
   	<font size="2">Trazadora I (ATENCIÓN TEMPRANA DE EMBARAZO): Campos obligatorios son todos menos Observaciones.</font>
   	<font size="2">Trazadora II (SEGUIMIENTO DE EMBARAZO): Campos obligatorios son todos menos Observaciones.</font>
   </td>
  </tr>
  
 </table></td></tr>
 
 </table>
 </form>
 
 <?=fin_pagina();// aca termino ?>