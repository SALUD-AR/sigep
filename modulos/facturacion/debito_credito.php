<?

require_once ("../../config.php");


extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

if($marcar1=="True"){
	 $db->StartTrans();
	$query="delete from facturacion.debito
             where id_debito=$id_debito";
	
    sql($query, "Error al eliminar") or fin_pagina();
    	  
   //------------calcula el debito y actualiza el monto facturado 
    $query_deb=" SELECT
					sum(cantidad * monto) as subtotal,
					facturacion.factura.monto_prefactura
					FROM
					facturacion.debito
					INNER JOIN facturacion.factura on (facturacion.debito.id_factura=facturacion.factura.id_factura)
					WHERE
					facturacion.factura.id_factura='$id_factura'
					GROUP BY
					facturacion.factura.monto_prefactura";
	    
	    $res_deb= sql($query_deb, "Error al cerrar la factura") or fin_pagina();
	    $debitos=$res_deb->fields[subtotal];
	    //-------------total credito
    $query_cred=" SELECT
					sum(cantidad * monto) as subtotal,
					facturacion.factura.monto_prefactura
					FROM
					facturacion.credito
					INNER JOIN facturacion.factura on (facturacion.credito.id_factura=facturacion.factura.id_factura)
					WHERE
					facturacion.factura.id_factura='$id_factura'
					GROUP BY
					facturacion.factura.monto_prefactura";
	    
	    $res_cred= sql($query_cred, "Error al cerrar la factura") or fin_pagina();
	    $credito=$res_cred->fields[subtotal];
	    
	    //-------------------------total facturado
    $query_t="SELECT sum 
			(facturacion.prestacion.cantidad*facturacion.prestacion.precio_prestacion) as total
			FROM
			  facturacion.factura
			  INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
			  INNER JOIN facturacion.prestacion ON (facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante)
			  where factura.id_factura=$id_factura";
		$total=sql($query_t,"NO puedo calcular el total");
		$query_t1="SELECT sum 
			(nomenclador.prestaciones_n_op.precio) as total1
			FROM
			  facturacion.factura
			  INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
			  INNER JOIN nomenclador.prestaciones_n_op using (id_comprobante)
			  where factura.id_factura=$id_factura";
		$total1=sql($query_t1,"NO puedo calcular el total");
		$monto_prefactura=$total->fields['total']+$total1->fields['total1'] + $debitos - $credito;
	  
	  
	  //-----------------------
 
	 // $monto_prefactura=$res_deb->fields[monto_prefactura] + $debitos;

	    
	    
	    $query_fact="update facturacion.factura set 
   								monto_prefactura='$monto_prefactura'   				
   					where id_factura=$id_factura";
   sql($query_fact, "Error al modificar el monto de la factura") or fin_pagina();
   
   $accion="Se elimino el debito Numero: $id_debito."; 
    $db->CompleteTrans();   
}

