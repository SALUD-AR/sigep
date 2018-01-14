<?
require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

if ($_POST['alta_efector']=="Alta Efector"){
 $ref = encode_link("alta_efector.php",array("id_beneficiario"=>$id_beneficiario,"id_pagina"=>$id_pagina));
		    echo "<SCRIPT>window.location='$ref';</SCRIPT>";

}

if ($_POST['guardar']=="Guardar Comprobante"){
	
			$precio=$total/$cantidad;

   		    $usuario1=$_ses_user['name'];
            	
	        $sql_efector="select nombre from cardiopatia.efector where id_efector='$id_efector'";
			$res_sql_efector=sql($sql_efector) or die;
			$nombre_efector=$res_sql_efector->fields['nombre'];
		
			$fecha_comprobante=Fecha_db($fecha_comprobante);
			$total=$precio*$cantidad;
			
			if ($id_pagina==1){        
	
	        $db->StartTrans();
			//Obtengo el Id para la tabla comprobante
			$q="select nextval('cardiopatia.seq_id_comprobante') as id_comprobante";
		    $id_comprobante=sql($q) or fin_pagina();
		    $id_comprobante=$id_comprobante->fields['id_comprobante'];	
			
			//obtengo de la pagina el valor del campo comprobante, es el numero de OP que viene en la factura
			$comprobante=$_POST['comprobante'];
			//
		    	    		    		    
		    
		    $q="select nextval('cardiopatia.seq_id_factura') as id_factura";
		    $id_factura=sql($q) or fin_pagina();
		    $id_factura=$id_factura->fields['id_factura'];
		    
		    $q="select nextval('cardiopatia.seq_id_expediente') as id_expediente";
		    $id_expediente=sql($q) or fin_pagina();
		    $id_expediente=$id_expediente->fields['id_expediente'];
		    
		    $hoy_anio=date("Y");
		    
		    $orden_pago="$id_expediente.$hoy_anio";	    
		    
		    if ($tipo_pago=="Cheque") $cbu=0;
					    else $cheque_nombre="";

			if ($id_prestacion==21){
				$id_modulo=$_POST['nomenclador'];
			};
		    
		    
		    $query="INSERT INTO cardiopatia.factura
		             (id_factura, orden_pago, id_efector,nombre,tipo_pago, cheque_a_nombre_de,cbu,expediente,
		             total,usuario,fecha_factura,comprobante,id_beneficiario,expediente_interno)
		             values
		             ($id_factura,$orden_pago,$id_efector,'$nombre_efector','$tipo_pago','$cheque_nombre',$cbu,$id_expediente,
		             $total,'$usuario1','$fecha_comprobante',$comprobante,$id_beneficiario,'$interno')";

		    sql($query, "Error al insertar la factura") or fin_pagina();	    
		    	    		 
		     $query="INSERT INTO cardiopatia.comprobantes
		             (id_comprobante,cantidad,id_factura,id_prestacion,id_diagnostico,id_modulo,comentario,valor)
		             values
		             ($id_comprobante,$cantidad,$id_factura,$id_prestacion,'$id_diagnostico','$id_modulo','$comentario',$precio)";	
		    sql($query, "Error al insertar el comprobante") or fin_pagina();	    
		    		    
		    $accion="Se guardo la factura Numero: $id_factura";
		    		    
		    $db->CompleteTrans(); 
		    $id_pagina++;
		    $ref = encode_link("factura_cardiop.php",array("id_beneficiario"=>$id_beneficiario,"id_factura"=>$id_factura,"id_efector"=>$id_efector,"id_pagina"=>$id_pagina));
		    echo "<SCRIPT>window.location='$ref';</SCRIPT>"; 
		   }//del if ($id_pagina==1)
		   
		   else {
		   	$db->StartTrans();
		   	$q="select nextval('cardiopatia.seq_id_comprobante') as id_comprobante";
		    $id_comprobante=sql($q) or fin_pagina();
		    $id_comprobante=$id_comprobante->fields['id_comprobante'];
			
		    if ($id_prestacion==21){
				$id_modulo=$_POST['nomenclador'];
			};

			$query="INSERT INTO cardiopatia.comprobantes
		             (id_comprobante,cantidad,id_factura,id_prestacion,id_diagnostico,id_modulo,comentario,valor)
		             values
		             ($id_comprobante,$cantidad,$id_factura,$id_prestacion,'$id_diagnostico','$id_modulo','$comentario',$precio)";
		    sql($query, "Error al insertar el comprobante") or die();	    
		     
		    
		    $sql_fact="select total from cardiopatia.factura where id_factura='$id_factura'";
		    $res_sql_fact=sql($sql_fact) or fin_pagina();
		    $total_fact=$res_sql_fact->fields['total'];
		    $total=$total_fact+$total;
		    
		    $sql_fact="update cardiopatia.factura set total='$total' where id_factura='$id_factura'";
		    $res_sql_fact=sql($sql_fact,"Error al actualizar los totales de la factura") or fin_pagina();
		    
		    $accion="Se guardo el Comprobante para la factura: $id_factura";
		       
		    $db->CompleteTrans();
		    
		    $id_pagina++;
		    $ref = encode_link("factura_cardiop.php",array("id_beneficiario"=>$id_beneficiario,"id_factura"=>$id_factura,"id_efector"=>$id_efector,"id_pagina"=>$id_pagina));
		    echo "<SCRIPT>window.location='$ref';</SCRIPT>"; 
		   
		   }
           
}//de if ($_POST['guardar']=="Guardar Comprobante")

 if ($_POST['guardar']=="Guardar Comprobante y Facturar"){
		    	
		 $precio=$total/$cantidad;
		 $usuario1=$_ses_user['name'];
		 
		 if ($id_pagina==1){
		 
		    $db->StartTrans();
		 	
			$q="select nextval('cardiopatia.seq_id_comprobante') as id_comprobante";
		    $id_comprobante=sql($q) or fin_pagina();
		    $id_comprobante=$id_comprobante->fields['id_comprobante'];	
			
			//obtengo de la pagina el valor del campo comprobante, es el numero de OP que viene en la factura
			$comprobante=$_POST['comprobante'];
			//
		    	    		    		    
		    $q="select nextval('cardiopatia.seq_id_factura') as id_factura";
		    $id_factura=sql($q) or fin_pagina();
		    $id_factura=$id_factura->fields['id_factura'];
		    
		    $q="select nextval('cardiopatia.seq_id_expediente') as id_expediente";
		    $id_expediente=sql($q) or fin_pagina();
		    $id_expediente=$id_expediente->fields['id_expediente'];
		    
		    $sql_efector="select nombre from cardiopatia.efector where id_efector='$id_efector'";
			$res_sql_efector=sql($sql_efector) or die;
			$nombre_efector=$res_sql_efector->fields['nombre'];
		    
		    
		    $fecha_comprobante=Fecha_db($fecha_comprobante);
		    
		    $hoy_anio=date("Y");
		    
		    $orden_pago="$id_expediente.$hoy_anio";
		    
		    if ($tipo_pago=="Cheque") $cbu=0;
					    else $cheque_nombre="";

			if ($id_prestacion==21){
				$id_modulo=$_POST['nomenclador'];
			};
		    
		    $query="INSERT into cardiopatia.factura
		             (id_factura, orden_pago, id_efector,nombre,tipo_pago, cheque_a_nombre_de,cbu,expediente,total,usuario,fecha_factura,comprobante,id_beneficiario,expediente_interno)
		             values
		             ($id_factura,$orden_pago,$id_efector,'$nombre_efector','$tipo_pago','$cheque_nombre',$cbu,$id_expediente,$total,'$usuario1','$fecha_comprobante',$comprobante,$id_beneficiario,'$interno')";
		    sql($query, "Error al insertar la factura") or fin_pagina();	    
		    		    		 
		     $query="INSERT INTO cardiopatia.comprobantes
		             (id_comprobante,cantidad,id_factura,id_prestacion,id_diagnostico,id_modulo,comentario,valor)
		             values
		             ($id_comprobante,$cantidad,$id_factura,$id_prestacion,'$id_diagnostico','$id_modulo','$comentario',$precio)";
		    sql($query, "Error al insertar el comprobante") or fin_pagina();	    
		    
		    $accion="Se guardo la Factura Numero: $id_factura";
		    		    
		    $db->CompleteTrans(); 		    
		 }//del if 
 		
		 else {
		   	$db->StartTrans();
			
		   	$q="select nextval('cardiopatia.seq_id_comprobante') as id_comprobante";
		    $id_comprobante=sql($q) or fin_pagina();
		    $id_comprobante=$id_comprobante->fields['id_comprobante'];
			if ($id_prestacion==21){
				$id_modulo=$_POST['nomenclador'];
			};

			$query="INSERT INTO cardiopatia.comprobantes
		             (id_comprobante,cantidad,id_factura,id_prestacion,id_diagnostico,id_modulo,comentario,valor)
		             values
		             ($id_comprobante,$cantidad,$id_factura,$id_prestacion,'$id_diagnostico','$id_modulo','$comentario',$precio)";
		    sql($query, "Error al insertar el comprobante") or die();	    
		    
		    $sql_fact="select total from cardiopatia.factura where id_factura='$id_factura'";
		    $res_sql_fact=sql($sql_fact) or fin_pagina();
		    $total_fact=$res_sql_fact->fields['total'];
		    $total=$total_fact+$total;
		    
		    $sql_fact="update cardiopatia.factura set total='$total' where id_factura='$id_factura'";
		    $res_sql_fact=sql($sql_fact,"Error al actualizar los totales de la factura") or fin_pagina();
		    
		    $accion="Se guardo el Comprobante Numero: $id_comprobante";
		    
		    
		    $db->CompleteTrans();
		    
		    }//del else
 
$facturar=1; 

 
 }; //del if "guardar comprobantes y facturar"

