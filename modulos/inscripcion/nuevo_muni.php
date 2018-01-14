<?php
/*
Author: ferni 

modificada por
$Author: gaby $
$Revision: 1.30 $
$Date: 2006/07/20 15:22:40 $
*/
require_once("../../config.php");
extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

/*$id_localidad=$parametros['id_localidad'] or $id_localidad=$_POST['id_localidad'];
$nom_localidad=$parametros['nom_localidad'] or $nom_localidad=$_POST['nom_localidad'];
$nom_provincia=$parametros['nom_provincia'] or $nom_provincia=$_POST['nom_provincia'];
$id_departamento=$parametros['id_departamento'] or $id_localidad=$_POST['id_departamento'];*/

if ($_POST['guardar']=='Guardar'){
	$db->StartTrans();
	
	$nombre=$_POST['nombre'];	
	
	//---------verifico-------------
    $nombre=strtoupper($nombre);
	$very="SELECT * from uad.municipios
			where nombre='$nombre' and id_localidad=$id_localidad";
		
	$res_very=sql($very, "Error al realizar la verificacion")or fin_pagina();
	
	if($res_very->recordCount()==0){
				
				$codpost="SELECT * from uad.codpost
						where id_localidad=$id_localidad";
				$codpost=sql($codpost, "Error al realizar la verificacion")or fin_pagina();
				$id_codpos=$codpost->fields['id_codpos'];
	
			    $q="select nextval('uad.municipios_id_municipio_seq') as id_municipio";
			    $municipio=sql($q) or fin_pagina();
			    $id_municipio=$municipio->fields['id_municipio']; 
			   
			   
			   $query="insert into uad.municipios
					   	(id_municipio, nombre, id_localidad, id_codpos)
					   	values
					   	('$id_municipio', '$nombre' , '$id_localidad', '$id_codpos')";
				
			   sql($query, "Error al insertar provincia") or fin_pagina();
			 	 
			   $accion="Los datos se han guardado correctamente"; 
			   
			   $db->CompleteTrans();   
	} else {$accion= "Ya existe un Mnicipio con ese nombre";}
         
}

echo $html_header;
?>
<script>
function control_nuevos(){ 
   if(document.all.nombre.value==""){
  alert('Debe ingresar un Municipio');
  return false;
 } 
 } 
</script>
<form name=form1 action="nuevo_muni.php" method=POST>
<input type="hidden" name="id_localidad" value="<?=$id_localidad?>">
<input type="hidden" name="id_municipio" value="<?=$id_municipio?>">
<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>


<table width="50%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">

    <tr><td><table width="100%" class="bordes" align="center">
			<tr align="center" id="mo">
			  <td align="center">
			   <b>Nuevo Municipio de la Localidad <?=$nom_localidad?></b>
			  </td>
			</tr>
	</table></td></tr>
	<tr><td><table width="100%"  align="center">	
				         	 <tr><td><table width=90% align="center" class="bordes">
		     <tr>
			      <td id=mo colspan="2">
			       <b> Nuevo Municipio</b>
			      </td>
		     </tr>
		     <tr>
		       <td><table>
		         <tr>	           
		           <td align="center" colspan="2">
		            <b> Número del Dato: <font size="+1" color="Red"><?=($id_municipio)? $id_municipio : "Nuevo Dato"?></font> </b>
		           </td>
		         </tr>
		         <tr>
				   	<td align="right">
				       	  <b>Nombre:</b>
				   	</td>         	
					<td align='left'>
					       <input type="text" size="40" value="" name="nombre">
					</td>
				</tr>
		</table></td></tr>	
		<tr><td><table width="100%"  align="center">		 	 
				 	  <tr>
					  	<td align="center" colspan="5" class="bordes">		    
						<input type="submit" name="guardar" value="Guardar" title="Guardar" style="width=130px" onclick="return control_nuevos()">&nbsp;&nbsp;
	 				    
		<?$sql01="SELECT id_pais,departamentos.nombre,departamentos.id_departamento
            from uad.provincias 
	  		inner join uad.departamentos using (id_provincia)
	  		inner join uad.localidades using (id_departamento)
	  		where id_localidad=$id_localidad";
	  $result01=sql($sql01, "Error")or fin_pagina();
	  $nombre_provincia=$result01->fields['nombre'];
	  $ref = encode_link("localidad_admin.php",array("id_departamento"=>$result01->fields['id_departamento'],"nom_departamento"=>$result01->fields['nombre']));
	  ?>
	  
	 				    <input type=button name="volver" value="Volver" onclick="document.location='<?=$ref?>'"title="Volver al Listado" style="width=150px">     
 						</td>
					 </tr> 
		</table></td></tr>
	</table></td></tr>
</table>
<br>
<br>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>