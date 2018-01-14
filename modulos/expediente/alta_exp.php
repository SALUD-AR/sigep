<?
/*
Author: seba

$Revision: 1.2 $
$Date: 2015/02/24 $
*/

require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

function extrae_mes($fecha) {
        list($d,$m,$a) = explode("/",$fecha);
        return $m;
}

function extrae_anio($fecha) {
        list($d,$m,$a) = explode("/",$fecha);
        $a=$a-2000;
        return $a;
}
function devuelve_cuie($id_efe_conv){
	$sql= "select * from nacer.efe_conv where id_efe_conv=$id_efe_conv ";
	$cuie_efectores=sql($sql) or fin_pagina();
	return $cuie_efectores->fields['cuie'];
}

function string_mes($periodo){
	
	$calend[1]="Enero";
	$calend[2]="Febrero";
	$calend[3]="Marzo";
	$calend[4]="Abril";
	$calend[5]="Mayo";
	$calend[6]="Junio";
	$calend[7]="Julio";
	$calend[8]="Agosto";
	$calend[9]="Septiembre";
	$calend[10]="Octubre";
	$calend[11]="Noviembre";
	$calend[12]="Diciembre";

	$long= strlen ($periodo);
	$anio= substr ($periodo,$long-2,2);
	$anio_int = (int)$anio + 2000;
	
	$mes= substr ($periodo,0,$long-2);
	$mes_int=(int)$mes;
	$mes_string=$calend[$mes_int];
	$fecha="$mes_string $anio_int";
	return $fecha;

}

if ($_POST['guardar']=="Guardar"){   
   $db->StartTrans();         
    
    $q="select nextval('expediente.expediente_id_expediente_seq') as id_expediente";
    $id_expediente=sql($q) or fin_pagina();
     
    $id_expediente=$id_expediente->fields['id_expediente'];
    
    $q_trans="select nextval('expediente.transaccion_id_transac_seq') as id_transac";
    $id_transac=sql($q_trans) or fin_pagina();
    $id_transac=$id_transac->fields['id_transac'];
    $id_area=3;
    $estado='V';
    $comentario="Expediente en fase de revision";
   
   $mes=extrae_mes($fecha_alta);
   $anio=extrae_anio($fecha_alta);
   
   $fecha_alta=Fecha_db($fecha_alta);
   $dias=50;
   $plazo_para_pago=date("Y-m-d", strtotime ("$fecha_alta +$dias days"));
   
   $fecha_informe=Fecha_db($fecha_informe);
   $cuie_nuevo=devuelve_cuie($id_efector_real);  
     
	$nro_exp= "$periodo$id_expediente$cuie_nuevo";
	$fecha_coment=string_mes($periodo);

	$usuario=$_ses_user['name'];
	
    $query="insert into expediente.expediente
               (id_expediente,
  				id_efe_conv,
  				fecha_ing,
  				monto,
  				nro_exp,
  				plazo_para_pago,
  				comentario1,
  				id_factura,
  				periodo,
  				estado
  			)
             values
              ('$id_expediente',
  				'$id_efector_real',
  		        '$fecha_alta',
  				'$monto',
  				'$nro_exp',
  				'$plazo_para_pago',
  				'$comentario',
  				'$id_factura_real',
  				'$fecha_coment',
  				'$estado'
  				)";

    
	$query_trans="insert into expediente.transaccion
               (id_transac,
  				id_expediente,
               	id_area,
  				fecha_mov,
  				estado,
  				comentario,
  				total_pagar,
  				id_factura,
  				usuario  				
  				)
             values
              ('$id_transac',
  				'$id_expediente',
  				'$id_area',
  				'$fecha_alta',
  				'$estado',
  				'$comentario',
  				'$monto',
  				'$id_factura_real',
  				'$usuario'  				
  				)";

    
    //echo ($query);
    
	if ($id_factura_real) {
		$sql_update1="select * from facturacion.factura where id_factura=$id_factura_real";
		$result_update1=sql($sql_update1, "Error de consulta") or fin_pagina();
		$si_online=$result_update1->fields['online'];
		if ($si_online=='SI') {
					$sql_update="UPDATE facturacion.factura SET traba='si',estado_exp=1 WHERE id_factura=$id_factura_real";
					sql ($sql_update, "Error al insertar campo en facturacion") or fin_pagina();
					};
			$sql_update="UPDATE facturacion.factura SET estado_exp=1 WHERE id_factura=$id_factura_real";
					sql ($sql_update, "Error al insertar campo en facturacion") or fin_pagina();
				};
		
    
    
    sql($query, "Error al insertar el Expediente") or fin_pagina();
    sql($query_trans, "Error al insertar el Expediente") or fin_pagina();    
    
    $accion="Se guardo la Alta";    
	 
    $db->CompleteTrans();    
}//de if ($_POST['guardar']=="Guardar Expediente")




