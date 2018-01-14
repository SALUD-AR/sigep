<?require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

cargar_calendario();

if ($_POST['guardar']=="Guardar"){   
     $fecha_registro=Fecha_db($fecha_registro);
     $fecha_carga=date("Y-m-d H:i:s");     
     $usuario=$_ses_user['id'];
	 
	 	$q="select nextval('registro.registro_id_registro_seq') as id_planilla";
	    $id_planilla=sql($q) or fin_pagina();
	    $id_planilla=$id_planilla->fields['id_planilla'];
	       
	    $query="insert into registro.registro
	             (id_registro,cuie,evolucion,id_beneficiarios,fecha_registro)
	             values
	             ('$id_planilla','$cuie','$evolucion','$id_beneficiarios','$fecha_registro')";
	
	    sql($query, "Error al insertar la Planilla") or fin_pagina();
	    
	    $accion="Registro Grabado con el Numero de Transaccion: $id_planilla";  
	     /*cargo los log*/ 
	    $q_1="select nextval('registro.log_registro_id_log_registro_seq') as id_log";
	    $id_log=sql($q_1) or fin_pagina();
	    $id_log=$id_log->fields['id_log'];
	    
		    $usuario=$_ses_user['name'];
			$log="insert into registro.log_registro
				   (id_log_registro,id_registro, fecha, tipo, descripcion, usuario) 
			values ($id_log, '$id_planilla','$fecha_carga','Nuevo Registro','Nro. Transaccion $id_planilla', '$usuario')";
			sql($log) or fin_pagina();    
} 

if ($id_beneficiarios){//carga de prestacion a paciente NO PLAN NACER
	$sql="select 
	  uad.beneficiarios.id_beneficiarios,
	  uad.beneficiarios.apellido_benef,
	  uad.beneficiarios.nombre_benef,
	  uad.beneficiarios.numero_doc,
	  uad.beneficiarios.fecha_nacimiento_benef,
	  uad.beneficiarios.localidad_nac
	  from uad.beneficiarios
	  where id_beneficiarios=$id_beneficiarios";
    $res_comprobante=sql($sql, "Error al traer los Comprobantes") or fin_pagina();
    
    $apellido=$res_comprobante->fields['apellido_benef'];
	$nombre=$res_comprobante->fields['nombre_benef'];
	$dni=$res_comprobante->fields['numero_doc'];
	$fecha_nac=$res_comprobante->fields['fecha_nacimiento_benef'];

} 
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
//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos()
{
 	 
	 if(document.all.evolucion.value==""){
	  alert('Debe Ingresar una Evolucion');
	  return false; 
	 } 

}//de function control_nuevos()



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
<?

?>
<form name='form1' action='paciente_admin.php' method='POST'>
<input type="hidden" value="<?=$id_beneficiarios?>" name="id_beneficiarios">
<?
echo "<center><b><font size='+2' color='red'>$accion</font></b></center>";

/*******Traemos y mostramos el Log **********/
$q="SELECT 
	  *
	FROM
     registro.log_registro
    LEFT JOIN registro.registro using (id_registro)           
	where registro.registro.id_beneficiarios='$id_beneficiarios'
	order by id_log_registro";
