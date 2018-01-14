<?require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

cargar_calendario();

if ($_POST['guardar_editar']=='Guardar'){
	$fecha_inf=Fecha_db($fecha_inf);
     $fecha_nac=Fecha_db($fecha_nac);
     $fecha_mu=Fecha_db($fecha_mu);
     $fecha_carga=date("Y-m-d H:i:s");     
     $usuario=$_ses_user['login'];

      $query="update registro.rep_def set 
      			  fecha_inf='$fecha_inf',
				  fecha_dia='$fecha_carga',
				  usuario='$usuario',
				  hist_cli='$hist_cli',
				  apellido='$apellido',
				  nombre='$nombre',
				  documento='$documento',
				  sexo='$sexo',
				  fecha_nac='$fecha_nac',
				  hora_nac='$hora_nac',
				  fecha_mu='$fecha_mu',
				  hora_mu='$hora_mu',
				  domicilio='$domicilio',
				  causa='$causa',
				  peso_nac='$peso_nac',
				  nom_ape_madre='$nom_ape_madre',
				  his_cli_mat='$his_cli_mat',
				  doc_madre='$doc_madre',
				  obs_madre='$obs_madre',
				  nom_prof='$nom_prof',
				  edad='$edad',
				  contacto='$contacto'				  
      		 	where id_rep_def=$id_rep_def";
	
	  sql($query, "Error al insertar la Planilla") or fin_pagina();
	  $accion="Registro Modificado";
}

if ($_POST['borrar']=='Borrar'){
	$query="delete from registro.rep_def where id_rep_def=$id_rep_def";
	sql($query, "Error al insertar la Planilla") or fin_pagina();
	$accion="Registro Eliminado";
}

if ($_POST['guardar']=="Guardar"){   
     $fecha_inf=Fecha_db($fecha_inf);
     $fecha_nac=Fecha_db($fecha_nac);
     $fecha_mu=Fecha_db($fecha_mu);
     $fecha_carga=date("Y-m-d H:i:s");     
     $usuario=$_ses_user['login'];
	 
	 	$q="select nextval('registro.rep_def_id_rep_def_seq') as id_rep_def";
	    $id_rep_def=sql($q) or fin_pagina();
	    $id_rep_def=$id_rep_def->fields['id_rep_def'];
	       
	    $query="insert into registro.rep_def
	             (id_rep_def,fecha_inf,fecha_dia,usuario,hist_cli,apellido,nombre,edad,documento,sexo,fecha_nac,hora_nac,fecha_mu,
	             	hora_mu,domicilio,causa,peso_nac,nom_ape_madre,his_cli_mat,doc_madre,obs_madre,cuie,nom_prof,establecimiento_privado,
	             	contacto,pais_nac,provincia_nac,departamento,localidad)
	             values
	             ('$id_rep_def','$fecha_inf','$fecha_carga','$usuario','$hist_cli','$apellido','$nombre','$edad','$documento','$sexo','$fecha_nac','$hora_nac','$fecha_mu',
	             	'$hora_mu','$domicilio','$causa','$peso_nac','$nom_ape_madre','$his_cli_mat','$doc_madre','$obs_madre','$cuie','$nom_prof','$establecimiento_privado',
	             	'$contacto','$pais_nac','$provincia_nac','$departamento','$localidad')";
	
	    sql($query, "Error al insertar la Planilla") or fin_pagina();
	    
	    $accion="Registro Grabado con el Numero de Transaccion: $id_rep_def";  	 

	    $contenido_mail_control="El profesional: $nom_prof. Cargo la denuncia : $id_rep_def. Con Fecha de Notificacion: $fecha_inf. Nombre: $nombre, Apellido: $apellido y DNI: $documento.
	    POR FAVOR VERIFICAR DETALLES EN SISTEMA. Link: sigep.sanluis.gov.ar. Menu: Registro Clinico > Reporte Defunciones";
    	enviar_mail('fernandonu@gmail.com','','','Nuevo Alerta Mortalidad',$contenido_mail_control,'','');     
    	enviar_mail('prosaman.sanluis@gmail.com','','','Nuevo Alerta Mortalidad',$contenido_mail_control,'','');     
    	enviar_mail('ccano@sanluis.gov.ar','','','Nuevo Alerta Mortalidad',$contenido_mail_control,'','');     
    	enviar_mail('alejandrorios1969@yahoo.com.ar','','','Nuevo Alerta Mortalidad',$contenido_mail_control,'','');     
    	enviar_mail('promiju@hotmail.com','','','Nuevo Alerta Mortalidad',$contenido_mail_control,'','');     
} 

