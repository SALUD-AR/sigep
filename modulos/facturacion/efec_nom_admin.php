<?

require_once ("../../config.php");


extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

if ($_POST['guardar']=="Guardar Prestacion"){
	$fecha_carga=date("Y-m-d H:i:s");
	$cuie=$_POST['cuie'];
	$nomenclador=$_POST['nomenclador'];
	$usuario=$_ses_user['name'];
	
	$query="select codigo from facturacion.nomenclador
			where id_nomenclador='$nomenclador'";
    $val=sql($query, "Error en consulta de validacion") or fin_pagina();  
    $nomenclador=$val->fields['codigo'];
	
	$query="select * from facturacion.efec_nom
			where cuie='$cuie' and codigo='$nomenclador'";
    $val=sql($query, "Error en consulta de validacion") or fin_pagina();    
    if ($val->RecordCount()==0)
    {
    	$db->StartTrans();
		$q="select nextval('facturacion.efec_nom_id_efec_nom_seq') as id_comprobante";
	    $id_comprobante=sql($q) or fin_pagina();
	    $id_comprobante=$id_comprobante->fields['id_comprobante'];
	    	    	
	    $query="insert into facturacion.efec_nom
	             (id_efec_nom, cuie, codigo, usuario, fecha_carga)
	             values
	             ($id_comprobante,'$cuie','$nomenclador','$usuario','$fecha_carga')";	
	    sql($query, "Error al insertar el comprobante") or fin_pagina();	    
	    $accion="Se guardo la Prestacion $nomenclador";
	    
	    $db->CompleteTrans(); 
	}
	
	else $accion="Ya esta cargada la prestacion $nomenclador, para este efector.";  	      
	        
}//de if ($_POST['guardar']=="Guardar nuevo Muleto")

echo $html_header;
?>
<script>
//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos()
{
 if(document.all.nomenclador.value=="-1"){
  alert('Debe Seleccionar una PRESTACION');
  return false;
 } 
 if (confirm('Esta Seguro que Desea Agregar Prestacion?'))return true;
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

<form name='form1' action='efec_nom_admin.php' method='POST'>

<?echo "<center><b><font size='+2' color='red'>$accion</font></b></center>";?>

<input type="hidden" name="cuie" value="<?=$cuie?>">
<input type="hidden" name="nombreefector" value="<?=$nombreefector?>">
<table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
     <font size=+1><b>Efector</b></font>    
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
         	<td align="right">
         	  <b>CUIE:
         	</td>         	
            <td align='left'>
              <font size="+1" color="Red"><?=$cuie?></font> </b>
            </td>
         </tr>
         <tr>
         	<td align="right">
         	  <b>Efector Asignado:
         	</td>         	
            <td align='left'>
              <font size="+1" color="Red"><?=$nombreefector?></font> </b>
            </td>
         </tr>         
        </table>
      </td>      
     </tr>
   </table>     
	 <table class="bordes" align="center" width="70%">
		 <tr align="center" id="sub_tabla">
		 	<td colspan="2">	
		 		Nuevo Codigo de Prestacion
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
				 			<select name=nomenclador Style="width=700px"
				 			onKeypress="buscar_combo(this);"
				 			onblur="borrar_buffer();"
				 			onchange="borrar_buffer();">
			     			<option value=-1>Seleccione</option>
			                 <?
			                 $sql= "select distinct id_nomenclador, codigo, nomenclador.descripcion as nombre_nom , nomenclador_detalle.descripcion,id_nomenclador_detalle
									from facturacion.nomenclador 
									left join facturacion.nomenclador_detalle using (id_nomenclador_detalle)
									ORDER BY id_nomenclador_detalle DESC, codigo";
			                 $res_efectores=sql($sql) or fin_pagina();
			                 while (!$res_efectores->EOF){ 
			                 	$id_nomenclador=$res_efectores->fields['id_nomenclador'];
			                 	$codigo=$res_efectores->fields['codigo'];			                 	
			                 	$nombre_nom=$res_efectores->fields['nombre_nom'];			                 	
			                 	$descripcion=$res_efectores->fields['descripcion'];			                 	
			                 ?>
			                   <option value=<?=$id_nomenclador;?> >
			                   	<?=$descripcion." - ".$codigo." - ".$nombre_nom?>
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
		    	<input type="submit" name="guardar" value="Guardar Prestacion" title="Guardar Prestacion" Style="width=300px" onclick="return control_nuevos()">
		    </td>
		 </tr> 
	 </table>	
 </td></tr>
 
<?//tabla de comprobantes
$query="SELECT * FROM facturacion.efec_nom
  INNER JOIN facturacion.nomenclador ON (facturacion.efec_nom.codigo = facturacion.nomenclador.codigo)
  where cuie='$cuie'
  order by efec_nom.codigo";
$res_comprobante=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
?>
<tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Comprobantes" align="left" style="cursor:hand;" onclick="muestra_tabla(document.all.prueba_vida,2);" >
	  </td>
	  <td align="center">
	   <b>Prestaciones</b>
	  </td>
	</tr>
</table></td></tr>
<tr><td><table id="prueba_vida" border="1" width="100%" style="display:none;border:thin groove">
	<?if ($res_comprobante->RecordCount()==0){?>
	 <tr>
	  <td align="center">
	   <font size="3" color="Red"><b>No existen comprobantes para este beneficiario</b></font>
	  </td>
	 </tr>
	 <?}
	 else{	 	
	 	?>
	 	<tr id="sub_tabla">		 	    
	 		<td width="10%">Codigo Prestacion</td>
	 		<td width="40%">Descripcion</td>
	 		<td width="40%">Usuario Carga</td>
	 		<td width="40%">Fecha Carga</td>
	 		
	 	</tr>
	 	<?
	 	$res_comprobante->movefirst();
	 	while (!$res_comprobante->EOF) {?>
	 		
	 		<tr>
	 			<td><?=$res_comprobante->fields['codigo'];?> </td>	
	 			<td><?=$res_comprobante->fields['descripcion'];?> </td>			 		
	 			<td><?=$res_comprobante->fields['usuario'];?> </td>			 		
	 			<td><?=$res_comprobante->fields['fecha_carga'];?> </td>			 		
		 	</tr>			        
	 		<?$res_comprobante->movenext();
	 	}
	 }?>
</table></td></tr>
 
 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
     <input type=button name="volver" value="Volver" onclick="document.location='efec_nom_listado.php'"title="Volver al Listado" style="width=150px">     
   </td>
  </tr>
 </table></td></tr>
 
</table>    
</form>
<?=fin_pagina();// aca termino ?>
