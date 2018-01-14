<?
/*
Author: sebastian lohaiza

modificada por
$Author: seba $
$Revision: 1.30 $
$Date: 2009/11/01 18:25:40 $
*/
require_once ("../../config.php");


extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();




$sql_tmp="SELECT * FROM expediente.transaccion left join (select id_expediente,id_efe_conv,
  nro_exp,
  fecha_ing,
  monto ,
  plazo_para_pago,
  control,
  comentario1,
  id_factura ,
  periodo from expediente.expediente) as expediente using (id_expediente) left join (select id_efe_conv,nombre,cuie from nacer.efe_conv) as efector using (id_efe_conv) left join expediente.areas using (id_area) where id_expediente='$id_expediente' ORDER BY (id_transac)DESC";
$result = sql($sql_tmp) or die;

echo $html_header;
?>

<form name='form1' action='historial_det.php' method='POST'>
<input type="hidden" value="<?=$id_expediente?>" name="id_expediente">
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      
     </tr>
</table>

<table width="85%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
    	  
    	<font size=+1><b>Datos del Expediente</b></font>   
    	
       
    </td>
 </tr>
 <tr><td>
  <table width=90% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b>Datos Basico</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
         <tr>	           
           <td align="center" colspan="2">
            <b> Número del Expediente: <font size="+1" color="Red"><?=$result->fields['nro_exp'];?></font> </b>
           </td>
         </tr>
         
      		          
                 
         <tr>
         	<td align="right">
				<b>Efector: </b>
			</td>
			<td align="left">		    	
		    	 <input type=text id=nombre_efector name=nombre_efector value='<?=$result->fields['nombre'];?>' size=50 readonly>
		    	 				    	 
		    </td>
         </tr>
         
         <tr>
         	<td align="right">
				<?$id_factura=$result->fields['id_factura'];
				$ref = encode_link("../facturacion/factura_admin.php",array("id_factura"=>$id_factura));?>
				<a href="<?=$ref?>" target="_blank"><b>Numero de Factura: </b>
			</td>
			<td align="left">		    	
		    	 <input type=text id=id_factura name=id_factura value='<?=$result->fields['id_factura'];?>' size=50 >
		    	 				    	 
		    </td>
         </tr>
         
         <tr>
			<td align="right">
				<b>Fecha Alta:</b>
			</td>
		    <td align="left">		    	
		    	 <?php ?>
		    	 <input type=text id=fecha_alta name=fecha_alta value='<?=$result->fields['fecha_ing'];?>' size=15 readonly>
		    	 				    	 
		    </td>		    
		</tr>
		
		 <tr>
			<td align="right">
				<b>Fecha de Plazo para Pago:</b>
			</td>
		    <td align="left">		    	
		    	 <?php ?>
		    	 <input type=text id=fecha_fin name=fecha_fin value='<?=$result->fields['plazo_para_pago'];?>' size=15 readonly>
		    	 				    	 
		    </td>		    
		</tr>
		         
         <tr>
         	<td align="right">
         	  <b>Monto de Prefacturacion:</b>
         	</td>         	
            <td align='left'>
            <?php 
					if (!$id_transac) {	              
					$id_expediente_fact=$result->fields['id_factura'];
              		$sql_monto="SELECT * FROM facturacion.factura WHERE id_factura='$id_expediente_fact'";
					$result_monto = sql($sql_monto) or die;
					$monto=(float)$result_monto->fields['monto_prefactura'];
					}
					?>
            
               <input type="text" size="40" value="$ <?=number_format($monto,2,',','.')?>" name="monto" readonly>
            </td>
         </tr>
         
         <tr>
         	<td align="right">
         	  <b>debito:</b>
         	</td>         	
            <td align='left'>
              <?php $id_factura=$result->fields['id_factura'];
					$sql_debito="SELECT * FROM facturacion.debito WHERE id_factura='$id_factura'";
					$result_debito = sql($sql_debito) or die;
					$debito=0;
					while (!$result_debito -> EOF) {
					$debito=$debito+($result_debito->fields['monto'] * $result_debito->fields['cantidad']);
					$result_debito->MoveNext();
					};
					if (!$debito) $debito=0;
					?>
              <input type="text" size="40" value="$ <?=number_format($debito,2,',','.')?>" name="debito" readonly >
            </td>
         </tr>
         
