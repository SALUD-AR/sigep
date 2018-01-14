<?php
/*
Author: ferni 

modificada por
$Author: ferni $
$Revision: 1.30 $
$Date: 2006/07/20 15:22:40 $
*/
require_once("../../config.php");

cargar_calendario();

$id_provincia=$parametros['id_provincia'] or $id_provincia=$_POST['id_provincia'];
$nom_provincia=$parametros['nom_provincia'] or $nom_provincia=$_POST['nom_provincia'];


if ($_POST['guardar_departamento']=='Guardar'){
	$db->StartTrans();
	
	$nom_departamento=$_POST['nom_departamento'];	
	$nom_departamento=strtoupper($nom_departamento);
	//---------verifico-------------

	$very="SELECT * from uad.departamentos
			where nombre='$nom_departamento' and id_provincia=$id_provincia";
		
	$res_very=sql($very, "Error al realizar la verificacion")or fin_pagina();
	
	if($res_very->recordCount()==0){
				
			    $q="select nextval('uad.departamentos_id_departamento_seq') as id_departamento";
			    $id_departamento=sql($q) or fin_pagina();
			    $id_departamento=$id_departamento->fields['id_departamento']; 
			    $query="insert into uad.departamentos
					   	(id_departamento, nombre, id_provincia)
					   	values
					   	('$id_departamento', '$nom_departamento' , '$id_provincia')";
				
			   sql($query, "Error al insertar el departamento") or fin_pagina();
			   $accion="Los datos se han guardado correctamente"; 
		   	   $db->CompleteTrans();   
	} else {$accion= "Ya existe un Departamento con ese nombre";}      
}

echo $html_header;
?>
<script>
//controlan que ingresen todos los datos necesarios par el muleto
//---------------------scrip para departamento------------------------------

function control_nuevo_departamento(){ 
  if(document.all.nom_departamento.value==""){
  alert('Debe ingresar un departamento');
  return false;
 } 
 } 
</script>
<form name=form1 action="nuevo_dpto.php" method=POST>
<input type="hidden" name="id_provincia" value="<?=$id_provincia?>">
<input type="hidden" name="nom_provincia" value="<?=$nom_provincia?>">
<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>

<table width="85%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
   
    <tr><td><table width="100%" class="bordes" align="center">
			<tr align="center" id="mo">
			  <td align="center">
			   <b>Nuevo Departamento de la Provincia <?=$nom_provincia?></b>
			  </td>
			</tr>
	</table></td></tr>
	<tr><td><table width="100%"  align="center">	
				         	<td align="right">
				         	  <b>Nombre:</b>
				         	</td>         	
				            <td align='left'>
				              <input type="text" size="40" value="" name="nom_departamento">
				            </td>
				      </tr>
				 	  <tr>
					  	<td align="center" colspan="5" class="bordes">		    
						  <input type="submit" name="guardar_departamento" value="Guardar" title="Guardar" style="width=130px" onclick="return control_nuevo_departamento()">&nbsp;&nbsp;
	 				         
						  <?
						  $sql1="SELECT id_pais from uad.provincias where id_provincia=$id_provincia";
						  $result=sql($sql1, "Error")or fin_pagina();
						  $ref = encode_link("pais_admin.php",array("id_pais"=>$result->fields['id_pais']));?>
						  <input type=button name="volver" value="Volver" onclick="document.location='<?=$ref?>'"title="Volver" style="width=150px">     
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