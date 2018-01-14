<?


require_once ("../../config.php");


extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();
$usuario1=$_ses_user['id'];
if($marcar=="True"){
	 $db->StartTrans();
	$query="update facturacion.comprobante set
             marca=1
             where id_comprobante=$id_comprobante";

    sql($query, "Error al marcar el comprobante") or fin_pagina();
    $accion="Se marco el Comprobante Numero: $id_comprobante, como anulado";    
    /*cargo los log*/ 
    $usuario=$_ses_user['name'];
    $fecha_carga=date("Y-m-d H:i:s");
	$log="insert into facturacion.log_comprobante 
		   (id_comprobante, fecha, tipo, descripcion, usuario) 
	values ($id_comprobante, '$fecha_carga','Comprobante Anulado','Nro. Comprobante $id_comprobante', '$usuario')";
	sql($log) or fin_pagina();
	 
    $db->CompleteTrans();   
}
function suma_fechas($fecha,$ndias){
      if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha))
      	list($dia,$mes,$a�)=split("/", $fecha);
      if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha))
        list($dia,$mes,$a�)=split("-",$fecha);
      $nueva = mktime(0,0,0, $mes,$dia,$a�) + $ndias * 24 * 60 * 60;
      $nuevafecha=date("d-m-Y",$nueva);
      return ($nuevafecha);  
}

if (($_POST['guardar']=="Guardar Comprobante")||($_POST['guardar']=="Guardar Comprobante y Facturar")){
	
		$fecha_carga=date("Y-m-d H:i:s");
		$cuie=$_POST['efector'];
		$nom_medico=$_POST['nom_medico'];
		$fecha_comprobante=$_POST['fecha_comprobante'];
		$comentario=$_POST['comentario'];
		$fecha_comprobante=Fecha_db($fecha_comprobante);		    
	       
	      $db->StartTrans();
			$q="select nextval('comprobante_id_comprobante_seq') as id_comprobante";
		    $id_comprobante=sql($q) or fin_pagina();
		    $id_comprobante=$id_comprobante->fields['id_comprobante'];	
		    
		    $query="insert into facturacion.comprobante
		             (id_comprobante, cuie, nombre_medico, fecha_comprobante, clavebeneficiario, id_beneficiarios, fecha_carga,periodo,comentario,id_servicio,activo)
		             values
		             ($id_comprobante,'$cuie','$nom_medico','$fecha_comprobante','$clavebeneficiario', $id,'$fecha_carga','$periodo','$comentario','$servicio','N')";	
		    sql($query, "Error al insertar el comprobante") or fin_pagina();	    
		    $accion="Se guardo el Comprobante.";	    /*cargo los log*/ 
		    $usuario=$_ses_user['name'];
			$log="insert into facturacion.log_comprobante 
				   (id_comprobante, fecha, tipo, descripcion, usuario) 
			values ($id_comprobante, '$fecha_carga','Nuevo Comprobante','Nro. Comprobante $id_comprobante', '$usuario')";
			sql($log) or fin_pagina();		 
		    $db->CompleteTrans(); 
		    if ($_POST['guardar']=="Guardar Comprobante y Facturar"){
	 			$ref = encode_link("prestacion_admin.php",array("id"=>$id,"id_comprobante"=>$res_comprobante->fields['id_comprobante'],"pagina_viene"=>"comprobante_admin_total.php","pagina_listado"=>$pagina_listado));
		    	echo "<SCRIPT>window.location='$ref';</SCRIPT>"; 
		    	exit();
		    }
	       
}//de if ($_POST['guardar']=="Guardar nuevo Muleto")

$sql="select 
		  leche.beneficiarios.id_beneficiarios as id,
		  leche.beneficiarios.apellido as a,
		  leche.beneficiarios.nombre as b,
		  leche.beneficiarios.documento as c,
		  leche.beneficiarios.fecha_nac as d,
		  leche.beneficiarios.domicilio as e
	 from leche.beneficiarios	 
	 where id_beneficiarios=$id";
