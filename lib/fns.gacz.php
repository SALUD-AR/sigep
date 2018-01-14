<?



/*****************************************************************************
 *row_count BY GACZ
 *retorna la cantidad de lineas del @string
 *dependiendo de la longitud de linea dada en @line_long o los \n
 *una aplicacion: variar la cantidad de lineas de los textarea de acuerdo al texto
 ****************************************************************************/
function row_count($string, $line_long)
{
 $str_long=strlen($string);
 $rows=0;
 $chars=0;
 for ($i=0; $i < $str_long; $i++ )
 {
	//se debe comparar como string el \n
	if ($string[$i]=="\n" || $chars == $line_long)
 	{
		$chars=0;
 	 	$rows++;
 	}
 	else
 		$chars++;
 }
 return $rows+1;
}


/*****************************************************************************
 * make_options BY GACZ
 * @return void
 * @param resultset los option para imprimir
 * @param value string el nombre del campo para el valor
 * @param text string el nombre del campo para el texto
 * @param selected_value string el valor que deberia seleccionarse
 * @param id string el nombre del campo para el id (opcional)
 * @desc imprime los options en un buffer de retorno
 ****************************************************************************/
function make_options($resultset,$value,$text,$selected_value=false,$id=false) {
	global $html_root;
	$buffer="";
	while (!$resultset->EOF)
	{
		$valor=$resultset->fields[$value];
//		if (strpos($resultset->fields[$id],"'")===false)
			$Id=($id)?"id='".$resultset->fields[$id]."'":"";
//		else
//			$Id=($id)?"id=\"".str_replace("'","",$resultset->fields[$id]) ."\"":"";

		$text2=$resultset->fields[$text];
		$selected=($resultset->fields[$value]==$selected_value)?"selected":"";
   	$buffer.="<option value='$valor' $Id $selected>$text2</option>\n";
   	$resultset->MoveNext();
	}
	return  $buffer;

}
/*****************************************************************************
 * strtoArray BY GACZ
 * @return array de lineas de texto cuya longitud max=@line_long
 * @param string string palabra original
 * @param line_long int longitud maxima de una linea
 * @param barra_n bool indica si se insertan o no los "\n" (default true)
 * @desc divide un string en filas terminadas en "\n" (opcional)
 ****************************************************************************/
function strtoArray($string,$line_long,$barra_n=true)
{
 $str_long=strlen($string);
 $chars=0;
 $first_div=0;
 $substrlong=0;
 for ($i=0; $i < $str_long; $i++ )
 {
	//guardo el ultimo divisor de palabra o de linea
 	switch ($string[$i])
 	{
 		case " ":
 		case ",":
 		case ".":
 		case ";":
 		case ":":
 		case ".":
 		case "\n":
 		case "/":
 		$last_div=$i;
 	}
	//si llego a la cantidad de caracteres maxima
	if ($chars == $line_long-1 || $string[$i]=="\n")
 	{
		if ( ($substrlong=$last_div-$first_div) > 0)
		{
 			$strings[]=	substr($string,$first_div,$substrlong);
	 		$i=$first_div=$last_div=$last_div+1;
		}
 		else
 		{
 			$strings[]=	substr($string,$first_div,$chars+1);
 			$first_div=$last_div=$i;
 		}

 		$chars=0;
 	}
 	else
 		$chars++;
 }

 //en caso de que no se agrego la ultima parte del string
 if ($first_div < $i-1)
	$strings[]=	substr($string,$first_div);

 //añade los \n
 if ($barra_n)
 {
	//la longitud del arreglo
 	$str_count=count($strings);
 	for ($j=0; $j < $str_count; $j++)
 	{
 		//la longitud del string
 		$str_long=strlen($strings[$j]);
		if ($strings[$j][$str_long-1]!="\n")
 			$strings[$j].="\n";
 	}
 }

 return ($strings) ;
}
/*****************************************************************************
 * arraytoStr BY GACZ
 * @return string con el arreglo concatenado
 * @param strs_array array de strings
 * @param start_index int indice desde el cual se empieza a concatenar
 * @param end_index int indice hasta el cual se concatenara (inclusive)
 * @desc une un arreglo de strings (inversa de la funcion anterior)
 ****************************************************************************/
