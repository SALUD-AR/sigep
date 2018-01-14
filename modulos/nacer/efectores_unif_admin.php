<?

require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

if ($_POST['guardar_editar']=="Guardar"){
   $db->StartTrans();
      
   $fecha_modificacion=date("Y-m-d");
   $usuario=$_ses_user['name'];
   $fecha_comp_ges_db=Fecha_db($fecha_comp_ges);
   $fecha_fin_comp_ges_db=Fecha_db($fecha_fin_comp_ges);
   $fecha_tercero_admin_db=Fecha_db($fecha_tercero_admin);
   $fecha_fin_tercero_admin_db=Fecha_db($fecha_fin_tercero_admin);
   ($fecha_adenda_per=='')?$fecha_adenda_per='1980-01-01':$fecha_adenda_per=Fecha_db($fecha_adenda_per);
   
   $query="update nacer.efe_conv set    
   			 nombre='$nombre', 
   			 cod_siisa='$cod_siisa', 
   			 cod_remediar='$cod_remediar', 
   			 domicilio='$domicilio', 
             cod_pos='$cod_pos', 
             cuidad='$cuidad', 
             referente='$referente', 
             tel='$tel',             
             com_gestion='$com_gestion',
             com_gestion_firmante='$com_gestion_firmante',
             tercero_admin='$tercero_admin',
             tercero_admin_firmante='$tercero_admin_firmante',
             com_gestion_firmante_actual='$com_gestion_firmante_actual',
             dni_firmante_actual='$dni_firmante_actual',
             fecha_modificacion='$fecha_modificacion',
             usuario='$usuario',
             fecha_comp_ges='$fecha_comp_ges_db',
             fecha_fin_comp_ges='$fecha_fin_comp_ges_db',
             com_gestion_pago_indirecto='$com_gestion_pago_indirecto',
             fecha_tercero_admin='$fecha_tercero_admin_db',
             fecha_fin_tercero_admin='$fecha_fin_tercero_admin_db',
             n_2008='$n_2008',
             n_2009='$n_2009',
             id_nomenclador_detalle='$nomenclador_detalle',
             id_zona_sani='$id_zona_sani',
             incentivo='$incentivo',
             per_alta_com='$per_alta_com',
             adenda_per='$adenda_per',
             fecha_adenda_per='$fecha_adenda_per',
             categoria_per='$categoria_per',
             conv_sumar='$conv_sumar'
            
             where id_efe_conv=$id_efe_conv";

   sql($query, "Error al insertar/actualizar el efector") or fin_pagina();
    
    $db->CompleteTrans();    
   
	$paracc='seba_cyb1202@hotmail.com';
	$asunto='MODIFICACION EFECTOR';
	$contenido="El Efector: $nombre. CUIE: $cuie fue Modificado por el Usuario: $usuario. Por favor revisar y actualizar en la tabla de Efectores";
	enviar_mail($para,$paracc,$parabcc,$asunto,$contenido,'','');
	
   $accion="Los datos se actualizaron se Envio mail";  
   
}

