<?

require_once ("../../config.php");


extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

if($marcar1=="True"){
	 $db->StartTrans();
	$query="delete from nacer.mail_efe_conv
             where id_mail_efe_conv=$id_mail_efe_conv";

    sql($query, "Error al eliminar") or fin_pagina();
    $accion="Se elimino el MAIL: $id_mail_efe_conv."; 
    $db->CompleteTrans();   
}

if ($_POST['guardar']=="Guardar"){
	
	$cuie=$_POST['cuie'];
	$descripcion=$_POST['descripcion'];
	$mail=$_POST['mail'];
	    $db->StartTrans();
		$q="select nextval('nacer.mail_efe_conv_id_mail_efe_conv_seq') as id_comprobante";
	    $id_comprobante=sql($q) or fin_pagina();
	    $id_comprobante=$id_comprobante->fields['id_comprobante'];	
	    $query="insert into nacer.mail_efe_conv
	             (id_mail_efe_conv,cuie,descripcion,mail)
	             values
	             ($id_comprobante,'$cuie','$descripcion','$mail')";	
	    sql($query, "Error al insertar el comprobante") or fin_pagina();	    
	    $accion="Se guardo el Mail Numero: $id_comprobante";
	    $db->CompleteTrans();   
	        
}//de if ($_POST['guardar']=="Guardar nuevo Muleto")

echo $html_header;
?>
<script>
//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos_ingresos()
{
  if(document.all.descripcion.value==""){
  alert('Debe Ingresar Descripcion');
  return false;
  }
 if(document.all.mail.value==""){
  alert('Debe Ingresar un Mail');
  return false;
 }
 
 if (confirm('Esta Seguro que Desea Guardar?'))return true;
 else return false;	
}
 

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
}
</script>

<form name='form1' action='administra_mail.php' method='POST'>

<?echo "<center><b><font size='+2' color='red'>$accion</font></b></center>";?>
<input type="hidden" name="cuie" value="<?=$cuie?>">
<input type="hidden" name="nombre" value="<?=$nombre?>">

<table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 
 <tr><td>     
	 <table class="bordes" align="center" width="70%">
	 	<tr>
      <td id=mo colspan="2">
      <font size="+1"> <b> Efector: <?=$nombre?> - CUIE: <?=$cuie?></b></font>
      </td>
     </tr>
     <tr>
		 <tr align="center" id="sub_tabla">
		 	<td colspan="2">	
		 		Nuevo Mail <font color="red">Debe Ingresar 1 (UN) MAIL por REGISTRO</font>
		 	</td>
		 </tr>
		 <tr><td class="bordes"><table>
			 <tr>
				 <td>
					  <tr>
					    <td align="right">
					    	<b>Descripcion:</b>
					    </td>
					    <td align="left">		          			
				 			<input type="text" name="descripcion" value="" size=30 align="right">
					    </td>
					 </tr>
					 
					 <tr>
					    <td align="right">
					    	<b>Mail:</b>
					    </td>
					    <td align="left">		          			
				 			<input type="text" name="mail" value="" size=30 align="right">
					    </td>
					 </tr>
					 		 					 
				  </td>
			 </tr>
		 </table></td></tr>	 
		 <tr>
		  	<td align="center" colspan="2" class="bordes">		      
		    	<input type="submit" name="guardar" value="Guardar" title="Guardar" Style="width=300px" onclick="return control_nuevos_ingresos()">
		    </td>
		 </tr> 
	 </table>	
 </td></tr>
 
<?//tabla de comprobantes
$sql="select * from nacer.mail_efe_conv
	 where cuie='$cuie'";
$res_comprobante=sql($sql,"<br>Error al traer los comprobantes<br>") or fin_pagina();
?>
<tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Ingresos" align="left" style="cursor:hand;" onclick="muestra_tabla(document.all.prueba_vida,2);" >
	   LISTADO DE MAIL CARGADOS
	</td>	  	  
	</tr>
</table></td></tr>
<tr><td><table id="prueba_vida" border="1" width="100%" style="display:none;border:thin groove">
	<?if ($res_comprobante->RecordCount()==0){?>
	 <tr>
	  <td align="center">
	   <font size="3" color="Red"><b>No existen Ingresos para este Efector</b></font>
	  </td>
	 </tr>
	 <?}
	 else{	 	
	 	?>
	 	<tr id="sub_tabla">		 	    
	 		<td width="5%">ID</td>
	 		<td width="15%">Descripcion</td>
	 		<td width="15%">Mail</td>	 		
	 		<td width="15%">Eliminar</td>	 		
	 	</tr>
	 	<?
	 	$res_comprobante->movefirst();
	 	while (!$res_comprobante->EOF) {
	 		 $ref1 = encode_link("administra_mail.php",array("id_mail_efe_conv"=>$res_comprobante->fields['id_mail_efe_conv'],"marcar1"=>"True","cuie"=>$cuie,"nombre"=>$nombre));
             $id_mail_efe_conv=$res_comprobante->fields['id_mail_efe_conv'];
             $onclick_eliminar="if (confirm('Esta Seguro que Desea Eliminar Mail $id_mail_efe_conv ?')) location.href='$ref1'
            						else return false;	";
	 		?>
	 		<tr <?=atrib_tr()?>>	 			
		 		<td ><?=$res_comprobante->fields['id_mail_efe_conv']?></td>
		 		<td ><?=$res_comprobante->fields['descripcion']?></td>
		 		<td ><?=$res_comprobante->fields['mail']?></td>		 				
		 		<td onclick="<?=$onclick_eliminar?>" align="center"><img src='../../imagenes/salir.gif' style='cursor:hand;'></td>		 		
		 	</tr>	
		 	
	 		<?$res_comprobante->movenext();
	 	}
	 }?>
</table></td></tr>

</table>

</form>
<?=fin_pagina();// aca termino ?>