$sql="select * from cardiopatia.beneficiario where id_beneficiario='$id_beneficiario'";
$res_benef=sql($sql, "Error al traer los datos del beneficiario") or fin_pagina();

$afiapellido=$res_benef->fields['apellido'];
$afinombre=$res_benef->fields['nombre'];
$afidni=$res_benef->fields['dni'];
$afifechanac=$res_benef->fields['fechanacimiento'];

if ($id_factura){
	$query="select * from (select * from cardiopatia.factura where id_factura=$id_factura)
	as tabla left join cardiopatia.efector using (id_efector)";
	$rs_query=sql ($query) or die;
	$tipo_pago=$rs_query->fields ['tipo_pago'];
	$direccion=$rs_query->fields['domicilio'];
	$cheque_nombre=$rs_query->fields['cheque_a_nombre_de'];
	$cuenta=$rs_query->fields['numero_cuenta'];
	$cbu=$rs_query->fields['cbu'];
	$comprobante=$rs_query->fields['comprobante'];
	$interno=$rs_query->fields['expediente_interno'];
}


echo $html_header;
?>

<script language="javascript">
$(document).ready(function(){
	document.all.precio.disabled=true;
    
    // Parametros para el select con id_efector como id
    $("#id_efector").change(function () {
   		
   		$("#id_efector option:selected").each(function () {
			//alert($(this).val());
				elegido=$(this).val();
				$.ajax({
    				data: { elegido: elegido },
   					type: "POST",
    				dataType: "json",
    				url: "datos_efector.php",
					})
 			.done(function( data, textStatus, jqXHR ) {
     			if ( console && console.log ) {
        			 console.log( "La solicitud se ha completado correctamente.");
        			 $("#direccion").val(data[0].domicilio);
        			 $("#cuit").val(data[0].cuit);
        			 $("#cbu").val(data[0].cbu);
        			 $("#cuenta").val(data[0].numero_cuenta);

        			 //console.log(data);
    			}
 			})//done
			.fail(function( jqXHR, textStatus, errorThrown ) {
     			if ( console && console.log ) {
         			console.log( "La solicitud a fallado: " +  textStatus);
     			}
			});//fail

			/*$.post("datos_efector.php", { elegido: elegido }, function(data,textStatus, jqXHR){},"json")*/
						
           });//each
      })//change
	
 $("#tipo_pago").change(function () {
 	$("#tipo_pago option:selected").each(function () {
			elegido_1=$(this).val();
			//alert(elegido_1);
		if (elegido_1=="Deposito") document.all.cheque_nombre.disabled=true;
		else document.all.cheque_nombre.disabled=false;
		});
 });


  $("#id_prestacion").change(function () {
   		
   		$("#id_prestacion option:selected").each(function () {
			//alert($(this).val());
				id_prestacion=$(this).val();
				if (id_prestacion!=21){
				
					if (id_prestacion<21 || id_prestacion>=28){
					/*$.post("datos_efector.php", { id_prestacion: id_prestacion }, function(data){
					$("#id_diagnostico").html(data[0]);
					//$("#nomenclador").val(data2);
						});*/
		   			$("#precio").val("0.00");
          			document.all.precio.disabled=true;
		   			$.ajax({
		    				data: { id_prestacion: id_prestacion },
		   					type: "POST",
		    				dataType: "json",
		    				url: "datos_efector.php",
							})
		 			.done(function( data, textStatus, jqXHR ) {
		     			if ( console && console.log ) {
		        			 console.log( "La solicitud se ha completado correctamente.");
		        			 $("#id_diagnostico").html(data[0].ret);
		        			 $("#nomenclador").val(data[0].codigo+" - "+data[0].modulo);
		        			 $("#id_modulo").html(data[0].select_modulo);

		        			 //console.log(data);
		    			}
		 			})//done
					.fail(function( jqXHR, textStatus, errorThrown ) {
		     			if ( console && console.log ) {
		         			console.log( "La solicitud a fallado: " +  textStatus);
		     			}
						});//fail
					} //del if_1
					else {
						$.ajax({
		    				data: { id_prestacion: id_prestacion },
		   					type: "POST",
		    				dataType: "json",
		    				url: "datos_efector.php",
							})

		 			.done(function( data, textStatus, jqXHR ) {
		     			if ( console && console.log ) {
		        			console.log( "La solicitud se ha completado correctamente.");
		        			var sin_diagnostico = '<option value="Sin Diagnostico">No va diagnostico </option>';
							var sin_modulo = '<option value="Sin Modulo">No va Modulo </option>';
          					$("#id_diagnostico").html(sin_diagnostico);
          					$("#id_modulo").html(sin_modulo);
		        			$("#nomenclador").val(data[0].codigo+" - "+data[0].modulo);
		        			$("#precio").val(data[0].precio);
		        			document.all.precio.disabled=true;		        		
		    			}
		 			})//done

					.fail(function( jqXHR, textStatus, errorThrown ) {
		     			if ( console && console.log ) {
		         			console.log( "La solicitud a fallado: " +  textStatus);
		     			}
						});//fail
					}
				} //del if_2
				else { 
						var sin_diagnostico = '<option value="Sin Diagnostico">No va diagnostico </option>';
						var sin_modulo = '<option value="Sin Modulo">No va Modulo </option>';
          				$("#id_diagnostico").html(sin_diagnostico);
          				$("#nomenclador").val("");
          				$("#nomenclador").attr("placeholder","Ingrese el Nombre de la Practica Manualmente");
          				$("#id_modulo").html(sin_modulo);
          				$("#precio").val("");
          				$("#precio").attr("placeholder","Ingrese el Precio");
          				document.all.precio.disabled=false;

				}
		});//each
      })//change

$("#id_modulo").change(function () {
   		
   		$("#id_modulo option:selected").each(function () {
			//alert($(this).val());
				modulo=$(this).val();
				prestacion_1=$("#id_prestacion").val();
				$.ajax({
    				data: { modulo: modulo, prestacion_1: prestacion_1 },
   					type: "POST",
    				dataType: "json",
    				url: "datos_efector.php",
					})
 			.done(function( data, textStatus, jqXHR ) {
     			if ( console && console.log ) {
        			 console.log( "La solicitud se ha completado correctamente.");
        			 $("#precio").val(data);
        			 //console.log(data);
    			}
 			})//done
			.fail(function( jqXHR, textStatus, errorThrown ) {
     			if ( console && console.log ) {
         			console.log( "La solicitud a fallado: " +  textStatus);
     			}
			});//fail
		});//each
      })//change

// Parametros para el combo2
	/*$("#combo2").change(function () {
   		$("#combo2 option:selected").each(function () {
			//alert($(this).val());
				elegido=$(this).val();
				$.post("combo2.php", { elegido: elegido }, function(data){
				$("#combo3").html(data);
			});			
        });
   })*/
});//ready
</script>


