<?php

include("../../config.php");


//traemos los modulos posibles para los contactos
 $query="select * from modulos";
 $mod=sql($query) or fin_pagina();

//datos para insertar

$do=0;
extract($_POST,EXTR_SKIP);
$modulo=$parametros["modulo"];
$id_general=$parametros["id_general"];

if (($boton=="Guardar")&&($editar!="editar"))
    $do=1;
elseif ($boton=="Eliminar")
    $do=2;
elseif($editar=="editar")
    $do=3;

if ($do==1){
   $flag=1;$flag2=1;
   $db->StartTrans();
//me fijo si ya existe un contacto con el mismo nombre si ya existe no lo inserto
   $sql="select * from contactos_generales where nombre='$nombre'";
   $resultado=sql($sql) or fin_pagina();
   if ($resultado->RecordCount()==1)
               $informar="<center><b>El Contacto \"$nombre\" ya existe, no se actualizaron los datos</b></center>";
               else {
               $campos="(nombre,tel,direccion,provincia,localidad,cod_postal,mail,fax,icq,observaciones,cargado_sistema)";
               $query_insert="INSERT INTO contactos_generales $campos VALUES ".
                        "('$nombre','$tel','$direccion','$provincia','$localidad','$cod_postal','$mail','$fax','$icq','$observaciones',1)";
               if (sql($query_insert) or fin_pagina())
                       $flag=1;
                       else
                       $flag2=0;

              $sql="select * from contactos_generales  order by id_contacto_general";
              $resultado=sql($sql) or fin_pagina();
              $resultado->Movelast();
              $id_contacto_general=$resultado->fields["id_contacto_general"];
              //insertamos el id de entidad al que pertenece el contacto si no esta ya en la BD
              //si no se accedio a la pagina desde el menu
              //es decir, se accedio desde licitaciones,
              //o cobranzas, o algun otro modulo
              if($id_general){
                       $query="select entidad from relaciones_contacto where id_contacto_general=$id_contacto_general and entidad=$id_general";
                       $aaa=sql($query) or fin_pagina();
                                   if($aaa->RecordCount()==0)
   	                                  $sql="insert into relaciones_contacto (id_contacto_general,entidad) values ($id_contacto_general,$id_general)";
                                	 else
                                	  $sql=0;
                       }
                       else{//se accedio desde el menu
                       $query="select entidad from relaciones_contacto where id_contacto_general=$id_contacto_general and entidad=$select_entidad";
                       $aaa=sql($query) or fin_pagina();
                   	   if($aaa->RecordCount()==0)
                                $sql="insert into relaciones_contacto (id_contacto_general,entidad) values ($id_contacto_general,$select_entidad)";
                                else
                                $sql=0;
                        }
                     if(($sql)&&(sql($sql)))
                     $flag2=1;
                     else
                     $flag2=0;

                    //insertamos los modulos a los que pertenece el contacto
                    $mod->Move(0);
                    while(!$mod->EOF){
                              	if($_POST[$mod->fields['nombre']]==$mod->fields['id_modulo']){
                                    $query_mod="insert into modulos_contacto (id_contacto_general,id_modulo) values($id_contacto_general,".$mod->fields['id_modulo'].")";
                                    sql($query_mod) or fin_pagina();
                                    }
                	$mod->MoveNext();
    }   //

   if ($flag && $flag2)
      $informar="<center><b>El Contacto \"$nombre\" fue añadido con éxito</b></center>";
      else
      $informar="<center><b>El Contacto \"$nombre\" no se pudo agregar</b></center>";
   }//del else que corresponde al if que pregunta si ya exite nombre
   $db->CompleteTrans();
}   //del if($do==1)
elseif ($do==2)
{
    $sql="delete from  relaciones_contacto where entidad=$id_general and id_contacto_general=$select_contactos";
    //$sql="delete from contactos_generales where id_contacto_general=$select_contactos";
    if (sql($sql))
     $informar="<center><b>El Contacto \"$nombre\" fue eliminado con éxito</b></center>";
    else
     $informar="<center><b>El Contacto \"$nombre\" no se pudo eliminar</b></center>";
}
elseif ($do==3)
{$flag1=1;
 $flag2=1;
$db->StartTrans();
$query="update contactos_generales set nombre='$nombre',direccion='$direccion',cod_postal='$cod_postal',
                localidad='$localidad',provincia='$provincia',
                tel='$tel',mail='$mail',observaciones='$observaciones',fax='$fax',icq='$icq'
                WHERE id_contacto_general=$select_contactos";
//echo $query;
 if (sql($query))
     $flag1=1;//$informar="<center><b>Los datos  fueron actualizado con éxito</b></center>";
 else $flag1=0;
     //$informar="<center><b>Error: Los datos no se actualizaron</b></center>";

   //borramos todos los existentes e insertamos aquellos seleccionados.
   $query_del="delete from modulos_contacto where id_contacto_general=$select_contactos";
   $a=sql($query_del) or fin_pagina();

   //insertamos los modulos a los que pertenece el contacto
   $mod->Move(0);
   while(!$mod->EOF)
   {
   	if($_POST[$mod->fields['nombre']]==$mod->fields['id_modulo'])
    {
     $query_mod1="insert into modulos_contacto (id_contacto_general,id_modulo)
                         values
                         ($select_contactos,".$mod->fields['id_modulo'].")";
   	 sql($query_mod1) or fin_pagina();
    }
   	$mod->MoveNext();
   }

if(!$id_general)
 $id_general=$select_entidad;

$sql="select * from relaciones_contacto where id_contacto_general = $select_contactos and entidad=$id_general";
 $resultado=sql($sql) or fin_pagina();
 if ($resultado->RecordCount()==0) {
    $query="insert into relaciones_contacto (id_contacto_general,entidad) values ($select_contactos,$id_general)";
    if (sql($query))
       $flag2=1;
       else $flag2=0;
    }
if ($flag1 && $flag2)
    $informar="<center><b>Los datos  fueron actualizados con éxito</b></center>";
    else
    $informar="<center><b>Error: Los datos no se actualizaron</b></center>";
$db->CompleteTrans();
}

