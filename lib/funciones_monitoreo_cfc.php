<?php
/*
Creador: Gabriel
Modificado por:
$Author: nazabal $
$Revision: 1.17 $
$Date: 2006/11/27 15:59:01 $
*/
//verifica los certificados de todos los competidores con seguimiento y certificados vencidos
function monitorear_cfcs($forzado=false){
	global $db;

	//hoy + 1 semana
	$fecha_limite=date("Y-m-d", strtotime("+8 day"));
	/////////////////////////////////////////////

	$consulta="select id_competidor, nombre, cuit, fecha_certificado from licitaciones.competidores 
		where (competidor_activo=1) and (cuit is not null)and ((fecha_certificado<='".date("Y-m-d")."')or(fecha_certificado is null))";

	$rta_consulta=$db->Execute($consulta)or die("c30: ".$consulta);
	$user_agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";

	$flag_lic=false;
	$sendto_lic="licitaciones@coradir.com.ar";
	$subject_lic="Aviso de cambios en seguimientos de C.F.C. de competidores (".date("d/m/Y H:i").")";
	$contenido_lic="Seguimiento automático (".date("d/m/Y H:i")."):\n";

	$flag_prog=false;
	$sendto_prog="nazabal@coradir.com.ar";
	$subject_prog="Aviso de verificación de página de C.F.C. (".date("d/m/Y H:i").")";
	$contenido_prog="Seguimiento automático (".date("d/m/Y H:i")."):\n";

	while ($fila=$rta_consulta->fetchRow()) {
		if (ereg("([0-9]{10,11})", $fila["cuit"], $cuit)){
	  	$ch = curl_init();
  	 	$e = 1;
//   		$e = $e && curl_setopt($ch, CURLOPT_PROXY,'192.168.1.48:3128');
	   	$e = $e && curl_setopt($ch, CURLOPT_POST,1);
  	 	$e = $e && curl_setopt($ch, CURLOPT_REFERER,"http://www.afip.gov.ar/Tramites/tramites_en_linea/rg1814/rg1814_main.asp");
   		$e = $e && curl_setopt($ch, CURLOPT_POSTFIELDS,"fcuit=".$cuit[0]);
	   	$e = $e && curl_setopt($ch, CURLOPT_URL,"http://www.afip.gov.ar/Tramites/tramites_en_linea/rg1814/consulta1814.asp");
  	 	$e = $e && curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
   		$e = $e && curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

	   	$result=curl_exec ($ch);
  	 	curl_close ($ch);
   
  	 	// Busco el primer comentario de titulo
	   	$desde=strpos($result, "<!------ <Título Principal> ------->")+strlen("<!------ <Título Principal> ------->");
  	 	// Busco el segundo comentario de titulo
	   	$desde=strpos($result, "<!------ <Título Principal> ------->",$desde)+strlen("<!------ <Título Principal> ------->");
	   	// Busco el inicio de la fila
  	 	$desde=strpos($result, "<TR>", $desde);
	   	// Busco el final de la fila
  	 	$hasta=strpos($result, "</TR>", $desde)+5;
   		$data=substr($result, $desde, $hasta-$desde);
	   	if (ereg("[0-9]{2}\/[0-9]{2}\/[0-9]{4}.*([0-9]{2})\/([0-9]{2})\/([0-9]{4})", $data, $fecha)){
	   		$fecha_afip=$fecha[3]."-".$fecha[2]."-".$fecha[1];
	   		if ($fecha_afip!=substr($fila["fecha_certificado"], 0, 10)){//nuevo certificado -> actualizar tablas
	   			$db->StartTrans();
	   			$consulta="update licitaciones.competidores set fecha_certificado='".$fecha_afip."' where id_competidor=".$fila["id_competidor"];
	   			$db->Execute($consulta) or die("c58".$consulta);
	   			$consulta="insert into licitaciones.log_certificados_competidores (fecha_certificado, id_competidor) values ('".$fecha_afip."', ".$fila["id_competidor"].")";
	   			$db->Execute($consulta) or die("c60".$consulta);
	   			$db->CompleteTrans();
	   		}//else Fecha igual -> hacer nada
	   		if ($fecha_afip<date("Y-m-d")){
	   			$contenido_lic.="Nombre competidor: ".$fila["nombre"]."(C.U.I.T.: ".$fila["cuit"].") --> Fecha último certificado: ".$fila["fecha_certificado"]
	   				." -->  ¡CERTIFICADO VENCIDO!";
	   				$flag_lic=true;
	   		}elseif ($fecha_afip<=$fecha_limite){//el certificado vence esta semana
	   			$contenido_lic.="Nombre competidor: ".$fila["nombre"]."(C.U.I.T.: ".$fila["cuit"].") --> Fecha último certificado: ".$fila["fecha_certificado"]
	   				." --> ¡VENCE EN LOS PRÓXIMOS 7 DÍAS!\n";
	   			$flag_lic=true;
	   		}
	   	}elseif (strpos($result, "No se encontraron datos para esa ")){//nro cuit incorrecto
	   		$contenido_lic.="Nombre competidor: ".$fila["nombre"]."(C.U.I.T.: ".$fila["cuit"].") --> Fecha último certificado: ".$fila["fecha_certificado"]
	   			." --> LA PÁGINA NO RECONOCE EL NRO DE CUIT (DEBE VERIFICARLO)\n";
	   		$flag_lic=true;
	   	}elseif (strpos($result, "El contribuyente no registra certificado vigente a la fecha")){
	   		$contenido_lic.="Nombre competidor: ".$fila["nombre"]."(C.U.I.T.: ".$fila["cuit"].") --> Fecha último certificado: 'desconocido'
	   			--> 'El contribuyente no registra certificado vigente a la fecha.' --> \n";
	   		$flag_lic=true;
	   	}else{//probable cambio en la página de la A.F.I.P.
	   		$contenido_lic.="Nombre competidor: ".$fila["nombre"]."(C.U.I.T.: ".$fila["cuit"].") --> Fecha último certificado: ".$fila["fecha_certificado"]
	   			." --> PROBABLE CAMBIO DE DISEÑO EN LA PÁGINA DE LA A.F.I.P. (CFC) - NO SE PUEDE EXTRAER LA FECHA DE VENCIMIENTO DEL CERTIFICADO\n";
	   		$contenido_prog.="Nombre competidor: ".$fila["nombre"]."(C.U.I.T.: ".$fila["cuit"].") --> Fecha último certificado: ".$fila["fecha_certificado"]
	   			." --> PROBABLE CAMBIO EN LA PÁGINA DE LA A.F.I.P. (NO SE PUEDE EXTRAER LA FECHA DE VENCIMIENTO DEL CERTIFICADO - VERIFICAR)\n";
	   		$flag_prog=true;
	   		$flag_lic=true;
	   	}
		}else{//nro cuit incorrecto
			$contenido_lic.="Nombre competidor: ".$fila["nombre"]."(C.U.I.T.: ".$fila["cuit"].") --> Fecha último certificado: ".$fila["fecha_certificado"]
				." --> NO SE RECONOCE EL NRO DE CUIT (DEBE VERIFICARLO)\n";
			$flag_lic=true;
		}
	}
	if ((!$forzado)&&(date("D")=="Mon")){
		if ($flag_prog){//enviar mail a "programadores"
			$contenido_prog.="\n\nDirección de la página:\n\nhttp://www.afip.gov.ar/Tramites/tramites_en_linea/rg1814/rg1814_main.asp\n";
			enviar_mail($sendto_prog, $subject_prog, $contenido_prog, "", "", "", 0);
		}
		if ($flag_lic){//enviar mail a "licitaciones"
			enviar_mail($sendto_lic, $subject_lic, $contenido_lic, "", "", "", 0);
		}
	}
}