if ($id_rep_def){//carga de prestacion a paciente NO PLAN NACER
	$sql="SELECT *
			FROM registro.rep_def
	  		where id_rep_def=$id_rep_def";
    $res_comprobante=sql($sql, "Error al traer los Comprobantes") or fin_pagina();
    
    $id_rep_def=$res_comprobante->fields['id_rep_def'];
	$fecha_inf=$res_comprobante->fields['fecha_inf'];
	$fecha_dia=$res_comprobante->fields['fecha_dia'];
	$usuario=$res_comprobante->fields['usuario'];
	$hist_cli=$res_comprobante->fields['hist_cli'];
	$apellido=$res_comprobante->fields['apellido'];
	$nombre=$res_comprobante->fields['nombre'];
	$edad=$res_comprobante->fields['edad'];
	$documento=$res_comprobante->fields['documento'];
	$sexo=$res_comprobante->fields['sexo'];
	$fecha_nac=$res_comprobante->fields['fecha_nac'];
	$hora_nac=$res_comprobante->fields['hora_nac'];
	$fecha_mu=$res_comprobante->fields['fecha_mu'];
	$hora_mu=$res_comprobante->fields['hora_mu'];
	$domicilio=$res_comprobante->fields['domicilio'];
	$causa=$res_comprobante->fields['causa'];
	$peso_nac=$res_comprobante->fields['peso_nac'];
	$nom_ape_madre=$res_comprobante->fields['nom_ape_madre'];
	$his_cli_mat=$res_comprobante->fields['his_cli_mat'];
	$doc_madre=$res_comprobante->fields['doc_madre'];
	$obs_madre=$res_comprobante->fields['obs_madre'];
	$cuie=$res_comprobante->fields['cuie'];
	$nom_prof=$res_comprobante->fields['nom_prof'];
	$establecimiento_privado=$res_comprobante->fields['establecimiento_privado'];
	$contacto=$res_comprobante->fields['contacto'];
	$pais_nac=$res_comprobante->fields['pais_nac'];
	$provincia_nac=$res_comprobante->fields['provincia_nac'];
	$departamento=$res_comprobante->fields['departamento'];
	$localidad=$res_comprobante->fields['localidad'];
} 
echo $html_header;
echo "<link rel=stylesheet type='text/css' href='$html_root/lib/bootstrap-3.3.1/css/custom-bootstrap.css'>";

?>
<script>
//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos()
{
 	 
	 if(document.all.documento.value==""){
	  alert('Debe Ingresar un Documento');
	  document.all.documento.focus();
	  return false; 
	 } 

	 if(document.all.fecha_inf.value==""){
	  alert('Debe Ingresar una Fecha de Informe');
	  document.all.fecha_inf.focus();
	  return false; 
	 } 

	  if(document.all.fecha_nac.value==""){
	  alert('Debe Ingresar una Fecha de Nacimiento');
	  document.all.fecha_nac.focus();
	  return false; 
	 } 

	  if(document.all.fecha_mu.value==""){
	  alert('Debe Ingresar una Fecha de Defuncion');
	  document.all.fecha_mu.focus();
	  return false; 
	 } 
	 if((document.all.establecimiento_privado.value=="-1")&&(document.all.cuie.value=="-1")){
	  alert('Debe Ingresar un Establecimiento Publico o Privado');	  
	  return false; 
	 } 

}//de function control_nuevos()