if($marcar2=="True"){
	 $db->StartTrans();
	$query="delete from facturacion.credito
             where id_credito=$id_credito";

    sql($query, "Error al eliminar") or fin_pagina();
    
    	    //---------calculo para actualizacion de credito en monto total
	     $query_deb=" SELECT
					sum(cantidad * monto) as subtotal,
					facturacion.factura.monto_prefactura
					FROM
					facturacion.debito
					INNER JOIN facturacion.factura on (facturacion.debito.id_factura=facturacion.factura.id_factura)
					WHERE
					facturacion.factura.id_factura='$id_factura'
					GROUP BY
					facturacion.factura.monto_prefactura";
	    
	    $res_deb= sql($query_deb, "Error al cerrar la factura") or fin_pagina();
	    $debitos=$res_deb->fields[subtotal];
		 //-------------total credito
	    $query_cred=" SELECT
					sum(cantidad * monto) as subtotal,
					facturacion.factura.monto_prefactura
					FROM
					facturacion.credito
					INNER JOIN facturacion.factura on (facturacion.credito.id_factura=facturacion.factura.id_factura)
					WHERE
					facturacion.factura.id_factura='$id_factura'
					GROUP BY
					facturacion.factura.monto_prefactura";
	    
	    $res_cred= sql($query_cred, "Error al cerrar la factura") or fin_pagina();
	    $credito=$res_cred->fields[subtotal];
	    
	    //-------------------------total facturado
   		$query_t="SELECT sum 
			(facturacion.prestacion.cantidad*facturacion.prestacion.precio_prestacion) as total
			FROM
			  facturacion.factura
			  INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
			  INNER JOIN facturacion.prestacion ON (facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante)
			  where factura.id_factura=$id_factura";
		$total=sql($query_t,"NO puedo calcular el total");
		$query_t1="SELECT sum 
			(nomenclador.prestaciones_n_op.precio) as total1
			FROM
			  facturacion.factura
			  INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
			  INNER JOIN nomenclador.prestaciones_n_op using (id_comprobante)
			  where factura.id_factura=$id_factura";
		$total1=sql($query_t1,"NO puedo calcular el total");
		$monto_prefactura=$total->fields['total']+$total1->fields['total1'] + $debitos - $credito;

//-----------ACTUALIZACION DEL MONTO DE FACTURA----------- 
	    $query_fact="update facturacion.factura set 
   								monto_prefactura='$monto_prefactura'   				
   					where id_factura=$id_factura";
   sql($query_fact, "Error al modificar el monto de la factura") or fin_pagina();
	    
	  //---------------fin de la actualizacion  
	  
    $accion="Se elimino el credito Numero: $id_credito."; 
    $db->CompleteTrans();   
}

if ($_POST['guardar']=="Guardar Debito"){
	
	$id_factura=$_POST['id_factura'];
	$id_nomenclador=$_POST['id_nomenclador'];	
	$cantidad=$_POST['cantidad'];
	$id_motivo_d=$_POST['id_motivo_d'];
		
	    $db->StartTrans();
	    
	    $q="select precio from facturacion.nomenclador where id_nomenclador=$id_nomenclador";
	    $monto=sql($q) or fin_pagina();
	    ($monto->fields['precio']=='0')?$monto=$_POST['monto']:$monto=$monto->fields['precio'];
	    
		$q="select nextval('facturacion.debito_id_debito_seq') as id_comprobante";
	    $id_comprobante=sql($q) or fin_pagina();
	    $id_comprobante=$id_comprobante->fields['id_comprobante'];	
	    $query="insert into facturacion.debito
	             (id_debito,id_factura,id_nomenclador,cantidad,id_motivo_d,monto,documento_deb,apellido_deb,nombre_deb,codigo_deb,observaciones_deb)
	             values
	             ($id_comprobante,'$id_factura','$id_nomenclador','$cantidad','$id_motivo_d','$monto','$documento_deb','$apellido_deb','$nombre_deb','$codigo_deb','$observaciones_deb')";	
	    sql($query, "Error al insertar el comprobante") or fin_pagina();	    
	    
	    //----------suma debito a la prefactura
   	    //---------calculo para actualizacion de credito en monto total
	     $query_deb=" SELECT
					sum(cantidad * monto) as subtotal,
					facturacion.factura.monto_prefactura
					FROM
					facturacion.debito
					INNER JOIN facturacion.factura on (facturacion.debito.id_factura=facturacion.factura.id_factura)
					WHERE
					facturacion.factura.id_factura='$id_factura'
					GROUP BY
					facturacion.factura.monto_prefactura";
	    
	    $res_deb= sql($query_deb, "Error al cerrar la factura") or fin_pagina();
	    $debitos=$res_deb->fields[subtotal];
		 //-------------total credito
	    $query_cred=" SELECT
					sum(cantidad * monto) as subtotal,
					facturacion.factura.monto_prefactura
					FROM
					facturacion.credito
					INNER JOIN facturacion.factura on (facturacion.credito.id_factura=facturacion.factura.id_factura)
					WHERE
					facturacion.factura.id_factura='$id_factura'
					GROUP BY
					facturacion.factura.monto_prefactura";
	    
	    $res_cred= sql($query_cred, "Error al cerrar la factura") or fin_pagina();
	    $credito=$res_cred->fields[subtotal];
	    
	    //-------------------------total facturado
   		$query_t="SELECT sum 
			(facturacion.prestacion.cantidad*facturacion.prestacion.precio_prestacion) as total
			FROM
			  facturacion.factura
			  INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
			  INNER JOIN facturacion.prestacion ON (facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante)
			  where factura.id_factura=$id_factura";
		$total=sql($query_t,"NO puedo calcular el total");
		$query_t1="SELECT sum 
			(nomenclador.prestaciones_n_op.precio) as total1
			FROM
			  facturacion.factura
			  INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
			  INNER JOIN nomenclador.prestaciones_n_op using (id_comprobante)
			  where factura.id_factura=$id_factura";
		$total1=sql($query_t1,"NO puedo calcular el total");
		$monto_prefactura=$total->fields['total']+$total1->fields['total1'] + $debitos - $credito;

//-----------ACTUALIZACION DEL MONTO DE FACTURA----------- 
	    $query_fact="update facturacion.factura set 
   								monto_prefactura='$monto_prefactura'   				
   					where id_factura=$id_factura";
   sql($query_fact, "Error al modificar el monto de la factura") or fin_pagina();
	    
	  //---------------fin de la actualizacion  
	
	  
    $accion="Se guardo el Debito Numero: $id_comprobante";
	$db->CompleteTrans();   
	        
}//de if ($_POST['guardar']=="Guardar nuevo Muleto")

