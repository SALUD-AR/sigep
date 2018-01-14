<?

require_once ("../../config.php");


extract($_POST,EXTR_SKIP);
if ($parametros) extract($parametros,EXTR_OVERWRITE);
cargar_calendario();
$usuario=$_ses_user['name'];

function suma_fechas($fecha,$ndias){
      if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha))
      	list($dia,$mes,$anio)=split("/", $fecha);
      if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha))
        list($dia,$mes,$anio)=split("-",$fecha);
      $nueva = mktime(0,0,0, $mes,$dia,$anio) + $ndias * 24 * 60 * 60;
      $nuevafecha=date("d-m-Y",$nueva);
      return ($nuevafecha); }

if($desvincular=="True"){
	 $db->StartTrans();
	$query="update facturacion.comprobante set
             id_factura=NULL
             where id_comprobante=$id_comprobante";

    sql($query, "Error al desvincular el comprobante") or fin_pagina();
    $accion="Se desvinculo el Comprobante Numero: $id_comprobante";    
    /*cargo los log*/ 
    $usuario=$_ses_user['name'];
    $fecha_carga=date("Y-m-d H:i:s");
	$log="insert into facturacion.log_comprobante 
		   (id_comprobante, fecha, tipo, descripcion, usuario) 
	values ($id_comprobante, '$fecha_carga','Comprobante Desvinculado de Factura $id_factura','Nro. Comprobante $id_comprobante', '$usuario')";
	sql($log) or fin_pagina();
	$log="insert into facturacion.log_factura
		   (id_factura, fecha, tipo, descripcion, usuario) 
	values ($id_factura, '$fecha_carga','Comprobante Desvinculado de Factura $id_factura','Nro. Comprobante $id_comprobante', '$usuario')";
	sql($log) or fin_pagina();
	 
    $db->CompleteTrans();   
}
if ($_POST['desvincular_seleccion']=="Desvincular Seleccion"){
	$tamaño1 = sizeof($_POST[check_prestacion]);
	for ($i=0;$i<$tamaño1;$i++){		
		$comprobante_total.=$_POST[check_prestacion][$i]." ";
		$id_comprobante=$_POST[check_prestacion][$i];
		$db->StartTrans();
		$query="update facturacion.comprobante set
				 id_factura=NULL
				 where id_comprobante=$id_comprobante";

		sql($query, "Error al desvincular el comprobante") or fin_pagina();		   
		/*cargo los log*/ 
		$usuario=$_ses_user['name'];
		$fecha_carga=date("Y-m-d H:i:s");
		$log="insert into facturacion.log_comprobante 
			   (id_comprobante, fecha, tipo, descripcion, usuario) 
		values ($id_comprobante, '$fecha_carga','Comprobante Desvinculado de Factura $id_factura (por Desvincular Seleccion)','Nro. Comprobante $id_comprobante', '$usuario')";
		sql($log) or fin_pagina();
		$log="insert into facturacion.log_factura
			   (id_factura, fecha, tipo, descripcion, usuario) 
		values ($id_factura, '$fecha_carga','Comprobante Desvinculado de Factura $id_factura (por Desvincular Seleccion)','Nro. Comprobante $id_comprobante', '$usuario')";
		sql($log) or fin_pagina();
		 
		$db->CompleteTrans(); 
	}	
	$accion="Se desvincularon el/los Comprobantes Numeros: $comprobante_total";
}