//datos por parametros
//$id_contacto=$parametros['id_contacto'];
//$pagina=$parametros['pagina'];
/*
echo "Modulo:".$modulo."<br>";
echo "Id_entidad:".$id_general."<br>";
*/
$link=encode_link("contactos.php",array("modulo"=> $modulo,"id_general"=>$id_general));
?>
<center>

<html>
<head>
<title>Contactos </title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php echo "<link rel=stylesheet type='text/css' href='$html_root/lib/estilos.css'>"; ?>
<style type="text/css">
<!--
.tablaEnc {
    background-color: #006699;
    color: #c0c6c9;
    font-weight: bold;
}
-->
</style>
<?
//include("../ayuda/ayudas.php");
?>
</head>
<body bgcolor=#E0E0E0 leftmargin=4>
<SCRIPT LANGUAGE="JavaScript">
<?php
$sql="select * from contactos_generales order by nombre";
$datos_contactos=sql($sql) or fin_pagina();

while (!$datos_contactos->EOF)
{
?>
var contactos_<?php echo $datos_contactos->fields["id_contacto_general"]; ?>=new Array();
contactos_<?php echo $datos_contactos->fields["id_contacto_general"]; ?>["nombre"]="<?php if($datos_contactos->fields["nombre"]){echo $datos_contactos->fields["nombre"];}else echo "null";?>";
contactos_<?php echo $datos_contactos->fields["id_contacto_general"]; ?>["tel"]="<?php if($datos_contactos->fields["tel"]){echo $datos_contactos->fields["tel"];}else echo "null";?>";
contactos_<?php echo $datos_contactos->fields["id_contacto_general"];?>["direccion"]="<?php if($datos_contactos->fields["direccion"]){echo $datos_contactos->fields["direccion"];}else echo "null";?>";
contactos_<?php echo $datos_contactos->fields["id_contacto_general"]; ?>["provincia"]="<?php if($datos_contactos->fields["provincia"]){echo $datos_contactos->fields["provincia"];}else echo "null";?>";
contactos_<?php echo $datos_contactos->fields["id_contacto_general"]; ?>["localidad"]="<?php if($datos_contactos->fields["localidad"]){echo $datos_contactos->fields["localidad"];}else echo "null";?>";
contactos_<?php echo $datos_contactos->fields["id_contacto_general"]; ?>["cod_postal"]="<?php if($datos_contactos->fields["cod_postal"]){echo $datos_contactos->fields["cod_postal"];}else echo "null";?>";
contactos_<?php echo $datos_contactos->fields["id_contacto_general"]; ?>["mail"]="<?php if($datos_contactos->fields["mail"]){echo $datos_contactos->fields["mail"];}else echo "null";?>";
contactos_<?php echo $datos_contactos->fields["id_contacto_general"];?>["fax"]="<?php if($datos_contactos->fields["fax"]){echo $datos_contactos->fields["fax"];}else echo "null";?>";
contactos_<?php echo $datos_contactos->fields["id_contacto_general"]; ?>["icq"]="<?php if($datos_contactos->fields["icq"]){echo $datos_contactos->fields["icq"];}else echo "null";?>";
<?
$observaciones=$datos_contactos->fields["observaciones"];
$observaciones=ereg_replace("\r\n","<br>",$observaciones);
?>
contactos_<?php echo $datos_contactos->fields["id_contacto_general"];?>["observaciones"]="<?php if($datos_contactos->fields["observaciones"]){echo ($observaciones);}else echo "null";?>";

contactos_<?php echo $datos_contactos->fields["id_contacto_general"];?>["modulos"]=new Array();
<?
//traemos los modulos en los que se usa este contacto
//asi lo insertamos en la variable correspondiente
$query="select modulos.nombre,modulos.id_modulo from (contactos_generales join modulos_contacto using(id_contacto_general) join modulos using (id_modulo)) where id_contacto_general=".$datos_contactos->fields["id_contacto_general"]." order by modulos.id_modulo";
$modulos_contacto=sql($query) or fin_pagina();

//y lo ponemos en un arreglo en la variable contacto_<id_contacto>
$cant_mod=$modulos_contacto->RecordCount();
$index=0;
$mod->Move(0);
while (!$mod->EOF)
{?> contactos_<?php echo $datos_contactos->fields["id_contacto_general"];?>["modulos"]["<?=$mod->fields['id_modulo']?>"]="false";
<?
  $modulos_contacto->Move(0);
  while(!$modulos_contacto->EOF)
  {
  	if($modulos_contacto->fields['id_modulo']==$mod->fields['id_modulo'])
   {
 ?>
 	contactos_<?php echo $datos_contactos->fields["id_contacto_general"];?>["modulos"]["<?=$mod->fields['id_modulo']?>"]="true";
 <?
    //si hay match, movemos afuera del recordset de $modulos_contacto
    //porque ya encontramos el modulo que queriamos
    break;
   }
   else//sino, movemos al proximo
    $modulos_contacto->MoveNext();
  }
 $mod->MoveNext();
}
$index++;
/*
//por cada contacto, traemos todas las entidades a las que pertenece
//el contacto, solo si se llego a esta pagina desde el menu. Sino no es
//necesario porque el select esta deshabilitado.
if($id_general)
{$query="select entidad.nombre,entidad.id_entidad from relaciones_contacto join entidad on relaciones_contacto.entidad=entidad.id_entidad";
 $entidades_contacto=$db->Execute($query) or die($db->ErrorMsg()."<br>entidades_contacto<br>");
}
*/
$datos_contactos->MoveNext();

}//del while de datos_contactos
?>

