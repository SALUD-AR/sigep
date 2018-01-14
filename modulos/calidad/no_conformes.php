<?
/*AUTOR: MAC

$Author: mari $
$Revision: 1.4 $
$Date: 2007/01/03 13:59:39 $
*/

require_once("../../config.php");
echo $html_header;
$msg=$parametros['msg'];

//$up=$_POST["up"] or $up=$_GET["up"];
if($_POST['boton']=="Borrar")
{               $db->StartTrans();
 	            $i=1;$bien=1;
 	            while ($i<=$_POST['cant'])
                {if ($_POST['borrar_'.$i]!="")
                  {$sql="delete from noconformes where id_noconforme=".$_POST['borrar_'.$i];
                   if(!$db->Execute($sql))
                    $bien=0; 
                  }
                  $i++;
                } 
                if($bien)
                 $msg="<b><center>Los Productos No Conformes seleccionados se borraron con éxito</b></center>"; 
                else 
                 $msg="<b><center>Los Productos No Conformes seleccionados no se pudieron borrar</b></center>";  
                $db->CompleteTrans(); 
}//del if de borrar

?>
<html>
<head>
<script>
// funciones que iluminan las filas de la tabla
function sobre(src,color_entrada) {
    src.style.backgroundColor=color_entrada;src.style.cursor="hand";
}
function bajo(src,color_default) {
    src.style.backgroundColor=color_default;src.style.cursor="default";
}

</script>
<link rel=stylesheet type='text/css' href='../../lib/estilos.css'>
<? include("../ayuda/ayudas.php");?>
</head>
<body bgcolor="#E0E0E0">
<?


$orden = array(
		"default" => "1",
 //		"default_up" => "1",
		"1" => "noconformes.fecha_evento",
		"2" => "noconformes.descripcion_inconformidad",
        "3" => "noconformes.id_noconforme"
	);

$filtro = array(
		"noconformes.descripcion_inconformidad" => "Descripcion de Inconformidad"

	);

$query="select id_noconforme,descripcion_inconformidad,fecha_evento from noconformes";

?>
<br>
<form name="form1" method="post" action="no_conformes.php">
<div align="right">
 <img src='<?php echo "$html_root/imagenes/ayuda.gif" ?>' border="0" alt="ayuda" onClick="abrir_ventana('<?php echo "$html_root/modulos/ayuda/calidad/ayuda_noconforme.htm" ?>', 'LISTAR PRODUCTOS NO CONFORMES')" >
</div>
<center>
<?
$contar="select count(*) from noconformes";
if($_POST['keyword'] || $keyword)
 $contar="buscar";
list($sql,$total_noconformes,$link_pagina,$up) = form_busqueda($query,$orden,$filtro,$link_tmp,$where,$contar);
$resultado=$db->Execute($sql) or die($db->ErrorMsg()."<br>".$sql);
?>

 &nbsp;&nbsp;<input type=submit name=form_busqueda value='Buscar'>
</center>
 <br>
 <?=$msg?>
 <table border=0 width="95%" align="center" cellpadding="3" cellspacing='0' bgcolor=<?=$bgcolor3?>>
 <tr id=ma>
    <!--<td align="left">
     <b>Total:</b> <?//=$total_noconformes?>.
    </td>-->
	<td align=right>
	 <?=$link_pagina?>
	</td>
  </tr>
</table>
<div style='position:relative; width:100%; height:70%; overflow:auto;'>
<table width='95%' border='0' cellspacing='2' align="center">
    <tr>
      <td width="1%" id=mo></td>
      <!--<td width="4%" align="center" id=mo><a id=mo href='<?//=encode_link($_SERVER["PHP_SELF"],array("sort"=>"3","up"=>$up))?>'><b>Id</b></a> </td>-->
      <td width="19%" align="center" id=mo><a id=mo href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"1","up"=>$up))?>'><b>Fecha Evento</b></a></td>
      <td width="80%" align="center" id=mo><a id=mo href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"2","up"=>$up))?>'><b>Descripción</b></a></td>
    </tr>
    <?
  $i=1;
  while (!$resultado->EOF )
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
?>
      <tr  bgcolor='<?php echo $color; ?>' onMouseOver="sobre(this,'#FFFFFF');" onMouseOut="bajo(this,'<? echo $color?>' );"><a href="<? echo encode_link("detalle_no_conformes.php", array("pagina"=>"listado","id" =>$resultado->fields["id_noconforme"])); ?>" >
      <td align="center">
       <font color="<? echo $color1?>">
        <input type="checkbox" name="borrar_<? echo $i; ?>" value="<? echo $resultado->fields['id_noconforme']; ?>">
       </font>
      </td>
      <!--<td align="center"><font color="<?// echo $color1?>"><b><? //echo $resultado->fields['id_noconforme']; ?></font></td>-->
      <td align="center"><font color="<? echo $color1?>"><b><? echo fecha($resultado->fields['fecha_evento']); ?></font></td>
      <td align="center"><font color="<? echo $color1?>"><b><? echo $resultado->fields['descripcion_inconformidad']; ?></font></td>

    </a> </tr>
    <?
	$resultado->MoveNext();
	$i++;
  }  ?>
  </table>
  <input type="hidden" name="cant" value="<? echo $resultado->RecordCount(); ?>">

</div>

  <div align="center">
    <table>
      <tr>
        <td><input type="button" name="boton" value='Agregar Nuevo' onclick="document.location='detalle_no_conformes.php'">
        </td>
        <td>
             <input type="submit" name="boton" value="Borrar">
        </td>
      </tr>
    </table>
  </div>
</form>
</body>
</html>