if ($_POST['cierra_factura']=="Cierra Factura"){
   $fecha_carga=date("Y-m-d");
   $db->StartTrans();
   
   $query_1="SELECT sum 
			(facturacion.prestacion.cantidad*facturacion.prestacion.precio_prestacion) as total
			FROM
			  facturacion.factura
			  INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
			  INNER JOIN facturacion.prestacion ON (facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante)
			  INNER JOIN facturacion.nomenclador ON (facturacion.prestacion.id_nomenclador = facturacion.nomenclador.id_nomenclador)
			  INNER JOIN nacer.smiafiliados ON (facturacion.comprobante.id_smiafiliados = nacer.smiafiliados.id_smiafiliados)
			  INNER JOIN facturacion.smiefectores ON (facturacion.comprobante.cuie = facturacion.smiefectores.cuie)
			  where factura.id_factura=$id_factura";
   $monto_prefactura_1=sql($query_1,('No se puede ejecutar la consulta'));
   $monto_prefactura_1=$monto_prefactura_1->fields['total'];
   ($monto_prefactura_1=='')?$monto_prefactura_1=0:$monto_prefactura_1=$monto_prefactura_1;
   
   $query="update facturacion.factura set 
   					estado='C', 
   					monto_prefactura='$monto_prefactura_1'
   					where id_factura=$id_factura";
   sql($query, "Error al cerrar la factura") or fin_pagina();
   
   
   
   /*cargo los log*/ 
    $usuario=$_ses_user['name'];
	$log="insert into facturacion.log_factura
		   (id_factura, fecha, tipo, descripcion, usuario) 
	values ($id_factura, '$fecha_carga','Cerrar Factura','Cierra la Factura', '$usuario')";
	
	sql($log) or fin_pagina();

//codigo para la insercion de la factura al sistema de expediente


	$q="select nextval('expediente.expediente_id_expediente_seq') as id_expediente";
    $id_expediente=sql($q) or fin_pagina();
    $id_expediente=$id_expediente->fields['id_expediente'];
    
    $q_trans="select nextval('expediente.transaccion_id_transac_seq') as id_transac";
    $id_transac=sql($q_trans) or fin_pagina();
    $id_transac=$id_transac->fields['id_transac'];
    
    $id_area=3;
    $estado='V';
    $comentario="Expediente en fase de revision - Generado desde cierre de Factura";
   
   $mes=date("m");
   $anio=date("Y");

   $dias=50;
   $plazo_para_pago=date("Y-m-d", strtotime ("$fecha_carga +$dias days"));
   
   //$fecha_informe=Fecha_db($fecha_informe);
   
   $sql_efector="select id_efe_conv,cuie from nacer.efe_conv where cuie='$cuie'";
   $res_efector=sql($sql_efector,"no se pudo traer los datos del efector") or fin_pagina();
   $id_efector_real=$res_efector->fields['id_efe_conv'];
   //$cuie_nuevo=devuelve_cuie($id_efector_real);  
     
   $sql_periodo="select * from facturacion.factura where id_factura='$id_factura'";
   $res_periodo=sql($sql_periodo,"no se pudieron traer los datos de la factura") or fin_pagina();
   $periodo=$res_periodo->fields['periodo_actual'];
   $periodo=explode ("/",$periodo);


	$nro_exp= "$periodo[1]$periodo[0]$id_expediente$cuie";
	
	$calend[1]="Enero";
	$calend[2]="Febrero";
	$calend[3]="Marzo";
	$calend[4]="Abril";
	$calend[5]="Mayo";
	$calend[6]="Junio";
	$calend[7]="Julio";
	$calend[8]="Agosto";
	$calend[9]="Septiembre";
	$calend[10]="Octubre";
	$calend[11]="Noviembre";
	$calend[12]="Diciembre";

	$mes=(int)$periodo[1];
	$fecha_coment=$calend[$mes]." ".$periodo[0];

	$usuario=$_ses_user['name'];
	
    $query="insert into expediente.expediente
               (id_expediente,
  				id_efe_conv,
  				fecha_ing,
  				monto,
  				nro_exp,
  				plazo_para_pago,
  				comentario1,
  				id_factura,
  				periodo,
  				estado
  			)
             values
              ('$id_expediente',
  				'$id_efector_real',
  		        '$fecha_carga',
  				'$monto_prefactura_1',
  				'$nro_exp',
  				'$plazo_para_pago',
  				'$comentario',
  				'$id_factura',
  				'$fecha_coment',
  				'$estado'
  				)";

    
	$query_trans="insert into expediente.transaccion
               (id_transac,
  				id_expediente,
               	id_area,
  				fecha_mov,
  				estado,
  				comentario,
  				total_pagar,
  				id_factura,
  				usuario  				
  				)
             values
              ('$id_transac',
  				'$id_expediente',
  				'$id_area',
  				'$fecha_carga',
  				'$estado',
  				'$comentario',
  				'$monto_prefactura_1',
  				'$id_factura',
  				'$usuario'  				
  				)";

    
   //trabamos la factura
	$sql_update="UPDATE facturacion.factura SET traba='si',estado_exp=1 WHERE id_factura=$id_factura";
	sql ($sql_update, "Error al insertar campo en facturacion") or fin_pagina();
							
    
    
    sql($query, "Error al insertar el Expediente") or fin_pagina();
    sql($query_trans, "Error al insertar la transaccion") or fin_pagina();    


//----------------------------------------------------------------------------------------------------------------------
	$accion="Se CERRO la Factura Numero: $id_factura"; 
   
    $db->CompleteTrans(); 
    
    if (es_cuie($_ses_user['login'])){
    	$contenido_mail_control="CERRARON la Factura ONLINE Numero: $id_factura el efector con CUIE $usuario";
    	enviar_mail('','','','Cierra de Factura On Line',$contenido_mail_control,'','');
    	enviar_mail('magianello@hotmail.com','','','Cierra de Factura On Line',$contenido_mail_control,'','');
		enviar_mail('danireque1517@hotmail.com','','','Cierra de Factura On Line',$contenido_mail_control,'','');
		//enviar_mail('seba1202@gmail.com','','','Cierra de Factura On Line',$contenido_mail_control,'','');

    	echo 'Se Envio Mail Correctamente';
    }
    else{
    	$contenido_mail_control="CERRARON la Factura Numero: $id_factura el usuario $usuario";
    	enviar_mail('','','','Cierre de Factura',$contenido_mail_control,'','');
    	enviar_mail('magianello@hotmail.com','','','Cierra de Factura',$contenido_mail_control,'','');
		enviar_mail('danireque1517@hotmail.com','','','Cierra de Factura',$contenido_mail_control,'','');
		//enviar_mail('seba1202@gmail.com','','','Cierra de Factura On Line',$contenido_mail_control,'','');
		
    	echo 'Se Envio Mail Correctamente';
    }
   		
}

if ($_POST['anula_factura']=="Anula Factura"){
	$fecha_carga=date("Y-m-d");
   $db->StartTrans();
   
   $query="update facturacion.factura set estado='X'
   			where id_factura=$id_factura";
   sql($query, "Error al anular la factura") or fin_pagina();
   
   $accion="Se ANULO la Factura Numero: $id_factura";
   
   /*cargo los log*/ 
    $usuario=$_ses_user['name'];
	$log="insert into facturacion.log_factura
		   (id_factura, fecha, tipo, descripcion, usuario) 
	values ($id_factura, '$fecha_carga','Anula Factura','Anula la Factura', '$usuario')";
	sql($log) or fin_pagina();
	 
    $db->CompleteTrans();    
   		
}
if ($_POST['abre_factura']=="Abre Factura"){
	$fecha_carga=date("Y-m-d");
   $db->StartTrans();
   
   $query="update facturacion.factura set estado='A'
   			where id_factura=$id_factura";
   sql($query, "Error al cerrar la factura") or fin_pagina();
   
   $accion="Se Abrio la Factura Numero: $id_factura";
   
   /*cargo los log*/ 
    $usuario=$_ses_user['name'];
	$log="insert into facturacion.log_factura
		   (id_factura, fecha, tipo, descripcion, usuario) 
	values ($id_factura, '$fecha_carga','Abrio Factura','Abrio la Factura', '$usuario')";
	sql($log) or fin_pagina();
	 
    $db->CompleteTrans();    
   		
}