$res_comprobante=sql($sql, "Error al traer los Comprobantes") or fin_pagina();


$a=$res_comprobante->fields['a'];
$b=$res_comprobante->fields['b'];
$c=$res_comprobante->fields['c'];
$d=$res_comprobante->fields['d'];
$e=$res_comprobante->fields['e'];


echo $html_header;
?>
<script>
//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos()
{
 if(document.all.efector.value=="-1"){
  alert('Debe Seleccionar un EFECTOR');
  return false;
 }
 <?if ($pagina=='listado_beneficiarios_hist.php'){?>
   if(document.all.periodo.value=="-1"){
  	alert('Debe Seleccionar un PERIODO');
  	return false;
   }
 <?}?> 
 if(document.all.servicio.value=="-1"){
  alert('Debe Seleccionar un Servicio');
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
}

/**********************************************************/
//funciones para busqueda abreviada utilizando teclas en la lista que muestra los clientes.
var digitos=10; //cantidad de digitos buscados
var puntero=0;
var buffer=new Array(digitos); //declaraci�n del array Buffer
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
   event.returnValue = false; //invalida la acci�n de pulsado de tecla para evitar busqueda del primer caracter
}//de function buscar_op_submit(obj)

</script>

<form name='form1' action='comprobante_admin_total.php' method='POST'>
<input type="hidden" name="pagina_viene" value="<?=$fecha_viene?>">
<input type="hidden" name="pagina_listado" value="<?=$pagina_listado?>">
<input type="hidden" name="id" value="<?=$id?>">