<script>
//controlan que ingresen todos los datos necesarios para la factura
function control_nuevos()
{
 if(document.all.id_efector.value=="-1"){
  alert('Debe Seleccionar un EFECTOR');
  document.all.id_efector.focus();
  return false;
 }
 
  if(document.all.tipo_pago.value=="-1"){
	  alert('Debe Seleccionar un TIPO DE PAGO');
	  document.all.tipo_pago.focus();
	  return false;
	 }

 if(document.all.comprobante.value==""){
	  alert('Debe Seleccionar un NUMERO DE COMPROBANTE');
	  document.all.comprobante.focus();
	  return false;
	 }
 
 if(document.all.interno.value==""){
	  alert('Debe Seleccionar un NUMERO PARA EL EXPEDIENTE INTERNO');
	  document.all.interno.focus();
	  return false;
	 }

 if(document.all.nomenclador.value==""){
	  alert('Debe Ingresar el Nombre de la Practica');
	  document.all.nomenclador.focus();
	  return false;
	 }


 if(document.all.id_prestacion.value=="0"){
	  alert('Debe Seleccionar una PRACTICA PARA FACTURAR');
	  document.all.id_prestacion.focus();
	  return false;
	 }

 if(document.all.cantidad.value==""){
	  alert('Debe Indicar la CANTIDAD');
	  document.all.cantidad.focus();
	  return false;
	 }

 if(document.all.precio.value==""){
	  alert('Debe Indicar el Valor de la Practica Facturada');
	  document.all.precio.focus();
	  return false;
	 } 
 if (confirm('Esta Seguro que Desea Agregar Comprobante?'))return true;
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
};

