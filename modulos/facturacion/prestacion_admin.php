<?

require_once ("../../config.php");
include_once("./funciones.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
$usuario1=$_ses_user['id'];

if ($_POST['nomenclador_detalle']){
	$query="update nacer.efe_conv
			set id_nomenclador_detalle='$nomenclador_detalle'
			where cuie='$cuie'";	
	sql($query, "Error al insertar la prestacion") or fin_pagina();
}

if ($_POST['guardar_obl']=="Guardar Datos Obligatorios"){
	$fecha_carga=date("Y-m-d H:i:s");
	$db->StartTrans();
	
	if ($peso<>"")$peso=$peso; else $peso='0';
	if ($tension_arterial<>"")$tension_arterial=$tension_arterial; else $tension_arterial='00/00';
	
	$query="update facturacion.prestacion 
			set 
			tension_arterial='$tension_arterial', peso='$peso'
			where id_prestacion=$id_prestacion_extra";	
	sql($query, "Error al insertar la prestacion") or fin_pagina();
	
	$accion="Se guardo DATO EXTRA en la Prestacion Numero: $id_prestacion_extra";
	
	/*cargo los log*/ 
	$usuario=$_ses_user['name'];
	$log="insert into facturacion.log_prestacion
		   (id_prestacion, fecha, tipo, descripcion, usuario) 
			values ($id_prestacion_extra, '$fecha_carga','DATOS OBLIGATORIOS','Nro. prestacion $id_prestacion_extra', '$usuario')";
	sql($log) or fin_pagina();
		
	$db->CompleteTrans(); 
	
}

if ($_POST['guardar']=="Guardar Prestacion"){
   $fecha_carga=date("Y-m-d H:i:s");
   if (($pagina_viene=='comprobante_admin_total.php')||((valida_prestacion($id_comprobante,$nomenclador))&&(valida_prestacion1($id_comprobante,$nomenclador)))){
	   $db->StartTrans();
	      
	    $q="select nextval('facturacion.prestacion_id_prestacion_seq') as id_prestacion";
	    $id_prestacion=sql($q) or fin_pagina();
	    $id_prestacion=$id_prestacion->fields['id_prestacion'];
	
	    //traigo el precio de la prestacion del nomencladorpara guardarla en la 
	    //tabla de prestacion por que si se cambia el precio en el nomenclador
	    //cambia el precio de todas las prestaciones y las facturas
	    $q="select precio from facturacion.nomenclador where id_nomenclador=$nomenclador";
	    $precio_prestacion=sql($q,"Error en traer el precio del nomenclador") or fin_pagina();
	    $precio_prestacion=$precio_prestacion->fields['precio'];
	    $precio_prestacion=$precio_prestacion;
	    
	    $query="insert into facturacion.prestacion
	             (id_prestacion,id_comprobante, id_nomenclador,cantidad,precio_prestacion,id_anexo,peso,tension_arterial,estado_envio)
	             values
	             ($id_prestacion,$id_comprobante,$nomenclador,$cantidad,$precio_prestacion,$anexo,'0','00/00','n')";
	
	    sql($query, "Error al insertar la prestacion") or fin_pagina();
	    
	    $query="select codigo
	    		from facturacion.nomenclador
	    		where id_nomenclador='$nomenclador'";
	
	    $codigo=sql($query, "Error al insertar la prestacion") or fin_pagina();
	    $codigo=$codigo->fields['codigo'];
	    
	    $accion="Se guardo la Prestacion con el Codigo: $codigo";
		
	    /*cargo los log*/ 
	    $usuario=$_ses_user['name'];
		$log="insert into facturacion.log_prestacion
			   (id_prestacion, fecha, tipo, descripcion, usuario) 
		values ($id_prestacion, '$fecha_carga','Nueva PRESTACION','Nro. prestacion $id_prestacion', '$usuario')";
		sql($log) or fin_pagina();

   		// guardo en caso de hipoacustia 
		
		if (($res_codigo=='NNE 114')or ($res_codigo=='NPE 117')or($res_codigo=='NPE 119')or($res_codigo=='NPE 118')){
		
		$q_reporte="select nextval('trazadoras.reporte_hipoacustia_id_reporte_hipoacustia_seq') as id_reporte_hipo";
	    $id_reporte_hipoacustia=sql($q_reporte) or fin_pagina();
	    $id_reporte_hipoacustia=$id_reporte_hipoacustia->fields['id_reporte_hipo'];
		
		 $query_hipo="insert into trazadoras.reporte_hipoacustia
	             (id_reporte_hipoacustia,id_prestacion, oido_d,oido_i,grado_hipo)
	             values
	             ('$id_reporte_hipoacustia','$id_prestacion','$oido_d','$oido_i','$grado_hipo')";
	
	    sql($query_hipo, "Error al insertar el reporte de Hipoacustia") or fin_pagina();
		}
		
	    $db->CompleteTrans();   
   }
   else $accion="NO SE guardo la Prestacion"; 
}//de if ($_POST['guardar']=="Guardar nuevo Muleto")


$query="SELECT 
  facturacion.comprobante.id_comprobante,
  nacer.efe_conv.nombre,
  nacer.efe_conv.cuie,
  facturacion.comprobante.nombre_medico,
  facturacion.comprobante.fecha_comprobante,
  comprobante.alta_comp
FROM
  facturacion.comprobante
  INNER JOIN nacer.efe_conv ON (facturacion.comprobante.cuie = nacer.efe_conv.cuie)
  where id_comprobante=$id_comprobante";
$res_comprobante=sql($query, "Error al traer el Comprobantes") or fin_pagina();

$alta_comp=$res_comprobante->fields['alta_comp'];
$nombre=$res_comprobante->fields['nombre'];
$cuie=$res_comprobante->fields['cuie'];
$nombre_medico=$res_comprobante->fields['nombre_medico'];
$fecha_comprobante=$res_comprobante->fields['fecha_comprobante'];

if ($alta_comp=='SI'){
	$ref = encode_link("prestacion_admin_comp.php",array("id_smiafiliados"=>$id_smiafiliados,"id_comprobante"=>$id_comprobante,"estado"=>"","pagina_listado"=>$pagina_listado,"pagina_viene"=>"comprobante_admin.php"));
	echo "<SCRIPT>window.location='$ref';</SCRIPT>"; 
	exit();
}

/*$sql="SELECT 
  *
FROM
  nacer.efe_conv
  left join facturacion.nomenclador_detalle using (id_nomenclador_detalle)
  where cuie='$cuie'";*/

$sql="SELECT * FROM facturacion.nomenclador_detalle where '$fecha_comprobante' between fecha_desde and fecha_hasta;";

$res_nom=sql($sql, "Error al traer el nomenclador detalle") or fin_pagina();
$descripcion=$res_nom->fields['descripcion'];
$id_nomenclador_detalle=$res_nom->fields['id_nomenclador_detalle'];

$modo_facturacion=$res_nom->fields['modo_facturacion'];
if (($modo_facturacion==2)&&($pagina_viene=='comprobante_admin_total.php')) {
	$ref = encode_link("prestacion_admin_2011.php",array("id"=>$id,"id_smiafiliados"=>$id_smiafiliados,"id_comprobante"=>$id_comprobante,"estado"=>"","pagina_listado"=>$pagina_listado,"pagina_viene"=>"comprobante_admin_total.php"));
	echo "<SCRIPT>window.location='$ref';</SCRIPT>"; 
	exit();
}
if (($modo_facturacion==2)&&($pagina_viene=='comprobante_admin.php')) {
	$ref = encode_link("prestacion_admin_2011.php",array("id_smiafiliados"=>$id_smiafiliados,"id_comprobante"=>$id_comprobante,"estado"=>"","pagina_listado"=>$pagina_listado,"pagina_viene"=>"comprobante_admin.php"));
	echo "<SCRIPT>window.location='$ref';</SCRIPT>"; 
	exit();
}

if (($modo_facturacion==4)&&($pagina_viene=='comprobante_admin_total.php')) {
	$ref = encode_link("prestacion_admin_nop.php",array("id"=>$id,"id_smiafiliados"=>$id_smiafiliados,"id_comprobante"=>$id_comprobante,"estado"=>"","pagina_listado"=>$pagina_listado,"pagina_viene"=>"comprobante_admin_total.php"));
	echo "<SCRIPT>window.location='$ref';</SCRIPT>"; 
	exit();
}
if (($modo_facturacion==4)&&($pagina_viene=='comprobante_admin.php')) {
	$ref = encode_link("prestacion_admin_nop.php",array("id_smiafiliados"=>$id_smiafiliados,"id_comprobante"=>$id_comprobante,"estado"=>"","pagina_listado"=>$pagina_listado,"pagina_viene"=>"comprobante_admin.php"));
	echo "<SCRIPT>window.location='$ref';</SCRIPT>"; 
	exit();
}

if ($nomenclador<>"") {
  		$sql= "select * from facturacion.nomenclador 
				where id_nomenclador='$nomenclador'";
     	$res_codigo=sql($sql) or fin_pagina();
     	$res_codigo=$res_codigo->fields['codigo'];     
}

$query_b="SELECT nacer.smiafiliados.*,smitiposcategorias.*
	   FROM nacer.smiafiliados
 	   left join nacer.smitiposcategorias on (afitipocategoria=codcategoria)
	   left join facturacion.comprobante using (id_smiafiliados)
  	   where comprobante.id_comprobante=$id_comprobante";
$res_comprobante_b=sql($query_b, "Error al traer el Comprobantes") or fin_pagina();

$afiapellido=$res_comprobante_b->fields['afiapellido'];
$afinombre=$res_comprobante_b->fields['afinombre'];
$afidni=$res_comprobante_b->fields['afidni'];
$descripcion_b=$res_comprobante_b->fields['descripcion'];
$codcategoria=$res_comprobante_b->fields['codcategoria'];

echo $html_header;
?>
<script>
//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos()
{
 if(document.all.nomenclador.value=="-1"){
  alert('Debe Seleccionar un codigo del Nomenclador');
  return false;
 }
 
 if(document.all.cantidad.value==""){
  alert('Debe ingresar una Cantidad');
  return false;
 }
 
 if((document.all.nomenclador.value=="51")&&(document.all.anexo.value=="1")){
  alert('Debe Ingresar un Anexo Categoria 1');
  return false;
 }
 
 if((document.all.nomenclador.value=="52")&&(document.all.anexo.value=="1")){
  alert('Debe Ingresar un Anexo Categoria 2');
  return false;
 }
 
 if((document.all.nomenclador.value=="53")&&(document.all.anexo.value=="1")){
  alert('Debe Ingresar un Anexo Categoria 3');
  return false;
 }
 
 if((document.all.nomenclador.value=="54")&&(document.all.anexo.value=="1")){
  alert('Debe Ingresar un Anexo Categoria 4');
  return false;
 }
 if ((document.all.res_codigo.value=="NNE 114")|(document.all.res_codigo.value=="NPE 117")|(document.all.res_codigo.value=="NPE 119")){
		if(document.all.oido_d.value==-1){
			alert('Debe Ingresar el resultado del Oido Derecho');
			return false;
		}
		if(document.all.oido_i.value==-1){
			alert('Debe Ingresar el resultado del Oido Izquierdo');
			return false;
		}
 }
 if (document.all.res_codigo.value=="NPE 118"){
	if(document.all.grado_hipo.value==-1){
		alert('Debe Ingresar Grado de Hipoacustia que corresponde');
		return false;
	}
 }	 
 
}//de function control_nuevos()

function cambiar_nomenclador(){
	borrar_buffer(); 
	if (confirm ('Esta Accion Cambiara el Nomenclador Asociado al Efector: <?=$nombre;?>. ¿Esta Seguro?')){
		document.forms[0].submit()
	}
	else return false
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

<form name='form1' action='prestacion_admin.php' method='POST'>

<input type="hidden" name="id" value="<?=$id?>">
<input type="hidden" name="id_comprobante" value="<?=$id_comprobante?>">
<input type="hidden" name="id_prestacion_extra" value="<?=$id_prestacion?>">
<input type="hidden" name="id_smiafiliados" value="<?=$id_smiafiliados?>">
<input type="hidden" name="pagina_viene" value="<?=$pagina_viene?>">
<input type="hidden" name="pagina_listado" value="<?=$pagina_listado?>">
<input type="hidden" name="cuie" value="<?=$cuie?>">
<input type="hidden" name="res_codigo" value="<?=$res_codigo?>">
<table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
     <?php
	     if ($accion=='')echo "<font size=+1><b>PRESTACIONES</b></font>";
	     else echo "<font size=+1 color=white><b>$accion</b></font>";
     ?>   
    </td>
 </tr>
 <tr><td>
 <?if ($pagina_viene!='comprobante_admin_total.php'){?>
  <table width=70% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b> Descripción del COMPROBANTE</b>
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
         	  <b> Tipo de Afiliado: 
         	</td> 
           <td colspan="2">
            <input type='text' name='descripcion' value='<?echo $codcategoria . "-" .$descripcion_b;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
         
          <?//if (!permisos_check('inicio','cambia_nomenclador_prestaciones')) 
          			$cambia_nomenclador_prestaciones="disabled";?>
          <tr>			
			<td align="right" id=mo>
				<b>Nomenclador en Uso:</b>
			</td>
			<td align="left" id=mo>		          			
				<select name=nomenclador_detalle Style="width=378px"
				onKeypress="buscar_combo(this);"
				onblur="borrar_buffer();"
				onchange="cambiar_nomenclador()" <?echo $cambia_nomenclador_prestaciones?>>
			    <?$sql="select * from facturacion.nomenclador_detalle";
			    $res=sql($sql) or fin_pagina();
			    while (!$res->EOF){ 
			    	$id_nomenclador_detalle_1=$res->fields['id_nomenclador_detalle'];
			        $descripcion=$res->fields['descripcion'];?>
			        <option value=<?=$id_nomenclador_detalle_1?> <?if ($id_nomenclador_detalle==$id_nomenclador_detalle_1) echo " selected"?> >
			        	<?=$descripcion?>
			        </option>
			        <?$res->movenext();
			        }?>
			    </select>
			</td>
		</tr>
        
         <tr>
         	<td align="right">
         	  <b>Nombre del Efector:</b>
         	</td>         	
            <td align='left'>
              <input type='text' name='afiapellido' value='<?=$nombre;?>' size=60 align='right' readonly></b>
            </td>
         </tr>
         
          <tr>
           <td align="right">
         	  <b> Fecha del Comprobante:
         	</td> 
           <td colspan="2">
             <input type='text' name='afidni' value='<?=fecha($fecha_comprobante);?>' size=60 align='right' readonly></b>
           </td>
          </tr>          
        </table>
      </td>      
     </tr>
   </table>  
   <?}?>   
	 <table class="bordes" align="center" width="70%">
		 <tr align="center" id="sub_tabla">
		 	<td colspan="2">	
		 		Nueva PRESTACION
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
					    	<? if (($codcategoria==3)or($codcategoria==4))
					    		$grupo_nomenclador='MUJER';
					    	   if (($codcategoria==1)or($codcategoria==2)) 
					    		$grupo_nomenclador='NIÑO';
							   if (($codcategoria==5)or($codcategoria==6)) 
					    		$grupo_nomenclador='';
					    	?>		          			
				 			<select name=nomenclador Style="width=700px"
				 			onKeypress="buscar_combo(this);"
				 			onblur="borrar_buffer();"
				 			onchange="borrar_buffer(); document.forms[0].submit();">
			     			<option value=-1>Seleccione</option>
			                 <?
			                 $sql1="select * from facturacion.efec_nom 
			                 		where cuie='$cuie'";
			                 $res_flag=sql($sql1) or fin_pagina();
			                 if ($res_flag->recordCount()>0){			                 
			                 $sql= "select * 
			                 		from facturacion.nomenclador 
			                 		inner join facturacion.efec_nom using (codigo)
									where cuie='$cuie' and 
										  id_nomenclador_detalle='$id_nomenclador_detalle' and 
										  nomenclador.codigo<>'DIFERENCIA DE NOMENCLADOR' and
										  nomenclador.codigo<>'DEB-CRED' and 
										  nomenclador.grupo<>'$grupo_nomenclador'
			                 		ORDER BY codigo";
			                 }
			                 else {
			                 	$sql= "select * 
			                 		   from facturacion.nomenclador 
			                 		   where id_nomenclador_detalle='$id_nomenclador_detalle' and
			                 		   		nomenclador.codigo<>'DIFERENCIA DE NOMENCLADOR' and
			                 		   		nomenclador.codigo<>'DEB-CRED' and 
			                 		   		nomenclador.grupo<>'$grupo_nomenclador' 
			                 	ORDER BY codigo";	
			                 }
							 
							 
			                 
			                 if ($pagina_viene=='comprobante_admin_total.php'){
				                 $sql= "select * from facturacion.nomenclador 
				                 			where id_nomenclador_detalle='$id_nomenclador_detalle' and
				                 		   		nomenclador.codigo<>'DIFERENCIA DE NOMENCLADOR' and
				                 		   		nomenclador.codigo<>'DEB-CRED' ORDER BY codigo";
			                 }
			                 
			                 $res_efectores=sql($sql) or fin_pagina();
			                 while (!$res_efectores->EOF){ 
			                 	$id_nomenclador=$res_efectores->fields['id_nomenclador'];
			                 	$codigo=$res_efectores->fields['codigo'];
			                 	$subgrupo=$res_efectores->fields['subgrupo'];
			                 	$descripcion=$res_efectores->fields['descripcion'];
			                 	$tipo_nomenclador=$res_efectores->fields['tipo_nomenclador'];
			                 ?>
			                   <option value=<?=$id_nomenclador;?> <?if ($codigo==$res_codigo) echo "selected"?> >
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
					    	<b>Anexo:</b>
					    </td>
					    <td align="left">		          			
				 			<select name=anexo Style="width=700px"
				 			onKeypress="buscar_combo(this);"
				 			onblur="borrar_buffer();"
				 			onchange="borrar_buffer();">			     			
			                 <?
			                 if ($nomenclador<>'')
			                 	$sql= "select * from facturacion.anexo 
				                 		where id_nomenclador_detalle='$id_nomenclador_detalle' and id_nomenclador='$nomenclador'
				                 		ORDER BY categoria, numero";
			                 else 
			                 	$sql= "select * from facturacion.anexo 
			                 		   where id_nomenclador_detalle='$id_nomenclador_detalle'
			                 		   ORDER BY categoria, numero";
			                 	
			                 $res_efectores=sql($sql) or fin_pagina();
			                 
			                 if ($res_efectores->recordcount()==0){
			                 	$sql= "select * from facturacion.anexo 
			                 		   where id_nomenclador_detalle='$id_nomenclador_detalle' and id_nomenclador is NULL
			                 		   ORDER BY categoria, numero";
			                 	
			                 $res_efectores=sql($sql) or fin_pagina();
			                 }
			                 
			                 while (!$res_efectores->EOF){ 
			                 	$id_anexo=$res_efectores->fields['id_anexo'];
			                 	$numero=$res_efectores->fields['numero'];
			                 	$prueba=$res_efectores->fields['prueba'];
			                 	$categoria=$res_efectores->fields['categoria'];			                 	
			                 ?>
			                   <option <?=($res_efectores->fields['prueba']=="No Corresponde")?"selected":""?> value=<?=$id_anexo;?> >
			                   	<?=$categoria."-".$numero." ".$prueba?>
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
					    	 <input type="text" value="1" name="cantidad" Style="width=50px">
					    </td>		    
					 </tr>						 			 
				  </td>
			 </tr>
			 
			 <? if (($res_codigo=='NNE 114')||($res_codigo=='NPE 117')||($res_codigo=='NPE 119')||($res_codigo=='NPE 118')){ 

			  if($res_codigo=='NPE 118'){?>
			  	<tr>
			   <td>
			   <b>Grados de Perdida Auditiva:</b>
			   </td>
			  <td>
			 	<select name=grado_hipo Style="width=257px" >
										  <option value=-1>Seleccione</option>
										  <option value="L">Leve</option>
										  <option value="M">Moderada</option>
										  <option value="MS">Moderada a Severa</option>
										  <option value="S">Severa</option>
										  <option value="P">Profunda o Sordera</option>
						</select>
			   </td>
			   </tr>
			  <? }
			  else{
			 ?>
			 <tr>
			   <td>
			   <b>Oido Derecho:</b>
			   </td>
			  <td>
			 	<select name=oido_d Style="width=257px" >
										  <option value=-1>Seleccione</option>
										  <option value="Pasa"<?if(oido_d=="Pasa")echo "selected"?>>Pasa</option>
										  <option value="No Pasa"<?if(oido_d=="Pasa")echo "selected"?>>No Pasa</option>
						</select>
			   </td>
			   </tr>
			   <tr>
			   <td>
			   <b>Oido Izquierdo:</b>
			   </td>
			  <td>
			 	<select name=oido_i Style="width=257px" >
										  <option value=-1>Seleccione</option>
										  <option value="Pasa"<?if(oido_i=="Pasa")echo "selected"?>>Pasa</option>
										  <option value="No Pasa"<?if(oido_i=="No Pasa")echo "selected"?>>No Pasa</option>
						</select>
			   </td>
			   </tr>
			   
			  <? } 
			    } ?>
			 
		 </table></td></tr>	
		 <tr>
		  	<td align="center" colspan="2" class="bordes">
		  		<?		  		
		  		if (($usuario1)&&(($res_codigo=='MEM 01')||($res_codigo=='MER 15')||($res_codigo=='MER 16')||($res_codigo=='NPE 32')||($res_codigo=='NPE 33'))) 
		  			$hab_on_line='disabled';
		  		else 
		  			$hab_on_line='';
		  		?>
		  		<input type="submit" name="guardar" value="Guardar Prestacion" title="Guardar Prestacion" Style="width=300px;height=40px" onclick="return control_nuevos()" <?=$hab_on_line?>>
		    </td>
		 </tr> 
	 </table>	
 </td></tr>
 
 <?if ((($res_codigo=='MPA 17')||($res_codigo=='MPA 18')||($res_codigo=='MEM 02'))&&($id_prestacion!='')){?>
 <tr><td bgcolor="#d3d3cd"><table width=100% align="center" class="bordes">
  <tr align="center"><td>
  
   <table width=70% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b>Datos Extras</b>
      </td>
     </tr>
     <tr>
       <td>  
               
    <table>         
    	
    <?
    if (($res_codigo=='MPA 17')||($res_codigo=='MPA 18')||($res_codigo=='MEM 02')){?>
    	<tr>
         	<td align="right">
         	  <b>Peso:
         	</td>         	
            <td align='left'>
              <input type='text' name='peso' value='<?=($peso<>"")?$peso:"0";?>' size=20 align='right'><font color="Red">*En Gramos. Separador de Decimales "."</font></b>
            </td>
         </tr>
    <?}
    if ($res_codigo=='MEM 02'){?>        
         <tr>
            <td align="right">
         	  <b>Tensión Arterial:
         	</td>   
           <td  colspan="2">
             <input type='text' name='tension_arterial' value='<?=($tension_arterial<>"")?$tension_arterial:"00/00";?>' size=20 align='right'><font color="Red">*Ejemplos: 12/08 16/10</font></b>
           </td>
          </tr>        
    <?}?> 
		                  
    <tr>
		<td align="center" colspan="3" >		      
			<b><font color="red">DEBE VENIR EN EL COMPROBANTE EL DATO. Para los MPA 17, MPA 18 y MEM 02.</font></b>
		</td>
   </tr> 
   
    </table>
       
    </td>      
     </tr>
   </table> 
   
   <tr>
		  	<td align="center" colspan="2" class="bordes">		      
		    	<input type="submit" name="guardar_obl" value="Guardar Datos Obligatorios" title="Guardar Datos Obligatorios" Style="width=300px" >
		    </td>
   </tr> 
   
  </td></tr>
 </table></td></tr>
 <?}?>
 
   
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
   	 <?}
   	 
     if ($pagina_viene=='comprobante_admin_total.php'){
	 	 $ref = encode_link("comprobante_admin_total.php",array("id"=>$id,"pagina_listado"=>$pagina_listado,"pagina_viene"=>"prestacion_admin.php","estado"=>$estado));?>
	     <input type=button name="volver" value="Volver al Beneficiario" onclick="document.location='<?=$ref?>'"title="Volver a los comprobantes" style="width=150px">  
	 <?}
	 else{
	 	 $ref = encode_link("comprobante_admin.php",array("id_smiafiliados"=>$id_smiafiliados,"clavebeneficiario"=>$clave_beneficiario,"pagina_listado"=>$pagina_listado,"pagina_viene"=>"prestacion_admin.php","estado"=>$estado));?>
	     <input type=button name="volver" value="Volver al Beneficiario" onclick="document.location='<?=$ref?>'"title="Volver a los comprobantes" style="width=150px"> 
	 <?}
     switch ($res_codigo){
     	case 'MEM 01' : $ref = encode_link("../trazadoras/emb_admin.php",array("fecha_comprobante"=>$fecha_comprobante,"id_smiafiliados"=>$id_smiafiliados,"cuie"=>$cuie,"pagina"=>"prestacion_admin.php","id_comprobante"=>$id_comprobante,"pagina_viene"=>$pagina_viene));
     		break;
			
		case 'REM 76' : $ref = encode_link("../trazadoras/emb_admin.php",array("fecha_comprobante"=>$fecha_comprobante,"id_smiafiliados"=>$id_smiafiliados,"cuie"=>$cuie,"pagina"=>"prestacion_admin.php","id_comprobante"=>$id_comprobante,"pagina_viene"=>$pagina_viene));
     		break;
     	    	
     	case 'MPA 17': $ref = encode_link("../trazadoras/par_admin.php",array("fecha_comprobante"=>$fecha_comprobante,"id_smiafiliados"=>$id_smiafiliados,"cuie"=>$cuie,"pagina"=>"prestacion_admin.php","id_comprobante"=>$id_comprobante,"pagina_viene"=>$pagina_viene));
     		break;
			
     	case 'MPA 18': $ref = encode_link("../trazadoras/par_admin.php",array("fecha_comprobante"=>$fecha_comprobante,"id_smiafiliados"=>$id_smiafiliados,"cuie"=>$cuie,"pagina"=>"prestacion_admin.php","id_comprobante"=>$id_comprobante,"pagina_viene"=>$pagina_viene));
     		break;
			
     	case 'NPE 32' :  if (date("Y-m-d")>='2011-01-01') $link_trz_nino='nino_admin_new.php';
     					else $link_trz_nino='nino_admin.php';
     					$ref = encode_link("../trazadoras/$link_trz_nino",array("fecha_comprobante"=>$fecha_comprobante,"id_smiafiliados"=>$id_smiafiliados,"cuie"=>$cuie,"pagina"=>"prestacion_admin.php","id_comprobante"=>$id_comprobante,"pagina_viene"=>$pagina_viene));
     		break;
			
		case 'RPE 86' :  if (date("Y-m-d")>='2011-01-01') $link_trz_nino='nino_admin_new.php';
     					else $link_trz_nino='nino_admin.php';
     					$ref = encode_link("../trazadoras/$link_trz_nino",array("fecha_comprobante"=>$fecha_comprobante,"id_smiafiliados"=>$id_smiafiliados,"cuie"=>$cuie,"pagina"=>"prestacion_admin.php","id_comprobante"=>$id_comprobante,"pagina_viene"=>$pagina_viene));
     		break;
			
     	case 'NPE 33':  if (date("Y-m-d")>='2011-01-01') $link_trz_nino='nino_admin_new.php';
     					else $link_trz_nino='nino_admin.php';
     	                $ref = encode_link("../trazadoras/$link_trz_nino",array("fecha_comprobante"=>$fecha_comprobante,"id_smiafiliados"=>$id_smiafiliados,"cuie"=>$cuie,"pagina"=>"prestacion_admin.php","id_comprobante"=>$id_comprobante,"pagina_viene"=>$pagina_viene));
     		break;   

		case 'RPE 87' :  if (date("Y-m-d")>='2011-01-01') $link_trz_nino='nino_admin_new.php';
						else $link_trz_nino='nino_admin.php';
     					$ref = encode_link("../trazadoras/$link_trz_nino",array("fecha_comprobante"=>$fecha_comprobante,"id_smiafiliados"=>$id_smiafiliados,"cuie"=>$cuie,"pagina"=>"prestacion_admin.php","id_comprobante"=>$id_comprobante,"pagina_viene"=>$pagina_viene));
     		break;			
     }     
         
     switch ($res_codigo){
     	case 'MEM 01' : ?>&nbsp;&nbsp;&nbsp;<input type=button name="carga_trazadora" value="Trazadora Embarazada" onclick="document.all.guardar.disabled=false; window.open('<?=$ref?>','Trazadoras','dependent:yes,width=900,height=700,top=1,left=60,scrollbars=yes');" title="Carga Trazadora Embarazada" style="widthç:150px;background-color:#F781F3;"> <?
     		break;
		case 'REM 76' : ?>&nbsp;&nbsp;&nbsp;<input type=button name="carga_trazadora" value="Trazadora Embarazada" onclick="document.all.guardar.disabled=false; window.open('<?=$ref?>','Trazadoras','dependent:yes,width=900,height=700,top=1,left=60,scrollbars=yes');" title="Carga Trazadora Embarazada" style="widthç:150px;background-color:#F781F3;"> <?
     		break;			
     	case 'MPA 17': ?>&nbsp;&nbsp;&nbsp;<input type=button name="carga_trazadora" value="Trazadora Partos" onclick="document.all.guardar.disabled=false; window.open('<?=$ref?>','Trazadoras','dependent:yes,width=900,height=700,top=1,left=60,scrollbars=yes');" title="Carga Trazadora Partos" style="widthç:150px;background-color:#F781F3;"> <?
     		break;
     	case 'MPA 18': ?>&nbsp;&nbsp;&nbsp;<input type=button name="carga_trazadora" value="Trazadora Partos" onclick="document.all.guardar.disabled=false; window.open('<?=$ref?>','Trazadoras','dependent:yes,width=900,height=700,top=1,left=60,scrollbars=yes');" title="Carga Trazadora Partos" style="widthç:150px;background-color:#F781F3;"> <?
     		break;
     	case 'NPE 32': ?>&nbsp;&nbsp;&nbsp;<input type=button name="carga_trazadora" value="Trazadora Niño" onclick="document.all.guardar.disabled=false; window.open('<?=$ref?>','Trazadoras','dependent:yes,width=900,height=700,top=1,left=60,scrollbars=yes');" title="Carga Trazadora Niño" style="widthç:150px;background-color:#F781F3;"> <?
     		break;
		case 'RPE 86': ?>&nbsp;&nbsp;&nbsp;<input type=button name="carga_trazadora" value="Trazadora Niño" onclick="document.all.guardar.disabled=false; window.open('<?=$ref?>','Trazadoras','dependent:yes,width=900,height=700,top=1,left=60,scrollbars=yes');" title="Carga Trazadora Niño" style="widthç:150px;background-color:#F781F3;"> <?
     		break;
     	case 'NPE 33': ?>&nbsp;&nbsp;&nbsp;<input type=button name="carga_trazadora" value="Trazadora Niño" onclick="document.all.guardar.disabled=false; window.open('<?=$ref?>','Trazadoras','dependent:yes,width=900,height=700,top=1,left=60,scrollbars=yes');" title="Carga Trazadora Niño" style="widthç:150px;background-color:#F781F3;"> <?
     		break; 
		case 'RPE 87': ?>&nbsp;&nbsp;&nbsp;<input type=button name="carga_trazadora" value="Trazadora Niño" onclick="document.all.guardar.disabled=false; window.open('<?=$ref?>','Trazadoras','dependent:yes,width=900,height=700,top=1,left=60,scrollbars=yes');" title="Carga Trazadora Niño" style="widthç:150px;background-color:#F781F3;"> <?
     		break;			
     }?>
     
   </td>   
  </tr>
 </table></td></tr>
 
 
 </table>
 </form>
 
 <?=fin_pagina();// aca termino ?>
