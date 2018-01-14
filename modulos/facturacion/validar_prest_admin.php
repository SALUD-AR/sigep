<?
/*
Author:  Gaby $
$Revision: 1.42 $
$Date: 2010/12/03 6:14:40 $
*/

require_once ("../../config.php");



extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

if ($_POST['guardar_editar']=='Guardar'){
	$db->StartTrans();
   $tipo_iva=strtoupper($tipo_iva);     
   
   $query="UPDATE facturacion.validacion_prestacion set 
		--codigo='$codigo',
		cant_pres_lim='$cant_pres_lim',
		per_pres_limite='$per_pres_limite',
		msg_error='$msg_error'
		--where id_val_pres=$id_val_pres
    where id_nomenclador=$id_nomenclador";	

   sql($query, "Error al insertar/actualizar la Validacion de la Prestacion") or fin_pagina();
 	 
    $db->CompleteTrans();    
   $accion="Los datos se actualizaron";  
}

if ($_POST['guardar']=='Guardar'){
	$db->StartTrans();
	
	$sql_cons= "SELECT 
			 *
			FROM
			  facturacion.validacion_prestacion
			  --where id_nomenclador='$codigo'
        where id_nomenclador=$id_nomenclador";
	 $res_sql=sql($sql_cons, "Error al verificar la Validacion con el codigo") or fin_pagina();
	 
	 $sql_cons= "SELECT 
			 *
			FROM
			  facturacion.nomenclador
			  --where id_nomenclador='$codigo'
        where id_nomenclador=$id_nomenclador";
	 $res_sql_codigo=sql($sql_cons, "Error al verificar la Validacion con el codigo") or fin_pagina();
 	 $codigo1=$res_sql_codigo->fields['codigo'];
    
	if($res_sql->recordCount()==0){
	
    $q="select nextval('facturacion.validacion_prestacion_id_val_pres_seq') as id_val_pres";
    $id_val_pres=sql($q) or fin_pagina();
    $id_val_pres=$id_val_pres->fields['id_val_pres']; 

   $query="insert into facturacion.validacion_prestacion
   	(id_val_pres, codigo, cant_pres_lim, per_pres_limite, msg_error,id_nomenclador)
   	values
  	('$id_val_pres', '$codigo1', '$cant_pres_lim', '$per_pres_limite', '$msg_error','$id_nomenclador')";
	
   sql($query, "Error al insertar/actualizar la Validacion de la Prestacion") or fin_pagina();
 	 
   $accion="Los datos se han guardado correctamente"; 
   
   $db->CompleteTrans();   
	} else { $accion="Ya existe una Validacion con este Codigo"; }
         
}

if ($_POST['borrar']=='Borrar'){

	$query="delete from facturacion.validacion_prestacion
			where id_val_pres=$id_val_pres";
	
	sql($query, "Error al eliminar la Validacion de la Prestacion") or fin_pagina(); 
	
	$accion="Los datos se han borrado";
}

//if ($id_val_pres) {
if ($id_nomenclador) {
$query=" SELECT 
		 *
		FROM
		  facturacion.validacion_prestacion
		  --where id_val_pres=$id_val_pres
      where id_nomenclador=$id_nomenclador";

$res_val=sql($query, "Error al traer la prestacion") or fin_pagina();

//$codigo=$res_val->fields['id_nomenclador'];
$cant_pres_lim=$res_val->fields['cant_pres_lim'];
$per_pres_limite=$res_val->fields['per_pres_limite'];
$msg_error=$res_val->fields['msg_error'];
}
echo $html_header;
?>
<script>
//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos()
{ 
  if(document.all.cant_pres_lim.value==""){
  alert('Debe ingresar la cantidad de prestaciones');
  return false;
 }  
 if(document.all.per_pres_limite.value==""){
  alert('Debe ingresar el Periodo de tiempo Limite');
  return false;
 }  
 if(document.all.msg_error.value==""){
  alert('Debe ingresar el Mensaje de error que desea que muestre el sistema');
  return false;
 }  
  
}//de function control_nuevos()