<?echo "<center><b><font size='+2' color='red'>$accion</font></b></center>";?>
<input type="hidden" value="<?=$usuario1?>" name="usuario1">
<input type="hidden" name="id" value="<?=$id?>">
<input type="hidden" name="entidad_alta" value="<?=$entidad_alta?>">
<table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
     <font size=+1><b>Beneficiario</b></font>    
    </td>
 </tr>
 <tr><td>
  <table width=70% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b> Descripci�n del Beneficiario</b>
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
              <input type='text' name='a' value='<?=$a;?>' size=60 align='right' readonly></b>
            </td>
         </tr>
         <tr>
            <td align="right">
         	  <b> Nombre:
         	</td>   
           <td  colspan="2">
             <input type='text' name='b' value='<?=$b;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
          <tr>
           <td align="right">
         	  <b> Documento:
         	</td> 
           <td colspan="2">
             <input type='text' name='c' value='<?=$c;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
          <tr>
           <td align="right">
         	  <b> Fecha de Nacimiento:
         	</td> 
           <td colspan="2">
             <input type='text' name='d' value='<?=Fecha($d);?>' size=60 align='right' readonly></b>
           </td>
          </tr>
          <tr>
           <td align="right">
         	  <b> Domicilio:
         	</td> 
           <td colspan="2">
             <input type='text' name='e' value='<?=$e;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
          
        </table>
      </td>      
     </tr>
   </table>     
	 <table class="bordes" align="center" width="70%">
		 <tr align="center" id="sub_tabla">
		 	<td colspan="2">	
		 		Nueva Prestaci�n No Empadronado		 		
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
				 			<select name=efector Style="width=450px" 
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer();"
				onchange="borrar_buffer();" >
				<option value=-1>Seleccione</option>
			 <?
			 $sql= "select * from nacer.efe_conv order by nombre";
			 /*$sql= " select nacer.efe_conv.nombre, nacer.efe_conv.cuie from nacer.efe_conv join sistema.usu_efec on (nacer.efe_conv.cuie = sistema.usu_efec.cuie) 
			        join sistema.usuarios on (sistema.usu_efec.id_usuario = sistema.usuarios.id_usuario) 
			        where sistema.usuarios.id_usuario = '$usuario1' order by nombre";*/
			 $res_efectores=sql($sql) or fin_pagina();
			 while (!$res_efectores->EOF){ 
			 	$com_gestion=$res_efectores->fields['com_gestion'];
			    $cuie=$res_efectores->fields['cuie'];
			    $nombre_efector=$res_efectores->fields['nombre'];
				($com_gestion=='FALSO')?$color_style='#F78181':$color_style='';
			    ?>
				<option value=<?=$cuie;?> Style="background-color: <?=$color_style?>;"><?=$cuie." - ".$nombre_efector?></option>
			    <?
			    $res_efectores->movenext();
			    }?>
			</select>
				 			
					    </td>
					    </td>
					 </tr>
					 <tr>
					    <td align="right">
					    	<b>Servicio:</b>
					    </td>
					    <td align="left">		          			
				 			<select name=servicio Style="width=450px"
				 			onKeypress="buscar_combo(this);"
				 			onblur="borrar_buffer();"
				 			onchange="borrar_buffer();"
           					>
			     			<option value=-1>Seleccione</option>
			                 <?
			                 $sql= "select * from facturacion.servicio order by descripcion";
			                 $res_efectores=sql($sql) or fin_pagina();
			                 while (!$res_efectores->EOF){ 
			                 	$cuie=$res_efectores->fields['id_servicio'];
			                 	$nombre_efector=$res_efectores->fields['descripcion'];
			                 ?>
			                   <option <?=($res_efectores->fields['descripcion']=="No Corresponde")?"selected":""?> value=<?=$cuie;?>><?=$nombre_efector?></option>
			                 <?
			                 $res_efectores->movenext();
			                 }
			                 ?>
			      			</select>
					    </td>
					 </tr>
					 <tr>
					 	<td align="right">
					    	<b>Nombre Medico:</b>
					    </td>
					    <td align="left">
					    	 <input type="text" value="" name="nom_medico" Style="width=450px">
					    </td>		    
					 </tr>	
					 <tr>
					 	<td align="right">
					    	<b>Fecha Prestaci�n:</b>
					    </td>
					    <td align="left">
					    						    	
					    	<?$fecha_comprobante=date("d/m/Y");?>
					    	 <input type=text id=fecha_comprobante name=fecha_comprobante value='<?=$fecha_comprobante;?>' size=15 readonly>
					    	 <?=link_calendario("fecha_comprobante");?>					    	 
					    </td>		    
					 </tr>
					 <tr>
         	<td align="right">
				<b>Periodo de Facturaci�n:</b>
			</td>
			<td align="left">		          			
			 <select name=periodo Style="width=450px" <? if ($id_factura) echo "disabled"?>>
			 <option value=-1>Seleccione</option>
			  <?
			  $sql = "select * from facturacion.periodo order by periodo";
			  $result=sql($sql,"No se puede traer el periodo");
			  while (!$result->EOF) {?>
			  			  
			  <option value=<?=$result->fields['periodo']?> <?if ($periodo==$result->fields['periodo']) echo "selected"?>><?=$result->fields['periodo']?></option>
			  <?
			  $result->movenext();
			  }
			  ?>			
			  </select>
			</td>
         </tr>
					 <tr>
         				<td align="right">
         	  				<b>Comentario:</b>
         				</td>         	
            			<td align='left'>
              				<textarea cols='70' rows='3' name='comentario' <? if ($id_planilla) echo "readonly"?>></textarea>
            			</td>
         			</tr>   					 
				  </td>
			 </tr>
		 </table></td></tr>	 
		 <tr>
		  	<td align="center" colspan="2" class="bordes">		      
		    	<input type="submit" name="guardar" value="Guardar Comprobante" title="Guardar Comprobante" Style="width=250px;height=30px" onclick="return control_nuevos()">
		   		&nbsp;&nbsp;&nbsp;
		    	<!--<input type="submit" name="guardar" value="Guardar Comprobante y Facturar" title="Guardar Comprobante y Facturar" Style="width=250px;height=30px" onclick="return control_nuevos()">-->
		    </td>
		 </tr> 
	 </table>	
 </td></tr>
 