if ($_POST['guardar_extra']=="Guardar"){
	
   $fecha_carga=date("Y-m-d");
   $db->StartTrans();
   $mes_fact_d_c=$_POST['mes_fact_d_c'];
   $fecha_control=Fecha_db($fecha_control);
   if ($fecha_control=='') $fecha_control='1980-01-01';
     
   $query="update facturacion.factura set 
   				mes_fact_d_c='$mes_fact_d_c',
   				monto_prefactura='$monto_prefactura',
   				fecha_control='$fecha_control',
   				nro_exp='$nro_exp'
   				where id_factura=$id_factura";
   sql($query, "Error al cerrar la factura") or fin_pagina();
   
   $accion="Guardo Datos Extras en la factura $id_factura";
   
   /*cargo los log*/ 
    $usuario=$_ses_user['name'];
	$log="insert into facturacion.log_factura
		   (id_factura, fecha, tipo, descripcion, usuario) 
	values ($id_factura, '$fecha_carga','Guardo el dato extra','Guardo el dato en la factura $id_factura', '$usuario')";
	sql($log) or fin_pagina();
	 
    $db->CompleteTrans();    
   		
}

if ($_POST['guardar']=="Guardar Factura"){
	
	if ($_POST[alta_comp] == "alta_comp"){
		$alta_comp='SI';
	}
	else{
		$alta_comp='';
	}
	$sql= "select cuie, nombre, com_gestion, fecha_comp_ges, fecha_fin_comp_ges from nacer.efe_conv where cuie='$cuie'";
	$res_efectores_aux=sql($sql) or fin_pagina();
	$com_gestion_aux=$res_efectores_aux->fields['com_gestion'];
	$fecha_comp_ges_aux=$res_efectores_aux->fields['fecha_comp_ges'];
	$fecha_fin_comp_ges_aux=$res_efectores_aux->fields['fecha_fin_comp_ges'];
	
	$fecha_factura=Fecha_db($fecha_factura);
	
	if ($com_gestion_aux=='VERDADERO'){
		if (($fecha_comp_ges_aux<=$fecha_factura)&&($fecha_factura<=$fecha_fin_comp_ges_aux)){		 	
		   $fecha_carga=date("Y-m-d");
		   $db->StartTrans();
	   
		   $q="select nextval('facturacion.factura_id_factura_seq') as id_factura";
		    $id_factura=sql($q) or fin_pagina();
		    $id_factura=$id_factura->fields['id_factura'];
		   
			if (es_cuie($_ses_user['login'])) $factura_online='SI';
			else $factura_online='NO';
			   
		    $query="insert into facturacion.factura
		             (id_factura,cuie,fecha_carga,fecha_factura,periodo,estado,observaciones,online,periodo_actual,alta_comp,estado_envio)
		             values
		             ($id_factura,'$cuie','$fecha_carga','$fecha_factura','$periodo','A','$observaciones','$factura_online','$periodo_actual','$alta_comp','n')";
		
		    sql($query, "Error al insertar la factura") or fin_pagina();
		    
		    $accion="Se guardo la Factura Numero: $id_factura";
			
		    /*cargo los log*/ 
		    $usuario=$_ses_user['name'];
			$log="insert into facturacion.log_factura
				   (id_factura, fecha, tipo, descripcion, usuario) 
			values ($id_factura, '$fecha_carga','ALTA','Alta desde Usuario', '$usuario')";
			sql($log) or fin_pagina();
			 
		    $db->CompleteTrans();    
	    }
	    else{
	    	$accion="Error: La fecha de factura esta fuera de vigencia con el compromiso de gestion";
	    }
	}
	else{
		$accion="Error: El efector seleccionado no tiene Compromiso de Gestion";
	}
}//de if ($_POST['guardar']=="Guardar nuevo Muleto")
// -------------- Guardar editar-----------------------

if ($_POST['guardar_editar']=="Guardar"){
	
		   $fecha_mod=date("Y-m-d");
		   $db->StartTrans();
		   $fecha_factura=fecha_db($fecha_factura);
	   
		    $query="update facturacion.factura set
		             periodo= '$periodo',
		             fecha_factura='$fecha_factura',
		             observaciones='$observaciones' ,
		             periodo_actual= '$periodo_actual'
		             Where id_factura='$id_factura'";
		
		    sql($query, "Error al modificar la factura $id_factura") or fin_pagina();
		    
		    $accion="Se guardo la modificacion de la Factura Numero: $id_factura";
			
		    /*cargo los log*/ 
		    $usuario=$_ses_user['name'];
			$log="insert into facturacion.log_factura
				   (id_factura, fecha, tipo, descripcion, usuario) 
			values ($id_factura, '$fecha_mod','MODIFICACION','Modificacion desde Usuario', '$usuario')";
			sql($log) or fin_pagina();
			 
		    $db->CompleteTrans();    
	}//----------------Fin de guardar editar----------------