function calcula_total() {

	/*var a = document.all.precio.value();
	var b = document.all.cantidad.value();
	var c = a * b;

	document.all.total.value(c);*/

	
	var a = $("#precio").val();
	var b = $("#cantidad").val();
	if (isNaN(a)) {alert ("El numero ingresado como Precio es Invalido, el separador decimal es el (.) punto"); document.all.precio.focus()}
	else {	
		if (isNaN(b)) {alert ("El numero ingresado como Cantidad es Invalido"); document.all.cantidad.focus()}
		else {	
			var c = a*b;
			$("#total").val(c);}
		};

}




</script>

<form name='form1' action='factura_cardiop.php' method='POST' enctype='multipart/form-data'>

<!--<input type="hidden" name="id_efector" value="<?=$id_efector?>">-->
<input type="hidden" name="id_pagina" value="<?=$id_pagina?>">
<input type="hidden" name="id_beneficiario" value="<?=$id_beneficiario?>">
<input type="hidden" name="id_factura" value="<?=$id_factura?>">





<?echo "<center><b><font size='+2' color='red'>$accion</font></b></center>";



//$cantidad=$_POST['cantidad'];
$id_prestacion_1=$_POST['id_prestacion'];
//$precio=$_POST['precio'];
$id_modulo=$_POST['id_modulo'];
$modulo_arr = array (
		"dia_estada_prequirurgico" => "Dia Estada Prequirurgicos",
		"acto_quirurgico" => "Acto Quirurgico",
		"dia_estada_postquirurgico_uti" => "Dia Estada Postquirurgico UTI",
		"medicacion_postquirurgica" => "Medicacion Postquirurgica",
		"dia_estada_postquirurgico_con_medicacion_uti" => "Dia Estada Postquirurgico con Medicacion UTI",
		"dia_estada_postquirurgico_en_sala_comun" => "Dia Estada Postquirurgico en Sala Comun",
	);


