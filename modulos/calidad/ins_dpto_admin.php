<?

require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

if ($_POST['Guardar']=='Guardar'){
	$nombre_ca=$_POST['nombre_ca'];
	$db->StartTrans();
		$query="update nacer.dpto set
             ins_comp_anual=$nombre_ca
             where id_dpto=$id_dpto";
		sql($query, "Error al cambiar el compromiso anual") or fin_pagina();
		$accion="Se Combio la Cantidad de Inscriptos Segun Compromiso Anual";	
	$db->CompleteTrans();	
}

$sql="select * from nacer.dpto
	 where id_dpto=$id_dpto";
$result=sql($sql, "Error al traer los Comprobantes") or fin_pagina();

$nombre=$result->fields['nombre'];
$codigo=$result->fields['codigo'];
$ins_comp_anual=$result->fields['ins_comp_anual'];

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

<form name='form1' action='ins_dpto_admin.php' method='POST'>

<?echo "<center><b><font size='+2' color='red'>$accion</font></b></center>";?>

<input type="hidden" name="id_dpto" value="<?=$id_dpto?>">

<table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
     <font size=+1><b>Departamento <?=$nombre?> - INDEC <?=$codigo?></b></font>    
    </td>
 </tr>
 <tr><td>
  <table width=70% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b> Nivel de Cumplimiento en Inscripción</b>
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
					left join nacer.efe_conv ON (nacer.efe_conv.cuie = nacer.smiafiliados.cuieefectorasignado)
     				WHERE departamento='$codigo' and activo='S'";
     		  $r1=sql($sql,"error R1");?>
             <b><font size="+1"><?=number_format($r1->fields['r1'],0,'','.')?></font></b>
           </td>
          </tr>
          
         <tr>   	           
           <td align="right">
            <b> INSCRIPTOS SEGÚN C.A. A LA FECHA: 
            </td>
            <td colspan="2">
            <font size="+1" color="black"><?=number_format($ins_comp_anual,0,'','.')?></font></b>
            </td>            
         </tr>   
         
         <?if (permisos_check("inicio","cambia_ca")) { ?>
         <tr>
         	<td align="right">
         	  <b>Cambiar Cantidad Inscriptos CA:
         	</td>         	
            <td align='left'>
              <input type="text" name="nombre_ca" value="<?=number_format($ins_comp_anual,0,'','.');?>">&nbsp;<input type="submit" name="Guardar" value="Guardar">
            </td>
         </tr>
         <?}?>
           
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
        
     <td><tr>
     <table width=70% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b> Avance en la cobertura de oferta prestacional</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
        <?//tabla de comprobantes
		$query="select * 
				from 
				nacer.efe_conv
				where departamento='$codigo'
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
       <b>Nivel de Involucramiento de los Prestadores</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
        <?//tabla de comprobantes
		$query="select nombre,cuie, count (cuie) as cant_facturacion
				from facturacion.factura
				left join nacer.efe_conv using (cuie)
				where departamento='$codigo' and estado='C'
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
       <b>Nivel de Involucramiento de los Prestadores</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
       <?//tabla de comprobantes
		$query="select nombre,cuie, count (cuie) as cant_facturacion
				from facturacion.factura
				left join nacer.efe_conv using (cuie)
				where estado='C' and online='SI' and departamento='$codigo'
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
         	  <b>TOTAL PRESTADORES CON CONVENIO:
         	</td>         	
            <td align='left'>
              <font size="+1" color="black"><?=$efectores_conv?></font></b>
            </td>
         </tr>
         
                  
          <tr>
           <td align="right">
         	  <b>PRESENTAN FACTURAS ONLINE / PRESTADORES CON CONVENIO:
         	</td>            
           	<?$indi3=number_format(($total_efectores_facturan_online/$efectores_conv)*100,0,'','');
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
       <b>Nivel de Involucramiento de los Prestadores</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
       <?//tabla de comprobantes
		$query="select count (id_efe_conv) as cant_facturacion_nueva_operacion
				from nacer.efe_conv 
				left join facturacion.nomenclador_detalle using (id_nomenclador_detalle)
				where modo_facturacion='2' and departamento ='$codigo'";
		$cant_facturacion_nueva_operacion=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
		$cant_facturacion_nueva_operacion=$cant_facturacion_nueva_operacion->fields['cant_facturacion_nueva_operacion'];
		?>
         <tr>         	           
           <td align="right">
            <b>PRESTADORES CON CONVENIO QUE PRESENTAN FACTURAS NUEVA OPERACION: 
            </td>
            <td colspan="2">
            <font size="+1" color="black"><?=$cant_facturacion_nueva_operacion?></font></b>
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
         	  <b>PRESENTAN FACTURAS NUEVA OPERACION / PRESTADORES CON CONVENIO:
         	</td>            
           	<?$indi3=number_format(($cant_facturacion_nueva_operacion/$efectores_conv)*100,0,'','');
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
       <b>Nivel de Capacitación  de Prestadores I</b>
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
				where departamento='$codigo' and estado='C'";
		$total_debitado=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
		$total_debitado=$total_debitado->fields['total_debitado'];
		
		$query="select sum(cantidad*precio_prestacion) as total_pagado
				from facturacion.factura
				left join nacer.efe_conv using (cuie)
				left join facturacion.comprobante using (id_factura)
				left join facturacion.prestacion using (id_comprobante)
				where departamento='$codigo' and estado='C'";
		$total_pagado=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
		$total_pagado=$total_pagado->fields['total_pagado'];
		
		$query_n_op="select sum(precio) as total_pagado
				from nomenclador.prestaciones_n_op
				left join facturacion.comprobante using (id_comprobante)
				left join facturacion.factura using (id_factura)
				left join nacer.efe_conv ON (facturacion.factura.cuie=nacer.efe_conv.cuie)
				where departamento='$codigo' and estado='C'";
		$total_pagado_n_op=sql($query_n_op,"<br>Error al traer los comprobantes<br>") or fin_pagina();
		$total_pagado_n_op=$total_pagado_n_op->fields['total_pagado'];
		
		($total_pagado_n_op!='')?$total_pagado=$total_pagado+$total_pagado_n_op:$total_pagado=$total_pagado;
		
		$query="select sum (monto_prefactura) as total_prefacturado
				from facturacion.factura
				left join nacer.efe_conv using (cuie)
				where departamento='$codigo' and estado='C'";
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
       <b>Nivel de Capacitación de los Prestadores II</b>
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
				left join nacer.efe_conv using (cuie)
				where departamento='$codigo'";
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
   
   <tr>
   	<td align="center" colspan="2" class="bordes">		      
		<input type=button name="volver" value="Volver" onclick="document.location='ins_dpto.php'"title="Volver al Listado" style="width=150px">
	</td>
   </tr>    
	 
 </td></tr>
 