function arraytoStr($strs_array,$start_index=0,$end_index=0)
{
 $str="";
 $end=count($strs_array) - 1;
 if ($end_index===0 || $end_index > $end)
 	$end_index=$end;
 while ($start_index <= $end_index)
	 	$str.=$strs_array[$start_index++];

 return $str;
}

/*****************************************************************************
 * PostvartoArray BY GACZ
 * @return array
 * @param prefijos string
 * @param byRef bool, true indica si el arreglo se pasa con referencias al POST,
 * 										falso el arreglo es una copia de los valores en POST
 * @desc funcion que recupera del post una variable cuyo nombre fue generado
 * por un prefijo/s (separados por coma) y devuelve un arreglo con todos los valores coincidentes
 * si se pasa mas de un prefijo, se devuelve un arreglo de arreglos cuya 1er clave es el prefijo
 * y la segunda clave, es la parte restante del prefijo ej $arr['prefijo']['resto_del_prefijo']
 ****************************************************************************/
function PostvartoArray($prefijos,$byRef=false)
{
	 global $_POST;
	 $prefijos=split(",",$prefijos);
	 foreach ($prefijos as $prefijo)
	 {
	 		//hago una busqueda en POST por cada prefijo
			foreach($_POST as $key => $value)
		 	{
			 	//la clave en POST comienza con el prefijo??
				if (strpos($key,$prefijo)===0)
			 	{
					$index=substr($key,strlen($prefijo));
			 		//es por referencias??
					if ($byRef)
	  				$ret[$prefijo][$index]= &$_POST[$key];
	  			else
	  				$ret[$prefijo][$index]=$value;
		 		}
		 	}
	}
	//se encontro algo??
	if (is_array($ret))
	{
		//se paso un solo prefijo??
		if (count($prefijos)===1)
	  	return $ret[$prefijos[0]];//retorno un arreglo simple (1 dimension)
	  else
	  	return $ret;
	}
	else
	  return false;
}

/*****************************************************************************
replace BY GACZ:
SOPORTA:
  -TRANSACCIONES
  -TABLAS (NO SOLO FILAS)
  -QUE SEA OPCIONAL INDICAR QUE CAMPOS ACTUALIZAR
  -TOMA COMO POLITICA INSERTAR AQUELLAS FILAS CUYA CLAVE CONTENGA UN VALOR NULO
   SOLO EN EL CASO DE QUE LA CLAVE SEA DE UNA SOLA COLUMNA Y NO UN ARRAY

LOS PARAMETROS DE ENTRADA
@tabla -> el nombre de la tabla o vista (con regla de insercion y actualizacion) para actualizar
@array[nro_fila]['nbre_col'] -> este contiene los nbre_de_col y los valores a actualizar o insertar
@clave: array-> la clave por la cual buscar la fila
@campos:array -> los nombres de campos que se debe actualizar
 se puede dar una arreglo con mas campos de los que hay en la tabla
 especificando este parametro, el cual debe contener los campos minimos para poder insertar
 Sino se especifica actualiza todos y puede producir errores si hay campos inexistentes

select setval('compras.fila_id_fila_seq', 8) from compras.fila_id_fila_seq;

 * @return la cantidad de entradas que fallaron
 * @param nbre_tabla nombre de la tabla donde actualizar/insertar
 * @param array una pseudotabla (arreglo asociativo) si se inserto, guarda el id insertado en el campo con el nombre $clave
 * @param clave un arreglo que contiene los nbres de columnas clave
 * @param campos los campos a actualizar (opcional)
 * @desc Trata de reemplazar las entradas de la tabla sino las inserta
 ****************************************************************************/