if ($_POST['guardar']=="Guardar Credito"){
	
	$id_factura=$_POST['id_factura'];
	$id_nomenclador=$_POST['id_nomenclador1'];	
	$cantidad=$_POST['cantidad1'];
	$id_motivo_d=$_POST['id_motivo_d1'];
		
	    $db->StartTrans();
	    
	    $q="select precio from facturacion.nomenclador where id_nomenclador=$id_nomenclador";
	    $monto=sql($q) or fin_pagina();
	    ($monto->fields['precio']=='0')?$monto=$_POST['monto1']:$monto=$monto->fields['precio'];
	    	    
		$q="select nextval('facturacion.credito_id_credito_seq') as id_comprobante";
	    $id_comprobante=sql($q) or fin_pagina();
	    $id_comprobante=$id_comprobante->fields['id_comprobante'];	
	    $query="insert into facturacion.credito
	             (id_credito,id_factura,id_nomenclador,cantidad,id_motivo_d,monto,codigo_cred,observaciones_cred)
	             values
	             ($id_comprobante,'$id_factura','$id_nomenclador','$cantidad','$id_motivo_d','$monto','$codigo_cred','$observaciones_cred')";	
	    sql($query, "Error al insertar el comprobante") or fin_pagina();

	    //---------calculo para actualizacion de credito en monto total
	     $query_deb=" SELECT
					sum(cantidad * monto) as subtotal,
					facturacion.factura.monto_prefactura
					FROM
					facturacion.debito
					INNER JOIN facturacion.factura on (facturacion.debito.id_factura=facturacion.factura.id_factura)
					WHERE
					facturacion.factura.id_factura='$id_factura'
					GROUP BY
					facturacion.factura.monto_prefactura";
	    
	    $res_deb= sql($query_deb, "Error al cerrar la factura") or fin_pagina();
	    $debitos=$res_deb->fields[subtotal];
		 //-------------total credito
	    $query_cred=" SELECT
					sum(cantidad * monto) as subtotal,
					facturacion.factura.monto_prefactura
					FROM
					facturacion.credito
					INNER JOIN facturacion.factura on (facturacion.credito.id_factura=facturacion.factura.id_factura)
					WHERE
					facturacion.factura.id_factura='$id_factura'
					GROUP BY
					facturacion.factura.monto_prefactura";
	    
	    $res_cred= sql($query_cred, "Error al cerrar la factura") or fin_pagina();
	    $credito=$res_cred->fields[subtotal];
	    
	    //-------------------------total facturado
   		$query_t="SELECT sum 
			(facturacion.prestacion.cantidad*facturacion.prestacion.precio_prestacion) as total
			FROM
			  facturacion.factura
			  INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
			  INNER JOIN facturacion.prestacion ON (facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante)
			  where factura.id_factura=$id_factura";
		$total=sql($query_t,"NO puedo calcular el total");
		$query_t1="SELECT sum 
			(nomenclador.prestaciones_n_op.precio) as total1
			FROM
			  facturacion.factura
			  INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
			  INNER JOIN nomenclador.prestaciones_n_op using (id_comprobante)
			  where factura.id_factura=$id_factura";
		$total1=sql($query_t1,"NO puedo calcular el total");
		$monto_prefactura=$total->fields['total']+$total1->fields['total1'] + $debitos - $credito;

//-----------ACTUALIZACION DEL MONTO DE FACTURA----------- 
	    $query_fact="update facturacion.factura set 
   								monto_prefactura='$monto_prefactura'   				
   					where id_factura=$id_factura";
   sql($query_fact, "Error al modificar el monto de la factura") or fin_pagina();
	    
	  //---------------fin de la actualizacion  
	    
	    
	        
	    $accion="Se guardo el Credito Numero: $id_comprobante";
	    $db->CompleteTrans();   
	        
}//de if ($_POST['guardar']=="Guardar nuevo Muleto")

