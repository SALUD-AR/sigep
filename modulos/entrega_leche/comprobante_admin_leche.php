<?
require_once ("../../config.php");

extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);

if ($cmd=="download") {
		$file=$parametros["file"];
	    $size=$parametros["size"];
	    Mostrar_Header($file,"application/octet-stream",$size);
	    $filefull= MOD_DIR."/entrega_leche/ar_info_social/".$file;
	    readfile($filefull);

	
	
	/*$q_arc="select * from leche.archivo_fichero where id_ar_fic=$id_archivo";
	$res_arc=sql($q_arc,"error al traer datos del archivo") or fin_pagina();
	$size=$res_arc->fields['size'];
	
    Mostrar_Header($res_arc->fields['nombre'],"application/octet-stream",$size);
    $filefull = MOD_DIR."/entrega_leche/ar_info_social/".$res_arc->fields['nombre'];
    readfile($filefull);  
     exit();
     */
}

function bisiesto_local($anio_actual){ 
    $bisiesto=false; 
    //probamos si el mes de febrero del año actual tiene 29 días 
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

	$anos =  $array_actual[0] - $array_nacimiento[0]; // calculamos años 
	$meses = $array_actual[1] - $array_nacimiento[1]; // calculamos meses 
	$dias =  $array_actual[2] - $array_nacimiento[2]; // calculamos días 

	//ajuste de posible negativo en $días 
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
}//de la funcion del calculo de la edad

cargar_calendario();

$remito=$_POST['remito'];
$cuie=$_POST['efector'];

if($marcar=="True"){	
	 $db->StartTrans();
	 $query="delete from leche.detalle_leche
			where id_detalle_leche='$id_detalle_leche'";

     sql($query, "Error al eliminar el comprobante") or fin_pagina();
     $accion="Se Elimino Entrega";    
     $db->CompleteTrans();   
}


if ($_POST['guardar']=="Guardar"){	
	
	$motivo=$_POST['motivo'];
	$sql="select * from leche.motivo where id_motivo='$motivo'";
	$res=sql($sql,"no se puede ejecutar");
	$desc_motivo=$res->fields['desc_motivo'];
	$desc_motivo_res=substr($desc_motivo,0,2);
	if (($desc_motivo_res=='MI')&&($remito=='')){
		$flag_guardar='NO';
		$accion='No se puede Guardar, Debe Ingresar Un Remito';
	}
}


if ($_POST['guardar_form_inicio']=="Guardar Formula Inicio"){	
	
	$form_solicitada=$_POST['formula_inicio'];
	$causas_neonatales=$_POST['causas_neonatales'];
	$causas_maternas=$_POST['causas_maternas'];
	$otras_causas=$_POST['otras_causas'];
	$cantidad_form=$_POST['cantidad_form'];
	$medico=$_POST['medico'];
	$fecha_solicitud=Fecha_db($fecha_solicitud);

	if ($entidad_alta=='na'){

		$id_smiafiliados=$id;
		$id_beneficiarios=0;
		$sql_benef="SELECT * from leche.formula_inicio where id_smiafiliados='$id' and estado='p'";
		$res_sql_benf=sql($sql_benef,"No se pudo ejecutar la consulta en BD") or fin_pagina();
		}
	else {
		$id_beneficiarios=$id;
		$id_smiafiliados=0;
		$sql_benef="SELECT * from leche.formula_inicio where id_beneficiario='$id' and estado='p'";
		$res_sql_benf=sql($sql_benef,"No se pudo ejecutar la consulta en BD") or fin_pagina();

	}

	if ($res_sql_benf->RecordCount()==0) {
	$db->StartTrans();
	$q="SELECT nextval('leche.seq_id_formula') as id_formula";
	$res_id_formula=sql($q) or fin_pagina();
	$id_formula=$res_id_formula->fields['id_formula'];

	$sql="INSERT into leche.formula_inicio (id_formula,
  	id_beneficiario,id_smiafiliados,fecha_solicitud,medico,formula_solicitada,causas_neonatales,causas_maternas,
  	otras_causas,cantidad,cuie,estado) values
  	($id_formula,'$id_beneficiarios','$id_smiafiliados','$fecha_solicitud','$medico','$form_solicitada','$causas_neonatales','$causas_maternas',
  	'$otras_causas',$cantidad_form,'$cuie','p')";

	$res_sql=sql($sql,"No se pudo inserta el registro de formula de inicio") or fin_pagina();
	$db->CompleteTrans();

	$accion1="Se guardo la solicitud de la formula de inicio";
	}

	else $accion1="Ya se encuentra una solicitud pendiente para el mismo beneficiario";
	
}//del guardar formula de inicio

