<?php
/*
Creado por:Norberto (????)

$Author: mari $
$Revision: 1.49 $
$Date: 2006/12/20 20:36:16 $
*/

require "../../config.php";
require_once("gutils.php");

function suma_fechas($fecha,$ndias){
      if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha))
      	list($dia,$mes,$año)=split("/", $fecha);
      if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha))
        list($dia,$mes,$año)=split("-",$fecha);
      $nueva = mktime(0,0,0, $mes,$dia,$año) + $ndias * 24 * 60 * 60;
      $nuevafecha=date("d-m-Y",$nueva);
      return ($nuevafecha);  
}

if($_POST['poner_calificacion']=='Poner Calificacion'){
	$sql="select * from personal.capacitados 
			join personal.capacitaciones using (id_capacitacion)
			where (calificacion is not null)";
	$result=sql($sql,'no se puede calificar');
	while (!$result->EOF){
		$id_legajo=$result->fields['id_legajo'];
		$id_capacitacion=$result->fields['id_capacitacion'];
		$fecha_calificacion=suma_fechas(Fecha($result->fields['dictado_hasta']),30);
		if (feriado($fecha_calificacion)){
			$fecha_calificacion=suma_fechas($fecha_calificacion,2);
		}
		$fecha_calificacion=Fecha_db($fecha_calificacion);		
		$sql="update personal.capacitados set fecha_calificacion = '$fecha_calificacion'
				where (id_legajo='$id_legajo' and id_capacitacion='$id_capacitacion')";
		sql($sql,'no se puede actualizar');		
		$result->movenext();		
	}
	
}
///////////////////////////////////////// agregado por Gabriel //////////////////////////////////////////////
	if (!$lext){
		$lext=array(
 			"ext"=>array(),
	 		"promociones"=>array(),
			"familiares"=>array(),
			"idiomas"=>array(),
			"referencias"=>array(),
			"ausentismo"=>array(),
			"suspensiones"=>array(),
			"enfermedades"=>array()
		);
	}
	//valores pasados por post al modificar campos
	if ((!$_POST["h_id_legajo"])&&(!$id)&&(!$_POST["leg_id_legajo"])&&(!$_POST["h_id_legajo"])&&(!$parametros["id_legajo"])){
		$rta_consulta=sql("select nextval('legajos_id_legajo_seq') as id_legajo") or fin_pagina();
		$h_id_legajo=$rta_consulta->fields["id_legajo"];
	}else{
		$h_id_legajo=$_POST["h_id_legajo"]
			or $h_id_legajo=$id or $id_legajo=$_POST["h_id_legajo"]
			or $h_id_legajo=$_POST["leg_id_legajo"]
			or $h_id_legajo=$parametros["id_legajo"];
	}
	$lext["ids"]["promociones"]=$_POST["promociones_ids"] or $lext["ids"]["promociones"]=0;
	$lext["ids"]["idiomas"]=$_POST["idiomas_ids"] or $lext["ids"]["idiomas"]=0;
	$lext["ids"]["familiares"]=$_POST["familiares_ids"] or $lext["ids"]["familiares"]=0;
	$lext["ids"]["referencias"]=$_POST["referencias_ids"] or $lext["ids"]["referencias"]=0;
	$lext["ids"]["ausentismo"]=$_POST["ausentismo_ids"] or $lext["ids"]["ausentismo"]=0;
	$lext["ids"]["suspensiones"]=$_POST["suspensiones_ids"] or $lext["ids"]["suspensiones"]=0;
	$lext["ids"]["enfermedades"]=$_POST["enfermedades_ids"] or $lext["ids"]["enfermedades"]=0;

	if ($_POST["leg_cedula_identidad"]) $lext["ext"]["cedula_identidad"]=$_POST["leg_cedula_identidad"];
	if ($_POST["leg_nacionalidad"]) $lext["ext"]["nacionalidad"]=$_POST["leg_nacionalidad"];
	if ($_POST["sel_tipo_nacionalidad"]) $lext["ext"]["tipo_nacionalidad"]=$_POST["sel_tipo_nacionalidad"];
	if ($_POST["leg_codigo_postal"]) $lext["ext"]["codigo_postal"]=$_POST["leg_codigo_postal"];
	if ($_POST["sel_estado_civil"]){
		$lext["ext"]["estado_civil"]=$_POST["sel_estado_civil"];
		$lext["ext"]["fecha_estado_civil"]=date("Y-m-d");
	}
	if ($_POST["t_presentador"]) $lext["ext"]["in_presentador"]=$_POST["t_presentador"];
	if ($_POST["t_motivo_egreso"]) $lext["ext"]["baja_motivo"]=$_POST["t_motivo_egreso"];
	if ($_POST["t_observaciones_egreso"]) $lext["ext"]["baja_observaciones"]=$_POST["t_observaciones_egreso"];
	if ($_POST["t_sector"]) $lext["ext"]["in_sector"]=$_POST["t_sector"];
	if ($_POST["t_ocupacion"]) $lext["ext"]["in_ocupacion"]=$_POST["t_ocupacion"];
	if ($_POST["sel_categoria"]) $lext["ext"]["in_categoria"]=$_POST["sel_categoria"];
	if ($_POST["t_sueldo_inicial"]) $lext["ext"]["in_sueldo_inicial"]=$_POST["t_sueldo_inicial"];
	if ($_POST["ta_observaciones_ingreso"]) $lext["ext"]["in_observaciones"]=$_POST["ta_observaciones_ingreso"];
	if ($_POST["sel_horario_entra"]) $lext["ext"]["hr_entra"]=$_POST["sel_horario_entra"];
	if ($_POST["sel_horario_sale"]) $lext["ext"]["hr_sale"]=$_POST["sel_horario_sale"];
	if ($_POST["t_seguro_obligatorio"]) $lext["ext"]["in_seguro_vida_obligatorio"]=$_POST["t_seguro_obligatorio"];
	if ($_POST["t_seguro_convenio"]) $lext["ext"]["in_seguro_vida"]=$_POST["t_seguro_convenio"];
	if ($_POST["ch_examen_medico"]) $lext["ext"]["in_examen_medico"]="s";
	else $_POST["ch_examen_medico"]="n";
	if ($_POST["t_beneficiario"]) $lext["ext"]["in_beneficiario"]=$_POST["t_beneficiario"];
	if ($_POST["t_art"]) $lext["ext"]["in_art"]=$_POST["t_art"];
	if ($_POST["t_profesion"]) $lext["ext"]["profesion"]=$_POST["t_profesion"];
	if ($_POST["t_estudios"]) $lext["ext"]["estudios"]=$_POST["t_estudios"];
	if ($_POST["chtitulos"]) $lext["ext"]["exhibe_titulos"]="s";
	else $lext["ext"]["exhibe_titulos"]="n";
	if ($_POST["t_otros"]) $lext["ext"]["otros_conocimientos"]=$_POST["t_otros"];
	if ($_POST["leg_afjp"])	$lext["ext"]["id_afjp"]=$_POST["leg_afjp"];
	if ($_POST["t_login"]) $lext["ext"]["login"]=$_POST["t_login"];

	if ($lext["ext"]["login"]){
		$rta_consulta=sql("select * from sistema.usuarios where login='".$lext["ext"]["login"]."'", "No se pudo obtener el id de usuario") or fin_pagina();
		if ($rta_consulta->recordCount()==1) $lext["ext"]["id_usuario"]=$rta_consulta->fields["id_usuario"];
		else $lext["ext"]["id_usuario"]="null";
	}

	for ($i=0; $i<$lext["ids"]["promociones"]; $i++){
		if ($_POST["leg_id_legajo"]) $lext["promociones"][$i]["id_legajo"]=$_POST["leg_id_legajo"];
		if ($_POST["t_cat_fecha_".$i]) $lext["promociones"][$i]["fecha"]=Fecha_db($_POST["t_cat_fecha_".$i]);
		if ($_POST["sel_cat_categoria_".$i]) $lext["promociones"][$i]["id_categoria"]=$_POST["sel_cat_categoria_".$i];
		if ($_POST["t_cat_comentario_".$i]) $lext["promociones"][$i]["comentario"]=$_POST["t_cat_comentario_".$i];
	}
	for ($i=0; $i<$lext["ids"]["idiomas"]; $i++){
		if ($_POST["leg_id_legajo"]) $lext["idiomas"][$i]["id_legajo"]=$_POST["leg_id_legajo"];
		if ($_POST["t_idioma_".$i]) $lext["idiomas"][$i]["idioma"]=$_POST["t_idioma_".$i];
		if ($_POST["ch_lee_".$i]) $lext["idiomas"][$i]["lee"]="s";
		else $lext["idiomas"][$i]["lee"]="n";
		if ($_POST["ch_escribe_".$i]) $lext["idiomas"][$i]["escribe"]="s";
		else $lext["idiomas"][$i]["escribe"]="n";
	}
	for ($i=0; $i<$lext["ids"]["familiares"]; $i++){
		if ($_POST["leg_id_legajo"]) $lext["familiares"][$i]["id_legajo"]=$_POST["leg_id_legajo"];
		if ($_POST["sel_relacion_flia_".$i]) $lext["familiares"][$i]["relacion"]=$_POST["sel_relacion_flia_".$i];
		if ($_POST["t_nombre_flia_".$i]) $lext["familiares"][$i]["nombre_apellido"]=$_POST["t_nombre_flia_".$i];
		if ($_POST["t_fecha_flia_".$i]) $lext["familiares"][$i]["fecha_nacimiento"]=Fecha_db($_POST["t_fecha_flia_".$i]);
		if ($_POST["t_domicilio_flia_".$i]) $lext["familiares"][$i]["domicilio"]=$_POST["t_domicilio_flia_".$i];
		if ($_POST["t_dni_flia_".$i]) $lext["familiares"][$i]["dni"]=$_POST["t_dni_flia_".$i];
	}
	for ($i=0; $i<$lext["ids"]["referencias"]; $i++){
		if ($_POST["leg_id_legajo"]) $lext["referencias"][$i]["id_legajo"]=$_POST["leg_id_legajo"];
		if ($_POST["t_empleador_".$i]) $lext["referencias"][$i]["empleador"]=$_POST["t_empleador_".$i];
		if ($_POST["t_domicilioe_".$i]) $lext["referencias"][$i]["domicilio_empresa"]=$_POST["t_domicilioe_".$i];
		if ($_POST["t_referencias_".$i]) $lext["referencias"][$i]["referencias"]=$_POST["t_referencias_".$i];
		if ($_POST["t_domicilio_".$i]) $lext["referencias"][$i]["domicilio"]=$_POST["t_domicilio_".$i];
		if ($_POST["t_desde_".$i]) $lext["referencias"][$i]["desde"]=Fecha_db($_POST["t_desde_".$i]);
		if ($_POST["t_hasta_".$i]) $lext["referencias"][$i]["hasta"]=Fecha_db($_POST["t_hasta_".$i]);
		if ($_POST["t_telefono_".$i]) $lext["referencias"][$i]["telefono"]=$_POST["t_telefono_".$i];
		if ($_POST["t_tareas_".$i]) $lext["referencias"][$i]["tareas"]=$_POST["t_tareas_".$i];
		if ($_POST["ch_certificado_".$i]) $lext["referencias"][$i]["certificado"]="s";
		else $lext["referencias"][$i]["certificado"]="n";
	}
	for ($i=0; $i<$lext["ids"]["ausentismo"]; $i++){
		if ($_POST["leg_id_legajo"]) $lext["ausentismo"][$i]["id_legajo"]=$_POST["leg_id_legajo"];
		if ($_POST["sel_agno_".$i]) $lext["ausentismo"][$i]["agno"]=$_POST["sel_agno_".$i];
		if ($_POST["t_inpuntualidad_".$i]) $lext["ausentismo"][$i]["inpuntualidad"]=$_POST["t_inpuntualidad_".$i];
		if ($_POST["t_inasistencia_".$i]) $lext["ausentismo"][$i]["inasistencia"]=$_POST["t_inasistencia_".$i];
		if ($_POST["t_enfermedad_".$i]) $lext["ausentismo"][$i]["enfermedad"]=$_POST["t_enfermedad_".$i];
		if ($_POST["t_accidente_".$i]) $lext["ausentismo"][$i]["accidente"]=$_POST["t_accidente_".$i];
		if ($_POST["t_licencias_".$i]) $lext["ausentismo"][$i]["licencias"]=$_POST["t_licencias_".$i];
	}
	for ($i=0; $i<$lext["ids"]["suspensiones"]; $i++){
		if ($_POST["leg_id_legajo"]) $lext["suspensiones"][$i]["id_legajo"]=$_POST["leg_id_legajo"];
		if ($_POST["t_fecha_susp_".$i]) $lext["suspensiones"][$i]["fecha"]=Fecha_db($_POST["t_fecha_susp_".$i]);
		if ($_POST["t_motivos_".$i]) $lext["suspensiones"][$i]["motivo"]=$_POST["t_motivos_".$i];
		if ($_POST["t_dias_".$i]) $lext["suspensiones"][$i]["dias"]=$_POST["t_dias_".$i];
	}
	for ($i=0; $i<$lext["ids"]["enfermedades"]; $i++){
		if ($_POST["leg_id_legajo"]) $lext["enfermedades"][$i]["id_legajo"]=$_POST["leg_id_legajo"];
		if ($_POST["t_fecha_enf_".$i]) $lext["enfermedades"][$i]["fecha"]=Fecha_db($_POST["t_fecha_enf_".$i]);
		if ($_POST["t_diagnostico_".$i]) $lext["enfermedades"][$i]["diagnostico"]=$_POST["t_diagnostico_".$i];
	}
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if ($h_id_legajo){

		if ((($_POST["guardar_promocion"])||($_POST["guardar_familiares"])||($_POST["bidioma"])||
				($_POST["guardar_referencias"])||($_POST["guardar_ausentismo"])||($_POST["guardar_suspensiones"])||
				($_POST["guardar_enfermedades"]))&&(($id_legajo)||($_POST["leg_id_legajo"]>0))){

			if ($_POST["guardar_promocion"]){
				$consulta="delete from personal.promociones where id_legajo=".$h_id_legajo;
				sql($consulta, "No se pudieron borrar las promociones") or fin_pagina();
				for ($i=0; $i<count($lext["promociones"]); $i++){
					if ($lext["promociones"][$i]["id_categoria"]>1){
						if (!$lext["promociones"][$i]["fecha"]) $lext["promociones"][$i]["fecha"]=date("Y-m-d");
						$consulta="insert into personal.promociones (id_legajo, fecha, id_categoria, comentario)
							values ($h_id_legajo, '".$lext["promociones"][$i]["fecha"]."', ".$lext["promociones"][$i]["id_categoria"].", '".$lext["promociones"][$i]["comentario"]."')";
						$rta_consulta=sql($consulta, "No se pudieron guardar las promociones") or fin_pagina();
					}else {
						$lext["ids"]["promociones"]-=1;
						array_pop($lext["promociones"]);
					}
				}
			}elseif ($_POST["guardar_familiares"]){
				$consulta="delete from personal.familia where id_legajo=".$h_id_legajo;
				sql($consulta, "No se pudieron borrar los datos de familiares") or fin_pagina();
				for ($i=0; $i<count($lext["familiares"]); $i++){
					if (($lext["familiares"][$i]["relacion"]!="-borrar registro-")&&($lext["familiares"][$i]["nombre_apellido"])){
						$consulta="insert into personal.familia (id_legajo, relacion, nombre_apellido, fecha_nacimiento, domicilio, dni)
							values ($h_id_legajo, '"
								.$lext["familiares"][$i]["relacion"]."', '"
								.$lext["familiares"][$i]["nombre_apellido"]."', "
								.(($lext["familiares"][$i]["fecha_nacimiento"])?"'".$lext["familiares"][$i]["fecha_nacimiento"]."'":"null").", "
								.(($lext["familiares"][$i]["domicilio"])?"'".$lext["familiares"][$i]["domicilio"]."'":"null").", "
								.(($lext["familiares"][$i]["dni"])?"'".$lext["familiares"][$i]["dni"]."'":"null").")";
						$rta_consulta=sql($consulta, "No se pudieron guardar los datos de los familiares") or fin_pagina();
					}elseif ($lext["familiares"][$i]["relacion"]=="-borrar registro-"){
						$lext["ids"]["familiares"]-=1;
						array_pop($lext["familiares"]);
					}else{
						echo("<script>alert('Se necesita el nombre del familiar.')</script>");
					}
				}
			}elseif ($_POST["bidioma"]){
				$consulta="delete from personal.curriculum_idiomas where id_legajo=".$h_id_legajo;
				sql($consulta, "No se pudieron borrar los datos de los idiomas") or fin_pagina();
				for ($i=0; $i<count($lext["idiomas"]); $i++){
					if ($lext["idiomas"][$i]["idioma"]){
						$consulta="insert into personal.curriculum_idiomas (id_legajo, idioma, lee, escribe)
							values ($h_id_legajo, '".$lext["idiomas"][$i]["idioma"]."', "
								.(($lext["idiomas"][$i]["lee"])?"'".$lext["idiomas"][$i]["lee"]."'":"n").", "
								.(($lext["idiomas"][$i]["escribe"])?"'".$lext["idiomas"][$i]["escribe"]."'":"n").")";
						$rta_consulta=sql($consulta, "No se pudieron guardar los datos de los idiomas") or fin_pagina();
					}else{
						$lext["ids"]["idiomas"]-=1;
						array_pop($lext["idiomas"]);
					}
				}
			}elseif ($_POST["guardar_referencias"]){
				$consulta="delete from personal.curriculum_referencia where id_legajo=".$h_id_legajo;
				sql($consulta, "No se pudieron borrar los datos de las referencias") or fin_pagina();
				for ($i=0; $i<count($lext["referencias"]); $i++){
					if (($lext["referencias"][$i]["empleador"])&&($lext["referencias"][$i]["tareas"])){
						$consulta="insert into personal.curriculum_referencia (id_legajo, empleador, domicilio_empresa, referencias, domicilio, desde, hasta, telefono, tareas, certificado)
							values ($h_id_legajo, '".$lext["referencias"][$i]["empleador"]."', "
								.(($lext["referencias"][$i]["domicilio_empresa"])?"'".$lext["referencias"][$i]["domicilio_empresa"]."'":"null").", "
								.(($lext["referencias"][$i]["referencias"])?"'".$lext["referencias"][$i]["referencias"]."'":"null").", "
								.(($lext["referencias"][$i]["domicilio"])?"'".$lext["referencias"][$i]["domicilio"]."'":"null").", "
								.(($lext["referencias"][$i]["desde"])?"'".$lext["referencias"][$i]["desde"]."'":"null").", "
								.(($lext["referencias"][$i]["hasta"])?"'".$lext["referencias"][$i]["hasta"]."'":"null").", "
								.(($lext["referencias"][$i]["telefono"])?"'".$lext["referencias"][$i]["telefono"]."'":"null").", '"
								.$lext["referencias"][$i]["tareas"]."', "
								.(($lext["referencias"][$i]["certificado"])?"'".$lext["referencias"][$i]["certificado"]."'":"n").")";
						$rta_consulta=sql($consulta, "No se pudieron guardar los datos de las referencias") or fin_pagina();
					}elseif (!$lext["referencias"][$i]["empleador"]){
						$lext["ids"]["referencias"]-=1;
						array_pop($lext["referencias"]);
					}else{
						echo("<script>alert('Se necesita indicar la tarea desempeñada.')</script>");
					}
				}
			}elseif ($_POST["guardar_ausentismo"]){
				$consulta="delete from personal.ausentismo where id_legajo=".$h_id_legajo;
				sql($consulta, "No se pudieron borrar los datos de ausentismo") or fin_pagina();
				for ($i=0; $i<count($lext["ausentismo"]); $i++){
					if ($lext["ausentismo"][$i]["agno"]!="-borrar-"){
						$rta_consulta=sql("select * from personal.ausentismo where id_legajo=".$h_id_legajo." and agno=".$lext["ausentismo"][$i]["agno"]);
						if ($rta_consulta->recordCount()==0){
							$consulta="insert into personal.ausentismo (id_legajo, agno, inpuntualidad, inasistencia, enfermedad, accidente, licencias)
								values ($h_id_legajo, ".$lext["ausentismo"][$i]["agno"].", "
								.(($lext["ausentismo"][$i]["inpuntualidad"])?$lext["ausentismo"][$i]["inpuntualidad"]:"0").", "
								.(($lext["ausentismo"][$i]["inasistencia"])?$lext["ausentismo"][$i]["inasistencia"]:"0").", "
								.(($lext["ausentismo"][$i]["enfermedad"])?$lext["ausentismo"][$i]["enfermedad"]:"0").", "
								.(($lext["ausentismo"][$i]["accidente"])?$lext["ausentismo"][$i]["accidente"]:"0").", "
								.(($lext["ausentismo"][$i]["licencias"])?$lext["ausentismo"][$i]["licencias"]:"0").")";
							$rta_consulta=sql($consulta, "No se pudieron guardar los datos de ausentismo") or fin_pagina();
						}
					}else{
						$lext["ids"]["ausentismo"]-=1;
						array_pop($lext["ausentismo"]);
					}
				}
			}elseif ($_POST["guardar_suspensiones"]){
				$consulta="delete from personal.suspensiones where id_legajo=".$h_id_legajo;
				sql($consulta, "No se pudieron borrar los datos de suspensiones") or fin_pagina();
				for ($i=0; $i<count($lext["suspensiones"]); $i++){
					if (($lext["suspensiones"][$i]["motivo"])&&($lext["suspensiones"][$i]["dias"])){
						$consulta="insert into personal.suspensiones (id_legajo, fecha, motivo, dias)
							values ($h_id_legajo, "
							.(($lext["suspensiones"][$i]["fecha"])?"'".$lext["suspensiones"][$i]["fecha"]."'":"'".date("Y-m-d")."'").", '"
							.$lext["suspensiones"][$i]["motivo"]."', ".$lext["suspensiones"][$i]["dias"].")";
						$rta_consulta=sql($consulta, "No se pudieron guardar los datos de la suspension") or fin_pagina();
					}elseif ((!$lext["suspensiones"][$i]["motivo"])&&(!$lext["suspensiones"][$i]["dias"])){
						$lext["ids"]["suspensiones"]-=1;
						array_pop($lext["suspensiones"]);
					}else{
						echo("<script>alert('Se necesita indicar el motivo y los días de suspensión.')</script>");
					}
				}
			}elseif ($_POST["guardar_enfermedades"]){
				$consulta="delete from personal.enfermedades where id_legajo=".$h_id_legajo;
				sql($consulta, "No se pudieron borrar los datos de las enfermedades") or fin_pagina();
				for ($i=0; $i<count($lext["enfermedades"]); $i++){
					if ($lext["enfermedades"][$i]["diagnostico"]){
						$consulta="insert into personal.enfermedades (id_legajo, diagnostico, fecha)
							values ($h_id_legajo, '".$lext["enfermedades"][$i]["diagnostico"]."', '"
								.(($lext["enfermedades"][$i]["fecha"])?$lext["enfermedades"][$i]["fecha"]:date("Y-m-d"))."')";
						$rta_consulta=sql($consulta, "No se pudieron guardar los datos de la enfermedad") or fin_pagina();
					}elseif (!$lext["enfermedades"][$i]["diagnostico"]){
						$lext["ids"]["enfermedades"]-=1;
						array_pop($lext["enfermedades"]);
					}
				}
			}
		}
		//////////////////////////////////////////////////////////////////////////////////////////
		if (!$_POST["leg_guardar"]){
			$db->SetFetchMode(ADODB_FETCH_ASSOC);
			$consulta="select id_legajo, hr_entra, hr_sale, ubicacion, id_afjp, id_usuario, login,
				estado_civil, fecha_estado_civil, nacionalidad, tipo_nacionalidad, cedula_identidad,
				baja_motivo, baja_observaciones, in_presentador, in_examen_medico, in_fecha, in_sector, in_observaciones,
				in_calificacion, in_seguro_vida_obligatorio, in_seguro_vida, in_art, in_beneficiario, profesion, estudios,
				otros_conocimientos, exhibe_titulos, codigo_postal, in_categoria, in_sueldo_inicial, in_ocupacion ";
			$consulta.= "from personal.legajos left join personal.legajos_ext using (id_legajo) left join sistema.usuarios using (id_usuario)
				where id_legajo=$h_id_legajo";
			$rta_consulta=sql($consulta, "No se pudieron traer los datos extras del legajo") or fin_pagina();
			if ($rta_consulta->recordCount()==1) $lext["ext"]=$rta_consulta->fields;
			if (!$lext["ext"]["fecha_estado_civil"]) $lext["ext"]["fecha_estado_civil"]=date("Y-m-d");
			switch ($estado_civil){
				case 0: $estado_civil="Soltero"; break;
				case 1: $estado_civil="Casado"; break;
				case 2: $estado_civil="Divorciado"; break;
				case 3: $estado_civil="Viudo"; break;
				default: $estado_civil="Desconocido";
			}
			$lext["ext"]["estado_civil_text"]=$estado_civil;
		}
		$rta_consulta=sql("select * from personal.promociones join personal.categorias using (id_categoria)
			where id_legajo=".$h_id_legajo, "promociones") or fin_pagina();
		$i=0;
		while(!$rta_consulta->EOF){
			$lext["promociones"][$i++]=$rta_consulta->fields;
			$rta_consulta->moveNext();
		}
		for ($j=$i; $j<$i+1; $j++) $lext["promociones"][$j]=array("id_legajo"=>"", "fecha"=>"", "id_categoria"=>"", "comentario"=>"", "nombre"=>"", "descripcion"=>"");

		$rta_consulta=sql("select * from personal.familia where id_legajo=".$h_id_legajo, "familia") or fin_pagina();
		$i=0;
		while(!$rta_consulta->EOF){
			$lext["familiares"][$i++]=$rta_consulta->fields;
			$rta_consulta->moveNext();
		}
		for ($j=$i; $j<$i+5; $j++) $lext["familiares"][$j]=array("id_familia"=>"", "id_legajo"=>"", "relacion"=>"",
			"nombre_apellido"=>"", "fecha_nacimiento"=>"", "domicilio"=>"", "dni"=>"");

		$rta_consulta=sql("select * from personal.curriculum_idiomas where id_legajo=".$h_id_legajo, "idiomas") or fin_pagina();
		$i=0;
		while(!$rta_consulta->EOF){
			$lext["idiomas"][$i++]=$rta_consulta->fields;
			$rta_consulta->moveNext();
		}
		for ($j=$i; $j<$i+2; $j++) $lext["idiomas"][$j]=array("id_curriculum_idiomas"=>"", "id_legajo"=>"", "idioma"=>"", "lee"=>"n", "escribe"=>"n");

		$rta_consulta=sql("select * from personal.curriculum_referencia where id_legajo=".$h_id_legajo, "referencias") or fin_pagina();
		$i=0;
		while(!$rta_consulta->EOF){
			$lext["referencias"][$i++]=$rta_consulta->fields;
			$rta_consulta->moveNext();
		}
		for ($j=$i; $j<$i+1; $j++) $lext["referencias"][$j]=array("id_curriculum_referencia"=>"", "id_legajo"=>"", "empleador"=>"",
			"domicilio_empresa"=>"", "desde"=>"", "hasta"=>"", "tareas"=>"", "certificado"=>"n", "referencia"=>"",
			"domicilio"=>"", "telefono"=>"");

		$rta_consulta=sql("select * from personal.ausentismo where id_legajo=".$h_id_legajo, "ausentismo") or fin_pagina();
		$i=0;
		while(!$rta_consulta->EOF){
			$lext["ausentismo"][$i++]=$rta_consulta->fields;
			$rta_consulta->moveNext();
		}
		for ($j=$i; $j<$i+1; $j++) $lext["ausentismo"][$j]=array("id_legajo"=>"", "agno"=>"2005", "inpuntualidad"=>"0",
			"inasistencia"=>"0", "enfermedad"=>"0", "accidente"=>"0", "licencias"=>"0");

		$rta_consulta=sql("select * from personal.suspensiones where id_legajo=".$h_id_legajo, "suspensiones") or fin_pagina();
		$i=0;
		while(!$rta_consulta->EOF){
			$lext["suspensiones"][$i++]=$rta_consulta->fields;
			$rta_consulta->moveNext();
		}
		for ($j=$i; $j<$i+1; $j++) $lext["suspensiones"][$j]=array("id_suspension"=>"", "id_legajo"=>"", "fecha"=>"", "motivo"=>"", "dias"=>"");

		$rta_consulta=sql("select * from personal.enfermedades where id_legajo=".$h_id_legajo, "enfermedades") or fin_pagina();
		$i=0;
		while(!$rta_consulta->EOF){
			$lext["enfermedades"][$i++]=$rta_consulta->fields;
			$rta_consulta->moveNext();
		}
		for ($j=$i; $j<$i+1; $j++) $lext["enfermedades"][$j]=array("id_legajo"=>"", "fecha"=>"", "diagnostico"=>"");

	}else{//inicializar vacío
		$lext["ext"]=array("id_legajo"=>"", "estado_civil"=>"0", "fecha_estado_civil"=>date("Y-m-d"), "nacionalidad"=>"argentino",
			"tipo_nacionalidad"=>"0", "cedula_identidad"=>"", "baja_motivo"=>"", "baja_observaciones"=>"", "in_presentador"=>"",
			"in_examen_medico"=>"n", "in_fecha"=>"", "in_sector"=>"", "in_observaciones"=>"", "in_calificacion"=>"",
			"in_seguro_vida_obligatorio"=>"", "in_seguro_vida"=>"", "in_art"=>"", "in_beneficiario"=>"", "in_ocupacion"=>"",
			"profesion"=>"", "estudios"=>"", "otros_conocimientos"=>"", "exhibe_titulos"=>"n", "codigo_postal"=>"",
			"estado_civil_text"=>"Soltero", "hr_entra"=>"08:00:00", "hr_sale"=>"18:00:00", "in_categoria"=>"1", "id_afjp"=>"",
			"login"=>"", "id_usuario"=>"");
		for ($j=0; $j<1; $j++) $lext["promociones"][$j]=array("id_legajo"=>"", "fecha"=>"", "id_categoria"=>"", "comentario"=>"", "nombre"=>"", "descripcion"=>"");
		for ($j=0; $j<5; $j++) $lext["familiares"][$j]=array("id_familia"=>"", "id_legajo"=>"", "relacion"=>"",
			"nombre_apellido"=>"", "fecha_nacimiento"=>"", "domicilio"=>"", "dni"=>"");
		for ($j=0; $j<2; $j++) $lext["idiomas"][$j]=array("id_curriculum_idiomas"=>"", "id_legajo"=>"", "idioma"=>"", "lee"=>"n", "escribe"=>"n");
		for ($j=0; $j<1; $j++) $lext["referencias"][$j]=array("id_curriculum_referencia"=>"", "id_legajo"=>"", "empleador"=>"",
			"domicilio_empresa"=>"", "desde"=>"", "hasta"=>"", "tareas"=>"", "certificado"=>"n", "referencia"=>"",
			"domicilio"=>"", "telefono"=>"");
		for ($j=0; $j<1; $j++) $lext["ausentismo"][$j]=array("id_legajo"=>"", "agno"=>"2005", "inpuntualidad"=>"0",
			"inasistencia"=>"0", "enfermedad"=>"0", "accidente"=>"0", "licencias"=>"0");
		for ($j=0; $j<1; $j++) $lext["suspensiones"][$j]=array("id_suspension"=>"", "id_legajo"=>"", "fecha"=>"", "motivo"=>"", "dias"=>"");
		for ($j=0; $j<1; $j++) $lext["enfermedades"][$j]=array("id_legajo"=>"", "fecha"=>"", "diagnostico"=>"");
	}
	$db->SetFetchMode(ADODB_FETCH_DEFAULT);
	if (($_POST["guardar_promocion"])||($_POST["guardar_familiares"])||($_POST["bidioma"])||
		($_POST["guardar_referencias"])||($_POST["guardar_ausentismo"])||($_POST["guardar_suspensiones"])||
		($_POST["guardar_enfermedades"])){
		if (($_POST["leg_nuevo"] == "1")||($_POST["nuevo_legajo"])) {
			echo("<script>alert('Debe crear el legajo primero');</script>");
			form_modificar(true);
		}else form_modificar(false, $h_id_legajo);
	}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////

$sql = "SELECT id_evaluador FROM evaluadores WHERE id_usuario=".$_ses_user["id"];
$result = sql($sql) or fin_pagina();
$es_evaluador = $result->fields["id_evaluador"] or $es_evaluador = 0;

if ($_POST["leg_historial"]) {
     $h_id_legajo = $_POST["leg_id_legajo"];
     $sql="update legajos set activo=0 where id_legajo=$h_id_legajo";
     sql($sql) or fin_pagina();
     header("Location:listado_legajos.php");
}


$id_legajo=$parametros["id_legajo"] or $id_legajo=$_POST["h_id_legajo"];
/*if($id_legajo!="")
{
$sel_sum="select * from personal.sumarios_personal where id_legajo=$id_legajo";
$sumario=sql($sel_sum,"No se pudo recuperar los sumarios") or fin_pagina();
$can_sum=$sumario->RecordCount();

while (!$sumario->EOF)
{

   $id_sumario=$sumario->fields["id_sumario_personal"];
?>
    <script>
	var sumario_<?=$id_sumario?>=new Array();
	</script>
	<?php
	if($sumario->fields["fecha"])
	{
	$datos_t=$sumario->fields["fecha"];
	$datos_t=ereg_replace("\r\n","<br>",$datos_t);
	$datos_t=ereg_replace("\n","<br>",$datos_t);
	$datos_t=ereg_replace("'","",$datos_t);
	}
	if($sumario->fields["titulo"])
	{
	$datos_d=$sumario->fields["titulo"];
	$datos_d=ereg_replace("\r\n","<br>",$datos_d);
	$datos_d=ereg_replace("\n","<br>",$datos_d);
	$datos_d=ereg_replace("'","",$datos_d);
	}

	if($sumario->fields["descripcion"])
	{ //da error cuando la descripcion tiene enter
	$datos_tel=$sumario->fields["descripcion"];
	$datos_tel=ereg_replace("\r\n","<br>",$datos_tel);
	$datos_tel=ereg_replace("\n","<br>",$datos_tel);
	$datos_tel=ereg_replace("'","",$datos_tel);
	}
	?>
	<script>
	sumario_<?=$id_sumario?>["fecha"]="<?=$datos_t?>";
	sumario_<?=$id_sumario?>["titulo"]="<?=$datos_d?>";
	sumario_<?=$id_sumario?>["descripcion"]="<?=$datos_tel?>";
	</script>
<?
$sumario->MoveNext();
}
}*/
echo $html_header;

?>
<script>
var img_ext='<?=$img_ext='../../imagenes/drop2.gif' ?>';//imagen extendido
var img_cont='<?=$img_cont='../../imagenes/dropdown2.gif' ?>';//imagen contraido
function muestra_tabla(obj_tabla, nro){
	oimg=eval("document.all.imagen_"+nro);//objeto tipo IMG
 	if (obj_tabla.style.display=='none'){
 		obj_tabla.style.display='inline';
    oimg.show=0;
    oimg.src=img_cont;
    if (nro==1) oimg.title='Ocultar tabla';
		else oimg.title='Imagen no identificada';
	}else{
		obj_tabla.style.display='none';
    oimg.show=1;
		oimg.src=img_ext;
		if (nro==1) oimg.title='Mostrar tabla';
		else oimg.title='Imagen no identificada';
  }
}

/*
function set_datos(valor)
{
    var info=eval("sumario_"+valor);
    if(info["fecha"]!="null")
            document.all.fecha_sum.value=info["fecha"];
            else
            document.all.fecha_sum.value="";
    if(info["titulo"]!="null")
            document.all.titulo_sum.value=info["titulo"];
    if(info["descripcion"]!="null")
            document.all.desc_sum.value=info["descripcion"];
            else
            document.all.desc_sum.value="";
    var p=0;

}*/

</script>
<?

if ($parametros["cmd"] == "modificar") {
	$h_id_legajo = $parametros["id_legajo"];
	form_modificar(false, $h_id_legajo);
}elseif ($_POST["leg_guardar"]) {
	$error = 0;
	$nombre = $_POST["leg_nombre"] or Error("Falta ingresar el nombre");
	$apellido = $_POST["leg_apellido"] or Error("Falta ingresar el apellido");
	$dni = $_POST["leg_dni"] or Error("Falta ingresar el número de DNI");
	$fecha_nacimiento = $_POST["leg_fecha_nacimiento"];
	$lugar_nacimiento = $_POST["leg_lugar_nacimiento"];
	$nacionalidad = $_POST["leg_nacionalidad"];
	$domicilio = $_POST["leg_domicilio"] or Error("Falta ingresar el domicilio");
	$tel_particular = $_POST["leg_telefono_particular"];
	$tel_celular = $_POST["leg_telefono_celular"];
	$localidad = $_POST["leg_localidad"];
	$provincia = $_POST["leg_provincia"];
	$em_nombre = $_POST["leg_em_nombre"];
	$em_telefono = $_POST["leg_em_telefono"];
	$em_direccion = $_POST["leg_em_direccion"];
	$em_relacion = $_POST["leg_em_relacion"];
	$comentarios = $_POST["leg_comentarios"];
	$evaluador = $_POST["leg_evaluador"] or $evaluador = "NULL";
	$id_legajo = $_POST["leg_id_legajo"];
	$usuario = $_POST["leg_usuario"] or $usuario = "NULL";
	// estas son todos los campos q se agregaron para completar datos para
	// las liquidaciones de sueldo
	$fecha_ingreso= Fecha_db($_POST['leg_fecha_ingreso']);
	$fecha_egreso=Fecha_db($_POST['leg_fecha_egreso']);
	$cuil=$_POST['leg_cuil'];
	// esto es xq si se da d baja un empleado el tipo d liq pasa a ser honorarios
	// para el control dl libro d sueldo
	if ($fecha_egreso!="") $tipo_liq=2;
	else $tipo_liq=$_POST['leg_tipo'] or $tipo_liq=1;

	$id_tarea=$_POST['leg_tarea'];
	$id_calif=$_POST['leg_calif'];  
  $id_afjp=$_POST['leg_afjp'];
	$tipo_jub=$_POST['leg_jub'];
	$caja_ahorro=$_POST['leg_caja_ahorro'];
	/////////////////////////////////////////////
	if ($fecha_nacimiento != "") {
		if (FechaOk($fecha_nacimiento)) {
			$fecha_nacimiento_db = "'".Fecha_db($fecha_nacimiento)."'";
		}
		else {
			Error("El formato de la fecha de nacimiento no es válido");
		}
	}
	else {
		$fecha_nacimiento_db="null";
	}
	if (!$error) {
		if ($_POST["leg_nuevo"] == "1") {
			$db->StartTrans();
			$sql = "INSERT INTO legajos (apellido,nombre,dni,fecha_nacimiento,";
			$sql .= "lugar_nacimiento,domicilio,tel_celular,tel_particular,";
			$sql .= "localidad,provincia,em_nombre,em_telefono,em_direccion,";
			$sql .= "em_relacion,comentarios,id_evaluador,id_usuario,";

			$sql .= "fecha_ingreso,cuil,caja_ahorro_pesos_nro,tipo_liq,";
			$sql .= "tipo_jub,id_tarea,id_calificacion";
			$sql .=",id_afjp";
			$sql.=", hr_entra, hr_sale, ubicacion ";
			if ($fecha_egreso!="") $sql.=",fecha_baja)";
			else $sql.=")";

			$sql .= "VALUES ('$apellido','$nombre','$dni',$fecha_nacimiento_db,";
			$sql .= "'$lugar_nacimiento','$domicilio','$tel_celular','$tel_particular',";
			$sql .= "'$localidad','$provincia','$em_nombre','$em_telefono','$em_direccion',";
			$sql .= "'$em_relacion','$comentarios',$evaluador,".(($usuario)?$usuario:"null").",";
			$sql .= (($fecha_ingreso)?"'".$fecha_ingreso."'":"null").",'$cuil','$caja_ahorro',$tipo_liq,";
			$sql .= "$tipo_jub,$id_tarea,$id_calif";
			$sql.=",$id_afjp";
			$sql.=", '".$_POST["sel_horario_entra"]."', '".$_POST["sel_horario_sale"]."', ".$_POST["sel_ubicacion"];
			if ($fecha_egreso!="") $sql.=",'$fecha_egreso')";
			else $sql.=")";
			sql($sql, "No se pudieron cargar los datos en la base de datos") or fin_pagina();
			//$db->Execute($sql) or Error("No se pudieron cargar los datos en la base de datos");
			$sql = "SELECT currval('legajos_id_legajo_seq') AS id_legajo";
			$result = $db->Execute($sql) or Error("No se pudo obtener el id de legajo");
			$id_legajo = $result->fields["id_legajo"];
			////////////////////////////////////////// Gabriel ///////////////////////////////////////////////////////
			$consulta="insert into legajos_ext (id_legajo, estado_civil, fecha_estado_civil, cedula_identidad, nacionalidad,
			  tipo_nacionalidad, baja_motivo, baja_observaciones, in_presentador, in_ocupacion, in_sueldo_inicial, in_examen_medico,
			  in_fecha, in_sector, in_observaciones, in_calificacion, in_seguro_vida_obligatorio, in_seguro_vida,
			  in_art, in_beneficiario, in_categoria, profesion, estudios, codigo_postal, otros_conocimientos,	exhibe_titulos) values (";
			$consulta.=$id_legajo.", "
				.(($lext["ext"]["estado_civil"])?$lext["ext"]["estado_civil"]:"0").", "
				.(($lext["ext"]["fecha_estado_civil"])?"'".$lext["ext"]["fecha_estado_civil"]."'":"'".date("Y-m-d")."'").", "
				.(($lext["ext"]["cedula_identidad"])?"'".$lext["ext"]["cedula_identidad"]."'":"null").", "
				.(($lext["ext"]["nacionalidad"])?"'".$lext["ext"]["nacionalidad"]."'":"'argentino'").", "
				.(($lext["ext"]["tipo_nacionalidad"])?$lext["ext"]["tipo_nacionalidad"]:"0").", "
				.(($lext["ext"]["baja_motivo"])?"'".$lext["ext"]["baja_motivo"]."'":"null").", "
				.(($lext["ext"]["baja_observaciones"])?"'".$lext["ext"]["baja_observaciones"]."'":"null").", "
				.(($lext["ext"]["in_presentador"])?"'".$lext["ext"]["in_presentador"]."'":"null").", "
				.(($lext["ext"]["in_ocupacion"])?"'".$lext["ext"]["in_ocupacion"]."'":"null").", "
				.(($lext["ext"]["in_sueldo_inicial"])?str_replace(",", ".", $lext["ext"]["in_sueldo_inicial"]):"null").", "
				.(($lext["ext"]["in_examen_medico"])?"'".$lext["ext"]["in_examen_medico"]."'":"'n'").", "
				.(($lext["ext"]["in_fecha"])?"'".$lext["ext"]["in_fecha"]."'":"null").", "
				.(($lext["ext"]["in_sector"])?"'".$lext["ext"]["in_sector"]."'":"null").", "
				.(($lext["ext"]["in_observaciones"])?"'".$lext["ext"]["in_observaciones"]."'":"null").", "
				.(($lext["ext"]["in_calificacion"])?"'".$lext["ext"]["in_calificacion"]."'":"null").", "
				.(($lext["ext"]["in_seguro_vida_obligatorio"])?"'".$lext["ext"]["in_seguro_vida_obligatorio"]."'":"null").", "
				.(($lext["ext"]["in_seguro_vida"])?"'".$lext["ext"]["in_seguro_vida"]."'":"null").", "
				.(($lext["ext"]["in_art"])?"'".$lext["ext"]["in_art"]."'":"null").", "
				.(($lext["ext"]["in_beneficiario"])?"'".$lext["ext"]["in_beneficiario"]."'":"null").", "
				.(($lext["ext"]["in_categoria"])?$lext["ext"]["in_categoria"]:"1").", "
				.(($lext["ext"]["profesion"])?"'".$lext["ext"]["profesion"]."'":"null").", "
				.(($lext["ext"]["estudios"])?"'".$lext["ext"]["estudios"]."'":"null").", "
				.(($lext["ext"]["codigo_postal"])?$lext["ext"]["codigo_postal"]:"null").", "
				.(($lext["ext"]["otros_conocimientos"])?"'".$lext["ext"]["otros_conocimientos"]."'":"null").", "
				.(($lext["ext"]["exhibe_titulos"])?"'".$lext["ext"]["exhibe_titulos"]."'":"'n'").")";
			sql($consulta, "No se puedieron guardar los datos extras del legajo") or fin_pagina();
			//////////////////////////////////////////////////////////////////////////////////////////////////////////
			$db->CompleteTrans();
		}
		elseif ($_POST["leg_nuevo"] == "0") {
			$sql = "UPDATE legajos SET apellido='$apellido',nombre='$nombre',";
			$sql .= "dni='$dni',fecha_nacimiento=$fecha_nacimiento_db,";
			$sql .= "lugar_nacimiento='$lugar_nacimiento',domicilio='$domicilio',";
			$sql .= "tel_celular='$tel_celular',tel_particular='$tel_particular',";
			$sql .= "localidad='$localidad',provincia='$provincia',em_nombre='$em_nombre',";
			$sql .= "em_telefono='$em_telefono',em_direccion='$em_direccion',";
			$sql .= "em_relacion='$em_relacion',comentarios='$comentarios',";
			$sql .= "id_evaluador=$evaluador,id_usuario=$usuario, ";
			$sql.="id_afjp=$id_afjp,";
			$sql .= "fecha_ingreso=".(($fecha_ingreso)?"'".$fecha_ingreso."'":"null").",cuil='$cuil', ";
			$sql .= "caja_ahorro_pesos_nro='$caja_ahorro',tipo_liq=$tipo_liq,tipo_jub=$tipo_jub, ";
			$sql .= "id_tarea=$id_tarea,id_calificacion=$id_calif ";
			$sql.=", hr_entra='".$_POST["sel_horario_entra"]."', hr_sale='".$_POST["sel_horario_sale"]."', ubicacion=".$_POST["sel_ubicacion"]." ";
			if ($fecha_egreso!="") $sql.=",fecha_baja='$fecha_egreso'";
			else $sql.=",fecha_baja=null ";


			$sql .= "WHERE id_legajo=$id_legajo";
			$result = sql($sql) or fin_pagina();

			////////////////////////////////////////// Gabriel ///////////////////////////////////////////////////////
			$rta_consulta=sql("select * from legajos_ext where id_legajo=$id_legajo", "No se pudo verificar la existencia de la extensión de legajo") or fin_pagina();
			if ($rta_consulta->recordCount()==1){
				$consulta="update legajos_ext set "
					." estado_civil=".(($lext["ext"]["estado_civil"])?$lext["ext"]["estado_civil"]:"0")
					.", fecha_estado_civil=".(($lext["ext"]["fecha_estado_civil"])?"'".$lext["ext"]["fecha_estado_civil"]."'":"'".date("Y-m-d")."'")
					.", cedula_identidad=".(($lext["ext"]["cedula_identidad"])?"'".$lext["ext"]["cedula_identidad"]."'":"null")
					.", nacionalidad=".(($lext["ext"]["nacionalidad"])?"'".$lext["ext"]["nacionalidad"]."'":"argentino")
					.", tipo_nacionalidad=".(($lext["ext"]["tipo_nacionalidad"])?$lext["ext"]["fipo_nacionalidad"]:"0")
					.", baja_motivo=".(($lext["ext"]["baja_motivo"])?"'".$lext["ext"]["baja_motivo"]."'":"null")
					.", baja_observaciones=".(($lext["ext"]["baja_observaciones"])?"'".$lext["ext"]["baja_observaciones"]."'":"null")
					.", in_presentador=".(($lext["ext"]["in_presentador"])?"'".$lext["ext"]["in_presentador"]."'":"null")
					.", in_ocupacion=".(($lext["ext"]["in_ocupacion"])?"'".$lext["ext"]["in_ocupacion"]."'":"null")
					.", in_sueldo_inicial=".(($lext["ext"]["in_sueldo_inicial"])?str_replace(",", ".", $lext["ext"]["in_sueldo_inicial"]):"null")
					.", in_examen_medico=".(($lext["ext"]["in_examen_medico"])?"'".$lext["ext"]["in_examen_medico"]."'":"'n'")
					.", in_fecha=".(($lext["ext"]["in_fecha"])?"'".$lext["ext"]["in_fecha"]."'":"null")
					.", in_sector=".(($lext["ext"]["in_sector"])?"'".$lext["ext"]["in_sector"]."'":"null")
					.", in_observaciones=".(($lext["ext"]["in_observaciones"])?"'".$lext["ext"]["in_observaciones"]."'":"null")
					.", in_calificacion=".(($lext["ext"]["in_calificacion"])?"'".$lext["ext"]["in_calificacion"]."'":"null")
					.", in_seguro_vida_obligatorio=".(($lext["ext"]["in_seguro_vida_obligatorio"])?"'".$lext["ext"]["in_seguro_vida_obligatorio"]."'":"null")
					.", in_seguro_vida=".(($lext["ext"]["in_seguro_vida"])?"'".$lext["ext"]["in_seguro_vida"]."'":"null")
					.", in_art=".(($lext["ext"]["in_art"])?"'".$lext["ext"]["in_art"]."'":"null")
					.", in_beneficiario=".(($lext["ext"]["in_beneficiario"])?"'".$lext["ext"]["in_beneficiario"]."'":"null")
					.", in_categoria=".(($lext["ext"]["in_categoria"])?$lext["ext"]["in_categoria"]:"1")
					.", profesion=".(($lext["ext"]["profesion"])?"'".$lext["ext"]["profesion"]."'":"null")
					.", estudios=".(($lext["ext"]["estudios"])?"'".$lext["ext"]["estudios"]."'":"null")
					.", codigo_postal=".(($lext["ext"]["codigo_postal"])?$lext["ext"]["codigo_postal"]:"null")
					.", otros_conocimientos=".(($lext["ext"]["otros_conocimientos"])?"'".$lext["ext"]["otros_conocimientos"]."'":"null")
					.", exhibe_titulos=".(($lext["ext"]["exhibe_titulos"])?"'".$lext["ext"]["exhibe_titulos"]."'":"'n'")
					." where id_legajo=".$id_legajo;
				sql($consulta, "No se puedieron actualizar los datos extras del legajo") or fin_pagina();
			}else{
				$consulta="insert into legajos_ext (id_legajo, estado_civil, fecha_estado_civil, cedula_identidad, nacionalidad,
				  tipo_nacionalidad, baja_motivo, baja_observaciones, in_presentador, in_ocupacion, in_sueldo_inicial, in_examen_medico,
				  in_fecha, in_sector, in_observaciones, in_calificacion, in_seguro_vida_obligatorio, in_seguro_vida,
				  in_art, in_beneficiario, in_categoria, profesion, estudios, codigo_postal, otros_conocimientos,	exhibe_titulos) values (";
				$consulta.=$id_legajo.", "
					.(($lext["ext"]["estado_civil"])?$lext["ext"]["estado_civil"]:"0").", "
					.(($lext["ext"]["fecha_estado_civil"])?"'".$lext["ext"]["fecha_estado_civil"]."'":"'".date("Y-m-d")."'").", "
					.(($lext["ext"]["cedula_identidad"])?"'".$lext["ext"]["cedula_identidad"]."'":"null").", "
					.(($lext["ext"]["nacionalidad"])?"'".$lext["ext"]["nacionalidad"]."'":"'argentino'").", "
					.(($lext["ext"]["tipo_nacionalidad"])?$lext["ext"]["tipo_nacionalidad"]:"0").", "
					.(($lext["ext"]["baja_motivo"])?"'".$lext["ext"]["baja_motivo"]."'":"null").", "
					.(($lext["ext"]["baja_observaciones"])?"'".$lext["ext"]["baja_observaciones"]."'":"null").", "
					.(($lext["ext"]["in_presentador"])?"'".$lext["ext"]["in_presentador"]."'":"null").", "
					.(($lext["ext"]["in_ocupacion"])?"'".$lext["ext"]["in_ocupacion"]."'":"null").", "
					.(($lext["ext"]["in_sueldo_inicial"])?str_replace(",", ".", $lext["ext"]["in_sueldo_inicial"]):"null").", "
					.(($lext["ext"]["in_examen_medico"])?"'".$lext["ext"]["in_examen_medico"]."'":"'n'").", "
					.(($lext["ext"]["in_fecha"])?"'".$lext["ext"]["in_fecha"]."'":"null").", "
					.(($lext["ext"]["in_sector"])?"'".$lext["ext"]["in_sector"]."'":"null").", "
					.(($lext["ext"]["in_observaciones"])?"'".$lext["ext"]["in_observaciones"]."'":"null").", "
					.(($lext["ext"]["in_calificacion"])?"'".$lext["ext"]["in_calificacion"]."'":"null").", "
					.(($lext["ext"]["in_seguro_vida_obligatorio"])?"'".$lext["ext"]["in_seguro_vida_obligatorio"]."'":"null").", "
					.(($lext["ext"]["in_seguro_vida"])?"'".$lext["ext"]["in_seguro_vida"]."'":"null").", "
					.(($lext["ext"]["in_art"])?"'".$lext["ext"]["in_art"]."'":"null").", "
					.(($lext["ext"]["in_beneficiario"])?"'".$lext["ext"]["in_beneficiario"]."'":"null").", "
					.(($lext["ext"]["in_categoria"])?$lext["ext"]["in_categoria"]:"1").", "
					.(($lext["ext"]["profesion"])?"'".$lext["ext"]["profesion"]."'":"null").", "
					.(($lext["ext"]["estudios"])?"'".$lext["ext"]["estudios"]."'":"null").", "
					.(($lext["ext"]["codigo_postal"])?$lext["ext"]["codigo_postal"]:"null").", "
					.(($lext["ext"]["otros_conocimientos"])?"'".$lext["ext"]["otros_conocimientos"]."'":"null").", "
					.(($lext["ext"]["exhibe_titulos"])?"'".$lext["ext"]["exhibe_titulos"]."'":"'n'").")";
				sql($consulta, "No se puedieron guardar los datos extras del legajo") or fin_pagina();
			}
			//////////////////////////////////////////////////////////////////////////////////////////////////////////
		}
		else {
			Error("No se pueden guardar los datos");
		}
	}
	if (!$error) {
		$path = MOD_DIR."/personal/fotos";
		$name = $_FILES["leg_foto"]["name"];
		$temp = $_FILES["leg_foto"]["tmp_name"];
		$size = $_FILES["leg_foto"]["size"];
		$type = $_FILES["leg_foto"]["type"];
		$extensiones = array("gif","jpg");
		if ($name) {
			$name = strtolower($name);
			$ext = substr($name,-3);
			if ($ext != "gif" and $ext != "jpg") {
				Error("El formato de la imagen debe ser GIF o JPG");
			}
			$name = "leg_$id_legajo.$ext";
			$ret = FileUpload($temp,$size,$name,$type,$max_file_size,$path,"",$extensiones,"",1,0);
			if ($ret["error"] != 0) {
				Error("No se pudo subir el archivo");
			}
		}
	}
	if (!$error) {
		Aviso("Los datos se cargaron correctamente");
		echo "<br><br><form><center><input type='button' name='volver' value='Volver al listado' onClick=\"document.location='listado_legajos.php';\"></form></center>";
	}
	else {
		if ($_POST["leg_nuevo"] == "1") {
			form_modificar(true);
		}
		elseif ($_POST["leg_nuevo"] == "0") {
			form_modificar(false,$id_legajo);
		}
		else {
			fin_pagina();
		}
	}
}
elseif ($_POST["legajo_personal"]) {
	form_modificar(true,$_POST["id_usuario"]);
}
elseif ($_POST["nuevo_legajo"]) {
	form_modificar(true);
}
else {
	fin_pagina();
}

function form_modificar($nuevo = false,$id = "") {
	global $html_header,$html_root,$bgcolor2,$bgcolor3,$max_file_size,$_ses_user,$es_evaluador;
  global $parametros;
  global $lext, $h_id_legajo;
  ////////////////////////////////////////////////////
  /*echo("<br><br>EXT: ");
  print_r($lext);
  echo("<br><br>POST:");
  print_r($_POST);
  echo("<br><br>");*/
	////////////////////////////////////////////////////
  cargar_calendario();
	echo "<form action='".$_SERVER["PHP_SELF"]."' method=POST enctype='multipart/form-data'>";
	$nombre = $_POST["leg_nombre"];
	$apellido = $_POST["leg_apellido"];
	$dni = $_POST["leg_dni"];
	$fecha_nacimiento = $_POST["leg_fecha_nacimiento"];
	$lugar_nacimiento = $_POST["leg_lugar_nacimiento"];
	$nacionalidad = $_POST["leg_nacionalidad"];
	//$domicilio = $_POST["leg_domicilio"];
	$direccion = $_POST["leg_domicilio"];
	$tel_particular = $_POST["leg_telefono_particular"];
	$tel_movil=$tel_celular = $_POST["leg_telefono_celular"];
	$localidad = $_POST["leg_localidad"];
	$provincia = $_POST["leg_provincia"];
	$em_nombre = $_POST["leg_em_nombre"];
	$em_telefono = $_POST["leg_em_telefono"];
	$em_direccion = $_POST["leg_em_direccion"];
	$em_relacion = $_POST["leg_em_relacion"];
	$comentarios = $_POST["leg_comentarios"];
	$evaluador = $_POST["leg_evaluador"] or $evaluador = "NULL";
	$id_legajo = $_POST["leg_id_legajo"];
	$usuario = $_POST["leg_usuario"] or $usuario = "NULL";
	// estas son todos los campos q se agregaron para completar datos para
	// las liquidaciones de sueldo
	$fecha_ingreso= Fecha_db($_POST['leg_fecha_ingreso']);
	$fecha_egreso=Fecha_db($_POST['leg_fecha_egreso']);
	$cuil=$_POST['leg_cuil'];
	// esto es xq si se da d baja un empleado el tipo d liq pasa a ser honorarios
	// para el control dl libro d sueldo
	if ($fecha_egreso!="") $tipo_liq=2;
	else $tipo_liq=$_POST['leg_tipo'];

	$id_tarea=$_POST['leg_tarea'];
	$id_calif=$_POST['leg_calif'];
  
  $id_afjp=$_POST['leg_afjp'];
	$tipo_jub=$_POST['leg_jub'];
	$caja_ahorro=$_POST['leg_caja_ahorro'];
	/////////////////////////////////////////////
	if ($fecha_nacimiento != "") {
		if (FechaOk($fecha_nacimiento)) {
			$fecha_nacimiento_db = "'".Fecha_db($fecha_nacimiento)."'";
		}
		else {
			Error("El formato de la fecha de nacimiento no es válido");
		}
	}else {
		$fecha_nacimiento_db="";
	}
	

	if (!$nuevo and ($id != "")) {
		$sql = "SELECT apellido,nombre,dni,fecha_nacimiento,lugar_nacimiento,";
		$sql .= "domicilio,tel_celular,tel_particular,localidad,provincia,";
		$sql .= "em_nombre,em_telefono,em_direccion,em_relacion,comentarios,";
		$sql .= "id_evaluador,id_legajo,id_usuario,";

		$sql .= "fecha_ingreso,fecha_baja,cuil,caja_ahorro_pesos_nro,tipo_liq,";
		$sql .= "tipo_jub,id_tarea,id_calificacion ";

		$sql .= "FROM legajos ";

		$sql .= "WHERE id_legajo=".$id;
		$result = sql($sql) or fin_pagina();
		if ($result->RecordCount() == 1) {
			$nombre = $result->fields["nombre"];
			$apellido = $result->fields["apellido"];
			$dni = $result->fields["dni"];
			$fecha_nacimiento = fecha($result->fields["fecha_nacimiento"]);
			$lugar_nacimiento = $result->fields["lugar_nacimiento"];
			$direccion = $result->fields["domicilio"];
			$tel_particular = $result->fields["tel_particular"];
			$tel_movil = $result->fields["tel_celular"];
			$localidad = $result->fields["localidad"];
			$provincia = $result->fields["provincia"];
			$em_nombre = $result->fields["em_nombre"];
			$em_telefono = $result->fields["em_telefono"];
			$em_direccion = $result->fields["em_direccion"];
			$em_relacion = $result->fields["em_relacion"];
			$comentarios = $result->fields["comentarios"];
			$usuario = $result->fields["id_usuario"];
			$evaluador = $result->fields["id_evaluador"];
			$id_legajo = $result->fields["id_legajo"];

			$fecha_ingreso = $result->fields["fecha_ingreso"];
            $fecha_egreso = $result->fields["fecha_baja"];
			$cuil = $result->fields["cuil"];
			$caja_ahorro = $result->fields["caja_ahorro_pesos_nro"];
			$tipo_liq = $result->fields["tipo_liq"];
			$tipo_jub = $result->fields["tipo_jub"];
			
			$id_tarea = $result->fields["id_tarea"];
			$id_calif = $result->fields["id_calificacion"];
			$id_afjp = $result->fields["id_afjp"];

		}
		echo "<input type=hidden name=leg_nuevo value=0>";
		echo "<input type=hidden name=leg_id_legajo value='$id'>";
	}
	elseif ($nuevo and ($id != "")) {
		$sql = "SELECT nombre,apellido,direccion,telefono,celular,id_evaluador ";
		$sql .= "FROM usuarios LEFT JOIN evaluadores USING (id_usuario)";
		$sql .= "WHERE id_usuario=$id";
		$result = sql($sql) or fin_pagina();
		if ($result->RecordCount() == 1) {
			$nombre = $result->fields["nombre"];
			$apellido = $result->fields["apellido"];
			$direccion = $result->fields["direccion"];
			$tel_particular = $result->fields["telefono"];
			$tel_movil = $result->fields["celular"];
		}
		echo "<input type=hidden name=leg_nuevo value=1>";
	}
	else {
		echo "<input type=hidden name=leg_nuevo value=1>";
	}
	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	?>
		<input type="hidden" name="promociones_ids" value="<?=$_POST["promociones_ids"]?>">
		<input type="hidden" name="familiares_ids" value="<?=$_POST["familiares_ids"]?>">
		<input type="hidden" name="idiomas_ids" value="<?=$_POST["idiomas_ids"]?>">
		<input type="hidden" name="referencias_ids" value="<?=$_POST["referencias_ids"]?>">
		<input type="hidden" name="ausentismo_ids" value="<?=$_POST["ausentismo_ids"]?>">
		<input type="hidden" name="suspensiones_ids" value="<?=$_POST["suspensiones_ids"]?>">
		<input type="hidden" name="enfermedades_ids" value="<?=$_POST["enfermedades_ids"]?>">
		<input type="hidden" name="h_id_legajo" value="<?=($_POST["h_id_legajo"] or $h_id_legajo)?>">
	<?
		$nro_legajo=" (legajo nro. $h_id_legajo)";
	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	if ($h_id_legajo) ?><script>document.all.h_id_legajo.value=<?=$h_id_legajo?></script><?;
	echo "<input type='hidden' name='MAX_FILE_SIZE' value='$max_file_size'>\n";
	echo "<br><table width=95% border=1 cellspacing=0 cellpadding=3 bgcolor=$bgcolor2 align=center>";
	echo "<tr><td style=\"border:$bgcolor3;cursor:hand;\" colspan=2 align=center id=mo onclick=\"muestra_tabla(document.all.tabla_datos_personales,0);\">
		<img id=\"imagen_0\" src=\"../../imagenes/dropdown2.gif\" border=0 title=\"Mostrar columnas\" align=\"left\" style=\"cursor:hand;\">
		<font size=+1>Legajo de personal $nro_legajo</font></td></tr>";
	echo "<tr><td colspan=\"2\"><table id=\"tabla_datos_personales\" align=\"center\" border=0>";
	echo "<tr>";
	echo "<td align=center width=50% rowspan=2>";
	echo "<table width=100% border=0>";
	echo "<tr><td align=left colspan=2><b>Datos personales:<b></td></tr>";
	echo "<tr><td align=right width=50%><b><font color='red'>*</font>Apellido:</b></td>";
	echo "<td align=left width=50%><input type=text name=leg_apellido value='$apellido' size=30></td>";
	echo "</tr><tr>";
	echo "<td align=right><b><font color='red'>*</font>Nombres:</b></td>";
	echo "<td align=left><input type=text name=leg_nombre value='$nombre' size=30></td>";
	echo "</tr><tr>";
	echo "<td align=right><font color='red'>*</font><b>D.N.I.:</b></td>";
	echo "<td align=left><input type=text name=leg_dni value='$dni' size=30></td>";
	echo "</tr><tr>";
	echo "<td align=right><b>Cédula de identidad:</b></td>";
	echo "<td align=left><input type=text name=leg_cedula_identidad value='".$lext["ext"]["cedula_identidad"]."' size=30></td>";
	echo "</tr><tr>";
	echo "<td align=right><b>Fecha de nacimiento:</b></td>";
	echo "<td align=left><input type=text name=leg_fecha_nacimiento value='$fecha_nacimiento' size=10 maxlength=10>".link_calendario("leg_fecha_nacimiento")."</td>";
	echo "</tr><tr>";
	echo "<td align=right><b>Lugar de nacimiento:</b></td>";
	echo "<td align=left><input type=text name=leg_lugar_nacimiento value='".$lugar_nacimiento."' size=30></td>";
	echo "</tr><tr>";
	echo "<td align=right><b>Nacionalidad:</b></td>";
	echo "<td align=left><input type=text name=leg_nacionalidad value='".$lext["ext"]["nacionalidad"]."' size=16>";
	g_draw_value_select("sel_tipo_nacionalidad", $lext["ext"]["tipo_nacionalidad"], array(0, 1), array("nativo", "por opción"));
	echo "</td></tr><tr>";
	echo "<td align=right><font color='red'>*</font><b>Domicilio:</b></td>";
	echo "<td align=left><input type=text name=leg_domicilio value='$direccion' size=30></td>";
	echo "</tr><tr>";
	echo "<td align=right><b>Teléfono particular:</b></td>";
	echo "<td align=left><input type=text name=leg_telefono_particular value='$tel_particular' size=30></td>";
	echo "</tr><tr>";
	echo "<td align=right><b>Teléfono móvil:</b></td>";
	echo "<td align=left><input type=text name=leg_telefono_celular value='$tel_movil' size=30></td>";
	echo "</tr><tr>";
	echo "<td align=right><b>Localidad:</b></td>";
	echo "<td align=left><input type=text name=leg_localidad value='$localidad' size=30></td>";
	echo "</tr><tr>";
	echo "<td align=right><b>Código postal:</b></td>";
	echo "<td align=left><input type=text name=leg_codigo_postal value='".$lext["ext"]["codigo_postal"]."' size=30 onchange=\"this.value=this.value.replace(',','.'); return control_numero(this, 'Código postal');\"></td>";
	echo "</tr><tr>";
	echo "<td align=right><b>Provincia:</b></td>";
	echo "<td align=left><input type=text name=leg_provincia value='$provincia' size=30></td>";
	echo "</tr><tr>";
	echo "<td align=right><b>Estado civil (al ".(Fecha($lext["ext"]["fecha_estado_civil"]))."):</b></td>";
	echo "<td align=left>";
	g_draw_value_select("sel_estado_civil", (($lext["ext"]["estado_civil_text"])?$lext["ext"]["estado_civil_text"]:"Desconocido"),
		array(0, 1, 2, 3, 4), array("Soltero", "Casado", "Divorciado", "Viudo", "Desconocido"));
	echo "</td>";
	echo "</tr></table></td>";
	?>
			<td align="center">
				<b>Login de usuario:</b> <input type="text" name="t_login" id="t_login" value="<?=$lext["ext"]["login"]?>">
			</td>
		</tr>
	<?
	echo "<td align=center>";
	if (file_exists(MOD_DIR."/personal/fotos/leg_$id_legajo.gif")) {
		$foto = "fotos/leg_$id_legajo.gif";
	}
	elseif (file_exists(MOD_DIR."/personal/fotos/leg_$id_legajo.jpg")) {
		$foto = "fotos/leg_$id_legajo.jpg";
	}
	else { $foto = "fotos/no_disponible.jpg"; }
	echo "<img width=120 height=120 src='$foto'><br>";
	echo "<br><b>Cargar imagen:</b>&nbsp;";
	echo "<input type=file name='leg_foto' size=15 value=".$_POST["leg_foto"].">";
	echo "</td></tr><tr><td>";
	if ($es_evaluador) {
		echo "<table border=0 width=100% cellpadding=3>";
		echo "<tr><td align=right width=50%><b>Evaluador:</b></td>";
		echo "<td align=left><select name=leg_evaluador>";
		echo "<option value=''>No Asignado";
		$sql = "SELECT nombre,apellido,id_evaluador ";
		$sql .= "FROM evaluadores LEFT JOIN usuarios USING (id_usuario) ";
		$sql .= "ORDER BY apellido,nombre";
		$result_eval = sql($sql) or fin_pagina();
		while (!$result_eval->EOF) {
			echo "<option value='".$result_eval->fields["id_evaluador"]."'";
			if ($evaluador == $result_eval->fields["id_evaluador"]) echo " selected";
			echo ">".$result_eval->fields["apellido"]." ".$result_eval->fields["nombre"];
			$result_eval->MoveNext();
		}
		echo "</select></td></tr></table>";
		echo "<table border=0 width=100% cellpadding=3>";
		echo "<tr><td align=right width=50%><b>Usuario del sistema:</b></td>";
		echo "<td align=left><select name=leg_usuario>";
		echo "<option value=''>Ninguno";
		$sql = "SELECT id_usuario,nombre,apellido FROM usuarios ORDER BY apellido,nombre";
		$result = sql($sql) or fin_pagina();
		while (!$result->EOF) {
			echo "<option value='".$result->fields["id_usuario"]."'";
			if ($usuario == $result->fields["id_usuario"]) echo " selected";
			echo ">".$result->fields["apellido"]." ".$result->fields["nombre"];
			$result->MoveNext();
		}
		echo "</select></td></tr></table>";
	}
	elseif (!$nuevo and ($id != "")) {
		echo "<input type=hidden name=leg_usuario value='$usuario'>";
	}
	elseif ($nuevo and ($id != "")) {
		echo "<input type=hidden name=leg_usuario value='$id'>";
	}
	else {
		echo "<input type=hidden name=leg_usuario value=''>";
	}
	echo "</td>";
	echo "</tr><tr>";
	echo "<td align=center>";
	echo "<table border=0 width=100%>";
	echo "<tr><td align=left colspan=2><b>EMERGENCIAS, contactar a:<b></td>";
	echo "</tr><tr>";
	echo "<td align=right width=50%><b>Apellido y Nombre:</b></td>";
	echo "<td align=left width=50%><input type=text name=leg_em_nombre value='$em_nombre' size=30></td>";
	echo "</tr><tr>";
	echo "<td align=right><b>Teléfonos:</b></td>";
	echo "<td align=left><input type=text name=leg_em_telefono value='$em_telefono' size=30></td>";
	echo "</tr><tr>";
	echo "<td align=right><b>Dirección:</b></td>";
	echo "<td align=left><input type=text name=leg_em_direccion value='$em_direccion' size=30></td>";
	echo "</tr><tr>";
	echo "<td align=right><b>Relación:</b></td><td align=left>";
	g_draw_value_select("leg_em_relacion", $em_relacion, array(" ", "padre", "madre", "hermano", "hijo", "conyugue", "otro"),
								array(" ", "padre", "madre", "hermano", "hijo", "conyugue", "otro"), 1, "style=\"width='100%'\"");
	//echo "<input type=text name=leg_em_relacion value='$em_relacion' size=30>";
	echo "</td>";
	echo "</tr></table></td>";
	echo "<td align=left valign=top><b>Observaciones:</b><br>";
	echo "<textarea style='width=100%' name=leg_comentarios rows=8 cols=30>$comentarios</textarea></td>";
	echo "</tr>";
	echo "</table></td></tr>";
	?>
	<tr>
		<td style=\"border:$bgcolor3;cursor:hand;\" colspan=2 align=center id=mo onclick="muestra_tabla(document.all.tabla_datos_laborales,1);">
			<img id="imagen_1" src="../../imagenes/drop2.gif" border=0 title="Mostrar columnas" align="left" style="cursor:hand;">
			<font size=+1>Datos Laborales</font>
		</td>
	</tr>
	<tr><td colspan="2">
		<table id="tabla_datos_laborales" style="display:none;"><tr><td>
	  <table>
	  	<tr>
	  		<td align=right><b>Presentado por:</b></td>
	      <td align=left><input type=text name=t_presentador value='<?=$lext["ext"]["in_presentador"]?>'></td>
	  	</tr>
	    <tr>
	      <td align=right><b>Fecha de ingreso:</b></td>
	      <td align=left><input type=text name=leg_fecha_ingreso value='<?=fecha($fecha_ingreso)?>' size=10 maxlength=10><?=link_calendario("leg_fecha_ingreso")?></td>
	    </tr>
	    <tr>
	      <td align=right><b>Fecha de egreso:</b></td>
	      <td align=left><input type=text name=leg_fecha_egreso value='<?=fecha($fecha_egreso)?>' size=10 maxlength=10><?=link_calendario("leg_fecha_egreso")?></td>
	    </tr>
	    <tr>
	    	<td align="right"><b>Motivo de la baja:</b></td>
	    	<td align="left"><input type=text name=t_motivo_egreso value='<?=$lext["ext"]["baja_motivo"]?>'></td>
	    </tr>
	    <tr>
	    	<td align="right"><b>Observaciones sobre la baja:</b></td>
	    	<td align="left"><input type=text name=t_observaciones_egreso value='<?=$lext["ext"]["baja_observaciones"]?>'></td>
	    </tr>
	    <tr>
	      <td align=right><b>CUIL:</b></td>
	      <td align=left><input type=text name=leg_cuil value='<?=$cuil?>' size=30></td>
	    </tr>
	    <tr><td colspan="2"><hr><b>Tipo de Liquidación:</b></td></tr>
	    <tr><td align="left" colspan="2">
            <INPUT TYPE='RADIO' name='leg_tipo' value='1' <? if ($tipo_liq==1) echo 'checked'?>>&nbsp;<b>Sueldos y jornales</b>
        </td></tr>
        <tr><td align="left" colspan="2">
             <INPUT TYPE='RADIO' name='leg_tipo' value='2' <?if ($tipo_liq==2) echo 'checked'?>>&nbsp;<b>Honorarios</b>
        </td></tr>
        <tr><td colspan="2"><hr><b>Tarea Desempeñada:</b>
        <? $q_tarea="select * from personal.tareas_desemp";
           $res_q_tarea=sql($q_tarea, "Error al traer los datos de las tareas") or fin_pagina();
           $cant_tareas=$res_q_tarea->RecordCount();
        ?>

             <select name="leg_tarea">
             <? for ($i=0;$i<$cant_tareas;$i++) {
                $id_tarea_res=$res_q_tarea->fields['id_tarea'];
             ?>
              <option value='<?=$id_tarea_res?>'
               <?if ($id_tarea==$id_tarea_res) echo "selected"?>>
               <?=$res_q_tarea->fields['nombre_tarea'];?>
              </option>
             <? $res_q_tarea->MoveNext(); }?>
             </select>
        </td></tr>
        <tr><td colspan="2">
        	<b>Lugar de trabajo:&nbsp;</b>
        	<?
        		$rta_consulta=sql("select * from personal.distrito", "No se pudo conseguir la lista de distritos") or fin_pagina();
        		$distrito_nombre=array();
        		$distrito_id=array();
        		while (!$rta_consulta->EOF){
        			$distrito_id[]=$rta_consulta->fields["id_distrito"];
        			$distrito_nombre[]=$rta_consulta->fields["nombre"];
        			$rta_consulta->moveNext();
        		}
        		g_draw_value_select("sel_ubicacion", (($lext["ext"]["ubicacion"])?$lext["ext"]["ubicacion"]:1), $distrito_id, $distrito_nombre);
?>
        </td>
      </tr>
      <tr>
       	<td align="left" nowrap><b>Sector:</b><input type="text" name="t_sector" value="<?=$lext["ext"]["in_sector"]?>"></td>
       	<td align="left" nowrap><b>Ocupaci&oacute;n:</b>
       	<input type="text" name="t_ocupacion" value="<?=$lext["ext"]["in_ocupacion"]?>"></td>
      </tr>
      <tr>
       	<td align="left"><b>Categor&iacute;a:</b>
       	<?
       		$rta_consulta=sql("select * from categorias", "No se pudieron obtener las categorías") or fin_pagina();
       		$categorias_nombre=array();
       		$categorias_id=array();
       		$categorias_id[0]=0;
       		$categorias_nombre[0]=" ";
       		while (!$rta_consulta->EOF){
       			$categorias_nombre[]=$rta_consulta->fields["nombre"];
       			$categorias_id[]=$rta_consulta->fields["id_categoria"];
       			$rta_consulta->moveNext();
       		}
       		g_draw_value_select("sel_categoria", (($lext["ext"]["in_categoria"])?$categorias_nombre[$lext["ext"]["in_categoria"]]:$categorias_nombre[0]), $categorias_id, $categorias_nombre);
?>
       	</td>
       	<td align="left" nowrap><b>Sueldo inicial: $</b><input type="text" name="t_sueldo_inicial" value="<?=number_format($lext["ext"]["in_sueldo_inicial"], 2, ",", "")?>" onchange="this.value=this.value.replace(',','.'); return control_numero(this, 'Sueldo inicial');"></td>
      </tr>
      <tr>
       	<td align="left" colspan="2"><b>Observaciones del ingreso:</b>
       	<textarea name="ta_observaciones_ingreso" cols="70" rows="4"><?=$lext["ext"]["in_observaciones"]?></textarea></td>
      </tr>
      <tr>
        <td colspan="2" nowrap>
        	<?
        		$horarios=array();
        		for ($i=g_timeToSec("00:00"); $i<g_timeToSec("24:00"); $i+=g_timeToSec("00:30")) $horarios[]=g_secToTime($i);
        	?><b>Horario:</b>&nbsp;desde&nbsp;
        		<?=g_draw_value_select("sel_horario_entra", (($lext["ext"]["hr_entra"])?$lext["ext"]["hr_entra"]:"08:00:00"), $horarios, $horarios)?>
        		&nbsp;hasta&nbsp;
        		<?=g_draw_value_select("sel_horario_sale", (($lext["ext"]["hr_sale"])?$lext["ext"]["hr_sale"]:"18:00:00"), $horarios, $horarios);?>
        </td>
        </tr>
        <tr><td colspan="2"><hr><b>Calificación:</b></td></tr>
        <? $q_calif="select * from personal.calificacion";
           $res_q_calif=sql($q_calif, "Error al traer los datos de las calificaciones") or fin_pagina();
           $cant_calif=$res_q_calif->RecordCount();
        ?>
        <tr><td colspan="2">
            <select name="leg_calif">
            <? for ($i=0;$i<$cant_calif;$i++) {
                $id_calif_res=$res_q_calif->fields['id_calificacion'];
            ?>
              <option value='<?=$id_calif_res?>'
               <?if ($id_calif==$id_calif_res) echo "selected"?>>
               <?=$res_q_calif->fields['nombre_calificacion'];?>
              </option>
            <? $res_q_calif->MoveNext(); }?>
            </select>
        </td></tr>
      </table></td>
	  <td valign="top"><table>	         
	         <tr><td colspan="2"><hr><b>Caja de Ahorro Nro.</b></td></tr>
	         <tr><td>
	           <input type=text name=leg_caja_ahorro value='<?=$caja_ahorro?>' size=30>
	         </td></tr>
	         <tr><td colspan="2"><hr><b>Tipo Jubilación:</b></td></tr>
	         <tr><td align="left">
                 <INPUT TYPE='RADIO' name='leg_jub' value='0' <? if ($tipo_jub==0) echo 'checked'?>>&nbsp;<b>Afjp</b>
            </td>
             	<td align="left" rowspan="2">
             		<b>Nombre de la afjp:</b>
             		<?
             			$rta_consulta=sql("select * from personal.afjp", "No se puede acceder a los datos de afjp") or fin_pagina();
             			$afjp_id=array();
             			$afjp_nombre=array();
             			while (!$rta_consulta->EOF){
             				$afjp_id[]=$rta_consulta->fields["id_afjp"];
             				$afjp_nombre[]=$rta_consulta->fields["nombre_afjp"];
             				$rta_consulta->moveNext();
             			}
             			if ($tipo_jub==1) $afjp_marcada="reparto";
             			elseif ($lext["ext"]["id_afjp"]) $afjp_marcada=$afjp_nombre[$lext["ext"]["id_afjp"]-1];
             			else $afjp_marcada=$afjp_nombre[count($afjp_nombre)-1];
             			g_draw_value_select("leg_afjp", $afjp_marcada, $afjp_id, $afjp_nombre);
             		?>
             	</td>
           	</tr>
             <tr><td align="left" colspan="2">
                 <INPUT TYPE='RADIO' name='leg_jub' value='1' <?if ($tipo_jub==1) echo 'checked'?>>&nbsp;<b>Reparto</b>
             </td></tr>
             <tr>
             	<td align="center" colspan="2">
             		<hr>
             		<b>Examen m&eacute;dico: </b>
             		<input type="checkbox" name="ch_examen_medico" <?=(($lext["ext"]["in_examen_medico"]=='s')?"checked":"")?>>
             	</td>
             </tr>
            <tr>
             	<td align="right"><b>Seguro de vida obligatorio:</b></td>
             	<td align="left"><input type="text" name="t_seguro_obligatorio" value="<?=$lext["ext"]["in_seguro_vida_obligatorio"]?>"></td>
            </tr>
            <tr>
             	<td align="right"><b>Seguro de vida de convenio:</b></td>
             	<td align="left"><input type="text" name="t_seguro_convenio" value="<?=$lext["ext"]["in_seguro_vida"]?>"></td>
            </tr>
            <tr>
             	<td align="right"><b>Beneficiario:</b></td>
             	<td align="left"><input type="text" name="t_beneficiario" value="<?=$lext["ext"]["in_beneficiario"]?>"></td>
            </tr>
            <tr>
             	<td align="right"><b>A.R.T.:</b></td>
             	<td align="left"><input type="text" name="t_art" value="<?=$lext["ext"]["in_art"]?>"></td>
            </tr>
            <tr><td colspan="2"><hr></td></tr>
            <tr>
            	<td colspan="2" align="center" id="ma">Promociones</td>
            </tr>
            <tr>
            	<td colspan="2">
            		<table width="100%" class="bordes">
            			<tr id="ma">
		      	      	<td width="10%">Fecha</td><td>Categor&iacute;a</td><td width="80%">Comentario</td>
    			        </tr>
            <?
            	$rta_consulta=sql("select * from categorias", "No se pudieron obtener las categorías") or fin_pagina();
		       		$categorias_nombre=array();
    		   		$categorias_id=array();
       				$categorias_id[0]=0;
		       		$categorias_nombre[0]=" -- Borrar promoción -- ";
    		   		while (!$rta_consulta->EOF){
       					$categorias_nombre[]=$rta_consulta->fields["nombre"];
		       			$categorias_id[]=$rta_consulta->fields["id_categoria"];
    		   			$rta_consulta->moveNext();
       				}
       				$i=0;
           		for(; $i<count($lext["promociones"]); $i++){
           			?>
           				<tr>
           					<td nowrap>
           						<input type="text" name="t_cat_fecha_<?=$i?>" value="<?=Fecha($lext["promociones"][$i]["fecha"])?>" size=10 maxlength=10>
           						<?=link_calendario("t_cat_fecha_".$i)?>
           					</td>
           					<td>
           						<?
           						if ($lext["promociones"][$i]["nombre"]) $categoria_seleccionada=$lext["promociones"][$i]["nombre"];
           						elseif ($lext["promociones"][$i]["id_categoria"]) $categoria_seleccionada=$categorias_nombre[$lext["promociones"][$i]["id_categoria"]];
           						else $categoria_seleccionada="no seleccionada";
           						g_draw_value_select("sel_cat_categoria_".$i, $categoria_seleccionada, $categorias_id, $categorias_nombre)
           						?>
           					</td>
           					<td>
           						<input type="text" name="t_cat_comentario_<?=$i?>" value="<?=$lext["promociones"][$i]["comentario"]?>" style="width:100%">
           					</td>
           				</tr>
           			<?
           		}
            	$lext["ids"]["promociones"]=$i;
?>
<script>document.all.promociones_ids.value=<?=$i?></script>
					<tr>
						<td colspan="3" align="center">
							<input type="submit" name="guardar_promocion" value="Guardar promoción">
						</td>
					</tr>
				</table>
			</td>
		</tr>
	 </table></td>
	</tr>
	</td></tr></table>
	<tr>
		<td style=\"border:$bgcolor3;cursor:hand;\" colspan=2 align=center id=mo onclick="muestra_tabla(document.all.tabla_familia,2);">
			<img id="imagen_2" src="../../imagenes/drop2.gif" border=0 title="Mostrar columnas" align="left" style="cursor:hand;">
			<font size=+1>Datos familiares</font>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table width="100%" id="tabla_familia" style="display:none;">
				<tr id="mo">
					<td><font color='red'>*</font>Parentesco</td>
					<td><font color='red'>*</font>Nombres y apellidos</td>
					<td width="10%">Fecha nac.</td>
					<td>Domicilio</td>
					<td width="10%">D.N.I.</td>
				</tr>
				<?
				$i=0;
				for(; $i<count($lext["familiares"]); $i++){
				?>
					<tr>
						<td width="15%"><?=g_draw_value_select("sel_relacion_flia_$i", (($lext["familiares"][$i]["relacion"])?$lext["familiares"][$i]["relacion"]:" "), array("-borrar registro-", "padre", "madre", "hermano", "hijo", "conyugue", "otro"),
							array("-borrar registro-", "padre", "madre", "hermano", "hijo", "conyugue", "otro"), 1, "style=\"width='100%'\"")?></td>
						<td width="40%"><input type="text" id="t_nombre_flia_<?=$i?>" name="t_nombre_flia_<?=$i?>" value="<?=$lext["familiares"][$i]["nombre_apellido"]?>" style="width='100%'"></td>
						<td align="center" width="5%" nowrap><input type="text" id="t_fecha_flia_<?=$i?>" name="t_fecha_flia_<?=$i?>" value="<?=Fecha($lext["familiares"][$i]["fecha_nacimiento"])?>" size=10 maxlength=10>
							<?=link_calendario("t_fecha_flia_".$i)?></td>
						<td width="30%"><input type="text" id="t_domicilio_flia_<?=$i?>" name="t_domicilio_flia_<?=$i?>" value="<?=$lext["familiares"][$i]["domicilio"]?>" style="width='100%'"></td>
						<td align="center" width="5%"><input type="text" id="t_dni_flia_<?=$i?>" name="t_dni_flia_<?=$i?>" value="<?=$lext["familiares"][$i]["dni"]?>" size="11"></td>
				<?
				}
				$lext["ids"]["familia"]=$i;
?>
<script>document.all.familiares_ids.value=<?=$i?></script>
				<tr><td colspan="5" align="center"><input type="submit" id="guardar_familiares" value="Guardar datos de familiares" name="guardar_familiares"></td></tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style=\"border:$bgcolor3;cursor:hand;\" colspan=2 align=center id=mo onclick="muestra_tabla(document.all.tabla_conocimientos,3);">
			<img id="imagen_3" src="../../imagenes/drop2.gif" border=0 title="Mostrar columnas" align="left" style="cursor:hand;">
			<font size=+1>Conocimientos y estudios</font>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table width="100%" id="tabla_conocimientos" style="display:none;">
				<tr>
					<td>
						<table width="100%">
							<tr>
								<td id="mo" width="10%" nowrap>Profesi&oacute;n u oficio:</td>
								<td colspan="2"><input type="text" id="t_profesion" name="t_profesion" value="<?=$lext["ext"]["profesion"]?>" style="width:'100%'"></td>
							</tr>
							<tr>
								<td id="mo" nowrap>Estudios cursados:</td>
								<td><input type="text" id="t_estudios" name="t_estudios" value="<?=$lext["ext"]["estudios"]?>" style="width:'100%'"></td>
								<td width="5%" nowrap><b>Exhibe t&iacute;tulos:</b><input type="checkbox" id="chtitulos" <?=((($lext["ext"]["exhibe_titulos"]=='s')||($_POST["chtitulos"]))?"checked":"")?> name="chtitulos"></td>
							</tr>
							<tr>
								<td id="mo" nowrap>Otros conocimientos:</td>
								<td colspan="2"><input type="text" id="t_otros" name="t_otros" value="<?=$lext["ext"]["otros_conocimientos"]?>" style="width:'100%'"></td>
							</tr>
						</table>
					</td>
					<td align="center">
						<table width="95%" class="bordes">
							<tr id="mo">
								<td><font color='red'>*</font>Idioma</td><td width="5%">Lee</td><td width="5%">Escribe</td>
							</tr>
							<tr>
						<?
						$i=0;
						for(; $i<count($lext["idiomas"]); $i++){
							?>
							<tr align="center">
								<td><input type="text" id="t_idioma_<?=$i?>" name="t_idioma_<?=$i?>" value="<?=$lext["idiomas"][$i]["idioma"]?>" style="width:'100%'"></td>
								<td><input type="checkbox" id="ch_lee_<?=$i?>" name="ch_lee_<?=$i?>" <?=((($lext["idiomas"][$i]["lee"]=="s")||($_POST["ch_lee_$i"]))?"checked":"")?>></td>
								<td><input type="checkbox" id="ch_escribe_<?=$i?>" name="ch_escribe_<?=$i?>" <?=((($lext["idiomas"][$i]["escribe"]=="s")||($_POST["ch_escribe_$i"]))?"checked":"")?>></td>
							</tr>
							<?
						}
						$lext["ids"]["idiomas"]=$i;
?>
<script>document.all.idiomas_ids.value=<?=$i?></script>
							</tr>
							<tr>
								<td colspan="3" align="center">
									<input type="submit" id="bidioma" name="bidioma" value="Guardar idioma(s)">
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td width="100%" colspan="2">
						<table width="100%">
							<tr id="mo">
								<td align="center">
									Capacitaciones
										<input type="button" name="b_mostrar_notas" value="Ver Notas" onclick="document.all.tabla_calificaciones.style.display=((document.all.tabla_calificaciones.style.display=='inline')?'none':'inline');">
										<input type="submit" name="poner_calificacion" value="Poner Calificacion">								
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td nowrap>
									<table width="100%" border="0" cellpadding="0">
										<tr>
											<td id="mo" width="90%">Curso</td>
											<td id="mo" nowrap>Calificaci&oacute;n</td>
											<td id="mo" nowrap>Fecha Calificaci&oacute;n</td>
										</tr>
							<?
								$consulta="select * from personal.capacitados join personal.capacitaciones using (id_capacitacion)
									where (id_legajo=".$h_id_legajo.") and (calificacion is not null) order by dictado_hasta";
								$rta_consulta=sql($consulta, "C1338") or fin_pagina();
								$i=0;
								while ($datos_cap[]=$rta_consulta->fetchRow());//{ print_r($datos_cap[$i++]); echo("<br>");}
								for ($i=0; $i<count($datos_cap)-1; $i++){?>
										<tr>
											<td align="left" nowrap><?=$datos_cap[$i]["tema"]?></td>
											<td align="center" bgcolor=<?=(($datos_cap[$i]["calificacion"]>=4)?"#1eaa19":"#fb471e")?>>&nbsp;</td>
											<td align="center" nowrap><?=fecha($datos_cap[$i]["fecha_calificacion"])?></td>
										</tr>
							<?}?>
									</table>
								</td>
								<td>
									<table id="tabla_calificaciones" style="display:none" width="10%" cellpadding="0">
										<tr>
											<td id="mo">Nota</td>
										</tr>
						<?for ($i=0; $i<count($datos_cap)-1; $i++){?>
										<tr>
											<td align="center"><?=$datos_cap[$i]["calificacion"]?></td>
										</tr>
						<?}?>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style=\"border:$bgcolor3;cursor:hand;\" colspan=2 align=center id=mo onclick="muestra_tabla(document.all.tabla_referencias,4);">
			<img id="imagen_4" src="../../imagenes/drop2.gif" border=0 title="Mostrar columnas" align="left" style="cursor:hand;">
			<font size=+1>Empleos anteriores y referencias</font>
		</td>
	</tr>
	<tr>
		<td align="center" colspan="2">
			<table width="100%" id="tabla_referencias" style="display:none;">
			<?
			$i=0;
			for(; $i<count($lext["referencias"]); $i++){
				if ($i>0) echo "<tr><td colspan='4'><hr></td></tr>";
				?>
				<tr>
					<td id="mo"><font color='red'>*</font>Empleador</td>
					<td width="30%"><input type="text" id="t_empleador_<?=$i?>" name="t_empleador_<?=$i?>" value="<?=$lext["referencias"][$i]["empleador"]?>" style="width:'100%'"></td>
					<td id="mo">Direcci&oacute;n empresa</td>
					<td width="30%"><input type="text" id="t_domicilioe_<?=$i?>" name="t_domicilioe_<?=$i?>" value="<?=$lext["referencias"][$i]["domicilio_empresa"]?>" style="width:'100%'"></td>
				</tr>
				<tr>
					<td id="mo">Referencias</td>
					<td><input type="text" id="t_referencias_<?=$i?>" name="t_referencias_<?=$i?>" value="<?=$lext["referencias"][$i]["referencias"]?>" style="width:'100%'">
					<td id="mo">Domicilio</td>
					<td><input type="text" id="t_domicilio_<?=$i?>" name="t_domicilio_<?=$i?>" value="<?=$lext["referencias"][$i]["domicilio"]?>" style="width:'100%'">
				</tr>
				<tr>
					<td id="mo">Per&iacute;odo: </td>
					<td nowrap>
						desde
						<input type="text" id="t_desde_<?=$i?>" name="t_desde_<?=$i?>" value="<?=Fecha($lext["referencias"][$i]["desde"])?>" size=10 maxlength=10>
						<?=link_calendario("t_desde_".$i)?>
						&nbsp;al&nbsp;
						<input type="text" id="t_hasta_<?=$i?>" name="t_hasta_<?=$i?>" value="<?=Fecha($lext["referencias"][$i]["hasta"])?>" size=10 maxlength=10>
						<?=link_calendario("t_hasta_".$i)?>
					</td>
					<td id="mo">Tel&eacute;fono</td><td><input type="text" id="t_telefono_<?=$i?>" name="t_telefono_<?=$i?>" value="<?=$lext["referencias"][$i]["telefono"]?>" style="width:'100%'">
				</tr>
				<tr>
					<td id="mo"><font color='red'>*</font>Tareas</td>
					<td colspan="2"><input type="text" id="t_tareas_<?=$i?>" name="t_tareas_<?=$i?>" value="<?=$lext["referencias"][$i]["tareas"]?>" style="width:'100%'">
					<td id="mo">Certif. <input type="checkbox" id="ch_certificado_<?=$i?>" name="ch_certificado_<?=$i?>" <?=((($lext["referencias"][$i]["certificado"]=="s")||($_POST["ch_certificado_$i"]))?"checked":"")?>></td>
				</tr>
				<?
			}
			$lext["ids"]["referencias"]=$i;
			echo "<tr><td colspan='4'><hr></td></tr>";
?>
<script>document.all.referencias_ids.value=<?=$i?></script>
				<tr>
					<td colspan="4" align="center">
						<input type="submit" id="guardar_referencias" name="guardar_referencias" value="Guardar datos de la referencia">
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style=\"border:$bgcolor3;cursor:hand;\" colspan=2 align=center id=mo onclick="muestra_tabla(document.all.tabla_disciplina,5);">
			<img id="imagen_5" src="../../imagenes/drop2.gif" border=0 title="Mostrar columnas" align="left" style="cursor:hand;">
			<font size=+1>Asistencia y disciplina</font>
		</td>
	</tr>
	<tr>
		<td align="center" colspan="2">
			<table width="100%"  id="tabla_disciplina" style="display:none;"><tr><td align="center">
				<table width="95%" class="bordes">
				<tr id="mo"><td colspan="6">Ausentismo</td></tr>
				<tr id="mo">
					<td><font color='red'>*</font>A&ntilde;o</td><td>Faltas puntualidad</td><td>Inasistencias</td><td>Enfermedad</td><td>Accidentes</td><td>Licencias especiales</td>
				</tr>
				<tr>
				<?
				$i=0;
				for(; $i<count($lext["ausentismo"]); $i++){
					?>
					<tr align="center">
						<td width="25%"><?=g_draw_range_select("sel_agno_".$i, (($lext["ausentismo"][$i]["agno"])?$lext["ausentismo"][$i]["agno"]:date("Y")), 1995, date("Y"), 1, "><option value='-borrar-'>-borrar-<b></b")?></td>
						<td width="15%"><input type="text" id="t_inpuntualidad_<?=$i?>" name="t_inpuntualidad_<?=$i?>" value="<?=$lext["ausentismo"][$i]["inpuntualidad"]?>" style="width:'100%'"></td>
						<td width="15%"><input type="text" id="t_inasistencia_<?=$i?>" name="t_inasistencia_<?=$i?>" value="<?=$lext["ausentismo"][$i]["inasistencia"]?>" style="width:'100%'"></td>
						<td width="15%"><input type="text" id="t_enfermedad_<?=$i?>" name="t_enfermedad_<?=$i?>" value="<?=$lext["ausentismo"][$i]["enfermedad"]?>" style="width:'100%'"></td>
						<td width="15%"><input type="text" id="t_accidente_<?=$i?>" name="t_accidente_<?=$i?>" value="<?=$lext["ausentismo"][$i]["accidente"]?>" style="width:'100%'"></td>
						<td width="15%"><input type="text" id="t_licencias_<?=$i?>" name="t_licencias_<?=$i?>" value="<?=$lext["ausentismo"][$i]["licencias"]?>" style="width:'100%'"></td>
					</tr>
					<?
				}
				$lext["ids"]["ausentismo"]=$i;
?>
<script>document.all.ausentismo_ids.value=<?=$i?></script>
					<tr>
						<td colspan="6" align="center">
							<input type="submit" id="guardar_ausentismo" name="guardar_ausentismo" value="Guardar datos de ausentismo">
						</td>
					</tr>
				</table>
			</td></tr><tr><td align="center">
				<table width="95%" class="bordes">
				<tr id="mo"><td colspan="3">Suspensiones</td></tr>
				<tr id="mo">
					<td width="10%"><font color='red'>*</font>Fecha</td><td width="80%"><font color='red'>*</font>Motivos</td><td width="10%"><font color='red'>*</font>D&iacute;as</td>
				</tr>
				<tr>
				<?
				$i=0;
				for(; $i<count($lext["suspensiones"]); $i++){
					?>
					<tr align="center">
						<td nowrap>
							<input type="text" id="t_fecha_susp_<?=$i?>" name="t_fecha_susp_<?=$i?>" value="<?=Fecha($lext["suspensiones"][$i]["fecha"])?>" size=10 maxlength=10>
							<?=link_calendario("t_fecha_susp_".$i)?>
						</td>
						<td><input type="text" id="t_motivos_<?=$i?>" name="t_motivos_<?=$i?>" value="<?=$lext["suspensiones"][$i]["motivo"]?>" style="width:'100%'"></td>
						<td><input type="text" id="t_dias_<?=$i?>" name="t_dias_<?=$i?>" value="<?=$lext["suspensiones"][$i]["dias"]?>" style="width:'100%'" onkeypress="return filtrar_teclas(event,'0123456789');"></td>
					</tr>
					<?
				}
				$lext["ids"]["suspensiones"]=$i;
?>
<script>document.all.suspensiones_ids.value=<?=$i?></script>
					<tr>
						<td colspan="3" align="center">
							<input type="submit" id="guardar_suspensiones" name="guardar_suspensiones" value="Guardar suspensiones">
						</td>
					</tr>
				</table>
			</td></tr><tr><td align="center">
				<table width="95%" class="bordes">
				<tr id="mo"><td colspan="2">Enfermedades</td></tr>
				<tr id="mo">
					<td><font color='red'>*</font>Fecha</td><td><font color='red'>*</font>Diagn&oacute;stico</td>
				</tr>
				<tr>
				<?
				$i=0;
				for(; $i<count($lext["enfermedades"]); $i++){
					?>
					<tr align="center">
						<td width="10%" nowrap>
							<input type="text" id="t_fecha_enf_<?=$i?>" name="t_fecha_enf_<?=$i?>" value="<?=Fecha($lext["enfermedades"][$i]["fecha"])?>" size=10 maxlength=10>
							<?=link_calendario("t_fecha_enf_".$i)?>
						</td>
						<td width="90%"><input type="text" id="t_diagnostico_<?=$i?>" name="t_diagnostico_<?=$i?>" value="<?=$lext["enfermedades"][$i]["diagnostico"]?>" style="width:'100%'"></td>
					</tr>
					<?
				}
				$lext["ids"]["enfermedades"]=$i;
?>
<script>document.all.enfermedades_ids.value=<?=$i?></script>
					<tr>
						<td colspan="2" align="center">
							<input type="submit" id="guardar_enfermedades" name="guardar_enfermedades" value="Guardar datos sobre enfermedades">
						</td>
					</tr>
				</table>
			</td></tr></table>
		</td>
	</tr>

<?/***************** ACA EMPIEZA ACCIDENTES LABORALES *********************/
if ($id_legajo) {
?>
	<tr>
		<td style=\"border:$bgcolor3;cursor:hand;\" colspan=2 align=center id=mo onclick="muestra_tabla(document.all.tabla_acc_lab,6);">
			<img id="imagen_6" src="../../imagenes/drop2.gif" border=0 title="Mostrar columnas" align="left" style="cursor:hand;">
			<font size=+1>Accidentes Laborales</font>
		</td>
	</tr>
	<tr>
		<?
		//$id_legajo=$parametros["id_legajo"];
		$sql=" select * from personal.accidentes_lab where id_legajo=$id_legajo order by fech_inicio ";
		$result_acc_lab=sql($sql,"<br>$sql Error al traer los Accidentes<br>") or fin_pagina();
		?>
		<td align="center" colspan="2">
			<table width="100%"  id="tabla_acc_lab" style="display:none;"><tr><td align="center">
				<table width="95%" class="bordes">

					<?$accion=$parametros["accion"];?>
					<tr>
						<td align="center" colspan="3">
							<b><font size='3' color='red'><?=$accion?></font></b>
						</td>
					</tr>

					<tr>
	    				<td align=right id=mo width="20%">Fecha Accidente</td>
	    				<td align=right id=mo width="55%">Titulo</td>
	    				<td align=right id=mo width="25%">A.R.T.</td>
  					</tr>
					<?
					while (!$result_acc_lab->EOF) {
					$ref = encode_link("accidente_lab_admin.php",array("id_accidentes_lab"=>$result_acc_lab->fields['id_accidentes_lab'],"pagina"=>"modificar_legajo.php","id_legajo"=>$id_legajo));
    				$onclick_elegir="location.href='$ref'";
					?>
					<tr <?=atrib_tr()?> onclick="<?=$onclick_elegir?>">
						 <td align="center"><?=fecha($result_acc_lab->fields['fech_inicio']);?></td>
					     <td ><?=$result_acc_lab->fields['titulo']?></td>
					     <td ><?=$result_acc_lab->fields['art'];?></td>
					</tr>
					<?
					$result_acc_lab->MoveNext();
					}?>
				</table>

				 	<tr>
						<td colspan="3" align="center"><br>
							<?$ref = encode_link("accidente_lab_admin.php",array("id_legajo"=>$id_legajo,"pagina"=>"modificar_legajo.php"));
    						$onclick_elegir="location.href='$ref'";
    						?>
							<input type="button" name="nuevo_acc_lab" value="Nuevo Accidente Laboral" onclick="<?=$onclick_elegir?>">
						</td>
					</tr>
					<tr>
						<td>
							&nbsp;
						</td>
					</tr>
			</table>
		</td>
	</tr>

<?
}
/************ TERMINA ACCIDENTES LABORALES *******************/

/************************Tabla Sumario*******************************/
if($id_legajo!="")
{?>
<tr>
		<td style=\"border:$bgcolor3;cursor:hand;\" colspan=2 align=center id=mo onclick="muestra_tabla(document.all.tabla_sumario,7);">
			<img id="imagen_7" src="../../imagenes/drop2.gif" border=0 title="Mostrar columnas" align="left" style="cursor:hand;">
			<font size=+1>Sumario/Incidentes</font>
		</td>
	</tr>
	<tr><td colspan="2">
	
	<?
	
	$sel_sum="select * from personal.sumarios_personal where id_legajo=$id_legajo";
	$sumario=sql($sel_sum,"No se pudo recuperar los sumarios") or fin_pagina();
	$can_sum=$sumario->RecordCount();

	?>

		<table id="tabla_sumario" style="display:none;" align="center" width="80%"><tr><td>
	    <table align="center" width="100%" border="1">
	  	<?
	  	if($can_sum==0)
	  	{
	  	?>
	    <tr>
	      <td align="center"><font color="Red" size="2">El Empleado  no tiene Sumarios/Incidentes</font></td>
	    </tr>
	    <?}
	    else {?>
	    <tr id="mo">
	  		<td width="15%"><b>Fecha</b></td>
	        <td width="85%"><b>Titulo</b></td>
	  	</tr>
	   <? $i=1;
	    while($i<=$can_sum)
	    {

        $link = encode_link("nuevo_sumario.php",array("id_sumario"=>$sumario->fields['id_sumario_personal'],"id_legajo"=>$id_legajo));
        $link1="window.open('$link','','top=50, left=170, width=800, height=600, scrollbars=1, status=1,directories=0');";
		//tr_tag("target=_blanck $link");
	    ?>
	      <tr onclick="<?=$link1?>" style="cursor: pointer;" bgcolor=#B7C7D0>
	      <td><b><?=Fecha($sumario->fields['fecha'])?></b></td>
	      <td><b><?=$sumario->fields['titulo']?></b></td>
	    </tr>
	    <?
	    $sumario->MoveNext();
	    $i++;
	    }

        }

        $link = encode_link("nuevo_sumario.php",array("id_legajo"=>$id_legajo,"pagina"=>1));
        $link1="window.open('$link','','top=50, left=170, width=800, height=600, scrollbars=1, status=1,directories=0');";
        
        ?>
	    <tr>
	      <td align="center" colspan="2"><input type="button" name=nuevo value='Nuevo Sumario' onclick="<?=$link1?>"></td>
	    </tr>

	   
	 </table>
	 
	</td>
	</tr>
	</table></td></tr>



	<?

}
/**********************Fin Tabla Sumario************************/	

	echo "<tr>";
	echo "<td style=\"border:$bgcolor3;\" align=center colspan=2><br>";
	
		echo "<input style='width:160' type='submit' name='leg_guardar' value='Guardar'>&nbsp;&nbsp;&nbsp;";

	
	echo "<input style='width:160' type='button' name='leg_volver' value='Volver al listado' onClick=\"document.location='listado_legajos.php';\">";
    echo "<input style='width:160' type='submit' name='leg_historial' value='Pasar a Historial'>";

    echo "<br><br></td></tr>";
    echo "</table><br>";
}
fin_pagina();
?>