$sql="select * from facturacion.factura
		left join facturacion.smiefectores using (cuie)
	 where id_factura='$id_factura'";
$res_comprobante=sql($sql, "Error al traer los Comprobantes") or fin_pagina();

$id_factura=$res_comprobante->fields['id_factura'];
$cuie=$res_comprobante->fields['cuie'];
$nombreefector=$res_comprobante->fields['nombreefector'];

$sql="select * from nacer.efe_conv
		left join facturacion.nomenclador_detalle using (id_nomenclador_detalle)
	 where efe_conv.cuie='$cuie'";
$res_nom=sql($sql, "Error al traer los Comprobantes") or fin_pagina();

$id_nomenclador_detalle=$res_nom->fields['id_nomenclador_detalle'];
$descripcion=$res_nom->fields['descripcion'];

echo $html_header;
?>
<script>
//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos_ingresos()
{
 if(document.all.id_nomenclador.value=="-1"){
  alert('Debe Seleccionar un codigo del Nomenclador');
  return false;
 }
 if(document.all.cantidad.value==""){
  alert('Debe Ingresar una cantidad');
  return false;
 }
 if(document.all.id_motivo_d.value=="-1"){
  alert('Debe Seleccionar un Motivo');
  return false;
 }
 if (confirm('Esta Seguro que Desea Agregar Debito?'))return true;
 else return false;	
}

function control_nuevos_egresos()
{
 if(document.all.id_nomenclador1.value=="-1"){
  alert('Debe Seleccionar un codigo del Nomenclador');
  return false;
 }
 if(document.all.cantidad1.value==""){
  alert('Debe Ingresar una cantidad');
  return false;
 }
 if(document.all.id_motivo_d1.value=="-1"){
  alert('Debe Seleccionar un Motivo');
  return false;
 }
 if (confirm('Esta Seguro que Desea Agregar Credito?'))return true;
 else return false;	
}//de function control_nuevos()

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

<form name='form1' action='debito_credito.php' method='POST'>

<?echo "<center><b><font size='+2' color='red'>$accion</font></b></center>";?>
<input type="hidden" name="id_factura" value="<?=$id_factura?>">