if (($_POST['guardar']=="Guardar")&&($flag_guardar!='NO')){		
	
	$cuie=$_POST['efector'];
	$producto=$_POST['producto'];
	
	$fecha_comprobante=$_POST['fecha_comprobante'];	
	$fecha_comprobante=Fecha_db($fecha_comprobante);
	
	if ($producto==9) {$tipo_producto=2;}
		else {$tipo_producto=1;}
		
	
//coloca el id del periodo
			$periodo_temp=substr($fecha_comprobante,0,4).'/'.substr($fecha_comprobante,5,2);
			$sql="select * from leche.periodo where periodo='$periodo_temp'";
			$res=sql($sql,"no se puede ejecutar");
			$periodo=$res->fields['id_periodo'];
			//$periodo=$_POST['periodo'];
			
			//genero consulta para validar el insert
	if ($entidad_alta=='na'){//$id_smiafiliados
			$query_cos="Select * from leche.detalle_leche
						where id_smiafiliados=$id and id_periodo=$periodo and tipo_producto=$tipo_producto"; //verifico entrega de la leche en el mismo periodo
			
	}else{ //$id_beneficiarios
			$query_cos="Select * from leche.detalle_leche
						where id_beneficiarios=$id and id_periodo='$periodo' and tipo_producto=$tipo_producto"; //verifico entrega de la leche en el mismo periodo
			
	}
  $res_qcos=sql($query_cos,"Error al realizar verificacion de entregas anteriores") or fin_pagina();
	if ($res_qcos->RecordCount()==EOF){	
		
			$motivo=$_POST['motivo'];
			$producto=$_POST['producto'];
			$cantidad=$_POST['cantidad'];	
			$comentario=$_POST['comentario'];
			
			if ($remito=='')$remito=0;
			
			if ($entidad_alta=='na'){
				$id_smiafiliados=$id;
				$id_beneficiarios='0';
				$sql="select id_fichero, fecha_pcontrol from fichero.fichero 
				where id_smiafiliados=$id and fecha_pcontrol_flag=1 order by id_fichero DESC";
				$res_fich=sql($sql,"no se ejecuta");
				$fecha_pcontrol=$res_fich->fields['fecha_pcontrol'];
				if ($fecha_pcontrol<$fecha_comprobante) $accion="La Fecha del Proximo Control del Fichero Cronologico es Menor a la Fecha de Esta Prestacion";
				if ($res_fich->recordcount()==0) $accion="No hay registro en el Fichero Cronologico";		
			}
			else{
				$id_smiafiliados='0';
				$id_beneficiarios=$id;
				$sql="select id_fichero, fecha_pcontrol from fichero.fichero 
				where id_beneficiarios=$id and fecha_pcontrol_flag=1 order by id_fichero DESC";
				$res_fich=sql($sql,"no se ejecuta");
				$fecha_pcontrol=$res_fich->fields['fecha_pcontrol'];
				if ($fecha_pcontrol<$fecha_comprobante) $accion="La Fecha del Proximo Control del Fichero Cronologico es Menor a la Fecha de Esta Prestacion";
				if ($res_fich->recordcount()==0) $accion="No hay registro en el Fichero Cronologico";
			}
				       
		      $db->StartTrans();
				$q="select nextval('leche.detalle_leche_id_detalle_leche_seq') as id_comprobante";
			    $id_comprobante=sql($q) or fin_pagina();
			    $id_comprobante=$id_comprobante->fields['id_comprobante'];	
			   // print_r ($_POST);
	
			    $query="insert into leche.detalle_leche
			             (id_detalle_leche,id_smiafiliados,id_beneficiarios,cuie,cantidad,id_periodo,
		  					id_producto,id_motivo,comentario,fecha,remito,tipo_producto)
			             values
			             ($id_comprobante,'$id_smiafiliados','$id_beneficiarios',
			              '$efector','$cantidad','$periodo','$producto',
			              '$motivo','$comentario','$fecha_comprobante','$remito','$tipo_producto')";	
			    sql($query, "Error al insertar el comprobante") or fin_pagina();	
			    
			        
			    $accion1="Registro Grabado";	    /*cargo los log*/ 
			    	 
			    $db->CompleteTrans();   
	}//fin de recordCount
	else 	$accion1="La persona ya posee una entrega, por favor verifique la informacion";        
}//de if ($_POST['guardar']=="Guardar nuevo Muleto")



if ($_POST['guardar_info']=="Guardar"){	
	$db->StartTrans();	  	
		$fecha_inf=fecha_db($fecha_inf);
		
		//genero consulta para validar el insert
	if ($entidad_alta=='na'){//$id_smiafiliados
			$query_cos="Select * from leche.info_social
						where id_smiafiliados=$id and autorizado='PEND'"; //verifico entrega de la leche en el mismo periodo
			
	}else{ //$id_beneficiarios
			$query_cos="Select * from leche.info_social
						where id_beneficiarios=$id and autorizado='PEND'"; //verifico entrega de la leche en el mismo periodo
			
	}
  $res_qcos=sql($query_cos,"Error al realizar verificacion de entregas anteriores") or fin_pagina();
	if ($res_qcos->RecordCount()==EOF){		

				if ($entidad_alta=='na'){
					$id_beneficiarios=0;
					$id_smiafiliados=$id;
				}else{
					$id_beneficiarios=$id;
					$id_smiafiliados=0;
				}
				$q="select nextval('leche.info_social_id_informe_seq') as id_informe";
				$id_informe=sql($q) or fin_pagina();
				$id_informe=$id_informe->fields['id_informe'];	
						
				$query="insert into leche.info_social
				        (id_informe,id_smiafiliados,id_beneficiarios,cuie,resp_infor,autorizado,fecha_inf,informe)
				        values
				        ($id_informe,'$id_smiafiliados','$id_beneficiarios','$efector_informe','$resp_infor','PEND','$fecha_inf','$informe')";	
				sql($query, "Error al Grabar Informe Social") or fin_pagina();	
			    $accion1="Informe Grabado";	    
			     
		
/*  ----------------------Ingresamos archivo---------------------------------------   */	
		$path = MOD_DIR."/entrega_leche/ar_info_social";
		$name = $_FILES["archivo"]["name"];		
		$temp = $_FILES["archivo"]["tmp_name"];
		$size = $_FILES["archivo"]["size"];
		$type = $_FILES["archivo"]["type"];
		$extensiones = array("gif","jpg","pdf","doc","docx");
		
		if ($name) {
			$name=$id_informe."_".$name;
			$name = strtolower($name);
			$ext = substr($name,-3);
		 	$ret = FileUpload($temp,$size,$name,$type,$max_file_size,$path,"",$extensiones,"",1,0);
			if ($ret["error"] != 0) {
				Error("No se pudo subir el archivo");
			}
		$num=$numero +1;
		$id_user=$_ses_user['id'];
		$fecha_sub=date("Y-m-d");
		//*******************consulto por si ya esta grabado el mismo informe
		
			$qu="select nextval('leche.archivo_fichero_id_ar_fic_seq') as id_ar_fic";
				$id_ar_fic=sql($qu,"error al ejecutar nextval") or fin_pagina();
				$id_ar_fic=$id_ar_fic->fields['id_ar_fic'];	
				
				$query="insert into leche.archivo_fichero
				        (id_ar_fic,id_informe,nombre,id_user,fecha_sub,num,size,tipo)
				        values
				        ($id_ar_fic,'$id_informe','$name',$id_user,'$fecha_sub','1','$size','$type')";
				sql($query, "Error al Grabar Archivo del Social") or fin_pagina();
		
		} 
/*  ----------------------Fin de Ingreso de archivo---------------------------------------   */
$db->CompleteTrans();  
}else $accion="El Informe no puede ser guardado, existe un informe Pendiente de respuesta";
}

