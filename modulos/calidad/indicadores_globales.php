<?

require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

if ($_POST['guardar_indi_1_1']=='Guardar'){
	$nombre_1_1=$_POST['nombre_1_1'];
	$db->StartTrans();
		$query="update calidad.indicadores_globales set
             valor=$nombre_1_1
             WHERE descripcion='rechazos_padron'";
		sql($query, "Error al cambiar rechazos padron") or fin_pagina();
		$accion="Se Cambio la Cantidad de rechazos en el Padron";	
	$db->CompleteTrans();	
}
if ($_POST['guardar_indi_6_4']=='Guardar'){
	$nombre_6_4=$_POST['nombre_6_4'];
	$db->StartTrans();
		$query="update calidad.indicadores_globales set
             valor=$nombre_6_4
             WHERE descripcion='resumen_cuenta'";
		sql($query, "Error al cambiar resumen cuenta") or fin_pagina();
		$accion="Se Cambio Resumen Cuenta";	
	$db->CompleteTrans();	
}
if ($_POST['capita_promedio']=='Guardar'){
	$nombre_capita_promedio=$_POST['nombre_capita_promedio'];
	$db->StartTrans();
		$query="update calidad.indicadores_globales set
             valor=$nombre_capita_promedio
             WHERE descripcion='capita_promedio'";
		sql($query, "Error al cambiar capita_promedio") or fin_pagina();
		$accion="Se Cambio Capita Promedio";	
	$db->CompleteTrans();	
}



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

/**********************************************************/
//funciones para busqueda abreviada utilizando teclas en la lista que muestra los clientes.
var digitos=10; //cantidad de digitos buscados
var puntero=0;
var buffer=new Array(digitos); //declaración del array Buffer
var cadena="";


</script>

<form name='form1' action='indicadores_globales.php' method='POST'>

<?echo "<center><b><font size='+2' color='red'>$accion</font></b></center>";?>

