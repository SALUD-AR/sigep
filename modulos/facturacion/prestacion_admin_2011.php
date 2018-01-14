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

if ($_POST['guardar']=="Guardar Prestacion"){
		$fecha_carga=date("Y-m-d H:i:s");
		
		$sql="select id_categoria_prestacion 
				from nomenclador.grupo_prestacion 
				where codigo='$tema' and id_nomenclador_detalle='$id_nomenclador_detalle'";
		$result_id_tema=sql($sql,'error en el tema');
		$id_tema=$result_id_tema->fields['id_categoria_prestacion'];
 		if (($pagina_viene=='comprobante_admin_total.php')||(valida_prestacion3($id_comprobante,$id_tema))){
 		
	 		$db->StartTrans();
	 		(($profesional=='-1')||($profesional==''))?$profesional="P99":$profesional;
	 		
	 		$fecha_nacimiento_cod=str_replace('-','',$fecha_nacimiento);
	 		$fecha_comprobante_cod=substr(str_replace('-','',$fecha_comprobante),0,8);
	 		
	 		$codigo=$cuie.$fecha_comprobante_cod.$clave_beneficiario.$fecha_nacimiento_cod.$sexo_codigo.$edad.$prestacion.$tema.$patologia.$profesional; 		
	 		
	 		$res_dia_mes_anio=dia_mes_anio($fecha_nacimiento,$fecha_comprobante);
	 		$anios_desde_nac=$res_dia_mes_anio['anios'];
	 		$meses_desde_nac=$res_dia_mes_anio['meses'];
	 		$dias_desde_nac=$res_dia_mes_anio['dias'];

	 		$dias_de_vida=GetCountDaysBetweenTwoDates($fecha_nacimiento, $fecha_comprobante);
				if (($dias_de_vida>=0)&&($dias_de_vida<=28)) $grupo_etareo='Neonato';
				if (($dias_de_vida>28)&&($dias_de_vida<=2190)) $grupo_etareo='Cero a Cinco Años';
				if (($dias_de_vida>2190)&&($dias_de_vida<=3650)) $grupo_etareo='Seis a Nueve Años';
				if (($dias_de_vida>3650)&&($dias_de_vida<=7300)) $grupo_etareo='Adolecente';
				if (($dias_de_vida>7300)&&($dias_de_vida<=23725)) $grupo_etareo='Adulto';	
			if (($sexo=='M')||($sexo=='Masculino')){
					     			$sexo_codigo='V';
					     			$sexo_1='Masculino';
					     			$sexo='M';
					     		}
			if (($sexo=='F')||($sexo=='Femenino')){
					     			$sexo_codigo='M';
					     			$sexo_1='Femenino';
					     			$sexo='F';
					     		}			     		     		
						 		
	 		/*Calcula Precio con tabla auxiliar
	 		$query_precio="select precio from nomenclador.calcula_precio 
	 						where practica_vincula= '$prestacion' and obj_prestacion_vincula = '$tema' and id_nomenclador_detalle='$id_nomenclador_detalle'";
	 		$query_precio=sql($query_precio) or fin_pagina();
	 		
	 		if ($query_precio->recordcount()==0){
	 			$precio=0;	
	 			$msg_precio='No hay precios vinculados a esta prestacion. El precio Grabado fue 0';	
	 		}
	 		if ($query_precio->recordcount()==1){
	 			$precio=$query_precio->fields['precio'];
	 		}
	 		if ($query_precio->recordcount()>1){
	 			$precio=$query_precio->fields['precio'];
	 			$msg_precio='Has mas de UN precios vinculados a esta prestacion.';
	 		}*/
	 		
	 		$query_precio="select precio from nomenclador.grupo_prestacion 
	 						where codigo = '$tema' and id_nomenclador_detalle='$id_nomenclador_detalle'";
	 		$query_precio=sql($query_precio) or fin_pagina();
	 		$precio=$query_precio->fields['precio'];
	 		
	 		
	 		$q="select nextval('nomenclador.prestaciones_n_op_id_prestaciones_n_op_seq') as id_prestacion";
		    $id_prestacion=sql($q) or fin_pagina();
		    $id_prestacion=$id_prestacion->fields['id_prestacion'];
			    
		     //cargo la nueva prestacion - con nomenclador 2011
			$consulta= "insert into nomenclador.prestaciones_n_op
							(id_prestaciones_n_op,id_comprobante,fecha_nacimiento,fecha_comprobante,sexo_codigo, edad, prestacion, tema,patologia, profesional,codigo,precio,anio,mes,dias)
						values 
						    ('$id_prestacion','$id_comprobante','$fecha_nacimiento_cod','$fecha_comprobante_cod','$sexo_codigo','$edad','$prestacion','$tema','$patologia','$profesional','$codigo','$precio','$anios_desde_nac','$meses_desde_nac','$dias_desde_nac')";
			sql($consulta) or fin_pagina();
			
			
		    $db->CompleteTrans();   
		    $accion="Se Grabo la Prestacion.";
 		}
   }