function set_datos()
{
//alert(document.all.select_contactos.options[document.all.select_contactos.selectedIndex].value);

    switch(document.all.select_contactos.options[document.all.select_contactos.selectedIndex].value)
    {<?PHP
     $datos_contactos->Move(0);
     while(!$datos_contactos->EOF)
     {?>
      case '<?echo $datos_contactos->fields["id_contacto_general"]?>': info=contactos_<?echo $datos_contactos->fields["id_contacto_general"];?>; break;
     <?
      $datos_contactos->MoveNext();
     }
     ?>
    }

    if(info["nombre"]!="null")
    document.all.nombre.value=info["nombre"];
    else
     document.all.nombre.value="";
    if(info["direccion"]!="null")
     document.all.direccion.value=info["direccion"];
    else
     document.all.direccion.value="";

    if(info["tel"]!="null")
     document.all.tel.value=info["tel"];
    else
     document.all.tel.value="";
    if(info["provincia"]!="null")
     document.all.provincia.value=info["provincia"];
    else
     document.all.provincia.value="";

    if(info["localidad"]!="null")
     document.all.localidad.value=info["localidad"];
    else
     document.all.localidad.value="";
    if(info["cod_postal"]!="null")
     document.all.cod_postal.value=info["cod_postal"];
    else
     document.all.cod_postal.value="";
     if(info["mail"]!="null")
     document.all.mail.value=info["mail"];
    else
     document.all.mail.value="";
     if(info["fax"]!="null")
     document.all.fax.value=info["fax"];
    else
     document.all.fax.value="";
     if(info["icq"]!="null")
     document.all.icq.value=info["icq"];
    else
     document.all.icq.value="";


    <?//seteamos los checkboxes para mostrar los modulos del contacto
     $mod->Move(0);
     while(!$mod->EOF)
     { ?>
      if(info["modulos"]["<?=$mod->fields['id_modulo']?>"]=="true")
       document.all.<?=$mod->fields['nombre']?>.checked=true;
      else
       document.all.<?=$mod->fields['nombre']?>.checked=false;

     <?
     $mod->MoveNext();
     }
     ?>
     if(info["observaciones"]!="null")
     {

     document.all.observaciones.value=info["observaciones"];
     document.all.observaciones.value=document.all.observaciones.value.replace("<br>","\n");
    }
    else
     document.all.observaciones.value="";

   document.all.editar.value="editar";

} //fin de la funcion set_datos()