echo $html_header;

?>
<script>
</script>

<form name='form1' action='alta_exp.php' method='POST' enctype='multipart/form-data'>
<input type="hidden" value="<?=$id_expediente?>" name="id_expediente">
<input type="hidden" value="<?=$id_efector?>" name="id_efector">
<input type="hidden" value="<?=$id_factura?>" name="id_factura">


<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>
<table width="85%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
    	<?
    	if (!$id_expediente) {
    	?>  
    	<font size=+1><b>Ingreso Datos de Expediente</b></font>   
    	<? }
        else {
        ?>
        <font size=+1><b>Caratula</b></font>   
        <? } ?>
       
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
            <b> Número del Expediente: <font size="+1" color="Red"><?=($id_expediente)? $nro_exp : "Nuevo Dato"?></font> </b>
           </td>
         </tr>
	<table width=80% align="center" class="bordes">
	    <b> </b>	
 		<tr>
          <td align="right">
	         	  <b>Efector:</b>
	         	</td>
	    	
				<td align="left">		
				  <select name=id_efector_real Style="width=200px"
	        		onKeypress="buscar_combo(this);"
					onblur="borrar_buffer();"
					onchange="borrar_buffer(); document.forms[0].submit()" 
				<?if ($id_expediente) echo "disabled"?>>
				<option value=-1>Seleccione</option>
			 <?
				$q_efector="select * from nacer.efe_conv order by nombre";
	        		$res_efector=sql($q_efector,"Error el Efector no existe");?>	
	        
				  <? while (!$res_efector->EOF){
					  		$id_efector_temp=$res_efector->fields['id_efe_conv'];
						  	$nom_efector=$res_efector->fields['nombre']?>
						  <option value='<?=$id_efector_temp?>'<? if($id_efector_temp==$id_efector_real)echo "selected"?>><?=$nom_efector?></option>
						  <?$res_efector->movenext();
					  }?>
				
			</select>
				</td>
			</tr>	
			<tr>
	         	<td align="right">
	         	<b>Factura:</b>
	         	</td>
				<td align="left">		
				<select name=id_factura_real Style="width=200px" 
		        		onKeypress="buscar_combo(this);"
						onblur="borrar_buffer();"
						onchange="borrar_buffer(); document.forms[0].submit()" 
						<?if ($id_expediente) echo "disabled"?>>
						<option value=-1>Seleccione</option>
						<?
						if($id_efector_real){
					 		//$sql_factura_real= "SELECT * FROM facturacion.factura  INNER JOIN nacer.efe_conv ON facturacion.factura.cuie=nacer.efe_conv.cuie WHERE (id_efe_conv=$id_efector_real AND estado_exp=0 ) order by id_factura";
							 $sql_factura_real="select * from (
select id_factura from facturacion.factura except (select numero_factura from contabilidad.ingreso where numero_factura<>0)) as tabla1
left join facturacion.factura using (id_factura) 
left join nacer.efe_conv using (cuie) WHERE (id_efe_conv=$id_efector_real AND estado_exp=0 ) order by id_factura";				
							 $res_factura_real=sql($sql_factura_real) or fin_pagina();
							 while (!$res_factura_real->EOF){ 
							 	$id_factura_real_tem=$res_factura_real->fields['id_factura'];
							    $monto_factura_real=$res_factura_real->fields['monto_prefactura'];
							    ?>
								<option value='<?=$id_factura_real_tem?>'<? if ($id_factura_real_tem==$id_factura_real) echo "selected"?> ><?=$id_factura_real_tem?></option>
							    <?
							    $res_factura_real->movenext();
							    }
						}//fin de if $id_pais_nac?>

					</select>	
				</td>
			<td align="left">
	         	  <b>Si no tiene factura debera crearla primero,<A href="factura_admin.php">haciendo click AQUI</A> </b>
	         	</td>
			
			</tr>
			
		<tr>
		    <td align="right">
		       <b>Periodo de Factura:</b>
		    </td>    
		    <td align="left">
				<?php 
					$calend[1]="Enero";
					$calend[2]="Febrero";
					$calend[3]="Marzo";
					$calend[4]="Abril";
					$calend[5]="Mayo";
					$calend[6]="Junio";
					$calend[7]="Julio";
					$calend[8]="Agosto";
					$calend[9]="Septiembre";
					$calend[10]="Octubre";
					$calend[11]="Noviembre";
					$calend[12]="Diciembre";			
				
				
				$mes_period=129; //Constante dialogada con Miguel para calcular segun la DOIU los 120 dias habiles despues del ultimo dia del mes de prestacion o el primer dia del mes con 30 dias mas
        		$hoy_mes=date("m");
        		$hoy_anio=date("Y");
        		$hoy_anio=$hoy_anio-2000;
				$periodo_mes=date("m", strtotime ("-$mes_period days"));
				$periodo_anio=date("Y", strtotime ("-$mes_period days"));
				$periodo_anio=$periodo_anio-2000;
				
        		?>
			  <select size="1" name="periodo">
			  <option value=-1>Seleccione</option>
			   <?php for ($periodo_anio;$periodo_anio<=$hoy_anio;$periodo_anio++){
			   			if ($periodo_anio<>$hoy_anio){
			   				for ($periodo_mes;$periodo_mes<=12;$periodo_mes++) {
			   				?>
			   				<option value='<?="$periodo_mes$periodo_anio"?>'><?php $periodo_anio2=$periodo_anio+2000; echo "$calend[$periodo_mes]/$periodo_anio2"?></option>
			   	             <?php };
			   	        $periodo_mes=1;} 
			   			else for ($periodo_mes;$periodo_mes<=$hoy_mes;$periodo_mes++){
			   				?>
			   				<option value='<?="$periodo_mes$periodo_anio"?>'><?php $periodo_anio2=$periodo_anio+2000; echo "$calend[$periodo_mes]/$periodo_anio2"?></option>
			                  <?php };
			   }?>
			  	
                                
              </select>
            </td>
		 </tr>  
		
		<tr>
		    <td align="right">
		       <b>Fecha de Alta:</b>
		    </td>    
		    <?/* $fecha_alta=date("d-m-Y");*/?>
			<td align="left">
				<input type=text id=fecha_alta name=fecha_alta value='<?=$fecha_alta;?>' size=15 readonly>
				<?=link_calendario("fecha_alta");?>
				</td>
		 </tr>    
   
   <tr>
         	<td align=right>
         	  <b>Monto de Facturacion ($):</b>
         	</td>         	
            <td>
              <?php 
              if ($id_factura_real){
              $sql_monto= "SELECT * FROM facturacion.factura 
					    	where id_factura=$id_factura_real";
			  $res_monto=sql($sql_monto) or fin_pagina();
			  $monto=number_format($res_monto->fields['monto_prefactura'],2,'.','');			  
              //$monto_ver=round($monto * 100.00) / 100.00; 
              
              
              } 
			  ?>
              
              <input type="text" size="40" value=" <?=$monto?>" name="monto" readonly >
            </td>
            <td align="center">
        <? 	if ($id_expediente){
        	$sql="SELECT * FROM expediente.expediente left join nacer.efe_conv using (id_efe_conv) WHERE id_expediente=$id_expediente";
			$result_exp=sql($sql, "Error de consulta") or fin_pagina();
        	$link=encode_link("caratula_pdf.php", array("nro_exp"=>$result_exp->fields['nro_exp'],"nombre"=>$result_exp->fields['nombre'],"fecha_ing"=>$result_exp->fields['fecha_ing'],"periodo"=>$result_exp->fields['periodo'],"monto"=>$result_exp->fields['monto'],"plazo_pago"=>$result_exp->fields['plazo_para_pago'],"id_factura"=>$result_exp->fields['id_factura']));
        	echo "<a target='_blank' href='".$link."' title='Imprime Caratula'><IMG src='$html_root/imagenes/pdf_logo.gif' height='40' width='40' border='0'></a>";
        }
	       	 	?>
       	  
		</td>
            
            
         </tr>
         
   
   </table> 
    
                     
        
   

   <?if (!($id_expediente)){?>
	 
	 <tr id="mo">
  		<td align=center colspan="2">
  			<b>Guarda Expediente </b>
  		</td>
  	</tr>  
      <tr align="center">
       <td>
        <input type='submit' name='guardar' value='Guardar'  title="Guardar datos del Expediente">
       </td>
      </tr>
     
     <?}?>
     
 </table>           
<br>

 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
     <input type=button name="volver" value="Volver" onclick="document.location='alta_exp.php'"title="Volver al Listado" style="width=150px">     
   </td>
  </tr>
 </table></td></tr>
 
    
 </table></td></tr>
 
 </table>
 </form>
 
 <?=fin_pagina();// aca termino ?>