function replace($nbre_tabla,&$array,$clave,$campos=false)
{
 global $db;
 $ret_value=0;
 reset($array);
 $i=0;//indica si la clave tiene mas de un campo
 $columnas;
 if (!($campos===false))
 {
		if (is_array($campos))
		{
			$columnas=array_values($campos);
	 	   $i=count($campos);
		}
		else
		 return (-1);//"@campos : se esperaba un arreglo"
 }
 if (!is_array($clave))
 	return (-2);//("@clave: se esperaba un arreglo");
 unset($campos);
 $j=0;
 $total_filas=count($array);
 while ($j < $total_filas)
 {
	$campos="";
   $valores="";
 	$q="UPDATE $nbre_tabla set ";
 	if ($i > 0)
	{
	 	$k=0;
	   while ($k < $i )
	   {
	   	$campos.=$columnas[$k];
			$valores.=$array[$j][$columnas[$k]];
	   	$q.=$columnas[$k]."=".$array[$j][$columnas[$k]]." ";

			if ($k+1 < $i)
	   	{
	   		$campos.=",";
	   		$valores.=",";
	   		$q.=", ";
	   	}
	   	$k++;
	   }
	}//de if ($i > 0)
	else
	{
		reset($array[$j]);
		$l=count($array[$j]);
		$k=0;
		while ($col_value=each($array[$j]))
		{
	   	$campos.=$col_value[0];
			$valores.=$col_value[1];
	   	$q.=$col_value[0]."=".$col_value[1]." ";
			if (++$k < $l)
	   	{
	   		$campos.=",";
	   		$valores.=",";
	   		$q.=", ";

	   	}
		}
	}//del else de if ($i > 0)
	$q.=" WHERE ";
	$l=count($clave);//cantidad de columnas que componen la clave
	$k=0;
	foreach ($clave as $clave_n)
	{
		$q.=$clave_n."=".$array[$j][$clave_n];
		if (++$k < $l)
		 $q.=" AND ";
	}
	$exec_value=0;
//	echo "$q<br>";
	//si la clave es de un campo y no se paso el valor OR
	//trato actualizar correctamente
	if (($l==1 && (!isset($array[$j][$clave_n]) || $array[$j][$clave_n]=='')) || $exec_value=sql($q,"<br>Error: $q<br>")or fin_pagina())
	{   //insertamos las filas nuevas
		//si la clave es de un campo
		if ($l==1 && $valores[0]==",")
		{
			//se asume que la clave esta en el primer lugar de los campos
			//y entonces el primer campo del arreglo
			$campos=substr($campos,strlen($clave_n.","));
			$valores=substr($valores,1);
		}//de if ($l==1 && $valores[0]==",")
		//si no intento actualizar OR
		//actualizo y no afecto nada
		if (($exec_value===0) || ($exec_value && $db->Affected_Rows()==0))
		{
			//si la clave es de un solo campo y no viene el valor de la clave
			//deberia ser generada por la secuencia
			if ($l==1 && $array[$j][$clave_n]=="")
			{
				$q2="select nextval('{$nbre_tabla}_{$clave_n}_seq') as id ";
				$r= sql($q2,"<br>Error al traer la secuencia para la fila<br>") or fin_pagina();
				$id=$r->fields['id'];

				$q="INSERT INTO $nbre_tabla ($clave_n,$campos) values ($id,$valores)";
			}
			else
				$q="INSERT INTO $nbre_tabla ($campos) values ($valores)";

			//echo "$q<br>";
			$exec_value= sql($q,"<br>Error al insertar en la fila Nº $j<br>") or fin_pagina();
		}//de if (($exec_value===0) || ($exec_value && $db->Affected_Rows()==0))
	}//de if (($l==1 && (!isset($array[$j][$clave_n]) || $array[$j][$clave_n]=='')) || $exec_value)
	else //actualizamos las filas previamente guardadas
	 $exec_value=sql($q,"<br>Error al actualizar la fila Nº $j de la OC<br>")or fin_pagina();

	if ($exec_value===false)
		$ret_value++;
	elseif($l==1) //si la clave tiene un solo campo
		$array[$j][$clave_n]=$id;
	$j++;
 }//de while ($j < $total_filas)

 return $ret_value;
}//de function replace($nbre_tabla,&$array,$clave,$campos=false)

