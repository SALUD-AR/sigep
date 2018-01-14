<?php
require_once("../../config.php");
Header('Content-Type: text/html; charset=LATIN1');

variables_form_busqueda("ins_listado");
$usuario1=$_ses_user['id'];
$user_name=$_ses_user['name'];

$orden = array(
        "default" => "1",
        "1" => "beneficiarios.numero_doc",
		"2" => "beneficiario.apellido",
        "3" => "efe_conv.nombre",
        "4" => "beneficiarios.nro_doc_madre",
        "5" => "beneficiarios.nro_doc_padre",
 		"6" => "beneficiarios.nro_doc_tutor",
       );
$filtro = array(		
		"numero_doc" => "Número de Documento",		
		"apellido_benef" => "Apellido",
		"efe_conv.nombre" => "Efector",	
		"nro_doc_madre" => 	"DNI Madre",
		"nro_doc_padre" => "DNI Padre",
        "nro_doc_tutor" => "DNI Tutor",	
		"usuario_carga" => "Usuario de Carga"				
       );
       
if ($cmd == "")  $cmd="n";

$datos_barra = array(
     array(
        "descripcion"=> "No Enviados",
        "cmd"        => "n"
     ),
     array(
        "descripcion"=> "Borrados / No Enviados",
        "cmd"        => "d"
     ),
     array(
        "descripcion"=> "Enviados",
        "cmd"        => "e"
     ),
     array(
        "descripcion"=> "Todos",
        "cmd"        => "todos"
     )
);


generar_barra_nav($datos_barra);

/* Armar nombre del Archivo A
conseguir ultima parte, secuencia final.
 */
 $seq="select last_value as seq_archivo from uad.archivos_enviados_id_archivos_enviados_seq";
 $resultseq = sql($seq) or die;
 $resultseq->movefirst();
 $seq =$resultseq->fields['seq_archivo'] + 1;
 if (strlen($seq) < 5) {$seq = str_repeat("0",5-strlen($seq)).$seq;}
// Fin datos para armar nombre de archivo A
       
$sql_tmp="SELECT beneficiarios.*, efe_conv.nombre FROM uad.beneficiarios
			left join nacer.efe_conv on beneficiarios.cuie_ea=efe_conv.cuie";

if ($cmd=="n")
    $where_tmp=" (uad.beneficiarios.estado_envio='n' and tipo_ficha='2' and activo !='0') "; // Muestro los no enviados

if ($cmd=="d")
    $where_tmp=" (uad.beneficiarios.estado_envio='n' and tipo_ficha='2' and activo = '0') "; // Muestro los no enviados pero borrados
    

if ($cmd=="e")
    $where_tmp=" (uad.beneficiarios.estado_envio='e' and tipo_ficha='2')"; // Muestro todos los enviados incluso los borrados
    
    
if ($cmd=="todos")
    $where_tmp=" ( tipo_ficha='2')"; //Muestro todo enviado, no enviado y borrados en ambos casos


echo $html_header;

if (permisos_check("inicio","genera_archivo_permiso")) $permiso="";
else $permiso="disabled";
?>
<form name=form1 action="ins_listado.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	    &nbsp;&nbsp;<input type='button' name="nuevo" value='Nuevo Dato' onclick="document.location='ins_admin.php'">
	    &nbsp;&nbsp;<input type=submit name="generarnino" value='Generar Archivo' <?=$permiso?>>
	  </td>
     </tr>
</table>

<?$result = sql($sql) or die;

//var_dump($sql);

