<?/*

----------------------------------------
 Autor: FER
 Fecha: 03/03/2009
----------------------------------------

/*******************************************************************************
 Valida las prestaciones de acuerdo a las reglas especificadas
 
 @id_comprobante 
 @nomenclador    Id del nomenclador (despues tengo que sacar codigo)   
 
*******************************************************************************/
function valida_prestacion($id_comprobante,$nomenclador){
	 
	//asigno variables para usar la validacion
	$query="select codigo from facturacion.nomenclador 
			where id_nomenclador='$nomenclador'";	             
	$res_codigo_nomenclador=sql($query, "Error 1") or fin_pagina();	
	$codigo=$res_codigo_nomenclador->fields['codigo'];
	
	//traigo el codigo de nomenclador y si hay validaciones traigo los datos de la validacion
	$query="select * from facturacion.validacion_prestacion
			where codigo='$codigo'";	             
	$res=sql($query, "Error 1") or fin_pagina();
	
	if ($res->RecordCount()>0){//me fijo si hay que validar (si tiene regla)
		//recupero el id_smiafiliados para mas adelante
		$query="SELECT id_smiafiliados,fecha_comprobante
				FROM facturacion.comprobante
  				INNER JOIN nacer.smiafiliados using (id_smiafiliados)
				where id_comprobante='$id_comprobante'";	             
		$id_smiafiliados_res=sql($query, "Error 2") or fin_pagina();
		$id_smiafiliados=$id_smiafiliados_res->fields['id_smiafiliados'];
		$fecha_comprobante=$id_smiafiliados_res->fields['fecha_comprobante'];
		
		//cantidad de prestaciones limites
		$cant_pres_lim=$res->fields['cant_pres_lim'];
		$per_pres_limite=$res->fields['per_pres_limite'];
		
		//cuenta la cantidad de prestaciones de un determinado filiado, de un determinado codigo y 
		//en un periodo de tiempo parametrizado.
	$query="SELECT id_prestacion, codigo, fecha_comprobante
				FROM nacer.smiafiliados
  				INNER JOIN facturacion.comprobante ON (nacer.smiafiliados.id_smiafiliados = facturacion.comprobante.id_smiafiliados)
  				INNER JOIN facturacion.prestacion ON (facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante)
  				INNER JOIN facturacion.nomenclador ON (facturacion.prestacion.id_nomenclador = facturacion.nomenclador.id_nomenclador)
  				where smiafiliados.id_smiafiliados=$id_smiafiliados and 
  					  codigo='$codigo' and
  					   facturacion.comprobante.marca !=1 and
  					  fecha_comprobante between (CAST('$fecha_comprobante' AS date) - $per_pres_limite) and CAST('$fecha_comprobante' AS date) ";
  		$cant_pres=sql($query, "Error 3") or fin_pagina();
  
  		
  		if ($cant_pres->RecordCount()>=$cant_pres_lim){
  			$msg_error=$res->fields['msg_error'];
  			$accion = $msg_error." - Cantidad de Prestaciones: ".$cant_pres->RecordCount()." - Limite: ".$cant_pres_lim." en ".$per_pres_limite." dias" . " - Codigo: ".$codigo;
  			echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";
  			return 0;
  		}
  		else return 1;
	}
	else return 1;
}

function valida_prestacion1($id_comprobante,$nomenclador){
   
	$query="select codigo from facturacion.nomenclador 
			where id_nomenclador='$nomenclador'";	             
	$res=sql($query, "Error 1") or fin_pagina();	
	$codigo_nomenclador = $res->fields['codigo'];
	
	$query="SELECT afifechanac,fecha_comprobante
				FROM facturacion.comprobante
  				INNER JOIN nacer.smiafiliados using (id_smiafiliados)
				where id_comprobante='$id_comprobante'";	             
	$res1=sql($query, "Error 2") or fin_pagina();
	$fecha_nac=$res1->fields['afifechanac'];
	$fecha_comprobante=$res1->fields['fecha_comprobante'];
	
	list($aa,$mm,$dd) = explode("-",$fecha_comprobante);
    $fecha1 = mktime(0,0,0,$mm,$dd,$aa);
    list($aa,$mm,$dd) = explode("-",$fecha_nac);
    $fecha2 = mktime(0,0,0,$mm,$dd,$aa);
    $Dias=($fecha1 - $fecha2) / 86400;
	
	if (($codigo_nomenclador=='NPE 32')&&($Dias>365)){
		$accion = "No se Puede facturar un 'NPE 32' a un niño mayor de 1 año - Por favor Verifique o Facture un 'NPE 33'";
  		echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";
  		return 0;
	}
	else{
		if (($codigo_nomenclador=='NPE 33')&&($Dias<=365)){
			$accion = "No se Puede facturar un 'NPE 33' a un niño menor de 1 año - Por favor Verifique o Facture un 'NPE 32'";
	  		echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";
	  		return 0;
		}
		else return 1;
	}
	echo $codigo_nomenclador.$edad;
}