/*****************************************************************************
 * date_spa BY GACZ
 * @return string fecha formateada
 * @param string formato el formato de fecha igual que en date()
 * @param int fecha_db
 ****************************************************************************/
function date_spa($formato,$fecha_db=false)
{

if ($fecha_db)
	$timestamp=strtotime($fecha_db);

if ($timestamp)
	$fecha=date($formato,$timestamp);
else
	$fecha=date($formato);

if (!(strpos($formato,"l")===false))//dia de la semana texto completo
{
	if ($timestamp)
		$dia_eng=date("l",$timestamp);
	else
		$dia_eng=date("l");

	switch($dia_eng)
	{
	 case "Sunday":$dia="Domingo";break;
	 case "Monday":$dia="Lunes";break;
	 case "Tuesday":$dia="Martes";break;
	 case "Wednesday":$dia="Miércoles";break;
	 case "Thursday":$dia="Jueves";break;
	 case "Friday":$dia="Viernes";break;
	 case "Saturday":$dia="Sabado";break;
	}
}
//reemplaza el/los dias en ingles por el/los dias en español
$fecha=str_replace($dia_eng,$dia,$fecha);

if (!(strpos($formato,"D")===false))//dia de la semana texto 3 letras
{
	if ($timestamp)
		$dia_eng=date("D",$timestamp);
	else
		$dia_eng=date("D");
	switch($dia_eng)
	{
	 case "Sun":$dia="Dom";break;
	 case "Mon":$dia="Lun";break;
	 case "Tue":$dia="Mar";break;
	 case "Wed":$dia="Mie";break;
	 case "Thu":$dia="Jue";break;
	 case "Fri":$dia="Vie";break;
	 case "Sat":$dia="Sab";break;
	}

}

//reemplaza el/los dias en ingles por el/los dias en español
$fecha=str_replace($dia_eng,$dia,$fecha);

if ((!(strpos($formato,"F")===false)) || //mes texto completo
	 (!(strpos($formato,"M")===false)))//mes texto 3 letras
{
	if ($timestamp)
		$mes_eng=date("F",$timestamp);
	else
		$mes_eng=date("F");
	switch($mes_eng)
	{
	 case "Jan":
	 case "January":
	 		$mes="Enero";break;
	 case "Feb":
	 case "February":
	 		$mes="Febrero";break;
	 case "Mar":
	 case "March":
	 		$mes="Marzo";break;
	 case "Apr":
	 case "April":
	 		$mes="Abril";break;
	 case "May":
	 		$mes="Mayo";break;
	 case "Jun":
	 case "June":
	 		$mes="Junio";break;
	 case "Jul":
	 case "July":
	 		$mes="Julio";break;
	 case "Aug":
	 case "August":
	 		$mes="Agosto";break;
	 case "Sep":
	 case "September":
	 		$mes="Septiembre";break;
	 case "Oct":
	 case "October":
	 		$mes="Octubre";break;
	 case "Nov":
	 case "November":
	 		$mes="Noviembre";break;
	 case "Dec":
	 case "December":
	 		$mes="Diciembre";break;
	}
//reemplaza el/los dias en ingles por el/los dias en español
$fecha=str_replace($mes_eng,$mes,$fecha);

}
 return $fecha;
}
//ejemplo de fecha con formato largo
//echo date_spa("l, j \de F \de Y H:i:s");


