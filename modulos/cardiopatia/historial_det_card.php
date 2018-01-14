<?
require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();
$usuario1=$_ses_user['name'];

if ($_POST['confirmar']=="Confirmar Pago de Factura"){
	
   		    $db->StartTrans();
			    
		    $sql_fact="update cardiopatia.factura set pagado=true where id_factura='$id_factura'";
		    $res_sql_fact=sql($sql_fact,"Error al actualizar datos de la factura") or fin_pagina();
		    $accion="Se ha confirmado el pago para la factura nùmero $id_factura";
		    $db->CompleteTrans();
		    $ref = encode_link("historial_det_card.php",array("id_factura"=>$id_factura,"accion"=>$accion));
		    echo "<SCRIPT>window.location='$ref';</SCRIPT>"; 
		   
		  
           
}//de if ($_POST['guardar']=="Guardar Comprobante")

 
$sql="select * from cardiopatia.factura where id_factura='$id_factura'";
$res_fact=sql($sql, "Error al traer los datos del beneficiario") or fin_pagina();

$orden_pago=$res_fact->fields['orden_pago'];
$expediente=$res_fact->fields['expediente'];
$tipo_pago=$res_fact->fields['tipo_pago'];
$cheque_nombre=$res_fact->fields['cheque_a_nombre_de'];
$cbu=$res_fact->fields['cbu'];
$comprobante=$res_fact->fields['comprobante'];
$interno=$res_fact->fields['expediente_interno'];
$fecha_factura=$res_fact->fields['fecha_factura'];
$pagado=$res_fact->fields['pagado'];
$id_beneficiario=$res_fact->fields['id_beneficiario'];
$sql_benef="select * from cardiopatia.beneficiario where id_beneficiario='$id_beneficiario'";
$res_benef=sql($sql_benef) or die();

$afiapellido=$res_benef->fields['apellido'];
$afinombre=$res_benef->fields['nombre'];
$afidni=$res_benef->fields['dni'];
$afifechanac=$res_benef->fields['fechanacimiento'];
$edad=date("Y-m-d")-$afifechanac;

$id_efector=$res_fact->fields['id_efector'];
$sql_efect="select * from cardiopatia.efector where id_efector='$id_efector'";
$res_efec=sql($sql_efect) or die();

$nombre_efec=$res_efec->fields['nombre'];
$direccion=$res_efec->fields['domicilio'];
$cuit=$res_efec->fields['cuit'];

