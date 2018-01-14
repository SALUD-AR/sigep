<?
require_once ("../../config.php");


Header('Content-Type: text/html; charset=LATIN1');

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();


$usuario1=$_ses_user['id'];

if ($_POST['volver']=="volver"){
     
	$ref = encode_link("factura_cardiop.php",array("id_beneficiario"=>$id_beneficiario,"id_pagina"=>$id_pagina));
		    echo "<SCRIPT>window.location='$ref';</SCRIPT>";

}


// Alta Efector
if ($_POST['guardar']=="Guardar"){
		
   $db->StartTrans();
  
   $nombre=$_POST['nombre'];
   $direccion=$_POST['direccion'];
   $cuit=$_POST['cuit'];
   $cbu=$_POST['cbu'];
   $cuenta=$_POST['cuenta'];
   
      
   	$sql_efector="select nextval('cardiopatia.seq_id_efector') as id_efector";
	$sql_id_efector=sql($sql_efector) or die();
	$id_efector=$sql_id_efector->fields['id_efector'];
	
   
   $sql_alta="INSERT INTO cardiopatia.efector (id_efector,nombre,domicilio,cuit,cbu,numero_cuenta)
				VALUES ($id_efector,'$nombre','$direccion','$cuit','$cbu','$cuenta')";
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

//controlan que ingresen todos los datos necesarios par el muleto
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

<?//<form name='form1' action='alta_efector.php' accept-charset="latin1" method='POST'>?>
<form name='form1' action='alta_efector.php' accept-charset="latin1" method='POST' enctype='multipart/form-data'>
<input type="hidden" value="<?=$usuario1?>" name="usuario1">
<input type="hidden" name="id_efector" value="<?=$id_efector?>">
<input type="hidden" name="id_beneficiario" value="<?=$id_beneficiario?>">
<input type="hidden" name="id_pagina" value="<?=$id_pagina?>">


<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>
<?echo "<center><b><font size='+1' color='Blue'>$accion2</font></b></center>";?>
<table width="97%" cellspacing=0 border="1" bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
    	<?
    	if (!$id_efector) {
    	?>  
    	<font size=+1><b>Nuevo Formulario</b></font>   
    	<? }
        else {
        ?>
        <font size=+1><b>Alta Efector Numero: <?=$id_efector?></b></font>   
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
         	<td align="right">
         	  <b>Nombre:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="60" value="<?=$nombre?>" name="nombre" onkeypress="return pulsar(event)" onblur="this.value=this.value.toUpperCase();" >
            </td>
            </tr>
            <tr>
           	<td align="right">
         	  <b>Direccion:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="60" value="<?=$direccion?>" name="direccion" onkeypress="return pulsar(event)" onblur="this.value=this.value.toUpperCase();" >
            </td>
         </tr> 
         
		<tr>
         	<td align="right" width="20%">
         	  <b>CUIT:</b>
         	</td>         	
            <td align='left' width="30%">
              <input type="text" size="30" value="<?=$cuit?>" name="cuit" onblur="CheckUserName(this);" >
             </td>
            
                       
         </tr>    
         <tr>
         	<td align="right" width="20%">
         	  <b>C.B.U.:</b>
         	</td>         	
            <td align='left' width="30%">
              <input type="text" size="22" value="<?=$cbu?>" name="cbu" onblur="CheckUserName(this);" >
             </td>
            
                       
         </tr>  
		<tr>
         	<td align="right" width="20%">
         	  <b>Nº de Cuenta:</b>
         	</td>         	
            <td align='left' width="30%">
              <input type="text" size="22" value="<?=$cuenta?>" name="cuenta" onblur="CheckUserName(this);" >
             </td>
            
                       
         </tr>                 
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
     <input type="submit" name="volver" value="volver" title="Volver" Style="width=250px;height=30px" onclick="return control_nuevos()">     
   </td>
  </tr>
 
 </table></td></tr>
 
 
 </table>
</form>
 
<?=fin_pagina();// aca termino ?>