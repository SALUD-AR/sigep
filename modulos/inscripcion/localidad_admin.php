<?
/*
Author: ferni

modificada por
$Author: Gaby $
$Revision: 1.42 $
$Date: 2006/05/23 13:53:00 $
*/

require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);


if ($_POST['guardar_editar']=='Guardar'){
	$db->StartTrans();
   $nom_localidad=strtoupper($nom_localidad);     
   
   $query="update uad.localidades set 
		nombre='$nom_localidad',
   		id_departamento='$id_departamento'
		where id_localidad=$id_localidad";
   
   $query1="update uad.codpost set
            codigopostal='$codigopostal'
            where id_localidad=$id_localidad";
   		

   sql($query, "Error al insertar/actualizar localidad") or fin_pagina();
   sql($query1, "Error al insertar/actualizar localidad") or fin_pagina();
 	 
    $db->CompleteTrans();    
   $accion="Los datos se actualizaron";  
}

if ($_POST['guardar']=='Guardar'){
	$db->StartTrans();
	$nom_localidad=strtoupper($nom_localidad);
	//---------verifico-------------

	$very="SELECT * from uad.localidades
			where nombre='$nom_localidad' and id_departamento=$id_departamento";
		
	$res_very=sql($very, "Error al realizar la verificacion")or fin_pagina();
	
	if($res_very->recordCount()==0){
				
			    $q="select nextval('uad.localidades_id_localidad_seq') as id_localidad";
			    $localidad=sql($q) or fin_pagina();
			    $id_localidad=$localidad->fields['id_localidad']; 
			   
			
			   $query="insert into uad.localidades
			   	(id_localidad, nombre, id_departamento)
			   	values
			   	('$id_localidad', '$nom_localidad', '$id_departamento')";
				
			   $query1="insert into uad.codpost
			   	(id_localidad, codigopostal )
			   	values
			   	('$id_localidad', '$codigopostal')";
				
			   sql($query, "Error al insertar/actualizar pais") or fin_pagina();
			    
			   sql($query1, "Error al insertar/actualizar pais") or fin_pagina();
			 	 
			   $accion="Los datos se han guardado correctamente"; 
			   
			   $db->CompleteTrans();   
   	} else {$accion= "Ya existe una Localidad con ese nombre";}      
}

if ($_POST['borrar']=='Borrar'){

	$query="delete from uad.localidades  
			where id_localidad=$id_localidad";
	
	sql($query, "Error al eliminar la localidad") or fin_pagina(); 
	
	$accion="Los datos se han borrado";
}

if ($borra_loca=='borra_loca'){

	$query3="select * from uad.municipios where id_localidad=$id_localidad";
	$res_loca=sql($query3, "Error al verificar si posee Municipios relacionados") or fin_pagina(); 
		
	if ($res_loca->recordCount()==0){
	
	$query="delete from uad.localidades  
			where id_localidad=$id_localidad";
	
	sql($query, "Error al eliminar la Localidad") or fin_pagina(); 
	$accion="Los datos se han borrado";
	}
	else $accion="Existen Municipios Relacionadas";
}


if ($borra_muni=='borra_muni'){

	$query3="select * from uad.barrios where id_municipio=$id_municipio";
	$res_loca=sql($query3, "Error al verificar si posee barrios relacionados") or fin_pagina(); 
		
	if ($res_loca->recordCount()==0){
	
	$query="delete from uad.municipios  
			where id_municipio=$id_municipio";
	
	sql($query, "Error al eliminar el Municipio") or fin_pagina(); 
	$accion="Los datos se han borrado";
	}
	else $accion="Existen Barrios Relacionadas";
}

if ($id_localidad) {
$query=" SELECT *, uad.localidades.nombre as nom_localidad, uad.departamentos.nombre as nom_departamento, uad.codpost.codigopostal as codigopostal
			FROM
			  uad.departamentos
			  INNER JOIN uad.localidades ON (uad.localidades.id_departamento =uad.departamentos.id_departamento)
			  INNER JOIN uad.codpost ON (uad.localidades.id_localidad =uad.codpost.id_localidad)
			WHERE
			  uad.departamentos.id_departamento = $id_departamento and uad.localidades.id_localidad=$id_localidad";

$res_localidad=sql($query, "Error al traer el Comprobantes") or fin_pagina();
$codigopostal=$res_localidad->fields['codigopostal'];
$nom_localidad=$res_localidad->fields['nom_localidad'];

$nom_departamento=$res_localidad->fields['nom_departamento'];
$nom_localidad=strtoupper($nom_localidad);
}



//--------------------------barrio----------------------------