//funcion que chequea que no se hayan puesto caracteres del tipo comillas dobles (")
//para que no salte un error de JavaScript...ver Bd de errores para mas info
function control_datos()
{
    if (document.all.nombre.value=='' || document.all.nombre.value==' ')
    {
     alert ('Debes completar el nombre del cliente');
     return false;
    }
    if(document.all.nombre.value.indexOf('"')!=-1)
    {   alert('Ha ingresado un caracter no permitido: evite ingresar comillas dobles (") en el campo nombre');
        return false;
    }
    if(document.all.tel.value.indexOf('"')!=-1)
    {   alert('Ha ingresado un caracter no permitido: evite ingresar comillas dobles (") en el campo teléfono');
        return false;
    }
    if(document.all.direccion.value.indexOf('"')!=-1)
    {   alert('Ha ingresado un caracter no permitido: evite ingresar comillas dobles (") en el campo dirección');
        return false;
    }
    if(document.all.mail.value.indexOf('"')!=-1)
    {   alert('Ha ingresado un caracter no permitido: evite ingresar comillas dobles (") en el campo e-mail');
        return false;
    }
    if(document.all.fax.value.indexOf('"')!=-1)
    {   alert('Ha ingresado un caracter no permitido: evite ingresar comillas dobles (") en el campo fax');
        return false;
    }
    if(document.all.icq.value.indexOf('"')!=-1)
    {   alert('Ha ingresado un caracter no permitido: evite ingresar comillas dobles (") en el campo icq');
        return false;
    }

    if(document.all.observaciones.value.indexOf('"')!=-1)
    {   alert('Ha ingresado un caracter no permitido: evite ingresar comillas dobles (") en el campo observaciones');
        return false;
    }

    var check=0;
    <?//controlamos que al menos un checkbox de modulo este elejido
     $mod->Move(0);
     while(!$mod->EOF)
     { ?>
      if(document.all.<?=$mod->fields['nombre']?>.checked==true)
       check=1;
     <?
     $mod->MoveNext();
     }
     ?>
     if(check==0)
     {  alert('Debe elejir al menos un módulo para el contacto');
        return false;
     }

} //fin de la funcion control_datos()

