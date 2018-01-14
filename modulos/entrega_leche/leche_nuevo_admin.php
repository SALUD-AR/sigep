<?

require_once ("../../config.php");


extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

if ($_POST['guardar_editar']=="Guardar"){
   $db->StartTrans();
      
   $apellido=$_POST['apellido'];
   $nombre =$_POST['nombre'];
   $fecha_nac=Fecha_db($_POST['fecha_nac']);
   $documento =$_POST['documento'];
   $domicilio =$_POST['domicilio']; 
   $sexo =$_POST['sexo']; 
        
   $query="update leche.beneficiarios set 
            apellido='$apellido',
			nombre ='$nombre',
			fecha_nac='$fecha_nac',
			documento = '$documento',
			domicilio ='$domicilio',      
			sexo ='$sexo'      
             
             where id_beneficiarios=$id_planilla";

   sql($query, "Error al actualizar") or fin_pagina();
    $db->CompleteTrans(); 
        
   $accion="Los datos se actualizaron";  
}

if ($_POST['guardar']=="Guardar"){
   
   $db->StartTrans();         
    
   $apellido=$_POST['apellido'];
   $nombre =$_POST['nombre'];
   $fecha_nac=Fecha_db($_POST['fecha_nac']);
   $documento =$_POST['documento'];
   $domicilio =$_POST['domicilio'];  
   $sexo = $_POST['sexo'];  
   
   // Controlo que la persona no este cargada en NACER.SMIAFILIADOS Y LECHE.BENEFICARIOS
   
$q_smi="SELECT
		nacer.smiafiliados.afidni
		FROM
		nacer.smiafiliados
		WHERE
		afidni ='$documento'";
$res_smi=sql($q_smi)or fin_pagina();

$q_ben="SELECT
		leche.beneficiarios.documento
		FROM
		leche.beneficiarios
		WHERE
		documento ='$documento'";
 $res_ben=sql($q_ben)or fin_pagina();
 
 $q_benuad="SELECT
		uad.beneficiarios.numero_doc
		FROM
		uad.beneficiarios
		WHERE
		numero_doc ='$documento'";
 $res_benuad=sql($q_benuad)or fin_pagina();

if ($res_smi->RecordCount()==0 and $res_ben->RecordCount()==0 /*and $res_benuad->RecordCount()==0*/) {
 
   $q="select nextval('leche.beneficiarios_id_beneficiarios_seq') as id_planilla";
    $id_planilla=sql($q) or fin_pagina();
    $id_planilla=$id_planilla->fields['id_planilla'];   
 
    $query="insert into leche.beneficiarios
             (id_beneficiarios,apellido,nombre,fecha_nac,documento,domicilio,sexo)
             values
             ('$id_planilla','$apellido','$nombre','$fecha_nac','$documento','$domicilio','$sexo')";

    sql($query, "Error al insertar la Planilla") or fin_pagina();
    
     $sql_1="select
				  nacer.smiafiliados.afidni 
				  from nacer.smiafiliados
				  where nacer.smiafiliados.afidni='$documento'";
	 $res_sql1=sql($sql_1,"Error en la consulta 1") or fin_pagina();
	 if($res_sql1->RecordCount()==0 and $res_benuad->RecordCount()==0)  {
	 		$accion="Dato Guardado.";
	 		$accion="La persona no esta en el Padron de Plan Nacer, Le sugerimos incorporarla";
	 }
	else
		$accion="Se guardo el Beneficiario";    
	 
    $db->CompleteTrans();  
}else $accion="El Beneficiario ya se encuentra inscripto";

      
}//de if ($_POST['guardar']=="Guardar nuevo Muleto")

if ($id_planilla) {
$query="SELECT 
  *
FROM
  leche.beneficiarios  
  where id_beneficiarios=$id_planilla";

$res_factura=sql($query, "Error al traer el Comprobantes") or fin_pagina();

  $apellido=$res_factura->fields['apellido'];
  $nombre =$res_factura->fields['nombre'];
  $fecha_nac =$res_factura->fields['fecha_nac'];
  $documento =$res_factura->fields['documento'];
  $domicilio =$res_factura->fields['domicilio'];  
  $sexo =$res_factura->fields['sexo'];  


}
echo $html_header;
?>
<script>
//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos()
{ 
 
 
 if(document.all.apellido.value==""){
  alert('Debe Ingresar un apellido');
  return false;
 }
 if(document.all.nombre.value==""){
  alert('Debe Ingresar un nombre');
  return false;
 }

 if(document.all.fecha_nac.value==""){
  alert('Debe Ingresar una Fecha de Nacimiento');
  return false;
 }
 
 if(document.all.documento.value==""){
  alert('Debe Ingresar un documento');
  return false;
 }
 if(document.all.domicilio.value==""){
  alert('Debe Ingresar un domicilio');
  return false;
 }
 
  
}//de function control_nuevos()

function editar_campos()
{	
	document.all.apellido.readOnly=false;
	document.all.nombre.readOnly=false;
	document.all.documento.readOnly=false;
	document.all.domicilio.readOnly=false;	
	document.all.sexo.disabled=false;
	
	document.all.cancelar_editar.disabled=false;
	document.all.guardar_editar.disabled=false;
	document.all.editar.disabled=true;
 	return true;
}//de function control_nuevos()


