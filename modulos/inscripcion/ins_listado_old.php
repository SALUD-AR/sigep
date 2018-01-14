<?php
require_once("../../config.php");

Header('Content-Type: text/html; charset=LATIN1');

variables_form_busqueda("ins_listado_old");
$usuario1=$_ses_user['id'];
$user_name=$_ses_user['name'];
$orden = array(
        "default" => "1",
        "1" => "beneficiarios.numero_doc",
		"3" => "efe_conv.nombre",       
       );
$filtro = array(		
		"numero_doc" => "N�mero de Documento",		
		"apellido_benef" => "Apellido",
		"efe_conv.nombre" => "Efector",
		);
       
if ($cmd == "")  $cmd="n";

$datos_barra = array(
     array(
        "descripcion"=> "No Enviados",
        "cmd"        => "n"
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


$sql_tmp="SELECT 
			beneficiarios.id_beneficiarios, 
			beneficiarios.clave_beneficiario, 
			beneficiarios.apellido_benef, 
			beneficiarios.nombre_benef, 
			beneficiarios.fecha_nacimiento_benef, 
			beneficiarios.fecha_inscripcion, 
			beneficiarios.estado_envio, 
			beneficiarios.numero_doc, 
			efe_conv.nombre 
			FROM 
			uad.beneficiarios
			left join nacer.efe_conv on beneficiarios.cuie_ea=efe_conv.cuie";

if ($cmd=="n")
    $where_tmp=" (uad.beneficiarios.estado_envio='n' and tipo_ficha='1' and activo !='0') "; // Muestro los no enviados

if ($cmd=="e")
    $where_tmp=" (uad.beneficiarios.estado_envio='e' and tipo_ficha='1')"; // Muestro todos los enviados incluso los borrados
    
    
if ($cmd=="todos")
    $where_tmp=" ( tipo_ficha='1')"; //Muestro todo enviado, no enviado y borrados en ambos casos

echo $html_header;

if (permisos_check("inicio","permiso_genera_archivo_inscripcion")) $permiso="";
else $permiso="disabled";

if ($result->fields['estado_envio'] == 'e') {
	$estenvio = 'Enviado';
}?>

<form name=form1 action="ins_listado_old.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	    &nbsp;&nbsp;<input type='button' name="nuevo" value='Nuevo Dato' onclick="document.location='ins_admin_old.php';">
	    &nbsp;&nbsp;<input type=submit name="generarnino" value='Generar Archivo' <?=($_ses_user['name']=="sebastian lohaiza")?"":"disabled"?>>
	  </td>
     </tr>
</table>

<?$result = sql($sql) or die;

if ($_POST['generarnino']){
		
		$resultN=sql("select * from uad.archivos_enviados where id_archivos_enviados in (select max(id_archivos_enviados) from  uad.archivos_enviados)" ) or die;
    	$resultN->movefirst();
    	$id_nov = $resultN->fields['cantidad_registros_enviados'];
    	if ($id_nov == null) {$id_nov = 0;}

		$sql_tmp_arch="SELECT beneficiarios.*, efe_conv.nombre FROM uad.beneficiarios
			left join nacer.efe_conv on beneficiarios.cuie_ea=efe_conv.cuie";

		$result1=sql($sql_tmp_arch . " where beneficiarios.estado_envio='n' and tipo_ficha='1'" ) or die;
    	$result1->movefirst();
    	$user = $result1->fields['usuario_carga'];
    	
    	if (!$result1->EOF) {
    	  	
    	$resultP=sql("select * from uad.parametros") or die;
   		$resultP->movefirst();
   		$cod_uad = $resultP->fields['codigo_uad'];
  		$cod_prov = $resultP->fields['codigo_provincia'];
  		
  		
  		$resultU=sql("select id_usuario from sistema.usuarios where substr(usuarios.nombre,0,10)='$user'");
  		$id_user = $resultU->fields['id_usuario'];
  		

/////HEADER
    		$contenido.="H";
    		$contenido.=chr(9);
			$contenido.=date("d/m/Y");
			//$contenido.=chr(9);
			//$contenido.=$result1->fields['id_localidad'];
			$contenido.=chr(9);
			//$contenido.=$cod_uad; //$id_user; //10
			$contenido.=$id_user;
			$contenido.=chr(9);

    		if (!$resultP->EOF) {

    		$contenido.=$cod_prov;	//--2	Dos Primeras Letras? O el Id?
    		$contenido.=chr(9);
	  		$contenido.=$cod_uad; //UAD	//3	Ejemplo?
	  		$contenido.=chr(9);
    		$cod_ci = $resultP->fields['codigo_ci'];
			$contenido.=$cod_ci;
			$contenido.=chr(9);
    		

			//genero nombre de archivo
			$filename= 'A'.$cod_prov.$cod_uad.$cod_ci.$seq.'.txt';

			//creo y abro el archivo
    		if (!$handle = fopen($filename, "w")) { //'a'
        	 echo "No se Puede abrir ($filename)";
         	exit;
    		}else {
    			ftruncate($handle,filesize($filename));
     		}
			// fin gen archivo, sigo con la cadenas
			
			$contenido.=chr(9);
    		}
    		
    		//$result1AE=sql("select max(id_archivos_enviados) from uad.archivos_enviados") or die;
    		//$result1AE->movefirst();
    		//if (!$resultAE->EOF) {
			//$contenido.=$result1AE->fields['id_archivos_enviados'];  //secuencia
			//$contenido.=chr(9);
    		//}
/*VersionAplicativo	10	Agregado en versi�n 2. Si no viene nada, asumimos que es la versi�n anterior. La versi�n del aplicativo indica si vienen o no vienen la info de campos modificados.
En la versi�n 2.0, este campo vendr� con el texto �2.0�
*/			$contenido.=$seq;
    		$contenido.=chr(9);
			$contenido.="4.1";
			$contenido.=chr(9);
			$contenido.="\n";

			$where.=0;
    	while (!$result1->EOF) {
			$where.=',';

/////////DATOS
			$contenido.="D";
			$contenido.=chr(9);
			$id_beneficiario = $result1->fields['clave_beneficiario'];
			/*$where.=$id_beneficiario;*/
			$where.=$result1->fields['id_beneficiarios'];
			
    		if (strlen($id_beneficiario) < 16) {$id_beneficiario = str_repeat("0",16-strlen($id_beneficiario)).$id_beneficiario;}
    	
			$contenido.=$id_beneficiario;
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
			if (($id_categoria==4)or($id_categoria==7))$id_categoria=5;
			else $id_categoria=6;
			$contenido.=$id_categoria;	//1	Valores de 1 a 4
			$contenido.=chr(9);
			$contenido.=$result1->fields['fecha_nacimiento_benef'];	//10	AAAA-MM-DD (A�o, Mes, D�a)
			$contenido.=chr(9);
			$indigena = $result1->fields['indigena'];
			$contenido.=$indigena ;	//1	S/N
			$contenido.=chr(9);
			$id = $result1->fields['id_lengua'];
			if (is_numeric($id) == 0) { $id = 0;}
			$contenido.=$id;	//5	N�mero de identificaci�n de lengua
			$contenido.=chr(9);
    		$id = $result1->fields['id_tribu'];
			if (is_numeric($id) == 0) { $id = 0;}
			//$tribu = str_replace(null,0,$result1->fields['id_tribu']);
			$contenido.=$id;	//5	N�mero de tribu
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
			$contenido.=0;//$result1->fields['tutor_tipo_relacion'];	//1	
			$contenido.=chr(9);
			$contenido.=substr($result1->fields['fecha_inscripcion'],0,10);	//10	
			$contenido.=chr(9);
			//cambio formato de fecha
			$fecha_carga=substr($result1->fields['fecha_carga'],0,10);
			$fechaParaInsertar= '1899-12-30';
			/*$fechaExplode = explode("/", $fecha_carga);
			$fechaParaInsertar = date("Y-m-d", mktime(0,0,0,$fechaExplode[1], $fechaExplode[0], $fechaExplode[2]));*/
			// inserto nueva fecha
			$contenido.=$fechaParaInsertar;	
			$contenido.=chr(9);
			
			
				
			
			if ($id_categoria != 3) {
				$fecha_d_emb = chr(0);
				$fecha_pr_parto = chr(0);
				$fecha_ef_parto= chr(0);
			}else
			{$fecha_d_emb = $result1->fields['fecha_diagnostico_embarazo'];
			$fecha_pr_parto=$result1->fields['fecha_probable_parto'];	//10	
			$fecha_ef_parto = $result1->fields['fecha_efectiva_parto'];
			if ($fecha_pr_parto == $fecha_carga ) { $fecha_pr_parto = chr(0);}
			if ($fecha_d_emb == $fecha_carga ) { $fecha_d_emb = chr(0);}
			if ((substr($fecha_ef_parto,0,4) < '1980') OR($fecha_ef_parto == $fecha )) {$fecha_ef_parto= chr(0);} 
			}
			
			$fecha_d_emb = $result1->fields['fecha_diagnostico_embarazo'];
			$contenido.=$fecha_d_emb;	//10	
			$contenido.=chr(9);
			
			$sem_emb = $result1->fields['semanas_embarazo']; 	//3
			$contenido.=$sem_emb;	//3	
			$contenido.=chr(9);
				
			$fecha_pr_parto=$result1->fields['fecha_probable_parto'];
			$contenido.=$fecha_pr_parto;
			$contenido.=chr(9);
				
			$fecha_ef_parto=$result1->fields['fecha_efectiva_parto'];
			$contenido.= $fecha_ef_parto;	//10	Fecha del parto o de la interrupci�n del embarazo
			$contenido.=chr(9);
			
			if ($result1->fields['activo'] == 1) {$activo = 'S';} else {$activo = 'N';}
			$contenido.=$activo;	//1	Si/No � Campo para el borrado logico
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
			
			$contenido.= $id_nov; //id_novedad=id_beneficiario
			$contenido.=chr(9);
			$contenido.=$result1->fields['tipo_transaccion']; // TipoNovedad
			$contenido.=chr(9); 
			$contenido.=substr($result1->fields['fecha_carga'],0,10); //FechaNovedad	10	Fecha en la que se produjo la novedad. Fundamentalmente se utilizar� para la fecha de baja.
			$contenido.=chr(9); 
			$contenido.=$cod_prov;//CodigoProvinciaAltaDatos	2	
			$contenido.=chr(9); 
			$contenido.=$cod_uad; //CodigoUADAltaDatos	3
			$contenido.=chr(9); 	
			$contenido.=$cod_ci; //CodigoCIAltaDatos	5
			$contenido.=chr(9); 
			$contenido.=substr($result1->fields['fecha_carga'],0,10); //FechaCarga
			$contenido.=chr(9);
			$contenido.=$cod_ci; // Usuario Carga
			$contenido.=chr(9);
			$contenido.=$cod_uad; // checkSum
			$contenido.=chr(9);
			if ($result1->fields['tipo_transaccion']== 'M'){
				for($i=1; $i<70; $i++){
					$contenido.="1";
				}
				$contenido.=chr(9);
			}
			$contenido.="\n";	
	   		$result1->MoveNext();
    	}
    	
////// TRAILER
    	$contenido.="T";
    	$contenido.=chr(9);
    	$cantidad_registros=$result1->numRows();
		$contenido.=$cantidad_registros; // CantidadRegistros	6	Cantidad de registros que vinieron
		$contenido.="\n";
		
		if ($result1->EOF) {
		if (fwrite($handle, $contenido) === FALSE) {
        		echo "No se Puede escribir  ($filename)";
        		exit;
    		}
		else {	
		echo "El Archivo ($filename) se genero con exito";
        $fecha_carga1=date("Y-m-d");	
		$consulta= "insert into uad.archivos_enviados(fecha_generacion,estado,usuario,nombre_archivo_enviado,cantidad_registros_enviados,id_comienzo_lote) values('$fecha_carga1','E','$user_name','$filename',$cantidad_registros,$id_beneficiario)";
		sql($consulta, "Error al insertar en archivos enviados") or fin_pagina(); 
		$consulta= "UPDATE uad.beneficiarios SET estado_envio='e' WHERE (id_beneficiarios IN ($where) )";
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
    <td align=right id=mo><a id=mo href='<?=encode_link("ins_listado_old.php",array("sort"=>"1","up"=>$up))?>'>Documento</a></td>      	    
    <td align=right id=mo>Apellido</a></td>      	    
    <td align=right id=mo>Nombre</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("ins_listado_old.php",array("sort"=>"3","up"=>$up))?>'>Efector</a></td>
    <td align=right id=mo>F NAC</td>
    <td align=right id=mo>F Ins</td>
    <td align="right" id="mo">Certif.</td>     	    
  </tr>
 <?
   while (!$result->EOF) {
   	$ref = encode_link("ins_admin_old.php",array("id_planilla"=>$result->fields['id_beneficiarios'],"pagina_viene_1"=>"ins_listado_old.php"));   	
    $onclick_elegir="location.href='$ref'";?>
  
    <tr <?=atrib_tr()?>>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['clave_beneficiario']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['numero_doc']?></td>        
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['apellido_benef']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nombre_benef']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['nombre']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=fecha($result->fields['fecha_nacimiento_benef'])?></td>
     <td onclick="<?=$onclick_elegir?>"><?=fecha($result->fields['fecha_inscripcion'])?></td>
     <td align="center">
       	 <?$link=encode_link("certificado_pdf.php", array("id_beneficiarios"=>$result->fields['id_beneficiarios']));	
		   echo "<a target='_blank' href='".$link."' title='Imprime Certificado'><IMG src='$html_root/imagenes/pdf_logo.gif' height='20' width='20' border='0'></a>";?>
     </td>   
   </tr>
	<?$result->MoveNext();
    }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>
