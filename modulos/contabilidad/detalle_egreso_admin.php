<?
require_once ("../../config.php");


extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

if ($_POST['guardar']=="Guardar Egreso"){
    
	$id_servicio1=$_POST['servicio1'];
	$db->StartTrans();
	$query="update contabilidad.egreso set id_servicio=$id_servicio1
			where id_egreso=$id_egreso";
	             	
	 sql($query, "Error al insertar el Egreso") or fin_pagina();	    
	    
	 $accion="Se Modifico el Egreso Numero: $id_egreso";
	 $db->CompleteTrans(); 
		
}

if ($_POST['borrar']=="Borrar Egreso"){
    
	$usuario=$_ses_user['name'];	
    $fecha_hoy=date("Y-m-d");
	$db->StartTrans();
	
	$sql_incentivo="select * from contabilidad.incentivo where id_egreso='$id_egreso'";
    $rs_sql_inc=sql($sql_incentivo,"No se pudo traer los datos de incentivos");
    $id_incentivo=$rs_sql_inc->fields['id_incentivo'];
	
	if ($id_incentivo){
	$sql_update="update contabilidad.incentivo set cumple=0,fecha_autorizacion='$fecha_hoy',usuario='$usuario' where id_incentivo=$id_incentivo";
	$res_sql_update=sql($sql_update,"No se pudo actualizar la tabla de incentivos") or die;

	//borra los montos de los egresos en la tabla de contabilidad.egreso
	//asi mismo quedan indicados en la tabla contabilidad.incentivos
	
	$sql_id_egreso="update contabilidad.egreso set monto_egreso=0,usuario='$usuario',monto_egre_comp=0 where id_egreso='$id_egreso'";
	$res_id_egreso=sql($sql_id_egreso,"no se pudo modificar el registro de egreso") or die;
	
	    
	 $accion="Se Elimino el Egreso Numero: $id_egreso";
	 $db->CompleteTrans(); 
	}
	else {$sql_id_egreso="update contabilidad.egreso set monto_egreso=0,monto_egre_comp=0 where id_egreso='$id_egreso'";
	$res_id_egreso=sql($sql_id_egreso,"no se pudo modificar el registro de egreso") or die;}
	$accion="Se Elimino el Egreso Numero: $id_egreso";
	$db->CompleteTrans(); 
}

echo $html_header;
?>
<script>
//controlan que ingresen todos los datos necesarios par el muleto

 function control_borrar_egresos()
{
  if(document.all.borrar.value=="Borrar Egreso"){
	  if (confirm('Esta seguro que desea ELIMINAR el Egreso'))return true;
	  else return false;
	 }
  
 } 
 	
 function control_modificar_egresos()
 {
   
   if(document.all.guardar.value=="Guardar Egreso"){
 	  if (confirm('Esta seguro que desea MODIFICAR el Egreso'))return true;
 	  else return false;
 	 }
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
function destrabar_ingreso(){
	document.all.monto_egre_comp.readOnly=false;
	document.all.monto_egre_comp.focus();
}
</script>

<form name='form1' action='detalle_egreso_admin.php' method='POST' enctype='multipart/form-data'>
<input type="hidden" value="<?=$numero_factura?>" name="numero_factura">
<input type="hidden" value="<?=$id_egreso?>" name="id_egreso">
<input type="hidden" value="<?=$id_servicio?>" name="id_servicio">
<input type="hidden" value="<?=$id_factura?>" name="id_factura">
<?
$sql_egreso="select * from (
select * from contabilidad.egreso where id_egreso='$id_egreso') as tabla
left join contabilidad.inciso using (id_inciso)";
$res_egreso=sql($sql_egreso,"no se pudo ejecutar la consulta sobre el egreso");

$ins_nombre=$res_egreso->fields['id_inciso'];
$descripcion=$res_egreso->fields['ins_nombre'];
$fecha_egre_comp=$res_egreso->fields['fecha_egre_comp'];
$fecha_egreso=$res_egreso->fields['fecha_egreso'];
$comentario=$res_egreso->fields['comentario'];

$sql_incentivo="select * from contabilidad.incentivo where id_egreso='$id_egreso'";
$rs_sql_inc=sql($sql_incentivo,"No se pudo traer los datos de incentivos");
$id_incentivo=$rs_sql_inc->fields['id_incentivo'];
$id_fact_inc=$rs_sql_inc->fields['id_factura'];
$monto_incent=$rs_sql_inc->fields['monto_incentivo'];