if ($cmd=="eliminar") {

    if (!unlink(MOD_DIR."/entrega_leche/ar_info_social/".$file))
         $error="No se encontro el archivo";
    $sql="delete from leche.archivo_fichero where id_ar_fic=$id_ar_fic";
    sql($sql,'No se puede Eliminar el Archivo') or fin_pagina();
    if ($error)
        error($error);
    aviso("El archivo se Elimino correctamente.");
}
if ($entidad_alta=='na'){
	$sql="select *,
			  nacer.smiafiliados.id_smiafiliados as id,
			  nacer.smiafiliados.afiapellido as a,
			  nacer.smiafiliados.afinombre as b,
			  nacer.smiafiliados.afidni as c,
			  nacer.smiafiliados.afifechanac as d,
			  nacer.smiafiliados.afidomlocalidad as e
	     from nacer.smiafiliados	 
		 where id_smiafiliados=$id";

}
else{
	$sql="select *,
			  leche.beneficiarios.id_beneficiarios as id,
			  leche.beneficiarios.apellido as a,
			  leche.beneficiarios.nombre as b,
			  leche.beneficiarios.documento as c,
			  leche.beneficiarios.fecha_nac as d,
			  leche.beneficiarios.domicilio as e
		 from leche.beneficiarios	 
		 where id_beneficiarios=$id";
}
$res_comprobante=sql($sql, "Error al traer los Comprobantes") or fin_pagina();
$id=$res_comprobante->fields['id'];
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

 if(document.all.motivo.value=="-1"){
  alert('Debe Seleccionar un Motivo');
  return false;
 }
 if(document.all.producto.value=="-1"){
  alert('Debe Seleccionar un Producto');
  return false;
 }
 if(document.all.cantidad.value==""){
  alert('Debe Seleccionar una Cantidad');
  return false;
 }
 if (confirm('Esta Seguro que Desea Agregar Comprobante?'))return true;
 else return false;	
}//de function control_nuevos()

//controlan informe Social
function control_informe()
{
 if(document.all.resp_infor.value==""){
  alert('Debe ingresar quien ha Realizado el Informe Social');
  return false;
 }
 if(document.all.informe.value==""){
  alert('Debe ingresar los datos del Informe');
  return false;
 }
 if (confirm('Esta Seguro que Desea Agregar Informe Social?'))return true;
 else return false;	
}//de function control_nuevos()


function control_form_inicio()
{
 if(document.all.formula_inicio.value=="-1"){
  alert('Debe ingresar el tipo de formula de inicio');
  return false;
 }

 if(document.all.causas_neonatales.value=="-1" &&
 	document.all.causas_maternas.value=="-1" &&
 	document.all.otras_causas.value=="-1"){
  alert('Debe ingresar al menos una causa');
  return false;
 }

 if(document.all.medico.value==""){
  alert('Debe ingresar el nombre del medico que solicita');
  return false;
 }
 if (confirm('Esta Seguro que Desea Agregar La solicitud de la Formula de Inicio?'))return true;
 else return false;	
}//de function control_nuevos()

function editar_informe()
{
 document.all.resp_infor.disabled=false;
 document.all.informe.disabled=false;
 document.all.cancelar_editar.disabled=false;
}//de function control_editar()
//controlan que ingresen todos los datos necesarios par el muleto

function seleccionar(chkbox){
	for (var i=0;i < document.forms["form1"].elements.length;i++){
		var elemento = document.forms[0].elements[i];
		if (elemento.type == "checkbox"){
			elemento.checked = chkbox.checked
		}
	}
} 



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

<form name='form1' action='comprobante_admin_leche.php' method='POST' enctype='multipart/form-data'>

<?echo "<center><b><font size='+2' color='red'>$accion1</font></b></center>";?>
<?echo "<center><b><font size='+1' color='blue'>$accion</font></b></center>";?>
<input type="hidden" name="id_informe" value="<?=$id_informe?>">
<input type="hidden" name="id" value="<?=$id?>">
<input type="hidden" name="archivo" value="<?=$archivo?>">
<input type="hidden" name="entidad_alta" value="<?=$entidad_alta?>">
<table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
     <font size=+1><b>Beneficiario <?=$accion1?></b></font>    
    </td>
 </tr>
  
 <tr><td>
 <table width=70% align="center" class="bordes">
	 <tr>
      <td id=mo colspan="3">
       <b> Datos de Obra Social</b>
      </td>
     </tr>
<?$sql_tmp="SELECT puco.documento,puco.nombre,obras_sociales.nombre AS obra_social, obras_sociales.cod_os AS cod_obra_social
				FROM puco.puco
				INNER JOIN puco.obras_sociales ON (puco.puco.cod_os = puco.obras_sociales.cod_os)
				WHERE (puco.puco.documento ='$c')";
				$query1=sql($sql_tmp,"ERROR al realizar la consulta")or fin_pagina();
    			$cod_obra_social=$query1->fields['cod_obra_social'];
    				
    			if(($query1->recordCount()==0)||($cod_obra_social=='997001')){?> 
    				   	<tr id="sub_tabla">   
						     <td align="center" colspan="3"><b>SIN COBERTURA SOCIAL</b></td> 						      
						</tr> 
				<?}
				else{ 
					while (!$query1->EOF) {?>
						    <tr id="sub_tabla">   
						     <td>DNI: <?=$query1->fields['documento']?></td>      
						     <td>Nombre: <?=$query1->fields['nombre']?></td>
						     <td><b>Obra Social: <?=$query1->fields['obra_social']?></b></td> 
						    </tr>    
							<?$query1->MoveNext();
					}//FIN WHILE
					?> <td align="center" colspan="3" bgcolor="RED"><b>EL BENEFICIARIO DEBERA TENER AUTORIZADO EL INFORME SOCIAL PARA RECIBIR COBERTURA DE LECHE</b></td> 	
			  	<?}//fin else?>