</script>

<form name='form1' action='leche_nuevo_admin.php' method='POST'>
<input type="hidden" value="<?=$id_planilla?>" name="id_planilla">
<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>
<table width="85%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
    	<?
    	if (!$id_planilla) {
    	?>  
    	<font size=+1><b>Nuevo Dato</b></font>   
    	<? }
        else {
        ?>
        <font size=+1><b>Dato</b></font>   
        <? } ?>
       
    </td>
 </tr>
 <tr><td>
  <table width=90% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b> Descripción del Beneficiario</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
         <tr>	           
           <td align="center" colspan="2">
            <b> Número del Dato: <font size="+1" color="Red"><?=($id_planilla)? $id_planilla : "Nuevo Dato"?></font> </b>
           </td>
         </tr>
         <tr>	           
           <td align="center" colspan="2">
             <b><font size="2" color="Red">Nota: Los valores numericos se ingresan SIN separadores de miles, y con "." como separador DECIMAL</font> </b>
           </td>
         </tr>
         
         </tr>
                           
         <tr>
         	<td align="right">
         	  <b>Apellido:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$apellido?>" name="apellido" <? if ($id_planilla) echo "readonly"?>>
            </td>
         </tr> 
         
         <tr>
         	<td align="right">
         	  <b>Nombre:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$nombre?>" name="nombre" <? if ($id_planilla) echo "readonly"?>>
            </td>
         </tr> 
         
         <tr>
			<td align="right">
				<b>Fecha Nacimiento:</b>
			</td>
		    <td align="left">
		    	<?$fecha_comprobante=date("d/m/Y");?>
		    	 <input type=text id=fecha_nac name=fecha_nac value='<?=fecha($fecha_nac);?>' size=15 readonly>
		    	 <?=link_calendario("fecha_nac");?>					    	 
		    </td>		    
		</tr>		
         
         <tr>
         	<td align="right">
         	  <b>Número de Documento:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$documento?>" name="documento" <? if ($id_planilla) echo "readonly"?>><font color="Red">Sin Puntos</font>
            </td>
         </tr> 
         
         <tr>
         	<td align="right">
         	  <b>Domicilio:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$domicilio?>" name="domicilio" <? if ($id_planilla) echo "readonly"?>>
            </td>
         </tr>  
		
         <tr>
         <td align="right">
				<b>Sexo:</b>
			</td>
			<td align="left">			 	
			 <select name=sexo Style="width=257px" <?if ($id_planilla) echo "disabled"?>>
			  <option value=F <?if ($sexo=='F') echo "selected"?>>Femenino</option>
			  <option value=M <?if ($sexo=='M') echo "selected"?>>Masculino</option>			  		  
			 </select>
			</td>
         </tr>        
        </table>
      </td>      
     </tr> 
   

   <?if (!($id_planilla)){?>
	 
	 <tr id="mo">
  		<td align=center colspan="2">
  			<b>Guardar Beneficiario</b>
  		</td>
  	</tr>  
      <tr align="center">
       <td>
        <input type='submit' name='guardar' value='Guardar' onclick="return control_nuevos()" title="Guardar datos">
       </td>
      </tr>
    <?}?>
     
 </table>           
<br>
<?if ($id_planilla){?>
<table class="bordes" align="center" width="100%">
		 <tr align="center" id="sub_tabla">
		 	<td>	
		 		Editar DATO
		 	</td>
		 </tr>
		 
		 <tr>
		    <td align="center">
		      <input type=button name="editar" value="Editar" onclick="editar_campos()" title="Edita Campos" style="width=130px"> &nbsp;&nbsp;
		      <input type="submit" name="guardar_editar" value="Guardar" title="Guarda" disabled style="width=130px" onclick="return control_nuevos()">&nbsp;&nbsp;
		      <input type="button" name="cancelar_editar" value="Cancelar" title="Cancela" disabled style="width=130px" onclick="document.location.reload()">		   
		       <?
        $q_benuad="SELECT
		uad.beneficiarios.numero_doc as afidni
		FROM
		uad.beneficiarios
		WHERE
		numero_doc ='$documento'";
 	//$res_benuad=sql($q_benuad)or fin_pagina();
	 $res_sql2=sql($q_benuad,"Error en la consulta $q_benuad") or fin_pagina();
        
        if ($res_sql2->RecordCount()==0){
        	$ref = encode_link("../inscripcion/ins_admin_old.php",array("id_planilla"=>$res_sql2->fields['afidni'])); ?>
        	<script>
        	alert ("Recuerde Inscribir al Plan Nacer, haga clic en Inscribir")
        	</script>
			<input type=button name="inscribir" value="Inscribir" onclick="document.location='<?=$ref?>'"title="Incripcion como Beneficiario de Plan Nacer" style="width=150px">    
														
           <?}?>   
		      </td>
		 </tr> 
	 </table>	
	 <br>
	 <?}?>
 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
     <input type=button name="volver" value="Volver" onclick="document.location='listado_beneficiarios_leche.php'"title="Volver al Listado" style="width=150px">     
   </td>
  </tr>
 </table></td></tr> 
 
 
 </table>
 </form>
 
 <?=fin_pagina();// aca termino ?>