<table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
     <font size=+1><b>Debito / Credito</b></font>    
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
           <td align="center" colspan="2">
            <b> Numero de Factura: <font size="+1" color="Red"><?=$id_factura?></font> </b>
           </td>
         </tr>
         <tr>	           
           <td align="center" colspan="2">
            <b> CUIE: <font size="+1" color="Red"><?=$cuie?></font> </b>
           </td>
         </tr>
         <tr>
         	<td align="right">
         	  <b>Nombre:
         	</td>         	
            <td align='left'>
              <input type='text' name='nombre' value='<?=$nombreefector;?>' size=60 align='right' readonly></b>
            </td>
         </tr>
         
         
         <tr>	           
           <td align="center" colspan="2" bgcolor="00FF99 ">
            <b> Factura con el Nomenclador: <font size="+1" color="Blue"><?=$descripcion?></font> </b>
           </td>
         </tr>
         
          <tr>	           
           <td align="center" colspan="2">
             <b><font size="2" color="Red">Nota: Los valores numericos se ingresan SIN separadores de miles, y con "." como separador DECIMAL</font> </b>
           </td>
         </tr>          
        </table>
      </td>      
     </tr>
   </table>     
	 <table class="bordes" align="center" width="70%">
		 <tr align="center" id="sub_tabla">
		 	<td colspan="2">	
		 		Nuevo Debito
		 	</td>
		 </tr>
		 <tr><td class="bordes"><table>
			 <tr>
				 <td>
					 <tr>
					    <td align="right">
					    	<b>Código Nomenclador:</b>
					    </td>
					    <td align="left">		          			
				 			<select name=id_nomenclador Style="width=700px"
				 			onKeypress="buscar_combo(this);"
				 			onblur="borrar_buffer();"
				 			onchange="borrar_buffer();">
			     			<option value=-1>Seleccione</option>
			                 <?
			                 
			                 	$sql= "select * from facturacion.nomenclador 
			                 			where id_nomenclador_detalle=$id_nomenclador_detalle
			                 			ORDER BY codigo";
			                 $res_efectores=sql($sql) or fin_pagina();
			                 while (!$res_efectores->EOF){ 
			                 	$id_nomenclador=$res_efectores->fields['id_nomenclador'];
			                 	$codigo=$res_efectores->fields['codigo'];
			                 	$subgrupo=$res_efectores->fields['subgrupo'];
			                 	$descripcion=$res_efectores->fields['descripcion'];
			                 	$tipo_nomenclador=$res_efectores->fields['tipo_nomenclador'];
			                 ?>
			                   <option value=<?=$id_nomenclador;?> >
			                   	<?=$codigo." - ".$descripcion?>
			                   </option>
			                 <?
			                 $res_efectores->movenext();
			                 }
			                 ?>
			      			</select>
					    </td>
					 </tr>
					 <tr>
					 	<td align="right">
					    	<b>Cantidad:</b>
					    </td>
					    <td align="left">				    	
					    	 <input type="text" name="cantidad" value="" size=30 align="right">
					    </td>		    
					 </tr>
					 
					 <tr>
					 	<td align="right">
					    	<b>Monto:</b>
					    </td>
					    <td align="left">				    	
					    	 <input type="text" name="monto" value="" size=30 align="right">
					    	 &nbsp;<b><font size="1" color="Red">* Solo lo toma en cuenta si el nomenclador es "Reservado Debito / Credito"</font> </b>
					    </td>		    
					 </tr>
					 
					 <tr>
					 	<td align="right">
					    	<b>Codigo Nuevo Nomenclador:</b>
					    </td>
					    <td align="left">				    	
					    	 <input type="text" name="codigo_deb" value="" size=30 align="right">
					    </td>		    
					 </tr>
					 
					 <tr>
					 	<td align="right">
					    	<b>Observaciones:</b>
					    </td>
					    <td align="left">				    	
					    	 <input type="text" name="observaciones_deb" value="" size=30 align="right">
					    </td>		    
					 </tr>
					 
					 <tr><td colspan="2"><table class="bordes" align="center"width="100%">
	   			     <tr>
					 	<td align="right">
					    	<b>Documento:</b>
					    </td>
					    <td align="left">				    	
					    	 <input type="text" name="documento_deb" value="" size=30 align="right">
					    </td>		    
					 </tr>
					 
					 <tr>
					 	<td align="right">
					    	<b>Apellido:</b>
					    </td>
					    <td align="left">				    	
					    	 <input type="text" name="apellido_deb" value="" size=30 align="right">
					    </td>		    
					 </tr>
					 
					 <tr>
					 	<td align="right">
					    	<b>Nombre</b>
					    </td>
					    <td align="left">				    	
					    	 <input type="text" name="nombre_deb" value="" size=30 align="right">
					    </td>		    
					 </tr>
					 </td></tr></table>
					 
					 <tr>
					    <td align="right">
					    	<b>Motivo:</b>
					    </td>
					    <td align="left">		          			
				 			<select name=id_motivo_d Style="width=400px"
				 			onKeypress="buscar_combo(this);"
				 			onblur="borrar_buffer();"
				 			onchange="borrar_buffer();">
			     			<option value=-1>Seleccione</option>
			                 <?
			                 $sql="select * from facturacion.motivo_d order by descripcion";			                 
			                 $res_efectores=sql($sql) or fin_pagina();
			                 while (!$res_efectores->EOF){ 
			                 	$id_motivo_d=$res_efectores->fields['id_motivo_d'];
			                 	$descripcion=$res_efectores->fields['descripcion'];			                 	
			                 ?>
			                   <option value=<?=$id_motivo_d;?> >
			                   	<?=$descripcion?>
			                   </option>
			                 <?
			                 $res_efectores->movenext();
			                 }
			                 ?>
			      			</select>
					    </td>
					 </tr>
					        			 					 
				  </td>
			 </tr>
		 </table></td></tr>	 
		 <tr>
		  	<td align="center" colspan="2" class="bordes">		      
		    	<input type="submit" name="guardar" value="Guardar Debito" title="Guardar Debito" Style="width=300px" onclick="return control_nuevos_ingresos()">
		    </td>
		 </tr> 
	 </table>	
 </td></tr>
 