</table>
</td></tr>
			  	
 <tr><td>
  <table width=70% align="center" class="bordes">
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
              <input type='text' name='a' value='<?=$a;?>' size=50 align='right' readonly></b>
            </td>
         
            <td align="right">
         	  <b> Nombre:
         	</td>   
           <td  colspan="2">
             <input type='text' name='b' value='<?=$b;?>' size=50 align='right' readonly></b>
           </td>
          </tr>
          <tr>
           <td align="right">
         	  <b> Documento:
         	</td> 
           <td >
             <input type='text' name='c' value='<?=$c;?>' size=20 align='right' readonly></b>
           </td>
           <td align="right">
         	  <b> Fecha de Nacimiento:
         	</td> 
           <td>
             <input type='text' name='d' value='<?=Fecha($d);?>' size=20 align='right' readonly></b>
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
	<?		if ($entidad_alta=='na'){
						$q_cons="SELECT
									leche.info_social.id_informe,
									leche.info_social.informe,
									leche.info_social.fecha_inf,
									leche.info_social.resp_infor,
									nacer.efe_conv.cuie AS cu,
									nacer.efe_conv.nombre AS efector,
									leche.info_social.autorizado,
									leche.info_social.id_user_aut,
									leche.info_social.fecha_aut,
									sistema.usuarios.nombre AS nom_user,
									sistema.usuarios.apellido AS ape_user
									FROM
									leche.info_social
									LEFT OUTER JOIN nacer.efe_conv ON nacer.efe_conv.cuie = leche.info_social.cuie
									LEFT OUTER JOIN sistema.usuarios ON sistema.usuarios.id_usuario = leche.info_social.id_user_aut
									where id_smiafiliados=$id
									order by info_social.id_informe DESC";
				}else{
					$q_cons="SELECT
									leche.info_social.id_informe,
									leche.info_social.informe,
									leche.info_social.fecha_inf,
									leche.info_social.resp_infor,
									nacer.efe_conv.cuie AS cu,
									nacer.efe_conv.nombre AS efector,
									leche.info_social.autorizado,
									leche.info_social.id_user_aut,
									leche.info_social.fecha_aut,
									sistema.usuarios.nombre AS nom_user,
									sistema.usuarios.apellido AS ape_user
									FROM
									leche.info_social
							LEFT OUTER JOIN nacer.efe_conv ON nacer.efe_conv.cuie = leche.info_social.cuie
							LEFT OUTER JOIN sistema.usuarios ON sistema.usuarios.id_usuario = leche.info_social.id_user_aut
							 where id_beneficiarios=$id
							 order by info_social.id_informe DESC";
				}
				$resp_dos=sql($q_cons,"error en consulta 2")or fin_pagina();
				 $estado= trim($resp_dos->fields['autorizado']);//Estado de informe	 

	if( ($query1->RecordCount()==0) || ($estado=='SI') || ($cod_obra_social=='997001') ){?> 
	
	 <table class="bordes" align="center" width="70%">
		 <tr align="center" id="sub_tabla">
		 	<td colspan="2">	
		 		Nuevo Comprobante
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
				 			onchange="borrar_buffer();"
           					>
			                 <?$user_login=substr($_ses_user['login'],0,6);
								  if (es_cuie($_ses_user['login'])){
									$sql1= "select cuie, nombre, com_gestion from nacer.efe_conv where cuie='$user_login'";
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
					    	<b>Fecha Prestación:</b>
					    </td>
					    <td align="left">
					    						    	
					    	<?$fecha_comprobante=date("d/m/Y");?>
					    	 <input type=text id=fecha_comprobante name=fecha_comprobante value='<?=$fecha_comprobante;?>' size=15 readonly>
					    	 <?=link_calendario("fecha_comprobante");?>					    	 
					    </td>		    
					 </tr>
				        
         <tr>
         <td align="right">
				<b>Tipo de Leche:</b>
			</td>
			<td align="left">		          			
			 <select name=motivo Style="width=450px" >
			 <option value=-1>Seleccione</option>
			  <?
			  $sql = "select * from leche.motivo where (activo is NULL or activo='1') order by desc_motivo";
			  $result=sql($sql,"No se puede traer el periodo");
			  while (!$result->EOF) {?>
			  			  
			  <option value=<?=$result->fields['id_motivo']?> ><?=$result->fields['desc_motivo']?></option>
			  <?
			  $result->movenext();
			  }
			  ?>			
			  </select>
			</td>
         </tr>
         
         <tr>
         <td align="right">
				<b>Descripcion de Producto:</b>
			</td>
			<td align="left">		          			
			 <select name=producto Style="width=450px" >
			 <option value=-1>Seleccione</option>
			  <?
			  $sql = "select * from leche.producto where (activo is NULL or activo='1') order by desc_producto";
			  $result=sql($sql,"No se puede traer el periodo");
			  while (!$result->EOF) {?>
			  			  
			  <option value=<?=$result->fields['id_producto']?> ><?=$result->fields['desc_producto']?></option>
			  <?
			  $result->movenext();
			  }
			  ?>			
			  </select>
			</td>			
         </tr>
         
         <tr>
         	<td align="right">
         	  <b>Cantidad:
         	</td>         	
            <td align='left'>
              <input type='text' name='cantidad' value='1' size=8 align='right'></b>
            </td>
         </tr>
         
         <tr>
         	<td align="right">
         	  <b>Remito:
         	</td>         	
            <td align='left'>
              <input type='text' name='remito' value='<?=$remito?>' size=8 align='right'></b>
              <b><font color=red>Obligatorio para el Plan Materno Infantil</font></b>
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
</td></tr>	  <? }//$query->RecordCount()==EOF?>

		<? if(($query1->RecordCount()==0) || ($estado=='SI') || ($cod_obra_social=='997001') ){?> 
			 <tr>
			  	<td align="center" colspan="2" class="bordes">		      
			    	<input type="submit" name="guardar" value="Guardar" title="Guardar" Style="width=230" onclick="return control_nuevos()">
			    </td>
			 </tr> 
	 <? }?>
	</table>
</table>
 </td></tr>

<?

//tabla de comprobantes
$query="SELECT 
  nacer.efe_conv.nombre,
  leche.periodo.periodo,
  leche.motivo.desc_motivo,
  leche.producto.desc_producto,
  leche.detalle_leche.cantidad,
  leche.detalle_leche.fecha,
  leche.detalle_leche.comentario,
  leche.detalle_leche.id_detalle_leche,
  leche.detalle_leche.remito
FROM
  leche.detalle_leche
  INNER JOIN nacer.efe_conv ON (leche.detalle_leche.cuie = nacer.efe_conv.cuie)
  INNER JOIN leche.periodo ON (leche.detalle_leche.id_periodo = leche.periodo.id_periodo)
  INNER JOIN leche.producto ON (leche.detalle_leche.id_producto = leche.producto.id_producto)
  INNER JOIN leche.motivo ON (leche.detalle_leche.id_motivo = leche.motivo.id_motivo)";

if ($entidad_alta=='na')
$query.=" where detalle_leche.id_smiafiliados=$id
			order by leche.detalle_leche.fecha DESC";
else
$query.=" where detalle_leche.id_beneficiarios=$id
			order by leche.detalle_leche.fecha DESC";

$res_comprobante=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
?>
<tr><td><table width="95%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Comprobantes" align="left" style="cursor:pointer;" onclick="muestra_tabla(document.all.prueba_vida,2);" >
	  </td>
	  <td align="center">
	   <b>Comprobantes</b>
	  </td>
	</tr>
</table></td></tr>
<tr align="center"><td><table id="prueba_vida" border="1" width="95%" style="display:none;border:thin groove">
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
	 		<td >Efector</td>
	 		<td >Periodo</td>
	 		<td >Motivo</td>
	 		<td >Producto</td>
	 		<td >Cantidad</td>
	 		<td >Fecha</td>
	 		<td >Remito</td>
	 		<td >Comentario</td>
	 		<td >Borrar</td>	 		
	 	</tr>
	 	<?
	 	$res_comprobante->movefirst();
	 		while (!$res_comprobante->EOF){
	 		$ref1 = encode_link("comprobante_admin_leche.php",array("id_detalle_leche"=>$res_comprobante->fields['id_detalle_leche'],"marcar"=>"True",'id'=>$id,'entidad_alta'=>$entidad_alta));            		
            $onclick_marcar="if (confirm('Esta Seguro que Desea Eliminar?')) location.href='$ref1'
            				else return false; ";?>
	 		<tr <?=atrib_tr()?>>	 			
		 		<td><?=$res_comprobante->fields['nombre']?></td>		 		
		 		<td><?=$res_comprobante->fields['periodo']?></td>		 		
		 		<td><?=$res_comprobante->fields['desc_motivo']?></td>		 		
		 		<td><?=$res_comprobante->fields['desc_producto']?></td>		 		
		 		<td><?=$res_comprobante->fields['cantidad']?></td>		 	 		
		 		<td><?=fecha($res_comprobante->fields['fecha'])?></td>		 		
		 		<td><?=$res_comprobante->fields['remito']?></td>		 		
		 		<td><?=$res_comprobante->fields['comentario']?></td>
		 		<td onclick="<?=$onclick_marcar?>" align="center"><img src='../../imagenes/salir.gif' style='cursor:pointer;'></td>		 				
		 	</tr>	
		 	
	 		<?$res_comprobante->movenext();
	 	 }
	 	}
	 ?>
