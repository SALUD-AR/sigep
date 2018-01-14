<?
require_once ("../../config.php");
extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
$usuario1=$_ses_user['id'];
if ($entidad_alta=='nu'){//carga de prestacion a paciente NO PLAN NACER
	$link=encode_link("comprobante_admin_total.php", array("id"=>$id_smiafiliados,"pagina_viene"=>"comprobante_admin.php","pagina_listado"=>$pagina_listado,"entidad_alta"=>$entidad_alta));?>
	<script>location.href='<?=$link?>' </script>
<?exit();
}


$sql_parametro="select valor from nacer.parametros where parametro='control_historicos'";
$res_parametro=sql($sql_parametro, "Error") or fin_pagina();
$res_parametro=$res_parametro->fields['valor'];

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
	if ($_POST[alta_comp] == "alta_comp"){
		$alta_comp='SI';
	}
	else{
		$alta_comp='';
	}
	if ($pagina_listado=='listado_beneficiarios_hist.php') {
		$fecha_comprobante_hist=substr(eregi_replace('-','',Fecha_db($_POST['fecha_comprobante'])),0,6).'01';
		$periodo_comprobante_hist=eregi_replace('/','',$_POST['periodo']).'01';
		if ($res_parametro=='por_periodo')$sql_hist="select count(id_smiafiliados) as cant from nacer.historicotemp
											where clavebeneficiario = '$clavebeneficiario' and periodo = '$periodo_comprobante_hist' and activo='S'";
		if ($res_parametro=='por_fecha')$sql_hist="select count(id_smiafiliados) as cant from nacer.historicotemp
											where clavebeneficiario = '$clavebeneficiario' and periodo = '$fecha_comprobante_hist' and activo='S'";
		$result_hist=sql($sql_hist,'No puedo ejecutar la funcion');
		$result_hist=$result_hist->fields['cant'];
		if ($result_hist>'0') $comienza_validacion='S';
		else{
			($res_parametro=='por_periodo')?$accion="NO esta ACTIVO en el PERIODO de la Prestacion.":$accion="NO esta ACTIVO en la FECHA de la Prestacion.";
		}
	}
	else $comienza_validacion='S';
	
	if ($comienza_validacion=='S'){
		$fecha_carga=date("Y-m-d H:i:s");
		$cuie=$_POST['efector'];
		$nom_medico=$_POST['nom_medico'];
		$fecha_comprobante=$_POST['fecha_comprobante'];
		$comentario=$_POST['comentario'];
		$fecha_comprobante=Fecha_db($fecha_comprobante);
		
		$query="select * from facturacion.comprobante
				where id_smiafiliados=$id_smiafiliados and fecha_comprobante='$fecha_comprobante'";
	    $val=sql($query, "Error en consulta de validacion") or fin_pagina();
	    $val_id_comp=$val->fields['id_comprobante'];
	    if ($val->RecordCount()==0)$accion1="";
	    else $accion1="Ya se genero el comprobante N�mero $val_id_comp, para esta persona el mismo dia.";  
	
	    $query="select fechainscripcion from nacer.smiafiliados
				where id_smiafiliados=$id_smiafiliados";
	    $val=sql($query, "Error en consulta de validacion fecha Inscripcion") or fin_pagina();
	    $fecha_inscripcion=$val->fields['fechainscripcion'];    
	        
	    $fecha_inscripcion_comp=Fecha_db(Fecha($fecha_inscripcion));
	    $fecha_comprobante_comp=$fecha_comprobante; 
	    
	    $query="select nomenclador_detalle.* from facturacion.nomenclador_detalle
	    		left join nacer.efe_conv using (id_nomenclador_detalle)
	    		where efe_conv.cuie='$cuie'";
	    $nomenclador_query=sql($query, "Error en consulta de trer nomenclador") or fin_pagina();
	    $fecha_desde_nom=$nomenclador_query->fields['fecha_desde'];
	    $fecha_hasta_nom=$nomenclador_query->fields['fecha_hasta'];
	            
	    //validacion de fecha de inscripcion y rango de nomenclador
	    /*if (($fecha_comprobante_comp<$fecha_inscripcion_comp)||($fecha_comprobante < $fecha_desde_nom||$fecha_comprobante>$fecha_hasta_nom)){
	    	if ($fecha_comprobante_comp<$fecha_inscripcion_comp) $accion1="ERROR: la Fecha de la Prestacion es MENOR a la fecha de Inscripcion del Beneficiario.";
	    	if ($fecha_comprobante < $fecha_desde_nom||$fecha_comprobante>$fecha_hasta_nom) $accion1="ERROR: La Fecha de la Prestacion NO ESTA en el Rango del Nomenclador Utilizado.";
	    }*/
	    	    
		if ($fecha_comprobante_comp<$fecha_inscripcion_comp) $accion1="ERROR: la Fecha de la Prestacion es MENOR a la fecha de Inscripcion del Beneficiario.";	    	
	    else 
	    {
	       
	      $db->StartTrans();
			$q="select nextval('comprobante_id_comprobante_seq') as id_comprobante";
		    $id_comprobante=sql($q) or fin_pagina();
		    $id_comprobante=$id_comprobante->fields['id_comprobante'];	
		    
		    if ($flag_inactivo=="S")$activo='S';
		    
		    $periodo= str_replace("-","/",substr($fecha_comprobante,0,7));
		    		    
		    $query="insert into facturacion.comprobante
		             (id_comprobante, cuie, nombre_medico, fecha_comprobante, clavebeneficiario, id_smiafiliados, fecha_carga,periodo,comentario,id_servicio,activo,alta_comp)
		             values
		             ($id_comprobante,'$cuie','$nom_medico','$fecha_comprobante','$clavebeneficiario', $id_smiafiliados,'$fecha_carga','$periodo','$comentario','$servicio','$activo','$alta_comp')";	
		    sql($query, "Error al insertar el comprobante") or fin_pagina();	    
		    $accion="Se guardo el Comprobante Numero: $id_comprobante.";	    /*cargo los log*/ 
		    $usuario=$_ses_user['name'];
			$log="insert into facturacion.log_comprobante 
				   (id_comprobante, fecha, tipo, descripcion, usuario) 
			values ($id_comprobante, '$fecha_carga','Nuevo Comprobante','Nro. Comprobante $id_comprobante', '$usuario')";
			sql($log) or fin_pagina();		 
		    $db->CompleteTrans(); 
		    if ($_POST['guardar']=="Guardar Comprobante y Facturar"){
		    	$ref = encode_link("prestacion_admin.php",array("id_smiafiliados"=>$id_smiafiliados,"id_comprobante"=>$id_comprobante,"estado"=>"","pagina_listado"=>$pagina_listado,"pagina_viene"=>"comprobante_admin.php","entidad_alta"=>$entidad_alta));
		    	echo "<SCRIPT>window.location='$ref';</SCRIPT>"; 
		    	exit();
		    }
	    }  
    }       
}//de if ($_POST['guardar']=="Guardar nuevo Muleto")