<?//tabla de comprobantes
$query="SELECT 
  *
FROM
  facturacion.debito
  left join facturacion.nomenclador using (id_nomenclador)  
  left join facturacion.motivo_d using (id_motivo_d)  
  where id_factura='$id_factura' 
  order by codigo";
$res_comprobante=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
?>
<tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Debito" align="left" style="cursor:hand;" onclick="muestra_tabla(document.all.prueba_vida,2);" >
	  </td>	
	  <td align="center">
	   <b>Debito</b>
	  </td>  
	</tr>
</table></td></tr>
<tr><td><table id="prueba_vida" border="1" width="100%" style="display:none;border:thin groove">
	<?if ($res_comprobante->RecordCount()==0){?>
	 <tr>
	  <td align="center">
	   <font size="3" color="Red"><b>No existen Debito para este Efector</b></font>
	  </td>
	 </tr>
	 <?}
	 else{	 	
	 	?>
	 	<tr id="sub_tabla"> 		
	 		<td width="15%">ID</td>
	 		<td width="15%">Codigo</td>
	 		<td width="15%">Cantidad</td>
	 		<td width="15%">Unit</td>
	 		<td width="15%">Total</td>
	 		<td width="15%">Motivo</td>	
	 		<td width="15%">Codigo</td>	
	 		<td width="15%">Observaciones</td>	
	 		<td width="15%">Elim</td>	
	 		 		
	 	</tr>
	 	<?
	 	$res_comprobante->movefirst();
	 	while (!$res_comprobante->EOF) {	 		 
             
             $ref1 = encode_link("debito_credito.php",array("id_debito"=>$res_comprobante->fields['id_debito'],"marcar1"=>"True","id_factura"=>$id_factura));
             $id_debito=$res_comprobante->fields['id_debito'];
             $onclick_eliminar="if (confirm('Esta Seguro que Desea Eliminar Debito $id_debito ?')) location.href='$ref1'
            						else return false;	";
             
             
	 		?>
	 		<tr <?=atrib_tr()?>>	 			
		 		<td ><?=$res_comprobante->fields['id_debito']?></td>
		 		<td ><?=$res_comprobante->fields['codigo']?></td>
		 		<td ><?=number_format($res_comprobante->fields['cantidad'],0,',','.')?></td>
		 		<td ><?=number_format($res_comprobante->fields['monto'],2,',','.')?></td>
		 		<td ><?=number_format($res_comprobante->fields['cantidad']*$res_comprobante->fields['monto'],2,',','.')?></td>
		 		<td ><?=$res_comprobante->fields['descripcion']?></td>				
		 		<td ><?=$res_comprobante->fields['codigo_deb']?></td>
		 		<td ><?=$res_comprobante->fields['observaciones_deb']?></td>
		 		<td onclick="<?=$onclick_eliminar?>" align="center"><img src='../../imagenes/salir.gif' style='cursor:hand;'></td>		 			 		
		 	</tr>	
		 	
	 		<?$res_comprobante->movenext();
	 	}
	 }?>
