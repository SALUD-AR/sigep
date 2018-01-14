<?php

require_once ("../../config.php");

$cmd=$parametros["cmd"];

$sql="SELECT 
  nacer.efe_conv.id_efe_conv,
  nacer.efe_conv.nombre,
  nacer.efe_conv.domicilio,
  nacer.efe_conv.departamento,
  nacer.efe_conv.localidad,
  nacer.efe_conv.cod_pos,
  nacer.efe_conv.cuidad,
  nacer.efe_conv.referente,
  nacer.efe_conv.tel,
  nacer.efe_conv.mail,
  nacer.efe_conv.com_gestion,
  nacer.efe_conv.com_gestion_firmante,
  nacer.efe_conv.fecha_comp_ges,
  nacer.efe_conv.fecha_fin_comp_ges,
  nacer.efe_conv.com_gestion_pago_indirecto,
  nacer.efe_conv.tercero_admin,
  nacer.efe_conv.tercero_admin_firmante,
  nacer.efe_conv.fecha_tercero_admin,
  nacer.efe_conv.fecha_fin_tercero_admin,
  nacer.efe_conv.cuie
FROM
  nacer.efe_conv";


if ($cmd=="VERDADERO")
    $sql.=" where (efe_conv.com_gestion='VERDADERO')";
    

if ($cmd=="FALSO")
    $sql.=" where (efe_conv.com_gestion='FALSO')";
    
$result=sql($sql) or fin_pagina();

excel_header("Saldo de Efectores.xls");

?>
<form name=form1 method=post action="ing_egre_saldos_excel.php">
<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total: </b><?=$result->RecordCount();?> 
       </td>       
      </tr>      
    </table>  
   </td>
  </tr>  
 </table> 
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5">   
  <?   
  while (!$result->EOF) {
  	$cuie=$result->fields['cuie'];
  		$sql="select monto_egreso from contabilidad.egreso
		where cuie='$cuie'";
$res_egreso=sql($sql,"no puede calcular el saldo");

if ($res_egreso->recordCount()==0){
	$sql="select ingre as total, ingre,egre from
		(select sum (monto_deposito)as ingre from contabilidad.ingreso
		where cuie='$cuie') as ingreso,
		(select sum (monto_egreso)as egre from contabilidad.egreso
		where cuie='$cuie') as egreso";

}
else{
$sql="select ingre-egre as total, ingre,egre from
		(select sum (monto_deposito)as ingre from contabilidad.ingreso
		where cuie='$cuie') as ingreso,
		(select sum (monto_egreso)as egre from contabilidad.egreso
		where cuie='$cuie') as egreso";
}
$res_saldo=sql($sql,"no puede calcular el saldo")?>  
    <tr bgcolor=#86bbf1>
    	<td colspan="4" align="center">	Datos del Efector</td>
    	<td align="center">Ingreso</td>
    	<td align="center">Egreso</td>
    	<td align="center">Saldo</td>
    </tr>
    <tr>    
     <td ><?=$result->fields['cuie']?></td>
     <td ><?=$result->fields['nombre']?></td>
     <td ><?=$result->fields['domicilio']?></td>     
     <td ><?=$result->fields['cuidad']?></td>         
     <td ><?=number_format($res_saldo->fields['ingre'],2,',','.')?></td>         
     <td ><?=number_format($res_saldo->fields['egre'],2,',','.')?></td>         
     <td bgcolor="Silver" ><?=number_format($res_saldo->fields['total'],2,',','.')?></td> 
    </tr>
    	<?//ingresos
		$query="SELECT 
		  *
		FROM
		  contabilidad.ingreso  
		  where cuie='$cuie' 
		  order by id_ingreso DESC";
		$res_comprobante=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
		if ($res_comprobante->recordcount()>0){?>
		    <tr bgcolor=#C0C0FF>
		    	<td align="center" id=mo colspan="6">Ingresos</td>    	
		  	</tr>
	    	<tr bgcolor=#C0FFFF>		    	
		 		<td width="15%">Monto PreFactura</td>
		 		<td width="15%">Fecha PreFactura</td>
		 		<td width="15%">Monto Factura</td>
		 		<td width="15%">Fecha Factura</td>
		 		<td width="15%">Monto Deposito</td>
		 		<td width="15%">Fecha Deposito</td>	 		
		  	</tr>
		  	<?
		 	$res_comprobante->movefirst();
		 	while (!$res_comprobante->EOF) {	 		 
		 		?>
		 		<tr >	 						 		
			 		<td ><?=number_format($res_comprobante->fields['monto_prefactura'],2,',','.')?></td>
			 		<td ><?=fecha($res_comprobante->fields['fecha_prefactura'])?></td>
			 		<td ><?=number_format($res_comprobante->fields['monto_factura'],2,',','.')?></td>
			 		<td><?=fecha($res_comprobante->fields['fecha_factura'])?></td>
					<td ><?=number_format($res_comprobante->fields['monto_deposito'],2,',','.')?></td>
			 		<td ><?=fecha($res_comprobante->fields['fecha_deposito'])?></td>		 		
			 	</tr>	
			 	
		 		<?$res_comprobante->movenext();
		 	}
		}
		 //los egresos
		 $query="SELECT *
				FROM
					 contabilidad.egreso
					 left join contabilidad.inciso using (id_inciso)  
					 where cuie='$cuie' 
					 order by id_egreso DESC";
		 $res_comprobante=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
		 if ($res_comprobante->recordcount()>0){?>
			<tr bgcolor=#C0C0FF>
		    	<td align="center" id=mo colspan="3">Egresos</td>    	
		  	</tr>
		 	<tr bgcolor=#C0FFFF>		 	    	 			
		 		<td width="15%">Monto Egreso</td>
		 		<td width="15%">Inciso</td>
	 			<td width="15%">Fecha Egreso</td>		 			
	 		</tr>	
			<?
	 		$res_comprobante->movefirst();
	 		while (!$res_comprobante->EOF) {?>
		 		<tr >	 						 		
			 		<td ><?=number_format($res_comprobante->fields['monto_egreso'],2,',','.')?></td>
			 		<td ><?=$res_comprobante->fields['ins_nombre']?></td>
			 		<td ><?=fecha($res_comprobante->fields['fecha_egreso'])?></td>				 		 			 		
			 	</tr>		 	
		 		<?$res_comprobante->movenext();
	 		}
		 }	  	
	$result->MoveNext();
    }?>
 </table>
 </form>