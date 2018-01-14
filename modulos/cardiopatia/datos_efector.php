<?php
require_once ("../../config.php");
//echo $html_header;

 $id_efector=$_POST["elegido"];
 if ($id_efector > 0) {
        $query = "SELECT * FROM cardiopatia.efector where id_efector=$id_efector";
        
        $res_efector = sql($query) or die();

        $res_json[] = array(
          "id_efector"    => $res_efector->fields["id_efector"],
          "nombre"        => $res_efector->fields["nombre"],
          "domicilio"     => $res_efector->fields["domicilio"],
          "cuit"          => $res_efector->fields["cuit"],
          "cbu"           => $res_efector->fields["cbu"],
          "numero_cuenta" => $res_efector->fields["numero_cuenta"],
          );

 echo json_encode($res_json);

 //echo ($res_json["nombre"]);	
};//del if

 $id_prestacion=$_POST["id_prestacion"];

 if ($id_prestacion > 0) {
        if ($id_prestacion<21 || $id_prestacion>=28){
        $query = "SELECT * FROM cardiopatia.diagnostico WHERE id_codigo_actual=$id_prestacion";
        $res_prestacion = sql($query) or die();
        $ret = '<option value="0">Seleccione</option>';
        while (!$res_prestacion->EOF) {
	   		$ret .= '<option value="' . $res_prestacion->fields['diagnostico'] . '">';
          	$ret .= $res_prestacion->fields['diagnostico'] . '</option>';
          	$res_prestacion->MoveNext();
	       };

$sql_prest="select id_nomenclador from cardiopatia.prestaciones where id_prestacion='$id_prestacion'";
$res_prest= sql ($sql_prest) or die();
$id_nomenclador=$res_prest->fields['id_nomenclador'];	
$sql_nomen="select * from cardiopatia.nomenclador where id_nomenclador='$id_nomenclador'";
$res_nomen=sql($sql_nomen) or die();
$modulo=utf8_encode($res_nomen->fields["modulo"]);
$codigo=$res_nomen->fields["codigo_anterior"];

$select_modulo ='<option value=0>Seleccione</option>';
$select_modulo .='<option value=dia_estada_prequirurgico>Dia Estada Prequirurgicos</option>';
$select_modulo .='<option value=acto_quirurgico>Acto Quirurgico</option>';
$select_modulo .='<option value=dia_estada_postquirurgico_uti>Dia Estada Postquirurgico UTI</option>';
$select_modulo .='<option value=medicacion_postquirurgica>Medicacion Postquirurgica</option>';
$select_modulo .='<option value=dia_estada_postquirurgico_con_medicacion_uti>Dia Estada Postquirurgico con Medicacion UTI</option>';
$select_modulo .='<option value=dia_estada_postquirurgico_en_sala_comun>Dia Estada Postquirurgico en Sala Comun</option>';


$info[]=array (
		"ret" => $ret,
		"modulo" => $modulo,
		"codigo" => $codigo,
    "select_modulo" => $select_modulo);

echo json_encode($info);
  }//del segundo if
  else {

$ret="Sin Diagnostico";
$sql_prest="select id_nomenclador,valor as precio from cardiopatia.prestaciones where id_prestacion='$id_prestacion'";
$res_prest= sql ($sql_prest) or die();
$id_nomenclador=$res_prest->fields['id_nomenclador'];
$precio=number_format($res_prest->fields['precio'],2,'.',''); 
$sql_nomen="select * from cardiopatia.nomenclador where id_nomenclador='$id_nomenclador'";
$res_nomen=sql($sql_nomen) or die();
$modulo=utf8_encode($res_nomen->fields["modulo"]);
$codigo=$res_nomen->fields["codigo_anterior"];

$info[]=array (
    "ret" => $ret,
    "modulo" => $modulo,
    "codigo" => $codigo,
    "precio" => $precio);

echo json_encode($info);

  }
};//del primer if

$modulo=$_POST["modulo"];
$prestacion_1=$_POST["prestacion_1"];

 if ($modulo!="" and $prestacion_1!="") {
 		$sql_prest_1="SELECT id_nomenclador FROM cardiopatia.prestaciones WHERE id_prestacion='$prestacion_1'";
		$res_prest_1= sql ($sql_prest_1) or die();
		$id_nomenclador_1=$res_prest_1->fields['id_nomenclador'];	
		$sql_nomen_1="SELECT $modulo as precio FROM cardiopatia.nomenclador WHERE id_nomenclador='$id_nomenclador_1'";
		$res_nomen_1=sql($sql_nomen_1) or die();
		$precio=number_format($res_nomen_1->fields["precio"],2,'.','');
		
echo json_encode($precio);
    };?>