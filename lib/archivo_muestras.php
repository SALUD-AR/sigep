<?php
/*
$Creador:fernando $

$Author: ferni $
$Revision: 1.3 $
$Date: 2006/11/08 18:48:30 $
*/
//ya esta incluido el html header
// y algunas variables ya estan en la pagina que le incluyo

require_once("../config.php");

if ($parametros["cmd"]){
 download_file($parametros["ID"]);
 exit();
}

$solo_lectura=$parametros["solo_lectura"];
$id_licitacion_muestra=$parametros["id_licitacion_muestra"];
echo $html_header;
echo "<script languaje='javascript' src='$html_root/lib/NumberFormat150.js'></script>";


if ($id_subir)
	$link_materiales=encode_link("$html_root/modulos/licitaciones/renglones_materiales.php",array("id_subir"=>$id_subir));

if (!$ID) $ID=$parametros["ID"];

//si tiene permiso de modificar se va a crear un formulario
//para poder enviar la informacion
$tiene_permiso_modificar=permisos_check("inicio","modificar_archivo_muestras");

if ($solo_lectura) $estilos_tabla="cellpading=0 cellspacin=0 border=1 class=border bgcolor=$bgcolor2";

if ($solo_lectura && !$tiene_permiso_modificar)
      {
      $readonly="readonly";
      $disabled="disabled";
      }



if ($tiene_permiso_modificar && $solo_lectura) {
             if ($_POST["aceptar"]=="Aceptar"){
                         $items_muestras=$_POST["items_muestras"];
                         $db->StartTrans();
                         $fecha_devolucion=fecha_db($_POST["fecha_devolucion"]);
                         $descripcion=$_POST["descripcion"];
                         //Esto anda solamente cuando hay solo un subido_lic OC
                         $sql_subir="update licitacion_muestra set
                                                    fecha_devolucion='$fecha_devolucion',
                                                    descripcion='$descripcion'
                                                    where id_licitacion_muestra=$id_licitacion_muestra
                                                    ";
                        sql($sql_subir) or fin_pagina();

                        for ($i=0;$i<$items_muestras;$i++){

                          $id_renglon=$_POST["id_renglon_muestras_$i"];
                         if ($_POST["items_muestras_$i"]==1) {



                                     $sql="select id_renglon from renglones_muestra
                                           where id_licitacion_muestra=$id_licitacion_muestra
                                           and id_renglon=$id_renglon
                                           ";
                                     $res=sql($sql) or fin_pagina();

                                     if (!$res->recordcount()){
                                                        $sql="insert into renglones_muestra
                                                             (id_licitacion_muestra,id_renglon)
                                                              values
                                                             ($id_licitacion_muestra,$id_renglon)
                                                             ";
                                                         sql($sql) or fin_pagina();
                                                         }
                                      //insertar_estado($id_renglon,1,1);
                                      } //del post items
                                      else
                                          {
                                           $sql="delete from renglones_muestra
                                                 where id_licitacion_muestra=$id_licitacion_muestra and id_renglon=$id_renglon";
                                           sql($sql) or fin_pagina();
                                           }
                     } //del for


                    if ($db->CompleteTrans()) {
                                      $exito=1;
                                      $msg="Los cambios se efectuaron con éxito";
                                      }
                                       else {
                                          $exito=0;
                                          $msg="Error:No se pudo actualizar los datos";
                                       }

             } // del aceptar

} //del if de solo_lectura y tiene permiso modificar
?>
<script>
   function borrar_renglones_muestras()
   {
    var i=0;
    var cant;
    var sentencias;
    var y=0;
    var j=0;
    var ejecutar;

    sentencias=new Array();
    bloquear=new Array();

    items_aux=parseInt(document.form1.items_muestras.value);

    //se fija si hay mas de un chekbox
    if (typeof(document.form1.chk_muestras.length)!='undefined'){

           i=0;
           j=0;
	   while (i < document.form1.chk_muestras.length)
		{
			if (document.form1.chk_muestras[i].checked)
				 {
				  y=i+1;
                  bloquear[j]="document.form1.items_muestras_"+i+".value=0";
				  sentencias[j]="document.all.tabla_renglones_muestras.deleteRow("+ y +")";
                                  j++;
				 }
		 i++;
	     }//del while

           i=sentencias.length-1;
	   while(i>=0)
	      {
              eval(bloquear[i]);
	      eval(sentencias[i]);
	      i--;
              items_aux--;
	      }//del segundo while
     }//del if

     else{

      //se fija que haya al menos un renglon
    if (typeof(document.form1.chk_muestras)!='undefined'){
	if (document.form1.chk_muestras.checked)  //hay un renglon
         {

         ejecutar="typeof(document.form1.items_muestras_"+i+")";
         if (eval(ejecutar)!='undefined')
                                 {
                                 ejecutar="document.form1.items_muestras_"+i+".value=0";
                                 eval(ejecutar);
                                 }
	 document.all.tabla_renglones_muestras.deleteRow(1);
         items_aux--;
         } //del if
	}//del if
   }
   //document.form1.items_muestras.value=items_aux;
   }//del fin de la funcion que borra productos


  function control_datos_muestras(){

  var sen;
  var hay_orden;
  var items;
 <?
 if (!$solo_lectura){
 ?>
 if (document.form1.visible_muestras.value=="visible"){
 <?
 }
 ?>

          if (parseInt(document.form1.items_muestras.value)==0 || typeof(document.form1.chk_muestras)=='undefined' ){
            alert("Debe Haber al menos un renglon");
            return false;
         }



         if (document.form1.fecha_devolucion.value=="")
             {
             alert("Debe elegir una fecha de devolución");
             return false;
              }
        if (document.form1.descripcion.value=="")
             {
             alert("Debe ingresar una descripción");
             return false;
            }

         items=parseInt(document.form1.items_muestras.value);
 <?
 if (!$solo_lectura){
 ?>
 }//del primer if
 <?
 }
 ?>



  return true;
  }


