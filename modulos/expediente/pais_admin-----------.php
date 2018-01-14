<?
/*
Author: GABY

$Revision: 1.42 $
$Date: 2010/08/13 13:53:00 $
*/

require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();


if ($_POST['guardar_editar']=='Guardar'){
	$db->StartTrans();
   $nom_pais=strtoupper($nom_pais);     
   
   $query="update fondos.pais set 
		nom_pais='$nom_pais'
		where id_pais=$id_pais";	

   sql($query, "Error al insertar/actualizar Pais") or fin_pagina();
 	 
    $db->CompleteTrans();    
   $accion="Los datos se actualizaron";  
}

if ($_POST['guardar']=='Guardar'){
	$db->StartTrans();
			   $nom_pais=strtoupper($nom_pais);
	
		$very="SELECT * from fondos.pais
			where nom_pais='$nom_pais'";
		
	$res_very=sql($very, "Error al realizar la verificacion")or fin_pagina();
	
	if($res_very->recordCount()==0){
		
		    $q="select nextval('fondos.pais_id_pais_seq') as id_pais";
		    $id_pais=sql($q) or fin_pagina();
		    $id_pais=$id_pais->fields['id_pais']; 

		    $query="insert into fondos.pais
		   	(id_pais, nom_pais)
		   	values
		   	('$id_pais', '$nom_pais')";
			
		   sql($query, "Error al insertar el Pais") or fin_pagina();
		 	 
		   $accion="Los datos se han guardado correctamente"; 
		   
		   $db->CompleteTrans();   
  } else {$accion= "Ya existe un Pais con ese nombre";}          
}

if ($_POST['borrar']=='Borrar'){

$query="select * from fondos.provincia where id_pais=$id_pais";
	$res_pais=sql($query, "Error al eliminar el Pais") or fin_pagina(); 
		
	if ($res_pais->recordCount()==0){
	
		$query="delete from fondos.pais  
			where id_pais=$id_pais";
			sql($query, "Error al eliminar el pais") or fin_pagina(); 
			$accion="Los datos se han borrado";
			}
	else $accion="Existen Provincias Relacionadas";

}

if ($borra_pcia=='borra_pcia'){

	$query="select id_localidad from fondos.localidad where id_provincia=$id_provincia";
	$res_dpto=sql($query, "Error al eliminar el pcia") or fin_pagina(); 
		
	if ($res_dpto->recordCount()==0){
	sql($query, "Error al eliminar la Provincia") or fin_pagina(); 
	$query="delete from fondos.provincia  
			where id_provincia=$id_provincia";
	
	sql($query, "Error al eliminar la Provincia") or fin_pagina(); 
	$accion="Los datos se han borrado";
	}
	else $accion="Existen Localidades Relacionadas";
}

if ($borra_loca=='borra_loca'){
// verifico que la tabla proveedor y persona no posean relacion con localidad
	$query="select * from fondos.localidad
				inner join matriculacion.persona on (fondos.localidad.id_localidad=matriculacion.persona.id_localidad_real)
				where matriculacion.persona.id_localidad_real=$id_localidad";
	$res_loca=sql($query, "Error al realizar la consulta") or fin_pagina(); 
	
	if ($res_loca->recordCount()==0){
		$query="select * from fondos.localidad
					inner join fondos.proveedor on (fondos.localidad.id_localidad=fondos.proveedor.id_localidad)
				where fondos.proveedor.id_localidad=$id_localidad";
		$res_loca=sql($query, "Error al realizar la consulta") or fin_pagina(); 			
			if ($res_loca->recordCount()==0){	
				$query2="SELECT *
					FROM
					  fondos.localidad
					WHERE
					  id_localidad = $id_localidad";				
					sql($query, "Error al eliminar la localidad") or fin_pagina(); 
					$query="delete from fondos.localidad  
							where id_localidad=$id_localidad";
					
					sql($query, "Error al eliminar el Localidad") or fin_pagina(); 
					$accion="Los datos se han borrado";
			}
			else $accion="Existen Datos Relacionados";
	}
	else $accion="Existen Datos Relacionados";
}

if ($id_pais) {
	$query=" SELECT 
			 *
			FROM
			  fondos.pais  
			  where id_pais=$id_pais";
	
	$res_pais=sql($query, "Error al traer el Comprobantes") or fin_pagina();
	$nom_pais=$res_pais->fields['nom_pais'];
	$nom_pais=strtoupper($nom_pais);
}

