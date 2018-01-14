<?
require_once ("../../config.php");
//include_once('lib_inscripcion.php');

Header('Content-Type: text/html; charset=LATIN1');

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();


$usuario1=$_ses_user['id'];

// Alta Beneficiarios
if ($_POST['guardar']=="Guardar"){
		
   $db->StartTrans();
  
   $dni=$_POST['num_doc'];
   $nombre=$_POST['nombre'];
   $apellido=$_POST['apellido'];
   $propio=$_POST['clase_doc'];
   $fecha_carga=date("Y-m-d H:m:s");
   $usuario=$_ses_user['login'];
   $fechanacimiento=Fecha_db($fecha_nac);
   $sexo=$_POST['sexo'];
   $id_pais=$_POST['id_pais'];
   $id_provincia=$_POST['id_provincia'];
   $id_departamento=$_POST['id_departamento'];
   
   	$sql_benef="select nextval('cardiopatia.seq_id_beneficiario') as id_beneficiario";
	$sql_id_benef=sql($sql_benef) or die();
	$id_beneficiario=$sql_id_benef->fields['id_beneficiario'];
	
   
   $sql_alta="INSERT INTO cardiopatia.beneficiario (id_beneficiario,dni,nombre,apellido,propio,fechanacimiento,sexo,pais,provincia,departamento,tipodocumento)
				VALUES ($id_beneficiario,$dni,'$nombre','$apellido','$propio','$fechanacimiento','$sexo',$id_pais,$id_provincia,$id_departamento,'$clase_doc')";
 	$sql_proc=sql($sql_alta,"Error al insertar el alta") or die();
 	
	//codigo de insercion
  		 
      
	 
   $db->CompleteTrans();    
   
		
} //FIN alta

echo $html_header;?>
<script>

// Validar Fechas
function esFechaValida(fecha){
    if (fecha != undefined && fecha.value != "" ){
        if (!/^\d{2}\/\d{2}\/\d{4}$/.test(fecha.value)){
            alert("formato de fecha no válido (dd/mm/aaaa)");
            return false;
        }
        var dia  =  parseInt(fecha.value.substring(0,2),10);
        var mes  =  parseInt(fecha.value.substring(3,5),10);
        var anio =  parseInt(fecha.value.substring(6),10);
 
    switch(mes){
        case 1:
        case 3:
        case 5:
        case 7:
        case 8:
        case 10:
        case 12:
            numDias=31;
            break;
        case 4: case 6: case 9: case 11:
            numDias=30;
            break;
        case 2:
            if (comprobarSiBisisesto(anio)){ numDias=29 }else{ numDias=28};
            break;
        default:
            alert("Fecha introducida errónea");
            return false;
    }
 
        if (dia>numDias || dia==0){
            alert("Fecha introducida errónea");
            return false;
        }
        return true;
    }
}
 
function comprobarSiBisisesto(anio){
if ( ( anio % 100 != 0) && ((anio % 4 == 0) || (anio % 400 == 0))) {
    return true;
    }
else {
    return false;
    }
}

// Funcion para verificar que el DNI no tenga espacios en blanco
function CheckUserName(ele) { 
	if (/\s/.test(ele.value)) { 
		alert("No se permiten espacios en blanco");
		document.all.num_doc.focus(); } 
	} 