$query="SELECT 
  facturacion.comprobante.id_comprobante,
  nacer.efe_conv.nombre,
  nacer.efe_conv.cuie,
  facturacion.comprobante.nombre_medico,
  facturacion.comprobante.fecha_comprobante
FROM
  facturacion.comprobante
  INNER JOIN nacer.efe_conv ON (facturacion.comprobante.cuie = nacer.efe_conv.cuie)
  where id_comprobante=$id_comprobante";
$res_comprobante=sql($query, "Error al traer el Comprobantes") or fin_pagina();
$nombre=$res_comprobante->fields['nombre'];
$cuie=$res_comprobante->fields['cuie'];
$nombre_medico=$res_comprobante->fields['nombre_medico'];
$fecha_comprobante=$res_comprobante->fields['fecha_comprobante'];

/*$sql=" SELECT  *
FROM
  nacer.efe_conv
  left join facturacion.nomenclador_detalle using (id_nomenclador_detalle)
  where cuie='$cuie'";*/

$sql="SELECT * FROM facturacion.nomenclador_detalle where '$fecha_comprobante' between fecha_desde and fecha_hasta;";

$res_nom=sql($sql, "Error al traer el nomenclador detalle") or fin_pagina();
$descripcion=$res_nom->fields['descripcion'];
$id_nomenclador_detalle=$res_nom->fields['id_nomenclador_detalle'];

if ($nomenclador<>"") {
  		$sql= "select * from facturacion.nomenclador 
				where id_nomenclador='$nomenclador'";
     	$res_codigo=sql($sql) or fin_pagina();
     	$res_codigo=$res_codigo->fields['codigo'];     
}
if ($pagina_viene=='comprobante_admin.php'){
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
	$fecha_nacimiento=$res_comprobante_b->fields['afifechanac'];
	$activo=$res_comprobante_b->fields['activo'];
	$sexo=trim($res_comprobante_b->fields['afisexo']);
	$clave_beneficiario=trim($res_comprobante_b->fields['clavebeneficiario']);
}

if ($pagina_viene=='comprobante_admin_total.php'){
	$sql="select 
			  leche.beneficiarios.id_beneficiarios as id,
			  leche.beneficiarios.apellido as a,
			  leche.beneficiarios.nombre as b,
			  leche.beneficiarios.documento as c,
			  leche.beneficiarios.fecha_nac as d,
			  leche.beneficiarios.domicilio as e,
			  leche.beneficiarios.sexo as f
		 from leche.beneficiarios	 
		 where id_beneficiarios=$id";
	$res_comprobante=sql($sql, "Error al traer los Comprobantes") or fin_pagina();
	
	
	$afiapellido=$res_comprobante->fields['a'];
	$afinombre=$res_comprobante->fields['b'];
	$afidni=$res_comprobante->fields['c'];
	$fecha_nacimiento=$res_comprobante->fields['d'];
	$sexo=trim($res_comprobante->fields['f']);
}

echo $html_header;
?>
<script>