<?//tabla de comprobantes
$query="SELECT 
  facturacion.comprobante.id_comprobante,
  nacer.efe_conv.nombre,
  facturacion.comprobante.nombre_medico,
  facturacion.comprobante.fecha_comprobante,
  facturacion.comprobante.id_factura,
  facturacion.comprobante.marca,
  facturacion.comprobante.periodo
FROM
  facturacion.comprobante
  INNER JOIN nacer.efe_conv ON (facturacion.comprobante.cuie = nacer.efe_conv.cuie)
  where id_beneficiarios=$id
  order by comprobante.id_comprobante DESC";
$res_comprobante=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();?>
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
	 	    <td width=1%>&nbsp;</td>
	 		<td width="10%">N&uacute;mero de Comprobante</td>
	 		<td width="40%">Efector</td>
	 		<td width="30%">Medico</td>
	 		<td width="9%">Fecha Prestaci�n</td>	 		
	 		<td width="10%">Cant Prestaciones</td>
	 		<td width="10%">Periodo</td>
	 		<td width="10%">Anular</td>
	 	</tr>
	 	<?
	 	$res_comprobante->movefirst();
	 	while (!$res_comprobante->EOF) {
	 		if ($res_comprobante->fields['id_factura']==""){
	 			$color_fondo="#FFFFCC"; 		
	 			$ref = encode_link("prestacion_admin.php",array("id"=>$id,"id_comprobante"=>$res_comprobante->fields['id_comprobante'],"pagina_viene"=>"comprobante_admin_total.php","pagina_listado"=>$pagina_listado,"entidad_alta"=>"nu"));
            	$onclick_elegir="location.href='$ref'";  
            	if ($res_comprobante->fields['marca']==0){
            		$ref1 = encode_link("comprobante_admin_total.php",array("id_comprobante"=>$res_comprobante->fields['id_comprobante'],"marcar"=>"True","id"=>$id,"entidad_alta"=>$entidad_alta));
            		$id_comprobante_aux=$res_comprobante->fields['id_comprobante'];
            		$onclick_marcar="if (confirm('Esta Seguro que Desea ANULAR Comprobante $id_comprobante_aux?')) location.href='$ref1'
            						else return false;	";
            	}   
            	else 
            	{
            		$onclick_marcar=""; 
            		$onclick_elegir="";
            	}
            	
	 		}
	 		else 
	 		{
	 			$color_fondo="FF9999"; 
	 			$onclick_elegir="";
	 			$onclick_marcar=""; 
	 		}
	 		
	 		if ($res_comprobante->fields['marca']==1){
	 			$color_fondo="AA888"; 
	 		}
	 		
	 		$id_tabla="tabla_".$res_comprobante->fields['id_comprobante'];	
	 		$onclick_check=" javascript:(this.checked)?Mostrar('$id_tabla'):Ocultar('$id_tabla')";
	 		
	 		//consulta para saber si tiene pretaciones el comprobante
	 		$sql=" select count(id_prestacion) as cant_prestaciones from facturacion.prestacion 								
					where id_comprobante=". $res_comprobante->fields['id_comprobante'];
	 		$cant_prestaciones=sql($sql,"no se puede traer la contidad de prestaciones") or die();
	 		$cant_prestaciones=$cant_prestaciones->fields['cant_prestaciones'];
	 		?>
	 		<tr <?=atrib_tr()?>>
	 			<td>
	              <input type=checkbox name=check_prestacion value="" onclick="<?=$onclick_check?>" class="estilos_check">
	            </td>	
		 		<td onclick="<?=$onclick_elegir?>" bgcolor='<?=$color_fondo?>'><font size="3" color="Red"><b><?=$res_comprobante->fields['id_comprobante']. "(".$res_comprobante->fields['id_factura'].")"?></b></font></td>
		 		<td onclick="<?=$onclick_elegir?>"><?=$res_comprobante->fields['nombre']?></td>
		 		<td onclick="<?=$onclick_elegir?>"><?if ($res_comprobante->fields['nombre_medico']!="") echo $res_comprobante->fields['nombre_medico']; else echo "&nbsp"?></td>
		 		<td onclick="<?=$onclick_elegir?>"><?=fecha($res_comprobante->fields['fecha_comprobante'])?> Edad: <?=$res_comprobante->fields['fecha_comprobante']-$afifechanac?></td>		 		
		 		<td onclick="<?=$onclick_elegir?>"><?="Total: ".$cant_prestaciones?></td>		 		
		 		<td onclick="<?=$onclick_elegir?>"><?=$res_comprobante->fields['periodo']?></td>		 		
		 		<td onclick="<?=$onclick_marcar?>" align="center"><?if ($res_comprobante->fields['marca']==1){echo "<img src='../../imagenes/salir.gif' style='cursor:pointer;'>";}
		 											else if ($res_comprobante->fields['id_factura']!="") {echo "Facturado";}
		 											else echo "<img src='../../imagenes/sin_desc.gif' style='cursor:pointer;'>"?></td>		 		
		 	</tr>	
		 	<tr>
	          <td colspan=6>
	
	                  <?
	                  $sql=" select *
								from facturacion.prestacion 
								left join facturacion.nomenclador using (id_nomenclador)							
								where id_comprobante=". $res_comprobante->fields['id_comprobante']." order by id_prestacion DESC";
	                  $result_items=sql($sql) or fin_pagina();
	                  ?>
	                  <div id=<?=$id_tabla?> style='display:none'>
	                  <table width=90% align=center class=bordes>
	                  			<?
	                  			$cantidad_items=$result_items->recordcount();
	                  			if ($cantidad_items==0){?>
		                            <tr>
		                            	<td colspan="10" align="center">
		                            		<b><font color="Red" size="+1">NO HAY PRESTACIONES PARA ESTE COMPROBANTE</font></b>
		                            	</td>	                                
			                        </tr>	                               
								<?}
								else{?>
		                           <tr id=ma>		                               
		                               <td>Cantidad</td>
		                               <td>Codigo</td>
		                               <td>Descripci�n</td>
		                               <td>Precio</td>
		                               <td>Total</td>	                               
		                            </tr>
		                            <?while (!$result_items->EOF){?>
			                            <tr>
			                            	 <td class="bordes"><?=$result_items->fields["cantidad"]?></td>			                                 
			                                 <td class="bordes"><?=$result_items->fields["codigo"]?></td>
			                                 <td class="bordes"><?=$result_items->fields["descripcion"]?></td>
			                                 <td class="bordes"><?=number_format($result_items->fields["precio_prestacion"],2,',','.')?></td>
			                                 <td class="bordes"><?=number_format($result_items->fields["cantidad"]*$result_items->fields["precio_prestacion"],2,',','.')?></td>
			                            </tr>
		                            	<?$result_items->movenext();
		                            }//del while
								}//del else?>
	                            	                            
	               </table>
	               </div>
	
	         </td>
	      </tr>  	
	 		<?$res_comprobante->movenext();
	 	}
	 }?>
</table></td></tr>
 
 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
   	 <?if ($pagina_listado=='listado_beneficiarios_hist.php'){?>
   	 	<input type=button name="volver" value="Volver" onclick="document.location='listado_beneficiarios_hist.php'"title="Volver al Listado" style="width=150px">     
   	 <?}
   	 else if ($pagina_listado=='listado_beneficiarios_leche.php'){?>
   	 	<input type=button name="volver" value="Volver" onclick="document.location='../entrega_leche/listado_beneficiarios_leche.php'"title="Volver al Listado" style="width=150px">     
   	 <?}
   	 else{?>
     	<input type=button name="volver" value="Volver" onclick="document.location='listado_beneficiarios_fact.php'"title="Volver al Listado" style="width=150px">     
   	 <?}?>
    </td>
  </tr>
 </table></td></tr>
 
 
</table>

    
</form>
<?=fin_pagina();// aca termino ?>