<table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
     <font size=+1><b>Indicadores Globales</b></font>    
    </td>
 </tr>
 <tr><td>
  <table width=70% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b> Nivel de Cumplimiento en Inscripción 1.1</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
        <tr>
            <td align="right">
         	  <b> INSCRIPTOS A LA FECHA:
         	</td>   
           <td  colspan="2">
           	  <?
           	  $sql = "SELECT count (smiafiliados.id_smiafiliados)as r1 
					from nacer.smiafiliados 					
     				WHERE activo='S'";
     		  $r1=sql($sql,"error R1");?>
             <b><font size="+1"><?=number_format($r1->fields['r1'],0,'','.')?></font></b>
           </td>
          </tr>
          
          <?
          $sql="select sum (ins_comp_anual) as total from nacer.dpto";
          $ins_comp_anual=sql($sql,"no puedo sumar los CA");
          $ins_comp_anual=$ins_comp_anual->fields['total'];
          ?>
         <tr>   	           
           <td align="right">
            <b> INSCRIPTOS SEGÚN C.A. A LA FECHA: 
            </td>
            <td colspan="2">
            <font size="+1" color="black"><?=number_format($ins_comp_anual,0,'','.')?></font></b>
            </td>            
         </tr>           
           
          <tr>
           <td align="right">
         	  <b> INSCRIPTOS A LA FECHA / INSCRIPTOS SEGÚN C.A. A LA FECHA:
         	</td>            
           	<?$indi1=number_format(($r1->fields['r1']/$ins_comp_anual)*100,0,'','.');
           	switch ($indi1){
				case $indi1 <= 60 : $color_indi1="Red";
									break;
				
				case ($indi1 > 60 and $indi1 <= 80) : $color_indi1="Yellow";
									break;
									
				case $indi1 > 80 : $color_indi1="Green";
									break;
           	}
           	?>
           	<td colspan="2" bgcolor="<?=$color_indi1?>">
           	<b><font size="+1" color="Black"><?=$indi1?> %</font></b>
           </td>
          </tr>         
         
        </table>
     </td></tr>
     
     <tr>
      <td id=mo colspan="2">
       <b>Efectividad en las inscripciones del periodo 1.2</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
        <tr>
            <td align="right">
         	  <b> INSCRIPTOS RECHAZADOS:
         	</td>   
           <td  colspan="2">
           	  <?
           	  $sql = "SELECT *
					from calidad.indicadores_globales 					
     				WHERE descripcion='rechazos_padron'";
     		  $indi_1_1=sql($sql,"error indi_1_1");
     		  $indi_1_1=$indi_1_1->fields['valor']?>
             <b><font size="+1"><?=number_format($indi_1_1,0,'','.')?></font></b>
           </td>
            <?if (permisos_check("inicio","cambia_ca")) { ?>
            <td align='left'>
              <input type="text" size="4" name="nombre_1_1" value="<?=number_format($indi_1_1,0,'','.');?>">&nbsp;<input type="submit" name="guardar_indi_1_1" value="Guardar" size="5">
            </td>
            <?}?>
          </tr>
          
          
          <?
          $sql="select distinct * from calidad.indicadores_ins
				where id_desc_indicador_ins=3
				order by id_indicadores_ins DESC";
          $indi_1_1_d=sql($sql,"no puedo sumar los CA");
          $indi_1_1_d=$indi_1_1_d->fields['valor'];
          ?>
         <tr>   	           
           <td align="right">
            <b> INCORPORACIONES DEL MES: 
            </td>
            <td colspan="2">
            <font size="+1" color="black"><?=number_format($indi_1_1_d,0,'','.')?></font></b>
            </td>            
         </tr>           
           
          <tr>
           <td align="right">
         	  <b> INSCRIPTOS RECHAZADOS / INCORPORACIONES DEL MES:
         	</td>            
           	<?
           	$indi_1_1_total=($indi_1_1/$indi_1_1_d)*100;           
           	$indi_1_1_total=number_format($indi_1_1_total,0,'','.');           	
           	
           	switch ($indi_1_1_total){									
           		case $indi_1_1_total <= 1 : $color_indi_1_1="Green";
									break;
				
				case ($indi_1_1_total > 1 and $indi_1_1_total <= 5) : $color_indi_1_1="Yellow";
									break;
									
				case $indi_1_1_total > 5 : $color_indi_1_1="Red";
									break;
           	}           	
           	if ($indi_1_1_total==0) $color_indi_1_1='Green';           	
           	?>
           	<td colspan="2" bgcolor="<?=$color_indi_1_1?>">
           	<b><font size="+1" color="Black"><?=$indi_1_1_total?> %</font></b>
           </td>
          </tr>         
         
        </table>
     </td></tr>
     
     <tr>
      <td id=mo colspan="2">
       <b>Desvío en el Cumplimiento de Plazos de Pago a Prestadores 5.2</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
                 <?
           	  $sql = "SELECT 
					  sum((contabilidad.ingreso.fecha_deposito - facturacion.factura.fecha_factura)) AS sumados,
					  count((contabilidad.ingreso.fecha_deposito - facturacion.factura.fecha_factura)) AS contados,
					  sum((contabilidad.ingreso.fecha_deposito - facturacion.factura.fecha_factura))/count((contabilidad.ingreso.fecha_deposito - facturacion.factura.fecha_factura)) AS total
					FROM
					  facturacion.factura
					  INNER JOIN contabilidad.ingreso ON (facturacion.factura.id_factura = contabilidad.ingreso.numero_factura)";
     		  $indi_5_2=sql($sql,"error indi_1_1");
     		  $indi_5_2=$indi_5_2->fields['total']?>            
           
          <tr>
           <td align="right">
         	  <b> PROMEDIO DIAS PLAZO DE PAGO A PRESTADORES:
         	</td>            
           	<?          	
           	switch ($indi_5_2){									
           		case $indi_5_2 <= 50 : $color_indi_5_2="Green";
									break;
				
				case ($indi_5_2 > 50 and $indi_5_2 <= 65) : $color_indi_5_2="Yellow";
									break;
									
				case $indi_5_2 > 65 : $color_indi_5_2="Red";
									break;
           	}           	           	        	
           	?>
           	<td colspan="2" bgcolor="<?=$color_indi_5_2?>">
           	<b><font size="+1" color="Black"><?=number_format($indi_5_2,0,'','')?> Dias.</font></b>
           </td>
          </tr>         
         
        </table>
     </td></tr>
    
      <tr>
      <td id=mo colspan="2">
       <b>Nivel de Efectividad en la Cancelación de Facturas 6.2</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
        <tr>
            <td align="right">
         	  <b> IMPORTES PAGADOS A PRESTADORES:
         	</td>   
           <td  colspan="2">
           	  <?
           	  $sql = "SELECT 
					  sum(contabilidad.ingreso.monto_deposito) AS total_pagado
					  FROM
					  contabilidad.ingreso";
     		  $indi_6_2=sql($sql,"error indi_6_4");
     		  $indi_6_2=$indi_6_2->fields['total_pagado']?>
             <b><font size="+1"><?=number_format($indi_6_2,0,'','.')?></font></b>
           </td>            
          </tr>
          
          
          <?
           $sql="SELECT 
				  sum (facturacion.prestacion.cantidad*
				  facturacion.prestacion.precio_prestacion) as total_controlado
				FROM
				  facturacion.factura
				  INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
				  INNER JOIN facturacion.prestacion ON (facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante)
				WHERE
				  (facturacion.factura.estado = 'C')";
          $indi_6_2_d=sql($sql,"no puedo sumar los CA");
          $indi_6_2_d=$indi_6_2_d->fields['total_controlado'];
          ?>
         <tr>   	           
           <td align="right">
            <b> TOTAL FACTURACION AUTORIZADA: 
            </td>
            <td colspan="2">
            <font size="+1" color="black"><?=number_format($indi_6_2_d,0,'','.')?></font></b>
            </td>                   
         </tr>           
           
          <tr>
           <td align="right">
         	  <b> IMPORTES PAGADOS A PRESTADORES / TOTAL FACTURACION AUTORIZADA:
         	</td>            
           	<?
           	$indi_6_2_total=(($indi_6_2*100)/$indi_6_2_d);           
           	$indi_6_2_total=number_format($indi_6_2_total,0,',','.'); 
           	          	        	
           	
           	switch ($indi_6_2_total){									
           		case ($indi_6_2_total > '90') : $color_indi_6_2="Green";
									break;
				
				case ($indi_6_2_total >= '70' and $indi_6_2_total <= '90') : $color_indi_6_2="Yellow";
									break;
									
				case $indi_6_2_total < '70' : $color_indi_6_2="Red";
									break;									
				
           	}           	
           	if ($indi_1_1_total==0) $color_indi_1_1='Green';           	
           	?>
           	<td colspan="2" bgcolor="<?=$color_indi_6_2?>">
           	<b><font size="+1" color="Black"><?=$indi_6_2_total?> %</font></b>
           </td>
          </tr>         
         
        </table>
     </td></tr>
      
     <tr>
      <td id=mo colspan="2">
       <b>Recursos disponibles por Beneficiario 6.3</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
        <tr>
            <td align="right">
         	  <b> TOTAL SALDO BANCARIO:
         	</td>   
           <td  colspan="2">
           	  <?
           	  $sql = "SELECT *
					from calidad.indicadores_globales 					
     				WHERE descripcion='resumen_cuenta'";
     		  $indi_6_4=sql($sql,"error indi_6_4");
     		  $indi_6_4=$indi_6_4->fields['valor']?>
             <b><font size="+1"><?=number_format($indi_6_4,0,'','.')?></font></b>
           </td>            
          </tr>
          
          
          <?
           $sql="SELECT count (id_smiafiliados) as valor
					from nacer.smiafiliados 					
     				WHERE activo='S'";
          $total_bene=sql($sql,"no puedo sumar los CA");
          $total_bene=$total_bene->fields['valor'];
                    
          $indi_6_4_d=$total_bene;
          
          ?>
         <tr>   	           
           <td align="right">
            <b> CANTIDAD BENEFIARIOS ULTIMO PADRON APROBADO: 
            </td>
            <td colspan="2">
            <font size="+1" color="black"><?=number_format($indi_6_4_d,0,'','.')?></font></b>
            </td>                   
         </tr>           
           
          <tr>
           <td align="right">
         	  <b> TOTAL SALDO BANCARIO / CANTIDAD BENEFIARIOS ULTIMO PADRON APROBADO:
         	</td>            
           	<?
           	$indi_6_4_total=($indi_6_4/$indi_6_4_d);           
           	$indi_6_4_total=number_format($indi_6_4_total,0,',','.'); 
           	
           	$sql="SELECT *
					from calidad.indicadores_globales 					
     				WHERE descripcion='capita_promedio'";
          $capita_promedio=sql($sql,"no puedo sumar los CA");
          $capita_promedio=$capita_promedio->fields['valor'];          	
           	
           	switch ($indi_6_4_total){									
           		case ($indi_6_4_total > ('2,5' * $capita_promedio) and $indi_6_4_total <= ('3,5' * $capita_promedio)) : $color_indi_6_4="Green";
									break;
				
				case ($indi_6_4_total > ('3,5' * $capita_promedio) and $indi_6_4_total <= (6 * $capita_promedio)) : $color_indi_6_4="Yellow";
									break;
									
				case $indi_6_4_total > (6 * $capita_promedio) : $color_indi_6_4="Red";
									break;
									
				case $indi_6_4_total <= ('2,5'* $capita_promedio) : $color_indi_6_4="Red";
									break;
           	}           	
           	if ($indi_1_1_total==0) $color_indi_1_1='Green';           	
           	?>
           	<td colspan="2" bgcolor="<?=$color_indi_6_4?>">
           	<b><font size="+1" color="Black"><?=$indi_6_4_total?></font></b>
           </td>
          </tr>         
         
        </table>
     </td></tr>
     
     <tr>
      <td id=mo colspan="2">
       <b>Cápitas disponibles por Beneficiario 6.4</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
        <tr>
            <td align="right">
         	  <b> TOTAL SALDO BANCARIO:
         	</td>   
           <td  colspan="2">
           	  <?
           	  $sql = "SELECT *
					from calidad.indicadores_globales 					
     				WHERE descripcion='resumen_cuenta'";
     		  $indi_6_4=sql($sql,"error indi_6_4");
     		  $indi_6_4=$indi_6_4->fields['valor']?>
             <b><font size="+1"><?=number_format($indi_6_4,0,'','.')?></font></b>
           </td>
            <?if (permisos_check("inicio","cambia_ca")) { ?>
            <td align='left'>
              <input type="text" size="10" name="nombre_6_4" value="<?=number_format($indi_6_4,0,'','');?>">&nbsp;<input type="submit" name="guardar_indi_6_4" value="Guardar" size="5">
            </td>
            <?}?>
          </tr>
          
          
          <?
           $sql="SELECT count (id_smiafiliados) as valor
					from nacer.smiafiliados 					
     				WHERE activo='S'";
          $total_bene=sql($sql,"no puedo sumar los CA");
          $total_bene=$total_bene->fields['valor'];
          
          $sql="SELECT *
					from calidad.indicadores_globales 					
     				WHERE descripcion='capita_promedio'";
          $indi_6_4_d=sql($sql,"no puedo sumar los CA");
          $capita_promedio=$indi_6_4_d->fields['valor'];
          $indi_6_4_d=$indi_6_4_d->fields['valor'] * $total_bene;
          
          ?>
         <tr>   	           
           <td align="right">
            <b> CANTIDAD BENEFIARIOS ULTIMO PADRON APROBADO * CAPITA PROMEDIO: 
            </td>
            <td colspan="2">
            <font size="+1" color="black"><?=number_format($indi_6_4_d,0,'','.')?></font></b>
            </td>  
            <?if (permisos_check("inicio","cambia_ca")) { ?>
            <td align='left'>
              <input type="text" size="10" name="nombre_capita_promedio" value="<?=number_format($capita_promedio,0,'','');?>">&nbsp;<input type="submit" name="capita_promedio" value="Guardar" size="5">
            </td>
            <?}?>          
         </tr>           
           
          <tr>
           <td align="right">
         	  <b> TOTAL SALDO BANCARIO / (CANTIDAD BENEFIARIOS ULTIMO PADRON APROBADO * CAPITA PROMEDIO):
         	</td>            
           	<?
           	$indi_6_4_total=($indi_6_4/$indi_6_4_d);           
           	$indi_6_4_total=number_format($indi_6_4_total,1,',','.');           	
           	
           	switch ($indi_6_4_total){									
           		case ($indi_6_4_total > '2,5' and $indi_6_4_total <= '3,5') : $color_indi_6_4="Green";
									break;
				
				case ($indi_6_4_total > '3,5' and $indi_6_4_total <= 6) : $color_indi_6_4="Yellow";
									break;
									
				case $indi_6_4_total > 6 : $color_indi_6_4="Red";
									break;
									
				case $indi_6_4_total <= '2,5' : $color_indi_6_4="Red";
									break;
           	}           	
           	if ($indi_1_1_total==0) $color_indi_1_1='Green';           	
           	?>
           	<td colspan="2" bgcolor="<?=$color_indi_6_4?>">
           	<b><font size="+1" color="Black"><?=$indi_6_4_total?> </font></b>
           </td>
          </tr>         
         
        </table>
     </td></tr>
        
     <td><tr>
     <table width=70% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b> Avance en la cobertura de oferta prestacional 2.1</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
        <?//tabla de comprobantes
		$query="select * 
				from 
				nacer.efe_conv				
				order by nombre";
		$efectores=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
		
		$efectores->movefirst();
		$efectores_conv=0;
		$efectores_sin_conv=0;
		$efectores_total=$efectores->RecordCount();
		while (!$efectores->EOF) {
			if ($efectores->fields['com_gestion']=='VERDADERO') $efectores_conv++;
			else $efectores_sin_conv++;
			$efectores->movenext();
		}
		?>

        
         <tr>         	           
           <td align="right">
            <b>CONVENIOS FIRMADOS: 
            </td>
            <td colspan="2">
            <font size="+1" color="black"><?=$efectores_conv?></font></b>
            </td>            
         </tr>   
         
         
         <tr>
         	<td align="right">
         	  <b>PRESTADORES ELEGIBLES:
         	</td>         	
            <td align='left'>
              <font size="+1" color="black"><?=$efectores_total?></font></b>
            </td>
         </tr>
         
                  
          <tr>
           <td align="right">
         	  <b> CONVENIOS FIRMADOS / PRESTADORES ELEGIBLES:
         	</td>            
           	<?$indi2=number_format(($efectores_conv/$efectores_total)*100,0,'','');
           	switch ($indi2){
				case $indi2 <= 60 : $color_indi1="Red";
									break;
				
				case ($indi2 > 60 and $indi2 <= 80) : $color_indi1="Yellow";
									break;
									
				case $indi2 > 80 : $color_indi1="Green";
									break;
           	}
           	?>
           	<td colspan="2" bgcolor="<?=$color_indi1?>">
           	<b><font size="+1" color="Black"><?=$indi2?> %</font></b>
           </td>
          </tr>         
         
        </table>        
      </td></tr>
      
     <td><tr>
     <table width=70% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b>Nivel de Involucramiento de los Prestadores 4.1</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
        <?//tabla de comprobantes
		$query="select nombre,cuie, count (cuie) as cant_facturacion
				from facturacion.factura
				left join nacer.efe_conv using (cuie)
				where estado='C'
				group by cuie,efe_conv.nombre
				order by nombre";
		$efectores_facturan=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
		$total_efectores_facturan=$efectores_facturan->recordCount();
		?>

        
         <tr>         	           
           <td align="right">
            <b>PRESTADORES CON CONVENIO QUE PRESENTAN FACTURAS: 
            </td>
            <td colspan="2">
            <font size="+1" color="black"><?=$total_efectores_facturan?></font></b>
            </td>            
         </tr>   
         
         
         <tr>
         	<td align="right">
         	  <b>TOTAL PRESTADORES CON CONVENIO:
         	</td>         	
            <td align='left'>
              <font size="+1" color="black"><?=$efectores_conv?></font></b>
            </td>
         </tr>
         
                  
          <tr>
           <td align="right">
         	  <b>PRESENTAN FACTURAS / PRESTADORES CON CONVENIO:
         	</td>            
           	<?$indi3=number_format(($total_efectores_facturan/$efectores_conv)*100,0,'','');
           	switch ($indi3){
				case $indi3 <= 60 : $color_indi1="Red";
									break;
				
				case ($indi3 > 60 and $indi3 <= 80) : $color_indi1="Yellow";
									break;
									
				case $indi3 > 80 : $color_indi1="Green";
									break;
           	}
           	?>
           	<td colspan="2" bgcolor="<?=$color_indi1?>">
           	<b><font size="+1" color="Black"><?=$indi3?> %</font></b>
           </td>
          </tr>      
        </table>        
      </td></tr>
      
      
     <td><tr>
     <table width=70% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b>FACTURACION ON LINE 4.1.1</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
        <?//tabla de comprobantes
		$query="select nombre,cuie, count (cuie) as cant_facturacion
				from facturacion.factura
				left join nacer.efe_conv using (cuie)
				where estado='C' and online='SI'
				group by cuie,efe_conv.nombre
				order by nombre";
		$efectores_facturan_online=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
		$total_efectores_facturan_online=$efectores_facturan_online->recordCount();
		?>

        
         <tr>         	           
           <td align="right">
            <b>PRESTADORES CON CONVENIO QUE PRESENTAN FACTURAS ONLINE: 
            </td>
            <td colspan="2">
            <font size="+1" color="black"><?=$total_efectores_facturan_online?></font></b>
            </td>            
         </tr>   
         
         
         <tr>
         	<td align="right">
         	  <b>PRESTADORES CON CONVENIO QUE PRESENTAN FACTURAS:
         	</td>         	
            <td align='left'>
              <font size="+1" color="black"><?=$total_efectores_facturan?></font></b>
            </td>
         </tr>
         
                  
          <tr>
           <td align="right">
         	  <b>PRESTADORES CON CONVENIO ONLINE/PRESTADORES CON CONVENIO:
         	</td>            
           	<?$indi3=number_format(($total_efectores_facturan_online/$total_efectores_facturan)*100,0,'','');
           	switch ($indi3){
				case $indi3 <= 40 : $color_indi1="Red";
									break;
				
				case ($indi3 > 40 and $indi3 <= 60) : $color_indi1="Yellow";
									break;
									
				case $indi3 > 60 : $color_indi1="Green";
									break;
           	}
           	?>
           	<td colspan="2" bgcolor="<?=$color_indi1?>">
           	<b><font size="+1" color="Black"><?=$indi3?> %</font></b>
           </td>
          </tr>      
        </table>        
      </td></tr> 
      
      
      <td><tr>
     <table width=70% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b>FACTURACION NUEVA OPERACION 4.1.2</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
        <?//tabla de comprobantes
		$query="select count (id_efe_conv) as cant_facturacion_nueva_operacion
				from nacer.efe_conv 
				left join facturacion.nomenclador_detalle using (id_nomenclador_detalle)
				where modo_facturacion='2'";
		$cant_facturacion_nueva_operacion=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
		$cant_facturacion_nueva_operacion=$cant_facturacion_nueva_operacion->fields['cant_facturacion_nueva_operacion'];
		?>

        
         <tr>         	           
           <td align="right">
            <b>PRESTADORES CON CONVENIO QUE PRESENTAN FACTURAS CON NUEVA OPERACION: 
            </td>
            <td colspan="2">
            <font size="+1" color="black"><?=$cant_facturacion_nueva_operacion?></font></b>
            </td>            
         </tr>   
         
         
         <tr>
         	<td align="right">
         	  <b>PRESTADORES CON CONVENIO QUE PRESENTAN FACTURAS:
         	</td>         	
            <td align='left'>
              <font size="+1" color="black"><?=$total_efectores_facturan?></font></b>
            </td>
         </tr>
         
                  
          <tr>
           <td align="right">
         	  <b>PRESTADORES FACTURAS NUEVA OPERACION/PRESTADORES QUE FACTURAN:
         	</td>            
           	<?$indi3=number_format(($cant_facturacion_nueva_operacion/$total_efectores_facturan)*100,0,'','');
           	switch ($indi3){
				case $indi3 <= 50 : $color_indi1="Red";
									break;
				
				case ($indi3 > 50 and $indi3 <= 80) : $color_indi1="Yellow";
									break;
									
				case $indi3 > 80 : $color_indi1="Green";
									break;
           	}
           	?>
           	<td colspan="2" bgcolor="<?=$color_indi1?>">
           	<b><font size="+1" color="Black"><?=$indi3?> %</font></b>
           </td>
          </tr>      
        </table>        
      </td></tr>
      
      <td><tr>
     <table width=70% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b>Nivel de Capacitación  de Prestadores I 4.2</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
        <?//tabla de comprobantes
		$query="select sum(cantidad*monto)as total_debitado
				from facturacion.factura
				left join nacer.efe_conv using (cuie)
				left join facturacion.debito using (id_factura)
				where estado='C'";
		$total_debitado=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
		$total_debitado=$total_debitado->fields['total_debitado'];
		
		$query="select sum(cantidad*precio_prestacion) as total_pagado
				from facturacion.factura
				left join nacer.efe_conv using (cuie)
				left join facturacion.comprobante using (id_factura)
				left join facturacion.prestacion using (id_comprobante)
				where estado='C'";
		$total_pagado=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
		$total_pagado=$total_pagado->fields['total_pagado'];
		
		$query_n_op="select sum(precio) as total_pagado
				from nomenclador.prestaciones_n_op
				left join facturacion.comprobante using (id_comprobante)
				left join facturacion.factura using (id_factura)
				where estado='C'";
		$total_pagado_n_op=sql($query_n_op,"<br>Error al traer los comprobantes<br>") or fin_pagina();
		$total_pagado_n_op=$total_pagado_n_op->fields['total_pagado'];
		
		($total_pagado_n_op!='')?$total_pagado=$total_pagado+$total_pagado_n_op:$total_pagado=$total_pagado;
		
		$query="select sum (monto_prefactura) as total_prefacturado
				from facturacion.factura
				left join nacer.efe_conv using (cuie)
				where estado='C'";
		$total_prefacturado=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
		$total_prefacturado=$total_prefacturado->fields['total_prefacturado'];
		?>

        
         <tr>         	           
           <td align="right">
            <b>DEBITOS EPCSS A PRESTADORES: 
            </td>
            <td colspan="2">
            <font size="+1" color="black">$ <?=number_format($total_debitado,0,'','.')?></font></b>
            </td>            
         </tr>   
         
         
         <tr>
         	<td align="right">
         	  <b>TOTAL FACTURACION RECIBIDA PRESTADORES (Prefacturado):
         	</td>         	
            <td align='left'>
              <font size="+1" color="black">$ <?=number_format($total_prefacturado,0,'','.')?></font></b>
            </td>
         </tr>
         
                  
          <tr>
           <td align="right">
         	  <b>DEBITOS EPCSS A PRESTADORES / TOTAL FACTURACION RECIBIDA PRESTADORES:
         	</td>            
           	<?$indi4=number_format(($total_debitado/$total_prefacturado)*100,0,'','.');
           	switch ($indi4){
				case $indi4 > 40 : $color_indi1="Red";
									break;
				
				case ($indi4 > 20 and $indi4 <= 40) : $color_indi1="Yellow";
									break;
									
				case $indi4 <= 20 : $color_indi1="Green";
									break;
           	}
           	?>
           	<td colspan="2" bgcolor="<?=$color_indi1?>">
           	<b><font size="+1" color="Black"><?=$indi4?> %</font></b>
           </td>
          </tr>      
        </table>        
      </td></tr>
      
     <td><tr>
     <table width=70% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b>Nivel de Capacitación de los Prestadores II 4.2</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>        
         <tr>         	           
           <td align="right">
            <b>TOTAL PAGADO: 
            </td>
            <td colspan="2">
            <font size="+1" color="black"><?=number_format($total_pagado,0,'','.')?></font></b>
            </td>            
         </tr>   
         
         
         <tr>
         	<td align="right">
         	  <b>TOTAL PREFACTURADO:
         	</td>         	
            <td align='left'>
              <font size="+1" color="black"><?=number_format($total_prefacturado,0,'','.')?></font></b>
            </td>
         </tr>
         
                  
          <tr>
           <td align="right">
         	  <b>TOTAL PAGADO / TOTAL PREFACTURADO:
         	</td>            
           	<?$indi3=number_format(($total_pagado/$total_prefacturado)*100,0,'','.');
           	switch ($indi3){
				case $indi3 <= 50 : $color_indi1="Red";
									break;
				
				case ($indi3 > 50 and $indi3 <= 70) : $color_indi1="Yellow";
									break;
									
				case $indi3 > 70 : $color_indi1="Green";
									break;
           	}
           	?>
           	<td colspan="2" bgcolor="<?=$color_indi1?>">
           	<b><font size="+1" color="Black"><?=$indi3?> %</font></b>
           </td>
          </tr>      
        </table>        
      </td></tr>
      
      <td><tr>
     <table width=70% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b>Nivel de Uso de los Fondos</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>        
         
      <?$query="select sum (monto_egreso) as total_ejecutado
				from contabilidad.egreso
				left join nacer.efe_conv using (cuie)";
		$total_ejecutado=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
		$total_ejecutado=$total_ejecutado->fields['total_ejecutado'];?>
        
        <tr>
         	<td align="right">
         	  <b>TOTAL EJECUTADO:
         	</td>         	
            <td align='left'>
              <font size="+1" color="black"><?=number_format($total_ejecutado,0,'','.')?></font></b>
            </td>
         </tr>
         
        <tr>         	           
           <td align="right">
            <b>TOTAL PAGADO: 
            </td>
            <td colspan="2">
            <font size="+1" color="black"><?=number_format($total_pagado,0,'','.')?></font></b>
            </td>            
         </tr>   
                          
          <tr>
           <td align="right">
         	  <b>TOTAL EJECUTADO / TOTAL PAGADO:
         	</td>            
           	<?$indi3=number_format(($total_ejecutado/$total_pagado)*100,0,'','.');
           	switch ($indi3){
				case $indi3 <= 50 : $color_indi1="Red";
									break;
				
				case ($indi3 > 50 and $indi3 <= 70) : $color_indi1="Yellow";
									break;
									
				case $indi3 > 70 : $color_indi1="Green";
									break;
           	}
           	?>
           	<td colspan="2" bgcolor="<?=$color_indi1?>">
           	<b><font size="+1" color="Black"><?=$indi3?> %</font></b>
           </td>
          </tr>      
        </table>        
      </td></tr>
      
      
   </table>  
      
	 
 </td></tr>
 

</table>

<br>
	<table align='center' border=1 bordercolor='#000000' bgcolor='#FFFFFF' width='80%' cellspacing=0 cellpadding=0>
     <tr>
      <td colspan=10 bordercolor='#FFFFFF' align="center"><b>Las referencias Bajarlas en Utilidades->Lista de Archivos</b></td>
     <tr>         
    </table>  
</form>
<?=fin_pagina();// aca termino ?>