function funcion_aceptar_muestras() {
       document.all.files_add.value = 'Aceptar';
       document.form1.submit();

  } //de la funcion funcion_aceptar_muestras



  var total=0;


  </script>
 <?
 //print_r($_POST);
cargar_calendario();
if ($msg) Aviso($msg);

 if ($solo_lectura){
     $sql="select entidad.nombre,simbolo,
                   licitacion_muestra.*,
                   licitacion.id_licitacion
           from licitacion join entidad using (id_entidad)
           join moneda using (id_moneda)
           join entrega_estimada using(id_licitacion)
           join licitacion_muestra using(id_entrega_estimada)
           where id_licitacion_muestra=$id_licitacion_muestra";
     $licitacion=sql($sql) or fin_pagina();
     $ID=$licitacion->fields["id_licitacion"];
     $fecha_devolucion=fecha($licitacion->fields["fecha_devolucion"]);
     $descripcion=$licitacion->fields["descripcion"];
     }
     else {
     //traigo los datos de la licitacion
     $sql="select entidad.nombre,fecha_entrega,plazo_entrega,simbolo from
           licitacion join entidad using (id_entidad)
           join moneda using (id_moneda)
           where id_licitacion=$ID";
     $licitacion=sql($sql) or fin_pagina();
   }

  $nombre_entidad=$licitacion->fields["nombre"];
  $simbolo=$licitacion->fields["simbolo"];
  $fecha_devolucion=fecha($licitacion->fields["fecha_devolucion"]) or $fecha_devolucion=$_POST["fecha_devolucion"];
  $descripcion=$licitacion->fields["descripcion"] or $descripcion=$_POST["descripcion"];

 if ($solo_lectura && $tiene_permiso_modificar) {
  $link=encode_link("archivo_muestras.php",array("ID"=>$parametros["ID"],
                                                "solo_lectura"=>$parametros["solo_lectura"],
                                                "id_licitacion_muestra"=>$parametros["id_licitacion_muestra"]));
 ?>
 <form name=form1 method=post action=<?=$link?>>
 <?
 }
 ?>