?>

<?echo "<center><b><font size='+2' color='red'>$accion</font></b></center>";?>
<input type="hidden" name="cuie" value="<?=$cuie?>">

<?
$sql_efec="select * from nacer. efe_conv where cuie='$cuie'";
$res_efec=sql($sql_efec,"No se pudieron traer los datos del efector");
$nombre=$res_efec->fields['nombre'];
$domicilio=$res_efec->fields['domicilio'];
$ciudad=$res_efec->fields['ciudad'];

$sql="select monto_egreso from contabilidad.egreso
		where cuie='$cuie'";
$res_egreso=sql($sql,"no puede calcular el saldo");

if ($res_egreso->recordCount()==0){
	$sql="select ingre as total, ingre,egre,deve,egre_comp from
		(select sum (monto_deposito)as ingre from contabilidad.ingreso
		where cuie='$cuie') as ingreso,
		(select sum (monto_egreso)as egre from contabilidad.egreso
		where cuie='$cuie') as egreso,
		(select sum (monto_factura)as deve from contabilidad.ingreso
		where cuie='$cuie') as devengado,
		(select sum (monto_egre_comp)as egre_comp from contabilidad.egreso
		where cuie='$cuie') as egre_comp";

}
else{
$sql="select ingre-egre as total, ingre,egre,deve,egre_comp from
		(select sum (monto_deposito)as ingre from contabilidad.ingreso
		where cuie='$cuie') as ingreso,
		(select sum (monto_egreso)as egre from contabilidad.egreso
		where cuie='$cuie') as egreso,
		(select sum (monto_factura)as deve from contabilidad.ingreso
		where cuie='$cuie') as devengado,
		(select sum (monto_egre_comp)as egre_comp from contabilidad.egreso
		where cuie='$cuie') as egre_comp";
}
$res_saldo=sql($sql,"no puede calcular el saldo")

