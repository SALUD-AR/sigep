<?
/*
Author: gaby
$Revision: 1.0 $
$Date: 2012/10/18 10:52:40 $
*/

require_once ("../../config.php");


extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

if ($_POST['guardar']=="Guardar"){	
	$db->StartTrans();	  	
		$fecha_inf=fecha_db($fecha_inf);
		
		//genero consulta para validar el insert
	if ($entidad_alta=='na'){//$id_smiafiliados
			$query_cos="SELECT
						count(leche.archivo_fichero.num) as numero
						FROM
						leche.info_social
						LEFT OUTER JOIN leche.archivo_fichero ON leche.archivo_fichero.id_informe = leche.info_social.id_informe
						where id_smiafiliados=$id and autorizado='PEND' "; //verifico entrega de la leche en el mismo periodo
			
	}else{ //$id_beneficiarios
			$query_cos="SELECT
						count(leche.archivo_fichero.num) as numero
						FROM
						leche.info_social
						LEFT OUTER JOIN leche.archivo_fichero ON leche.archivo_fichero.id_informe = leche.info_social.id_informe
						where id_beneficiarios=$id and autorizado='PEND'"; //verifico entrega de la leche en el mismo periodo
			
	}
  $res_qcos=sql($query_cos,"Error al realizar verificacion de entregas anteriores") or fin_pagina();
	if ($res_qcos->RecordCount()!=EOF){		
$numero=$res_qcos->fields['numero'];
		$path = MOD_DIR."/entrega_leche/ar_info_social";
		$name = $_FILES["archivo"]["name"];		
		$temp = $_FILES["archivo"]["tmp_name"];
		$size = $_FILES["archivo"]["size"];
		$type = $_FILES["archivo"]["type"];
		$extensiones = array("gif","jpg","pdf","doc","docx");
		
		if ($name) {
			$name=$id_informe."_".$name;
			$name = strtolower($name);
			$ext = substr($name,-3);
		 	$ret = FileUpload($temp,$size,$name,$type,$max_file_size,$path,"",$extensiones,"",1,0);
			if ($ret["error"] != 0) {
				Error("No se pudo subir el archivo");
			}
		$num=$numero +1;
		$id_user=$_ses_user['id'];
		$fecha_sub=date("Y-m-d");
		//*******************consulto por si ya esta grabado el mismo informe
		
		$qu="select nextval('leche.archivo_fichero_id_ar_fic_seq') as id_ar_fic";
				$id_ar_fic=sql($qu,"error al ejecutar nextval") or fin_pagina();
				$id_ar_fic=$id_ar_fic->fields['id_ar_fic'];	
				
				$query="insert into leche.archivo_fichero
				        (id_ar_fic,id_informe,nombre,id_user,fecha_sub,num,size,tipo)
				        values
				        ($id_ar_fic,'$id_informe','$name',$id_user,'$fecha_sub','1','$size','$type')";
				sql($query, "Error al Grabar Archivo del Social") or fin_pagina();	   
 	//}	
	} else echo "entro por Vacio";
/*  ----------------------Fin de Ingreso de archivo---------------------------------------   */		
		

$db->CompleteTrans();  
}else $accion="El informe ya posee una respuesta";
}


echo $html_header;
?>
<script>
</script>
<form name='form1' action='comprobante_nuevo_archivo.php' method='POST' enctype='multipart/form-data'>

<?echo "<center><b><font size='+2' color='red'>$accion1</font></b></center>";?>
<?echo "<center><b><font size='+1' color='blue'>$accion</font></b></center>";?>
<input type="hidden" name="id_informe" value="<?=$id_informe?>">
<input type="hidden" name="id" value="<?=$id?>">
<input type="hidden" name="archivo" value="<?=$archivo?>">
<input type="hidden" name="entidad_alta" value="<?=$entidad_alta?>">
<table width="100%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">

			 <tr align="center" id="sub_tabla">
			 	<td id="mo" colspan="3">	
			 		Cargar Archivo Complementario del Informe Social	 		
			 	</td>
			 </tr>
			 <tr><td class="bordes" align="center"><table>
						<tr>
							<td align="right">
								<b>Cargar Archivo:</b>
							</td>
							<td align="left">
								<input type=file name=archivo style="width=350px">
							</td> 
						</tr>				 
					</table></td></tr>
					<tr><td><table align="center" width="100%" class="bordes" >
						 <tr>
						  	<td align="center" colspan="2">		      
							    <input type="submit" name="guardar" value="Guardar" title="Guardar" Style="width=130px" >
							    <input type="button" name="cancelar_editar" value="Cancelar" title="Cancela Edicion" disabled style="width=130px" onclick="document.location.reload()">	
							    	      
						    </td>
						 </tr> 
						  <tr align="center">
	   <td>
	   	<?  $ref = encode_link("comprobante_admin_leche.php",array('id'=>$id,'entidad_alta'=>$entidad_alta)); ?>
	     <input type=button name="cerrar" value="Cerrar" onclick="window.close('<?=$ref?>')" title="Cerrar ventana" style="width=150px">     
	   </td>
	  </tr>		
					</table></td></tr>	
			 <tr><td>
	 
					
			</table></td></tr>	

	
	 
  
</table>
    
</form>
<?=fin_pagina();// aca termino ?>