if ($_POST['guardar_barrio']=='Guardar'){
	$db->StartTrans();
	
		
    $q="select nextval('uad.barrios_id_barrio_seq') as id_barrio";
    $barrio=sql($q) or fin_pagina();
    $id_barrio=$barrio->fields['id_barrio']; 
   
   $nom_barrio=strtoupper($nom_barrio);
   $query="insert into uad.barrios
		   	(id_barrio, nombre, id_municipio)
		   	values
		   	('$id_barrio', '$nom_barrio' , '$id_municipio')";
	
   sql($query, "Error al insertar el barrio") or fin_pagina();
 	 
   $accion="Los datos se han guardado correctamente"; 
   
   $db->CompleteTrans();   
         
}
//---------------------fin barrio------------------------------

echo $html_header;

?>
<script>
//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos()
{ 
 if(document.all.nom_localidad.value==""){
  alert('Debe ingresar una Localidad');
  return false;
 }
 if(document.all.codigopostal.value==""){
  alert('Debe ingresar un Codigo Postal');
  return false;
 }  
  
}//de function control_nuevos()

function editar_campos()
{	
	document.all.nom_localidad.disabled=false;
	document.all.guardar_editar.disabled=false;
	document.all.cancelar_editar.disabled=false;
	document.all.borrar.disabled=false;
	document.all.guardar.enaible=false;
	return true;
}
//fin de function control_nuevos()
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


 //---------------------scrip para barrio------------------------------

function control_nuevo_barrio(){ 
  if(document.all.nom_barrio.value==""){
  alert('Debe ingresar un Barrio');
  return false;
 } 
 } 
 
//---------------------fin scrip para provincia---------------------------

</script>

<form name='form1' action='localidad_admin.php' method='POST'>
<input type="hidden" value="<?=$id_departamento?>" name="id_departamento">
<input type="hidden" value="<?=$id_localidad?>" name="id_localidad">
<input type="hidden" value="<?=$id_municipio?>" name="id_municipio">
<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>
<table width="85%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">

 <tr id="mo">
    <td>
    	<?
    	if (!$id_departamento) {
    	?>  
    	<font size=+1><b><? echo ($nom_departamento)?></b></font>   
    	<? }
        else {
        ?>
        <font size=+1><b> Datos de las Localidades </b></font>   
        <? } ?>
       
    </td>
 </tr>
 <tr><td>
 	
 
  <table width=90% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b> Nueva Localidad</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
         <tr>	           
           <td align="center" colspan="2">
            <b> Número del Dato: <font size="+1" color="Red"></font> </b>
           </td>
         </tr>
         </tr>
          <tr>
         	<td align="right">
         	  <b>Codigo Postal:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="" name="codigopostal" >
            </td>
         </tr> 
        <tr>
         	<td align="right">
         	  <b>Localidad:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="" name="nom_localidad">
            </td>
         </tr> 
 </table>           
<br>

<table class="bordes" align="center" width="100%">
		 <tr align="center" id="sub_tabla">
		 	<td>
		      <input type="submit" name="guardar" value="Guardar" title="Guardar" style="width=130px" onclick="return control_nuevos()">&nbsp;&nbsp;
			</td>
	 	</tr>
</table>
	 	 <?	//---------------------localidad------------------------------?>