<?//tabla de comprobantes
$query="select efe_conv.nombre, count (id_smiafiliados) as total, departamento, cuie from 
nacer.efe_conv
left join nacer.smiafiliados on  (nacer.efe_conv.cuie=nacer.smiafiliados.cuieefectorasignado)
where departamento='$codigo' and activo='S'
group by nombre,departamento,cuie
order by nombre";
$res_comprobante=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
?>
<tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar" align="left" style="cursor:hand;" onclick="muestra_tabla(document.all.prueba_vida,2);" >
	  </td>
	  <td align="center">
	   <b>Efectores del Departamento con Inscripcion - Total de Efectores: <?=$efectores_total?> Inscriben: <?=$res_comprobante->recordcount()?> - Porcentaje de Efectores que Inscriben: <?=number_format(($res_comprobante->recordcount()/$efectores_total)*100,0,'','')?> %</b>
	  </td>
	</tr>
</table></td></tr>
<tr><td><table id="prueba_vida" border="1" width="100%" style="display:none;border:thin groove">
	<?if ($res_comprobante->RecordCount()==0){?>
	 <tr>
	  <td align="center">
	   <font size="3" color="Red"><b>No existen inscripciones</b></font>
	  </td>
	 </tr>
	 <?}
	 else{	 	
	 	?>
	 	<tr id="sub_tabla">		 	    
	 		<td width="10%">Nº</td>
	 		<td width="20%">cuie</td>
	 		<td width="40%">Efector</td>
	 		<td width="30%">Inscriptos Activos</td>	 		
	 	</tr>
	 	<?
	 	$res_comprobante->movefirst();
	 	$i=0;
	 	while (!$res_comprobante->EOF) {
	 		$i++;?>
	 		<tr <?=atrib_tr()?>>	 				
		 		<td ><?=$i?></td>
		 		<td ><?=$res_comprobante->fields['cuie']?></td>
		 		<td ><?=$res_comprobante->fields['nombre']?></td>
		 		<td ><?=$res_comprobante->fields['total']?></td>
		 	</tr>			 	  	
	 		<?$res_comprobante->movenext();
	 	}	 	
	 }?>
	 <tr>
	 	<td colspan="5" align="center">
	 		<font size=1 color="Red"><b>Efectores que no Figuran no Tienen Inscriptos (a Continuacion Listado con el Total de Efectores)</b></font>
	 	</td>
	 </tr>