if ($_POST['generarnino']){
		
		$resultN=sql("select * from uad.archivos_enviados where id_archivos_enviados in (select max(id_archivos_enviados) from  uad.archivos_enviados)" ) or die;
    	$resultN->movefirst();
    	$id_nov = $resultN->fields['cantidad_registros_enviados'];
    	if ($id_nov == null) {$id_nov = 0;}

		$result1=sql($sql_tmp . " where beneficiarios.estado_envio='n'" ) or die;
    	$result1->movefirst();
    	$user = $result1->fields['usuario_carga'];
    	$usuario1=$_ses_user['id'];//usuario de generacion de Archivo A
    	if (!$result1->EOF) {
    	  	
	    	$resultP=sql("select * from uad.parametros", "Error en la tabla de Parametros") or die;
	   		$resultP->movefirst();
	   		if ($resultP->RecordCount()==EOF) $accion("Verifique la tabla de parametros en el esquema UAD");//la consulta era para $result1
	   		$cod_uad = $resultP->fields['codigo_uad'];
	  		$cod_prov = $resultP->fields['codigo_provincia'];
			$cod_ci = $resultP->fields['codigo_ci'];
	  		
	  		
			//*********************HEADER******************************************
    		$contenido.="H";//HEADER
    		$contenido.=chr(9);
			$contenido.=date("Y/m/d");//fecha AAAA-MM-DD
			$contenido.=chr(9);
			$contenido.=$usuario1;//usuario de generacion
			$contenido.=chr(9);
    		$contenido.=$cod_prov;//codigo_provincia
    		$contenido.=chr(9);
	  		$contenido.=$cod_uad;//codigo_uad
	  		$contenido.=chr(9);
			$contenido.=$cod_ci;//codigo_ci
			$contenido.=chr(9);
    		

			//genero nombre de archivo
			$filename= 'A'.$cod_prov.$cod_uad.$cod_ci.$seq.'.txt';

			//creo y abro el archivo
    		if (!$handle = fopen($filename, "w")) { //'a'
        	 echo "No se Puede abrir ($filename)";
         	exit;
    		}else {ftruncate($handle,filesize($filename));
     		}
     		//********************************* fin de HEADER******************************************
     		$contenido.=$seq;//secuencia del archivo
	    		$contenido.=chr(9);
				$contenido.="4.5";//version
				$contenido.=chr(9);
				$contenido.="\n";   		
				
	
	    	//************************************DATOS**************************************************
				$where.=0;
	    	while (!$result1->EOF) {
				$where.=',';
	    		$contenido.="D";
				$contenido.=chr(9);
				$id_beneficiario = $result1->fields['clave_beneficiario'];
				/*$where.=$id_beneficiario;*/
				$where.=$result1->fields['id_beneficiarios'];
				
	    			if (strlen($id_beneficiario) < 16) {$id_beneficiario = str_repeat("0",16-strlen($id_beneficiario)).$id_beneficiario;}
	    	
				$contenido.=$id_beneficiario;//arriba se asigna clave de beneficiario
				$contenido.=chr(9);
				$contenido.=$result1->fields['apellido_benef'];	//30	Uad.Beneficiarios.apellido
				$contenido.=chr(9);
				$contenido.=$result1->fields['nombre_benef'];	//30	Uad.Beneficiarios.nombre
				$contenido.=chr(9);
				$contenido.=$result1->fields['tipo_documento'];	//5	Sigla (DNI, CUIL, etc)
				$contenido.=chr(9);
				$contenido.=$result1->fields['clase_documento_benef'];	//1	Propio o Ajeno? Si es ajeno, seria el dni de quien hace el tramite?
				$contenido.=chr(9);
				$contenido.=$result1->fields['numero_doc'];	//12	
				$contenido.=chr(9);
				$contenido.=$result1->fields['sexo'];	//1	M / F
				$contenido.=chr(9);
				$id_categoria = $result1->fields['id_categoria'];
				$contenido.=$id_categoria;	//1	Valores de 5 Menor a 6 Mayor
				$contenido.=chr(9);
				$contenido.=$result1->fields['fecha_nacimiento_benef'];	//10	AAAA-MM-DD (Año, Mes, Día)
				$contenido.=chr(9);
					//$indigena = $result1->fields['indigena'];
				$contenido.=$result1->fields['indigena'];	//1	S/N
				$contenido.=chr(9);
					$id = $result1->fields['id_lengua'];
					if (is_numeric($id) ==-1) { $id = 0;}
				$contenido.=$id;	//	Número de identificación de lengua
				$contenido.=chr(9);
	    			$id = $result1->fields['id_tribu'];
				if (is_numeric($id) == -1) { $id = 0;}
					//$tribu = str_replace(null,0,$result1->fields['id_tribu']);
				$contenido.=$id;	//	Número de tribu
				$contenido.=chr(9);
				$contenido.=$result1->fields['tipo_doc_madre'];	//5	
				$contenido.=chr(9);
				$contenido.=$result1->fields['nro_doc_madre'];	//12	
				$contenido.=chr(9);
				$contenido.=$result1->fields['apellido_madre'];	//30	
				$contenido.=chr(9);
				$contenido.=$result1->fields['nombre_madre'];	//30	
				$contenido.=chr(9);
				$contenido.=$result1->fields['tipo_doc_padre'];	//5	
				$contenido.=chr(9);
				$contenido.=$result1->fields['nro_doc_padre'];	//12	
				$contenido.=chr(9);
				$contenido.=$result1->fields['apellido_padre'];	//30	
				$contenido.=chr(9);
				$contenido.=$result1->fields['nombre_padre'];	//30	
				$contenido.=chr(9);
				$contenido.=$result1->fields['tipo_doc_tutor'];	//5	
				$contenido.=chr(9);
				$contenido.=$result1->fields['nro_doc_tutor'];	//12	
				$contenido.=chr(9);
				$contenido.=$result1->fields['apellido_tutor'];	//30	
				$contenido.=chr(9);
				$contenido.=$result1->fields['nombre_tutor'];	//30	
				$contenido.=chr(9);
				$contenido.=0;//tutor_tipo_relacion==1?
				$contenido.=chr(9);
				$contenido.=substr($result1->fields['fecha_inscripcion'],0,10);	// Fecha Inscripcion	
				$contenido.=chr(9);
					$fecha_carga=substr($result1->fields['fecha_carga'],0,10);
				$contenido.=substr($result1->fields['fecha_carga'],0,10); //Fecha Alta Efectiva	
				$contenido.=chr(9);
					$sexo=$result1->fields['sexo'];
					$embarazada=$result1->fields['menor_embarazada'];
					if ($sexo == 'M' or $embarazada=='N') {
						$fecha_d_emb = chr(0);
						$fecha_pr_parto = chr(0);
						$fecha_ef_parto= chr(0);
						$fecha_fum=chr(0);
					}else{
						$fecha_d_emb = $result1->fields['fecha_diagnostico_embarazo'];
						$fecha_pr_parto=$result1->fields['fecha_probable_parto'];	//10	
						$fecha_ef_parto =$result1->fields['fecha_efectiva_parto'];
						$fecha_fum=$result1->fields['fum'];
						if(substr($fecha_fum,0,4) < '1980')$fecha_fum=chr(0);
						if ($fecha_ef_parto == $fecha_carga ) { $fecha_ef_parto = '1899-12-30';}
						if ($fecha_pr_parto == $fecha_carga ) { $fecha_pr_parto = chr(0);}
						if ($fecha_d_emb == $fecha_carga ) { $fecha_d_emb = chr(0);}
						if ((substr($fecha_ef_parto,0,4) < '1980') OR($fecha_ef_parto == $fecha )) {$fecha_ef_parto= chr(0);}
					}
				$contenido.=$fecha_d_emb;	//10	
				$contenido.=chr(9);
					//$sem_emb = $result1->fields['semanas_embarazo']; 	//3
				$contenido.=$result1->fields['semanas_embarazo'];	//3	
				$contenido.=chr(9);
					//$fecha_pr_parto=$result1->fields['fecha_probable_parto'];
				$contenido.=$fecha_pr_parto;
				$contenido.=chr(9);
					//$fecha_ef_parto=$result1->fields['fecha_efectiva_parto'];
				$contenido.= $fecha_ef_parto;	//10	Fecha del parto o de la interrupción del embarazo
				$contenido.=chr(9);
					if ($result1->fields['activo'] == 1) {$activo = 'S';} else {$activo = 'N';}
				$contenido.=$activo;	//1	Si/No – Campo para el borrado logico
				$contenido.=chr(9);
				$contenido.=$result1->fields['calle'];	//40	
				$contenido.=chr(9);
				$contenido.=$result1->fields['numero_calle'];	//5	
				$contenido.=chr(9);
				$contenido.=$result1->fields['manzana'];	//5	
				$contenido.=chr(9);
				$contenido.=$result1->fields['piso'];	//5	
				$contenido.=chr(9);
				$contenido.=$result1->fields['dpto'];	//5	
				$contenido.=chr(9);
				$contenido.=$result1->fields['entre_calle_1'];	//40	
				$contenido.=chr(9);
				$contenido.=$result1->fields['entre_calle_2'];	//40	
				$contenido.=chr(9);
				$contenido.=str_replace('-1','',$result1->fields['barrio']);	//40	
				$contenido.=chr(9);
				$contenido.=str_replace('-1','',$result1->fields['municipio']);	//40	
				$contenido.=chr(9);
				$contenido.=str_replace('-1','',$result1->fields['departamento']);	//40	
				$contenido.=chr(9);
				$contenido.=str_replace('-1','',$result1->fields['localidad']);	//40	
				$contenido.=chr(9);
				$contenido.=$result1->fields['cod_pos']; //DomCodigoPostal	
				$contenido.=chr(9);
				$contenido.=$cod_prov;//$result1->fields['provincia_nac'];
				$contenido.=chr(9);
				$contenido.=$result1->fields['telefono'];	//20	
				$contenido.=chr(9);
				$contenido.=$result1->fields['cuie_ea']; //Efector
				$contenido.=chr(9);
				$contenido.=$result1->fields['cuie_ea']; //LugarAtencionHabitual	80	Efector
				$contenido.=chr(9);
				//$id_nov += 1;
				$contenido.= $id_nov; //id_novedad
				$contenido.=chr(9);
				$contenido.=$result1->fields['tipo_transaccion']; // TipoNovedad se deja de usar en la version 4.6, el tipo de novedad no se dejo de utilizar, ERROR SE HABILITA NUEVAMENTE
				$contenido.=chr(9);
				$contenido.=substr($result1->fields['fecha_carga'],0,10); //FechaNovedad	10	Fecha en la que se produjo la novedad. Fundamentalmente se utilizará para la fecha de baja.
				$contenido.=chr(9); 
				$contenido.=$cod_prov;//CodigoProvinciaAltaDatos	2	
				$contenido.=chr(9); 
				$contenido.=$cod_uad; //CodigoUADAltaDatos	3
				$contenido.=chr(9); 	
				$contenido.=$cod_ci; //CodigoCIAltaDatos	5
				$contenido.=chr(9); 
				$contenido.=substr($result1->fields['fecha_carga'],0,10); //FechaCarga
				$contenido.=chr(9);
				$contenido.=$result1->fields['usuario_carga'];//$id_user; //UsuarioCarga 
				$contenido.=chr(9);
				
			if ($result1->fields['tipo_transaccion']== 'M'){
				for($i=1; $i<70; $i++){
					$contenido.="1";
				}
				$contenido.=chr(9);
			}else $contenido.=chr(9);
				$contenido.=$result1->fields['score_riesgo'];
				$contenido.=chr(9); 
					if (($result1->fields['alfabeta'] == 'N')or  ($result1->fields['alfabeta'] == '')) {$alfabeta = '';} 
					if (($result1->fields['alfabeta'] == 'S') && ($result1->fields['estudios'] == '')) {$alfabeta = 'SA';} else {$alfabeta = 'NA';}
		    	    if (($result1->fields['estudios'] == 'INICIAL') && ($result1->fields['estadoest'] == 'I')) {$alfabeta = 'II';}
		    		if (($result1->fields['estudios'] == 'INICIAL') && ($result1->fields['estadoest'] == 'C')) {$alfabeta = 'IC';}
		    		if (($result1->fields['estudios'] == 'PRIMARIO') && ($result1->fields['estadoest'] == 'I')) {$alfabeta = 'PI';}
		    		if (($result1->fields['estudios'] == 'PRIMARIO') && ($result1->fields['estadoest'] == 'C')) {$alfabeta = 'PC';}
		    		if (($result1->fields['estudios'] == 'SECUNDARIO') && ($result1->fields['estadoest'] == 'I')) {$alfabeta = 'SI';}
		    		if (($result1->fields['estudios'] == 'SECUNDARIO') && ($result1->fields['estadoest'] == 'C')) {$alfabeta = 'SC';}
			    	if (($result1->fields['estudios'] == 'TERCIARIO') && ($result1->fields['estadoest'] == 'I')) {$alfabeta = 'TI';}
		    		if (($result1->fields['estudios'] == 'TERCIARIOS') && ($result1->fields['estadoest'] == 'C')) {$alfabeta = 'TC';}
		    		if (($result1->fields['estudios'] == 'UNIVERSITARIO') && ($result1->fields['estadoest'] == 'I')) {$alfabeta = 'UI';}
		    		if (($result1->fields['estudios'] == 'UNIVERSITARIO') && ($result1->fields['estadoest'] == 'C')) {$alfabeta = 'UC';}
				$contenido.=$alfabeta;	//1	Beneficiario Alfabeta
				$contenido.=chr(9);
				$contenido.=$result1->fields['anio_mayor_nivel'];
				$contenido.=chr(9);
					if (($result1->fields['alfabeta_madre'] == 'N')or  ($result1->fields['alfabeta_madre'] == '')) {$alfabeta_madre = '';} 
					if (($result1->fields['alfabeta_madre'] == 'S') && ($result1->fields['estudios_madre'] == '')) {$alfabeta_madre = 'SA';} else {$alfabeta_madre = 'NA';}
		    	    if (($result1->fields['estudios_madre'] == 'INICIAL') && ($result1->fields['estadoest_madre'] == 'I')) {$alfabeta_madre = 'II';}
		    		if (($result1->fields['estudios_madre'] == 'INICIAL') && ($result1->fields['estadoest_madre'] == 'C')) {$alfabeta_madre = 'IC';}
		    		if (($result1->fields['estudios_madre'] == 'PRIMARIO') && ($result1->fields['estadoest_madre'] == 'I')) {$alfabeta_madre = 'PI';}
		    		if (($result1->fields['estudios_madre'] == 'PRIMARIO') && ($result1->fields['estadoest_madre'] == 'C')) {$alfabeta_madre = 'PC';}
		    		if (($result1->fields['estudios_madre'] == 'SECUNDARIO') && ($result1->fields['estadoest_madre'] == 'I')) {$alfabeta_madre = 'SI';}
		    		if (($result1->fields['estudios_madre'] == 'SECUNDARIO') && ($result1->fields['estadoest_madre'] == 'C')) {$alfabeta_madre = 'SC';}
			    	if (($result1->fields['estudios_madre'] == 'TERCIARIO') && ($result1->fields['estadoest_madre'] == 'I')) {$alfabeta_madre = 'TI';}
		    		if (($result1->fields['estudios_madre'] == 'TERCIARIOS') && ($result1->fields['estadoest_madre'] == 'C')) {$alfabeta_madre = 'TC';}
		    		if (($result1->fields['estudios_madre'] == 'UNIVERSITARIO') && ($result1->fields['estadoest_madre'] == 'I')) {$alfabeta_madre = 'UI';}
		    		if (($result1->fields['estudios_madre'] == 'UNIVERSITARIO') && ($result1->fields['estadoest_madre'] == 'C')) {$alfabeta_madre = 'UC';}
				$contenido.=$alfabeta_madre;	//1	Madre Alfabeta
				$contenido.=chr(9);
				$contenido.=$result1->fields['anio_mayor_nivel_madre'];
				$contenido.=chr(9);
					if (($result1->fields['alfabeta_padre'] == 'N')or  ($result1->fields['alfabeta_padre'] == '')) {$alfabeta_padre = '';} 
					if (($result1->fields['alfabeta_padre'] == 'S') && ($result1->fields['estudios_padre'] == '')) {$alfabeta_padre = 'SA';} else {$alfabeta_padre = 'NA';}
		    	    if (($result1->fields['estudios_padre'] == 'INICIAL') && ($result1->fields['estadoest_padre'] == 'I')) {$alfabeta_padre = 'II';}
		    		if (($result1->fields['estudios_padre'] == 'INICIAL') && ($result1->fields['estadoest_padre'] == 'C')) {$alfabeta_padre = 'IC';}
		    		if (($result1->fields['estudios_padre'] == 'PRIMARIO') && ($result1->fields['estadoest_padre'] == 'I')) {$alfabeta_padre = 'PI';}
		    		if (($result1->fields['estudios_padre'] == 'PRIMARIO') && ($result1->fields['estadoest_padre'] == 'C')) {$alfabeta_padre = 'PC';}
		    		if (($result1->fields['estudios_padre'] == 'SECUNDARIO') && ($result1->fields['estadoest_padre'] == 'I')) {$alfabeta_padre = 'SI';}
		    		if (($result1->fields['estudios_padre'] == 'SECUNDARIO') && ($result1->fields['estadoest_padre'] == 'C')) {$alfabeta_padre = 'SC';}
			    	if (($result1->fields['estudios_padre'] == 'TERCIARIO') && ($result1->fields['estadoest_padre'] == 'I')) {$alfabeta_padre = 'TI';}
		    		if (($result1->fields['estudios_padre'] == 'TERCIARIOS') && ($result1->fields['estadoest_padre'] == 'C')) {$alfabeta_padre = 'TC';}
		    		if (($result1->fields['estudios_padre'] == 'UNIVERSITARIO') && ($result1->fields['estadoest_padre'] == 'I')) {$alfabeta_padre = 'UI';}
		    		if (($result1->fields['estudios_padre'] == 'UNIVERSITARIO') && ($result1->fields['estadoest_padre'] == 'C')) {$alfabeta_padre = 'UC';}
				$contenido.=$alfabeta_padre;	//1	Padre Alfabeta
				$contenido.=chr(9);
				$contenido.=$result1->fields['anio_mayor_nivel_padre'];
				$contenido.=chr(9);
					if (($result1->fields['alfabeta_tutor'] == 'N')or  ($result1->fields['alfabeta_tutor'] == '')) {$alfabeta_tutor = '';} 
					if (($result1->fields['alfabeta_tutor'] == 'S') && ($result1->fields['estudios_tutor'] == '')) {$alfabeta_tutor = 'SA';} else {$alfabeta_tutor = 'NA';}
		    	    if (($result1->fields['estudios_tutor'] == 'INICIAL') && ($result1->fields['estadoest_tutor'] == 'I')) {$alfabeta_tutor = 'II';}
		    		if (($result1->fields['estudios_tutor'] == 'INICIAL') && ($result1->fields['estadoest_tutor'] == 'C')) {$alfabeta_tutor = 'IC';}
		    		if (($result1->fields['estudios_tutor'] == 'PRIMARIO') && ($result1->fields['estadoest_tutor'] == 'I')) {$alfabeta_tutor = 'PI';}
		    		if (($result1->fields['estudios_tutor'] == 'PRIMARIO') && ($result1->fields['estadoest_tutor'] == 'C')) {$alfabeta_tutor = 'PC';}
		    		if (($result1->fields['estudios_tutor'] == 'SECUNDARIO') && ($result1->fields['estadoest_tutor'] == 'I')) {$alfabeta_tutor = 'SI';}
		    		if (($result1->fields['estudios_tutor'] == 'SECUNDARIO') && ($result1->fields['estadoest_tutor'] == 'C')) {$alfabeta_tutor = 'SC';}
			    	if (($result1->fields['estudios_tutor'] == 'TERCIARIO') && ($result1->fields['estadoest_tutor'] == 'I')) {$alfabeta_tutor = 'TI';}
		    		if (($result1->fields['estudios_tutor'] == 'TERCIARIOS') && ($result1->fields['estadoest_tutor'] == 'C')) {$alfabeta_tutor = 'TC';}
		    		if (($result1->fields['estudios_tutor'] == 'UNIVERSITARIO') && ($result1->fields['estadoest_tutor'] == 'I')) {$alfabeta_tutor = 'UI';}
		    		if (($result1->fields['estudios_tutor'] == 'UNIVERSITARIO') && ($result1->fields['estadoest_tutor'] == 'C')) {$alfabeta_tutor = 'UC';}
				$contenido.=$alfabeta_tutor;	//1	Tutor Alfabeta
				$contenido.=chr(9);
				$contenido.=$result1->fields['anio_mayor_nivel_tutor'];
				$contenido.=chr(9);
				$contenido.=$result1->fields['mail'];
				$contenido.=chr(9);
				$contenido.=$result1->fields['celular'];
				$contenido.=chr(9);
				$contenido.=$fecha_fum;	//10	AAAA-MM-DD (Año, Mes, Día)
				$contenido.=chr(9);
				$contenido.=$result1->fields['obsgenerales'];
				$contenido.=chr(9);
					if ($result1->fields['discv'] == 'VISUAL') {
						$discapacidad = 'V';
					}elseif ($result1->fields['disca'] == 'AUDITIVA') {
						$discapacidad = 'A';
					}elseif ($result1->fields['discmo'] == 'MOTRIZ') {
						$discapacidad = 'Z';
					}elseif ($result1->fields['discme'] == 'MENTAL') {
						$discapacidad = 'M';
					}elseif ($result1->fields['otradisc'] == 'OTRA DISCAPACIDAD') {
						$discapacidad = 'O';
					}else $discapacidad = '';
				$contenido.=$discapacidad;	//1	Discapacidad
				$contenido.="\n";	
		   		$result1->MoveNext();
	    	}
	    	
	////// TRAILER
	    	$contenido.="T";
	    	$contenido.=chr(9);
	    		$cantidad_registros=$result1->numRows();
			$contenido.=$cantidad_registros; // CantidadRegistros	long==6	Cantidad de registros que vinieron
			$contenido.="\n";
			
			if ($result1->EOF) {
			if (fwrite($handle, $contenido) === FALSE) {
	        		echo "No se Puede escribir  ($filename)";
	        		exit;
	    		}
			else {	
			echo "El Archivo ($filename) se genero con exito";
			
			 $q="select nextval('uad.archivos_enviados_id_archivos_enviados_seq') as id_archivos_enviados";
		    $id_archivos_enviados=sql($q) or fin_pagina();
		    $id_archivos_enviados=$id_archivos_enviados->fields['id_archivos_enviados']; 
			
		 $consulta= "insert into uad.archivos_enviados
						(id_archivos_enviados,fecha_generacion,estado,usuario,nombre_archivo_enviado,cantidad_registros_enviados,id_comienzo_lote) 
						values
						('$id_archivos_enviados', '$fecha_carga','E','$user','$filename',$cantidad_registros,$id_beneficiario)";
			sql($consulta, "Error al insertar en archivos enviados") or fin_pagina(); 
			
			$consulta= "UPDATE uad.beneficiarios SET estado_envio='e' WHERE (id_beneficiarios IN ($where))";
			sql($consulta, "Error al actualizar beneficiarios") or fin_pagina(); 
			}
		}
		else {echo "No hay registros para generar";}
    	fclose($handle);
    	}
		else {echo "No hay registros para generar";}
//var_dump($contenido);

}