</table></td></tr>

<tr><td>
<table class="bordes" align="center" width="70%">
	<tr align="center" id="sub_tabla">
		 	<td colspan="2">	
		 		Nuevo Credito
		 	</td>
		 </tr>
		 <tr><td class="bordes"><table>
			 <tr>
				 <td>
					 <tr>
					    <td align="right">
					    	<b>Código Nomenclador:</b>
					    </td>
					    <td align="left">		          			
				 			<select name=id_nomenclador1 Style="width=700px"
				 			onKeypress="buscar_combo(this);"
				 			onblur="borrar_buffer();"
				 			onchange="borrar_buffer();">
			     			<option value=-1>Seleccione</option>
			                 <?$sql= "select * from facturacion.nomenclador 
			                 			where id_nomenclador_detalle=$id_nomenclador_detalle
			                 			ORDER BY codigo";
			                 $res_efectores=sql($sql) or fin_pagina();
			                 while (!$res_efectores->EOF){ 
			                 	$id_nomenclador=$res_efectores->fields['id_nomenclador'];
			                 	$codigo=$res_efectores->fields['codigo'];
			                 	$subgrupo=$res_efectores->fields['subgrupo'];
			                 	$descripcion=$res_efectores->fields['descripcion'];
			                 	$tipo_nomenclador=$res_efectores->fields['tipo_nomenclador'];
			                 ?>
			                   <option value=<?=$id_nomenclador;?> >
			                   	<?=$codigo." - ".$descripcion?>
			                   </option>
			                 <?
			                 $res_efectores->movenext();
			                 }
			                 ?>
			      			</select>
					    </td>
					 </tr>
					 <tr>
					 	<td align="right">
					    	<b>Cantidad:</b>
					    </td>
					    <td align="left">				    	
					    	 <input type="text" name="cantidad1" value="" size=30 align="right">
					    </td>		    
					 </tr>
					 
					 <tr>
					 	<td align="right">
					    	<b>Monto:</b>
					    </td>
					    <td align="left">				    	
					    	 <input type="text" name="monto1" value="" size=30 align="right">
					    	 &nbsp;<b><font size="1" color="Red">* Solo lo toma en cuenta si el nomenclador es "Reservado Debito / Credito"</font> </b>
					    </td>		    
					 </tr>
					 
					 <tr>
					 	<td align="right">
					    	<b>Codigo Nuevo Nomenclador:</b>
					    </td>
					    <td align="left">				    	
					    	 <input type="text" name="codigo_cred" value="" size=30 align="right">
					    </td>		    
					 </tr>
					 
					 <tr>
					 	<td align="right">
					    	<b>Observaciones:</b>
					    </td>
					    <td align="left">				    	
					    	 <input type="text" name="observaciones_cred" value="" size=30 align="right">
					    </td>		    
					 </tr>
					 
					 <tr>
					    <td align="right">
					    	<b>Motivo:</b>
					    </td>
					    <td align="left">		          			
				 			<select name=id_motivo_d1 Style="width=400px"
				 			onKeypress="buscar_combo(this);"
				 			onblur="borrar_buffer();"
				 			onchange="borrar_buffer();">
			     			<option value=-1>Seleccione</option>
			                 <?
			                 $sql="select * from facturacion.motivo_d order by descripcion";			                 
			                 $res_efectores=sql($sql) or fin_pagina();
			                 while (!$res_efectores->EOF){ 
			                 	$id_motivo_d=$res_efectores->fields['id_motivo_d'];
			                 	$descripcion=$res_efectores->fields['descripcion'];			                 	
			                 ?>
			                   <option value=<?=$id_motivo_d;?> >
			                   	<?=$descripcion?>
			                   </option>
			                 <?
			                 $res_efectores->movenext();
			                 }
			                 ?>
			      			</select>
					    </td>
					 </tr>
					        			 					 
				  </td>
			 </tr>
		 </table></td></tr>	 
		 <tr>
		  	<td align="center" colspan="2" class="bordes">		      
		    	<input type="submit" name="guardar" value="Guardar Credito" title="Guardar Credito" Style="width=300px" onclick="return control_nuevos_egresos()">
		    </td>
		 </tr> 
	 </table>	
 </td></tr>
 