echo $html_header;
?>
<script>
//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos()
{  

	if ('<?=$pagado?>'){
		alert('La Factura ya se encuentra paga en el sistema');
		return false;
	}

	if (confirm('Esta Seguro que Desea Confirmar el Pago?'))return true;
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

/**********************************************************/
//funciones para busqueda abreviada utilizando teclas en la lista que muestra los clientes.
var digitos=10; //cantidad de digitos buscados
var puntero=0;
var buffer=new Array(digitos); //declaración del array Buffer
var cadena="";

function buscar_combo(obj)
{
   var letra = String.fromCharCode(event.keyCode)
   if(puntero >= digitos)
   {
       cadena="";
       puntero=0;
   }   
   //sino busco la cadena tipeada dentro del combo...
   else
   {
       buffer[puntero]=letra;
       //guardo en la posicion puntero la letra tipeada
       cadena=cadena+buffer[puntero]; //armo una cadena con los datos que van ingresando al array
       puntero++;

       //barro todas las opciones que contiene el combo y las comparo la cadena...
       //en el indice cero la opcion no es valida
       for (var opcombo=1;opcombo < obj.length;opcombo++){
          if(obj[opcombo].text.substr(0,puntero).toLowerCase()==cadena.toLowerCase()){
          obj.selectedIndex=opcombo;break;
          }
       }
    }//del else de if (event.keyCode == 13)
   event.returnValue = false; //invalida la acción de pulsado de tecla para evitar busqueda del primer caracter
}//de function buscar_op_submit(obj)

</script>

<form name='form1' action='historial_det_card.php' method='POST' enctype='multipart/form-data'>
<input type="hidden" value="<?=$usuario1?>" name="usuario1">
<input type="hidden" name="id_factura" value="<?=$id_factura?>">
<input type="hidden" name="accion" value="<?=$accion?>">


<?echo "<center><b><font size='+2' color='red'>$accion</font></b></center>";
?>

<table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor=#cccccc>
</table>
</div>
<hr>
<input type="hidden" name="id_factura" value="<?=$id_factura?>">



<table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes" border="solid 1px ">
 <tr id="mo">
    <td>
     <font size=+1><b>Facturacion Cardiopatia </b></font>    
    </td>
 </tr>
 <tr><td>
  
  <table width=70% align="center" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes" border="solid 1px " >
     <tr>
      <td id=mo colspan="2">
       <b> Descripción del Beneficiario</b>
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
             <input type='text' name='afidni' value='<?=fecha($afifechanac);?>' size=60 align='right' readonly></b>
           </td>
          </tr>
          
          <tr>
           <td align="right" title="Edad a la Fecha actual">
         	  <b> Edad:
           </td> 
           <td colspan="2">
         	 <input type='text' name='afidni' value='<?=$edad?>' size=60 align='right' readonly></b>
           </td>
          </tr>
          
          </table>
      </td>      
     </tr>
   </table>     
	 <table class="bordes" align="center" width="70%"  cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes" border="solid 1px ">
		 <tr align="center" id="sub_tabla">
		 	<td colspan="2">	
		 		Datos de la Factura 
		 		
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
				 			<input type='text' name='nombre_efec' value='<?=$nombre_efec;?>' size=60 align='right' readonly></b>
				 			
					    </td>
					 </tr>
					  <tr>
					 	<td align="right">
					    	<b>Direccion:</b>
					    </td>
					    <td align="left">
					    	     	 
					    	 <input type="text" value="<?=$direccion?>" name="direccion" size=45 Style="width=450px" readonly>
					    </td>		    
					 </tr>	
					 
					 <tr>
					 	<td align="right">
					    	<b>C.U.I.T.:</b>
					    </td>
					    <td align="left">
					    	 <input type="text" value="<?=$cuit?>" name="cuit" Style="width=450px" readonly>
					    </td>		    
					 </tr>				 
					 
					 <tr>
					 	<td align="right">
					    	<b>Tipo de Pago:</b>
					    </td>
					    <td align="left">
					    <input type="text" value="<?=$tipo_pago?>" name="tipo_pago" Style="width=450px" readonly>

					    <!--<select name=tipo_pago Style="width=150px" readonly>
					    <option value=Cheque <?if ($tipo_pago=="Cheque") echo "selected"?>>Cheque</option>
			            <option value=Deposito <?if ($tipo_pago=="Deposito") echo "selected"?>>Deposito</option>
					    </select>-->
					    </td>		    
					 </tr>	
					 
					 <tr>
					 	<td align="right">
					    	<b>Cheque a Nombre de:</b>
					    </td>
					    <td align="left">
					    	 <input type="text" value="<?=$cheque_nombre?>" name="cheque_nombre" Style="width=450px" <?php if ($tipo_pago=="Deposito")echo "disabled"?> readonly>
					    </td>		    
					 
					 </tr>
					 	 
					 	
					 <tr>
					 	
					 	<tr>
					 	<td align="right">
					    	<b>C.B.U.:</b>
					    </td>
					    <td align="left">
					    	 <input type="text" value="<?=$cbu?>" name="cbu" Style="width=450px" <?php if ($tipo_pago=="Cheque")echo "disabled"?> readonly>
					    </td>		    
					 </tr>	
					 <tr>
					 	
					 	
					 	<td align="right">
					    	<b>Nº de Comprobante:</b>
					    </td>
					    <td align="left">
					    	 <input type="text" value="<?=$comprobante?>" name="comprobante" Style="width=450px" readonly>
					    </td>		    
					
					 </tr>	
					<tr>
					 	
					 	
					 	<td align="right">
					    	<b>Nº de Expediente Interno:</b>
					    </td>
					    <td align="left">
					    	 <input type="text" value="<?=$interno?>" name="interno" Style="width=450px" readonly>
					    </td>		    
					 </tr>	
					
					<tr>
					 	<td align="right">
					    	<b>Fecha de Factura:</b>
					    </td>
					    <td align="left">
					    						    	
					    	<input type=text id=fecha_comprobante name=fecha_comprobante value='<?=$fecha_factura;?>' size=15 readonly>
					    </td>		    
					 </tr> 			  					 
				  
				  
				  
				  
				  </td>
			 </tr>
		 </table></td></tr>	 
		<table class="bordes" align="center" width="20%" border="solid 1px ">
  	    <tr align="center" id="sub_tabla">
		<td colspan="2">	
		Imprimir 
		</td> 				
		</tr>			
        <tr align="center">
        <td>
           <?
         if ($id_factura){
         $link=encode_link("factura_pdf.php", array("id_factura"=>$id_factura));?>
        <? echo "<a target='_blank' href='".$link."' title='Imprime Factura'><IMG src='$html_root/imagenes/pdf_logo.gif' height='40' width='40' border='0'></a>";
        	}?>
        </td>
        </tr>			</table>
         			

<table class="bordes" align="center" width="70%">
		 <tr align="center" id="sub_tabla">
		 	<td colspan="2">	
		 		Confrmacion de pago de factura
		 		
		 	</td>
		 </tr>
		 <tr>
		  	<td align="center" colspan="2" class="bordes">		      
		    	<input type="submit" name="confirmar" value="Confirmar Pago de Factura" title="Guardar Comprobante" Style="width=250px;height=30px" onclick="return control_nuevos()">
		   		&nbsp;&nbsp;&nbsp;
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
	   <b>Detalles de Comprobantes</b>
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
		 		<? 
		 		  
	 	   $res_comprobante->movenext();
	 		}
	 	
	 	}
	 }?>

	
		
	 </td>
	 </tr>         	                            
	 </table></td></tr>
 
 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
   	 <input type=button name="volver" value="Volver" onclick="document.location='historial_cardiopatia.php'"title="Volver al Listado" style="width=150px">     
   	 </td>
  </tr>
 </table></td></tr>
 
</table>

<br>
	
    
</form>
<?=fin_pagina();// aca termino ?>