</table></td></tr>
&nbsp&nbsp

<tr><td><table border="1" width="95%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Formulas Especiales" align="left" style="cursor:pointer;" onclick="muestra_tabla(document.all.formula_especiales,2);" >
	  </td>
	  <td align="center">
	   <b>Formulas Especiales</b>
	  </td>
	</tr>
</table></td></tr>

<tr align="center"><td><table id="formula_especiales" border="1" width="100%" style="display:none;border:thin groove">
<tr id="sub_tabla"><td class="bordes" width="100%" align ="center">
<table colspan=4 cellpadding="10">
	<br>
	<tr>
	<td align="right" colspan="1" rowspan="1">
				<b>Formula Solicitada:</b>
</td>
<td align="left" colspan="1" rowspan="1">		          			
<select name="formula_inicio" motivo Style="width=350px" >
	<option value="-1">Seleccione</option>
	<?$edad=edad_con_meses($d);?>
	<option value="Formula Inicio" <?if ($edad['anos']>0 or ($edad['anos']<=0 and $edad['meses']>6)) echo "disabled"?>>Formula Inicio</option>
	<option value="Formula Reducida en Lactosa">Formula Reducida en Lactosa</option>
	</select>

<td align="right" colspan="1" rowspan="1">
				<b>Causas Neonatales:</b>
</td>
<td align="left" colspan="1" rowspan="1">		          			
<select name="causas_neonatales" motivo Style="width=350px" >
	<option value="-1">Seleccione</option>
	<option value="Galactosemia">Galactosemia</option>
	<option value="Prematures (<1500 grs / <32 Sem)">Prematures (<1500 grs / <32 Sem)</option>
	<option value="Alteraciones Neurologicas">Alteraciones Neurologicas</option>
	<option value="Fenilcitonuria">Fenilcitonuria</option>
	<option value="Cardiopatias Congenitas">Cardiopatias Congenitas</option>
	<option value="Intolerancia a la Lactosa">Intolerancia a la Lactosa</option>
	<option value="Lesion Temporal de Epitelio Intestinal">Lesion Temporal de Epitelio Intestinal</option>
</select>
</tr>
<br>
<tr>
<td align="right" colspan="1" rowspan="1">
				<b>Causas Maternas:</b>
</td>
<td align="left" colspan="1" rowspan="1">		          			
<select name="causas_maternas" motivo Style="width=300px" >
	<option value="-1">Seleccione</option>
	<option value="Infecciones HIV">Infecciones HIV</option>
	<option value="Tuberculosis Activa no Tratada">Tuberculosis Activa no Tratada</option>
	<option value="Medicamentos que Interfieren Lactancia">Medicamentos que Interfieren Lactancia</option>
	<option value="Herpes Simple o Zoster">Herpes Simple o Zoster</option>
	<option value="Patologias que Comprometen Estado General">Patologias que Comprometen Estado General</option>
