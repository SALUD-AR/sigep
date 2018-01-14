<?php

require_once("../../config.php");

$afidni=$parametros['afidni'] or $id_smiafiliados=$_POST['afidni'];


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
<form name=form1 action="detalle_trazadoras.php" method=POST>
<input type="hidden" name="afidni" value="<?=$afidni?>">
<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  
<?//tabla de comprobantes
$query="SELECT * FROM trazadoras.embarazadas
			left join facturacion.smiefectores using (CUIE)
  where num_doc='$afidni' 
  order by fecha_control DESC";
$result=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
?>
<tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	    &nbsp;&nbsp;&nbsp;&nbsp;<input type=button name="volver" value="Volver" onclick="document.location='listado_beneficiarios_cal.php'"title="Volver al Listado" style="width=150px">
	  </td>
	</tr>
</table></td></tr>
<tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Ingresos" align="left" style="cursor:hand;" onclick="muestra_tabla(document.all.prueba_vida,2);" >
	  </td>
	  <td align="center">
	   <b>Embarazadas</b>&nbsp; (Total: <?=number_format($result->recordCount(),0,',','.')?>)
	  </td>
	</tr>
</table></td></tr>
<tr><td><table id="prueba_vida" border="1" width="100%" style="display:none;border:thin groove">
	<?if ($result->RecordCount()==0){?>
	 <tr>
	  <td align="center">
	   <font size="3" color="Red"><b>No existen Datos</b></font>
	  </td>
	 </tr>
	 <?}
	 else{	 	
	 	?>
	 	<tr id="sub_tabla">	 		  	
		    <td align=right id=mo>cuie</td>      	
		    <td align=right id=mo>nombreefector</td>      	
		    <td align=right id=mo>tipo_doc</td>
		    <td align=right id=mo>num_doc</td>
		    <td align=right id=mo>apellido</td>        
		    <td align=right id=mo>nombre</td> 
		    <td align=right id=mo>fecha_control</td> 
		    <td align=right id=mo>sem_gestacion</td> 
		    <td align=right id=mo>fum</td> 
		    <td align=right id=mo>fpp</td> 
		    <td align=right id=mo>fpcp</td> 
		    <td align=right id=mo>observaciones</td> 
		    <td align=right id=mo>antitetanica</td> 
		    <td align=right id=mo>vdrl</td> 
	 	</tr>
	 	<?
	 	$result->movefirst();
	 	while (!$result->EOF) {?>
	 		<tr <?=atrib_tr()?>>	 			
		 		<td ><?=$result->fields['cuie']?></td>
			     <td ><?=$result->fields['nombreefector']?></td>
			     <td ><?=$result->fields['tipo_doc']?></td>
			     <td ><?=number_format($result->fields['num_doc'],0,'','')?></td>     
			     <td ><?=$result->fields['apellido']?></td>      
			     <td ><?=$result->fields['nombre']?></td>      
			     <td ><?=fecha($result->fields['fecha_control'])?></td>      
			     <td ><?=number_format($result->fields['sem_gestacion'],0,'','')?></td>      
			     <td ><?=fecha($result->fields['fum'])?></td>      
			     <td ><?=fecha($result->fields['fpp'])?></td>      
			     <td ><?=fecha($result->fields['fpcp'])?></td>      
			     <td ><?=$result->fields['observaciones']?></td>      
			     <td ><?=$result->fields['antitetanica']?></td>      
			     <td ><?=$result->fields['vdrl']?></td>       	 		
		 	</tr>	
		 	
	 		<?$result->movenext();
	 	}
	 }?>
</table></td></tr>

<?//tabla de comprobantes
$query="SELECT * FROM trazadoras.partos
			left join facturacion.smiefectores using (CUIE)
  where num_doc='$afidni' 
  order by fecha_parto DESC";
$result=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
?>

<tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Ingresos" align="left" style="cursor:hand;" onclick="muestra_tabla(document.all.prueba_vida3,2);" >
	  </td>
	  <td align="center">
	   <b>Partos</b>&nbsp; (Total: <?=number_format($result->recordCount(),0,',','.')?>)
	  </td>
	</tr>