function editar_campos(){	
	document.all.documento.disabled=false;
	document.all.apellido.disabled=false;	
	document.all.nombre.disabled=false;

	document.all.cancelar_editar.disabled=false;
	document.all.guardar_editar.disabled=false;
	document.all.editar.disabled=true;
	return true;
}


/**********************************************************/
//funciones para busqueda abreviada utilizando teclas en la lista que muestra los clientes.
var digitos=10; //cantidad de digitos buscados
var puntero=0;
var buffer=new Array(digitos); //declaraci�n del array Buffer
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
   event.returnValue = false; //invalida la acci�n de pulsado de tecla para evitar busqueda del primer caracter
}//de function buscar_op_submit(obj)
</script>

<form name='form1' action='rep_def_admin.php' method='POST'>
<input type="hidden" value="<?=$id_rep_def?>" name="id_rep_def">

<?php if (!$id_rep_def){?>
<div class="alert alert-danger" align="center">
	<b>INFORMACION!</b> El reporte debe realizarse con caracter de obligatorio y dentro de las 24 hs de ocurrida la defuncion (ley III-0067-2004(5668)).
</div>
<?php }?>
<?php if ($accion){?>
<div class="alert alert-info" align="center">
	<b>INFORMACION!</b> <?=$accion?>.
</div>
<?php }?>


