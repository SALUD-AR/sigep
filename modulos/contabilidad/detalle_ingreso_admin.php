<?
require_once ("../../config.php");


extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

if ($_POST['guardar']=="Guardar Ingreso"){
    $comentario=$_POST['comentario'];
	$usuario=$_ses_user['name'];	
	$id_servicio=$_POST['servicio'];
	
	$sql_update="update contabilidad.ingreso set id_servicio='$id_servicio', usuario='$usuario' where
					cuie='$cuie' and numero_factura='$numero_factura'";
	$res_update=sql($sql_update,"No se pudo actualizar la tabla de ingresos");
		
}
echo $html_header;
?>
<script>
//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos_ingresos()
{
  if(document.all.servicio.value=="-1"){
  alert('Debe Seleccionar un Servicio');
  return false;
  }

 if (confirm('Esta Seguro que Desea Modificar el Servicio de la Factura?'))return true;
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
function destrabar_ingreso(){
	document.all.monto_egre_comp.readOnly=false;
	document.all.monto_egre_comp.focus();
}
</script>

<form name='form1' action='detalle_ingreso_admin.php' method='POST' enctype='multipart/form-data'>
<input type="hidden" value="<?=$numero_factura?>" name="numero_factura">


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
     <font size=+1><b>Ingreso</b></font>    
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
		 		Editar Servicio de Ingreso
		 	</td>
		 </tr>
		 <tr><td class="bordes"><table>
			 <tr>
				 <td>
					 <tr>
					    <td align="right">
					    	<b>Número de Factura:</b>
					    </td>
					     <td align="left">		          			
				 			<select name=numero_factura Style="width=550px"
				 			onKeypress="buscar_combo(this);"
				 			onblur="borrar_buffer();"
				 			onchange="borrar_buffer(); document.forms[0].submit();"
           					> 
	     			
			     			<? if (!($numero_factura)) {
			                 ?><option value=-1 selected>Seleccione</option>
			                 <? $sql="select * from (
										select * from expediente.expediente left join facturacion.factura using (id_factura) where control=4 and factura.cuie='$cuie'
										order by id_factura DESC) as tabla left join facturacion.smiefectores using (cuie)";
                                                   
								
			                 $res_efectores=sql($sql) or fin_pagina();
			                 while (!$res_efectores->EOF){ 
			                 	$id_factura=$res_efectores->fields['id_factura'];
			                 	$nombreefector=$res_efectores->fields['nombreefector'];
			                 	$periodo_actual=$res_efectores->fields['periodo_actual'];
			                 	$periodo=$res_efectores->fields['periodo'];
			                 	$monto_prefactura=number_format($res_efectores->fields['monto_prefactura'],2,',','.');
			                 	$fecha_factura=fecha($res_efectores->fields['fecha_factura']);
			                 ?>
			                   <option value=<?=$id_factura;?><? if ($numero_factura==$id_factura) echo "selected"?>><?="N°:".$id_factura." - Periodo Prestaciones:".$periodo_actual." - Monto Cuasi:".$monto_prefactura?></option>
			                               
			                 <?
			                 $res_efectores->movenext();
			                 }
			     			}
			                 else {  $sql= "select * from facturacion.factura
									left join facturacion.smiefectores using (cuie)
									where id_factura=$numero_factura and factura.cuie='$cuie'";
			                 		
			                 $res_efectores=sql($sql) or fin_pagina();
			                 while (!$res_efectores->EOF){ 
			                 	$id_factura=$res_efectores->fields['id_factura'];
			                 	$nombreefector=$res_efectores->fields['nombreefector'];
			                 	$periodo_actual=$res_efectores->fields['periodo_actual'];
			                 	$periodo=$res_efectores->fields['periodo'];
			                 	$monto_prefactura=number_format($res_efectores->fields['monto_prefactura'],2,',','.');
			                 	$fecha_factura=fecha($res_efectores->fields['fecha_factura']);
			                 ?>
			                   <option value=<?=$id_factura;?>><?="N°:".$id_factura." - Periodo Prestaciones:".$periodo_actual." - Monto Cuasi:".$monto_prefactura?></option>
			                               
			                 <?
			                 $res_efectores->movenext();
			                 }
			                 }
			     			 ?>
			      			
			      			
			      			</select><font size="2" color="Red"></font>
			      		</td>
					    
					 </tr>
					 
					 <tr>
					    <td align="right">
					    	<b>Servicio:</b>
					    </td>
					    <td align="left">		          			
				 			<select name=servicio Style="width=450px"
				 			onKeypress="buscar_combo(this);"
				 			onblur="borrar_buffer();"
				 			onchange="borrar_buffer();"
           					>
			     			<option value=-1>Seleccione</option>
			                 <?
			                 $sql= "select * from facturacion.servicio order by descripcion";
			                 $res_efectores=sql($sql) or fin_pagina();
			                 while (!$res_efectores->EOF){ 
			                 	$id_servicio=$res_efectores->fields['id_servicio'];
			                 	$descripcion=$res_efectores->fields['descripcion'];
			                 ?>
			                   <option <?=($res_efectores->fields['descripcion']=="No Corresponde")?"selected":""?> value=<?=$id_servicio;?>><?=$descripcion?></option>
			                 <?
			                 $res_efectores->movenext();
			                 }
			                 ?>
			      			</select>
					    </td>
					 </tr>
					 
					 <tr>
					 	<td align="right">
					    	<b>Fecha de la Prefactura:</b>
					    </td>
					    <td align="left">
					    						    	
					    	<?$fecha_prefactura=date("d/m/Y");?>
					    	 <input type=text id=fecha_prefactura name=fecha_prefactura value='<?=$fecha_prefactura;?>' size=15 readonly>
					    	 <?=link_calendario("fecha_prefactura");?>					    	 
					    </td>		    
					 </tr>
					
					 
					 
					 <tr>
					    <td align="right">
					    	 <b>Expediente Externo:</b>
					    </td>
                         
					    <td align="left"		          			
				 			onchange="borrar_buffer(); document.forms[0].submit()" >
				 			<?					   
					     if ($numero_factura) {
				 		 $sql_exp_ext="select num_tranf from expediente.transaccion where id_factura='$numero_factura' and num_tranf is not null";
					     $result_exp_ext=sql($sql_exp_ext) or die;
					     $num_tranf=$result_exp_ext->fields ['num_tranf'];} ?>
				 			<input type="text" name="expediente_externo" value='<?=$num_tranf;?>' size=18 align="right" readonly>
					    </td>
					 </tr>

					 <tr>
						<td align="right"><b>Fecha de Expediente:</b></td>
						<td align="left"
						onchange="borrar_buffer(); document.forms[0].submit()" >
				 			<?					   
					     if ($numero_factura) {
				 		 $sql_exp_ext="select fecha_mov from expediente.transaccion where id_factura=$numero_factura and num_tranf is not null";
					     $result_exp_ext=sql($sql_exp_ext) or die;
					     $fecha_exp_ext=$result_exp_ext->fields ['fecha_mov'];} ?>
							<input type=text id=fecha_exp_ext name=fecha_exp_ext value='<?=fecha($fecha_exp_ext);?>' size=18 readonly>
				        	 					    	 
						</td>		    
					</tr>
					 
					 <tr>
         				<td align="right">
         	  				<b>Comentario:</b>
         				</td>         	
            			<td align='left'>
              				<textarea cols='70' rows='3' name='comentario' ></textarea>
            			</td>
         			</tr>         			 					 
				  </td>
			 </tr>
		 </table></td></tr>	 
		 <tr>
		  	<td align="center" colspan="2" class="bordes">		      
		    	<input type="submit" name="guardar" value="Guardar Ingreso" title="Guardar Ingreso" Style="width=300px" onclick="return control_nuevos_ingresos()">
		    </td>
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
