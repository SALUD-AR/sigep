<?php

require_once "../../config.php";


if ($_POST['nuevo_proyecto']=="Nuevo Proyecto")
{require_once('cargar_proyectos.php');
 exit;
}	

echo $html_header;

variables_form_busqueda("lista_proyectos");//para que funcione el form busqueda

//armo la barra de navegacion
if ($cmd == "") {
	$cmd="pe";
    phpss_svars_set("_ses_lista_proyectos_cmd", $cmd);
}
//pe= Pendientes
//hi= Historial

$datos_barra = array(
					array(
						"descripcion"	=> "Pendientes",
						"cmd"			=> "pe",
						),
					array(
						"descripcion"	=> "Historial",
						"cmd"			=> "hi"
						)
			      
				     );//Prepara los datos para armar la barra de navegación
echo "</br>";
generar_barra_nav($datos_barra);
//fin de barra de navegacion
?>

<form name="lista_proyectos" action="lista_proyectos.php" method="post">

<?

$seleccion = Array ( "comentarios" => "id_proyecto in (select id_proyecto
                                        from tareas_divisionsoft.log_proyectos
                                        where comentario ILIKE '%$keyword%')"
);


$ignorar = Array ( 0 => "comentarios");

$orden = array(
		"default" => "1",
 		"default_up" => "1",
		"1" => "proyectos.id_proyecto",
		"2" => "proyectos.titulo",
		"3" => "proyectos.fecha_inicio",
		"4" => "proyectos.fecha_fin",
		"5" => "usuarios.apellido",
              );
              
$filtro = array(
		"proyectos.id_proyecto" => "ID",
		"proyectos.titulo" => "Titulo",
        "proyectos.fecha_inicio" => "Fecha Inicio",
        "proyectos.fecha_fin" => "Fecha Finalizacion",
        "comentarios" => "Comentarios",		
	    );              
	    
       
$query="select proyectos.*,(usuarios.apellido || ', ' || usuarios.nombre) as nomb_usuario from proyectos         
        left join sistema.usuarios using(id_usuario)";	
$where="";

if($cmd=="pe")
{$where=" proyectos.estado=1";
 $contar="select count(*) from proyectos where proyectos.estado=1";
}
if($cmd=="hi")
{$where=" proyectos.estado=2";
 $contar="select count(*) from proyectos where proyectos.estado=2";
}


echo "<br>";
echo "<center>";
$contar="buscar";


list($sql,$total_proyectos,$link_pagina,$up) = form_busqueda($query,$orden,$filtro,$link_tmp,$where,$contar,"",$ignorar,$seleccion); 
$resultado=sql($sql) or fin_pagina();

?>	

&nbsp;&nbsp;<input type=submit name=form_busqueda value='Buscar'>
</center>
<br>
<table width="30%" align="center">  
 <tr >
  <td align="center" >
   <input name="nuevo_proyecto" type="submit" value="Nuevo Proyecto" >
  </td>
 </tr>
</table>
<br>


<table width='95%' align="center" cellspacing="2" cellpadding="2" class="bordes">
 <tr id=ma>
  <td align="left" colspan="2">
   <b>Total:</b> <?=$total_proyectos?> <b>Proyecto/s Encontrado/s.</b>
   <input name="total_proyectos" type="hidden" value=<?=$total_proyectos?>>
  </td>
  <td align="right" colspan="3">
   <?=$link_pagina?>
  </td>
 </tr>
 <tr id=mo>
  <td width="5%" ><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"1","up"=>$up))?>'>ID</a></b></td>
  <td width="50%"><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"2","up"=>$up))?>'>Titulo</a></b></td>
  <td width="10%"><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"3","up"=>$up))?>'>Fecha Inicio</a></b></td>
  <td width="10%"><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"4","up"=>$up))?>'>Fecha Fin</a></b></td>
  <td width="25%"><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"5","up"=>$up))?>'>Administrador</a></b></td>  
 </tr>
<?
while(!$resultado->EOF)
{
 echo  "<tr ".atrib_tr()." >";  
 $link = encode_link("cargar_proyectos.php",array("pagina"=>"nuevo_proyecto","id"=>$resultado->fields["id_proyecto"],"cmd"=>$cmd));  
 ?>	
  <a href='<?=$link?>'> 
  
 <td align="center"><?=$resultado->fields['id_proyecto']?></td>
 <td align="center"><?=$resultado->fields['titulo']?></td>
 <td align="center"><?$fecha_inicio=split(' ',$resultado->fields['fecha_inicio']);//función que separa la fecha de la hora
  echo fecha($fecha_inicio[0]);//aca me queda solo la fecha 
  ?>
 </td>
 <td align="center"><?$fecha_fin=split(' ',$resultado->fields['fecha_fin']);//función que separa la fecha de la hora
  echo fecha($fecha_fin[0]);//aca me queda solo la fecha 
  ?>
 </td>
 <td align="center"><? echo $resultado->fields['nomb_usuario']; ?></td>    
 </a>
</tr>
<?
 $resultado->MoveNext();
}//del while
?>
</table>



</form>
<?=fin_pagina();?>