<?//tabla de comprobantes
$query="SELECT 
  *
FROM
  facturacion.credito
  left join facturacion.nomenclador using (id_nomenclador)  
  left join facturacion.motivo_d using (id_motivo_d)  
  where id_factura='$id_factura' 
  order by codigo";
$res_comprobante=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
?>
<tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Debito" align="left" style="cursor:hand;" onclick="muestra_tabla(document.all.prueba_vida1,2);" >
	  </td>	
	  <td align="center">
	   <b>Credito</b>
	  </td>  
	</tr>
</table></td></tr>
<tr><td><table id="prueba_vida1" border="1" width="100%" style="display:none;border:thin groove">
	<?if ($res_comprobante->RecordCount()==0){?>
	 <tr>
	  <td align="center">
	   <font size="3" color="Red"><b>No existen Debito para este Efector</b></font>
	  </td>
	 </tr>
	 <?}
	 else{	 	
	 	?>
	 	<tr id="sub_tabla"> 		
	 		<td width="15%">ID</td>
	 		<td width="15%">Codigo</td>
	 		<td width="15%">Cantidad</td>
	 		<td width="15%">Unit</td>
	 		<td width="15%">Total</td>
	 		<td width="15%">Motivo</td>	
	 		<td width="15%">Codigo</td>	
	 		<td width="15%">Observaciones</td>	
	 		<td width="15%">Elim</td>	
	 		 		
	 	</tr>
	 	<?
	 	$res_comprobante->movefirst();
	 	while (!$res_comprobante->EOF) {	 		 
             
             $ref1 = encode_link("debito_credito.php",array("id_credito"=>$res_comprobante->fields['id_credito'],"marcar2"=>"True","id_factura"=>$id_factura));
             $id_credito=$res_comprobante->fields['id_credito'];
             $onclick_eliminar="if (confirm('Esta Seguro que Desea Eliminar Credito $id_credito ?')) location.href='$ref1'
            						else return false;	";
	 		?>
	 		<tr <?=atrib_tr()?>>	 			
		 		<td ><?=$res_comprobante->fields['id_credito']?></td>
		 		<td ><?=$res_comprobante->fields['codigo']?></td>
		 		<td ><?=number_format($res_comprobante->fields['cantidad'],0,',','.')?></td>
		 		<td ><?=number_format($res_comprobante->fields['monto'],2,',','.')?></td>
		 		<td ><?=number_format($res_comprobante->fields['cantidad']*$res_comprobante->fields['monto'],2,',','.')?></td>
		 		<td ><?=$res_comprobante->fields['descripcion']?></td>	
		 		<td ><?=$res_comprobante->fields['codigo_cred']?></td>
		 		<td ><?=$res_comprobante->fields['observaciones_cred']?></td>			
		 		<td onclick="<?=$onclick_eliminar?>" align="center"><img src='../../imagenes/salir.gif' style='cursor:hand;'></td>		 		
		 	</tr>	
		 	
	 		<?$res_comprobante->movenext();
	 	}
	 }?>	 
</table></td></tr>


 
 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
     <input type=button name="volver" value="Volver" onclick="document.location='listado_factura.php'"title="Volver al Listado" style="width=150px">     
   </td>
  </tr>
 </table></td></tr>
 
</table>

</form>
<?=fin_pagina();// aca termino ?>