?>
<table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
     <font size=+1><b>Egreso Numero <?=$id_egreso?></b></font>    
    </td>
 </tr>
 <tr><td>
  <table width=70% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b> Descripción del Efector</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
         <tr>	           
           <td align="center" colspan="2">
            <b> CUIE: <font size="+1" color="Red"><?=$cuie?></font> </b>
           </td>
         </tr>
         <tr>
         	<td align="right">
         	  <b>Nombre:
         	</td>         	
            <td align='left'>
              <input type='text' name='nombre' value='<?=$nombre;?>' size=60 align='right' readonly></b>
            </td>
         </tr>
         <tr>
            <td align="right">
         	  <b> Domicilio:
         	</td>   
           <td  >
             <input type='text' name='domicilio' value='<?=$domicilio;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
          <tr>
           <td align="right">
         	  <b> Ciudad:
         	</td> 
           <td >
             <input type='text' name='ciudad' value='<?=$ciudad;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
          <tr>
			<td align="right"><b>Saldo:</b></td>
			<td align="left">		          			
				<b><font size="+1" color="Blue"><?=number_format($res_saldo->fields['total'],2,',','.')?></font></b>
			</td>
		  </tr>  
            
        </table>
      </td>      
     </tr>
   </table>     
	 <table class="bordes" align="center" width="70%">
		 <tr align="center" id="sub_tabla">
		 	<td colspan="2">	
		 		Datos del Egreso
		 	</td>
		 </tr>
		 <tr><td class="bordes"><table>
			 <tr>
				 <td>				 
					 <tr>
					    <td align="right">
					    	<b>Rubro:</b>
					    </td>
					    <td align="left">
					    <input type=text id=rubro name=rubro value='<?=$descripcion;?>' Style="width=450px" align=right readonly>
				 		</td>
					    
					 </tr>
					  <tr>
					    <td align="right">
					    	<b>Numero de Factura:</b>
					    </td>
					    <td align="left">
					    <? if ($id_fact_inc){
					        $sql="select * from facturacion.factura where id_factura='$id_fact_inc'";
			               	$res_fact=sql($sql,"no se pudo traer los datos de la factura");
			               	$periodo_actual=$res_fact->fields['periodo_actual'];
			               	$monto_prefactura=number_format($res_fact->fields['monto_prefactura'],2,',','.');?>
					    <input type=text id=id_factura name=id_factura value='<?="N°:".$id_fact_inc." - Periodo Prestaciones:".$periodo_actual." - Monto Cuasi:".$monto_prefactura;?>' Style="width=450px" align=right readonly>
				 		<?}
				 		else {?>
				 		<input type=text id=id_factura name=id_factura value='<?="No Corresponde Factura para este Rubro";?>' Style="width=450px" align=right readonly>
				 		<? }?>
				 		</td>
					    
					 </tr>
					 
					 
					 <tr>
					    <td align="right">
					    	<b>Servicio:</b>
					    </td>
					    <td align="left">		          			
				 			<select name=servicio1 Style="width=450px"
				 			onKeypress="buscar_combo(this);"
				 			onblur="borrar_buffer();"
				 			onchange="borrar_buffer();"
           					>
			     			<option value=-1>Seleccione</option>
			                 <?
			                 $sql= "select * from facturacion.servicio order by descripcion";
			                 $res_efectores=sql($sql) or fin_pagina();
			                 while (!$res_efectores->EOF){ 
			                 	$id_servicio1=$res_efectores->fields['id_servicio'];
			                 	$descripcion=$res_efectores->fields['descripcion'];
			                 ?>
			                   <option <?=($res_efectores->fields['descripcion']=="No Corresponde")?"selected":""?> value=<?=$id_servicio1;?>><?=$descripcion?></option>
			                 <?
			                 $res_efectores->movenext();
			                 }
			                 ?>
			      			</select>
					    </td>
					 </tr>
					 
					 <tr>
					    <td align="right">
					    	<b>Monto del Egreso Comprometido:</b>
					    </td>
					    <td align="left"
					    onchange="borrar_buffer(); document.forms[0].submit()" >		          			
				 			<?if ($ins_nombre==1) {?>
				 			<input type=text id=monto_egre_comp name=monto_egre_comp value='<?=number_format($monto_incent,2,',','.');?>' size=30 align=right readonly>
				 			<?}
					        else {?>
					        <input type="text" name="monto_egre_comp" value="" size=30 align="right"> 
					     <?}?>
					     </td>
					    
					 </tr>
					 <tr>
					 	<td align="right">
					    	<b>Fecha del egreso Comprometido:</b>
					    </td>
					    <td align="left">
					    						    	
					    	<?//$fecha_egre_comp=date("d/m/Y");?>
					    	 <input type=text id=fecha_egre_comp name=fecha_egre_comp value='<?=$fecha_egre_comp;?>' size=15 readonly>
					    	 			    	 
					    </td>		    
					 </tr>
					 
					 
					 <tr>
					    <td align="right">
					    	<b>Monto del Egreso:</b>
					    </td>
					    <td align="left">		          			
				 			<input type="text" name="monto_egreso" value="" size=30 align="right">
					    </td>
					 </tr>
					 <tr>
					 	<td align="right">
					    	<b>Fecha del egreso:</b>
					    </td>
					    <td align="left">
					    						    	
					    	<?//$fecha_egreso=date("d/m/Y");?>
					    	 <input type=text id=fecha_egreso name=fecha_egreso value='<?=$fecha_egreso;?>' size=15 readonly>
					    	 		    	 
					    </td>		    
					 </tr>
					 		 
					 <tr>
         				<td align="right">
         	  				<b>Comentario:</b>
         				</td>         	
            			<td align='left'>
              				<textarea cols='70' rows='3' name='comentario' value='<?=$comentario;?>'></textarea>
            			</td>
         			</tr>   					 
				  </td>
			 </tr>
		 </table></td></tr>	 
		 <tr>
		  	<table class="bordes" align="center" width="70%">
		 <tr align="center" id="sub_tabla"></tr>
		 	<td colspan="2"></td>
		  	<td align="center" colspan="2" class="bordes">		      
		    	<input type="submit" name="guardar" value="Guardar Egreso" title="Guardar Egreso" Style="width=300px" onclick="return control_modificar_egresos()">
		    </td>
		 <td align="center" colspan="2" class="bordes">		      
		    	<input type="submit" name="borrar" value="Borrar Egreso" title="Borrar Egreso" Style="width=300px" onclick="return control_borrar_egresos()">
		    </td>
		 </table>
		 </tr> 
	 </table>	
 </td></tr>
 

 
 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
     <? $ref = encode_link("ingre_egre_admin.php",array("cuie"=>$cuie));
     ?>
     <input type=button name="volver" value="Volver" onclick="document.location='<?=$ref?>'" title="Volver al Listado" style="width=150px">     
   </td>
  </tr>
 </table></td></tr>
 
</table>

</form>
<?=fin_pagina();// aca termino ?>