</SCRIPT>
<script language="JavaScript1.2">
//funciones para busqueda abrebiada utilizando teclas en la lista que muestra los clientes.
var digitos=15 //cantidad de digitos buscados
var puntero=0
var buffer=new Array(digitos) //declaración del array Buffer
var cadena=""

function buscar_op(obj){
   var letra = String.fromCharCode(event.keyCode)
   if(puntero >= digitos){
       cadena="";
       puntero=0;
    }
   //si se presiona la tecla ENTER, borro el array de teclas presionadas y salto a otro objeto...
   if (event.keyCode == 13){
       borrar_buffer();
      // if(objfoco!=0) objfoco.focus(); //evita foco a otro objeto si objfoco=0
    }
   //sino busco la cadena tipeada dentro del combo...
   else{
       buffer[puntero]=letra;
       //guardo en la posicion puntero la letra tipeada
       cadena=cadena+buffer[puntero]; //armo una cadena con los datos que van ingresando al array
       puntero++;

       //barro todas las opciones que contiene el combo y las comparo la cadena...
       for (var opcombo=0;opcombo < obj.length;opcombo++){
          if(obj[opcombo].text.substr(0,puntero).toLowerCase()==cadena.toLowerCase()){
          obj.selectedIndex=opcombo;
          }
       }
    }
   event.returnValue = false; //invalida la acción de pulsado de tecla para evitar busqueda del primer caracter
}

function borrar_buffer(){
   //inicializa la cadena buscada
    cadena="";
    puntero=0;
}
</script>



<?=$informar;?>
<form name="form" method="post" action="<?$link;?>">
<!--
<div align='right'>
<a href="#" onClick="abrir_ventana('<?php /*echo "$html_root/modulos/ayuda/licitaciones/ayuda_competidor.htm" */ ?>', 'CARGAR COMPETIDOR')"> Ayuda </a>
</div>
-->