$sql="select * from nacer.smiafiliados
	 left join nacer.smitiposcategorias on (afitipocategoria=codcategoria)
	 left join nacer.efe_conv on (cuieefectorasignado=cuie)
	 where id_smiafiliados=$id_smiafiliados --and clavebeneficiario=$clavebeneficiario";
$res_comprobante=sql($sql, "Error al traer los Comprobantes") or fin_pagina();

$afiapellido=$res_comprobante->fields['afiapellido'];
$afinombre=$res_comprobante->fields['afinombre'];
$afidni=$res_comprobante->fields['afidni'];
$descripcion=$res_comprobante->fields['descripcion'];
$nombre=$res_comprobante->fields['nombre'];
$afifechanac=$res_comprobante->fields['afifechanac'];
$activo=$res_comprobante->fields['activo'];
$afisexo=$res_comprobante->fields['afisexo'];

echo $html_header;
?>
<script>
function mayor_fecha(fecha, fecha2){
var xMes=fecha.substring(3, 5);
var xDia=fecha.substring(0, 2);
var xAnio=fecha.substring(6,10);
var yMes=fecha2.substring(3, 5);
var yDia=fecha2.substring(0, 2);
var yAnio=fecha2.substring(6,10);
if (xAnio > yAnio){
return(true);
}else{
if (xAnio == yAnio){
if (xMes > yMes){
return(true);
}
if (xMes == yMes){
if (xDia > yDia){
return(true);
}else{
return(false);
}
}else{
return(false);
}
}else{
return(false);
}
}
}
//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos()
{
 if (mayor_fecha(document.all.fecha_comprobante.value, document.all.fecha_ahora_js.value)){
 alert('Debe Seleccionar un FECHA MENOR o IGUAL a la FECHA de HOY');
 return false;
 } 
 
 if(document.all.efector.value=="-1"){
  alert('Debe Seleccionar un EFECTOR');
  return false;
 }
 <?if ($pagina_listado=='listado_beneficiarios_hist.php'){?>
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

<form name='form1' action='comprobante_admin.php' method='POST'>
<input type="hidden" value="<?=$usuario1?>" name="usuario1">
<?echo "<center><b><font size='+2' color='red'>$accion</font></b></center>";
echo "<center><b><font size='+2' color='blue'>$accion1</font></b></center>";

/*******Traemos y mostramos el Log **********/
$q="SELECT 
	  facturacion.log_comprobante.id_log_comprobante,
      facturacion.comprobante.id_comprobante,
      facturacion.log_comprobante.fecha,
      facturacion.log_comprobante.tipo,
      facturacion.log_comprobante.descripcion,
      facturacion.log_comprobante.usuario
	FROM
      facturacion.comprobante
    LEFT JOIN facturacion.log_comprobante 
          ON (facturacion.comprobante.id_comprobante = facturacion.log_comprobante.id_comprobante)
	where comprobante.id_smiafiliados=$id_smiafiliados --and comprobante.clavebeneficiario=$clavebeneficiario
	order by id_log_comprobante";
$log=$db->Execute($q) or die ($db->ErrorMsg()."<br>$q");?>
<div align="right">
	<input name="mostrar_ocultar_log" type="checkbox" value="1" onclick="if(!this.checked)
																	  document.all.tabla_logs.style.display='none'
																	 else 
																	  document.all.tabla_logs.style.display='block'
																	  "> Mostrar Logs
</div>	
<!-- tabla de Log de la OC -->
<div style="display:'none';width:98%;overflow:auto;<? if ($log->RowCount() > 3) echo 'height:60;' ?> " id="tabla_logs" >
<table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor=#cccccc>
<?while (!$log->EOF){?>
	<tr>
	      <td height="20" nowrap>Fecha <?=fecha($log->fields['fecha']). " " .Hora($log->fields['fecha']);?> </td>
	      <td nowrap > Usuario : <?=$log->fields['usuario']; ?> </td>
	      <td nowrap > Tipo : <?=$log->fields['tipo']; ?> </td>
	      <td nowrap > descipcion : <?=$log->fields['descripcion']; ?> </td>	      
	</tr>
	<?$log->MoveNext();
}?>
</table>
</div>
<hr>
<?/*******************  FIN  LOG  ****************************/?>
<input type="hidden" name="id_smiafiliados" value="<?=$id_smiafiliados?>">
<input type="hidden" name="clavebeneficiario" value="<?=$clavebeneficiario?>">
<input type="hidden" name="pagina" value="<?=$pagina?>">
<input type="hidden" name="pagina_viene" value="<?=$pagina_viene?>">
<input type="hidden" name="pagina_listado" value="<?=$pagina_listado?>">
<input type="hidden" name="activo" value="<?=$activo?>">
<input type="hidden" name="flag_inactivo" value="<?=$flag_inactivo?>">
<input type="hidden" name="fecha_ahora_js" value="<?=fecha(date("Y-m-d"));?>">
<table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
     <font size=+1><b>Beneficiario <?if ($pagina_listado=='listado_beneficiarios_hist.php') echo "<font color=red>Verificando HISTORICOS </font>";?></b></font>    
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
           <td align="right">
         	  <b> Efector Asignado:
         	</td> 
           <td colspan="2">
             <input type='text' name='nombreefecto' value='<?=$nombre;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
        </table>
      </td>      
     </tr>
   </table>     
	 <table class="bordes" align="center" width="70%">
		 <tr align="center" id="sub_tabla">
		 	<td colspan="2">	
		 		Nueva Prestaci�n 
		 		<?if (($pagina_listado=='listado_beneficiarios_hist.php')&& ($res_parametro=='por_periodo')) echo "<font color=red> Verificando Activo PERIODO DE FACTURACION</font>";?>
		 		<?if (($pagina_listado=='listado_beneficiarios_hist.php')&& ($res_parametro=='por_fecha')) echo "<font color=red> Verificando Activo por FECHA DE PRESTACION</font>";?>
		 		<?	 	
		 		if ($pagina_listado=='listado_beneficiarios_leche.php')echo " -- Estado: ".$activo;
		 		?>
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
							onchange="borrar_buffer(); document.forms[0].submit();"
							>
							<option value=-1>Seleccione</option>
							
							<?$user_login1=substr($_ses_user['login'],0,6);
							  if (es_cuie($_ses_user['login'])){
							  	$sql= "select cuie, nombre, com_gestion from nacer.efe_conv where cuie='$user_login1' order by nombre";	
							 }
							  else{
							    $sql= "select cuie, nombre, com_gestion from nacer.efe_conv order by nombre";
							    /*$sql= " select nacer.efe_conv.nombreefector, nacer.efe_conv.cuie from nacer.efe_conv join sistema.usu_efec on (nacer.efe_conv.cuie = sistema.usu_efec.cuie) 
							        join sistema.usuarios on (sistema.usu_efec.id_usuario = sistema.usuarios.id_usuario) 
							        where sistema.usuarios.id_usuario = '$usuario1' order by nombre";*/
							 }
							 $res_efectores=sql($sql) or fin_pagina();?>
			 
			 				 <?while (!$res_efectores->EOF){ 
							 	$com_gestion=$res_efectores->fields['com_gestion'];
							    $cuie=$res_efectores->fields['cuie'];
							    $nombre_efector=$res_efectores->fields['nombre'];
								($com_gestion=='FALSO')?$color_style='#F78181':$color_style='';
							    ?>
								<option value=<?=$cuie;?> 
										Style="background-color: <?=$color_style?>;" 
										<?php if ($cuie==$efector) echo "selected"?>>
								<?=$cuie." - ".$nombre_efector?>
								</option>
							    <?
							    $res_efectores->movenext();
							    }?>
								</select>				 			
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
			                 
			                 <? if ($cuie=='D05035') {
			                 	$sql= "select * from facturacion.servicio where id_servicio=42 or id_servicio=1";
			                 	$res_efectores=sql($sql) or fin_pagina();
			                 	$id_servicio=$res_efectores->fields['id_servicio'];
			                 	$descripcion=$res_efectores->fields['descripcion'];?>
			                 	<option <?=($res_efectores->fields['descripcion']=="No Corresponde")?"selected":""?> value=<?=$id_servicio;?>><?=$descripcion?></option>
			                 <?}
			                 else {
			     			     			
			                 $sql= "select * from facturacion.servicio order by descripcion";
			                 $res_efectores=sql($sql) or fin_pagina();
			                 while (!$res_efectores->EOF){ 
			                 	$id_servicio=$res_efectores->fields['id_servicio'];
			                 	$descripcion=$res_efectores->fields['descripcion'];
			                 ?>
			                   <option <?=($res_efectores->fields['descripcion']=="No Corresponde")?"selected":""?> value=<?=$id_servicio;?>><?=$descripcion?></option>
			                 <?
			                 $res_efectores->movenext();
			                 	}
			                 }?>
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
					    	<?=cargar_calendario();?>				    	
					    	<?$fecha_comprobante=date("d/m/Y");?>
					    	 <input type=text id=fecha_comprobante name=fecha_comprobante value='<?=$fecha_comprobante;?>' size=15 readonly>
					    	 <?=link_calendario("fecha_comprobante");?>					    	 
					    </td>		    
					 </tr>
					 <?$sql="select per_alta_com,adenda_per,fecha_adenda_per,categoria_per 
								from nacer.efe_conv
								where cuie='$efector' and per_alta_com='SI'";
						$res_efec_1=sql($sql,"no se pudo ejecutar");
						
						$dias_de_vida=GetCountDaysBetweenTwoDates($afifechanac, date("Y-m-d"));
						if (($dias_de_vida>=0)&&($dias_de_vida<=365)) $grupo_etareo='unAnio';	
						if (($dias_de_vida>=3600)&&($dias_de_vida<=18200)) $grupo_etareo='Embarazo';	
												
						if (
							($res_efec_1->recordcount()==1) && 
							((trim($afisexo)=='F' and $grupo_etareo=='Embarazo') or ($grupo_etareo=='unAnio'))
							){?>
						<tr>
							<td align="right">
								<b>Factura Alta Complejidad:</b>
							</td>
							<td align="left">
								<input type="checkbox" name="alta_comp" value="alta_comp">				    	 
							</td>		    
						</tr>
						<?}?>
						
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
		    	<input type="submit" name="guardar" value="Guardar Comprobante y Facturar" title="Guardar Comprobante y Facturar" Style="width=250px;height=30px" onclick="return control_nuevos()">
		    </td>
		 </tr> 
	 </table>	
 </td></tr>
 
<?//tabla de comprobantes
$query="SELECT 
  facturacion.comprobante.id_comprobante,
  nacer.efe_conv.nombre,
  facturacion.comprobante.nombre_medico,
  facturacion.comprobante.comentario,
  facturacion.comprobante.fecha_comprobante,
  facturacion.comprobante.id_factura,
  facturacion.comprobante.marca,
  facturacion.comprobante.periodo,
  facturacion.comprobante.alta_comp
FROM
  facturacion.comprobante
  INNER JOIN nacer.efe_conv ON (facturacion.comprobante.cuie = nacer.efe_conv.cuie)
  where id_smiafiliados=$id_smiafiliados --and clavebeneficiario=$clavebeneficiario
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
	<?
	if ($_POST['guardar']=="Guardar Comprobante"){?>
		<script>
			muestra_tabla(document.all.prueba_vida,2);
		</script>
	<?}
	
	if ($res_comprobante->RecordCount()==0){?>
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
	 		<td width="10%">N�mero de Comprobante</td>
	 		<td width="40%">Efector</td>
	 		<td width="30%">Medico</td>
	 		<td width="30%">Comentario</td>
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
	 			$ref = encode_link("prestacion_admin.php",array("id_smiafiliados"=>$id_smiafiliados,"id_comprobante"=>$res_comprobante->fields['id_comprobante'],"pagina_listado"=>$pagina_listado,"pagina_viene"=>"comprobante_admin.php","estado"=>$res_comprobante->fields['id_factura'],"entidad_alta"=>$entidad_alta));
            	$onclick_elegir="location.href='$ref'";  
            	if ($res_comprobante->fields['marca']==0){
            		$ref1 = encode_link("comprobante_admin.php",array("id_comprobante"=>$res_comprobante->fields['id_comprobante'],"marcar"=>"True","id_smiafiliados"=>$id_smiafiliados,"entidad_alta"=>$entidad_alta));
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
	 		
	 		if ($res_comprobante->fields['alta_comp']=='SI'){
			$color_1='#A9BCF5'; 
			$title_1='PRESTACION ALTA COMPLEJIDAD';
			}
			else {
				$color_1=''; 
				$title_1='';
			} 
			
	 		$id_tabla="tabla_".$res_comprobante->fields['id_comprobante'];	
	 		$onclick_check=" javascript:(this.checked)?Mostrar('$id_tabla'):Ocultar('$id_tabla')";
	 		
	 		//consulta para saber si tiene pretaciones el comprobante
	 		$sql="SELECT (SELECT COUNT(id_prestaciones_n_op) FROM nomenclador.prestaciones_n_op WHERE id_comprobante ='". $res_comprobante->fields['id_comprobante']."') + (SELECT COUNT(id_prestacion) 
	 			FROM facturacion.prestacion WHERE id_comprobante='". $res_comprobante->fields['id_comprobante']."') as cant_prestaciones";
	 		$cant_prestaciones=sql($sql,"no se puede traer la cantidad de prestaciones") or die();
	 		$cant_prestaciones=$cant_prestaciones->fields['cant_prestaciones'];
	 		?>
	 		<tr <?=atrib_tr()?>>
	 			<td>
	              <input type=checkbox name=check_prestacion value="" onclick="<?=$onclick_check?>" class="estilos_check">
	            </td>	
		 		<td onclick="<?=$onclick_elegir?>" bgcolor='<?=$color_fondo?>'><font size="3" color="Red"><b><?=$res_comprobante->fields['id_comprobante']. "(".$res_comprobante->fields['id_factura'].")"?></b></font></td>
		 		<td onclick="<?=$onclick_elegir?>" bgcolor="<?=$color_1?>" title="<?=$title_1?>"><?=$res_comprobante->fields['nombre']?></td>
		 		<td onclick="<?=$onclick_elegir?>"><?if ($res_comprobante->fields['nombre_medico']!="") echo $res_comprobante->fields['nombre_medico']; else echo "&nbsp"?></td>
		 		<td onclick="<?=$onclick_elegir?>"><?if ($res_comprobante->fields['comentario']!="") echo $res_comprobante->fields['comentario']; else echo "&nbsp"?></td>
		 		<td onclick="<?=$onclick_elegir?>"><?=fecha($res_comprobante->fields['fecha_comprobante'])?> Edad: <?=$res_comprobante->fields['fecha_comprobante']-$afifechanac?></td>		 		
		 		<td onclick="<?=$onclick_elegir?>"><?="Total: ".$cant_prestaciones?></td>		 		
		 		<td onclick="<?=$onclick_elegir?>"><?=$res_comprobante->fields['periodo']?></td>		 		
		 		<td onclick="<?=$onclick_marcar?>" align="center"><?if ($res_comprobante->fields['marca']==1){echo "<img src='../../imagenes/salir.gif' style='cursor:pointer;'>";}
		 											else if ($res_comprobante->fields['id_factura']!="") {echo "Facturado";}
		 											else echo "<img src='../../imagenes/sin_desc.gif' style='cursor:pointer;'>"?></td>		 		
		 	</tr>	
		 	<tr>
	          <td colspan=9>
	
	                  <?
	                  $sql=" select prestacion.*,nomenclador.*, t1.codigo as cod_diag, t1.descripcion as desc_diag
								from facturacion.prestacion 
								left join facturacion.nomenclador using (id_nomenclador)	
								LEFT JOIN (select distinct codigo,descripcion from nomenclador.patologias_frecuentes) as t1 
								--LEFT JOIN nomenclador.patologias_frecuentes ON (prestacion.diagnostico=patologias_frecuentes.codigo)
								ON (prestacion.diagnostico=t1.codigo)
								where id_comprobante=". $res_comprobante->fields['id_comprobante']." order by id_prestacion DESC";
	                  $result_items=sql($sql) or fin_pagina();
	                  
	                  $sql=" select * from nomenclador.prestaciones_n_op							
							 where id_comprobante=". $res_comprobante->fields['id_comprobante']." order by id_prestaciones_n_op DESC";
					  $result_items1=sql($sql) or fin_pagina();
	                  ?>
	                  <div id=<?=$id_tabla?> style='display:none'>
	                  <table width=90% align=center class=bordes>
	                  			<?
	                  			$cantidad_items=$result_items->recordcount();
	                  			$cantidad_items1=$result_items1->recordcount();
	                  			if (($cantidad_items==0)&&($cantidad_items1==0)){?>
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
		                            <?while (!$result_items1->EOF){?>
							           					<tr>
							           						 <?
							           						 $query="select categoria from nomenclador.grupo_prestacion where codigo='".$result_items1->fields['prestacion']."';";
							           						 $res_i=sql($query) or fin_pagina();
							           						 $query="select categoria from nomenclador.grupo_prestacion where codigo='".$result_items1->fields['tema']."';";
							           						 $res_j=sql($query) or fin_pagina();
							           						 $query="select descripcion from nomenclador.patologias_frecuentes where codigo='".$result_items1->fields['patologia']."';";
							           						 $res_k=sql($query) or fin_pagina();
							           						 $query="select categoria from nomenclador.grupo_prestacion where codigo='".$result_items1->fields['profesional']."';";
							           						 $res_l=sql($query) or fin_pagina();
							           						 $descripcion='<b>Prestacion: </b>'.$res_i->fields["categoria"].' <b>Objeto: </b>'.$res_j->fields["categoria"].' <b>Diagnostico: </b>'.$res_k->fields["descripcion"];
							           						 $descripcion_amp='Prestacion: '.$res_i->fields["categoria"].' Objeto: '.$res_j->fields["categoria"].' Diagnostico: '.$res_k->fields["descripcion"].' Profesional: '.$res_l->fields["categoria"];
							           						 ?>
							                            	 <td class="bordes">1</td>			                                 
							                                 <td class="bordes" title="<?=$result_items1->fields["codigo"]?>"><?=substr($result_items1->fields["codigo"],41,2)."-".substr($result_items1->fields["codigo"],43,4)."-".substr($result_items1->fields["codigo"],47,3)?></td>
							                                 <td class="bordes" title="<?=$descripcion_amp?>"><?=$descripcion?></td>			                                 
							                                 <td class="bordes"><?=number_format($result_items1->fields["precio"],2,',','.')?></td>
							                                 <td class="bordes"><?=number_format($result_items1->fields["precio"],2,',','.')?></td>
							                            </tr>
						                            	<?$result_items1->movenext();
						     
           							}//del while?>
		                            <?while (!$result_items->EOF){?>
			                            <tr>
			                            	 <td class="bordes"><?=$result_items->fields["cantidad"]?></td>			                                 
			                                 <td class="bordes"><?=$result_items->fields["codigo"]?></td>
			                                 <td class="bordes"><?=$result_items->fields["descripcion"]. ' | Diagnostico: '.$result_items->fields["cod_diag"].'-'.$result_items->fields["desc_diag"]?></td>
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

<br>
	<table align='center' border=1 bordercolor='#000000' bgcolor='#FFFFFF' width='80%' cellspacing=0 cellpadding=0>
     <tr>
      <td colspan=10 bordercolor='#FFFFFF'><b>Colores de Referencia para la Columna N�mero de Comprobante:</b></td>
     <tr>
     <td width=30% bordercolor='#FFFFFF'>
      <table border=1 bordercolor='#FFFFFF' cellspacing=0 cellpadding=0 width=100%>
       <tr>
        <td width=30 bgcolor='#FFFFCC' bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Comprobante no Facturado</td>
       </tr>
       <tr>
       	<td>
       	 &nbsp;
       	</td>
       </tr>
       <tr>        
        <td width=30 bgcolor='FF9999' bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Comprobante Facturado</td>
       </tr>
       <tr>
       	<td>
       	 &nbsp;
       	</td>
       </tr>
       <tr>        
        <td width=30 bgcolor='AA888' bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Anulado</td>
       </tr>
      </table>
     </td>
     
     <tr>
      <td colspan=10 bordercolor='#FFFFFF'><b>Colores de Referencia para la Columna EFECTOR:</b></td>
     <tr>
	 <td width=30% bordercolor='#FFFFFF'>
      <table border=1 bordercolor='#FFFFFF' cellspacing=0 cellpadding=0 width=100%>
       <tr>
        <td width=30 bgcolor='#A9BCF5' bordercolor='#000000' height=30>&nbsp;</td>
        <td bordercolor='#FFFFFF'>Prestaciones de Alta Complejidad</td>
       </tr>              
      </table>
     </td>
     
    </table>
    
</form>
<?=fin_pagina();// aca termino ?>