</table></td></tr>

<tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar" align="left" style="cursor:hand;" onclick="muestra_tabla(document.all.prueba_vida1,2);" >
	  </td>
	  <td align="center">
	   <b>Efectores Total <?=$efectores_total?> - Con Convenio: <?=$efectores_conv?> - Sin Convenio: <?=$efectores_sin_conv?> </b>
	  </td>
	</tr>
</table></td></tr>
<tr><td><table id="prueba_vida1" border="1" width="100%" style="display:none;border:thin groove">
	<?if ($efectores->RecordCount()==0){?>
	 <tr>
	  <td align="center">
	   <font size="3" color="Red"><b>No existen efectores</b></font>
	  </td>
	 </tr>
	 <?}
	 else{	 	
	 	?>
	 	<tr id="sub_tabla">		 	    
	 		<td width="10%">Nº</td>
	 		<td width="30%">Efector</td>
	 		<td width="10%">Cuie</td>
	 		<td width="30%">Respontable</td>	 		
	 		<td width="10%">Telefono</td>	 		
	 		<td width="5%">Convenio</td>	 		
	 	</tr>
	 	<?
	 	$efectores->movefirst();
	 	$i=0;
	 	while (!$efectores->EOF) {
	 		$i++;?>
	 		<tr <?=atrib_tr()?>>	 				
		 		<td ><?=$i?></td>
		 		<td ><?=$efectores->fields['nombre']?></td>
		 		<td ><?=$efectores->fields['cuie']?></td>
		 		<td ><?=$efectores->fields['referente']?></td>
		 		<td ><?if ($efectores->fields['tel']!='')echo $efectores->fields['tel']; else echo '&nbsp';?></td>
		 		<td align="center" bgcolor="<?=($efectores->fields['com_gestion']=='VERDADERO')?"":"Red";?>"><?=($efectores->fields['com_gestion']=='VERDADERO')?"SI":"NO";?></td>
		 	</tr>			 	  	
	 		<?$efectores->movenext();
	 	}
	 }?>
</table></td></tr>
 
 <tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar" align="left" style="cursor:hand;" onclick="muestra_tabla(document.all.prueba_vida2,2);" >
	  </td>
	  <td align="center">
	   <b>Efectores con Convenio <?=$efectores_conv?> - Con Convenio que Facturan: <?=$total_efectores_facturan?> </b>
	  </td>
	</tr>