//verifica el certificado del competidor pasado por parámetro
function verificarCFC($id_competidor){
	global $db;
	if($id_competidor){
		//////////////////////////////////////////////
		//cálculo de la fecha "hoy + 1 semana"
		$fecha_limite=date("Y-m-d", strtotime("+8 day"));
		/////////////////////////////////////////////
	
		$consulta="select id_competidor, nombre, cuit, fecha_certificado from licitaciones.competidores where (competidor_activo=1)and(id_competidor=".$id_competidor.")";
		$rta_consulta=$db->Execute($consulta) or die("c115: ".$consulta);
		$fila=$rta_consulta->fetchRow();
		if ($rta_consulta->recordCount()==1){
			if (($fila["fecha_certificado"])&&($fila["fecha_certificado"]<date("Y-m-d"))){
				return "11: ¡CERTIFICADO VENCIDO! -->".$fila["nombre"]."(".$fila["id_competidor"].")";
			}elseif (!$fila["fecha_certificado"]) return "3: No se ha hecho la primera verificación de la fecha de vencimiento en la página de la A.F.I.P. -->".$fila["nombre"]."(".$fila["id_competidor"].")\n";
		}elseif ($rta_consulta->recordCount()==0) return "4: No existe un competidor con ese identificador o no se ha activado la opción de hacerle el seguimiento de C.F.C. -->".$fila["nombre"]."(".$fila["id_competidor"].")\n";
		else return "-1: Error de consistencia en base de datos -->".$fila["nombre"]."(".$fila["id_competidor"].")\n";//imposible teoricamente
		return "0: operación finalizada con éxito -->*";
	}
	return "4: id competidor nulo";
}
?>