<tr>
         	<td align="right">
         	  <b>credito:</b>
         	</td>         	
            <td align='left'>
              <?php $id_fact_credito=$result->fields['id_factura'];
					$sql_credito="SELECT * FROM facturacion.credito WHERE id_factura='$id_fact_credito'";
					$result_credito = sql($sql_credito) or die;
					$credito=0;
					while (!$result_credito -> EOF) {
					$credito=$credito+($result_credito->fields['monto'] * $result_credito->fields['cantidad']);
					$result_credito->MoveNext();
					};
					if (!$credito) $credito=0;
					?>
              
              <input type="text" size="40" value="$ <?=number_format($credito,2,',','.')?>" name="credito" readonly>
            </td>
         </tr>         
<tr>
         	<td align="right">
         	  <b>Total Autorizado para Pago:</b>
         	</td>         	
            <td align='left'>
              <?php  $total=$monto-$debito+$credito;
              		?>
              
              
              <input type="text" size="40" value="$ <?=number_format($total,2,',','.')?>" name="total" <? if ($id_expediente) echo "readonly"?>>
            </td>
         </tr>     

<tr>
         	<td align="right">
         	  <b>Numero de Expediente Externo:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value=" <?=$result->fields['num_tranf']?>" name="num_tranf"  readonly>
            </td>
         </tr>     
 
    
 </table>           
<br>

 
 

 
    
 </table></td></tr>
 
 </table>









<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=10 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       
      </tr>
    </table>
    </td>
  </tr>
    
<tr>
    <td align=right id=mo><a id=mo href='<?=encode_link("expediente.php",array("sort"=>"2","up"=>$up))?>'>Fecha de Movimiento</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("expediente.php",array("sort"=>"6","up"=>$up))?>'>Estado</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("expediente.php",array("sort"=>"6","up"=>$up))?>'>Area</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("expediente.php",array("sort"=>"6","up"=>$up))?>'>Comentario</a></td>  
   <td align=right id=mo><a id=mo href='<?=encode_link("expediente.php",array("sort"=>"6","up"=>$up))?>'>Usuario</a></td>
     
   
  </tr>


<?
   while (!$result->EOF) {
   	
   /*	$ref = encode_link("mod_exp.php",array("nro_exp"=>$result->fields['nro_exp'],"id_expediente"=>$result->fields['id_expediente'],"nombre_efector"=>$result->fields['nombre'],"fecha_ing"=>$result->fields['fecha_ing'],"monto"=>$result->fields['monto']));
    $onclick_elegir="location.href='$ref'";*/
   	switch ($result->fields['estado']){
   		case 'V':$tr=atrib_tr();break;
   		case 'A':$tr=atrib_tr6();break;
   		case 'R':$tr=atrib_tr4();break;
   		case 'D':$tr=atrib_tr7();break;
   		case 'E':$tr=atrib_tr5();break;
   		case 'C':$tr=atrib_tr3();break;
   	}?>
  
    <tr >     
      <td <?=$tr?> align="center" > <?=$result->fields['fecha_mov']?></td>
      <td <?=$tr?> align="center" ><?=$result->fields['estado']?></td>
      <td <?=$tr?> align="center" ><?=$result->fields['area']?></td>
      <td <?=$tr?>> <?=$result->fields['comentario']?></td>
      <td <?=$tr?>> <?=$result->fields['usuario']?></td>
    </tr>    
    </tr>
	<?$result->MoveNext();
    }?>
 
 <table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
     <input type=button aling ="center" name="volver" value="Volver" onclick="document.location='historial.php'"title="Volver al Listado" style="width=150px">     
   </td>
  </tr>
 </table>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?> 
