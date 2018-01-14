<?php
/*
Author: ferni 

modificada por
$Author: ferni $
$Revision: 1.30 $
$Date: 2006/07/20 15:22:40 $
*/
require_once("../../config.php");
extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

if ($_POST['guardar']=='Guardar'){
	$db->StartTrans();
	
	//---------verifico-------------
	$nom_barrio=strtoupper($nom_barrio);
	$very="SELECT * from uad.barrios
			where nombre='$nom_barrio' and id_municipio=$id_municipio";
		
	$res_very=sql($very, "Error al realizar la verificacion")or fin_pagina();
	
	if($res_very->recordCount()==0){
		
	    $q="select nextval('uad.barrios_id_barrio_seq') as id_barrio";
	    $barrio=sql($q) or fin_pagina();
	    $id_barrio=$barrio->fields['id_barrio']; 
	   

	   $query="insert into uad.barrios
			   	(id_barrio, nombre, id_municipio)
			   	values
			   	('$id_barrio', '$nom_barrio' , '$id_municipio')";
		
	   		sql($query, "Error al insertar provincia") or fin_pagina();
		 $accion="Los datos se han guardado correctamente"; 
		 $db->CompleteTrans();   
	} else {$accion= "Ya existe un Barrio con ese nombre";}
         
}

if ($borra_barrio=='borra_barrio'){

	$query="delete from uad.barrios  
			where id_barrio=$id_barrio";
	
	sql($query, "Error al eliminar el Barrio") or fin_pagina(); 
	$accion="Los datos se han borrado";
	
}

if ($id_municipio){

	$query="select * from uad.municipios  
			where id_municipio=$id_municipio";
	
	$res_query=sql($query, "Error al realizar la consulta") or fin_pagina(); 
	$nom_municipio=$res_query->fields['nombre'];
	
}


echo $html_header;
?>

<script>
function control_nuevos(){ 

  if(document.all.nom_barrio.value==""){
  alert('Debe ingresar un barrio');
  return false;
 } 
 } 
 
 //empieza funcion mostrar tabla
var img_ext='<?=$img_ext='../../imagenes/rigth2.gif' ?>';//imagen extendido
var img_cont='<?=$img_cont='../../imagenes/down2.gif' ?>';//imagen contraido

function muestra_tabla(obj_tabla,nro){
 oimg=eval("document.all.imagen_"+nro);//objeto tipo IMG
 if (obj_tabla.style.display=='none'){
 	obj_tabla.style.display='inline';
    oimg.show=0;
    oimg.src=img_ext;
 }
 else{
 	obj_tabla.style.display='none';
    oimg.show=1;
	oimg.src=img_cont;
 }
}//termina muestra tabla

 
 
 </script>
<form name=form1 action="nuevo_barrio.php" method=POST>
<input type="hidden" name="id_municipio" value="<?=$id_municipio?>">
<input type="hidden" name="nom_municipio" value="<?=$nom_municipio?>">
<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>

<table width="85%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">

 <tr id="mo">
    <td>
    	<font size=+1><b><? echo "Datos de la Municipalidad de ".$nom_municipio?></b></font>          
    </td>
 </tr>
 <tr><td>
 	 
 
  <table width=90% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b> Nuevo Barrio</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
         <tr>	           
           <td align="center" colspan="2">
            <b> Número del Dato: <font size="+1" color="Red"><?"Nuevo Dato"?></font> </b>
           </td>
         </tr>
         </tr>
				       <tr>
				         	<td align="right">
				         	  <b>Nombre:</b>
				         	</td>         	
				            <td align='left'>
				              <input type="text" size="40" value="" name="nom_barrio">
				            </td>
				      </tr>
				 	 <tr>
	</table></td></tr>			 	 
<tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
   	<input type="submit" name="guardar" value="Guardar" title="Guardar" style="width=130px" onclick="return control_nuevos()">&nbsp;&nbsp;
	
   	<?$sql01="SELECT id_pais,departamentos.nombre,departamentos.id_departamento
            from uad.provincias 
	  		inner join uad.departamentos using (id_provincia)
	  		inner join uad.localidades using (id_departamento)
	  		inner join uad.municipios using (id_localidad)
	  		where id_municipio=$id_municipio";
	  $result01=sql($sql01, "Error")or fin_pagina();
	  $nombre_provincia=$result01->fields['nombre'];
	  $ref = encode_link("localidad_admin.php",array("id_departamento"=>$result01->fields['id_departamento'],"nom_departamento"=>$result01->fields['nombre']));
	  ?>
   	
   	<input type=button name="volver" value="Volver" onclick="document.location='<?=$ref?>'"title="Volver al Listado" style="width=150px">     
     </td>
  </tr>
 </table></td></tr>
	 	 <?	//---------------------barrio------------------------------	?>
<tr><td><table width="100%" class="bordes" align="center">
			<tr align="center" id="mo">
			  <td align="center" width="3%">
			   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar" align="left" style="cursor:hand;" onclick="muestra_tabla(document.all.prueba_vida,2);" >
			  </td>
			  <td align="center">
			   <b>Barrios Relacionados al Municipio <?echo ($nom_municipio) ?></b>
			  </td>
			</tr>
					
	</table></td></tr>	
	<tr><td><table id="prueba_vida" border="1" width="100%" style="display:none;border:thin groove">
			<?//tabla de comprobantes
			$query="select * 
					 from  uad.barrios
					WHERE
					  id_municipio = $id_municipio";
			
			$res_comprobante=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
			if ($res_comprobante->RecordCount()==0){?>
				 <tr>
				  <td align="center">
				   <font size="2" color="Red"><b>No existe ningun Barrio relacionada a este municipio</b></font>
				  </td>
				</tr>
				 <?}
				 else{	 	
				 	?>
				 	<tr id="sub_tabla">	
				 	  
				 		<td width="20%">Barrio</td>
				 	
				 		<td width=1%>Borrar</td>
				 	</tr>
					
				 	<?
				 	$res_comprobante->movefirst();
				 	while (!$res_comprobante->EOF) {

			            $id_tabla="tabla_".$res_comprobante->fields['id_barrio'];	
				 		$onclick_check=" javascript:(this.checked)?Mostrar('$id_tabla'):Ocultar('$id_tabla')";?>
				 		
				 		<tr <?=atrib_tr()?>>
					 		<td onclick="<?=$onclick_elegir?>"><?=$res_comprobante->fields['nombre']?></td>
					 		<? $ref=encode_link("nuevo_barrio.php",array("id_barrio"=>$res_comprobante->fields['id_barrio'],"borra_barrio"=>"borra_barrio", "id_municipio"=>$res_comprobante->fields['id_municipio'])); 
					 		$onclick_barrio="if (confirm('Seguro que desea eliminar la Barrio?')) location.href='$ref'"; ?>
					 		<td align="center"><img src='../../imagenes/salir.gif' style='cursor:hand;' onclick="<?=$onclick_barrio?>"></td>
					 	</tr>
					 	<?$res_comprobante->movenext();
				 		}// fin while
				 		}?>
					 	
					 	</table></td></tr>			        
		
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>