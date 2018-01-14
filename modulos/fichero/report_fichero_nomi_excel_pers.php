<?php
/*
$Author: gaby $
$Revision: 1.0 $
$Date: 2012/10/20 15:22:40 $
*/
require_once("../../config.php");
extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

$fecha_desde=Fecha_db($fecha_desde);
$fecha_hasta=Fecha_db($fecha_hasta);

if($cuie!='Todos'){
			$sql_tmp="SELECT  DISTINCT
							leche.beneficiarios.*,
							nacer.smiafiliados.*,
							fichero.fichero.*,
							nacer.efe_conv.nombre as nom_efe
							FROM
							fichero.fichero
							LEFT OUTER JOIN nacer.smiafiliados ON fichero.fichero.id_smiafiliados = nacer.smiafiliados.id_smiafiliados
							LEFT OUTER JOIN leche.beneficiarios ON leche.beneficiarios.id_beneficiarios = fichero.fichero.id_beneficiarios
							INNER JOIN nacer.efe_conv ON nacer.efe_conv.cuie = fichero.fichero.cuie
						where (fichero.fichero.fecha_control BETWEEN '$fecha_desde' and '$fecha_hasta') and (nacer.efe_conv.cuie='$cuie')
						";
}else {
				$sql_tmp="SELECT  DISTINCT
							leche.beneficiarios.*,
							nacer.smiafiliados.*,
							fichero.fichero.*,
							nacer.efe_conv.nombre as nom_efe
								FROM
								fichero.fichero
								LEFT OUTER JOIN nacer.smiafiliados ON fichero.fichero.id_smiafiliados = nacer.smiafiliados.id_smiafiliados
								LEFT OUTER JOIN leche.beneficiarios ON leche.beneficiarios.id_beneficiarios = fichero.fichero.id_beneficiarios
								INNER JOIN nacer.efe_conv ON nacer.efe_conv.cuie = fichero.fichero.cuie
							where (fichero.fichero.fecha_control BETWEEN '$fecha_desde' and '$fecha_hasta')
							";
							
			}
			
$res_comprobante=sql($sql_tmp,"<br>Error al traer los datos<br>") or fin_pagina();
excel_header("report_fichero_nomi_excel.xls");?>

