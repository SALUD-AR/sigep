<?php

require_once ("../../config.php");

$cuie=$parametros["cuie"];
$nombre=$parametros["nombre"];

$query=" SELECT * FROM facturacion.servicio order by descripcion";
$result_servicio=$db->Execute($query) or die($db->ErrorMsg());

echo $html_header

?>

<form name=form1 method=post action="detalle_servicio.php">

<table width="100%" align=center cellspacing="0" cellpadding="5" class="bordes"> 
   
 <tr id="sub_tabla">
 	<td>
 		<font face="arial" size="3"><b>DETALLE POR SERVICIO DEL EFECTOR: <?=$nombre?></b></font>
 	</td>
 </tr>
 <tr>
 	<td>
 		&nbsp;
 	</td>
 </tr>
 
 <?
 $result_servicio->movefirst();
 
 while (!$result_servicio->EOF) {
 	$id_servicio=$result_servicio->fields['id_servicio'];
 	$descripcion=$result_servicio->fields['descripcion'];
 	
$sql="select monto_egreso from contabilidad.egreso
    	where cuie='$cuie' and id_servicio=$id_servicio";
$res_egreso=sql($sql,"no puede calcular el saldo");

if ($res_egreso->recordCount()==0){
	$sql="select ingre as total, ingre,egre,deve,egre_comp from
		(select sum (monto_deposito)as ingre from contabilidad.ingreso
		where cuie='$cuie' and id_servicio=$id_servicio) as ingreso,
		(select sum (monto_egreso)as egre from contabilidad.egreso
		where cuie='$cuie' and id_servicio=$id_servicio) as egreso,
		(select sum (monto_factura)as deve from contabilidad.ingreso
		where cuie='$cuie' and id_servicio=$id_servicio) as devengado,
		(select sum (monto_egre_comp)as egre_comp from contabilidad.egreso
		where cuie='$cuie' and id_servicio=$id_servicio) as egre_comp";

}
else{
$sql="select ingre-egre as total, ingre,egre,deve,egre_comp from
		(select sum (monto_deposito)as ingre from contabilidad.ingreso
		where cuie='$cuie' and id_servicio=$id_servicio) as ingreso,
		(select sum (monto_egreso)as egre from contabilidad.egreso
		where cuie='$cuie' and id_servicio=$id_servicio) as egreso,
		(select sum (monto_factura)as deve from contabilidad.ingreso
		where cuie='$cuie' and id_servicio=$id_servicio) as devengado,
		(select sum (monto_egre_comp)as egre_comp from contabilidad.egreso
		where cuie='$cuie' and id_servicio=$id_servicio) as egre_comp";
}
$res_saldo=sql($sql,"no puede calcular el saldo");


 //tabla de comprobantes
$query="SELECT * FROM contabilidad.ingreso 
	where cuie='$cuie' and id_servicio=$id_servicio
  	order by id_ingreso DESC";
$res_comprobante=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
?>
<table width="100%" align=center cellspacing="0" cellpadding="5" class="bordes"> 
   
 <tr id="sub_tabla">
 	<td>
 		<font face="arial" size="3"><b>DETALLE DEL SERVICIO: <?=$descripcion?></b></font>
 	</td>
 </tr>
 
<tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">	  
	  <td align="center">
	   <b>Ingresos del servicio: <?=$descripcion?></b>&nbsp; (Total Depositado: <?=number_format($res_saldo->fields['ingre'],2,',','.')?>
	   				  &nbsp; Total Devengado: <?=number_format($res_saldo->fields['deve'],2,',','.')?>)
	   				  <?$total_depositado=$res_saldo->fields['ingre'] //lo uso en ecuacion mas adelante?>
	  </td>
	</tr>
</table></td></tr>
<tr><td><table border="1" width="100%" >
	<?if ($res_comprobante->RecordCount()==0){?>
	 <tr>
	  <td align="center">
	   <font size="3" color="Red"><b>No existen Ingresos para este Servicio</b></font>
	  </td>
	 </tr>
	 <?}
	 else{	 	
	 	?>
	 	<tr id="sub_tabla">		 	    
	 		<td width="5%">ID</td>
	 		<td width="15%">Monto PreF</td>
	 		<td width="15%">Fecha PreF</td>
	 		<td width="15%">Monto Fact</td>
	 		<td width="15%">Fecha Fact</td>
	 		<td width="15%">Monto Depo</td>
	 		<td width="15%">Fecha Depo</td>
	 		<td width="15%">Num Fact</td>	 		
	 	</tr>
	 	<?
	 	$res_comprobante->movefirst();
	 	while (!$res_comprobante->EOF) {?>
	 		<tr <?=atrib_tr()?>>	 			
		 		<td ><?=$res_comprobante->fields['id_ingreso']?></td>
		 		<td ><?=number_format($res_comprobante->fields['monto_prefactura'],2,',','.')?></td>
		 		<td ><?=fecha($res_comprobante->fields['fecha_prefactura'])?></td>
		 		<td ><?=number_format($res_comprobante->fields['monto_factura'],2,',','.')?></td>
		 		<td ><?=fecha($res_comprobante->fields['fecha_factura'])?></td>
				<td ><?=number_format($res_comprobante->fields['monto_deposito'],2,',','.')?></td>
		 		<td ><?=fecha($res_comprobante->fields['fecha_deposito'])?></td>
		 		<td ><?=number_format($res_comprobante->fields['numero_factura'],0,'','.')?></td>
		 	</tr>	
		 	
	 		<?$res_comprobante->movenext();
	 	}
	 }?>
	</table></td></tr>
 
<?//tabla de comprobantes
$query="SELECT * FROM contabilidad.egreso 
		where cuie='$cuie' and id_servicio=$id_servicio
  		order by id_egreso DESC";
$res_comprobante=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
?>
<tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">	  
	  <td align="center">
	   <b>Egresos del servicio: <?=$descripcion?> </b>&nbsp; (Total de Egresos:<?=number_format($res_saldo->fields['egre'],2,',','.')?>)
	   // <font color=#F781F3>Saldo Real= <?=number_format($total_depositado-$res_saldo->fields['egre']-($res_saldo->fields['egre_comp']-$res_saldo->fields['egre']),2,',','.')?></font></b>
	  </td>
	</tr>
</table></td></tr>
<tr><td><table border="1" width="100%" >
	<?if ($res_comprobante->RecordCount()==0){?>
	 <tr>
	  <td align="center">
	   <font size="3" color="Red"><b>No existen Egresos para este Servicio</b></font>
	  </td>
	 </tr>
	 <?}
	 else{	 	
	 	?>
	 	<tr id="sub_tabla">		 	    
	 		<td width="5%">ID</td>
	 		<td width="15%">Monto Egre</td>
	 		<td width="15%">Fecha Egre</td>	 		
	 	</tr>
	 	<?
	 	$res_comprobante->movefirst();
	 	while (!$res_comprobante->EOF) {
	 		$ref = encode_link("modifica_egreso.php",array("id_egreso"=>$res_comprobante->fields['id_egreso'],"pagina"=>"ingre_egre_admin.php","cuie"=>$cuie,"monto_egreso"=>$res_comprobante->fields['monto_egreso']));             
            $onclick_elegir="location.href='$ref'";
	 		
	 		$ref1 = encode_link("ingre_egre_admin.php",array("id_egreso"=>$res_comprobante->fields['id_egreso'],"marcar2"=>"True","cuie"=>$cuie));
            $id_egreso=$res_comprobante->fields['id_egreso'];
            $onclick_eliminar="if (confirm('Esta Seguro que Desea Eliminar Egreso $id_egreso ?')) location.href='$ref1'
            						else return false;	"; 		
	 		?>
	 		<tr <?=atrib_tr()?>>	 			
		 		<td ><?=$res_comprobante->fields['id_egreso']?></td>
		 		<td ><?=number_format($res_comprobante->fields['monto_egreso'],2,',','.')?></td>
		 		<td ><?=fecha($res_comprobante->fields['fecha_egreso'])?></td>		 		
		 	</tr>	
		 	
	 		<?$res_comprobante->movenext();
	 	}
	 }?>
</table></td></tr>

<tr id="mo">
 	<td>
 		<font face="arial" size="3"><b>Total del Servicio <?=$descripcion?>: <b><font size="+1" color="Red"><?=number_format($res_saldo->fields['total'],2,',','.')?></font></b></b></font>
 	</td>
 </tr>
 <tr>
 	<td>
 		&nbsp;
 	</td>
 </tr>
</table>
<?$result_servicio->movenext();
 }?>
 </table>
 </form>
 <?=fin_pagina();?>