if ($id_factura) {
	  //---------calculo para actualizacion de credito en monto total
	     $query_deb=" SELECT
					sum(cantidad * monto) as subtotal,
					facturacion.factura.monto_prefactura
					FROM
					facturacion.debito
					INNER JOIN facturacion.factura on (facturacion.debito.id_factura=facturacion.factura.id_factura)
					WHERE
					facturacion.factura.id_factura='$id_factura'
					GROUP BY
					facturacion.factura.monto_prefactura";
	    
	    $res_deb= sql($query_deb, "Error al cerrar la factura") or fin_pagina();
	    $debitos=$res_deb->fields[subtotal];
		 //-------------total credito
	    $query_cred=" SELECT
					sum(cantidad * monto) as subtotal,
					facturacion.factura.monto_prefactura
					FROM
					facturacion.credito
					INNER JOIN facturacion.factura on (facturacion.credito.id_factura=facturacion.factura.id_factura)
					WHERE
					facturacion.factura.id_factura='$id_factura'
					GROUP BY
					facturacion.factura.monto_prefactura";
	    
	    $res_cred= sql($query_cred, "Error al cerrar la factura") or fin_pagina();
	    $credito=$res_cred->fields[subtotal];
	    
	    //-------------------------total facturado
   		$query_t="SELECT sum 
			(facturacion.prestacion.cantidad*facturacion.prestacion.precio_prestacion) as total
			FROM
			  facturacion.factura
			  INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
			  INNER JOIN facturacion.prestacion ON (facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante)
			  where factura.id_factura=$id_factura";
		$total=sql($query_t,"NO puedo calcular el total");
		$query_t1="SELECT sum 
			(nomenclador.prestaciones_n_op.precio) as total1
			FROM
			  facturacion.factura
			  INNER JOIN facturacion.comprobante ON (facturacion.factura.id_factura = facturacion.comprobante.id_factura)
			  INNER JOIN nomenclador.prestaciones_n_op using (id_comprobante)
			  where factura.id_factura=$id_factura";
		$total1=sql($query_t1,"NO puedo calcular el total");
		$monto_prefactura=$total->fields['total']+$total1->fields['total1'] + $debitos - $credito;

//-----------ACTUALIZACION DEL MONTO DE FACTURA----------- 
	    $query_fact="update facturacion.factura set 
   								monto_prefactura='$monto_prefactura'   				
   					where id_factura=$id_factura";
   sql($query_fact, "Error al modificar el monto de la factura") or fin_pagina();
	    
	  //---------------fin de la actualizacion  
$query="SELECT 
  *
FROM
  facturacion.factura
  where id_factura=$id_factura";
$res_factura=sql($query, "Error al traer el Comprobantes") or fin_pagina();

$cuie=$res_factura->fields['cuie'];
$fecha_factura=$res_factura->fields['fecha_factura'];
$periodo=$res_factura->fields['periodo'];
$periodo_actual=$res_factura->fields['periodo_actual'];
$observaciones=$res_factura->fields['observaciones'];
$estado=$res_factura->fields['estado'];
$mes_fact_d_c=$res_factura->fields['mes_fact_d_c'];
$monto_prefactura=$res_factura->fields['monto_prefactura'];
$fecha_control=$res_factura->fields['fecha_control'];
$nro_exp=$res_factura->fields['nro_exp'];
$traba=$res_factura->fields['traba'];
$alta_comp=$res_factura->fields['alta_comp'];

}

echo $html_header;
?>
<script type="text/javascript">

$(document).ready(function(){
	var id_factura = "<?php echo $id_factura?>";
	if (id_factura!=""){
		document.all.fecha_factura.disabled=true;
		$("input[name=fecha_factura]").next("img").hide(); 	
		document.all.periodo.disabled=true;
		document.all.periodo_actual.disabled=true;
		document.all.observaciones.disabled=true;}
	
	
})


//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos()
{
 if(document.all.cuie.value=="-1"){
  alert('Debe Seleccionar un efector');
  return false;
 }
 if(document.all.periodo.value=="-1"){
  alert('Debe Seleccionar un Periodo');
  return false;
 } 
 if(document.all.periodo_actual.value=="-1"){
	  alert('Debe Seleccionar un Periodo Actual');
	  return false;
	 } 
 if(document.all.fecha_factura.value==""){
  alert('Debe Ingresar una fecha de factura');
  return false;
 } 
 
}//de function control_nuevos()


function stringToDate(_date,_format,_delimiter)
{
            var formatLowerCase=_format.toLowerCase();
            var formatItems=formatLowerCase.split(_delimiter);
            var dateItems=_date.split(_delimiter);
            var monthIndex=formatItems.indexOf("mm");
            var dayIndex=formatItems.indexOf("dd");
            var yearIndex=formatItems.indexOf("yyyy");
            var month=parseInt(dateItems[monthIndex]);
            month-=1;
            var formatedDate = new Date(dateItems[yearIndex],month,dateItems[dayIndex]);
            return formatedDate;
}