if ($_POST['guardar_provincia']=='Guardar'){
	$db->StartTrans();
	   $nom_provincia=strtoupper($nom_provincia);
	
	   $very="SELECT * from fondos.provincia
			where nom_provincia='$nom_provincia' and id_pais=$id_pais";
		
	$res_very=sql($very, "Error al realizar la verificacion")or fin_pagina();
	
	if($res_very->recordCount()==0){
			
		    $q="select nextval('fondos.provincia_id_provincia_seq') as id_provincia";
		    $id_provincia=sql($q) or fin_pagina();
		    $id_provincia=$id_provincia->fields['id_provincia']; 
		   
		
		   $query="insert into fondos.provincia
				   	(id_provincia, nom_provincia, id_pais)
				   	values
				   	('$id_provincia', '$nom_provincia' , '$id_pais')";
			
		   sql($query, "Error al insertar provincia") or fin_pagina();
		 	 
		   $accion="Los datos se han guardado correctamente"; 
		   
		   $db->CompleteTrans();   
	} else {$accion= "Ya existe una Provincia con ese nombre";}      
    
}


//---------------------fin provincia------------------------------

echo $html_header;

?>
<script>
//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos()
{ 
 if(document.all.nom_pais.value==""){
  alert('Debe ingresar un pais');
  return false;
 } 
  
}//de function control_nuevos()