if ($id_efe_conv) {
$query="SELECT 
  efe_conv.*,dpto.nombre as dpto_nombre
FROM
  nacer.efe_conv 
  left join nacer.dpto on dpto.codigo=efe_conv.departamento   
  where id_efe_conv=$id_efe_conv";

$res_factura=sql($query, "Error al traer el Efector") or fin_pagina();

$cuie=$res_factura->fields['cuie'];
$cod_siisa=$res_factura->fields['cod_siisa'];
$cod_remediar=$res_factura->fields['cod_remediar'];
$nombre=$res_factura->fields['nombre'];
$domicilio=$res_factura->fields['domicilio'];
$departamento=$res_factura->fields['dpto_nombre'];
$localidad=$res_factura->fields['localidad'];
$cod_pos=$res_factura->fields['cod_pos'];
$cuidad=$res_factura->fields['cuidad'];
$referente=$res_factura->fields['referente'];
$tel=$res_factura->fields['tel'];
$com_gestion=$res_factura->fields['com_gestion'];
$com_gestion_firmante=$res_factura->fields['com_gestion_firmante'];
$fecha_comp_ges=$res_factura->fields['fecha_comp_ges'];
$fecha_fin_comp_ges=$res_factura->fields['fecha_fin_comp_ges'];
$com_gestion_pago_indirecto=$res_factura->fields['com_gestion_pago_indirecto'];
$tercero_admin=$res_factura->fields['tercero_admin'];
$tercero_admin_firmante=$res_factura->fields['tercero_admin_firmante'];
$fecha_tercero_admin=$res_factura->fields['fecha_tercero_admin'];
$fecha_fin_tercero_admin=$res_factura->fields['fecha_fin_tercero_admin'];
$com_gestion_firmante_actual=$res_factura->fields['com_gestion_firmante_actual'];
$dni_firmante_actual=$res_factura->fields['dni_firmante_actual'];
$n_2008=$res_factura->fields['n_2008'];
$n_2009=$res_factura->fields['n_2009'];
$id_nomenclador_detalle=$res_factura->fields['id_nomenclador_detalle'];
$id_zona_sani=$res_factura->fields['id_zona_sani'];
$incentivo=$res_factura->fields['incentivo'];
$per_alta_com=$res_factura->fields['per_alta_com'];
$adenda_per=$res_factura->fields['adenda_per'];
$fecha_adenda_per=$res_factura->fields['fecha_adenda_per'];
$categoria_per=$res_factura->fields['categoria_per'];
$conv_sumar=$res_factura->fields['conv_sumar'];
}
echo $html_header;
?>
<script>

function control_nuevos()
{ 
	 
	 if(document.all.fecha_comp_ges.value==""){
	  alert('Debe Ingresar una Fecha Compromiso de Gestion');
	  return false;
	 }
	 if(document.all.fecha_fin_comp_ges.value==""){
	  alert('Debe Ingresar una Fecha Fin Compromiso de Gestion');
	  return false;
	 }
	 if(document.all.fecha_tercero_admin.value==""){
	  alert('Debe Ingresar una Fecha Tercero Administrador');
	  return false;
	 }
	 if(document.all.fecha_fin_tercero_admin.value==""){
	  alert('Debe Ingresar una Fecha Fin Tercero Administrador');
	  return false;
	 }
	 if(document.all.id_zona_sani.value=="-1"){
	  alert('Debe Seleccionar una zona Sanitaria (Sino figura ninguna agregar en la tabla nacer.zona_sani)');
	  return false;
	 } 
}

function editar_campos()
{
	document.all.nombre.readOnly=false;
	document.all.cod_siisa.readOnly=false;
	document.all.cod_remediar.readOnly=false;
	document.all.domicilio.readOnly=false;
	document.all.cod_pos.readOnly=false;
	document.all.cuidad.readOnly=false;
	document.all.referente.readOnly=false;
	document.all.tel.readOnly=false;
	document.all.com_gestion_firmante.readOnly=false;
	document.all.tercero_admin.readOnly=false;
	document.all.tercero_admin_firmante.readOnly=false;
	document.all.com_gestion_firmante_actual.readOnly=false;
	document.all.dni_firmante_actual.readOnly=false;
	document.all.com_gestion.disabled=false;
	document.all.com_gestion_pago_indirecto.disabled=false;
	document.all.nomenclador_detalle.disabled=false;
	document.all.id_zona_sani.disabled=false;
	document.all.incentivo.disabled=false;
	document.all.per_alta_com.disabled=false;
	document.all.adenda_per.disabled=false;
	document.all.categoria_per.disabled=false;
		
	document.all.cancelar_editar.disabled=false;
	document.all.guardar_editar.disabled=false;
	document.all.editar.disabled=true;
 	return true;
}//de function control_nuevos()


</script>