function control_cierre(user) { 



/*stringToDate("17/9/2014","dd/MM/yyyy","/");
stringToDate("9/17/2014","mm/dd/yyyy","/")
stringToDate("9-17-2014","mm-dd-yyyy","-")*/


var fecha=document.all.periodo_actual.value;
var array_c = fecha.split("/");
var fecha_string = "01-"+array_c[1]+"-"+array_c[0];         
var fecha_js = new Date(stringToDate(fecha_string,"dd-mm-yyyy","-"));
var dias=159;

var fecha=fecha_js.getTime();

//Calculamos los milisegundos sobre los dias que hay que sumar o restar...
milisegundos=parseInt(dias*24*60*60*1000);
	
//Modificamos la fecha actual
var total=fecha_js.setTime(fecha+milisegundos);
var fecha_final = new Date (total);
var fecha_hoy = new Date();

alert ('El cierre de factura inicia el circuito de pago a travez del sistema de Expediente');
 
if (fecha_hoy>=fecha_final) {
	if (user=="miguel") {
	var r = confirm('La fecha de prestacion de la factura ha exedido el plazo maximo para pago, Desea cerrarla de todos modos');
	return r;
	}
	else { alert ('La fecha de prestacion de la factura ha exedido el plazo maximo para pago, se nesecita permiso especial para cerrarla');
	return false;}
	} 

//else { alert ("la fecha final es:"+fecha_final);
	return true;
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

//-----------------Editar factura-------------------

function editar_campos()
{	
	
	document.all.fecha_factura.disabled=false;	
	$("input[name=fecha_factura]").next("img").show(); 
	document.all.periodo.disabled=false;
	document.all.periodo_actual.disabled=false;
	document.all.observaciones.disabled=false;
	

	$("input[name=guardar_editar]").prop('disabled', false);
	$("input[name=cancelar_editar]").prop('disabled', false);
	$("input[name=cierra_factura]").prop('disabled', true);
	$("input[name=anula_factura]").prop('disabled', true);
	$("input[name=editar]").prop('disabled', true);
	
	
	return true;
}

//-----------------Fin de Editar Factura------------

</script>

<form name='form1' action='factura_admin.php' method='POST'>
<input type="hidden" value="<?=$id_factura?>" name="id_factura">
<input type="hidden" value="<?=$cuie?>" name="cuie">

<?echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";

/*******Traemos y mostramos el Log **********/
if ($id_factura) {

$q="SELECT 
  facturacion.log_factura.fecha,
  facturacion.log_factura.tipo,
  facturacion.log_factura.descripcion,
  facturacion.log_factura.usuario
FROM
  facturacion.factura
  INNER JOIN facturacion.log_factura ON (facturacion.factura.id_factura = facturacion.log_factura.id_factura)
  where factura.id_factura=$id_factura
	order by id_log_factura";
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
}
}?>
</table>
</div>
<hr>
<?/*******************  FIN  LOG  ****************************/?>