?>

<table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor=#cccccc>
</table>
</div>
<hr>
<input type="hidden" name="id_smiafiliados" value="<?=$id_beneficiario?>">



<table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
     <font size=+1><b>Facturacion Cardiopatia <?if ($pagina_listado=='factura_cardiop.php') echo "<font color=red>Verificando HISTORICOS </font>";?></b></font>    
    </td>
 </tr>
 <tr><td>
  <table class="bordes" align="center" width="70%" bordercolor=#E0E0E0 border="solid 1px ">
     <tr>
      <td id=mo colspan="2">
       <b> Descripci&oacute;n del Beneficiario</b>
      </td>
     </tr>
     <tr>
       <td>
        
        <table>
        <tr>
         	<td align="right">
         	  <b>Apellido:
         	</td>         	
            <td align='left'>
              <input type='text' name='afiapellido' value='<?=$afiapellido;?>' size=60 align='right' readonly></b>
            </td>
         </tr>
         <tr>
            <td align="right">
         	  <b> Nombre:
         	</td>   
           <td  colspan="2">
             <input type='text' name='afinombre' value='<?=$afinombre;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
          <tr>
           <td align="right">
         	  <b> Documento:
         	</td> 
           <td colspan="2">
             <input type='text' name='afidni' value='<?=$afidni;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
           <tr>
           <td align="right">
         	  <b> Fecha de Nacimiento:
         	</td> 
           <td colspan="2">
             <input type='text' name='fecha_nac' value='<?=fecha($afifechanac);?>' size=60 align='right' readonly></b>
           </td>
          </tr>
          
          <tr>
           <td align="right" title="Edad a la Fecha actual">
         	  <b> Edad:
           </td> 
           <td colspan="2">
         	 <input type='text' name='edad' value='<?=date("Y-m-d")-$afifechanac?>' size=60 align='right' readonly></b>
           </td>
          </tr>
          
          </table>
      </td>      
     </tr>
   </table>
   &nbsp;&nbsp;&nbsp;    
	<table class="bordes" align="center" width="70%" bordercolor=#E0E0E0 border="solid 1px ">
		 <tr align="center" id="sub_tabla">
		 	<td colspan="2">	
		 		<b>Datos de la Factura </b>
		 	</td>
		 </tr>
		 <tr><td class="bordes"><table>
			 <tr>
				 <td>
					 <tr>
					    <td align="right">
					    	<b>Efector:</b>
					    </td>
					    <td align="left">		          			
				 			<select name=id_efector id="id_efector" Style="width=450px" >
				
			 <?
			 if (!$id_efector) {
			 $sql= "select * from cardiopatia.efector order by nombre";
			 echo "<option value=-1>Seleccione</option>";
			 		  		  		   
			 $res_efectores=sql($sql) or fin_pagina();
			 while (!$res_efectores->EOF){ 
			 	$id_efector=$res_efectores->fields['id_efector'];
			    $nombre_efector=$res_efectores->fields['nombre'];
				?>
				<option value=<?=$id_efector;?> Style="background-color: <?=$color_style?>;"><?=$id_efector." - ".$nombre_efector?></option>
			    <?
			    $res_efectores->movenext();
			    }
			 } else {
			 $sql= "select * from cardiopatia.efector where id_efector='$id_efector'";
			 $res_efectores=sql($sql) or fin_pagina();
			 $nombre_efector=$res_efectores->fields['nombre'];
			 ?> <option value=<?=$id_efector;?> Style="background-color: <?=$color_style?>;"><?=$id_efector." - ".$nombre_efector?></option>
			  <? }?>
			
			</select>
			<td></b>
			<input type="submit" name="alta_efector" value="Alta Efector" title="Alta Efector" Style="width=120px" >	 			
					    </td>
					 </tr>
					  <tr>
					 	<td align="right">
					    	<b>Direccion:</b>
					    </td>
					    <td align="left">
					    	 <?/*if ($id_efector) $direccion=$res_efectores->fields['domicilio'];
					    	 else $direccion="";*/?>
					    	 
					    	 <input type="text" id="direccion" value="<?=$direccion?>" name="direccion" size=45 readonly>
					    </td>		    
					 </tr>	
					 
					 <tr>
					 	<td align="right">
					    	<b>C.U.I.T.:</b>
					    </td>
					    <td align="left">
					    	 <?if ($id_efector) $cuit=$res_efectores->fields['cuit'];
					    	 else $cuit="";?>
					    	 <input type="text" id="cuit" value="<?=$cuit?>" name="cuit" Style="width=450px" readonly>
					    </td>		    
					 </tr>				 
					 
					 <tr>
					 	<td align="right">
					    	<b>Tipo de Pago:</b>
					    </td>
					    <td align="left">
					    <select name=tipo_pago id="tipo_pago" Style="width=150px">
					   <? if ($id_efector and $res_efectores->fields['cbu']) $tipo_pago="Deposito"?>
					    <?if ($id_factura) echo "disabled"?>
					    <?echo "<option value=-1>Seleccione</option>";?>
					    <option value=Cheque <?if ($tipo_pago=="Cheque") echo "selected"?>>Cheque</option>
			            <option value=Deposito <?if ($tipo_pago=="Deposito") echo "selected"?>>Deposito</option>
					    </select>
					    </td>		    
					 </tr>	
					 
					 <tr>
					 	<td align="right">
					    	<b>Cheque a Nombre de:</b>
					    </td>
					    <td align="left">
					    	 <input <?if ($id_factura) echo "disabled"?> type="text" value="<?=$cheque_nombre?>" id="cheque_nombre" name="cheque_nombre" Style="width=450px" <?php if ($tipo_pago=="Deposito")echo "disabled"?>>
					    </td>		    
					 </tr>	
					 <tr>
					 	
					 	<tr>
					 	<td align="right">
					    	<b>C.B.U.:</b>
					    </td>
					    <td align="left">
					    	 <?if ($id_efector) $cbu=$res_efectores->fields['cbu'];
					    	 else $cbu="";?>
					    	 <input <?if ($id_factura || $tipo_pago=="Cheque") echo "disabled"?> type="text" id="cbu" value="<?=$cbu?>" name="cbu" Style="width=450px" <?php if ($id_efector && $res_efectores->fields['cbu'])echo "readonly"?>>
					    </td>		    
					 </tr>	
					 <tr>
					 <tr>
					 	<td align="right">
					    	<b>N&ordm; de Cuenta:</b>
					    </td>
					    <td align="left">
					    	 <?if ($id_efector) $cuenta=$res_efectores->fields['numero_cuenta'];
					    	 else $cuenta="";?>
					    	 <input <?if ($id_factura || $tipo_pago=="Cheque") echo "disabled"?> type="text" value="<?=$cuenta?>" id="cuenta" name="cuenta" Style="width=450px" <?php if ($id_efector and $res_efectores->fields['numero_cuenta'])echo "readonly"?>>
					    </td>		    
					 </tr>	
					 <tr>	
					 	
					 	<td align="right">
					    	<b>N&ordm; de Comprobante:</b>
					    </td>
					    <td align="left">
					    	 <input <?if ($id_factura) echo "disabled"?> type="text"  id="numero_cuenta" value="<?=$comprobante?>" name="comprobante" Style="width=450px">
					    </td>		    
					 </tr>	
					<tr>	
					 	
					 	<td align="right">
					    	<b>N&ordm; de Expediente Interno:</b>
					    </td>
					    <td align="left">
					    	 <input <?if ($id_factura) echo "disabled"?> type="text" value="<?=$interno?>" name="interno"   Style="width=450px">
					    </td>		    
					 </tr>	
					
					
					<tr>
					 	<td align="right">
					    	<b>Fecha de Factura:</b>
					    </td>
					    <td align="left">
					    						    	
					    	<?$fecha_comprobante=date("d/m/Y");?>
					    	 <input type=text id=fecha_comprobante name=fecha_comprobante value='<?=$fecha_comprobante;?>' size=15 readonly>
					    	 <?=link_calendario("fecha_comprobante");?>					    	 
					    </td>		    
					 </tr> 			  					 
				  
				  
				  
				  
				  </td>
			 </tr>
		 </table>
	</table>