<form name='form1' action='efectores_unif_admin.php' method='POST'>
<input type="hidden" value="<?=$id_efe_conv?>" name="id_efe_conv">
<input type="hidden" value="<?=$cuie?>" name="cuie">
<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>
<table width="85%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
    	<font size=+1><b>Efector</b></font>        
    </td>
 </tr>
 <tr><td>
  <table width=90% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b> Descripción del Efector</b>
      </td>
     </tr>
     <tr>
       <td>
        <table width=90% align="center">
         <tr>	           
           <td align="center" colspan="2">
            <b> CUIE: <font size="+1" color="Red"><?=$cuie?></font> </b>
           </td>
         </tr>
         <tr>	           
           <td align="center" colspan="2">
             <b><font size="2" color="Red">Nota: Los valores numericos se ingresan SIN separadores de miles, y con "." como separador DECIMAL</font> </b>
           </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Codigo SIISA:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$cod_siisa?>" name="cod_siisa" readonly>
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Codigo Remediar:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$cod_remediar?>" name="cod_remediar" readonly>
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Nombre:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$nombre?>" name="nombre" readonly>
            </td>
         </tr>
         
         <tr>	           
           
         <tr>
         <td align="right">
				<b>Domicilio:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$domicilio?>" name="domicilio" readonly>
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Departamento:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$departamento?>" name="departamento" readonly>
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Localidad:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$localidad?>" name="localidad" readonly>
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Codigo Postal:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$cod_pos?>" name="cod_pos" readonly>
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Cuidad:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$cuidad?>" name="cuidad" readonly>
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Referente:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$referente?>" name="referente" readonly>
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Telefono:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$tel?>" name="tel" readonly>
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Mail:</b>
			</td>
			<?   $sql="select * from nacer.mail_efe_conv where cuie = '$cuie'";
			     $result_mail=sql($sql,'Error');
			     $result_mail->movefirst();
			     $contenido_mail='';
			     while (!$result_mail->EOF) {
			     	$contenido_mail.=$result_mail->fields['mail'].', ';
			     	$result_mail->MoveNext();
			     }
			     ?>  
			<td align="left">			  		 
              <input type="text" size="40" value="<?=$contenido_mail?>" name="mail" readonly>
              <?$ref = encode_link("administra_mail.php",array("cuie"=>$cuie,"nombre"=>$nombre));?>
			  <input type="button" name="mail" value="Mail"  onclick="window.open('<?=$ref?>')">            
             </td>
         </tr>
         
         <tr>
         
         <td align="right">
				<b>Convenio Programa SUMAR:</b>
			</td>
			<td align="left">			 	
			 <select name=conv_sumar Style="width=257px" <?if ($id_planilla) echo "disabled"?>>
			  <option value=-1>Seleccione</option>
			  <option value='t' <?if ($conv_sumar=='t') echo "selected"?>>SI</option>
			  <option value='f' <?if ($conv_sumar=='f') echo "selected"?>>NO</option>
			  			 </select>
			</td>
         </tr>
					    <td align="right">
					    	<b>Nomenclador en Uso:</b>
					    </td>
					    <td align="left">		         			
				 			<select name=nomenclador_detalle Style="width=257px" disabled>
			     			<?$sql="select * from facturacion.nomenclador_detalle
							        ORDER BY facturacion.nomenclador_detalle.id_nomenclador_detalle ASC";
			     			  $res=sql($sql) or fin_pagina();
			                 while (!$res->EOF){ 
			                 	$id_nomenclador_detalle_1=$res->fields['id_nomenclador_detalle'];
			                 	$descripcion=$res->fields['descripcion'];?>
			                   <option value=<?=$id_nomenclador_detalle_1;if ($id_nomenclador_detalle==$id_nomenclador_detalle_1) echo " selected"?> >
			                   	<?=$descripcion?>
			                   </option>
								<?$res->movenext();
			                 }?>
			      			</select>
					    </td>
					 </tr>
         
         <tr>
         <td align="right">
				<b>Compromiso de Gestion:</b>
			</td>
			<td align="left">
			  <select name=com_gestion Style="width=257px" disabled>			  
			  <option value=VERDADERO <?if ($com_gestion=='VERDADERO') echo "selected"?>>VERDADERO</option>
			  <option value=FALSO <?if ($com_gestion=='FALSO') echo "selected"?>>FALSO</option>			  
			 </select>              
            </td>
         </tr>       
         
         <tr>
         <td align="right">
				<u><b>Referente con Addenda:</b><u>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$com_gestion_firmante_actual?>" name="com_gestion_firmante_actual" readonly>
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<u><b>DNI Referente con Addenda:</b><u>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$dni_firmante_actual?>" name="dni_firmante_actual" readonly>
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Compromiso de Gestion Firmante:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$com_gestion_firmante?>" name="com_gestion_firmante" readonly>
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Fecha del Compromiso de Gestion:</b>
			</td>
			<td align="left">		 
              <input type="text" size="35" value="<?=fecha($fecha_comp_ges)?>" name="fecha_comp_ges" readonly>
              <?=link_calendario("fecha_comp_ges");?>
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Fecha Fin del Compromiso de Gestion:</b>
			</td>
			<td align="left">		 
              <input type="text" size="35" value="<?=fecha($fecha_fin_comp_ges)?>" name="fecha_fin_comp_ges" readonly>
              <?=link_calendario("fecha_fin_comp_ges");?>
            </td>
         </tr>
         
         <tr>
         
         <tr>
         <td align="right">
				<b>Compromiso de Gestion Pago Indirecto:</b>
			</td>
			<td align="left">
			  <select name=com_gestion_pago_indirecto Style="width=257px" disabled>			  
			  <option value=VERDADERO <?if ($com_gestion_pago_indirecto=='VERDADERO') echo "selected"?>>VERDADERO</option>
			  <option value=FALSO <?if ($com_gestion_pago_indirecto=='FALSO') echo "selected"?>>FALSO</option>			  
			 </select>              
            </td>
         </tr>  
                                   
         <tr>
         <td align="right">
				<b>Tercero Administrador:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$tercero_admin?>" name="tercero_admin" readonly>
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Tercero Administrador Firmante:</b>
			</td>
			<td align="left">		 
              <input type="text" size="40" value="<?=$tercero_admin_firmante?>" name="tercero_admin_firmante" readonly>
            </td>
         </tr>
         
         <tr>
         
         <td align="right">
				<b>Fecha Tercero Administrador:</b>
			</td>
			<td align="left">		 
              <input type="text" size="35" value="<?=fecha($fecha_tercero_admin)?>" name="fecha_tercero_admin" readonly>
              <?=link_calendario("fecha_tercero_admin");?>
            </td>
         </tr>   
         
         <td align="right">
				<b>Fecha Fin Tercero Administrador:</b>
			</td>
			<td align="left">		 
              <input type="text" size="35" value="<?=fecha($fecha_fin_tercero_admin)?>" name="fecha_fin_tercero_admin" readonly>
              <?=link_calendario("fecha_fin_tercero_admin");?>
            </td>
         </tr> 
          
         
         <tr>
         
					    <td align="right">
					    	<b>Zona Sanitaria:</b>
					    </td>
					    <td align="left">		          			
				 			<select name=id_zona_sani Style="width=257px" disabled>
			     			<?
			                 $sql="select * from nacer.zona_sani";
			                 $res=sql($sql) or fin_pagina();
			                 while (!$res->EOF){ 
			                 	$id_nomenclador_detalle_1=$res->fields['id_zona_sani'];
			                 	$descripcion=$res->fields['nombre_zona'];
			                 	
			                 ?>
			                   <option value=<?=$id_nomenclador_detalle_1;if ($id_zona_sani==$id_nomenclador_detalle_1) echo " selected"?> >
			                   	<?=$descripcion?>
			                   </option>
			                 <?
			                 $res->movenext();
			                 }
			                 ?>
			      			</select>
					    </td>
		 </tr>
		 
		 <tr>
         <td align="right">
				<b>Incentivo:</b>
			</td>
			<td align="left">
			  <select name=incentivo Style="width=257px" disabled>			  
			  <option value=s <?if ($incentivo=='s') echo "selected"?>>SI</option>
			  <option value=n <?if ($incentivo=='n') echo "selected"?>>NO</option>			  
			 </select>              
            </td>
         </tr>   
         
         <tr>
         <td align="right">
				<b>Usa Alta Complejidad:</b>
			</td>
			<td align="left">
			  <select name=per_alta_com Style="width=257px" disabled>	
			  <option value=-1 <?if ($categoria_per=='-1') echo "selected"?>>Sin Dato</option>		  
			  <option value=SI <?if ($per_alta_com=='SI') echo "selected"?>>SI</option>
			  <option value=NO <?if ($per_alta_com=='NO') echo "selected"?>>NO</option>			  
			 </select>              
            </td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Tiene Adenda Alta Complejidad:</b>
			</td>
			<td align="left">
			  <select name=adenda_per Style="width=257px" disabled>	
			  <option value=-1 <?if ($categoria_per=='-1') echo "selected"?>>Sin Dato</option>		  
			  <option value=SI <?if ($adenda_per=='SI') echo "selected"?>>SI</option>
			  <option value=NO <?if ($adenda_per=='NO') echo "selected"?>>NO</option>			  
			 </select>              
            </td>
         </tr>
         
         <tr>
         
         <td align="right">
				<b>Fecha Adenda:</b>
			</td>
			<td align="left">		 
              <input type="text" size="35" value="<?=fecha($fecha_adenda_per)?>" name="fecha_adenda_per" readonly>
              <?=link_calendario("fecha_adenda_per");?>
            </td>
         </tr>
          
         <tr>
         <td align="right">
				<b>Categoria del Efector:</b>
			</td>
			<td align="left">
			  <select name=categoria_per Style="width=257px" disabled>			  
			  <option value=-1 <?if ($categoria_per=='-1') echo "selected"?>>Sin Dato</option>
			  <option value=NIVELI <?if ($categoria_per=='NIVELI') echo "selected"?>>NIVEL I</option>
			  <option value=NIVELII <?if ($categoria_per=='NIVELII') echo "selected"?>>NIVEL II</option>			  
			  <option value=NIVELIIIA <?if ($categoria_per=='NIVELIIIA') echo "selected"?>>NIVEL III A</option>			  
			  <option value=NIVELIIIB <?if ($categoria_per=='NIVELIIIB') echo "selected"?>>NIVEL III B</option>			  
			 </select>              
            </td>
         </tr>
         <td align="right">
				<b>Firma del Referente:</b>
			</td>
           <td>
           <IMG src='../nacer/firmas/<?=$cuie?>.jpg' height='150' width='200' border='1'>
           </td>
          </table>
      </td>      
     </tr> 
           
 </table>           
<br>
<?if ($id_efe_conv){?>
<table class="bordes" align="center" width="100%">
		 <tr align="center" id="sub_tabla">
		 	<td>	
		 		Editar DATO
		 	</td>
		 </tr>
		 
		 <tr>
		    <td align="center">		      
		      <input type=button name="editar" value="Editar" onclick="editar_campos()" title="Edita Campos" <?=(($_ses_user['name']=="sebastian lohaiza")or($_ses_user['login']=="fer"))?"":"disabled";?> style="width=130px"> &nbsp;&nbsp;
		      <input type="submit" name="guardar_editar" value="Guardar" onclick="control_nuevos()" title="Guarda Muleto" disabled style="width=130px" >&nbsp;&nbsp;
		      <input type="button" name="cancelar_editar" value="Cancelar" title="Cancela Edicion de Muletos" disabled style="width=130px" onclick="document.location.reload()">		      		      
		    </td>
		 </tr> 
	 </table>	
	 <br>
	 <?}?>
 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
     <input type=button name="volver" value="Volver" onclick="document.location='efectores_unif.php'"title="Volver al Listado" style="width=150px">     
   </td>
  </tr>
 </table></td></tr>
 
 
 </table>
 </form>
 
 <?=fin_pagina();// aca termino ?>