<input type="hidden" name="id_factura" value="<?=$id_factura?>">
<table width="95%" cellspacing=0 border=1 bordercolor=#E0E0E0 align="center" bgcolor='<?=$bgcolor_out?>' class="bordes">
 <tr id="mo">
    <td>
    	<?
    	if (!$id_factura) {
    	?>  
    	<font size=+1><b>Nueva Factura</b></font>&nbsp;&nbsp;&nbsp;
    	<img src='<?php echo "$html_root/imagenes/ayuda.gif" ?>' style="cursor:hand" border="0" alt="Ayuda" onClick="abrir_ventana('<?php echo "$html_root/modulos/ayuda/facturacion/nueva_factura.htm" ?>', 'Agregar Factura')" >   
    	<? }
        else {
        ?>
        <font size=+1><b>Factura (<?=($estado=='C')?"Cerrada":"Abierta"?>)</b></font> &nbsp;&nbsp;&nbsp;
        <img src='<?php echo "$html_root/imagenes/ayuda.gif" ?>' style="cursor:hand" border="0" alt="Ayuda" onClick="abrir_ventana('<?php echo "$html_root/modulos/ayuda/facturacion/modifica_factura.htm" ?>', 'Modificar Factura')" >  
        <? } ?>
       
    </td>
 </tr>
 <tr><td>
  <table width=70% align="center" class="bordes">
     <tr>
      <td id=mo colspan="2">
       <b> Descripción de la FACTURA</b>
      </td>
     </tr>
     <tr>
       <td>
        <table>
         <tr>	           
           <td align="center" colspan="2">
            <b> N&uacute;mero de Factura: <font size="+1" color="Red"><?=($id_factura)? $id_factura : "Nueva Factura"?></font> </b>
           </td>
         </tr>
         <tr>
         	<td align="right">
				<b>Efector:</b>
			</td>
			<td align="left">			 	
			 <select name=cuie Style="width=450px" 
        	onKeypress="buscar_combo(this);"
			onblur="borrar_buffer();"
			onchange="borrar_buffer(); document.forms[0].submit();"
        	<?if ($id_factura) echo "disabled"?>>
			 
			 <?
			  $user_login1=substr($_ses_user['login'],0,6);
			  if (es_cuie($_ses_user['login'])){
				echo"<option value=-1>Seleccione</option>";
				$sql= "select cuie, nombre, com_gestion from nacer.efe_conv where cuie='$user_login1' order by nombre";	
				}
			  else{
			  	echo"<option value=-1>Seleccione</option>";
			  	$sql= "select cuie, nombre, com_gestion from nacer.efe_conv order by nombre";
			 	 }
			 
			 $res_efectores=sql($sql) or fin_pagina();
			 while (!$res_efectores->EOF){ 
			 	$cuiel=$res_efectores->fields['cuie'];
			    $nombre_efector=$res_efectores->fields['nombre'];
			    
			    ?>
				<option value='<?=$cuiel?>' <?if ($cuie==$cuiel) echo "selected"?> ><?=$cuiel." - ".$nombre_efector?></option>
			    <?
			    $res_efectores->movenext();
			    }?>
			</select>
			</td>
         </tr>
         <?$sql="select per_alta_com,adenda_per,fecha_adenda_per,categoria_per 
								from nacer.efe_conv
								where cuie='$cuie' and per_alta_com='SI'";
						$res_efec_1=sql($sql,"no se pudo ejecutar");
						if ($res_efec_1->recordcount()==1){?>
					 <tr>
					 	<td align="right">
					    	<b>Factura Alta Complejidad:</b>
					    </td>
					    <td align="left">
					    	<input type="checkbox" name="alta_comp" value="alta_comp" <?=($alta_comp=='SI')?'checked':'';?>>				    	 
					    </td>		    
					 </tr>
					 <?}?>
         <tr>
			<td align="right">
				<b>Fecha Factura:</b>
			</td>
		    <td align="left">
		    	<?$fecha_comprobante=date("d/m/Y");?>
		    	 <input type=text id=fecha_factura  name=fecha_factura value='<?=fecha($fecha_factura);?>' size=15 placeholder="Usar Calendario">
		    	 <?php echo link_calendario("fecha_factura"); ?>
					    	 
		    </td>		    
		</tr> 
		<tr>
         	<td align="right">
				<b>Periodo Actual:</b>
			</td>
			<td align="left">
			<select name=periodo Style="width=450px">
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
				<b>Periodo Prestaci&oacute;n:</b>
			</td>
			<td align="left">
			<select name="periodo_actual" Style="width=450px">
			<option value=-1>Seleccione</option>
			  <?
			  $sql = "select * from facturacion.periodo order by periodo";
			  $result=sql($sql,"No se puede traer el periodo");
			  while (!$result->EOF) {?>
			  			  
			  <option value=<?=$result->fields['periodo']?> <?if ($periodo_actual==$result->fields['periodo']) echo "selected"?>><?=$result->fields['periodo']?></option>
			  <?
			  $result->movenext();
			  }
			  ?>			  
			  </select>
			</td>
         </tr>
         							 
         <tr>
         	<td align="right">
         	  <b>Observaciones:</b>
         	</td>         	
            <td align='left'>
              <textarea cols='70' rows='7' id="observaciones" name="observaciones"><?=$observaciones;?></textarea>
            </td>
         </tr>
			<?if ($estado=='C'){?>
			<tr>
     			 <td id=mo colspan="2">
       				<b>Datos Extras</b>
      			</td>
     		</tr>    
     		
			<tr>
         	<td align="right">
         	  <b>Mes Facturado debito/credito:</b>
         	</td>         	
            <td align='left'>
              <input type="text" name="mes_fact_d_c" value="<?=$mes_fact_d_c?>" style="width=250px">&nbsp;&nbsp;              
            </td>
         	</tr>
         	<tr>
         	<td align="right">
         	  <b>Monto Prefactura:</b>
         	</td>         	
            <td align='left'>
              <input type="text" name="monto_prefactura" value="<?=number_format($monto_prefactura,2,'.','')?>" style="width=250px" readonly>&nbsp;&nbsp;                             
            </td>
         	</tr>
         	
         	<tr>
			<td align="right">
				<b>Fecha Control:</b>
			</td>
		    <td align="left">
		    	<?$fecha_comprobante=date("d/m/Y");?>
		    	 <input type=text id=fecha_control name=fecha_control value='<?=fecha($fecha_control);?>' size=15 readonly>
		    	 <?=link_calendario("fecha_control");?>					    	 
		    </td>		    
			</tr>
         	
         	<tr>
         	<td align="right">
         	  <b>Nùmero de Expediente:</b>
         	</td>         	
            <td align='left'>
              <input type="text" name="nro_exp" value="<?=$nro_exp?>" style="width=250px">
            </td>
         	</tr>
         	
         	<tr>         	   	
            <td align="center" colspan="2" > 
              	             
              <input type="submit" name="guardar_extra" value="Guardar" style="width=150px" <?=$disabled?>>               
            </td>
         	</tr>
         	
         	<tr>
         	<td align="center" colspan="2">

         	  <b><font size="2" face="arial" color="Red">Nota: Los valores numericos se ingresan SIN separadores de miles, y con "." como separador DECIMAL</font> </b>
         	         	<b>Modificar el Monto solo en el caso que los Debito/Creditos ya hayan sido cargados</b>
         	  </td>
         	</tr>         	 
   		<?}?> 
                 
        </table>
      </td>      
     </tr>
     <tr id="mo">
  		<td align=center colspan="2">
  			<b>Cambios de Estado</b>
  		</td>
  	</tr>  
  	<tr>
	 <td align="center" colspan="2" class="bordes">
	 	<?if ($estado=='A'){
	 	if (!es_cuie($_ses_user['login'])){?>
	 		<input type="submit" name="cierra_factura" value="Cierra Factura" onclick="return control_cierre('<?php echo $usuario?>')"; style="width=150px">
	 	<!--	<input type="submit" name="cierra_factura" value="Cierra Factura" onclick="return confirm('Esta Accion Genera el alta automatica en el Sistema de Expediente - Esta Seguro que Desea CERRAR la FACTURA?')" style="width=150px">-->
	 		<input type="submit" name="anula_factura" value="Anula Factura" onclick="return confirm('Esta Seguro que Desea ANULAR la FACTURA?')" style="width=150px">
		
		<?}if (permisos_check('inicio','permiso_editar_factura')){ // coloco las opciones de edicion----?>
		 	  <input type=button name="editar" value="Editar" onclick="editar_campos()" title="Edita Campos" style="width=130px"> &nbsp;&nbsp;
		      <input type="submit" name="guardar_editar" value="Guardar" title="Guardar" disabled style="width=130px" onclick="return control_nuevos()">&nbsp;&nbsp;
		      <input type="button" name="cancelar_editar" value="Cancelar" title="Cancela Edicion" disabled style="width=130px" onclick="document.location.reload()">
	 		
		      <?}
		      // fin de las opciones de edicion----?>
	 		
	 		
	 		<?}?> 
		<?
		if (!es_cuie($_ses_user['login'])){
			if ($estado=='C'){
				($traba=='si')?$disabled="disabled":$disabled=""?>
				
		 		<input type="submit" name="abre_factura" value="Abre Factura" onclick="return confirm('Esta Seguro que Desea Abrir la FACTURA?')" style="width=150px" <?=$disabled?>>
			<?}?>
			<?if ($estado=='X'){
				($traba=='si')?$disabled="disabled":$disabled=""?>
				
		 		<input type="submit" name="abre_factura" value="Abre Factura" onclick="return confirm('Esta Seguro que Desea Abrir la FACTURA?')" style="width=150px" <?=$disabled?>>
			<?}
		}?>
	</td>
	</tr>
	
	<?if ($estado=='C') {?>
	<tr id="mo">
  		<td align=center colspan="2">
  			<b><font color="White">Debito / Credito</font></b>
  		</td>
  	</tr>  
  	<tr>
	 <td align="center" colspan="2" class="bordes" bgcolor="#d3d3cd">		
	 	<?$ref = encode_link("debito_credito.php",array("id_factura"=>$id_factura));
	    $onclick_elegir="location.href='$ref'";
	    ($traba=='si')?$disabled="disabled":$disabled=""?>
	 	<input type="button" name="debito_credito" value="Debito / Credito" onclick="(<?=$onclick_elegir?>)" style="width=250px" <?=$disabled?>>	 		 	
	 	&nbsp;&nbsp;
	 	<?$link=encode_link("debito_excel.php", array("id_factura"=>$id_factura));	
		   echo "<br><a target='_blank' href='".$link."' title='Debito/Credito'><IMG src='$html_root/imagenes/logo_impresora.gif' height='35' width='35' border='0'></a>";?>
		   <?$link=encode_link("debito_excel1.php", array("id_factura"=>$id_factura));	
		   echo "&nbsp&nbsp<a target='_blank' href='".$link."' title='Genera Excel'><IMG src='$html_root/imagenes/excel.gif' height='35' width='35' border='0'></a>";?>
	</td>
	</tr>
	
   <?}?> 

   <?if (!($id_factura)){?>
	 
	 <tr id="mo">
  		<td align=center colspan="2">
  			<b>Guarda Factura</b>
  		</td>
  	</tr>  
      <tr align="center">
       <td>
        <input type='submit' name='guardar' value='Guardar Factura' onclick="return control_nuevos()"
         title="Guardar datos de la Factura">
       </td>
      </tr>
     
     <?}?>
     
 </table>          
 <?
 if ($id_factura){//tabla de comprobantes
$query="SELECT 
  facturacion.comprobante.id_comprobante,
  facturacion.smiefectores.nombreefector,
  facturacion.comprobante.nombre_medico,
  facturacion.comprobante.fecha_comprobante,
  nacer.smiafiliados.afiapellido,
  nacer.smiafiliados.afinombre,
  nacer.smiafiliados.afidni,
  nacer.smiafiliados.clavebeneficiario
FROM
  facturacion.comprobante
  left JOIN facturacion.smiefectores using(cuie)
  left JOIN nacer.smiafiliados using(id_smiafiliados)
  where id_factura=$id_factura
  order by comprobante.id_comprobante DESC";
$res_comprobante=sql($query,"<br>Error al traer los comprobantes<br>") or fin_pagina();
?>
<BR>
<tr><td><table width="100%" class="bordes" align="center">
	<tr align="center" id="mo">
	  <td align="center" width="3%">
	   <img id="imagen_2" src="<?=$img_ext?>" border=0 title="Mostrar Comprobantes" align="left" style="cursor:hand;" onclick="muestra_tabla(document.all.prueba_vida,2);" >
	  </td>
	  <td align="center">
	   <b>Comprobantes</b> &nbsp;&nbsp;<input type="button" value="Agregar Comprobante" name="agregar_comprobante" <?=($estado=='C')?"disabled":""?> onclick="window.open('<?=encode_link('listado_comp_fact.php',array("id_factura"=>$id_factura,"cuie"=>$cuie,"alta_comp"=>$alta_comp,"periodo_actual"=>$periodo_actual))?>','','toolbar=0,location=0,directories=0,status=0, menubar=0,scrollbars=1');">
	    <!-- &nbsp;&nbsp;<input type="button" value="Prestaciones StandBy" name="prestaciones_standby" <?=($estado=='C')?"disabled":""?> onclick="window.open('<?=encode_link('listado_comp_standb.php',array("id_factura"=>$id_factura,"cuie"=>$cuie,"alta_comp"=>$alta_comp,"periodo_actual"=>$periodo_actual))?>','','toolbar=0,location=0,directories=0,status=0, menubar=0,scrollbars=1');">-->
	    &nbsp;&nbsp;<input type="submit" value="Desvincular Seleccion" name="desvincular_seleccion" <?=($estado=='C')?"disabled":""?>>
	  </td>
	</tr>
</table></td></tr>
<tr><td><table id="prueba_vida" border="1" width="100%" style="display:none;border:thin groove">
		<script>
			muestra_tabla(document.all.prueba_vida,2);
		</script>
	<?if ($res_comprobante->RecordCount()==0){?>
	 <tr>
	  <td align="center">
	   <font size="3" color="Red"><b>No existen ITEMS para esta FACTURA</b></font>
	  </td>
	 </tr>
	 <?}
	 else{	 	
	 	?>
	 	<tr id="sub_tabla">	
	 	    <td width=1%>&nbsp;</td>
	 		<td >Número de Comprobante</td>
	 		<td >Apellido</td>
	 		<td >Nombre</td>
	 		<td >DNI</td>
	 		<td >Beneficiario</td>
	 		<td >Efector</td>
	 		<td >Medico</td>
	 		<td >Fecha Prestación</td>
	 		<td >Cant Prestaciones</td>
	 		<?if ($estado=='A'){?>
	 		<td >Desvincular</td>
	 		<?}?>
	 	</tr>
	 	<?
	 	$res_comprobante->movefirst();
	 	while (!$res_comprobante->EOF) {	 		
	 		$id_tabla="tabla_".$res_comprobante->fields['id_comprobante'];	
	 		$onclick_check=" javascript:(this.checked)?Mostrar('$id_tabla'):Ocultar('$id_tabla')";
	 		
	 		//consulta para saber si tiene pretaciones el comprobante
	 		$sql=" select count(id_prestacion) as cant_prestaciones from facturacion.prestacion 								
					where id_comprobante=". $res_comprobante->fields['id_comprobante'];
	 		$cant_prestaciones=sql($sql,"no se puede traer la contidad de prestaciones") or die();
	 		$cant_prestaciones=$cant_prestaciones->fields['cant_prestaciones'];
	 		
	 		$ref1 = encode_link("factura_admin.php",array("id_comprobante"=>$res_comprobante->fields['id_comprobante'],"desvincular"=>"True","id_factura"=>$id_factura));
	 		$id_comprobante_aux=$res_comprobante->fields['id_comprobante'];
	 		$onclick_marcar="if (confirm('Esta Seguro que Desea Desvincular Comprobante $id_comprobante_aux?')) location.href='$ref1'
            						else return false;	";
	 		?>
	 		<tr <?=atrib_tr()?>>
	 			<td>
	              <input type=checkbox id=checkbox name=check_prestacion[] value="<?=$res_comprobante->fields['id_comprobante']?>" onclick="<?=$onclick_check?>" class="estilos_check">
	            </td>	
		 		<td ><font size="+1" color="Red"><?=$res_comprobante->fields['id_comprobante']?></font></td>
		 		<td ><?=$res_comprobante->fields['afiapellido']?></td>
		 		<td ><?=$res_comprobante->fields['afinombre']?></td>
		 		<td ><?=$res_comprobante->fields['afidni']?></td>
		 		<td ><?=$res_comprobante->fields['clavebeneficiario']?></td>
		 		<td ><?=$res_comprobante->fields['nombreefector']?></td>
		 		<td ><?=$res_comprobante->fields['nombre_medico']?></td>
		 		<td ><?=fecha($res_comprobante->fields['fecha_comprobante'])?></td>		 		
		 		<td ><?="Total: ".$cant_prestaciones?></td>	
		 		<?if ($estado=='A'){?>
		 		<td onclick="<?=$onclick_marcar?>" align="center"><img src='../../imagenes/sin_desc.gif' style='cursor:hand;'></td>		 		
		 		<?}?>	 		
		 	</tr>	
		 	<tr>
	          <td colspan=10>
	
	                  <?
	                  $sql=" select prestacion.*,nomenclador.*, patologias.codigo as cod_diag, patologias.descripcion as desc_diag
								from facturacion.prestacion 
								left join facturacion.nomenclador using (id_nomenclador)
								LEFT JOIN nomenclador.patologias ON (prestacion.diagnostico=patologias.codigo)											
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
		                               <td>Descripción</td>
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
							           						 $query="select descripcion from nomenclador.patologias where codigo='".$result_items1->fields['patologia']."';";
							           						 $res_k=sql($query) or fin_pagina();
							           						 $query="select categoria from nomenclador.grupo_prestacion where codigo='".$result_items1->fields['profesional']."';";
							           						 $res_l=sql($query) or fin_pagina();
							           						 $descripcion='<b>Prestacion: </b>'.$res_i->fields["categoria"].' <b>Objeto: </b>'.$res_j->fields["categoria"].' <b>Diagnostico: </b>'.$res_k->fields["descripcion"];
							           						 $descripcion_amp='Prestacion: '.$res_i->fields["categoria"].' Objeto: '.$res_j->fields["categoria"].' Diagnostico: '.$res_k->fields["descripcion"].' Profesional: '.$res_l->fields["categoria"];
							           						 ?>
							                            	 <td class="bordes">1</td>			                                 
							                                 <td class="bordes" title="<?=$result_items1->fields["codigo"]?>"><?=substr($result_items1->fields["codigo"],42)?></td>
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
<?}?>
<br>
 <tr><td><table width=100% align="center" class="bordes">
  <tr align="center">
   <td>
     <input type=button name="volver" value="Volver" onclick="document.location='listado_factura.php'"title="Volver a los comprobantes" style="width=150px">     
   </td>
  </tr>
 </table></td></tr>
 
 
 </table>
 </form>
 
 <?=fin_pagina();// aca termino ?>