</table></td></tr>
<tr><td><table id="prueba_vida2" border="1" width="100%" style="display:none;border:thin groove">
	<?if ($efectores_facturan->RecordCount()==0){?>
	 <tr>
	  <td align="center">
	   <font size="3" color="Red"><b>No existen efectores que facturen</b></font>
	  </td>
	 </tr>
	 <?}
	 else{	 	
	 	?>
	 	<tr id="sub_tabla">		 	    
	 		<td width="10%">Nº</td>
	 		<td width="30%">Efector</td>
	 		<td width="30%">Cuie</td>
	 		<td width="20%">Cantidad de Facturas Presentadas</td>	 		
	 		<td width="5%">ONLINE</td>	 		
	 		<td width="5%">NUEVA OP</td>	 		
	 	</tr>
	 	<?
	 	$efectores_facturan->movefirst();
	 	$i=0;
	 	while (!$efectores_facturan->EOF) {
	 		$i++;?>
	 		<tr <?=atrib_tr()?>>	 				
		 		<td ><?=$i?></td>
		 		<td ><?=$efectores_facturan->fields['nombre']?></td>
		 		<td ><?=$efectores_facturan->fields['cuie']?></td>
		 		<td ><?=$efectores_facturan->fields['cant_facturacion']?></td>		 			 		
		 		<?$cuie_aux=$efectores_facturan->fields['cuie'];
		 		$efectores_facturan_online->moveFirst();
		 		$factura_online_aux='0';
		 		while (!$efectores_facturan_online->EOF) {
		 			if ($efectores_facturan_online->fields['cuie']==$cuie_aux) $factura_online_aux='1';
		 			$efectores_facturan_online->MoveNext();		 			
		 		}
		 		?>		 		
		 		<td bgcolor="<?=($factura_online_aux=='1')?"Green":"Red";?>">&nbsp;</td>		 
		 		<?$query="select modo_facturacion
						from nacer.efe_conv 
						left join facturacion.nomenclador_detalle using (id_nomenclador_detalle) 
						where cuie='$cuie_aux'";
				$modo_facturacion=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
				$modo_facturacion=$modo_facturacion->fields['modo_facturacion'];?>		
		 		<td bgcolor="<?=($modo_facturacion=='2')?"Green":"Red";?>">&nbsp;</td>		 		
		 	</tr>			 	  	
	 		<?$efectores_facturan->movenext();
	 	}
	 }?>
	 <tr>
	 	<td colspan="5" align="center">
	 		<font size=1 color="Red"><b>Efectores que no Figuran no Tienen Facturacion (Anterior esta el Listado con el Total de Efectores)</b></font>
	 	</td>
	 </tr>
</table></td></tr>

<?//tabla de comprobantes
$query="select nombre, cuie, sum (monto_prefactura) as total, 'Total Prefacturado' as Detalle
from facturacion.factura
left join nacer.efe_conv using (cuie)
where departamento='$codigo' and estado='C'
group by efe_conv.nombre,factura.cuie
union 
select nombre, factura.cuie, sum(cantidad*precio_prestacion) as total, 'Total Pagado' as Detalle
from facturacion.factura
left join nacer.efe_conv using (cuie)
left join facturacion.comprobante using (id_factura)
left join facturacion.prestacion using (id_comprobante)
where departamento='$codigo' and estado='C'
group by efe_conv.nombre,factura.cuie
union
select nombre, factura.cuie,sum(precio) as total_pagado, 'Total Pagado Nuevo Nomenclador' as Detalle
from nomenclador.prestaciones_n_op
left join facturacion.comprobante using (id_comprobante)
left join facturacion.factura using (id_factura)
left join nacer.efe_conv ON (facturacion.factura.cuie=nacer.efe_conv.cuie)
where departamento='$codigo' and estado='C'
group by efe_conv.nombre,factura.cuie
union
select nombre, cuie, sum(cantidad*monto)as total, 'Total Debitado' as detalle
from facturacion.factura
left join nacer.efe_conv using (cuie)
left join facturacion.debito using (id_factura)
where departamento='$codigo' and estado='C'
group by efe_conv.nombre,factura.cuie
union
select efe_conv.nombre,egreso.cuie,sum(monto_egreso) as total , 'Total Ejecutado' as detalle
from contabilidad.egreso
left join nacer.efe_conv using (cuie)
where departamento='$codigo'
group by efe_conv.nombre,egreso.cuie
order by detalle,nombre";
$result=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
?>
<tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar" align="left" style="cursor:hand;" onclick="muestra_tabla(document.all.prueba_vida3,2);" >
	  </td>
	  <td align="center">
	   <b>Detalle Facturacion - Total Prefacturado: <?=number_format($total_prefacturado,2,',','.')?> - Total Debitado: <?=number_format($total_debitado,2,',','.')?> - Total Pagado: <?=number_format($total_pagado,2,',','.')?></b>
	  </td>
	</tr>