?>

<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=10 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$total_muletos?></td>       
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  

  <tr>
  	<td align=right id=mo>Clave Beneficiario</td>      	    
    <td align=right id=mo><a id=mo href='<?=encode_link("ins_listado.php",array("sort"=>"1","up"=>$up))?>'>Documento</a></td>      	    
    <td align=right id=mo>Apellido</a></td>      	    
    <td align=right id=mo>Nombre</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("ins_listado.php",array("sort"=>"3","up"=>$up))?>'>Efector</a></td>
    <td align="right" id="mo">Usuario</td>
    <td align="right" id="mo">Certif.</td>         	    
  </tr>
 <?
   while (!$result->EOF) {
   	$ref = encode_link("ins_admin.php",array("id_planilla"=>$result->fields['id_beneficiarios']));
   	
    $onclick_elegir="location.href='$ref'";?>
  
    <tr <?=atrib_tr()?>>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['clave_beneficiario']?></td>  
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['numero_doc']?></td>        
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['apellido_benef']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nombre_benef']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nombre']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['usuario_carga']?></td>
     <?if ($cmd!="d"){?>
    	 <td align="center">
       	 <?$link=encode_link("certificado_pdf.php", array("id_beneficiarios"=>$result->fields['id_beneficiarios']));	
		   echo "<a target='_blank' href='".$link."' title='Imprime Certificado'><IMG src='$html_root/imagenes/pdf_logo.gif' height='20' width='20' border='0'></a>";?>
       </td>
         <?}?>     
   </tr>
	<?$result->MoveNext();
    }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>