<input type="hidden" name="id_licitacion_muestras" value="<?=$ID?>">
<input type="hidden" name="control_ya_subido_muestras" value="0">
<table width=100% align=Center <?=$estilos_tabla?>>
<?
if (!$solo_lectura) {
?>
 <tr id=mo>
    <td>
      Nueva Muestra de <?=$nombre_entidad?>
    </td>
 </tr>
<?
}
else {






?>
 <tr id=mo>
   <td>
   Datos del Archivo de Muestras
   </td>
 </tr>
 <tr>
   <td alig=left>
     <table width=100% alig=left>
     <tr>
       <td id=ma_sf width=15%>Licitación Id:</td>
       <td><font color=red size=2><b><?=$ID?></b></font></td>
     </tr>
     <tr>
      <td id=ma_sf> Entidad:</td>
      <td align=left> <font color=red size=2><b><?=$nombre_entidad?></b></font> </td>
     </td>
     </tr>
<?
   if ($id_licitacion_muestra){

   $sql="select archivos.* from archivos
           join licitacion_muestra using(idarchivo)
           where id_licitacion_muestra=$id_licitacion_muestra";
     $archivos = sql($sql) or fin_pagina();
     $mc = substr($archivos->fields["subidofecha"],5,2);
     $dc = substr($archivos->fields["subidofecha"],8,2);
     $yc = substr($archivos->fields["subidofecha"],0,4);
     $hc = substr($archivos->fields["subidofecha"],11,5);

?>
     <tr>
      <td id=ma_sf> Archivo:</td>
      <td align=left>
          <table align=center width=100%>
            <tr id=ma>
		 <td width=50% align=left><b>Nombre</b></td>
		 <td width=25% align=center><b>Fecha de cargado</b></td>
		 <td width=25% align=left><b>Cargado por</b></td>
  	   </tr>
           <tr>
     	   <td align=left>
           <b>
           <?

          $link=encode_link($_SERVER["PHP_SELF"],array("ID"=>$ID,
                                                       "solo_lectura"=>$parametros["solo_lectura"],
                                                       "id_subir"=>$parametros["id_subir"],
                                                       "FileID"=>$archivos->fields["idarchivo"],
                                                       "cmd"=>"download"
                                                       ));
           ?>
           <a title='Archivo:<?echo $archivos->fields["nombrecomp"]."\n"?> Tamaño:<?=number_format($archivos->fields["tamañocomp"]/1024)?> Kb' href='<?=$link?>'>
           <img align=middle src=<?="$html_root/imagenes/zip.gif"?> border=0>
           </a>&nbsp;&nbsp;
    	    <a title='Archivo: <?=$archivos->fields["nombre"]."\n"?>Tamaño: <?=number_format($archivos->fields["tamaño"]/1024)?> Kb' href='<?=$link?>'>
            <?=$archivos->fields["nombre"]?>
            </a>
            </b>
            </td>
            <td><b><?="$dc/$mc/$yc $hc"?>hs.</b></td>
            <td><b><?=$archivos->fields["subidousuario"]?></b></td>
           </tr>
          </table>
       </td>
     </tr>
     <?
     }//del if de los archivos
     ?>
    </table>
   </td>
 </tr>
<?
}