<table width='95%' border='1' align="center" class="table table-bordered">
 <tr id="mo">
    <td>
    	<font size=+1><b>Registro de Vigilancia de Mortalidad Infantil y Materna</b></font>    	       
    </td>
 </tr>
 
 <tr><td>
         <table width=100% align="center" style='border: 1px solid black;'>  
         <tr>
      		<td id='mo' colspan="4">
       		<b> Datos de la Defuncion </b>
      		</td>
     	</tr>  
		
		<tr>	
			<td align="right">
				<b>Fecha de Informe:</b>
			</td>
		    <td align="left">
		    	 <input type=text id='fecha_inf' name='fecha_inf' value='<?=fecha($fecha_inf);?>' size=15 readonly>
		    	 <?=link_calendario("fecha_inf");?>
		    </td>			
			<td align="right">
         	  <b>Historia Clinica:</b>
         	</td>         	
            <td align='left'>
              <textarea cols='15' rows='1' name='hist_cli' ><?=$hist_cli?></textarea>
            </td>            
        </tr>

        <tr>
         	<td align="right">
         	  <b>Apellido y Nombre:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$apellido?>" name="apellido" >
            </td>
            <td align="right">
				<b>Edad: </b>
			</td>
		    <td align="left">
			  <input type=text name='edad' value='<?=$edad;?>' size=10 >
			  	<select name='nombre' Style="width:90px">	 
				 <option value='Anios' <?if (trim($nombre)=='Anios') echo "selected"?>>Anios</option>
				 <option value='Meses'  <?if (trim($nombre)=='Meses') echo "selected"?>>Meses</option>
				 <option value='Dias'  <?if (trim($nombre)=='Dias') echo "selected"?>>Dias</option>
				 <option value='Horas'  <?if (trim($nombre)=='Horas') echo "selected"?>>Horas</option>
				 <option value='Minutos'  <?if (trim($nombre)=='Minutos') echo "selected"?>>Minutos</option>
				</select>     	 
		    </td>	
        </tr> 
		
		<tr>
         	<td align="right">
         	  <b>Documento:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="20" value="<?=$documento?>" name="documento" >
            </td>
            <td align="right">
				<b>Sexo:</b>
			</td>
		    <td align="left">	
		    	<select name='sexo' Style="width:90px">	 
				 <option value='Masculino' <?if (trim($sexo)=='Masculino') echo "selected"?>>Masculino</option>
				 <option value='Femenino'  <?if (trim($sexo)=='Femenino') echo "selected"?>>Femenino</option>
				 <option value='Indeterminado'  <?if (trim($sexo)=='Indeterminado') echo "selected"?>>Indeterminado</option>
				</select> 
		    </td>		    	    
		</tr>

		<tr>
         	<td align="right">
				<b>Fecha de Nacimiento:</b>				
			</td>
		    <td align="left">
		    	<input type=text id='fecha_nac' name='fecha_nac' value='<?=fecha($fecha_nac);?>' size=15 readonly>
		    	 <?=link_calendario("fecha_nac");?>
		    </td>	
           <td align="right">
				<b>Fecha de Defuncion:</b>
			</td>
		    <td align="left">
		    	<input type=text id='fecha_mu' name='fecha_mu' value='<?=fecha($fecha_mu);?>' size=15 readonly>
		    	 <?=link_calendario("fecha_mu");?>
		    </td>		
         </tr>

		<script type="text/javascript">
         	$(document).ready(function(){
			$("#pais_nac").change(function(){
				$.ajax({
					url:"procesa.php",
					type: "POST",
					data:"id_pais="+$("#pais_nac").val(),
					success: function(opciones){
						$("#provincia_nac").html(opciones);
								
						}
					})
				});
			});

			$(document).ready(function(){
				$("#provincia_nac").change(function(){
					$.ajax({
						url:"procesa.php",
						type: "POST",
						data:"id_provincia="+$("#provincia_nac").val(),
						success: function(opciones){
							$("#departamento").html(opciones);
									
						}
					})
				});
			}); //FIN

			//Script para el manejo de combobox de Departamento - Localidad - Municipio y Barrio
			$(document).ready(function(){
				$("#departamento").change(function(){
					$.ajax({
						url:"procesa.php",
						type: "POST",
						data:"id_departamento="+$("#departamento").val(),
						success: function(opciones){
							$("#localidad").html(opciones);
									
						}
					})
				});
			});
			
			//Guarda el nombre del Pais
			function showpais_nac(){
				var pais_nac = document.getElementById('pais_nac')[document.getElementById('pais_nac').selectedIndex].innerHTML;
				document.all.pais_nacn.value =  pais_nac;
			}// FIN

			//Guarda el nombre de la Provincia de Nacimiento
			function showprovincia_nac(){
				var provincia_nac = document.getElementById('provincia_nac')[document.getElementById('provincia_nac').selectedIndex].innerHTML;
				document.all.provincia_nacn.value =  provincia_nac;
			}// FIN

			//Guarda el nombre del Departamento
			function showdepartamento(){
				var departamento = document.getElementById('departamento')[document.getElementById('departamento').selectedIndex].innerHTML;
				document.all.departamenton.value =  departamento;
			} // FIN

			//Guarda el nombre del Localidad
			function showlocalidad(){
				var localidad = document.getElementById('localidad')[document.getElementById('localidad').selectedIndex].innerHTML;
				document.all.localidadn.value =  localidad;
			}// FIN
         </script>

		
	<?php
	if ($id_rep_def){
		$strConsulta = "select id_pais, nombre from uad.pais where id_pais=$pais_nac";
		$result = @pg_exec($strConsulta); 	
		$fila = pg_fetch_array($result);
		$pais_nac='<option value="'.$fila["id_pais"].'">'.$fila["nombre"].'</option>';
			
		$strConsulta = "select id_provincia, nombre from uad.provincias where id_provincia = $provincia_nac";
		$result = @pg_exec($strConsulta); 
		$fila= pg_fetch_array($result);
		$opcionespcias='<option value="'.$fila["id_provincia"].'">'.$fila["nombre"].'</option>';

		$strConsulta = "select id_departamento, nombre from uad.departamentos where id_departamento = $departamento";
		$result = @pg_exec($strConsulta); 
		$fila= pg_fetch_array($result);
		$departamento='<option value="'.$fila["id_departamento"].'">'.$fila["nombre"].'</option>';

		$strConsulta = "select id_localidad, nombre from uad.localidades where id_localidad = $localidad";
		$result = @pg_exec($strConsulta); 
		$fila= pg_fetch_array($result);
		$opcionesloc='<option value="'.$fila["id_localidad"].'">'.$fila["nombre"].'</option>';
	}
	else{
		$strConsulta = "select id_pais, nombre from uad.pais order by nombre";
		$result = @pg_exec($strConsulta); 
		$pais_nac = '<option value="0"> Seleccione Pais </option>';
		$opcionespcias = '<option value="0"> Seleccione Provincia </option>';
		$departamento = '<option value="0"> Seleccione Departamento </option>';
		$opcionesloc = '<option value="0"> Seleccione Localidad </option>';
		while( $fila = pg_fetch_array($result) )
		{		
			$pais_nac.='<option value="'.$fila["id_pais"].'">'.$fila["nombre"].'</option>';
			
		}
	}?>
		
		<tr align="center" id="sub_tabla">
      		<td colspan="6">
       		<b> Los Datos de Domicilio corresponden a la Residencia Habitual del Fallecido</b>
      		</td>
     	</tr>


		<tr>
			<td align="right" >
				<b>Pais:</b> <input type="hidden" name="pais_nacn" value="<?=$pais_nacn?>">
			</td>
			<td align="left" >
			<select id="pais_nac" name="pais_nac" onchange="showpais_nac();" <?php if ($id_rep_def)echo "disabled"?>><?php echo $pais_nac;?></select>			 	
		   	</td> 
    
            <td align="right">
				<b>Provincia:</b> <input type="hidden" name="provincia_nacn" value="<?=$provincia_nacn?>">
			</td>
			<td align="left">	
			<select id="provincia_nac" name="provincia_nac" onchange="showprovincia_nac();" <?php if ($id_rep_def)echo "disabled"?>><?php echo $opcionespcias;?></select>
			</td> 
         	
        </tr> 
         

		<tr>
		    <td align="right">
		    <b>Departamento:</b> <input type="hidden" name="departamenton" value="<?=$departamenton?>"> 
		    </td>
		    <td align="left">
		    <select id="departamento" name="departamento" onchange="showdepartamento();" <?php if ($id_rep_def)echo "disabled"?>><?php echo $departamento;?></select>
		    </td>
		    <td align="right">
		    <b>Localidad:</b><input type="hidden" name="localidadn" value="<?=$localidadn?>">
		    </td>
		    <td align="left">
		    <select id="localidad" name="localidad" onchange="showlocalidad();" <?php if ($id_rep_def)echo "disabled"?>><?php echo $opcionesloc;?></select>
		    </td>
    	 </tr>
    

         <tr>
			<td align="right">
         	  <b>Domicilio:</b>
         	</td>         	
            <td align='left'>
              <textarea cols='40' rows='2' name='domicilio' onKeyUp="this.value=this.value.toUpperCase();" ><?=$domicilio?></textarea>
            </td>
			<td align="right">
         	  <b>Causa de Defuncion:</b>
         	</td>         	
            <td align='left'>
              <textarea cols='40' rows='4' name='causa' onKeyUp="this.value=this.value.toUpperCase();"><?=$causa?></textarea>
            </td>
        </tr> 

        <tr>
            <td align="right">
				<b>Debido a:</b>
			</td>
		    <td align="left">	
		      <textarea cols='40' rows='4' name='hora_mu' onKeyUp="this.value=this.value.toUpperCase();"><?=$hora_mu?></textarea>	      
		    </td>	
		    <td align="right">
				<b>Debido a:</b>
			</td>
		    <td align="left">		      
		      <textarea cols='40' rows='4' name='hora_nac' onKeyUp="this.value=this.value.toUpperCase();"><?=$hora_nac?></textarea>	      
		    </td>	
         </tr> 

         <tr>
            <td align="right">
				<b>Contacto Telefonico de Acompanante:</b>
			</td>
		    <td align="left">	
		      <textarea cols='40' rows='1' name='contacto' onKeyUp="this.value=this.value.toUpperCase();"><?=$contacto?></textarea>	      
		    </td>	
		    <td align="right">
				&nbsp
			</td>
		    <td align="left">		      
				&nbsp
			</td>	
         </tr> 

		<tr align="center" id="sub_tabla">
      		<td colspan="6">
       		<b> SI ES MUERTE INFANTIL COMPLETAR</b>
      		</td>
     	</tr>

     	<tr>           
		    <td align="right">
				<b>Peso al Nacimiento en Gramos:</b>
			</td>
		    <td align="left">
			  <input type=text name='peso_nac' value='<?=$peso_nac;?>' size=10 >		    	 
		    </td>	
         	<td align="right">
				<b>Nombre y Apellido de la Madre:</b>
			</td>
		    <td align="left">		      
			  <input type=text name='nom_ape_madre' value='<?=$nom_ape_madre;?>' size=40 onKeyUp="this.value=this.value.toUpperCase();">		    	 
		    </td>
         </tr>

         <tr>
         	<td align="right">
				<b>Historia Clinica Materna:</b>
			</td>
		    <td align="left">
			  <input type=text name='his_cli_mat' value='<?=$his_cli_mat;?>' size=40 onKeyUp="this.value=this.value.toUpperCase();">		    	 
		    </td>	
            <td align="right">
				<b>DNI de la Madre:</b>
			</td>
		    <td align="left">		      
			  <input type=text name='doc_madre' value='<?=$doc_madre;?>' size=40 onKeyUp="this.value=this.value.toUpperCase();">		    	 
		    </td>	
         </tr>

         <tr>
         	<td align="right">
				<b>Observaciones:</b>
			</td>
		    <td align="left">
			  <input type=text name='obs_madre' value='<?=$obs_madre;?>' size=40 onKeyUp="this.value=this.value.toUpperCase();">		    	 
		    </td>	
            <td align="right">
				&nbsp
			</td>
		    <td align="left">		      
			  &nbsp	    	 
		    </td>	
         </tr>

		<tr align="center" id="sub_tabla">
      		<td colspan="6">
       		<b> DATOS DE LA INSTITUCION QUE INFORMA</b>
      		</td>
     	</tr>       
		
   <script type="text/javascript">
	   function publico_div_muestra(){
	   	 document.getElementById('publico_div').style.display = 'inline';
	   	 document.getElementById('privado_div').style.display = 'none';
	   }
	   function privado_div_muestra(){
	   	 document.getElementById('publico_div').style.display = 'none';
	   	 document.getElementById('privado_div').style.display = 'inline';
	   }
   </script>


         <tr>
         	<td align="right">
				<b>Establecimiento:
				<INPUT TYPE=RADIO NAME='esta' VALUE='publico' onclick="publico_div_muestra()" checked>Publico
				<INPUT TYPE=RADIO NAME='esta' VALUE='privado' onclick="privado_div_muestra()" >Privado</b>
			</td>
			
			<td><table id='publico_div' style='display: inline;'>
			<td align="left">	 	
			 <select name='cuie' Style="width:290px" 
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer();"
				onchange="borrar_buffer();" 
				<?if ($id_rep_def) echo "disabled"?> >
			 <option value=-1>Seleccione</option>
			 	<optgroup label="MAS USADOS">
			 		<option value="D12009">Maternidad Provincial Dra. Teresita Baigorria</option>
			 		<option value="D05148">HOSPITAL JUAN DOMINGO PERON</option>
			 		<option value="D05071">HOSPITAL MERLO</option>
			 	</optgroup> 

			 	<optgroup label="TODOS"> 
				 <?
				 $sql= "select * from nacer.efe_conv order by nombre";
				 $res_efectores=sql($sql) or fin_pagina();
				 while (!$res_efectores->EOF){ 
				 	$cuiel=$res_efectores->fields['cuie'];
				    $nombre_efector=$res_efectores->fields['nombre'];
				    
				    ?>
					<option value='<?=$cuiel?>' <?if (trim($cuie)==$cuiel) echo "selected"?> ><?=$nombre_efector?></option>
				    <?
				    $res_efectores->movenext();
				    }?>
			    </optgroup> 
			</select>
			</td>
			</table></td>

			<td><table id='privado_div' style='display: none;'>				       	
	            <td align='left'>
	            	<select name='establecimiento_privado' Style="width:290px" 
		        		onKeypress="buscar_combo(this);"
						onblur="borrar_buffer();"
						onchange="borrar_buffer();">
					 <option value=-1>Seleccione</option>
					 	<?
						 $sql= "select * from nacer.efe_priv order by nombre";
						 $res_efectores=sql($sql) or fin_pagina();
						 while (!$res_efectores->EOF){ 
						 	$cuiel=$res_efectores->fields['id_efe_priv'];
						    $nombre_efector=$res_efectores->fields['nombre'];
						    
						    ?>
							<option value='<?=$cuiel?>' <?if (trim($establecimiento_privado)==$cuiel) echo "selected"?> ><?=$nombre_efector?></option>
						    <?
						    $res_efectores->movenext();
						    }?>
					</select>
	            </td>
			</table></td>
		</tr>

		<?if ($id_rep_def){
   		if (!($establecimiento_privado=='-1')){?>
   			<script type="text/javascript">
			   	 document.getElementById('publico_div').style.display = 'none';
			   	 document.getElementById('privado_div').style.display = 'inline';
			</script>
   		<?}
   		else{?>
   			<script type="text/javascript">
			   	 document.getElementById('publico_div').style.display = 'inline';
			   	 document.getElementById('privado_div').style.display = 'none';
			</script>

   		<?}
   		}?>

   		<tr>
         	<td align="right">
				<b>Nombre Profesional que reporta:</b>
			</td>
		    <td align="left">		      
			  <input type=text name='nom_prof' value='<?=$nom_prof;?>' size=40 onKeyUp="this.value=this.value.toUpperCase();">		    	 
		    </td>	
         </tr>

		 <tr id="mo">
	  		<td align=center colspan="4">
	  			<b>Acciones Con Registro</b>
	  		</td>
	  	 </tr>

		<tr>
		   <td align="center" colspan="4">
	   		<? if($id_rep_def){ ?>
				      <input type="button" name="editar" value="Editar" onclick="editar_campos()" title="Edita Campos" style="width:150;height:35"> &nbsp;&nbsp;
				      <input type="submit" name="guardar_editar" value="Guardar" title="Guardar" disabled style="width:150;height:35" onclick="return control_nuevos()">&nbsp;&nbsp;
				      <input type="button" name="cancelar_editar" value="Cancelar" title="Cancela Edicion" disabled style="width:150;height:35" onclick="document.location.reload()">	
				      <input type="submit" name="borrar" value="Borrar" title="Eliminar" style="width:150;height:35" onclick="return confirm('Esta Seguro que desea Eliminar?')">	      
			   <?}else {?>
				      <input type="submit" name="guardar" value="Guardar" title="Guardar" style="width:150;height:35" onclick="return control_nuevos()">&nbsp;&nbsp;
			 <? } ?>
		    </td>
		</tr> 

		<tr id="mo">
	  		<td align=center colspan="4">
	  			<b>Volver</b>
	  		</td>
	  	</tr>

		  <tr>
		   <td align="center" colspan="4">
		     <input type=button name="volver" value="Volver" onclick="document.location='rep_def_listado.php'"title="Volver al Listado" style="width:150;height:35">
		   </td>
		  </tr>

 </table></td></tr>
 
  </table>  
 </form>
 
 <?=fin_pagina();// aca termino ?>