function control_nuevos()
{
 if(document.all.prestacion.value=="-1"){
  alert('Debe Seleccionar una Prestacion');
  return false;
 }
 if(document.all.tema.value=="-1"){
  alert('Debe Seleccionar un Objeto de la Prestacion');
  return false;
 }
 if(document.all.patologia.value=="-1"){
  alert('Debe Seleccionar un Diagnostico');
  return false;
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

<form name='form1' action='prestacion_admin_2011.php' method='POST'>

<input type="hidden" name="id" value="<?=$id?>">
<input type="hidden" name="id_comprobante" value="<?=$id_comprobante?>">
<input type="hidden" name="id_prestacion_extra" value="<?=$id_prestacion?>">
<input type="hidden" name="id_smiafiliados" value="<?=$id_smiafiliados?>">
<input type="hidden" name="id_nomenclador_detalle" value="<?=$id_nomenclador_detalle?>">
<input type="hidden" name="cuie" value="<?=$cuie?>">
<input type="hidden" name="clave_beneficiario" value="<?=$clave_beneficiario?>">
<input type="hidden" name="fecha_nacimiento" value="<?=$fecha_nacimiento?>">
<input type="hidden" name="fecha_comprobante" value="<?=$fecha_comprobante?>">
<input type="hidden" name="pagina_viene" value="<?=$pagina_viene?>">
<input type="hidden" name="pagina_listado" value="<?=$pagina_listado?>">
<table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
     <?php
	     if ($accion=='')echo "<font size=+1><b>PRESTACIONES</b></font>";
	     else echo "<font size=+1 color=white><b>$accion $msg_precio</b></font>";
     ?>
    </td>
 </tr>

 <tr><td>
  <table width=80% align="center" class="bordes">
     <tr>
      <td id=mo colspan="4">
       <b> Descripción del COMPROBANTE</b>
      </td>
     </tr>
     <tr>
       <td colspan="4">
        <table align="center">
		 <tr>
         	<td align="right">
         	  <b>Apellido:
         	</td>         	
            <td align='left'>
              <input type='text' name='afiapellido' value='<?=$afiapellido;?>' size=60 align='right' readonly></b>
            </td>
         </tr>
         <tr>
            <td align="right" >
         	  <b> Nombre:
         	</td>   
           <td >
             <input type='text' name='afinombre' value='<?=$afinombre;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
          <tr>
           <td align="right" >
         	  <b> Documento:
         	</td> 
           <td>
             <input type='text' name='afidni' value='<?=$afidni;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
          <tr>
           <td align="right" >
         	  <b> Fecha de Nacimiento:
         	</td> 
           <td >
             <input type='text' name='fecha_nacimeinto' value='<?=Fecha($fecha_nacimiento);?>' size=60 align='right' readonly></b>
           </td>
          </tr>
	      <tr>
           <td align="right" title="Categoria asignada por el Sistema al momento de Empadronarse" >
         	  <b> Tipo de Afiliado: 
         	</td> 
           <td >
            <input type='text' name='descripcion' value='<?echo $codcategoria . "-" .$descripcion_b;?>' size=60 align='right' readonly></b>
           </td>
          </tr>
         
         <?if (!permisos_check('inicio','cambia_nomenclador_prestaciones')) 
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
			    <?$sql="select * from facturacion.nomenclador_detalle where modo_facturacion='2'";
			    $res=sql($sql) or fin_pagina();
			    while (!$res->EOF){ 
			    	$id_nomenclador_detalle_1=$res->fields['id_nomenclador_detalle'];
			        $descripcion=$res->fields['descripcion'];?>
			        <option value=<?=$id_nomenclador_detalle_1;if ($id_nomenclador_detalle==$id_nomenclador_detalle_1) echo " selected"?> >
			        	<?=$descripcion?>
			        </option>
			        <?$res->movenext();
			        }?>
			    </select>
			</td>
		</tr>
		
         <tr>
         	<td align="right">
         	  <b>Nombre del Efector:
         	</td>         	
            <td align='left'>
              <input type='text' name='afiapellido' value='<?=$nombre;?>' size=60 align='right' readonly></b>
            </td>
         </tr>
         <tr>
           <td align="right">
         	  <b> Fecha de la Prestacion:
         	</td> 
           <td colspan="2">
             <input type='text' name='afidni' value='<?=fecha($fecha_comprobante);?>' size=60 align='right' readonly></b>
           </td>
          </tr>
          <tr>      
			<td align="right" title="Grupo Etareo al la Fecha de la Practica">
				<b>Grupo Etario: </b>
			</td>
			 <td align="left">
				<?$dias_de_vida=GetCountDaysBetweenTwoDates($fecha_nacimiento, $fecha_comprobante);
				if (($dias_de_vida>=0)&&($dias_de_vida<=28)) $grupo_etareo='Neonato';
				if (($dias_de_vida>28)&&($dias_de_vida<=2190)) $grupo_etareo='Cero a Cinco Años';
				if (($dias_de_vida>2190)&&($dias_de_vida<=3650)) $grupo_etareo='Seis a Nueve Años';
				if (($dias_de_vida>3650)&&($dias_de_vida<=7300)) $grupo_etareo='Adolecente';
				if (($dias_de_vida>7300)&&($dias_de_vida<=23725)) $grupo_etareo='Adulto';?>					     		     		
				<input type="text" value="<?echo $grupo_etareo?>" name=grupo_etareo Style="width=400px"size="8" readonly>
				<input type="hidden" value="<?=$dias_de_vida?>" name="dias_de_vida">
			 </td>			               
			</tr>                  
        </table>
      </td>      
     </tr>
   </table>     
	 <table class="bordes" align="center" width="80%">
		 <tr align="center" id="sub_tabla">		 	
		 	<td colspan="3">	
		 		Referencia para Diagnostico
		 	</td>
		 	<td colspan="2">	
		 		Nueva PRESTACION
		 	</td>
		 </tr>
		 <tr>
		 
		  <td colspan="3" align="center">
			 <table align='center' border=1 bordercolor='#000000' bgcolor='#FFFFFF' width='100%' cellspacing=0 cellpadding=0>
		     
		     <td width=30% bordercolor='#FFFFFF'>
		      <table border=1 bordercolor='#FFFFFF' cellspacing=0 cellpadding=0 width=100%>
		       <tr>
		        <td width=30 bgcolor='#BEF781' bordercolor='#000000' height=20>&nbsp;</td>
		        <td bordercolor='#FFFFFF'>Signos y Sintomas</td>
		        <td width=30 bgcolor='#F3F781' bordercolor='#000000' height=20>&nbsp;</td>
		        <td bordercolor='#FFFFFF'>Infecciones</td>
		       </tr>
		       <tr>
		       	<td>
		       	 &nbsp;
		       	</td>
		       </tr>
		       <tr>        
		        <td width=30 bgcolor='#46D7F4' bordercolor='#000000' height=20>&nbsp;</td>
		        <td bordercolor='#FFFFFF'>Neoplacias</td>
		        <td width=30 bgcolor='#F366D7' bordercolor='#000000' height=20>&nbsp;</td>
		        <td bordercolor='#FFFFFF'>Lesiones</td>
		       </tr>
		       <tr>
		       	<td>
		       	 &nbsp;
		       	</td>
		       </tr>
		       <tr>        
		        <td width=30 bgcolor='#81BEF7' bordercolor='#000000' height=20>&nbsp;</td>
		        <td bordercolor='#FFFFFF'>Anomalias Congenitas</td>
		        <td width=30 bgcolor='#D0A9F5' bordercolor='#000000' height=20>&nbsp;</td>
		        <td bordercolor='#FFFFFF'>Otros Diagnosticos</td>
		       </tr>
		      </table>
		     </td>
		    </table>
		 </td>
		 
		 <td class="bordes"><table>
			 <tr>
				 <td>	 	
					    <tr>      
			             <td align="right">
					    		<b>Sexo: </b>
					    	</td>
					     	<td align="left">
					    
					     		<?
					     		if (($sexo=='M')||($sexo=='Masculino')){
					     			$sexo_codigo='V';
					     			$sexo_1='Masculino';
					     			$sexo='M';
					     		}
					     		if (($sexo=='F')||($sexo=='Femenino')){
					     			$sexo_codigo='M';
					     			$sexo_1='Femenino';
					     			$sexo='F';
					     		}
					     		?>					     		
					     	    <input type="hidden" value="<?=$sexo_codigo?>" name="sexo_codigo">
					    		<input type="text" value="<?=$sexo_1?>" name=sexo Style="width=400px"size="8" readonly>
			               </td>			               
					 	</tr>
					 	<tr>
					 		<td align="right" title="A la fecha de la Practica">
					 		<b>Edad: </b>
					    	</td>
					     	<td align="left">
					     	
					     	<?	
					     	$codigo_edad=$fecha_comprobante-$fecha_nacimiento;			     	
					     	if (strlen($codigo_edad)=='1'){
					     		$codigo_edad='0'.$codigo_edad;
					     	}
					     	else{
					     		$codigo_edad=$codigo_edad;
					     	}
					     	?>
					     		<input type="text" value="<?=$codigo_edad?>" name=edad Style="width=400px"size="8" readonly> 
			               </td>			               
					 	</tr>
					 	<tr>
					 		<td align="right">
					 			<b>Prestacion: </b>
					    	</td>
					    	<td align="left">
					     		<select name=prestacion Style="width=400px"
					 				onKeypress="buscar_combo(this);"
					 				onblur="borrar_buffer();"
					 				onchange="borrar_buffer(); document.forms[0].submit();">
				     				<option value=-1>Seleccione</option>
				                	<?$sql= "SELECT * FROM nomenclador.grupo_prestacion 
						     		WHERE ((tema = 'PRESTACION') and (id_nomenclador_detalle='$id_nomenclador_detalle')) order by id_categoria_prestacion";
				                 	$res_efectores=sql($sql) or fin_pagina();
				                 	while (!$res_efectores->EOF){ 
				                 		$categoria=$res_efectores->fields['categoria'];
				                 		$codigo=$res_efectores->fields['codigo'];?>		                 
				                   		<option value='<?=$codigo;?>' <?php if ($prestacion==$codigo) echo "selected"?>> 
				                  	 	<?=$codigo." - ".$categoria?></option>
				                    	<?$res_efectores->movenext();
				                 	}?>
			        			</select>
			      			</td>
			      			
					    	<td align="left">					    		
					    		<b><label style="color:green; font-size: 11pt"><?echo $prestacion;?></label></b>		     		
			                </td>
					 	</tr>
					 	
					 	<tr>
					 		<td align="right">
					 			<b>Objeto de la Prestación: </b>
					    	</td>
					    	<td align="left">
					     		<select name=tema Style="width=400px"
				 				onKeypress="buscar_combo(this);"
				 				onblur="borrar_buffer();"
				 				onchange="borrar_buffer(); document.forms[0].submit();">
			     				<option value=-1>Seleccione</option>
			                	 <? 
			                	 $sql= "SELECT * FROM nomenclador.grupo_prestacion WHERE (tema = 'OBJETO DE LA PRESTACION' and categoria_padre= '$prestacion' and id_nomenclador_detalle='$id_nomenclador_detalle') order by codigo";
			                 $res_efectores=sql($sql) or fin_pagina();
			                 while (!$res_efectores->EOF){ 
			                 	$categoria=$res_efectores->fields['categoria'];
			                 	$codigo=$res_efectores->fields['codigo'];
			                 	
			              			                 	
			                 ?>
			                   <option value=<?=$codigo;?> <?php if ($tema==$codigo) echo "selected"?>><?=$codigo.' - '.$categoria?></option>
			                   
			                 <?
			                 $res_efectores->movenext();
			                 }
			                 ?>
			      			</select>
			      			</td>
			      			
					    	<td align="left">
					    		<?if ($tema=='-1'){
					    			$color='red';
					    			$tema_cartel="*";
					    		}
					    		else{
					    			$color='green';
					    			$tema_cartel=$tema;
					    		}
					    		?>
					    		<b><label style="color:<?=$color?>; font-size: 11pt"><?echo $tema_cartel;?></label></b>	
			                </td>
					 	</tr>
					 	
					 	<tr>
					 		<td align="right">					 			
					 			<b>Grupo de Diagnostico: </b>
					    	</td>
					    	<td align="left">	
					    		<?if ($tipo_diag_radio=='')$tipo_diag_radio='Frec'?>				    		
					     		<input type="radio" name="tipo_diag_radio" value="Frec" onclick="document.forms[0].submit();" <?if ($tipo_diag_radio=='Frec') echo "checked"?>> Frecuente
					 			<input type="radio" name="tipo_diag_radio" value="Comp" onclick="document.forms[0].submit();" <?if ($tipo_diag_radio=='Comp') echo "checked"?>> Completo
			      			</td>
			      			
					 	</tr>
					 	<tr>
					 		<td align="right">					 			
					 			<b>Diagnostico: </b>
					    	</td>
					    	<td align="left">
					     		<select name=patologia Style="width=400px"
					 				onKeypress="buscar_combo(this);"
					 				onblur="borrar_buffer();"
					 				onchange="borrar_buffer(); document.forms[0].submit();">
				     				<option value=-1>Seleccione</option>
			                	 	<? 
			                	 	if ($tipo_diag_radio=='Frec') $tabla_consulta_diag="nomenclador.patologias_frecuentes";
			                	 	if ($tipo_diag_radio=='Comp') $tabla_consulta_diag="nomenclador.patologias";
			                	 	
			                	 	if ($grupo_etareo=='Neonato'){
				                	 	$sql= "SELECT * 
				                	 			FROM $tabla_consulta_diag 
				                	 			where 
				                	 				(id_nomenclador_detalle='$id_nomenclador_detalle') and
				                	 				(neo='1')
				                	 			order by codigo";
			                	 	}
			                	 	if ($grupo_etareo=='Cero a Cinco Años'){
				                	 	$sql= "SELECT * 
				                	 			FROM $tabla_consulta_diag 
				                	 			where 
				                	 				(id_nomenclador_detalle='$id_nomenclador_detalle') and
				                	 				(ceroacinco='1')
				                	 			order by codigo";
			                	 	}
			                	 	if ($grupo_etareo=='Seis a Nueve Años'){
				                	 	$sql= "SELECT * 
				                	 			FROM $tabla_consulta_diag
				                	 			where 
				                	 				(id_nomenclador_detalle='$id_nomenclador_detalle') and
				                	 				(seisanueve='1')
				                	 			order by codigo";
			                	 	}
			                	 	if (($grupo_etareo=='Adolecente')&&($sexo=='M')){
				                	 	$sql= "SELECT * 
				                	 			FROM $tabla_consulta_diag
				                	 			where 
				                	 				(id_nomenclador_detalle='$id_nomenclador_detalle') and
				                	 				(adol='1') and (m='1')
				                	 			order by codigo";
			                	 	}
			                	 	if (($grupo_etareo=='Adolecente')&&($sexo=='F')){
				                	 	$sql= "SELECT * 
				                	 			FROM $tabla_consulta_diag 
				                	 			where 
				                	 				(id_nomenclador_detalle='$id_nomenclador_detalle') and
				                	 				(adol='1') and (f='1')
				                	 			order by codigo";
			                	 	}
			                	 	if (($grupo_etareo=='Adulto')&&($sexo=='M')){
				                	 	$sql= "SELECT * 
				                	 			FROM $tabla_consulta_diag
				                	 			where 
				                	 				(id_nomenclador_detalle='$id_nomenclador_detalle') and
				                	 				(adulto='1') and (m='1')
				                	 			order by codigo";
			                	 	}
			                	 	if (($grupo_etareo=='Adulto')&&($sexo=='F')){
				                	 	$sql= "SELECT * 
				                	 			FROM $tabla_consulta_diag
				                	 			where 
				                	 				(id_nomenclador_detalle='$id_nomenclador_detalle') and
				                	 				(adulto='1') and (f='1')
				                	 			order by codigo";
			                	 	}
			                 
			                	 	$res_efectores=sql($sql) or fin_pagina();
			                	 	while (!$res_efectores->EOF){ 
			                 			$descripcion=$res_efectores->fields['descripcion'];
			                 			$codigo=$res_efectores->fields['codigo'];
			                 			$ceps_ap=$res_efectores->fields['ceps_ap'];
			                 			$color_diag=$res_efectores->fields['color'];?>
			                   			<option value=<?=$codigo;?> <?php if ($patologia==$codigo) echo "selected"?> <?=$color_diag?>> 
			                  	 		<?=$ceps_ap." - ".$codigo." - ".$descripcion?></option>
			                 			<?$res_efectores->movenext();
			                	 	}?>
			      				</select>
			      			</td>
			      			<td align="left">
					    		<?if ($patologia=='-1'){
					    			$color='red';
					    			$patologia_cartel='*';
					    		}
					    		else{
					    			$color='green';
					    			$patologia_cartel=$patologia;
					    		}
					    		?>
					    		<b><label style="color:<?=$color?>; font-size: 11pt"><?echo $patologia_cartel;?></label></b>	
			                </td>
					 	</tr>
					 						 						 	
					 	<!--<tr>
					 		<td align="right">
					 			<b>Profesional: </b>
					    	</td>
					    	<td align="left">
					     		<select name=profesional Style="width=400px"
				 				onKeypress="buscar_combo(this);"
				 				onblur="borrar_buffer();"
				 				onchange="borrar_buffer(); document.forms[0].submit();">
			     				<option value=-1>Seleccione</option>
			                	 <?/*$sql= "SELECT * FROM nomenclador.grupo_prestacion WHERE ((tema = 'PROFESIONAL') and (id_nomenclador_detalle='$id_nomenclador_detalle'))";
			                 $res_efectores=sql($sql) or fin_pagina();
			                 while (!$res_efectores->EOF){ 
			                 	$categoria=$res_efectores->fields['categoria'];
			                 	$codigo=$res_efectores->fields['codigo'];?>
         	                   <option value=<?=$codigo;?> <?php if ($profesional==$codigo) echo "selected"?>> 
			                  	 <?=$codigo." - ".$categoria?>
			                   </option>
			                 <?
			                 $res_efectores->movenext();
			                 }
			                 ?>
			      			</select>
			      			</td>
			      			<td align="right">
					    		<b>Codigo: </b>
					    	</td>
					    	<td align="left">
					    		<?if ($profesional=='-1'){
					    			$color='orange';
					    			$profesional_cartel='Opcional';
					    		}
					    		else{
					    			$color='green';
					    			$profesional_cartel=$profesional;
					    		}*/
					    		?>
					    		<b><label style="color:<?/*echo $color;*/?>; font-size: 12pt"><?/*echo $profesional_cartel;*/?></label></b>	
			                </td>
					 	</tr>-->				 			 			 
				  </td>
			 </tr>
		 </table>
		 </td>
		 
		 </tr>
		 	 
		 <tr> 
		  	<td align="center" colspan="4" class="bordes">
		  		<?php
		  		$query_trz="SELECT *
		    				FROM nomenclador.trz_pres
		    				WHERE obj_prestacion_vincula='$tema' and grupo_etareo='$grupo_etareo' and sexo='$sexo'";
		    	$result_trz=sql($query_trz,"no se puede ejecutar");
		    	
		    	
		    	if (($result_trz->RecordCount()>0)&&($result_trz->fields['obliga_efector']=='1')){
		    		if ($usuario1)$hab_on_line="disabled";		    		
		    	}?>
		  		<input type="submit" name="guardar" value="Guardar Prestacion" title="Guardar Prestacion" Style="width=300px;height=40px" onclick="return control_nuevos()" <?=$hab_on_line?>>
		    	<?php		    	
		    	if ($result_trz->RecordCount()>0){
		    		$texto_boton=$result_trz->fields['texto_boton'];
		    		$trz_vincula=$result_trz->fields['trz_vincula'];
		    		$pagina_destino=$result_trz->fields['pagina_destino'];
		    		$param_pagina_destino=$result_trz->fields['param_pagina_destino'];
		    		$practica_vincula=$result_trz->fields['practica_vincula'];
		    		$obj_prestacion_vincula=$result_trz->fields['obj_prestacion_vincula'];
		    		$diagnostico=$result_trz->fields['diagnostico'];
		    		
		    		$ref=$ref = encode_link("../trazadoras/$pagina_destino",array("fecha_comprobante"=>$fecha_comprobante,"id_smiafiliados"=>$id_smiafiliados,"cuie"=>$cuie,"pagina"=>"prestacion_admin.php"));
  	    		?>	
  	    		&nbsp;&nbsp;&nbsp;&nbsp;	    		
				<input type=button name="carga_trazadora" value="<?php echo $texto_boton?>" 
  	    		onclick="if(document.all.patologia.value=='-1'){
  							alert('Debe Seleccionar un Diagnostico');
  	    				}
  	    				else{  	    				
	  	    				document.all.guardar.disabled=false; 
	  	    				window.open('<?=$ref?>','Trazadoras','dependent:yes,width=900,height=700,top=1,left=60,scrollbars=yes');
  	    				}" 
  	    		title="<?php echo $trz_vincula?>" Style="width=300px;height=40px;background-color:#F781F3;">
		    	<?php }?>		  		
		    </td>
		 </tr> 
	 </table>	
 </td></tr>
 
   
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
	 <?}?>
	</td>   
  </tr>
 </table></td></tr>
 </table>
 
 </form>
 
 <?=fin_pagina();// aca termino ?>