<form name=form1 action="report_fichero_nomi_excel_pers.php" method=POST>
<table border=0 width=100% cellspacing=2 cellpadding=2 align=center>
  <tr>
  	<td colspan=10 align=left>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$res_comprobante->recordCount()?> Filtro: <?=$_POST['periodo'];?></td>       
      </tr>
    </table>
   </td>
  </tr>
    <td align=right>Efector</td>
    <td align=right>DNI</td>
    <td align=right>Nombre</td>
    <td align=right>Apellido</td>
    <td align=right>Fecha Nac</td>
    <td align=right>Domicilio</td>    
	<td align=right id=mo>Fecha Control</td>
    <td align=right id=mo>Peso</td>
    <td align=right id=mo>Talla</td>
    <td align=right id=mo>IMC</td>
    <td align=right id=mo>Per. Cef.</td>
    <td align=right id=mo>Perc Peso/Edad</td>
    <td align=right id=mo>Perc Talla/Edad</td>
    <td align=right id=mo>Perc Perim. Cefalico/Edad</td>
    <td align=right id=mo>Perc IMC/Edad</td>
    <td align=right id=mo>Perc Peso/Talla</td>
    <td align=right id=mo>Fecha Diag</td>
    <td align=right id=mo>FUM</td>
    <td align=right id=mo>FPP</td>
    <td align=right id=mo>Sem Ges</td>
  </tr>
 <? $t=0;
 	$s=0;
   while (!$res_comprobante->EOF) {?>  	
  
    <tr>     
	     <td ><?=$res_comprobante->fields['nom_efe']?></td>    
	     <td ><?=($res_comprobante->fields['afidni']!='')?$res_comprobante->fields['afidni']:$res_comprobante->fields['documento'];?></td>    
	     <td ><?=($res_comprobante->fields['afinombre']!='')?$res_comprobante->fields['afinombre']:$res_comprobante->fields['nombre'];?></td>    
	     <td ><?=($res_comprobante->fields['afiapellido']!='')?$res_comprobante->fields['afiapellido']:$res_comprobante->fields['apellido'];?></td>  
	     <td ><?=($res_comprobante->fields['afifechanac']!='')?fecha($res_comprobante->fields['afifechanac']):fecha($res_comprobante->fields['fecha_nac']);?></td>  
	     <td ><?=($res_comprobante->fields['afidomlocalidad']!='')?$res_comprobante->fields['afidomlocalidad']:$res_comprobante->fields['domicilio'];?></td>
		 <td ><?=fecha($res_comprobante->fields['fecha_control'])?></td>  
	     <td ><?=number_format($res_comprobante->fields["peso"],2,',',0)?></td>    
	     <td ><?=number_format($res_comprobante->fields["talla"],2,',',0)?></td>    
	     <td ><?=number_format($res_comprobante->fields["imc"],2,',',0)?></td>    
		 <td ><?if ($res_comprobante->fields['perim_cefalico']=="") echo "&nbsp"; echo number_format($res_comprobante->fields["perim_cefalico"],2,',',0)?></td>
		 <td ><?if($res_comprobante->fields['percen_peso_edad']=="1")echo "<3"; elseif ($res_comprobante->fields['percen_peso_edad']=="2")echo "3-10";  elseif ($res_comprobante->fields['percen_peso_edad']=="3")echo ">10-90 ";  elseif ($res_comprobante->fields['percen_peso_edad']=="4")echo ">90-97 ";  elseif ($res_comprobante->fields['percen_peso_edad']=="5")echo ">97";else echo"Dato Sin Ingresar";?></td>
	     <td ><?if ($res_comprobante->fields['percen_talla_edad']=='1') echo "-3"; elseif ($res_comprobante->fields['percen_talla_edad']=='2') echo "3-97"; elseif ($res_comprobante->fields['percen_talla_edad']=='3') echo "+97";  else echo "Dato Sin Ingresar";?></td>	
	  	 <td ><?if ($res_comprobante->fields['percen_perim_cefali_edad']=='1') echo "-3"; elseif ($res_comprobante->fields['percen_perim_cefali_edad']=='2') echo "3-97"; elseif ($res_comprobante->fields['percen_perim_cefali_edad']=='3') echo "+97"; else echo "Dato Sin Ingresar";?></td>		   
   	     <td ><?if ($res_comprobante->fields['percen_imc_edad']=='1') echo "<3"; elseif ($res_comprobante->fields['percen_imc_edad']=='2') echo "3-10"; elseif ($res_comprobante->fields['percen_imc_edad']=='3') echo " >10-85"; elseif ($res_comprobante->fields['percen_imc_edad']=='4') echo ">85-97";elseif ($res_comprobante->fields['percen_imc_edad']=='5') echo " >97"; else echo "Dato Sin Ingresar";?></td>
	     <td ><?if ($res_comprobante->fields['percen_peso_talla']=='1') echo "<3"; elseif ($res_comprobante->fields['percen_peso_talla']=='2') echo "3-10"; elseif ($res_comprobante->fields['percen_peso_talla']=='3') echo ">10-85"; elseif ($res_comprobante->fields['percen_peso_talla']=='4') echo ">85-97"; elseif ($res_comprobante->fields['percen_peso_talla']=='5') echo " >97"; else  echo "Dato Sin Ingresar"?></td>			                                 
		 <td ><?=fecha($res_comprobante->fields['f_diagnostico'])?></td>  
	     <td ><?=fecha($res_comprobante->fields['fum'])?></td>  
	     <td ><?=fecha($res_comprobante->fields['fpp'])?></td>  
	     <td ><?=number_format($res_comprobante->fields['semana_gestacional'],0,',',0)?></td>		 
	 </tr>
	<?$res_comprobante->MoveNext();
   }?>
    
    
</table>
<br>
	
</td>
</table>

</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>