&nbsp;&nbsp;&nbsp;
<table class="bordes" align="center" width="70%" bordercolor=#E0E0E0 border="solid 1px ">
		 <tr align="center" id="sub_tabla">
		 	<td colspan="2">	
		 		Datos de Prestaci&oacute;n 
		 		
		 	</td>
		 </tr>
		 <tr><td class="bordes"><table>
			 <tr>
				 <td>
			    	<tr>
					    <td align="right">
					    	<b>Practica:</b>
					    </td>
					    <td align="left">		          			
				 			<select name=id_prestacion  id="id_prestacion" Style="width=600px">
				 			<?
			                 if (!$id_prestacion) {?>
			                 <option value=0>Seleccione</option>	
			                 <?$sql= "select * from cardiopatia.prestaciones order by id_prestacion";
			                 $res_practica=sql($sql) or fin_pagina();
			                 while (!$res_practica->EOF){ 
			                 	$color=$res_practica->fields['color'];
			                 	$id_prestacion=$res_practica->fields['id_prestacion'];
			                 	$codigo=$res_practica->fields['codigo'];
			                 	$patologia=$res_practica->fields['patologia'];
			                 	$cirugia=$res_practica->fields['cirugia'];
			                 ?>
			                   <option <?=$color?> value=<?=$id_prestacion;?>><?=$codigo." - ".$patologia." - ".$cirugia?></option>
			                 <?
			                 $res_practica->movenext();
			                  }
			                 }
			                 else {$sql="select * from cardiopatia.prestaciones where id_prestacion='$id_prestacion'";
			                 $res_practica=sql($sql) or fin_pagina();
			                 $codigo=$res_practica->fields['codigo'];
			                 $patologia=$res_practica->fields['patologia'];
			                 $cirugia=$res_practica->fields['cirugia'];
			                 $id_prestacion_1=$id_prestacion;
			                 
			               ?>              
			      			<option value=<?=$id_prestacion;?>><?=$codigo." - ".$patologia." - ".$cirugia?></option>
			      			
			                 <?}?>	
			      			</select>
					    </td>
					 </tr>
			<tr>
					    <td align="right">
					    	<b>Codigo Diagostico:</b>
					    </td>
					    <td align="left">		          			
				 			<select name=id_diagnostico id="id_diagnostico" Style="width=600px">
			     		   	<?$id_diagnostico=0?>
			     		   	<option value=<?=$id_diagnostico?>>Debe selecionar una practica</option>	
			              	</select>
					    </td>
					 </tr>
			</table>
		</td>
	</tr>