function valida_prestacion3($id_comprobante,$nomenclador){
	 
	//asigno variables para usar la validacion
	$query="select codigo from nomenclador.grupo_prestacion
			where id_categoria_prestacion='$nomenclador'";	             
	$res_codigo_nomenclador=sql($query, "Error 1") or fin_pagina();	
	$codigo=$res_codigo_nomenclador->fields['codigo'];
	
	//traigo el codigo de nomenclador y si hay validaciones traigo los datos de la validacion
	$query="select * from facturacion.validacion_prestacion
			where codigo='$codigo'";	             
	$res=sql($query, "Error 1") or fin_pagina();
	
	if ($res->RecordCount()>0){//me fijo si hay que validar (si tiene regla)
		//recupero el id_smiafiliados para mas adelante
		$query="SELECT id_smiafiliados,fecha_comprobante
				FROM facturacion.comprobante
  				INNER JOIN nacer.smiafiliados using (id_smiafiliados)
				where id_comprobante='$id_comprobante'";	             
		$id_smiafiliados_res=sql($query, "Error 2") or fin_pagina();
		$id_smiafiliados=$id_smiafiliados_res->fields['id_smiafiliados'];
		$fecha_comprobante=$id_smiafiliados_res->fields['fecha_comprobante'];
		
		//cantidad de prestaciones limites
		$cant_pres_lim=$res->fields['cant_pres_lim'];
		$per_pres_limite=$res->fields['per_pres_limite'];
		
		//cuenta la cantidad de prestaciones de un determinado filiado, de un determinado codigo y 
		//en un periodo de tiempo parametrizado.
		$query="SELECT id_prestaciones_n_op, codigo, comprobante.fecha_comprobante
				FROM nacer.smiafiliados
  				INNER JOIN facturacion.comprobante ON (nacer.smiafiliados.id_smiafiliados = facturacion.comprobante.id_smiafiliados)
  				inner join nomenclador.prestaciones_n_op using (id_comprobante)
  				where smiafiliados.id_smiafiliados=$id_smiafiliados and 
  					  tema='$codigo' and
  					  prestaciones_n_op.fecha_comprobante between (CAST('$fecha_comprobante' AS date) - $per_pres_limite) and CAST('$fecha_comprobante' AS date and facturacion.comprobante.marca !=1)";
  		$cant_pres=sql($query, "Error 3") or fin_pagina();
  		
  		if ($cant_pres->RecordCount()>=$cant_pres_lim){
  			$msg_error=$res->fields['msg_error'];
  			$accion = $msg_error." - Cantidad de Prestaciones: ".$cant_pres->RecordCount()." - Limite: ".$cant_pres_lim." en ".$per_pres_limite." dias" . " - Codigo: ".$codigo;
  			echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";
  			return 0;
  		}
  		else return 1;
	}
	else return 1;
}

function valida_prestacion_nuevo_nomenclador($id_comprobante,$nomenclador){
	 
	//asigno variables para usar la validacion
	$query="select codigo from facturacion.nomenclador 
			where id_nomenclador='$nomenclador'";	             
	$res_codigo_nomenclador=sql($query, "Error 1") or fin_pagina();	
	$codigo=$res_codigo_nomenclador->fields['codigo'];
	
	//traigo el codigo de nomenclador y si hay validaciones traigo los datos de la validacion
	$query="select * from facturacion.validacion_prestacion
			where id_nomenclador='$nomenclador'";	             
	$res=sql($query, "Error 1") or fin_pagina();
	
	if ($res->RecordCount()>0){//me fijo si hay que validar (si tiene regla)
		//recupero el id_smiafiliados para mas adelante
		$query="SELECT id_smiafiliados,fecha_comprobante
				FROM facturacion.comprobante
  				INNER JOIN nacer.smiafiliados using (id_smiafiliados)
				where id_comprobante='$id_comprobante'";	             
		$id_smiafiliados_res=sql($query, "Error 2") or fin_pagina();
		$id_smiafiliados=$id_smiafiliados_res->fields['id_smiafiliados'];
		$fecha_comprobante=$id_smiafiliados_res->fields['fecha_comprobante'];
		
		//cantidad de prestaciones limites
		$cant_pres_lim=$res->fields['cant_pres_lim'];
		$per_pres_limite=$res->fields['per_pres_limite'];
		
		//cuenta la cantidad de prestaciones de un determinado filiado, de un determinado codigo y 
		//en un periodo de tiempo parametrizado.
	$query="SELECT id_prestacion, codigo, fecha_comprobante
				FROM nacer.smiafiliados
  				INNER JOIN facturacion.comprobante ON (nacer.smiafiliados.id_smiafiliados = facturacion.comprobante.id_smiafiliados)
  				INNER JOIN facturacion.prestacion ON (facturacion.comprobante.id_comprobante = facturacion.prestacion.id_comprobante)
  				INNER JOIN facturacion.nomenclador ON (facturacion.prestacion.id_nomenclador = facturacion.nomenclador.id_nomenclador)
  				where smiafiliados.id_smiafiliados=$id_smiafiliados and 
  					  nomenclador.id_nomenclador='$nomenclador' and
  					   facturacion.comprobante.marca !=1 and
  					  fecha_comprobante between ('$fecha_comprobante'::date - $per_pres_limite) and '$fecha_comprobante'::date";
  		$cant_pres=sql($query, "Error 3") or fin_pagina();
  
  		
  		if ($cant_pres->RecordCount()>=$cant_pres_lim){
  			$msg_error=$res->fields['msg_error'];
  			$accion = $msg_error." - Cantidad de Prestaciones: ".$cant_pres->RecordCount()." - Limite: ".$cant_pres_lim." en ".$per_pres_limite." dias" . " - Codigo: ".$codigo;
  			echo "<center><b><font size='+1' color='red'>$accion</font></b></center>";
  			return 0;
  		}
  		else return 1;
	}
	else return 1;
}

function valida_prestacion4($id_comprobante,$nomenclador){
	return 1;
}
