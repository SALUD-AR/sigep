<?php

require_once("../../config.php");

function contactos_existentes($modulo,$id_general){
global $db;



$sql="select contactos_generales.id_contacto_general,contactos_generales.nombre,contactos_generales.tel from (((modulos_contacto join contactos_generales on modulos_contacto.id_contacto_general = contactos_generales.id_contacto_general )";
$sql.=" join modulos on modulos.id_modulo = modulos_contacto.id_modulo and modulos.nombre='$modulo')";
$sql.=" join relaciones_contacto on contactos_generales.id_contacto_general = relaciones_contacto.id_contacto_general and relaciones_contacto.entidad = $id_general) ";


$contactos=$db->execute($sql) or die($db->ErrorMsg()."<br>".$sql);
$cantidad_contactos=$contactos->RecordCount();
echo "<table >";
 echo "<tr>";
    for($i=0;$i<$cantidad_contactos;$i++){
    $informacion="Nombre: ".$contactos->fields['nombre']."\n";
    $informacion.="Teléfono: ".$contactos->fields['tel'];
    $link=encode_link("../contactos_generales/ver_contacto.php",array("modulo"=>$modulo,"id_contacto"=>$contactos->fields["id_contacto_general"]));
    echo"<td title='$informacion' onclick=\"window.open('$link','','toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=0,left=25,top=10,width=600,height=300');\" style='cursor:hand'>";
    //echo $contactos->fields['nombre'];
    echo "<img align=middle src=../../imagenes/contacto.gif border=0>";
    echo"</td>";
    $contactos->MoveNext();
    }
 echo "</tr>";
echo "</table>";
}//fin de funcion


function contactos_existentes_licitacion($id_licitacion){
global $db;



$sql="select id_contactos_licitacion,nombre,tel from contactos_licitacion where id_licitacion = $id_licitacion";


$contactos=$db->execute($sql) or die($db->ErrorMsg()."<br>".$sql);
$cantidad_contactos=$contactos->RecordCount();
echo "<table >";
 echo "<tr>";
    for($i=0;$i<$cantidad_contactos;$i++)
	{
    $informacion="Nombre: ".$contactos->fields['nombre']."\n";
    $informacion.="Teléfono: ".$contactos->fields['tel'];
    $link=encode_link("../contactos_generales/ver_contacto_licitacion.php",array("id_contacto_licitacion"=>$contactos->fields["id_contacto_licitacion"]));
    echo"<td title='$informacion' onclick=\"window.open('$link','','toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=0,left=25,top=10,width=600,height=300');\" style='cursor:hand'>";
    echo "<img align=middle src=../../imagenes/contacto.gif border=0>";
    echo"</td>";
    $contactos->MoveNext();
    }
 echo "</tr>";
echo "</table>";
}//fin de funcion


?>