</table>
&nbsp;&nbsp;&nbsp; 
<table class="bordes" align="center" width="70%" bordercolor=#E0E0E0 border="solid 1px ">
		 <tr align="center" id="sub_tabla">
		 	<td colspan="2">	
		 		<b> Modulo Quirurgico</b>
		 	</td>
		 </tr>
				 <tr><td class="bordes"><table>
				 <tr>	
						
					    	 <input size=100  type="text" value="<?=$modulo?>" name="nomenclador" id="nomenclador">
					    
				 </tr>
					  <tr>
					 	<td align="right">
					    	<b>Modulo:</b>
					    </td>
					    <td align="left">
					    	<select name=id_modulo id="id_modulo" Style="width=600px">
	                 		<option value=0>Seleccione</option>
	                 		<option value="dia_estada_prequirurgico">Dia Estada Prequirurgico</option>
	                 		<option value="acto_quirurgico">Acto Quirurgico</option>
	                 		<option value="dia_estada_postquirurgico_uti">Dia Estada Postquirurgico UTI</option>
	                 		<option value="medicacion_postquirurgica">Medicacion Postquirurgica</option>
	                 		<option value="dia_estada_postquirurgico_con_medicacion_uti">Dia Estada Postquirurgico con Medicacion UTI</option>
	                 		<option value="dia_estada_postquirurgico_en_sala_comun">Dia Estada Postquirurgico en Sala Comun</option>
   			                </select>
					    </td>		    
					 </tr>	
					 
					 <tr>
					 	<td align="right">
					    	<b>Precio por Nomenc.:</b>
					    </td>
					    <td align="left">
					    	 <?$precio=($precio)?$precio:0;
					    	 ?>
					    	 <input  type="text" align="left" value="<?=number_format($precio,2,'.',',')?>" id="precio" name="precio" Style="width=150px">
						</td>		    
					 </tr>
					</td>
				</tr>
				

					 
					 <tr>
					 	<td align="right">
					    	<b>Cantidad:</b>
					    </td>
					    <td align="left">
					    <input aling="left" type="text" value="<?=$cantidad?>" id="cantidad" name="cantidad" Style="width=100px" onblur="calcula_total()">
					    </td>		    
					 </tr>	
					 
					 	
					 <tr>
					 	<td align="right">
					    	<b>Total:</b>
					    </td>
					    <td align="left">
					    	<input type="text" value="<?=number_format((($total)?$total:0),2,'.',',')?>" id="total" name="total" Style="width=100px" readonly>
					    	</td>		    
					 </tr>	


					 <tr>
					 	<td align="right">
					    	<b>Comentario:</b>
					    </td>
					    <td align="left">
					    	<textarea cols='70' rows='3' name='comentario' value=<?=$comentario?>  name='comentario' ></textarea>
					    </td>		    
					 </tr>
					</td>
				</tr>
			</table>	
			 
	</table>

		 <tr>
		  	<td align="center" colspan="2" class="bordes">		      
		    	<input type="submit" name="guardar" value="Guardar Comprobante" title="Guardar Comprobante" Style="width=250px;height=30px" onclick="return control_nuevos()">
		   		&nbsp;&nbsp;&nbsp;
		    	<input type="submit" name="guardar" value="Guardar Comprobante y Facturar" title="Guardar Comprobante y Facturar" Style="width=250px;height=30px" onclick="return control_nuevos()">
		    	
        </td>
		 </tr> 
	 </table>	
 </td></tr>
 


<tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Comprobantes" align="left" style="cursor:pointer;" onclick="muestra_tabla(document.all.prueba_vida,2);" >
	  </td>
	  <td align="center">
	   <b>Comprobantes</b>
	  </td>
	</tr>
</table></td></tr>

<tr><td><table id="prueba_vida" border="1" width="100%" style="display:none;border:thin groove">
	    <tr>
	  <td align="center">
	   <?if ($id_factura) {
		
	   	$query="SELECT cardiopatia.comprobantes.id_prestacion,
	cardiopatia.comprobantes.id_comprobante,
	cardiopatia.comprobantes.cantidad,
	cardiopatia.comprobantes.id_factura,
	cardiopatia.comprobantes.comentario,
	cardiopatia.comprobantes.valor as neto,
	cardiopatia.prestaciones.codigo,
	cardiopatia.prestaciones.patologia,
	cardiopatia.prestaciones.cirugia,
	cardiopatia.comprobantes.id_modulo
	from cardiopatia.comprobantes
	inner join cardiopatia.prestaciones on cardiopatia.comprobantes.id_prestacion=cardiopatia.prestaciones.id_prestacion
	where cardiopatia.comprobantes.id_factura=$id_factura";

		$res_comprobante=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();?>
		<?if ($res_comprobante->RecordCount()==0)
	   {?>
	   <font size="3" color="Red"><b>No existen comprobantes para esta Factura</b></font>
	  <?}
	 else{	 	
	 	?>
	 	<tr id="sub_tabla">	
	 	    <td width="10%">N&uacute;mero de Comprobante</td>
	 		<td width="10%">Id Prestacion</td>
	 		<td width="10%">Codigo</td>
	 		<td width="35%">Patologia/Practica</td>
	 		<td width="35%">Cirugia</td>
	 		<td width="25%">Comentario</td>	 		
	 		<td width="20%">Valor</td>
	 		<td width="20%">Cant.</td>
	 		<td width="20%">Total</td>
	 		</tr>
	 	<?
	 	$res_comprobante->movefirst();
	 	while (!$res_comprobante->EOF) {?>
	 			<tr <?=atrib_tr()?>>
	 			
		 		<td align="center" bgcolor='<?=$color_fondo?>'><font size="2" color="Red"><b><?=$res_comprobante->fields['id_comprobante']?></b></font></td>
		 		<td align="center"> <?=$res_comprobante->fields['id_prestacion']?></td>
		 		<td align="center"><?=$res_comprobante->fields['codigo']?></td>
		 		<td align="center"> <?=(($res_comprobante->fields['id_prestacion']!=21)?$res_comprobante->fields['patologia']:$res_comprobante->fields['id_modulo'])?></td>
		 		<td align="center"> <?=$res_comprobante->fields['cirugia']?></td>
		 		<td align="center"> <?=$res_comprobante->fields['comentario']?></td>
		 		<td align="center"> $ <?=number_format($res_comprobante->fields['neto'],2,'.','')?></td>
		 		<td align="center"> <?=$res_comprobante->fields['cantidad']?></td>
		 		<?$total=$res_comprobante->fields['neto']*$res_comprobante->fields['cantidad']?>
		 		<td align="center">$ <?=number_format($total,2,'.','')?></td>
		 		<?$res_comprobante->movenext();
		 		 } 
		 		  
	 	   }//del else

	     }//if $factura
	
	else {?>
	<font size="3" color="Red"><b>Aun no se han creado datos para la factura</b></font>
		<?}?>
	 </td>
	</tr>         	                            
</table></td></tr>
 
 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
   	 <input type=button name="volver" value="Volver" onclick="document.location='listado_benef_fact.php'"title="Volver al Listado" style="width=150px">     
   	 </td>
  </tr>
 </table></td></tr>
 
	</table>
	<br>
</form>
<?=fin_pagina();// aca termino ?>