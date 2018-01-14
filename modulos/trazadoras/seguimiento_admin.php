<?
require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();

if ($_POST['guardar']=="Guardar"){		
	$db->StartTrans();
	$fecha_carga=date("Y-m-d H:m:s");
    $usuario=$_ses_user['name'];
	$fecha_comprobante=fecha_db($fecha_comprobante);
	$fecha_comprobante_proximo=fecha_db($fecha_comprobante_proximo);
	
	$q="select nextval('trazadoras.seguimiento_remediar_id_seguimiento_remediar_seq') as id_planilla";
    $id_planilla=sql($q) or fin_pagina();
    $id_planilla=$id_planilla->fields['id_planilla'];
	
	$query = "insert into trazadoras.seguimiento_remediar
				(id_seguimiento_remediar,dtm2,hta,ta_sist,ta_diast,tabaquismo,col_tot,gluc,peso,talla,imc,hba1c,ecg,fondodeojo,
				examendepie,microalbuminuria,hdl,ldl,tags,creatininemia,esp1,esp2,esp3,esp4,riesgo_global,riesgo_globala,comentario,fecha_carga,usuario,
				efector,fecha_comprobante,fecha_comprobante_proximo,clave_beneficiario,profesional)
            values 
				($id_planilla,'$dtm2','$hta','$ta_sist','$ta_diast','$tabaquismo','$col_tot','$gluc','$peso','$talla','$imc','$hba1c','$ecg','$fondodeojo',
				'$examendepie','$microalbuminuria','$hdl','$ldl','$tags','$creatininemia','$esp1','$esp2','$esp3','$esp4','$riesgo_global','$riesgo_globala','$comentario','$fecha_carga','$usuario',
				'$efector','$fecha_comprobante','$fecha_comprobante_proximo','$clave_beneficiario','$profesional')";

    $res_extras = sql($query, "Error al insertar la Planilla") or fin_pagina();
    $accion = "Se guardo el Seguiemiento Nro. " . $id_planilla;
	$db->CompleteTrans();  
}

if ($marcar=="True"){
	$query = "delete from trazadoras.seguimiento_remediar where id_seguimiento_remediar=$id_seguimiento_remediar";
    $res_extras = sql($query, "Error al insertar la Planilla") or fin_pagina();
    $accion = "Se Elimino el Seguiemiento Nro. " . $id_seguimiento_remediar;				
}

//inicial los datos para cargar el beneficiarios
if ($pagina=='listado_beneficiarios_leche.php'){
	$sql="select clavebeneficiario from nacer.smiafiliados where id_smiafiliados='$id_smiafiliados'";
	$res_clave = sql($sql, "Error al traer la clave del beneficiario") or fin_pagina();
	$clave_beneficiario=$res_clave->fields['clavebeneficiario'];
}
if ($clave_beneficiario){
	$sql="select *
	     from uad.beneficiarios	 
		 where clave_beneficiario='$clave_beneficiario'";
	$res_comprobante=sql($sql, "Error al traer los Comprobantes") or fin_pagina();
}

$clave_beneficiario=$res_comprobante->fields['clave_beneficiario'];
$apellido_benef=$res_comprobante->fields['apellido_benef'];
$nombre_benef=$res_comprobante->fields['nombre_benef'];
$numero_doc=$res_comprobante->fields['numero_doc'];
$fecha_nacimiento_benef=$res_comprobante->fields['fecha_nacimiento_benef'];
$calle=$res_comprobante->fields['calle'];