/*****************************************************************************
 * date2 BY GACZ
 * @return string fecha formateada valor por default "now"
 * @param string formato puedes ser:
 	"L"  formato largo ej. "Miercoles, 21 de Enero de 2004"
 	"LH" formato largo con fecha ej. "Miercoles, 21 de Enero de 2004 22hs"
 	"LHM" formato largo con fecha ej. "Miercoles, 21 de Enero de 2004 22:50hs"
 	"LHMS" formato largo con fecha ej. "Miercoles, 21 de Enero de 2004 22:50:30hs"
 	"S"  formato corto ej. "21/01/2004"
 	"SH" formato corto con fecha ej. "21/01/2004 22hs"
 	"SHM" formato corto con fecha ej. "21/01/2004 22:50hs"
 	"SHMS" formato corto con fecha ej. "21/01/2004 22:50:30hs"
 	valor por default "S"
 * @param int fecha_db
 ****************************************************************************/
function date2($formato=false,$fecha_db=false)
{
		$formato=strtoupper($formato);

		switch ($formato)
		{
			//formato largo de fecha
			case "L":
				return date_spa("l, j \de F \de Y",$fecha_db);
			break;
			//formato largo de fecha y hora(unicamernte)
			case "LH":
				return date_spa("l, j \de F \de Y H\h\s",$fecha_db);
			break;
			//formato largo de fecha, hora y minutos
			case "LHM":
				return date_spa("l, j \de F \de Y H:i\h\s",$fecha_db);
			break;
			//formato largo de fecha, hora minutos y segundos
			case "LHMS":
				return date_spa("l, j \de F \de Y H:i:s\h\s",$fecha_db);
			break;
			//formato corto de fecha y hora(unicamernte)
			case "SH":
				return date_spa("d/m/Y H\h\s",$fecha_db);
			break;
			//formato corto de fecha, hora y minutos
			case "SHM":
				return date_spa("d/m/Y H:i\h\s",$fecha_db);
			break;
			//formato corto de fecha, hora y minutos
			case "SHMS":
				return date_spa("d/m/Y H:i:s\h\s",$fecha_db);
			break;
			//formato corto de fecha
			default: //case "S":
				return date_spa("d/m/Y",$fecha_db);
		}
}
/*****************************************************************************
@tipo_celda es un string y los valores pueden ser:
	"texto"					//para campos de texto
	"peso" "pesos" o "$"	//para campos cuya moneda es pesos
	"dolar" o "U$S"			//para campos cuya moneda es el dolar
	"fecha_corta"			//fecha tipo 28/5/2004
	"fecha_larga"			//fecha tipo viernes 28 de mayo de 2004
	"timestamp_corto"		//fecha tipo 28/5/2004 16:50:30
	"timestamp_largo"		//fecha tipo viernes 28 de mayo de 2004 16:50:30
	"numero"				//para campos numericos

*****************************************************************************/
function excel_style($tipo_celda='texto')
{
 //los simbolos para usar el campo simbolo en moneda
 $style['u$s']='style=\'mso-number-format:"\[$USD\]\\ \#\,\#\#0\.00\;\[Red\]\[$USD\]\\ \\-\#\,\#\#0\.00"\'';
 $style['dolar']='style=\'mso-number-format:"\[$USD\]\\ \#\,\#\#0\.00\;\[Red\]\[$USD\]\\ \\-\#\,\#\#0\.00"\'';
 $style['$']='style=\'mso-number-format:"\0022$\0022\\ \#\,\#\#0\.00\;\[Red\]\0022$\0022\\ \\-\#\,\#\#0\.00"\'';
 $style['peso']='style=\'mso-number-format:"\0022$\0022\\ \#\,\#\#0\.00\;\[Red\]\0022$\0022\\ \\-\#\,\#\#0\.00"\'';
 $style['pesos']='style=\'mso-number-format:"\0022$\0022\\ \#\,\#\#0\.00\;\[Red\]\0022$\0022\\ \\-\#\,\#\#0\.00"\'';
 //valor por default para fecha = fecha_corta
 $style['fecha']='style=\'mso-number-format:"Short Date"\'';
 $style['fecha_corta']='style=\'mso-number-format:"Short Date"\'';
 $style['fecha_larga']='style=\'mso-number-format:"\[$-C0A\]d\\ \0022de\0022\\ mmmm\\ \0022de\0022\\ yyyy\;\@"\'';
 //valor por default para timestamp = timestamp_corto
 $style['timestamp']='style=\'mso-number-format:"dd\\/mm\\/yy\\ h\:mm\:ss";\''; //con dos digitos en el dia
 $style['timestamp_corto']='style=\'mso-number-format:"dd\\/mm\\/yy\\ h\:mm\:ss";\''; //con dos digitos en el dia
 $style['timestamp_largo']='style=\'mso-number-format:"\[$-C0A\]dd\\ \0022de\0022\\ mmmm\\ \0022de\0022\\ yyyy\ h\:mm\:ss";\'';
 $style['texto']='style=\'mso-number-format:"\@"\'';
 $style['numero']='style=\'mso-number-format:Standard\'';//sin color los negativos
// $style['numero']='style=\'mso-number-format:"0\.00_ \;\[Red\]\\-0\.00\\ "\'';//con signo y color
// $style['numero']='style=\'mso-number-format:"\#\,\#\#0\.00\;\[Red\]\\-\#\,\#\#0\.00"\'';//con signo y color
// $style['numero']='style=\'mso-number-format:"\#\,\#\#0\.00\;\[Red\]\#\,\#\#0\.00"\'';//con color
 return($style[strtolower($tipo_celda)]);
}