</select>

<td align="right" colspan="1" rowspan="1">
				<b>Otras Causas:</b>
</td>
<td align="left" colspan="1" rowspan="1">		          			
<select name="otras_causas" motivo Style="width=300px" >
	<option value="-1">Seleccione</option>
	<option value="Adopcion">Adopcion</option>
	<option value="Razon Social">Razon Social</option>
</select>
</tr>

<tr>
	<td align="right" colspan="1" rowspan="1">
		<b>Cantidad Solicitada:</b>
	</td>
	<td align="left">
	<input type="radio" name="cantidad_form" value="1" checked><b>1</b>
	<input type="radio" name="cantidad_form" value="2"><b>2</b>
	<input type="radio" name="cantidad_form" value="3"><b>3</b>
	<input type="radio" name="cantidad_form" value="4"><b>4</b>
	</td>
</tr>

<tr>
	<td align="right" colspan="1" rowspan="1">
		<b>Medico que Prescribe:</b>
	</td>
	<td align="left">
	<input type="text" name="medico" value="<?=$medico?>">
	</td>

	<td align="right" colspan="1" rowspan="1">
		<b>Fecha de Solicitud:</b>
	</td>
	<td align="left"><?$fecha_solicitud=date("d/m/Y");?>
	<input type=text id="fecha_solicitud"  name="fecha_solicitud"  value='<?=$fecha_solicitud;?>' size=15 >
	<?=link_calendario("fecha_solicitud");?>					    	 
	</td>

</tr>
&nbsp&nbsp
<table border="0">
&nbsp&nbsp
<tr><td align="center">

<? $id_trz_pres=0;
   if ($edad['anos']<1) $id_trz_pres=47;
   if ($edad['anos']>=1 && $dad['anos']<=9) $id_trz_pres=48;
   if ($edad['anos']>9 && $edad['anos']<=19) $id_trz_pres=54;

   if ($id_trz_pres) {
   		$obliga="disabled";
   		$sql_trz_pres="SELECT * from nomenclador.trz_pres where id_trz_pres='$id_trz_pres'";
   		$result_trz=sql($sql_trz_pres,"no se puede ejecutar consulta sobre trz_pres");	
   		
   		$texto_boton=$result_trz->fields['texto_boton'];
		$trz_vincula=$result_trz->fields['trz_vincula'];
		$pagina_destino=$result_trz->fields['pagina_destino'];
			    		
		$ref=$ref = encode_link("../trazadorassps/$pagina_destino",array("fecha_comprobante"=>$fecha_comprobante,"id_smiafiliados"=>$id,"cuie"=>$cuie,"pagina_viene"=>"comprobante_admin_leche.php","entidad_alta"=>$entidad_alta));
  	    }?>

   


<input type=button name="carga_trazadora" value="<?php echo $texto_boton?>" Style="width=300px;height=40px;background-color:#F781F3"
  	    		onclick="document.all.guardar_form_inicio.disabled=false; 
	  	    			 window.open('<?=$ref?>','Trazadoras','dependent:yes,width=900,height=700,top=1,left=60,scrollbars=yes');"
	  	    			 title="<?php echo $trz_vincula?>">

<input type="submit" name="guardar_form_inicio" value="Guardar Formula Inicio" title="Guardar Formula Inicia" 
 onclick="return control_form_inicio()" <?=$obliga?> >
		    	
</td></tr>
</table>





</table>
</table></td></tr>

&nbsp&nbsp
 
<tr><td><table width="95%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Comprobantes" align="left" style="cursor:pointer;" onclick="muestra_tabla(document.all.prueba_vida2,2);" >
	  </td>
	  <td align="center">
	   <b>Informe Social</b>
	  </td>
	</tr>