//controlan que ingresen todos los datos necesarios para el beneficiario
function control_nuevos()
{
 if(document.all.num_doc.value==""){
	 alert("Debe completar el campo numero de documento");
	 document.all.num_doc.focus();
	 return false;
	 }else{
 		var num_doc=document.all.num_doc.value;
		if(isNaN(num_doc)){
			alert('El dato ingresado en numero de documento debe ser entero y no contener espacios');
			document.all.num_doc.focus();
			return false;
	 	}
	 }

	
 if(document.all.apellido.value==""){
	 alert("Debe completar el campo apellido");
	 document.all.apellido.focus();
	 return false;
 }else{
	 var charpos = document.all.apellido.value.search("/[^A-Za-z\s]/"); 
	   if( charpos >= 0) 
	    { 
	     alert( "El campo Apellido solo permite letras "); 
	     document.all.apellido.focus();
	     return false;
	    }
	 }	
 

 if(document.all.nombre.value==""){
	 alert("Debe completar el campo nombre");
	 document.all.nombre.focus();
	 return false;
	 }else{
		 var charpos = document.all.nombre.value.search("/[^A-Za-z\s]/"); 
		   if( charpos >= 0) 
		    { 
		     alert( "El campo Nombre solo permite letras "); 
		     document.all.nombre.focus();
		     return false;
		    }
		 }		
	
 if(document.all.clase_doc.value=="-1"){
		alert("Debe completar el campo clase de documento");
		document.all.clase_doc.focus();
		 return false;
	 }

 if(document.all.tipo_doc.value=="-1"){
		alert("Debe completar el campo tipo de documento");
		document.all.tipo_doc.focus();
		 return false;
	 }



 if(document.all.sexo.value=="-1"){
			alert("Debe completar el campo sexo");
			document.all.sexo.focus();
			 return false;
		 } 
		 

 var docu=document.all.clase_doc.value;
		if(docu!='P'){
			var num1=document.all.nro_doc_madre.value;
			var num2=document.all.num_doc.value;
			if (num1 != num2){
				alert("Los numeros de documento deben coincidir");
				document.all.num_doc.focus();
				return false;
			}
		}
	

  if(document.all.fecha_nac.value==""){
		alert("Debe completar el campo fecha de nacimiento");
		 return false;
		 }
}
//de function control_nuevos()

/**********************************************************/
</script>

<?//<form name='form1' action='beneficiario.php' accept-charset="latin1" method='POST'>?>
<form name='form1' action='beneficiario.php' accept-charset="latin1" method='POST' enctype='multipart/form-data'>
<input type="hidden" value="<?=$usuario1?>" name="usuario1">
<input type="hidden" name="id_pais" value="<?=$id_pais?>">
<input type="hidden" name="id_provincia" value="<?=$id_provincia?>">
<input type="hidden" name="id_departamento" value="<?=$id_departamento?>">


<?$id_pais_default=4;
  $id_provinc_default=12;
  $id_depart_default=18;
?>