</table></td></tr>
<tr><td><table id="prueba_vida3" border="1" width="100%" style="display:none;border:thin groove">
	<?if ($result->RecordCount()==0){?>
	 <tr>
	  <td align="center">
	   <font size="3" color="Red"><b>No existen efectores que facturen</b></font>
	  </td>
	 </tr>
	 <?}
	 else{	 	
	 	?>
	 	<tr id="sub_tabla">		 	    
	 		<td width="5%">Nº</td>
	 		<td width="35%">Efector</td>
	 		<td width="10%">Cuie</td>
	 		<td width="20%">Monto</td>	 		
	 		<td width="30%">Detalle</td>	 		
	 	</tr>
	 	<?
	 	$result->movefirst();
	 	$i=0;
	 	while (!$result->EOF) {
	 		$i++;?>
	 		<tr <?=atrib_tr()?>>	 				
		 		<td ><?=$i?></td>
		 		<td ><?=$result->fields['nombre']?></td>
		 		<td ><?=$result->fields['cuie']?></td>
		 		<td ><?=number_format($result->fields['total'],2,',','.')?></td>		 		
		 		<td ><?=$result->fields['detalle']?></td>		 		
		 	</tr>			 	  	
	 		<?$result->movenext();
	 	}
	 }?>
	 <tr>
	 	<td colspan="5" align="center">
	 		<font size=1 color="Red"><b>Efectores que no Figuran no Tienen Facturacion (Anterior esta el Listado con el Total de Efectores)</b></font>
	 	</td>
	 </tr>
</table></td></tr>
 
 
</table>

<br>
	<table align='center' border=1 bordercolor='#000000' bgcolor='#FFFFFF' width='80%' cellspacing=0 cellpadding=0>
     <tr>
      <td colspan=10 bordercolor='#FFFFFF'><b>Nivel de Cumplimiento en Inscripción - Avance en la Cobertura de Oferta Prestacional - Nivel de Involucramiento de los Prestadores</b></td>
     <tr>     
     <td width=30% bordercolor='#FFFFFF'>
      <table border=1 bordercolor='#FFFFFF' cellspacing=0 cellpadding=0 width=100%>
       <tr>
        <td width=30 bgcolor="Green" bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Mas del 80%</td>
        <td width=30 bgcolor="Yellow" bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Entre 80% y 60%</td>
        <td width=30 bgcolor="Red" bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Menos del 60%</td>
       </tr>       
      </table>
     </td>
    </table>
    
    <table align='center' border=1 bordercolor='#000000' bgcolor='#FFFFFF' width='80%' cellspacing=0 cellpadding=0>
     <tr>
      <td colspan=10 bordercolor='#FFFFFF'><b>Nivel de Capacitación  de Prestadores I</b></td>
     <tr>     
     <td width=30% bordercolor='#FFFFFF'>
      <table border=1 bordercolor='#FFFFFF' cellspacing=0 cellpadding=0 width=100%>
       <tr>
        <td width=30 bgcolor="Green" bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Menor al 20%</td>
        <td width=30 bgcolor="Yellow" bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Entre 20% y 40%</td>
        <td width=30 bgcolor="Red" bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Mayor al 40%</td>
       </tr>       
      </table>
     </td>
    </table>
    
     <table align='center' border=1 bordercolor='#000000' bgcolor='#FFFFFF' width='80%' cellspacing=0 cellpadding=0>
     <tr>
      <td colspan=10 bordercolor='#FFFFFF'><b>Nivel de Capacitación  de Prestadores I</b></td>
     <tr>     
     <td width=30% bordercolor='#FFFFFF'>
      <table border=1 bordercolor='#FFFFFF' cellspacing=0 cellpadding=0 width=100%>
       <tr>
        <td width=30 bgcolor="Green" bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Mayor al 70%</td>
        <td width=30 bgcolor="Yellow" bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Entre 50% y 70%</td>
        <td width=30 bgcolor="Red" bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Menor al 50%</td>
       </tr>       
      </table>
     </td>
    </table>
   
</form>
<?=fin_pagina();// aca termino ?>
