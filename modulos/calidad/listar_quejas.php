<?php

require_once("../../config.php");

echo $html_header;

?>

<script language="javascript">

// funciones que iluminan las filas de la tabla

function sobre(src,color_entrada) {

    src.style.backgroundColor=color_entrada;src.style.cursor="hand";

}

function bajo(src,color_default) {

    src.style.backgroundColor=color_default;src.style.cursor="default";

}

</script>

<form action="listar_quejas.php" method="post" name="form_listar_queja">



<?php 

echo "<table align=center cellpadding=5 cellspacing=0 >";

echo "<input type=hidden name=sort value='$sort'>\n";

echo "<input type=hidden name=up value='$up'>\n";

echo "<tr><td>\n";

// Formulario de busqueda

// Variables necesarias

$itemspp=10;

$up = $_POST["up"] or $up = $parametros["up"];

$sort = $_POST["sort"] or $sort = $parametros["sort"] or $sort = "";

$page = $parametros["page"] or $page = 0;                                //pagina actual

$filter = $_POST["filter"] or $filter = $parametros["filter"];           //campo por el que se esta filtrando

$keyword = $_POST["keyword"] or $keyword = $parametros["keyword"];       //palabra clave

// Fin variables necesarias

if ($up=="") $up = "1";   // 1 ASC 0 DESC

$orden = Array (

"default" => "2",

"1" => "nbre_cl",

"2" => "fecha",

"3" => "tipo_queja",

//"4"=> "usuario",

"5"=> "mail");



$filtro = Array (

"nbre_cl" => "Cliente",

"fecha" => "Fecha",

"tipo_queja" => "Tipo",

//"usuario" => "usuario",

"mail" => "mail");



$sql_temp="select * from Quejas join log_quejas using(id_queja)";

$contar="select count(*) from quejas";

if($_POST['keyword'] || $keyword)// en la variable de sesion para keyword hay datos)

     $contar="buscar";

list($sql,$total,$link_pagina,$up2) = form_busqueda($sql_temp,$orden,$filtro,$link_tmp,$where_tmp,$contar);

echo "&nbsp;&nbsp;&nbsp;<input type=submit name='form_busqueda' value='   Buscar   '>";

echo "</td></tr>\n";

echo "</table>\n";

$res_query = sql($sql) or die();



?>

<br>





<table border=0 width=100% cellspacing=2 cellpadding=3>

<tr>

<td colspan=5 align=left id=ma> <? echo "\n";?>

	<table width=100%>

	 <tr id=ma><? echo "\n";?>

	  <td width=30% align=left><b><? echo "Total:</b> $total Quejas.</td>\n";?>

      <td width=70% align=right><? echo $link_pagina ?></td> <? echo"\n";?>

	 </tr>

	</table> <? echo "\n";?>

  </td>

</tr>

<tr>

<!--<td width="10%" align="center" id=mo><a id=mo href='<?// echo encode_link("listar_quejas.php",Array('sort'=>1,'up'=>$up2,'page'=>$page,'keyword'=>$keyword,'filter'=>$filter))?>'><b>ID Queja</b></a></td>-->

<td width="10%" align="center" id=mo><a id=mo href='<? echo encode_link("listar_quejas.php",Array('sort'=>1,'up'=>$up2,'page'=>$page,'keyword'=>$keyword,'filter'=>$filter))?>'><b>Nombre_cliente</b></a></td>

<td width="10%" align="center" id=mo><a id=mo href='<? echo encode_link("listar_quejas.php",Array('sort'=>5,'up'=>$up2,'page'=>$page,'keyword'=>$keyword,'filter'=>$filter))?>'><b>E-mail</b></a></td>

<td width="10%" align="center" id=mo><a id=mo href='<? echo encode_link("listar_quejas.php",Array('sort'=>2,'up'=>$up2,'page'=>$page,'keyword'=>$keyword,'filter'=>$filter))?>'><strong>Fecha</strong></td>

<td width="10%" align="center" id=mo><a id=mo href='<? echo encode_link("listar_quejas.php",Array('sort'=>3,'up'=>$up2,'page'=>$page,'keyword'=>$keyword,'filter'=>$filter))?>'><strong>Tipo</strong></td>

<!--<td width="10%" align="center" id=mo><a id=mo href='<? //echo encode_link("listar_quejas.php",Array('sort'=>4,'up'=>$up2,'page'=>$page,'keyword'=>$keyword,'filter'=>$filter))?>'><strong>Usuario</strong></td>-->

</tr>



<? $cont_filas=0;

   

  while (!$res_query->EOF )

  {

   if ($cnr==1)

    {$color1=$bgcolor1;

     $color =$bgcolor2;

     $cnr=0;

    }

  else

   {$color1=$bgcolor2;

    $color =$bgcolor1;

    $cnr=1;

   }

//guardamos en esta variable, las observaciones de la licitacion

 //para mostrarlos en title del nombre de la licitacion

	$title_obs=$res_query->fields['descripcion'];



 //LIMITAR OBSERVACIONES: controlamos el ancho y la cantidad de

 //lineas que tienen las observaciones y cortamos el string si

 //se pasa de alguno de los limites

	$long_title=strlen($title_obs);

	//cortamos si el string supera los 600 caracteres

	if($long_title>600)

		{$title_obs=substr($title_obs,0,600);

    	 $title_obs.="   SIGUE >>>";

		}

		$count_n=str_count_letra("\n",$title_obs);

		//cortamos si el string tiene mas de 12 lineas

		if($count_n>12)

		{$cn=0;$j=0;

		 for($i=0;$i<$long_title;$i++)

		 {

		  if($cn>12)

		   $i=$long_title;

		  if($title_obs[$i]=="\n")

		   $cn++;

		  $j++;



		 }

		 $title_obs=substr($title_obs,0,$j);

		 $title_obs.="   SIGUE >>>";

		}

 ?>

   <tr  bgcolor='<?php echo $color; ?>' onMouseOver="sobre(this,'#FFFFFF');" onMouseOut="bajo(this,'<? echo $color?>' );" title="<? echo $title_obs ?>">

    <a href="<? echo encode_link("calidad_quejas.php", array("id_queja" =>$res_query->fields["id_queja"])); ?>" >

   <!--<td align="center"><font color="<?// echo $color1?>"><b><?// echo $res_query->fields['id_queja'] ?></b></font></td> -->

   <td align="center"><font color="<? echo $color1?>"><b><? echo $res_query->fields['nbre_cl'] ?></b></font></td>

   <td align="center"><font color="<? echo $color1?>"><b><? echo $res_query->fields['mail'] ?></b></font></td>

   <td align="center"><font color="<? echo $color1?>"><b><? echo Fecha($res_query->fields['fecha']) ?></b></font></td>

   <td align="center"><font color="<? echo $color1?>"><b><? echo $res_query->fields['tipo_queja'] ?></b></font></td>

   <!--<td align="center"><font color="<? //echo $color1?>"><b><? //echo $res_query->fields['usuario'] ?></b></font></td>-->

   </a>

   </tr>

  <?   

     $cont_filas++;

	 $res_query->MoveNext();

  }  ?>



</table>

<br>

<div align="center">

<input name="nueva_queja" type="button" value="Nueva Queja" Onclick="location.href='calidad_quejas.php';">

</div>

</form>