$log=sql($q);
?>
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
<table width="85%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
    	<font size=+1><b>Registro Clinico</b></font>    	       
    </td>
 </tr>
 <tr><td>
  <table width=90% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b> Descripcion del Beneficario </b>
      </td>
     </tr>
     <tr>
       <td>
        <table>           
         <tr>
         	<td align="right">
         	  <b>Apellido:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$apellido?>" name="apellido" <? if ($id_beneficiarios) echo "readonly"?>>
            </td>
       
         	<td align="right">
         	  <b>Nombre:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="40" value="<?=$nombre?>" name="nombre" <? if ($id_beneficiarios) echo "readonly"?>>
            </td>
         </tr> 
		<tr>
         	<td align="right">
         	  <b>Documento:</b>
         	</td>         	
            <td align='left'>
              <input type="text" size="20" value="<?=$dni?>" name="dni" <? if ($id_beneficiarios) echo "readonly"?>>
            </td>
         
			<td align="right">
				<b>Fecha de Nacimiento:</b>
			</td>
		    <td align="left">
		    	 <input type=text id=fecha_nac name=fecha_nac value='<?=fecha($fecha_nac);?>' size=15 readonly>
		    </td>	
		    <td align="right" title="Edad a la Fecha actual">
         	  <b> Edad a la Fecha Actual:
           </td> 
           <td align='left'>
			 <?
				function bisiesto_local($anio_actual){ 
				    $bisiesto=false; 
				    //probamos si el mes de febrero del a�o actual tiene 29 d�as 
				      if (checkdate(2,29,$anio_actual)) 
				      { 
				        $bisiesto=true; 
				    } 
				    return $bisiesto; 
				} 
				function edad_con_meses($fecha_de_nacimiento){ 
					$fecha_actual = date ("Y-m-d"); 

					// separamos en partes las fechas 
					$array_nacimiento = explode ( "-", $fecha_de_nacimiento ); 
					$array_actual = explode ( "-", $fecha_actual ); 

					$anos =  $array_actual[0] - $array_nacimiento[0]; // calculamos a�os 
					$meses = $array_actual[1] - $array_nacimiento[1]; // calculamos meses 
					$dias =  $array_actual[2] - $array_nacimiento[2]; // calculamos d�as 

					//ajuste de posible negativo en $d�as 
					if ($dias < 0) 
					{ 
						--$meses; 

						//ahora hay que sumar a $dias los dias que tiene el mes anterior de la fecha actual 
						switch ($array_actual[1]) { 
							   case 1:     $dias_mes_anterior=31; break; 
							   case 2:     $dias_mes_anterior=31; break; 
							   case 3:  
									if (bisiesto_local($array_actual[0])) 
									{ 
										$dias_mes_anterior=29; break; 
									} else { 
										$dias_mes_anterior=28; break; 
									} 
							   case 4:     $dias_mes_anterior=31; break; 
							   case 5:     $dias_mes_anterior=30; break; 
							   case 6:     $dias_mes_anterior=31; break; 
							   case 7:     $dias_mes_anterior=30; break; 
							   case 8:     $dias_mes_anterior=31; break; 
							   case 9:     $dias_mes_anterior=31; break; 
							   case 10:     $dias_mes_anterior=30; break; 
							   case 11:     $dias_mes_anterior=31; break; 
							   case 12:     $dias_mes_anterior=30; break; 
						} 

						$dias=$dias + $dias_mes_anterior; 
					} 

					//ajuste de posible negativo en $meses 
					if ($meses < 0) 
					{ 
						--$anos; 
						$meses=$meses + 12; 
					} 
					$edad_con_meses_result= array("anos"=>$anos,"meses"=>$meses,"dias"=>$dias);
					return  $edad_con_meses_result;
				}
			 $edad_con_meses=edad_con_meses($fecha_nac);
			 $anio_edad=$edad_con_meses["anos"];
			 $meses_edad=$edad_con_meses["meses"];
			 $dias_edad=$edad_con_meses["dias"];
			 ?>
         	 <input type='text' name='edad' value='<?echo $anio_edad." Años, ".$meses_edad." Meses"?>' size=30 align='right' readonly></b>
			</td>   
		    	    
		</tr>
	<?// -------------tablas dobes para armar los datos de la vacuna-----------------?>	
		
	<table width=100% align="center" class="bordes">
     <tr align="center" id="sub_tabla">
      <td colspan="2">
       <b> Carga de Evolucion </b>
      </td>
     </tr>
     
         <tr>
         	<td align="right">
				<b>Establecimiento de Salud:</b>
			</td>
			<td align="left">			 	
			 <select name=cuie Style="width=457px" 
        		onKeypress="buscar_combo(this);"
				onblur="borrar_buffer();"
				onchange="borrar_buffer();" 
				>
			 <?$user_login1=substr($_ses_user['login'],0,6);
								  if (es_cuie($_ses_user['login'])){
									$sql1= "select cuie, nombre, com_gestion from nacer.efe_conv where cuie='$user_login1' order by nombre";
								   }									
								  else{
									$usuario1=$_ses_user['id'];
									$sql1= "select nacer.efe_conv.nombre, nacer.efe_conv.cuie, com_gestion 
											from nacer.efe_conv 
											join sistema.usu_efec on (nacer.efe_conv.cuie = sistema.usu_efec.cuie) 
											join sistema.usuarios on (sistema.usu_efec.id_usuario = sistema.usuarios.id_usuario) 
											where sistema.usuarios.id_usuario = '$usuario1'
										 order by nombre";
								   }			 			   
								 $res_efectores=sql($sql1) or fin_pagina();
							 
							 while (!$res_efectores->EOF){ 
								$com_gestion=$res_efectores->fields['com_gestion'];
								$cuie=$res_efectores->fields['cuie'];
								$nombre_efector=$res_efectores->fields['nombre'];
								if($com_gestion=='FALSO')$color_style='#F78181'; else $color_style='';
								?>
								<option value='<?=$cuie;?>' Style="background-color: <?=$color_style;?>"><?=$cuie." - ".$nombre_efector?></option>
								<?
								$res_efectores->movenext();
								}?>
			</select>
			</td>
			
			
		</tr>	
		
		<tr>	
			<td align="right">
				<b>Fecha Control:</b>
			</td>
			<td align="left">
		    	<?$fecha_registro=date("d/m/Y");?>
		    	 <input type=text id=fecha_registro name=fecha_registro value='<?=$fecha_registro;?>' size=15  readonly> 
		    	 <?=link_calendario("fecha_registro");?>
		    </td>
			    		    
		</tr>  		
		     
          <tr>         	
         	<td align="right">
         	  <b>Evolucion:</b>
         	</td>         	
            <td align='left'>
              <textarea cols='70' rows='4' name='evolucion' ></textarea>
            </td>
         </tr>  		 
        
     <?// -------------tablas dobes para armar los datos de la vacuna-----------------?>	  
    </tr></td></table>    
  </table>
