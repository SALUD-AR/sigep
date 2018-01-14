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
    $sql.=" where (efe_conv.com_gestion='VERDADERO') order by cuie";
    

if ($cmd=="FALSO")
    $sql.=" where (efe_conv.com_gestion='FALSO') order by cuie";
    
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
 
 	<tr bgcolor=#86bbf1>
    	<td colspan="4" align="center">	Datos del Efector</td>
    	<td align="center">Ingreso</td>
    	<td align="center">Egreso</td>
    	<td align="center">Saldo</td>
    	<td align="center">Devengado</td>
    	<td align="center">Saldo Real</td>    	
    </tr>
  <?   
  while (!$result->EOF) {
  	$cuie=$result->fields['cuie'];
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
		
		$res_saldo=sql($sql,"no puede calcular el saldo");
		
		$total_depositado=$res_saldo->fields['ingre'];//lo uso en ecuacion mas adelante

?>  
    
    <tr>
	<td ><?=$result->fields['cuie']?></td>
     <td ><?=$result->fields['nombre']?></td>
     <td ><?=$result->fields['domicilio']?></td>     
     <td ><?=$result->fields['cuidad']?></td>         
     <td ><?=number_format($res_saldo->fields['ingre'],2,',','.')?></td>         
     <td ><?=number_format($res_saldo->fields['egre'],2,',','.')?></td>         
     <td ><?=number_format($res_saldo->fields['total'],2,',','.')?></td>   
     <td ><?=number_format($res_saldo->fields['deve'],2,',','.')?></td>   
     <?if ((($total_depositado-$res_saldo->fields['egre']-($res_saldo->fields['egre_comp']-$res_saldo->fields['egre'])))<0)$color_fondo1="#BE81F7";
      else $color_fondo1="";?>
     <td bgcolor='<?=$color_fondo1?>'><?=number_format($total_depositado-$res_saldo->fields['egre']-($res_saldo->fields['egre_comp']-$res_saldo->fields['egre']),2,',','.')?></td>    
    </tr>
    	<?//ingresos
			  	
	$result->MoveNext();
    }?>
 </table>
 </form>