<?
require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

if ($_POST['guardar']=="Guardar Parametros"){
	$query="update facturacion.nomenclador
			SET
			neo = $neo,
			ceroacinco = $ceroacinco,
			seisanueve = $seisanueve,
			adol = $adol,
			adulto = $adulto,
			f = $f,
			m = $m,
			precio= '$precio',
			priori= '$priori',
			catas= '$catas'
			where id_nomenclador='$id_nomenclador'";
    $val=sql($query, "Error en consulta de parametros") or fin_pagina(); 
	$accion="Se Actualizaron los Parametros";

}

if ($borra_efec == 'borra_efec') {	
	$query = "delete from facturacion.parametro_nomen  
			where id_parametro_nomen='$id_parametro_nomen'";
	
	sql ( $query, "Error al eliminar" ) or fin_pagina ();
	$accion = "Diagnostico Eliminado";
}

if ($_POST['guardar']=="Guardar Diagnostico"){
	$fecha_carga=date("Y-m-d H:i:s");
	$usuario=$_ses_user['name'];
	
	$query="select * from facturacion.parametro_nomen
			where codigo='$diag' and id_nomenclador=$id_nomenclador";
    $val=sql($query, "Error en consulta de validacion") or fin_pagina();    
    if ($val->RecordCount()==0){
	   	$db->StartTrans();
		$q="select nextval('facturacion.parametro_nomen_id_parametro_nomen_seq') as id_comprobante";
	    $id_comprobante=sql($q) or fin_pagina();
	    $id_comprobante=$id_comprobante->fields['id_comprobante'];
	    	    	
	    $query="insert into facturacion.parametro_nomen
	             (id_parametro_nomen,codigo,id_nomenclador,usuario)
	             values
	             ($id_comprobante,'$diag','$id_nomenclador','$usuario'||'-'||'$fecha_carga')";	
	    sql($query, "Error al insertar el comprobante") or fin_pagina();	    
	    $accion="Se guardo el Diagnostico";
	    
	    $db->CompleteTrans(); 
	}
	else{
		$accion="Diagnostico ya Cargado";
	}	 
}//de if ($_POST['guardar']=="Guardar nuevo Muleto")

if ($id_nomenclador){
	$sql="select * 
		 from facturacion.nomenclador	 
		 where id_nomenclador=$id_nomenclador";
	$res_comprobante=sql($sql, "Error al traer los Comprobantes") or fin_pagina();
	
	
	$codigo=$res_comprobante->fields['codigo'];
	$grupo=$res_comprobante->fields['grupo'];
	$subgrupo=$res_comprobante->fields['subgrupo'];
	$descripcion=$res_comprobante->fields['descripcion'];
	$precio=trim($res_comprobante->fields['precio']);
	$neo=trim($res_comprobante->fields['neo']);
	$ceroacinco=trim($res_comprobante->fields['ceroacinco']);
	$seisanueve=trim($res_comprobante->fields['seisanueve']);
	$adol=trim($res_comprobante->fields['adol']);
	$adulto=trim($res_comprobante->fields['adulto']);
	$f=trim($res_comprobante->fields['f']);
	$m=trim($res_comprobante->fields['m']);
	$priori=trim($res_comprobante->fields['priori']);
	$catas=trim($res_comprobante->fields['catas']);
}