function editar_campos()
{	
	document.all.nom_pais.disabled=false;
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

//---------------------scrip para provincia------------------------------

function control_nuevo_provincia(){ 

  if(document.all.nom_provincia.value==""){
  alert('Debe ingresar una Provincia');
  return false;
 } 
 } 
 
 
//---------------------fin scrip para provincia---------------------------

</script>
<form name='form1' action='pais_admin.php' method='POST'>
<input type="hidden" value="<?=$id_pais?>" name="id_pais">

<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>
<table width="85%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
    	<?
    	if (!$id_pais) {
    	?>  
    	<font size=+1><b>Nuevo Dato</b></font>   
    	<? }
        else {
        ?>
        <font size=+1><b><?=$nombre?></b></font>   
        <? } ?>
       
    </td>
 </tr>
 <tr><td>
  <table width=90% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b> Descripción de Pais</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
         <tr>	           
           <td align="center" colspan="2">
            <b> Número del Dato: <font size="+1" color="Red"> <?=($id_pais)? $id_pais : "Nuevo Dato"?></font> </b>
           </td>
         </tr>
         </tr>
        <tr>
         	<td align="right">
         	  <b>Pais:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$nom_pais;?>" name="nom_pais" <? if ($id_pais) echo "disabled"?>>
            </td>
         </tr> 
 </table>           
<br>
<?if ($id_pais){?>
<table class="bordes" align="center" width="100%">
		 <tr>
		    <td align="center">
		      <input type=button name="editar" value="Editar" onclick="editar_campos()" title="Edita Campos" style="width=130px"> &nbsp;&nbsp;
		      <input type="submit" name="guardar_editar" value="Guardar" title="Guardar" disabled style="width=130px" onclick="return control_nuevos()">&nbsp;&nbsp;
		      <input type="button" name="cancelar_editar" value="Cancelar" title="Cancela Edicion" disabled style="width=130px" onclick="document.location.reload()">		      
		      <input type="submit" name="borrar" value="Borrar" style="width=130px" onclick="return confirm('Esta seguro que desea eliminar')" >
		    </td>
		 </tr> 
	 </table>	
	
	 <?}
	 else {?>
	 	<tr>
		    <td align="center">
		      <input type="submit" name="guardar" value="Guardar" title="Guardar" style="width=130px" onclick="return control_nuevos()">&nbsp;&nbsp;
		    </td>
	 
	 <? } ?>
	 

 
 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   
  </tr>  
 </table></td></tr>
 <? if($id_pais) {
 	
 
//--------------------- form provincia------------------------------?>
 
 
 	<tr><td><table width="100%" class="bordes" align="center">
			<tr align="center" id="mo">
			   <td align="center">
			   <b>Nueva Provincia</b>
			  </td>
			</tr>
	<tr><td><table width="100%"  align="center">
				         	<td align="right">
				         	  <b>Nombre:</b>
				         	</td>         	
				            <td align='left'>
				              <input type="text" size="40" value="" name="nom_provincia">
				            </td>
				      </tr>
				 	  <tr>
					  	<td align="center" colspan="5" class="bordes">		    
						  <input type="submit" name="guardar_provincia" value="Guardar" title="Guardar" style="width=130px" onclick="return control_nuevo_provincia()">&nbsp;&nbsp;
	 				    </td>
					 </tr> 
		        
		</table></td></tr>
 </table></td></tr>
		
	
 <?//--------------------- lista provincia------------------------------?>

	<tr><td><table width="100%" class="bordes" align="center">
			<tr align="center" id="mo">
			  <td align="center" width="3%">
			   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar" align="left" style="cursor:hand;" onclick="muestra_tabla(document.all.prueba_vida,2);" >
			  </td>
			  <td align="center">
			   <b>Provincias Relacionadas</b>
			  </td>
			</tr>
					
	</table></td></tr>
		
		
	<tr><td><table id="prueba_vida" border="1" width="100%" style="display:none;border:thin groove">
			<?//tabla de comprobantes
			$query="select * 
					 from  fondos.provincia
					  INNER JOIN fondos.pais ON (fondos.provincia.id_pais = fondos.pais.id_pais)
					WHERE
					  fondos.pais.id_pais = '$id_pais'";
			
			$res_comprobante=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
			if ($res_comprobante->RecordCount()==0){?>
				 <tr>
				  <td align="center">
				   <font size="2" color="Red"><b>No existe ninguna Provincia relacionada a este pais</b></font>
				  </td>
				</tr>
				 <?}
				 else{	 	
				 	?>
				 	<tr id="sub_tabla">	
				 	    <td width=1%>&nbsp;</td>
				 		<td width="30%">Provincia</td>
				 		<td width=1%>Borrar</td>
				 		
				 	</tr>
					
				 	<?
				 	$res_comprobante->movefirst();
				 	while (!$res_comprobante->EOF) {
				 				
				 		   		$ref = encode_link("nuevo_dpto.php",array("id_provincia"=>$res_comprobante->fields['id_provincia'],"nom_provincia"=>$res_comprobante->fields['nom_provincia']));
			    				$onclick_elegir="location.href='$ref'"; 
			            
			            $id_tabla="tabla_".$res_comprobante->fields['id_provincia'];	
				 		$onclick_check=" javascript:(this.checked)?Mostrar('$id_tabla'):Ocultar('$id_tabla')";?>
				 		
				 		<tr <?=atrib_tr()?>>
				 			<td>
				              <input type=checkbox name=check_prestacion value="" onclick="<?=$onclick_check?>" class="estilos_check">
				            </td>	
					 		<td onclick="<?=$onclick_elegir?>"><?=$res_comprobante->fields['nom_provincia']?></td>
					 		<? $ref=encode_link("pais_admin.php",array("id_provincia"=>$res_comprobante->fields['id_provincia'],"borra_pcia"=>"borra_pcia","id_pais"=>$id_pais)); 
					 		$onclick_provincia="if (confirm('Seguro que desea eliminar la Provincia?')) location.href='$ref'"; ?>
					 		<td align="center"><img src='../../imagenes/salir.gif' style='cursor:hand;' onclick="<?=$onclick_provincia?>"></td>
					 	</tr>
					 <?	//---------------------Lista de Localidad------------------------------	?>
					 	<tr>
					 	
				          <td colspan=6>
				           <? $id_provincia=$res_comprobante->fields['id_provincia'];
				                $sql=" select * from  fondos.localidad													
										WHERE id_provincia = '$id_provincia'";										
				                  $result_items=sql($sql) or fin_pagina();
				                  
				                 
				                  ?>
				                  <div id=<?=$id_tabla?> style='display:none'>
				                 	<table width=90% align=center border="1" class=bordes>
				                  			<?
				                  			$cantidad_items=$result_items->recordcount();
				                  			if ($cantidad_items==0){?>
					                            <tr>
					                            	<td colspan="10" align="center">
					                            		<b><font color="Red" size="+1">NO HAY LOCALIDADES RELACIONADOS A ESTA PROVINCIA</font></b>
					                            	</td>	                                
						                        </tr>	                               
											<?}
											else{
												?>
					                           <tr> 
												    <td align=right id=mo colspan="1">Localidad</a></td>  
												    <td align=right id=mo colspan="2">Borrar</a></td>      	
										     
					                            <?while (!$result_items->EOF){
											        //   $id_tabla="tabla_".$res_comprobante->fields['id_barrio'];	
												 		//$onclick_check2=" javascript:(this.checked)?Mostrar('$id_tabla'):Ocultar('$id_tabla')";?>
			    									
												  <tr <?=atrib_tr()?>>
						                            	
												 		<td onclick="<?=$onclick_elegir?>"><?=$result_items->fields["nom_localidad"]?></td>
						                            	 		
						                            	 <? $ref=encode_link("pais_admin.php",array("id_localidad"=>$result_items->fields['id_localidad'],"borra_loca"=>"borra_loca","id_pais"=>$id_pais)); 
												 		$onclick_dpto="if (confirm('Seguro que desea eliminar la Localidad?')) location.href='$ref'"; ?>
												 		<td align="center"><img src='../../imagenes/salir.gif' style='cursor:hand;' onclick="<?=$onclick_dpto?>"></td>
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
		 <?php } ?>
 

	
 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">

  <td>
     <input type=button name="cerrar" value="Cerrar" onclick="window.opener.location.reload();window.close();"title="Cerrar" style="width=150px">     
     </td>

   <td>
     <input type=button name="volver" value="Volver" onclick="document.location='pais_listado.php'"title="Volver al Listado" style="width=150px">     
     </td>

  </tr>
 </table></td></tr>
 	</table></td></tr>
 </table>
 </form>
 
 <?=fin_pagina();// aca termino ?>