<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>
<?echo "<center><b><font size='+1' color='Blue'>$accion2</font></b></center>";?>
<table width="97%" cellspacing=0 border="1" bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
    	<?
    	if (!$id_beneficiario) {
    	?>  
    	<font size=+1><b>Nuevo Formulario</b></font>   
    	<? }
        else {
        ?>
        <font size=+1><b>Beneficiario Numero: <?=$id_beneficiario?></b></font>   
        <? } ?>
       
    </td>
 </tr>
 <tr><td>
  <table width=100% align="center" class="bordes">
      <tr>     
       <td>
        <table class="bordes" align="center">                          
         <tr>	           
           
         </tr>
         
         <tr>	           
           <td align="right" colspan="4">
             
           </td>
         </tr>
         
         <tr>
         	<td align="right" width="20%">
         	  <b>Número de Documento:</b>
         	</td>         	
            <td align='left' width="30%">
              <input type="text" size="30" value="<?=$num_doc?>" name="num_doc" onblur="CheckUserName(this);" >
             </td>
            
                       
         </tr>
         
         <tr>
         	<td align="right">
         	  <b>Apellido:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="30" value="<?=$apellido?>" name="apellido" onkeypress="return pulsar(event)" onblur="this.value=this.value.toUpperCase();" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
            </td>
           	<td align="right">
         	  <b>Nombre:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="30" value="<?=$nombre?>" name="nombre" onkeypress="return pulsar(event)" onblur="this.value=this.value.toUpperCase();" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
            </td>
         </tr> 
         
		<tr>
            <td align="right">
				<b>Clase de Documento:</b>
			</td>
			<td align="left">			 	
			 <select name=clase_doc Style="width=200px" onkeypress="return pulsar(event)">
			  <option value=-1>Seleccione</option>
			  <option value=P <?if ($clase_doc=='P') echo "selected"?>>Propio</option>
			  <option value=A <?if ($clase_doc=='A') echo "selected"?>>Ajeno</option>
			  </select>
			</td> 
         	<td align="right">
				<b>Tipo de Documento:</b>
			</td>
			<td align="left">			 	
			 <select name=tipo_doc Style="width=210px" onkeypress="return pulsar(event)">
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
           <td align="center" colspan="4" id="ma">
            <b> Datos de Nacimiento </b>
           </td>
         </tr>
         
         <tr>
         	<td align="right">
				<b>Sexo:</b>
			</td>
			<td align="left">			 	
			<select name=sexo Style="width=200px"  onkeypress="return pulsar(event)">
			  <option value=-1>Seleccione</option>
			  <option value=F <?if ($sexo=='F') echo "selected"?>>Femenino</option>
			  <option value=M <?if ($sexo=='M') echo "selected"?>>Masculino</option>
			  </select>
			  
			 
			</td> 
         	<td align="right">
				<b>Fecha de Nacimiento:</b>
			</td>
		    <td align="left">
		    	<input type=text name=fecha_nac id=fecha_nac onblur="esFechaValida(this);" value='<?=$fecha_nac;?>' size=15 onkeypress="return pulsar(event)" <?php if (($id_planilla) and ($tipo_transaccion != "M"))echo "disabled"?>>
		    	<?=link_calendario('fecha_nac');?>   
		    </td>		    
		</tr>   

		<tr>
			<td align="right">
					    	<b>Extranjero/Pais:</b>
					    </td>
					    <td align="left">
					    <select name=id_pais Style="width=150px"
					    onkeypress="buscar_combo(this);"
						onblur="borrar_buffer();"
				 		onchange="borrar_buffer(); document.forms[0].submit()">
					    
		    	<?     if (!$id_pais) {
	                         	    	
		    	           $sql_pais="select * from uad.pais";
					       $res_pais=sql($sql_pais) or die;
					        while (!$res_pais->EOF){
					  		$id_pais=$res_pais->fields['id_pais'];
						  	$pais=$res_pais->fields['nombre'];
						  	?> 
						    					   
						  <option <?if ($id_pais==4) echo "selected='selected'"?>value='<?=$id_pais?>'><?=$pais?></option>
						  
						  	<?$res_pais->movenext();
					  			}
		    	           $id_pais=$id_pais_default;}
					       
		    	         else {$sql_pais="select * from uad.pais where id_pais='$id_pais'";
					       $res_pais=sql($sql_pais) or die;
					       $pais=$res_pais->fields['nombre'];?> 
					       
					       <option value='<?=$id_pais?>'><?=$pais?></option>
					    <?}?>    
		    	             
					    </select>
					    </td>	
    
            <td align="right">
					    	<b>Provincia:</b>
					    </td>
					    <td align="left">
					    <select name=id_provincia Style="width=150px"
					    onKeypress="buscar_combo(this);"
				 		onblur="borrar_buffer();"
				 		onchange="borrar_buffer(); document.forms[0].submit()" >
					    
		    	<?    if ($id_pais==4){
		    	           if (!$id_provincia){
		    	           $sql_prov="select * from uad.provincias where id_pais='$id_pais' order by nombre";
					       $res_prov=sql($sql_prov) or die;
					        while (!$res_prov->EOF){
					  		$id_provincia=$res_prov->fields['id_provincia'];
						  	$provincia=$res_prov->fields['nombre']?>
						  
						  <option <?if ($id_provincia==12) echo "selected='selected'"?>value='<?=$id_provincia?>'><?=$provincia?></option>
						  
						  	<?$res_prov->movenext();
					  			}
					        $id_provincia=$id_provinc_default;
		    	            }
					        else { 
					       $sql_prov="select * from uad.provincias where id_provincia='$id_provincia'";
					       $res_prov=sql($sql_prov) or die;
					       $provincia=$res_prov->fields['nombre'];?> 
					       
					       <option value='<?=$id_provincia?>'><?=$provincia?></option>
					         <?}
					        }
					       else { echo "<option value=-1>Seleccione</option>";
					       	$sql_prov="select * from uad.provincias where id_pais='$id_pais' order by nombre";
					        $res_prov=sql($sql_prov) or die;
					        while (!$res_prov->EOF){
					  		$id_provincia=$res_prov->fields['id_provincia'];
						  	$provincia=$res_prov->fields['nombre']?>
						  
						  <option value='<?=$id_provincia?>'><?=$provincia?></option>
						  
						  	<?$res_prov->movenext();
					        }
					       }?>
					       </select>
					    </td>	
         	
         </tr> 
         
         <tr>
            <td align="right">
					    	<b>Departamento:</b>
					    </td>
					    <td align="left">
					    <select name=id_departamento Style="width=150px"
					    onKeypress="buscar_combo(this);"
				 		onblur="borrar_buffer();"
				 		onchange="borrar_buffer(); document.forms[0].submit()" >
					    
		    	<?  if ($id_pais==4 && $id_provincia==12){
		    	          if (!$id_departamento){
		    	           $sql_dep="select * from uad.departamentos where id_provincia='$id_provincia' order by nombre";
					       $res_dep=sql($sql_dep) or die;
					        while (!$res_dep->EOF){
					  		$id_departamento=$res_dep->fields['id_departamento'];
						  	$departamento=$res_dep->fields['nombre']?>
						  
						  <option <?if ($id_departamento==18) echo "selected='selected'"?>value='<?=$id_departamento?>'><?=$departamento?></option>
						  
						  	<?$res_dep->movenext();
					  			}
					        $id_departamento=$id_depart_default;
		    	            }
					        else { 
					       $sql_dep="select * from uad.departamentos where id_departamento='$id_departamento'";
					       $res_dep=sql($sql_dep) or die;
					       $departamento=$res_dep->fields['nombre'];?> 
					       
					       <option value='<?=$id_departamento?>'><?=$departamento?></option>
					         <?}
					        } else {echo "<option value=-1>Seleccione</option>";
					        $sql_dep="select * from uad.departamentos where id_provincia='$id_provincia' order by nombre";
					        $res_dep=sql($sql_dep) or die;
					        while (!$res_dep->EOF){
					  		$id_departamento=$res_dep->fields['id_departamento'];
						  	$departamento=$res_dep->fields['nombre']?>
						  
						  <option value='<?=$id_departamento?>'><?=$departamento?></option>
						  
						  	<?$res_dep->movenext();
					  			}
					        }
					        ?>
					       </select>
		 </table>
      </td>      
     </tr> 
   


	 <tr id="mo">
  		<td align=center colspan="2">
  			<b>Guardar Planilla</b>
  		</td>
  	</tr>  
  	 <tr align="center">
	 	<td>
	 		<b><font size="0" color="Red">Nota: Verifique todos los datos antes de guardar</font> </b>
	 	</td>
	</tr>
      <tr align="center">
       <td>
        <input type="submit" name="guardar" value="Guardar" title="Guardar"  style="width=130px"  onclick="return control_nuevos()">&nbsp;&nbsp;
        </td>
      </tr>
     

     
 </table>           
<br>
<br>

 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
     <input type=button name="volver" value="Volver" onclick="document.location='listado_benef_fact.php'"title="Volver al Listado" style="width=150px">     
   </td>
  </tr>
 
 </table></td></tr>
 
 
 </table>
</form>
 
<?=fin_pagina();// aca termino ?>