echo $html_header;
?>
<script>
//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos()
{
 if(document.all.diag.value=="-1"){
  alert('Debe Seleccionar un Diagnostico');
  return false;
 } 
 if (confirm('Esta Seguro que Desea Agregar el Diagnostico?'))return true;
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

<form name='form1' action='param_admin_fin.php' method='POST'>
<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";?>
<input type="hidden" name="id_nomenclador" value="<?=$id_nomenclador?>">
<table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
     <font size=+1><b>Nomenclador</b></font>    
    </td>
 </tr>
 <tr><td>
  <table width=90% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b> Descripción del Codigo</b>
      </td>
     </tr>
		
		<tr>
         	<td align="center"><br>
			<font size="+1">
         	  <b>Descripcion: <font color="blue"><?=$descripcion?></font> </b>
         	  <b>Codigo: <font color="blue"><?=$codigo?></font> </b>
         	  <b>Grupo: <font color="blue"><?=$grupo?></font> </b>
         	  <b>Sub Grupo: <font color="blue"><?=$subgrupo?></font> </b>
         	  <b>Precio: <font color="blue"><?=$precio?></font> </b>
			 </font>
         	</td>           	         
         </tr>
		 
         <tr>
         	<td align="center"><br>
         	  <b>Neo:</b>
				<select name=neo Style="width=45px">
				  <option value=1 <?if ($neo=='1') echo "selected"?>>SI</option>
				  <option value=0 <?if ($neo=='0') echo "selected"?>>NO</option>
				</select>
         	  <b>Cero a Cinco:</b>
				<select name=ceroacinco Style="width=45px">
				  <option value=1 <?if ($ceroacinco=='1') echo "selected"?>>SI</option>
				  <option value=0 <?if ($ceroacinco=='0') echo "selected"?>>NO</option>
				</select>
         	  <b>Seis a Nueve:</b>
				<select name=seisanueve Style="width=45px">
				  <option value=1 <?if ($seisanueve=='1') echo "selected"?>>SI</option>
				  <option value=0 <?if ($seisanueve=='0') echo "selected"?>>NO</option>
				</select>
         	  <b>Adolescente:</b>
         	  <select name=adol Style="width=45px">
				  <option value=1 <?if ($adol=='1') echo "selected"?>>SI</option>
				  <option value=0 <?if ($adol=='0') echo "selected"?>>NO</option>
				</select>
         	  <b>Adulto:</b>
         	  <select name=adulto Style="width=45px">
				  <option value=1 <?if ($adulto=='1') echo "selected"?>>SI</option>
				  <option value=0 <?if ($adulto=='0') echo "selected"?>>NO</option>
				</select>
         	  <b>Femenino:</b>
         	  <select name=f Style="width=45px">
				  <option value=1 <?if ($f=='1') echo "selected"?>>SI</option>
				  <option value=0 <?if ($f=='0') echo "selected"?>>NO</option>
				</select>
         	  <b>Masculino:</b>
         	  <select name=m Style="width=45px">
				  <option value=1 <?if ($m=='1') echo "selected"?>>SI</option>
				  <option value=0 <?if ($m=='0') echo "selected"?>>NO</option>
				</select>
			</td>              
         </tr>

		<tr>
         	<td align="center"><br>
         	  <b>Catastrofica:</b>
				<select name=catas Style="width=45px">
				  <option value=1 <?if ($catas=='1') echo "selected"?>>SI</option>
				  <option value=0 <?if ($catas=='0') echo "selected"?>>NO</option>
				</select>
         	  <b>Priorizada:</b>
				<select name=priori Style="width=45px">
				  <option value=1 <?if ($priori=='1') echo "selected"?>>SI</option>
				  <option value=0 <?if ($priori=='0') echo "selected"?>>NO</option>
				</select>
         	  <b>Precio:</b>
				<input type=text name=precio value=<?=$precio?>> 	  
			</td>              
         </tr>		 
                
		<tr>
		  	<td align="center" colspan="2" class="bordes">		      
		    	<input type="submit" name="guardar" value="Guardar Parametros" title="Guardar Parametros" Style="width=300px">
		    </td>
		 </tr> 
     
   </table>   
  <br>   
	 <table class="bordes" align="center" width="90%">
		 <tr align="center" id="mo">
		 	<td colspan="2">	
		 		Vincular Diagnosticos
		 	</td>
		 </tr>
		 <tr><td class="bordes"align="center"><table>
			 <tr>
				 <td >
					 <tr>
					    <td align="right">
					    	<b>Diagnostico:</b>
					    </td>
					    <td align="left">		          			
				 			<select name=diag Style="width=700px"
				 			onKeypress="buscar_combo(this);"
				 			onblur="borrar_buffer();"
				 			onchange="borrar_buffer();">
			     			<option value=-1>Seleccione</option>
			                 <?
			                 $sql= "select *
									from nomenclador.patologias_frecuentes
									ORDER BY codigo";
			                 $res_efectores=sql($sql) or fin_pagina();
			                 while (!$res_efectores->EOF){ 
			                 	$codigo=$res_efectores->fields['codigo'];			                 	
			                 	$descripcion=$res_efectores->fields['descripcion'];			                 	
			                 ?>
			                   <option value=<?=$codigo;?> >
			                   	<?=$codigo." - ".$descripcion?>
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
		    	<input type="submit" name="guardar" value="Guardar Diagnostico" title="Guardar Diagnostico" Style="width=300px" onclick="return control_nuevos()">
		    </td>
		 </tr> 
	 </table>	
 </td></tr>
 
<?//tabla de comprobantes
$query="SELECT * FROM facturacion.parametro_nomen
		  left join nomenclador.patologias using (codigo)
  		  where id_nomenclador='$id_nomenclador'
		  order by codigo";
$res_comprobante=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
?>
<tr><td><table width="90%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar" align="left" style="cursor:hand;" onclick="muestra_tabla(document.all.prueba_vida,2);" >
	  </td>
	  <td align="center">
	   <b>Diagnosticos</b>
	  </td>
	</tr>
</table></td></tr>
<tr><td><table id="prueba_vida" border="1" width="90%" style="display:inline;border:thin groove" align='center'>
	<?if ($res_comprobante->RecordCount()==0){?>
	 <tr>
	  <td align="center">
	   <font size="3" color="Red"><b>No existen Diagnosticos</b></font>
	  </td>
	 </tr>
	 <?}
	 else{	 	
	 	?>
			<tr id="sub_tabla">	
			<td width=1%>ID</td> 	    
	 		<td width="10%">Codigo Diagnostico</td>
	 		<td width="40%">Descripcion</td>
	 		<td width="40%">usuario</td>
	 		<td width=1%>Borrar</td>	 		
	 	</tr>
	 	<?
	 	$res_comprobante->movefirst();
	 	while (!$res_comprobante->EOF) {?>
	 		
	 		<tr>
	 			<td><?=$res_comprobante->fields['id_parametro_nomen'];?> </td>	
	 			<td><?=$res_comprobante->fields['codigo'];?> </td>	
	 			<td><?=$res_comprobante->fields['descripcion'];?> </td>	
	 			<td><?=$res_comprobante->fields['usuario'];?> </td>	
	 			<?$ref = encode_link ( "param_admin_fin.php", array ("id_nomenclador" => $id_nomenclador,"id_parametro_nomen" => $res_comprobante->fields ['id_parametro_nomen'], "borra_efec" => "borra_efec"));
				$onclick_provincia = "if (confirm('Seguro que desea eliminar el Diagnostico?')) location.href='$ref'";?>
				<td align="center"><img src='../../imagenes/salir.gif' style='cursor: pointer;' onclick="<?=$onclick_provincia?>"></td>		 		
    	 	</tr>			        
	 		<?$res_comprobante->movenext();
	 	}
	 }?>
</table></td></tr>
 
 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
     <input type=button name="volver" value="Volver" onclick="document.location='param_listado.php'"title="Volver al Listado" style="width=150px">     
   </td>
  </tr>
 </table></td></tr>
 
</table>    
</form>
<?=fin_pagina();// aca termino ?>