<?// -------------tablas dobes para armar los datos de la vacuna-----------------?>	  

	 
	 <tr id="mo">
  		<td align=center colspan="2">
  			<b>Guarda Registro</b>
  		</td>
  	</tr>  
      <tr align="center">
       <td>
        <input type='submit' name='guardar' value='Guardar' Style="width=300px;height=30px" onclick="return control_nuevos()"
         title="Guardar registro de vacunacion">
       </td>
      </tr>
     
           
<br>

	
 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
     <input type=button name="volver" value="Volver" onclick="document.location='paciente_listado.php'"title="Volver al Listado" style="width=150px">
   </td>
  </tr>
 </table></td></tr>
 </table>
 <?// --------------------tablas de muestra de vacunas dadas y facturadas 
	$query="SELECT *,nacer.efe_conv.nombre as nom_efector
			FROM
			registro.registro
			INNER JOIN nacer.efe_conv ON registro.registro.cuie = nacer.efe_conv.cuie
			where registro.registro.id_beneficiarios='$id_beneficiarios'
			ORDER BY registro.registro.fecha_registro DESC";

$res_comprobante=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();?>

<tr><td><table width="85%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Comprobantes" align="left" style="cursor:pointer;" onclick="muestra_tabla(document.all.prueba_vida,2);" >
	  </td>
	  <td align="center">
	   <b>Prestaciones</b>
	  </td>
	</tr>
</table></td></tr>
<tr><td><table id="prueba_vida" border="1" width="85%" style="display:none;border:thin groove"align="center">
	<?
	if ($_POST['guardar']=="Guardar"){?>
		<script>
			muestra_tabla(document.all.prueba_vida,2);
		</script>
	<?}
	
	if ($res_comprobante->RecordCount()==0){?>
	 <tr>
	  <td align="center">
	   <font size="3" color="Red"><b>No existen Prestaciones</b></font>
	  </td>
	 </tr>
	 <?}
	 else{	 	
	 	?>
	 	<tr id="sub_tabla">	
	 		<td >Id Transaccion</td>	 	    
	 		<td >Efector</td>	 		
	 		<td >Fecha</td>	 		
	 		<td >Evolucion</td>	 		
	 	</tr>
	 	<?
	 	$res_comprobante->movefirst();
	 	while (!$res_comprobante->EOF) {?>
	 		<tr <?=atrib_tr()?>>
	 			<td align="center" ><?=$res_comprobante->fields['id_registro']?></td>	
		 		<td align="center" ><?=$res_comprobante->fields['cuie'].' - '.$res_comprobante->fields['nom_efector']?></td>
		 		<td align="center" ><?=fecha($res_comprobante->fields['fecha_registro'])?></td>		 		
		 		<td align="center" ><?=$res_comprobante->fields['evolucion']?></td>		 		 		 		
		 	</tr>	
		 	
	 		<?$res_comprobante->movenext();
	 	}
	 }?>
</table></td></tr>
 <? // --------------------tabla principal color--------------?>
  </table>  
 </form>
 
 <?=fin_pagina();// aca termino ?>