echo $html_header;
?>
<script>
//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos()
{
 
 if(document.all.efector.value=="-1"){
            alert("Debe elegir un efector");
            document.all.efector.focus();
             return false;
           }
 if(document.all.profesional.value==""){
  alert('Debe Ingresar un Profesional');
  return false;
 }
 
 if (confirm('Esta Seguro que Desea Guardar Seguimiento?'))return true;
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

<form name='form1' action='seguimiento_admin.php' method='POST' enctype='multipart/form-data'>
<input type="hidden" name="clave_beneficiario" value="<?=$clave_beneficiario?>">

<?echo "<center><b><font size='+1' color='blue'>$accion</font></b></center>";?>
<table width="100%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
     <font size=+1><b>Beneficiario <?=$accion1?></b></font>    
    </td>
 </tr>
  
<tr><td>
  <table width=80% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b> Descripcion del Beneficiario</b>
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
              <input type='text' name='apellido_benef' value='<?=$apellido_benef;?>' size=50 align='right' readonly></b>
            </td>
         
            <td align="right">
         	  <b> Nombre:
         	</td>   
           <td  colspan="2">
             <input type='text' name='nombre_benef' value='<?=$nombre_benef;?>' size=50 align='right' readonly></b>
           </td>
          </tr>
          
          <tr>
           <td align="right">
         	  <b> Documento:
         	</td> 
           <td >
             <input type='text' name='numero_doc' value='<?=$numero_doc;?>' size=20 align='right' readonly></b>
           </td>
           <td align="right">
         	  <b> Fecha de Nacimiento:
         	</td> 
           <td>
             <input type='text' name='fecha_nacimiento_benef' value='<?=Fecha($fecha_nacimiento_benef);?>' size=20 align='right' readonly></b>
           </td>
          </tr>
          
          <tr>
           <td align="right">
         	  <b> Domicilio:
         	</td> 
           <td >
             <input type='text' name='calle' value='<?=$calle;?>' size=50 align='right' readonly></b>
           </td>        
          <td align="right">
         	  <b> Clave Beneficiario:
         	</td>   
           <td >
             <input type='text' name='clave_beneficiario' value='<?=$clave_beneficiario;?>' size=50 align='right' readonly></b>
           </td>
         </tr>
         
        </table>
      </td>      
     </tr>
   </table>     

	
	 <table class="bordes" align="center" width="90%">
		 <tr align="center" id="sub_tabla">
		 	<td colspan="2">	
		 		Nuevo Seguimiento
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
				 			onchange="borrar_buffer();">
							<option value=-1>Seleccione</option>
			                 <?$user_login=substr($_ses_user['login'],0,6);
								  if (es_cuie($_ses_user['login'])){
									$sql1= "select cuie, nombre, com_gestion from nacer.efe_conv where cuie='$user_login' order by nombre";
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
							 $sql1= "select cuie, nombre, com_gestion from nacer.efe_conv order by nombre";
							 $res_efectores=sql($sql1) or fin_pagina();
							 
							 while (!$res_efectores->EOF){ 
								$cuie=$res_efectores->fields['cuie'];
								$nombre_efector=$res_efectores->fields['nombre'];
								?>
								<option value='<?=$cuie;?>'><?=$cuie." - ".$nombre_efector?></option>
								<?
								$res_efectores->movenext();
								}?>
			      			</select>
					    </td>
					 </tr>					 
					 
					 <tr>
					 	<td align="right">
					    	<b>Fecha Seguimiento:</b>	
					    </td>	
					    <td align="left">		    						    	
					    	<?$fecha_comprobante=date("d/m/Y");?>
					    	 <input type=text id=fecha_comprobante name=fecha_comprobante value='<?=$fecha_comprobante;?>' size=15 readonly>
					    	 <?=link_calendario("fecha_comprobante");?>					    	 
					    </td>
					  </tr>
					  <tr>
					    <td align="right">
					    	<b>Fecha Proximo Seguimiento:</b>	
					    </td>	
					    <td align="left">				    						    	
					    	<?$fecha_comprobante_proximo=date("d/m/Y");?>
					    	 <input type=text id=fecha_comprobante_proximo name=fecha_comprobante_proximo value='<?=$fecha_comprobante_proximo;?>' size=15 readonly>
					    	 <?=link_calendario("fecha_comprobante_proximo");?>					    	 
					    </td>			    
					 </tr>

					 <tr>
					    <td align="right">
					    	<b>Profesional:</b>	
					    </td>	
					    <td align="left">				    						    	
					    	<input type="text" value="<?=$profesional;?>" name="profesional" size="20" title="Nombre Profesional"/>				    	 
					    </td>			    
					 </tr>
		
		  
				<tr>
                    <td colspan="5" align="center">                       
                        <table width=100% align="center" class="bordes" style="margin-top:5px">
                           <td align="center" id='mo' colspan="4"><b>Datos Seguimiento Cuatrimestral</b></td>
								<tr>
									<td align="left" style="padding-left:10px">
										<div style="display:inline;">
											<b title="DTM 2">DTM 2</b>
											<input type="checkbox" name="dtm2" title="dtm2" />
											<b title="Hipertension Arterial">HTA</b>
											<input type="checkbox" name="hta" title="Hipertension Arterial"/>
											<b title="Control de la Presion Arterial Sistolica">TA Sist</b>
											<input type="text" value="" name="ta_sist" size="3" style="font-size:9px;" maxlength="3" title="Control de la Presion Arterial Sistolica"/>
											&nbsp;
											<b title="Control de la Presion Arterial Diastolica">TA Diast</b>
											<input type="text" value="" name="ta_diast" size="3" style="font-size:9px;" maxlength="3" title="Control de la Presion Arterial Diastolica"/>
											&nbsp;
											<b>Tabaquismo</b>
											<input type="checkbox" name="tabaquismo"/>
											&nbsp;
											<b title="Control del colesterol Total">Col. Tot.</b>
											<input type="text" value="" name="col_tot" size="6" style="font-size:9px;" maxlength="5" title="Control del colesterol Total"/>mg/dl
											&nbsp;
										</div>
									</td>
								</tr>
								<tr>
									<td align="left" style="padding-left:10px">
										<div style="display:inline;">
											<b title="Control de la Glucosa">Gluc</b>
											<input type="text" value="" name="gluc" size="6" style="font-size:9px;" maxlength="5" title="Control de la Glucosa"/>
											<b title="Control del Peso">Peso</b>
											<input type="text" value="" name="peso" size="6" style="font-size:9px;" maxlength="5" title="Control del Peso"/>
											<b title="Talla">Talla</b>
											<input type="text" value="" name="talla" size="6" style="font-size:9px;" maxlength="5" title="Talla"/>
											&nbsp;
											<b title="IMC">IMC</b>
											<input type="text" value="" name="imc" size="6" style="font-size:9px;" maxlength="5" title="IMC"/>
											&nbsp;
											<b title="HbA1c">HbA1c</b>
											<input type="text" value="" name="hba1c" size="6" style="font-size:9px;" maxlength="5" title="HbA1c"/>
											&nbsp;
											<b title="ECG">ECG</b>
											<input type="checkbox" name="ecg"/>											
											&nbsp;
											<b title="Fondo de Ojo">Fondo de Ojo</b>
											<input type="checkbox" name="fondodeojo"/>
										</div>
									</td>
								</tr>
								<tr>
									<td align="left" style="padding-left:10px">
										<div style="display:inline;">
											<b title="Examen de Pie">Examen de Pie</b>
											<input type="checkbox" name="examendepie" title="Examen de Pie" />
											<b title="Microalbuminuria">Microalbuminuria</b>
											<input type="text" value="" name="microalbuminuria" size="7" style="font-size:9px;" maxlength="10" title="Control de la Microalbuminuria"/>
											<b title="HDL">HDL</b>
											<input type="text" value="" name="hdl" size="6" style="font-size:9px;" maxlength="5" title="Control del HDL"/>
											&nbsp;
											<b title="Control del LDL">LDL</b>
											<input type="text" value="" name="ldl" size="6" style="font-size:9px;" maxlength="5" title="Control del LDL"/>
											&nbsp;
											<b title="Control del TAGs">TAGs</b>
											<input type="text" value="" name="tags" size="6" style="font-size:9px;" maxlength="5" title="Control del TAGs"/>
											&nbsp;
											<b title="Control de Creatininemia">Creatininemia</b>
											<input type="text" value="" name="creatininemia" size="5" style="font-size:9px;" maxlength="5" title="Control de Creatininemia"/>
											&nbsp;
										</div>
									</td>
								</tr>
								<tr>
									<td align="left" style="padding-left:10px">
										<div style="display:inline;">
											<b title="Interconsulta Especialidad">Interconsulta</b>
											1)<input type="text" value="" name="esp1" size="20" style="font-size:9px;" maxlength="20" title="Interconsulta"/>
											&nbsp;
											2)<input type="text" value="" name="esp2" size="20" style="font-size:9px;" maxlength="20" title="Interconsulta"/>
											&nbsp;
											3)<input type="text" value="" name="esp3" size="20" style="font-size:9px;" maxlength="20" title="Interconsulta"/>
											&nbsp;
											4)<input type="text" value="" name="esp4" size="20" style="font-size:9px;" maxlength="20" title="Interconsulta"/>									
										</div>
									</td>
								</tr>
                           </td>
					    </table>
					</td>
				</tr>
				
				<tr>
                    <td colspan="4" align="center">                       
                        <table width=95% align="center" class="bordes" style="margin-top:5px">
                           <td align="center" id='mo' colspan="4"><b>RCGV INICIAL</b></td>
								<tr>
									<td align="left" style="padding-left:10px">
										<div style="display:inline;">
											<input style="margin-left:10px" type="radio" value="bajo" name="riesgo_global" title="Riesgo Bajo" /> Bajo < 10%
											<input style="margin-left:10px" type="radio" value="mode" name="riesgo_global" title="Riesgo Moderado" /> Moderado 10% a < 20% 
											<input style="margin-left:10px" type="radio" value="alto" name="riesgo_global" title="Riesgo Alto" /> Alto 20% a < 30% 
											<input style="margin-left:10px" type="radio" value="malto" name="riesgo_global" title="Riesgo Muy Alto" /> Muy Alto > 30% 
										</div>
									</td>
								</tr>								
                           </td>
					    </table>
					</td>
				</tr>
        	        
        	    <tr>
                    <td colspan="4" align="center">                       
                        <table width=95% align="center" class="bordes" style="margin-top:5px">
                           <td align="center" id='mo' colspan="4"><b>RCGV ACTUAL</b></td>
								<tr>
									<td align="left" style="padding-left:10px">
										<div style="display:inline;">
											<input style="margin-left:10px" type="radio" value="bajoa" name="riesgo_globala" title="Riesgo Bajo" /> Bajo < 10%
											<input style="margin-left:10px" type="radio" value="modea" name="riesgo_globala" title="Riesgo Moderado" /> Moderado 10% a < 20% 
											<input style="margin-left:10px" type="radio" value="altoa" name="riesgo_globala" title="Riesgo Alto" /> Alto 20% a < 30% 
											<input style="margin-left:10px" type="radio" value="maltoa" name="riesgo_globala" title="Riesgo Muy Alto" /> Muy Alto > 30% 
										</div>
									</td>
								</tr>								
                           </td>
					    </table>
					</td>
				</tr>
        
		 <tr>
         	 <td align="right">
         	  	<b>Comentario:</b>
         	 </td>         	
	         <td align='left'>
	            <textarea cols='70' rows='3' name='comentario' ></textarea>
	         </td>
         </tr>   					 
		</td>
	</tr>