</table></td></tr>
<tr><td><table id="prueba_vida2" border="1" width="95%" style="display:none;border:thin groove">		
		<tr id="sub_tabla"><td class="bordes" width="90%">
			<table >
				<tr><td>
				<table align="center" width="100%">
						 <tr>
						    <td align="right">
						    	<b>Efector Solicitante:</b>
						    </td>
						    <td align="left">		          			
					 			<select name=efector_informe Style="width=450px"
					 			onKeypress="buscar_combo(this);"
					 			onblur="borrar_buffer();"
					 			onchange="borrar_buffer();"
	           					>
				                 <?$user_login2=substr($_ses_user['login'],0,6);
									  if (es_cuie($_ses_user['login'])){
										$sql1= "select cuie, nombre, com_gestion from nacer.efe_conv where cuie='$user_login2'";
									   }									
									  else{
										$usuario2=$_ses_user['id'];
										$sql1= "select nacer.efe_conv.nombre, nacer.efe_conv.cuie, com_gestion 
												from nacer.efe_conv 
												join sistema.usu_efec on (nacer.efe_conv.cuie = sistema.usu_efec.cuie) 
												join sistema.usuarios on (sistema.usu_efec.id_usuario = sistema.usuarios.id_usuario) 
												where sistema.usuarios.id_usuario = '$usuario2'
											 order by nombre";
									   }			 			   
									 $res_efec=sql($sql1) or fin_pagina();
								 
								 while (!$res_efec->EOF){ 
									$com_gestion1=$res_efec->fields['com_gestion'];
									$cuie1=$res_efec->fields['cuie'];
									$nombre_efector1=$res_efec->fields['nombre'];
									if($com_gestion1=='FALSO')$color_style='#F78181'; else $color_style='';
									?>
									<option value='<?=$cuie1;?>' <?if($cuie1==$cuie_inf) echo "selected" ?> Style="background-color: <?=$color_style;?>"><?=$cuie1." - ".$nombre_efector1?></option>
									<?
									$res_efec->movenext();
									}?>
				      			</select>
						    </td>
						</tr>
						<tr>
							<td align="right" title="Nombre de la persona que realizo el Informe Social">
								<b>Expedido por:</b>
							</td>
							  <td align="left">  						    	
									<input type='text' name='resp_infor' value='<?=$resp_infor?>' size=50 align='right' <? if($id_informe) echo "disabled"?>></b>				    	 
							</td>	
						</tr>
						<tr>
							<td align="right" title="Fecha de Generacion del Informe" >
								<b>Fecha:</b>
							</td>
							  <td align="left">  						    	
									<?$fecha_inf=date("d/m/Y");?>
									<input type=text id=fecha_inf  name=fecha_inf  value='<?=$fecha_inf;?>' size=15 >
									 <?=link_calendario("fecha_inf");?>					    	 
							  </td>	
							  </tr>
						<tr>
							<td>
								<b>Detalle de Informe:</b>
				         	</td>         	
				            <td align='left'>
				              	<textarea cols='90' rows='5' name='informe' <? if($id_informe) echo "disabled"?>><?=$informe?> </textarea>
				            </td>
				        </tr>
					
					</table>
					</td></tr>	
					
					<tr><td>
						<table align="center" width="100%" >
								<tr>
							       <td align="right">
										<b>Cargar Archivo:</b>
								   </td>
								   <td align="left">
											  <input type=file name=archivo style="width=350px">
								   </td> 
						    	</tr>
						</table>
				</td></tr>	 
			<? 	
				if ($entidad_alta=='na'){
						$q_cons="SELECT
									leche.info_social.id_informe,
									leche.info_social.informe,
									leche.info_social.fecha_inf,
									leche.info_social.resp_infor,
									nacer.efe_conv.cuie AS cu,
									nacer.efe_conv.nombre AS efector,
									leche.info_social.autorizado,
									leche.info_social.id_user_aut,
									leche.info_social.fecha_aut,
									sistema.usuarios.nombre AS nom_user,
									sistema.usuarios.apellido AS ape_user
									FROM
									leche.info_social
									LEFT OUTER JOIN nacer.efe_conv ON nacer.efe_conv.cuie = leche.info_social.cuie
									LEFT OUTER JOIN sistema.usuarios ON sistema.usuarios.id_usuario = leche.info_social.id_user_aut
									where id_smiafiliados=$id
									order by info_social.id_informe DESC";
				}else{
					$q_cons="SELECT
									leche.info_social.id_informe,
									leche.info_social.informe,
									leche.info_social.fecha_inf,
									leche.info_social.resp_infor,
									nacer.efe_conv.cuie AS cu,
									nacer.efe_conv.nombre AS efector,
									leche.info_social.autorizado,
									leche.info_social.id_user_aut,
									leche.info_social.fecha_aut,
									sistema.usuarios.nombre AS nom_user,
									sistema.usuarios.apellido AS ape_user
									FROM
									leche.info_social
							LEFT OUTER JOIN nacer.efe_conv ON nacer.efe_conv.cuie = leche.info_social.cuie
							LEFT OUTER JOIN sistema.usuarios ON sistema.usuarios.id_usuario = leche.info_social.id_user_aut
							 where id_beneficiarios=$id
							 order by info_social.id_informe DESC";
				}	

				$resp_dos=sql($q_cons,"error en consulta 2")or fin_pagina();
				 //subir archivos 	?>	
		<tr><td><table align="center" width="100%" class="bordes">
			 	<tr>
			  		<td align="center" colspan="2">		      
				    	<input type="submit" name="guardar_info" value="Guardar" title="Guardar Informe Social" Style="width=130px" onclick="return control_info()">
				    	<?if ($id_informe){?>
				    	<input type="button" name="editar" value="Editar" title="Editar Informe Social" Style="width=130px" onclick="editar_informe()">
				    	<input type="button" name="cancelar_editar" value="Cancelar" title="Cancela Edicion" disabled style="width=130px" onclick="document.location.reload()">	
				    	<?}?>	      
			    	</td>
			 	</tr> 
			</table></td></tr>					 
				 
				 
<tr><td><table width="90%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Ver Informes Sociales registrados" align="left" style="cursor:pointer;" onclick="muestra_tabla(document.all.prueba_vida3,2);" >
	  </td>
	  <td align="center">
	   <b>Informes Relacionados</b>
	  </td>
	</tr>
