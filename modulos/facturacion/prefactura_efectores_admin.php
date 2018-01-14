<?

require_once ("../../config.php");


extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

echo $html_header;
?>
<script>

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

<form name='form1' action='prefactura_efectores_admin.php' method='POST'>

<?echo "<center><b><font size='+2' color='red'>$accion</font></b></center>";?>

<input type="hidden" name="cuie" value="<?=$cuie?>">
<input type="hidden" name="nombreefector" value="<?=$nombreefector?>">
<table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
     <font size=+1><b>Efector</b></font>    
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
         	<td align="right">
         	  <b>CUIE:
         	</td>         	
            <td align='left'>
              <font size="+1" color="Red"><?=$cuie?></font> </b>
            </td>
         </tr>
         <tr>
         	<td align="right">
         	  <b>Efector Asignado:
         	</td>         	
            <td align='left'>
              <font size="+1" color="Red"><?=$nombreefector?></font> </b>
            </td>
         </tr>         
        </table>
      </td>      
     </tr>
   </table>     
	 
 </td></tr>
 
<?//tabla de comprobantes
$query="select * 
	 from facturacion.comprobante	 
	 left join nacer.smiafiliados using (id_smiafiliados)
         left join nacer.smitiposcategorias on (afitipocategoria=codcategoria)
	 left join facturacion.smiefectores using (cuie)
	 where comprobante.id_factura is null and comprobante.cuie='$cuie' and marca=0";
$result=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();

?>
<tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Comprobantes" align="left" style="cursor:hand;" onclick="muestra_tabla(document.all.prueba_vida,2);" >
	  </td>
	  <td align="center">
	   <b>Prestaciones</b>
	  </td>
	</tr>
</table></td></tr>
<tr><td><table id="prueba_vida" border="1" width="100%" style="display:none;border:thin groove">
	<?if ($result->RecordCount()==0){?>
	 <tr>
	  <td align="center">
	   <font size="3" color="Red"><b>No existen comprobantes para este EFECTOR</b></font>
	  </td>
	 </tr>
	 <?}
	 else{	 	
	 	?>
	 	<tr>
    <td id=mo width=1%>&nbsp;</td>
    <td align=right id=mo>Nro Comp</td>      	
    <td align=right id=mo>Apellido</td>      	
    <td align=right id=mo>Nombre</td>
    <td align=right id=mo>DNI</td>
    <td align=right id=mo>Tipo Beneficiario</td>
    <td align=right id=mo>Nombre Efector</td>       
    <td align=right id=mo>Activo</td>    
    <td align=right id=mo>Clave Beneficiario</td>
    <td align=right id=mo>Total Prestaciones</td>
  </tr>
 <?
   while (!$result->EOF) {
   	    
    $id_tabla="tabla_".$result->fields['id_comprobante'];	
	$onclick_check=" javascript:(this.checked)?Mostrar('$id_tabla'):Ocultar('$id_tabla')";
	
	//consulta para saber si tiene pretaciones el comprobante
	$sql=" select count(id_prestacion) as cant_prestaciones from facturacion.prestacion 								
			where id_comprobante=". $result->fields['id_comprobante'];
	$cant_prestaciones=sql($sql,"no se puede traer la contidad de prestaciones") or die();
	$cant_prestaciones=$cant_prestaciones->fields['cant_prestaciones'];
	?>
  
    <tr <?=atrib_tr()?>>
     <td>
	  <input type=checkbox name=check_prestacion value="" onclick="<?=$onclick_check?>" class="estilos_check">
	 </td>     
     <td><?=$result->fields['id_comprobante']?></td>
     <td ><?=$result->fields['afiapellido']?></td>
     <td ><?=$result->fields['afinombre']?></td>
     <td ><?=$result->fields['afidni']?></td>     
     <td ><?=$result->fields['descripcion']?></td> 
     <td ><?=$result->fields['nombreefector']?></td>             
     <td ><?=$result->fields['activo']?></td>       
     <td ><?=$result->fields['clavebeneficiario']?></td> 
     <td ><?=$cant_prestaciones?></td> 
    </tr>    
    <tr>
	          <td colspan=10>
	
	                  <?
	                  $sql=" select *
								from facturacion.prestacion 
								left join facturacion.nomenclador using (id_nomenclador)							
								where id_comprobante=". $result->fields['id_comprobante']." order by id_prestacion DESC";
	                  $result_items=sql($sql) or fin_pagina();
	                  ?>
	                  <div id=<?=$id_tabla?> style='display:none'>
	                  <table width=90% align=center class=bordes>
	                  			<?
	                  			$cantidad_items=$result_items->recordcount();
	                  			if ($cantidad_items==0){?>
		                            <tr>
		                            	<td colspan="10" align="center">
		                            		<b><font color="Red" size="+1">NO HAY PRESTACIONES PARA ESTE COMPROBANTE</font></b>
		                            	</td>	                                
			                        </tr>	                               
								<?}
								else{?>
		                           <tr id=ma>		                               
		                               <td>Cantidad</td>
		                               <td>Codigo</td>
		                               <td>Descripción</td>
		                               <td>Precio</td>
		                               <td>Total</td>	                               
		                            </tr>
		                            <?while (!$result_items->EOF){?>
			                            <tr>
			                            	 <td class="bordes"><?=$result_items->fields["cantidad"]?></td>			                                 
			                                 <td class="bordes"><?=$result_items->fields["codigo"]?></td>
			                                 <td class="bordes"><?=$result_items->fields["descripcion"]?></td>
			                                 <td class="bordes"><?=number_format($result_items->fields["precio_prestacion"],2,',','.')?></td>
			                                 <td class="bordes"><?=number_format($result_items->fields["cantidad"]*$result_items->fields["precio_prestacion"],2,',','.')?></td>
			                            </tr>
		                            	<?$result_items->movenext();
		                            }//del while
								}//del else?>
	                            	                            
	               </table>
	               </div>
	
	         </td>
	      </tr>  	
	<?$result->MoveNext();
    }//del while
	}//del else?>
</table></td></tr>
 
 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
     <input type=button name="volver" value="Volver" onclick="document.location='prefactura_efectores_listado.php'"title="Volver al Listado" style="width=150px">     
   </td>
  </tr>
 </table></td></tr>
 
</table>    
</form>
<?=fin_pagina();// aca termino ?>