?>
 <tr>
    <td>
     <table width=100% align=center>
        <tr>
           <td  id=ma_sf width=40% ><b>Fecha de Vencimiento de la Muestra</b></td>
           <td>
           <!-- default fecha de hoy -->
           <input type=text name="fecha_devolucion" value="<?=$fecha_devolucion?>" size=12 readonly>
           <?echo link_calendario("fecha_devolucion")?>
           </td>
        </tr>
        <tr>
          <td id=ma_sf colspan=2 valign=top><b>Descripción</b></td>
        </tr>
        <tr>
         <td alig=left colspan=2 align=left valign=top>
        <textarea name='descripcion' rows=3 style="width:100%" <?=$readonly?>> <?=$descripcion?></textarea>
        <script>
        //fnNoQuotes(document.all.descripcion);
        </script>
        </td>
        </tr>
     </table>
    </td>
 </tr>
 <tr>
 <?

 if (!$_POST["traer_renglones"]) $checked_oc="checked";
                      else{
                       if ($_POST["traer_renglones"]=="todos") $checked_todos="checked";
                                                          else $checked_oc="checked";
                      }
 ?>
 <td align=center>
   <table width=100% align=center>
    <tr>
        <td align=left width=50% id=ma_sf> Productos </td>
        <td>
          Todos
          <input type=radio class='estilos_check' name="traer_renglones" value="todos" <?=$checked_todos?>>
        </td>
        <td>
          Preadjudicados
          <input type=radio class='estilos_check' name="traer_renglones" value="preadjudicados" <?=$checked_oc?>>
        </td>
        <td>
         <input type=submit name="traer" value="Traer" <?=$disabled?> >
        </td>
   </tr>
   </table>
  </td>
 </tr>
 <tr>
     <td>
         <!-- Tabla con los renglones-->
         <table width=100% id="tabla_renglones_muestras" align=center >
            <tr id=mo>
               <td width=1%>&nbsp;</td>
               <td>Renglon</td>
               <td>Descripción</td>
            </tr>

            <!-- Parte Dinamica de los productos de orden de compra-->
            <?
            //CONSULTAS QUE TRAEN LOS RENGLONES
            //LEER SI QUIEREN ENTENDER LA CONSULTA
            //Todas estas consulas son para traer los datos de los renglones
            //las consultas son diferentes dependiendo de donde se llame a la pagina
            if ($solo_lectura) {
                                //aca entra si viene para modificar los datos de la subida
                                //de la orden de compra
                                if ($_POST["traer"]=="Traer"){
                                         if ($_POST["traer_renglones"]=="preadjudicados") {
                                                   $sql="
                                                       select renglon.id_renglon,renglon.codigo_renglon,renglon.cantidad,renglon.cantidad as cantidad_testigo,
                                                       renglon.titulo, renglon.total as  precio_testigo,renglon.total as precio
                                                       from renglon where id_licitacion=$ID
                                                       and id_renglon in   (
                                                             select id_renglon from historial_estados where id_estado_renglon=2 and activo=1
                                                        )
                                                       order by codigo_renglon
                                                       ";
                                                  }
                                                  elseif ($_POST["traer_renglones"]=="todos") {
                                                           $sql="
                                                            select renglon.id_renglon,renglon.codigo_renglon,renglon.cantidad,renglon.cantidad as cantidad_testigo,
                                                            renglon.titulo, renglon.total as precio,renglon.total as precio_testigo
                                                            from renglon where id_licitacion=$ID
                                                            order by codigo_renglon
                                                            ";
                                                            }
                                            }
                                    else{
                                     $sql="select renglon.*
                                           from licitacion_muestra
                                           join renglones_muestra using(id_licitacion_muestra)
                                           join renglon using(id_renglon)
                                           where id_licitacion_muestra=$id_licitacion_muestra
                                           order by codigo_renglon";

                                      }
                              }
                             else
                                 {
                                  //Aca entra siempre que venga de agregar archivo
                                  if ($_POST["traer_renglones"]=="preadjudicados" || !$_POST["traer_renglones"])
                                   $estado_renglon=" and id_renglon in  (select id_renglon from historial_estados where id_estado_renglon=2 and activo=1)";

                                  $sql="
                                   select renglon.id_renglon,renglon.codigo_renglon,renglon.cantidad,
                                   renglon.titulo, renglon.total as precio
                                   from renglon where id_licitacion=$ID
                                   $estado_renglon
                                   order by codigo_renglon
                                   ";
                                }

            $resultado=sql($sql) or fin_pagina();
            $cantidad=$resultado->recordcount();
            ?>
            <input type=hidden name=items_muestras value="<?=$cantidad?>">
            <?
            for ($i=0;$i<$cantidad;$i++){
            ?>
               <input type=hidden name="items_muestras_<?=$i?>" value="1">
               <input type=hidden name="id_renglon_muestras_<?=$i?>" value="<?=$resultado->fields["id_renglon"]?>">
               <td><input type=checkbox name="chk_muestras" value="1"></td>
               <td>
               <input type=text name="codigo_muestras_<?=$i?>" value="<?=$resultado->fields["codigo_renglon"]?>" size=15 class="text_4" readonly>
               </td>
               <td>
                  <input type=text name="descripcion_muestras_<?=$i?>" value="<?=$resultado->fields["titulo"]?>" readonly size=70 class="text_4" readonly>
               </td>
               </tr>
            <?

            $resultado->movenext();
            }//del for

            ?>
            <!-- Fin de la parte dinamina -->
            <tr id=ma>
               <td colspan=7>
			   	<table width="100%">
					<tr>
						<td width="50%" align="left">
	                      <input type=button  name="muestras_borrar" value="Borrar Renglones" style="width:150" onclick="borrar_renglones_muestras();" <?=$disabled?>>
						</td>
		               <td width="50%" align="right">
        	              <?if ($id_subir) echo "<input type=button  value='Materiales' style='width:150' onclick=window.open('$link_materiales')>" ?>
            		   </td>
					</tr>
				</table>
               </td>
            </tr>
         </table>
     </td>
 </tr>
 <?
 if ($solo_lectura){
 ?>
   <tr>
     <td align=center>
       <?
       if ($tiene_permiso_modificar){
       ?>
       <input type=submit name=aceptar value=Aceptar onclick="return control_datos_muestras()">
       &nbsp;
       <?
       }
       ?>
       <input type=button name=cerrar value=Cerrar onclick="window.close()">
     </td>
   </tr>
 <?
 }
 ?>
 </table>
 <?
  if ($solo_lectura && $tiene_permiso_modificar) {
 ?>
 </form>
 <?
 echo fin_pagina();
  }
 ?>