</table></td></tr>
<tr><td><table id="prueba_vida3" border="1" width="90%" style="display:none;border:thin groove">		
		<tr id="sub_tabla"><td class="bordes" width="90%">
			<table><tr><td>
	         <?/*----------tercero datos---------------*/
		        if($resp_dos->RecordCount()!=0){
		        	?>
			 		<tr>
					  	<td colspan=12 align=left id=ma>
						     <table width=100%>
							      <tr id=ma>
								       <td width=30% align=left><b>Total:</b> <?=$resp_dos->RecordCount()?></td>       
								       <td width=40% align=right><?=$link_pagina?></td>
							      </tr>
						    </table>
					   </td>
					 </tr>
			 
				  <tr> 
 					<td id=mo><a id=mo>&nbsp</a></td> 
					<td id=mo><a id=mo>Estado</a></td> 
					<td id=mo><a id=mo>Responsable Conclusion I.S.</a></td>
					<td id=mo><a id=mo>Fecha Conclusion</a></td>
					<td align=right id=mo>Efector Solicitante</td>
					<td align=right id=mo>Fecha de I.S.</td>
					<td align=right id=mo width="40%">Informe</td>
					<td align=right id=mo >Responsable de I.S.</td>
					<td align=right id=mo title="Cargar Nuevo Archivo">Cargar</td>
	  			</tr>
 				<? while (!$resp_dos->EOF) {
 					
		        			$id_informe=$resp_dos->fields['id_informe'];
							$informe=$resp_dos->fields['informe'];
							$fecha_inf=$resp_dos->fields['fecha_inf'];//fecha de realizacion del informe social
							$resp_infor=$resp_dos->fields['resp_infor'];// responsable de gestion del informe social	
							$efector=$resp_dos->fields['cu'].' - '.$resp_dos->fields['efector'];// cuie del efector solcitante
							$estado= trim($resp_dos->fields['autorizado']);//Estado de informe							
							$user_aut= $resp_dos->fields['nom_user'].', '.$resp_dos->fields['ape_user'] ;// persona que realiza conclusionde informe
							$fecha_aut= $resp_dos->fields['fecha_aut'];// fecha de autorizacion

					 	$ref2= encode_link("comprobante_nuevo_archivo.php",array("id_informe"=>$resp_dos->fields['id_informe'],'id'=>$id,'entidad_alta'=>$entidad_alta));            		
            			//$conf="if (confirm('Esta a punto de agregar un nuevo archivo, Confirma?')) location.href='$ref2'; else return false;	";
					 	$id_tabla="tabla_".$resp_dos->fields['id_informe'];	
					 	$onclick_check_ver=" javascript:(this.checked)?Mostrar('$id_tabla'):Ocultar('$id_tabla')";?>
					    <tr <?=atrib_tr()?>> 
					    	<td><input type=checkbox name=check_ver value="" onclick="<?=$onclick_check_ver?>" class="estilos_check"></td>	
					     	<td align=center><?if($user_aut=='') echo " &nbsp"; elseif($estado=='PEND')echo "PENDIENTE"; elseif($estado=='SI')echo "AUTORIZADO";else echo "RECHAZADO"; ?></td>
						    <td align=center><?if($user_aut=='') echo " &nbsp"; else echo $user_aut?></td>
						    <td align=center><?if($fecha_aut=='1000-01-01') echo " &nbsp"; else echo fecha($fecha_aut)?></td>  
							<td align=center><?=$efector?></td>
							<td align=center><?=fecha($fecha_inf)?></td>
							<td align=center><?=$informe?></td>
							<td align=center><?=$resp_infor?></td> 
							<?if($estado=="PEND"){?>
							<td onclick="window.open('<?=$ref2?>','','toolbar=0,location=0,directories=0,status=0, menubar=0,scrollbars=1')"><img src='../../imagenes/tree/book_titel.gif' style='cursor:pointer;'></td>		 				
 							<?}else echo " &nbsp";?>	
						</tr>
     
    <?/*****************************************************************************************/?>
    <tr>
		<td colspan=6>
			<?   $sql1="SELECT
										leche.archivo_fichero.id_ar_fic,
										leche.archivo_fichero.id_informe,
										leche.archivo_fichero.nombre,
										leche.archivo_fichero.size,
										leche.archivo_fichero.id_user,
										leche.archivo_fichero.fecha_sub,
										leche.archivo_fichero.num,
										sistema.usuarios.nombre as n,
										sistema.usuarios.apellido as a
										FROM
										leche.archivo_fichero
										INNER JOIN sistema.usuarios ON sistema.usuarios.id_usuario = leche.archivo_fichero.id_user
										WHERE
										leche.archivo_fichero.id_informe = $id_informe
										ORDER BY leche.archivo_fichero.id_ar_fic DESC";										
									   $result_items1=sql($sql1) or fin_pagina();
				                  ?>
				                  <div id=<?=$id_tabla?> style='display:none'>
				                 	<table width=90% align=center border="1" class=bordes>
					                          <tr>
					                           	  
					                           	  <td align=right><a id=mo >Archivo</a></td> 
					                           		<td align=right><a id=mo >Usuario</a></td>  
												    <td align=right><a id=mo>Fecha</a></td>  
												    <td align=right><a id=mo>Eliminar</a></td> 
					                            </tr>
										     		<?while (!$result_items1->EOF){
										     		$ref3 = encode_link("comprobante_admin_leche.php",array ("id_ar_fic"=>$result_items1->fields['id_ar_fic'],"file" =>$result_items1->fields["nombre"],"size" =>$result_items1->fields["size"],"cmd" => "eliminar", 'id'=>$id,'entidad_alta'=>$entidad_alta));            		
										            $onclick_e="if (confirm('Esta Seguro que Desea Eliminar?')) location.href='$ref3'
										            				else return false; ";
											       $id_ar_fic=$result_items1->fields['id_ar_fic'];	
													?>								
												  <tr <?=atrib_tr()?>>
												   <? if (is_file(MOD_DIR."/entrega_leche/ar_info_social/".$result_items1->fields["nombre"]))?>
										        		<td ><a href='<?=encode_link("comprobante_admin_leche.php",array ("file" =>$result_items1->fields["nombre"],"size" =>$result_items1->fields["size"],"cmd" => "download", 'id'=>$id,'entidad_alta'=>$entidad_alta))?>'>
										    			<?=$result_items1->fields["nombre"]?></a> </td >
												   		
												 		<td ><?=$result_items1->fields['n'].' '.$result_items1->fields['a']?></td>
												 		<td ><?=fecha($result_items1->fields['fecha_sub'])?></td>	
												 		<?if($estado=="PEND"){?>	
												 		<td onclick="<?=$onclick_e?>" align="center"><img src='../../imagenes/salir.gif' style='cursor:pointer;'></td>		
												 			<?}else echo " &nbsp";?>							 														 								                            	 		
						                            </tr>
					                            	<?$result_items1->movenext();
					                            }//del while ?> 	                            
				               			</table>
				               		</div>
						         </td>
						      </tr>
    <?
    /******************************************************************************************/
				$resp_dos->MoveNext();
			    }
    			}else	{	?>
				<tr>
				  	<td align="center">
				   		<font size="3" color="Red"><b>No existe ningun Informe Social</b></font>
				  	</td>
				 </tr>
				<? }	?>
			</td></tr></table>		 
		</tr><!-- cierra tr de linea 826 id_sub_tabla-->
</table></td></tr><!-- cierra cierra prueba_vida3 linea 825-->


</table></td></tr>
</table></td></tr>

  <tr><td><table width=100% align="center" class="bordes">
	  <tr align="center">
	   <td>
	     <input type=button name="volver" value="Volver" onclick="document.location='listado_beneficiarios_leche.php'"title="Volver al Listado" style="width=150px">     
	   </td>
	  </tr>
  </table></td></tr>   
	 	 
</form>
<?=fin_pagina();// aca termino ?>