</table></td></tr>
<tr><td><table id="prueba_vida3" border="1" width="100%" style="display:none;border:thin groove">
	<?if ($result->RecordCount()==0){?>
	 <tr>
	  <td align="center">
	   <font size="3" color="Red"><b>No existen Datos</b></font>
	  </td>
	 </tr>
	 <?}
	 else{	 	
	 	?>
	 	<tr id="sub_tabla">	 		  	
		    <td align=right id=mo>cuie</td>      	
		    <td align=right id=mo>nombreefector</td>      	
		    <td align=right id=mo>tipo_doc</td>
		    <td align=right id=mo>num_doc</td>
		    <td align=right id=mo>apellido</td>        
		    <td align=right id=mo>nombre</td> 
		    <td align=right id=mo>fecha_parto</td> 
		    <td align=right id=mo>apgar</td> 
		    <td align=right id=mo>peso</td> 
		    <td align=right id=mo>vdrl</td> 
		    <td align=right id=mo>antitetanica</td> 
		    <td align=right id=mo>fecha_conserjeria</td> 
		    <td align=right id=mo>observaciones</td> 	
	 	</tr>
	 	<?
	 	$result->movefirst();
	 	while (!$result->EOF) {?>
	 		<tr <?=atrib_tr()?>>	 			
		 		<td ><?=$result->fields['cuie']?></td>
			     <td ><?=$result->fields['nombreefector']?></td>
			     <td ><?=$result->fields['tipo_doc']?></td>
			     <td ><?=number_format($result->fields['num_doc'],0,'','')?></td>     
			     <td ><?=$result->fields['apellido']?></td>      
			     <td ><?=$result->fields['nombre']?></td>      
			     <td ><?=fecha($result->fields['fecha_parto'])?></td>      
			     <td ><?=number_format($result->fields['apgar'],0,'','')?></td>      
			     <td ><?=number_format($result->fields['peso'],3,',','.')?></td>      
			     <td ><?=$result->fields['vdrl']?></td>      
			     <td ><?=$result->fields['antitetanica']?></td>      
			     <td ><?=fecha($result->fields['fecha_conserjeria'])?></td>      
			     <td ><?=$result->fields['observaciones']?></td> 			         	 		
		 	</tr>	
		 	
	 		<?$result->movenext();
	 	}
	 }?>
</table></td></tr>

<?//tabla de comprobantes
$query="SELECT * FROM trazadoras.nino
			left join facturacion.smiefectores using (CUIE)
  where num_doc='$afidni' 
  order by fecha_control DESC";
$result=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
?>
<tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Ingresos" align="left" style="cursor:hand;" onclick="muestra_tabla(document.all.prueba_vida1,2);" >
	  </td>
	  <td align="center">
	   <b>Niños</b>&nbsp; (Total: <?=number_format($result->recordCount(),0,',','.')?>)
	  </td>
	</tr>
</table></td></tr>
<tr><td><table id="prueba_vida1" border="1" width="100%" style="display:none;border:thin groove">
	<?if ($result->RecordCount()==0){?>
	 <tr>
	  <td align="center">
	   <font size="3" color="Red"><b>No existen Datos</b></font>
	  </td>
	 </tr>
	 <?}
	 else{	 	
	 	?>
	 	<tr id="sub_tabla">	 		  	
		    <td align=right id=mo>cuie</td>      	
		    <td align=right id=mo>nombreefector</td>      	
		    <td align=right id=mo>tipo_doc</td>
		    <td align=right id=mo>num_doc</td>
		    <td align=right id=mo>apellido</td>        
		    <td align=right id=mo>nombre</td> 
		    <td align=right id=mo>fecha_nac</td> 
		    <td align=right id=mo>fecha_control</td> 
		    <td align=right id=mo>peso</td> 
		    <td align=right id=mo>talla</td> 
		    <td align=right id=mo>perim_cefalico</td> 
		    <td align=right id=mo>percen_peso_edad</td> 
		    <td align=right id=mo>percen_talla_edad</td> 
		    <td align=right id=mo>percen_perim_cefali_edad</td> 
		    <td align=right id=mo>percen_peso_talla</td> 
		    <td align=right id=mo>triple_viral</td> 
		    <td align=right id=mo>nino_edad</td> 
		    <td align=right id=mo>observaciones</td> 		    
	 	</tr>
	 	<?
	 	$result->movefirst();
	 	while (!$result->EOF) {?>
	 		<tr <?=atrib_tr()?>>	 			
		 		<td ><?=$result->fields['cuie']?></td>
			     <td ><?=$result->fields['nombreefector']?></td>
			     <td ><?=$result->fields['tipo_doc']?></td>
			     <td ><?=number_format($result->fields['num_doc'],0,'','')?></td>     
			     <td ><?=$result->fields['apellido']?></td>      
			     <td ><?=$result->fields['nombre']?></td>      
			     <td ><?=$result->fields['fecha_nac']?></td>      
			     <td ><?=$result->fields['fecha_control']?></td>      
			     <td ><?=number_format($result->fields['peso'],2,',','.')?></td>      
			     <td ><?=number_format($result->fields['talla'],2,',','.')?></td>      
			     <td ><?=number_format($result->fields['perim_cefalico'],2,',','.')?></td>      
			     <td ><?=$result->fields['percen_peso_edad']?></td>      
			     <td ><?=$result->fields['percen_talla_edad']?></td>      
			     <td ><?=$result->fields['percen_perim_cefali_edad']?></td>      
			     <td ><?=$result->fields['percen_peso_talla']?></td> 			       
			     <td ><?=$result->fields['triple_viral']?></td>      
			     <td ><?=number_format($result->fields['nino_edad'],0,'','')?></td>      
			     <td ><?=$result->fields['observaciones']?></td>       	 		
		 	</tr>	
		 	
	 		<?$result->movenext();
	 	}
	 }?>
</table></td></tr>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>