function excel_header($filename)
{
	if (isset($_SERVER["HTTPS"])) {
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: must-revalidate"); // HTTP/1.1
		header("Cache-Control: post-check=0, pre-check=0", false);
	}

	header("Pragma: ");
	header("Cache-Control: ");
	header("Content-Type: application/xls");
	header("Content-Transfer-Encoding: binary");
	header("Content-Disposition: attachment; filename=\"$filename\"");
}
/*****************************************************************************
 * subir_archivo BY GACZ
 * @return boolean  true si se subio el archivo con exito
 					false si se produjo algun error
 * @param string tmp_pathname es la ruta y nombre temporal del archivo enviado por POST
 * @param string newpathname es la ruta y nombre del archivo destino
 * @param boolean automakepath crea la ruta si no existe o da error
 * @param string error en esta variable se retorna el error en caso de
 * @desc Sube un archivo enviado por POST
 *
 ****************************************************************************/
function subir_archivo($tmp_pathname,$newpathname,&$error_msg,$overwrite=0,$automakepath=1)
{
	 //recupero la ruta de directorio
	 $lastpos=strrpos($newpathname,"/");
	 $path= substr($newpathname,0,$lastpos+1);//lastpos+1=#caracteres
	 $filename=substr($newpathname,$lastpos+1);

  	 if ($automakepath)
  	 {
		if (!mkdirs($path))
		{
          $error_msg="No se pudo crear la ruta de directorio\n";
          return false;
		}
  	 }

     if (!$overwrite && is_file($newpathname))
     {
             $error_msg="El Archivo '$filename' ya existe\n";
             return false;
     }
     else
     {
	  //move_uploaded_file(string nombre_archivo_tmp, string destino)
	   if (move_uploaded_file($tmp_pathname,$newpathname))
	   	return true;
	   elseif (!copy($tmp_pathname,$newpathname))
	   {
          $error_msg="No se pudo subir el archivo '$filename'\n";
          return false;
	   }
     }
     return true;
}

/*****************************************************************************
 * next_habil BY GACZ
 * @return string contiene la proxima o anterior(dependiendo del offset) fecha habil, si el offset=0 devuelve false
 * @param string fecha_db es la fecha (formato DB) a partir del cual se empezara a contar
 * @param int offset cantidad de dias desde fecha
 * @desc devuelve un string(formato DB) con la proxima fecha habil a partir del parametro @fecha y sumandole @offset dias
 *
 ****************************************************************************/