</table>
</td></tr>	  

		
			 <tr>
			  	<td align="center" colspan="2" class="bordes">		      
			    	<input type="submit" name="guardar" value="Guardar" title="Guardar" Style="width=230" onclick="return control_nuevos()">
			    </td>
			 </tr> 
			 
	</table>
 </td></tr>
</table>
<?

//tabla de comprobantes
$query="SELECT * FROM trazadoras.seguimiento_remediar WHERE clave_beneficiario='$clave_beneficiario' ORDER BY id_seguimiento_remediar DESC";
$res_comprobante=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
?>
<tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Seguimientos" align="left" style="cursor:pointer;" onclick="muestra_tabla(document.all.prueba_vida,2);" >
	  </td>
	  <td align="center">
	   <b>Seguimientos</b>
	  </td>
	</tr>
</table></td></tr>
<tr><td><table id="prueba_vida" border="1" width="100%" style="display:none;border:thin groove">
	<?if ($res_comprobante->RecordCount()==0){?>
	 <tr>
	  <td align="center">
	   <font size="3" color="Red"><b>No existen Seguimientos para este beneficiario</b></font>
	  </td>
	 </tr>
	 <?}
	 else{	 	
	 	?>
	 	<tr id="sub_tabla">	
	 		<td >Efec</td>
	 		<td >Fecha</td>
	 		<td >Fecha Prox</td>
	 		<td >Edita Fecha</td>
			<td >dtm2</td>
	 		<td >hta</td>
	 		<td >ta_sist</td>
	 		<td >ta_diast</td>
	 		<td >tabaquismo</td>
	 		<td >col_tot</td>
	 		<td >gluc</td>
	 		<td >peso</td>
	 		<td >talla</td>
		    <td >imc </td>
		    <td >hba1c </td>
		    <td >ecg </td>
		    <td >fondodeojo </td>
		    <td >examendepie </td>
	        <td >microalbuminuria </td>
		    <td >hdl </td>
		    <td >ldl </td>
		    <td >tags </td>
	        <td >creatininemia </td>
		    <td >esp1 </td>
		    <td >esp2 </td>
		    <td >esp3 </td>
  	        <td >esp4 </td>
            <td >riesgo_global </td>
            <td >riesgo_globala </td>
            <td >fecha_carga </td>
            <td >usuario</td>
            <td >Comentario</td>
            <td >Profesional</td>
	 		<td >Borrar</td> 
	 	</tr>
	 	<?
	 	$res_comprobante->movefirst();
	 		while (!$res_comprobante->EOF){
	 		$ref1 = encode_link("seguimiento_admin.php",array("id_seguimiento_remediar"=>$res_comprobante->fields['id_seguimiento_remediar'],"clave_beneficiario"=>$clave_beneficiario,"marcar"=>"True"));
            $onclick_marcar="if (confirm('Esta Seguro que Desea Eliminar?')) location.href='$ref1'
            				else return false; ";
							
			$ref_editar = encode_link("modifica_seguimiento.php",array("id_seguimiento_remediar"=>$res_comprobante->fields['id_seguimiento_remediar']));
			$onclick_editar="if (confirm('Esta Seguro que Desea Editar la Fecha del Seguimiento?')) location.href='$ref_editar'
	            						else return false;	";?>
	 		<tr <?=atrib_tr()?>>	
		 		<td><?=$res_comprobante->fields['efector']?></td>		 		
		 		<td><?=fecha($res_comprobante->fields['fecha_comprobante'])?></td>		 		
		 		<td><?=fecha($res_comprobante->fields['fecha_comprobante_proximo'])?></td>		 		
				<td align="center"><img src='../../imagenes/editar1.png' style='cursor:hand;' height="32" width="32" onclick="<?=$onclick_editar?>"></td>
		 		<td><?=$res_comprobante->fields['dtm2']?></td>		 		
		 		<td><?=$res_comprobante->fields['hta']?></td>		 		
		 		<td><?=$res_comprobante->fields['ta_sist']?></td>		 		
		 		<td><?=$res_comprobante->fields['ta_diast']?></td>		 		
		 		<td><?=$res_comprobante->fields['tabaquismo']?></td>		
		 		<td><?=$res_comprobante->fields['col_tot']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['gluc']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['peso']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['talla']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['imc']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['hba1c']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['ecg']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['fondodeojo']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['examendepie']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['microalbuminuria']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['hdl']?></td>	
		 		<td><?=$res_comprobante->fields['ldl']?></td>	
		 		<td><?=$res_comprobante->fields['tags']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['creatininemia']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['esp1']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['esp2']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['esp3']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['esp4']?></td>		 	 		
		 		<td><?=$res_comprobante->fields['riesgo_global']?></td>	
		 		<td><?=$res_comprobante->fields['riesgo_globala']?></td>		 	 		
		 		<td><?=fecha($res_comprobante->fields['fecha_carga'])?></td>		 		
		 		<td><?=$res_comprobante->fields['usuario']?></td>
		 		<td><?=$res_comprobante->fields['comentario']?></td>
		 		<td><?=$res_comprobante->fields['profesional']?></td>
		 		<td align="center"><img src='../../imagenes/salir.gif' style='cursor:pointer;' height="32" width="32" onclick="<?=$onclick_marcar?>"></td>		 				
		 	</tr>	
		 	
	 		<?$res_comprobante->movenext();
	 	 }
	 	}
	 ?>
</table></td></tr>
 
  <tr><td><table width=100% align="center" class="bordes">
	  <tr align="center">
	   <td>
	     <input type=button name="cerrar" value="Cerrar" onclick="window.close()">     
	   </td>
	  </tr>
  </table></td></tr>   
	 	 
</form>
<?=fin_pagina();// aca termino ?>