<tr><td><table width="100%" class="bordes" align="center">
			<tr align="center" id="mo">
			  <td align="center" width="3%">
			   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar" align="left" style="cursor:hand;" onclick="muestra_tabla(document.all.prueba_vida,2);" >
			  </td>
			  <td align="center">
			   <b>Localidades relacionadas al Departamento <?=$nom_departamento ?></b>
			  </td>
			</tr>
					
	</table></td></tr>	
	<tr><td><table id="prueba_vida" border="1" width="100%" style="display:none;border:thin groove">
			<?//tabla de comprobantes
			$query="select * , uad.localidades.nombre as nom_localidad
					 from  uad.localidades
					 INNER JOIN uad.codpost ON (uad.localidades.id_localidad =uad.codpost.id_localidad)
					WHERE
					  id_departamento = $id_departamento";
			
			$res_comprobante=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
			if ($res_comprobante->RecordCount()==0){?>
				 <tr>
				  <td align="center">
				   <font size="2" color="Red"><b>No existe ninguna Localidad relacionada a este Departamento</b></font>
				  </td>
				</tr>
				 <?}
				 else{	 	
				 	?>
				 	<tr id="sub_tabla">	
				 	    <td width=1%>&nbsp;</td>
				 		<td width="20%">Codigo</td>
				 		<td width="30%">Localidad</td>
				 		<td width=1%>Borrar</td>
				 	</tr>
					
				 	<?
				 	$res_comprobante->movefirst();
				 	while (!$res_comprobante->EOF) {
				 				
				 		   		$ref = encode_link("nuevo_muni.php",array("id_localidad"=>$res_comprobante->fields['id_localidad'],"nom_localidad"=>$res_comprobante->fields['nom_localidad'],"id_departamento"=>$res_comprobante->fields['id_departamento']));
			    				$onclick_elegir="location.href='$ref', target=_blank"; 
			            
			            $id_tabla="tabla_".$res_comprobante->fields['id_localidad'];	
				 		$onclick_check=" javascript:(this.checked)?Mostrar('$id_tabla'):Ocultar('$id_tabla')";?>
				 		
				 		<tr <?=atrib_tr()?>>
				 			<td>
				              <input type=checkbox name=check_prestacion value="" onclick="<?=$onclick_check?>" class="estilos_check">
				            </td>	
					 		<td onclick="<?=$onclick_elegir?>"><?=$res_comprobante->fields['codigopostal']?></td>
					 		<td onclick="<?=$onclick_elegir?>"><?=$res_comprobante->fields['nom_localidad']?></td>
					 		<? $ref=encode_link("localidad_admin.php",array("id_localidad"=>$res_comprobante->fields['id_localidad'],"borra_loca"=>"borra_loca", "id_departamento"=>$res_comprobante->fields['id_departamento'])); 
					 		$onclick_localidad="if (confirm('Seguro que desea eliminar la Localidad?')) location.href='$ref'"; ?>
					 		<td align="center"><img src='../../imagenes/salir.gif' style='cursor:hand;' onclick="<?=$onclick_localidad?>"></td>
					 	</tr>
					 <?	//---------------------municipio------------------------------	?>
					 	<tr>
					 	
				          <td colspan=6>
				           <? $id_localidad=$res_comprobante->fields['id_localidad'];
				                $sql=" select * from  uad.municipios													
										WHERE id_localidad = '$id_localidad'";										
				                  $result_items=sql($sql) or fin_pagina();
				                  ?>
				                  <div id=<?=$id_tabla?> style='display:none'>
				                 	<table width=90% align=center border="1" class=bordes>
				                  			<?
				                  			$cantidad_items=$result_items->recordcount();
				                  			if ($cantidad_items==0){?>
					                            <tr>
					                            	<td colspan="10" align="center">
					                            		<b><font color="Red" size="+1">NO HAY MUNICIPIOS RELACIONADOS A ESTA LOCALIDAD</font></b>
					                            	</td>	                                
						                        </tr>	                               
											<?}
											else{
												?>
					                           <tr>
										    <td align=right id=mo colspan="1w">Municipios</a></td>      	
										    <td align=right id=mo colspan="2">Borrar</a></td> 
					                            <?while (!$result_items->EOF){
					                            	
					                            	$ref = encode_link("nuevo_barrio.php",array("id_municipio"=>$result_items->fields['id_municipio'],"nom_municipio"=>$result_items->fields['nombre']));
    												$onclick_elegir="location.href='$ref'";
						                            $id_tabla="tabla_".$result_items->fields['id_municipio'];	
				 									$onclick_check=" javascript:(this.checked)?Mostrar('$id_tabla'):Ocultar('$id_tabla')";?>
    												<tr <?=atrib_tr()?>>
						                            	 
    												<td onclick="<?=$onclick_elegir?>"><?=$result_items->fields["nombre"]?></td>
						                            	 		
						                            	 <? $ref=encode_link("localidad_admin.php",array("id_municipio"=>$result_items->fields['id_municipio'],"borra_muni"=>"borra_muni","id_localidad"=>$result_items->fields['id_localidad'],"id_departamento"=>$id_departamento )); 
												 		$onclick_muni="if (confirm('Seguro que desea eliminar el Municipio?')) location.href='$ref'"; ?>
												 	<td align="center"><img src='../../imagenes/salir.gif' style='cursor:hand;' onclick="<?=$onclick_muni?>"></td>
						                            
												 	</tr>
					                            	<?$result_items->movenext();
					                            }//del while
											}//del else?>
				                            	                            
				               			</table>
				               		</div>
						         </td>
						      </tr>  	
				 		<?$res_comprobante->movenext();
				 		}// fin while
				 		
				 		
				 	} //fin del else?>	 	
		</table></td></tr>
		 <?php //} ?>
 
 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
    <?
	  $sql01="SELECT id_pais,provincias.nombre from uad.provincias 
	  		inner join uad.departamentos using (id_provincia)
	  		where id_departamento=$id_departamento";
	  $result01=sql($sql01, "Error")or fin_pagina();
	  $nombre_provincia=$result01->fields['nombre'];
	  
	  $ref = encode_link("pais_admin.php",array("id_pais"=>$result01->fields['id_pais']));?>
						
     <input type=button name="volver" value="Volver" onclick="document.location='<?=$ref?>'"title="Volver al Listado" style="width=150px">     
     </td>
  </tr>
 </table></td></tr>
 	</table></td></tr>
 </table>
 </form>
 
 <?=fin_pagina();// aca termino ?>