function editar_campos()
{	
	document.all.codigo.disabled=false;
	document.all.cant_pres_lim.disabled=false;
	document.all.per_pres_limite.disabled=false;
	document.all.msg_error.disabled=false;
	document.all.guardar_editar.disabled=false;
	document.all.cancelar_editar.disabled=false;
	document.all.borrar.disabled=false;
	document.all.guardar.enaible=false;
	return true;
}
//de function control_nuevos()


</script>

<form name='form1' action='validar_prest_admin.php' method='POST'>
<input type="hidden" value="<?=$id_val_pres?>" name="id_val_pres">
<input type="hidden" value="<?=$id_nomenclador?>" name="id_nomenclador">
<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>
<table width="85%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
    	<?
    	if (!$id_iva) {
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
  <table width=100% align="center" >
     <tr>
      <td id=mo colspan="2">
       <b> Planilla de Validacion de Prestaciones</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
         <tr>	           
           <td align="center" colspan="2">
            <b> Número del Dato: <font size="+1" color="Red"><?=($id_val_pres)? $id_val_pres : "Nuevo Dato"?></font> </b>
           </td>
         </tr>
         </table>
         </tr>
        <tr>
         	<td align="right">
         	  <b>Codigo:</b>
         	</td>         	
            <td align='left'>
			              <select name=codigo Style="width=950px" <? if($id_val_pres) echo "disabled"?> >
			              <?$sql= "SELECT id_nomenclador, codigo, nomenclador.descripcion, nomenclador_detalle.descripcion as descripcion_n_d
									FROM  facturacion.nomenclador 
									left join facturacion.nomenclador_detalle using(id_nomenclador_detalle)
        							where codigo!='DIFERENCIA DE NOMENCLADOR' and codigo!='DEB-CRED' and nomenclador.id_nomenclador=$id_nomenclador
        							--order by id_nomenclador_detalle DESC, nomenclador.codigo, descripcion";
												$res_sql=sql($sql) or fin_pagina();
												while (!$res_sql->EOF){ 
												$codigo_aux=$res_sql->fields['id_nomenclador'];
												$codigo_sql=$res_sql->fields['codigo'].'- '.$res_sql->fields['descripcion'].'- '.$res_sql->fields['descripcion_n_d'];
												?>
												<option value='<?=$codigo_aux?>'<?if ($codigo_aux==$codigo) echo "selected"?>><?=$codigo_sql?></option>
												<?
												$res_sql->movenext();
												}
												?>
			              </select>
			        </td>
         </tr> 
         <tr>
         	<td align="right">
         	  <b>Cant. Prestaciones:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$cant_pres_lim;?>" name="cant_pres_lim" <? if ($id_val_pres) echo "disabled"?>>
            </td>
         </tr> 
         <tr>
         	<td align="right">
         	  <b>Periodo de Prestaciones:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$per_pres_limite;?>" name="per_pres_limite" <? if ($id_val_pres) echo "disabled"?>>
            </td>
         </tr> 
         <tr>
         	<td align="right">
         	  <b>Mensaje de Error:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="75" value="<?=$msg_error;?>" name="msg_error" <? if ($id_val_pres) echo "disabled"?>>
            </td>
         
         </tr> 
 </table>           
<br>
<?if ($id_val_pres){?>
<table class="bordes" align="center" width="100%">
		 <tr align="center" id="sub_tabla">
		 	<td>	
		 		Editar DATO
		 	</td>
		 </tr>
		 
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
		      <input type="button" name="cancelar_editar" value="Cancelar" title="Cancela Edicion" style="width=130px" onclick="document.location.reload()">		      
		    </td>
	 
	 <? } ?>
	 


 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
     <input type=button name="volver" value="Volver" onclick="document.location='listado_validar_prest.php'"title="Volver al Listado" style="width=150px">     
     </td>
  </tr>
 </table></td></tr>
 
 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   
  </tr>  
 </table></td></tr>
 
 </table>
 </form>
 
 <?=fin_pagina();// aca termino ?>