<input type="hidden" name="editar" value="">
  <TABLE width="100%" align="center" border="0" cellspacing="2" cellpadding="0">
    <tr id=mo>
      <td width="50%" align="center"><strong>INFORMACION DEL CONTACTO</strong>
      </td>
      <td height="20" align="center" ><strong>CONTACTOS CARGADOS</strong> </td>
    </tr>
    <tr>
      <td width="50%" align="center"><table width="99%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor=#E0E0E0>
          <tr>
            <td width="20%" nowrap><strong>Nombre</strong></td>
            <td width="70%" nowrap> <input name="nombre" type="text" id="nombre" size="35"></td>
            <td width="10%" nowrap><input type="reset" name="nuevo" value="Nuevo" title="Limpia el formulario, para agregar un nuevo cliente" onclick="document.all.editar.value=''"></td>
          </tr>
          <tr>
            <td  nowrap><strong>Dirección</strong></td>
            <td nowrap> <input name="direccion" type="text" id="direccion" size="35"></td>
          </tr>
          <tr>
            <td  nowrap><strong>Provincia</strong></td>
            <td nowrap>
            <SELECT name="provincia" >
            <option value='' <?if($_POST['provincia']=='') echo "selected"?>></option>
            <OPTION VALUE="Buenos Aires" <?if($_POST['provincia']=='Buenos Aires') echo "selected"?>>Buenos Aires</option>
            <OPTION VALUE="Catamarca" <?if($_POST['provincia']=='Catamarca') echo "selected"?>>Catamarca</option>
            <OPTION VALUE="Chaco" <?if($_POST['provincia']=='Chaco') echo "selected"?>>Chaco</option>
            <OPTION VALUE="Chubut" <?if($_POST['provincia']=='Chubut') echo "selected"?>>Chubut</option>
            <OPTION VALUE="Córdoba" <?if($_POST['provincia']=='Córdoba') echo "selected"?>>Córdoba</option>
            <OPTION VALUE="Corrientes" <?if($_POST['provincia']=='Corrientes') echo "selected"?>>Corrientes</option>
            <OPTION VALUE="Distrito Federal" <?if($_POST['provincia']=='Distrito Federal') echo "selected"?>>Distrito Federal</option>
            <OPTION VALUE="Entre Ríos" <?if($_POST['provincia']=='Entre Ríos') echo "selected"?>>Entre Ríos</option>
            <OPTION VALUE="Formosa" <?if($_POST['provincia']=='Formosa') echo "selected"?>>Formosa</option>
            <OPTION VALUE="Jujuy" <?if($_POST['provincia']=='Jujuy') echo "selected"?>>Jujuy</option>
            <OPTION VALUE="La Pampa" <?if($_POST['provincia']=='La Pampa') echo "selected"?>>La Pampa</option>
            <OPTION VALUE="La Rioja" <?if($_POST['provincia']=='La Rioja') echo "selected"?>>La Rioja</option>
            <OPTION VALUE="Mendoza" <?if($_POST['provincia']=='Mendoza') echo "selected"?>>Mendoza</option>
            <OPTION VALUE="Misiones" <?if($_POST['provincia']=='Misiones') echo "selected"?>>Misiones</option>
            <OPTION VALUE="Neuquén" <?if($_POST['provincia']=='Neuquén') echo "selected"?>>Neuquén</option>
            <OPTION VALUE="Río Negro" <?if($_POST['provincia']=='Río Negro') echo "selected"?>>Río Negro</option>
            <OPTION VALUE="Salta" <?if($_POST['provincia']=='Salta') echo "selected"?>>Salta</option>
            <OPTION VALUE="San Juan" <?if($_POST['provincia']=='San Juan') echo "selected"?>>San Juan</option>
            <OPTION VALUE="San Luis" <?if($_POST['provincia']=='San Luis') echo "selected"?>>San Luis</option>
            <OPTION VALUE="Santa Cruz" <?if($_POST['provincia']=='Santa Cruz') echo "selected"?>>Santa Cruz</option>
            <OPTION VALUE="Santa Fé" <?if($_POST['provincia']=='Santa Fé') echo "selected"?>>Santa Fé</option>
            <OPTION VALUE="Santiago del Estero" <?if($_POST['provincia']=='Santiago del Estero') echo "selected"?>>Santiago del Estero</option>
            <OPTION VALUE="Tierra del Fuego" <?if($_POST['provincia']=='Tierra del Fuego') echo "selected"?>>Tierra del Fuego</option>
            <OPTION VALUE="Tucumán" <?if($_POST['provincia']=='Tucumán') echo "selected"?>>Tucumán</option>
            </select>
            </td>
          </tr>
          <tr>
            <td  nowrap><strong>Localidad</strong></td>
            <td nowrap> <input name="localidad" type="text" id="localidad"></td>
          </tr>

          <tr>
            <td  nowrap> <strong>Código Postal</strong></td>
            <td nowrap> <input name="cod_postal" type="text" id="cod_postal"></td>
          </tr>
          <tr>
            <td  nowrap><strong>Teléfono</strong></td>
            <td nowrap> <input name="tel" type="text" id="tel"></td>
          </tr>
          <tr>
            <td  nowrap><strong>E-mail</strong></td>
            <td nowrap> <input name="mail" type="text" id="mail"></td>
          </tr>

          <tr>
            <td  nowrap><strong>Fax</strong></td>
            <td nowrap> <input name="fax" type="text" id="fax"></td>
          </tr>
          <tr>
            <td  nowrap><strong>ICQ</strong></td>
            <td nowrap> <input name="icq" type="text" id="icq"></td>
          </tr>
          <tr>
            <td  nowrap><strong>Entidad</strong></td>
            <td nowrap>
            <SELECT name="select_entidad" <?if($modulo=="Licitaciones"||$modulo=="Cobranzas")echo "disabled"?>>
             <option value="-1">Seleccione una entidad</option>
             <?
             $query="select nombre,id_entidad from entidad order by nombre";
             $res_entity=sql($query) or fin_pagina();
             while(!$res_entity->EOF)
             {?>
               <option value="<?=$res_entity->fields['id_entidad']?>" <?if($id_general==$res_entity->fields['id_entidad']) echo "selected"?>><?=cortar($res_entity->fields['nombre'],35);?></option>
             <?
              $res_entity->MoveNext();
             }
			?>
            </td>
          </tr>
          <tr>
         <td  nowrap><strong>Módulo</strong></td>
            <td>
            <?//generacion de los checkboxes para los modulos
             $mod->Move(0);
             while(!$mod->EOF)
             {?>
              <input type="checkbox" name="<?=$mod->fields['nombre']?>" value="<?=$mod->fields['id_modulo']?>" <?if($parametros["modulo"]==$mod->fields['nombre'])echo " checked "?>> <?=$mod->fields['nombre']?>
             <?
             $mod->MoveNext();
             }
            ?>
            </td>
          <tr>
            <td colspan="2" align="center" nowrap><strong>Observaciones</strong></td>
          </tr>
          <tr>
            <td colspan="2" nowrap> <div align="center">
                <textarea name="observaciones" cols="40" wrap="VIRTUAL" id="observaciones"></textarea>
              </div></td>
          </tr>
        </table></td>
        <td width="50%" align="center" nowrap>
        <div align="center">
         <select name="select_contactos" size="22" style="width:85%" onchange="set_datos();" onKeypress="set_datos();buscar_op(this);set_datos()" onblur="borrar_buffer()" onclick="borrar_buffer()">
            <? $datos_contactos->Move(0);
    while (!$datos_contactos->EOF)
    {
?>
            <option value="<?=$datos_contactos->fields['id_contacto_general']?>">
            <?=$datos_contactos->fields['nombre']?>
            </option>
            <?     $datos_contactos->MoveNext();

    } ?>
          </select>
        </div></td>
    </tr>
  </TABLE>
</center>
<TABLE align="center" width="45%" cellspacing="0">
<tr><td width="15%"> <input type="submit" name="boton" value="Guardar" style="width:100%" onClick="return control_datos()">
</TD>
<td width="15%">
<input type="submit" name="boton" value="Eliminar" style="width:100%" onClick=
"
var nombre_contacto;
if (document.all.select_contactos.selectedIndex==-1)
 {
    alert ('Debes elegir un contacto');
    return false;
 }
 else
 {
  cliente.value=nombre_contacto=document.all.select_contactos.options[document.all.select_contactos.selectedIndex].text;
 }
 return (confirm ('¿Está seguro que desea eliminar '+nombre_contacto+'?'))
">
</td>
<td width="15%">
<input type="button" name="boton" value="Salir" style="width:100%" onclick="window.close()">
</td>
</tr>
</TABLE>
<INPUT type="hidden" name="cliente">
</form>
</body>
</html>
<?echo fin_pagina();?>