function next_habil($fecha_db,$offset=1)
{
	$init_offset=0;

	if ($offset > 0 )
		$increment=1;
	elseif($offset < 0)
		$increment=-1;
	else
		return false;

	while (($offset >0 && $init_offset < $offset) || ($offset < 0 && $init_offset > $offset))
	{
		$atimestamp=getdate(strtotime($fecha_db));//informacion del timestamp en un arreglo
		$fecha_db=date("Y-m-j",mktime(0,0,0,$atimestamp['mon'],$atimestamp['mday']+$increment,$atimestamp['year']));
		$fecha=date2("S",$fecha_db);//fecha en formato d/mm/yyyy
		//si no es un domingo y no es feriado
		if (date("w",mktime(0,0,0,$atimestamp['mon'],$atimestamp['mday']+$increment,$atimestamp['year']))!=0 && !feriado($fecha))
			$init_offset+=$increment;
	}
	return $fecha_db;

}


/*****************************************************************************
 * ArrayRowsAsCols BY GACZ
 * @return array, contiene las filas cambiadas por columnas
 * @param array array2dim, una arreglo de 2 dimensiones (en todas las claves)
 * @param bool byRef, byRef=false se hace una copia en el nuevo arreglo,
 											byRef=true se tiene una referencia ordenada de los valores(sin copia)
 * @desc intercambia filas por columnas en un arreglo 2-dimensional
 *
 ****************************************************************************/
function ArrayRowsAsCols($array2dim,$byRef=false)
{
	//para cada fila del arreglo
	foreach ($array2dim as $colindex => $arr)
	{
		//creo una columna
		foreach ($arr as $rowindex => $value )
		{
			if ($byRef)
				$newArr[$rowindex][$colindex]= &$array2dim[$colindex][$rowindex];
			else
				$newArr[$rowindex][$colindex]=$value;
		}
	}
	return $newArr;
}

//CASE SENSITIVE constants
define("OVR_replace",1);
define("OVR_noReplace",2);//se cambia el nombre unicamente si no existe ya esa clave nueva
define("OVR_alias",3);//crea un alias(una segunda referencia) para la clave ya existente en el arreglo,
											//si la nueva clave ya existe (no se reemplaza)
/*****************************************************************************
 * ArrayChangeKeyName BY GACZ
 * @return int, retorna un valor indicando la cantidad de claves que no se cambiaron
 * @param arr array, el arreglo al que se le cambiaran los nombres de las claves
 * @param arrKeysChange array, un arreglo donde cada clave es el valor de la clave en @arr a cambiar por el value del array en esa clave
 * @param overwriteMode CONSTANTE, overwrite=true reemplaza la clave del arreglo aunque exista, false no reemplaza y crea
 * @desc cambia los nombres de clave/s al arreglo
 ****************************************************************************/
function ArrayChangeKeyName(&$arr,$arrKeysChange,$overwriteMode=OVR_replace)
{
	$ret_value=0;
	foreach ($arrKeysChange as $oldkey => $newkey)
	{
		//existe la clave en el arreglo??
		if(isset($arr[$oldkey]))
		{
			switch ($overwriteMode)
			{
				case OVR_replace:
							$arr[$newkey]= &$arr[$oldkey];
							unset($arr[$oldkey]);
				break;
				case OVR_alias:
							//no existe la clave??
							if (!isset($arr[$newkey]))
								$arr[$newkey]= &$arr[$oldkey];
							else
								$ret_value++;
				break;
				case OVR_noReplace:
					//no existe la clave??
					if (!isset($arr[$newkey]))
					{
							$arr[$newkey]= &$arr[$oldkey];
							unset($arr[$oldkey]);
					}
					else
						$ret_value++;
				break;
			}
		}
	}
}

?>
