<?php
/*
$Creador:fernando $

$Author: gabriel $
$Revision: 1.40 $
$Date: 2005/11/15 19:17:11 $
*/
//ya esta incluido el html header
// y algunas variables ya estan en la pagina que le incluyo
require_once("../config.php");

$muestras=$parametros["muestras"] or $muestras=$_POST["muestras"];
$solo_lectura=$parametros["solo_lectura"];
$id_subir=$parametros["id_subir"];

if ($parametros["cmd"]){
 download_file($parametros["ID"]);
 exit();
}

echo $html_header;
echo "<script languaje='javascript' src='$html_root/lib/NumberFormat150.js'></script>";

if ($id_subir)
	$link_materiales=encode_link("$html_root/modulos/licitaciones/renglones_materiales.php",array("id_subir"=>$id_subir));

if (!$ID) $ID=$parametros["ID"];

//si tiene permiso de modificar se va a crear un formulario
//para poder enviar la informacion
$tiene_permiso_modificar=permisos_check("inicio","modificar_archivo_oc");

if ($solo_lectura) $estilos_tabla="cellpading=0 cellspacin=0 border=1 class=border bgcolor=$bgcolor2";

if ($solo_lectura && !$tiene_permiso_modificar)
      {
      $readonly="readonly";
      $disabled="disabled";

      }



if ($tiene_permiso_modificar && $solo_lectura) {
             if ($_POST["aceptar"]=="Aceptar"){
                         $db->StartTrans();
         	             $fecha_subido=date("Y-m-d"); //fecha actual
		                 //$fecha_vencimiento=fecha_db($_POST["fecha_vencimiento"]);
                         $fecha_notificacion=fecha_db($_POST["fecha_notificacion"]);
                         $lugar_entrega=$_POST["lugar_entrega"];
                         $nro_orden=$_POST["nro_orden"];
                         $id_dias=$_POST["dias"];
                         $tipo_dias=$_POST["tipo_dias"];
                         $items=$_POST["items"];
                         $fecha_vencimiento=arma_fecha_venc($_POST["fecha_notificacion"],$id_dias,$tipo_dias);
                         $fecha_vencimiento=fecha_db($fecha_vencimiento);
                         //Esto anda solamente cuando hay solo un subido_lic OC
                         $sql_subir="update subido_lic_oc set fecha_notificacion='$fecha_notificacion',
                                                    lugar_entrega='$lugar_entrega',
                                                    nro_orden='$nro_orden',
                                                    id_dias=$id_dias,
                                                    tipo_dias='$tipo_dias',
                                                    vence_oc='$fecha_vencimiento'
                                                    where id_subir=$id_subir
                                                    ";
                        sql($sql_subir) or fin_pagina();

                     //ahora inserto los renglones que van a comprar
                     //print_r($_POST);
                     $precio_delete=0;
                     $precio_insert=0;
                     $precio_update=0;
                     for ($i=0;$i<$items;$i++){

                        $id_renglon=$_POST["id_renglon_$i"];
                        $precio=$_POST["precio_$i"];
                        $cantidad=$_POST["cant_$i"];

                         if ($_POST["items_$i"]==1) {

                                           $sql="select cantidad,precio from renglones_oc
                                                 where id_subir=$id_subir and id_renglon=$id_renglon
                                                ";
                                           $res=sql($sql) or fin_pagina();

                                           if ($res->recordcount()>=1){
                                                       $sql=" update  renglones_oc set
                                                              precio=$precio,cantidad=$cantidad
                                                              where id_subir=$id_subir and id_renglon=$id_renglon
                                                            ";
                                                       sql($sql) or fin_pagina();
                                                       $precio_ant=$res->fields["precio"];
                                                       $cantidad_ant=$res->fields["cantidad"];
                                                       $precio_update+=$precio_ant*$cantidad_ant;
                                                       $precio_insert+=$precio*$cantidad;
                                                       }
                                                       else {
                                                        $sql="insert into renglones_oc
                                                             (id_subir,id_renglon,precio,cantidad)
                                                              values
                                                             ($id_subir,$id_renglon,$precio,$cantidad)
                                                             ";
                                                         sql($sql) or fin_pagina();
                                                         $precio_insert+=$precio*$cantidad;
                                                         }
                                      //insertar_estado($id_renglon,1,1);

                                      } //del post items
                                      else
                                          {
                                           $sql="select cantidad,precio from renglones_oc
                                                 where id_subir=$id_subir and id_renglon=$id_renglon
                                                ";
                                           $res=sql($sql) or fin_pagina();
                                           $precio=$res->fields["precio"];
                                           $cantidad=$res->fields["cantidad"];
                                           $sql="delete from renglones_oc where id_subir=$id_subir and id_renglon=$id_renglon";
                                           sql($sql) or fin_pagina();
                                           $precio_delete+=$precio*$cantidad;
                                           }
                     } //del for


                     /*Comento lo que modifica el monto ganado
                     $monto=$precio_insert - ($precio_delete+$precio_update);
                     //actualizo el monto ganado
                     $sql="select licitacion.id_licitacion,monto_ganado from licitacion join subido_lic_oc using(id_licitacion)
                           where id_subir=$id_subir";
                     $res1=sql($sql) or fin_pagina();
                     $id_licitacion=$res1->fields["id_licitacion"];
                     $monto_ganado=$res1->fields["monto_ganado"];

                     if ($monto_ganado) {
                               $sql="update licitacion set monto_ganado=monto_ganado - $precio_update
                                     where id_licitacion=$id_licitacion";
                               sql($sql) or fin_pagina();
                               $monto_ganado-=$precio_update;

                     }
                     if ($monto_ganado) {
                             $sql="update licitacion set monto_ganado=monto_ganado - $precio_delete
                                   where id_licitacion=$id_licitacion";
                             sql($sql) or fin_pagina();
                     }

                     $sql="update licitacion set monto_ganado=monto_ganado + $precio_insert
                           where id_licitacion=$id_licitacion";
                     sql($sql) or fin_pagina();

                     */
	
                    if ($db->CompleteTrans()) {
                                      $exito=1;
                                      $msg="Los cambios se efectuaron con éxito";
                                      }
                                       else {
                                          $exito=0;
                                          $msg="Error:No se pudo actualizar los datos";
                                       }
                   //enviar mail si subio OC con renglones repetidos
                   if ($exito && $_POST["control_ya_subido"]) {
                   	$contenido = "[$fecha_subido] El usuario '".$_ses_user["name"]."', para la licitación: ".$_POST["id_licitacion"].",subió al sistema archivos de tipo Orden de Compra con renglones coincidentes a otros ya definidos en ordenes anteriores.";
                   	enviar_mail("juanmanuel@coradir.com.ar","Orden de Compra Repetida en Licitacion",$contenido,"","","",0);
                   }
             } // del aceptar
} //del if de solo_lectura y tiene permiso modificar
?>
<script>
   function borrar_renglones()
   {
    var i=0;
    var cant;
    var sentencias;
    var y=0;
    var j=0;
    var ejecutar;

    sentencias=new Array();
    bloquear=new Array();

    items_aux=parseInt(document.form1.items.value);

    //se fija si hay mas de un chekbox
    if (typeof(document.form1.chk.length)!='undefined'){

           i=0;
           j=0;
	   while (i < document.form1.chk.length)
		{
			if (document.form1.chk[i].checked)
				 {
				  y=i+1;
                  bloquear[j]="document.form1.items_"+i+".value=0";
				  sentencias[j]="document.all.tabla_renglones.deleteRow("+ y +")";
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
     if (typeof(document.form1.chk)!='undefined'){
	if (document.form1.chk.checked)  //hay un renglon
         {

         ejecutar="typeof(document.form1.items_"+i+")";
         if (eval(ejecutar)!='undefined')
                                 {
                                 ejecutar="document.form1.items_"+i+".value=0";
                                 eval(ejecutar);
                                 }
	 document.all.tabla_renglones.deleteRow(1);
         items_aux--;
         } //del if
	}//del if
   }
   //document.form1.items.value=items_aux;
   }//del fin de la funcion que borra productos

  function control_datos(){

  var sen;
  var hay_orden;
  var items;
 
 <?
 if (!$solo_lectura){
 ?>
 if (document.form1.visible.value=="visible"){
 <?
 }
 ?>
         if (parseInt(document.form1.items.value)==0 || typeof(document.form1.chk)=='undefined' ){
            alert("Debe Haber al menos un renglon");
            return false;
         }

          if (document.form1.nro_orden.value==""){
             alert("Debe ingresar un nro de orden");
             return false;
         }


         if (document.form1.dias.options[document.form1.dias.selectedIndex].value==-1)
         {
             alert("Debe elegir un día");
             return false;
         }

         if (document.form1.tipo_dias.options[document.form1.tipo_dias.selectedIndex].value==-1)
         {
             alert("Debe elegir un tipo de día");
             return false;
         }
        if (document.form1.lugar_entrega.value=="")
         {
             alert("Debe ingresar un lugar de entrega");
             return false;
         }

         items=parseInt(document.form1.items.value);
         for(i=0;i<items;i++){
             //armo la sentencia
             sen="document.form1.items_"+i+".value==1";

             if (eval(sen)) {

                             //sen="document.form1.cant_"+i+".value==''";
                             sen=eval("document.form1.cant_"+i);
                             if (typeof(sen)!='undefined' && sen.value=='') {
                                    alert('Debe ingresar cantidad en renglon');
                                    return false;
                                    }
                             sen=eval("document.form1.precio_"+i);
                             if (typeof(sen)!='undefined' && sen.value=='') {
                                    alert('Debe ingresar precio en renglones');
                                    return false;
                                    }

                            }
         }//del for
<?
if (!$solo_lectura){
?>
    }//del primer if
<?
 }
?>
	if (document.all.t_vencimiento_entrega.value==''){
		alert("Debe especificar la cantidad de días hasta el vencimiento del plazo de presentación de la garantía de contrato");
		return false;
  }
  return true;
  }

  var total=0;

  function calcular_total() {
    var i;
    for (i=0;i<document.all.items.value;i++){
    if (typeof(eval("document.all.cant_"+i))!='undefined'){

      total+= eval("document.all.cant_"+i+".value") * eval("document.all.precio_"+i+".value")
    }
    }//del for

  }

  function es_orden_de_compra(){
    var i;
    if (document.all.files_cant.value == 1)
    {   if (document.all.tipo_archivo.options[document.all.tipo_archivo.selectedIndex].text == 'Orden de Compra') return (1);
    }
    else {
        for (i=0;i < document.all.files_cant.value; i++){
          if (document.all.tipo_archivo[i].options[document.all.tipo_archivo[i].selectedIndex].text == 'Orden de Compra') return (1);
        }
    }
    return (0);
  }

  // si algun select es orden de compra abre el popup sino funciona como antes
  function funcion_aceptar() {
    if (es_orden_de_compra()){
      total=0;
      calcular_total();

      document.all.total.value=redondear_numero(total);

      //document.all.total.value=formato_BD(total);
      window.open('<?=$html_root?>/modulos/licitaciones/control_oc.php?id_licitacion='+document.all.id_licitacion.value,'','fullscreen=1,scrollbars=0')
    }
    else {

       document.all.files_add.value = 'Aceptar';
       document.form1.submit();
    }
  }
  </script>
 <?
 //print_r($_POST);
cargar_calendario();
if ($msg) Aviso($msg);

 if ($solo_lectura){
     $sql="select entidad.nombre,fecha_entrega,plazo_entrega,simbolo,
                   subido_lic_oc.id_dias,subido_lic_oc.tipo_dias,
                   subido_lic_oc.nro_orden,
                   subido_lic_oc.vence_oc,
                   subido_lic_oc.lugar_entrega,
                   subido_lic_oc.fecha_notificacion,
                   licitacion.id_licitacion
           from licitacion join entidad using (id_entidad)
           join moneda using (id_moneda)
           join subido_lic_oc using(id_licitacion)
           where id_subir=$id_subir";
     $licitacion=sql($sql) or fin_pagina();
     $cantidad_dias=$licitacion->fields["id_dias"];
     $tipos=$licitacion->fields["tipo"];
     $ID=$licitacion->fields["id_licitacion"];
     $fecha_vencimiento=fecha($licitacion->fields["vence_oc"]);
     $tipos_dias=$licitacion->fields["tipo_dias"];
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
  $plazo_entrega=$_POST["plazo_entrega"] or $plazo_entrega=$licitacion->fields["plazo_entrega"];
  $fecha_notificacion=$_POST["fecha_notificacion"] or $fecha_notificacion=fecha($licitacion->fields["fecha_notificacion"]) or $fecha_notificacion=date("d/m/Y");
  $nro_orden=$_POST["nro_orden"] or $nro_orden=$licitacion->fields["nro_orden"];
  $lugar_entrega=$_POST["lugar_entrega"] or $lugar_entrega=$licitacion->fields["lugar_entrega"];
  $cantidad_dias=$_POST["dias"] or $cantidad_dias=$licitacion->fields["id_dias"];


 if ($solo_lectura && $tiene_permiso_modificar) {
  $link=encode_link("archivo_orden_de_compra.php",array("ID"=>$parametros["ID"],
                                                        "solo_lectura"=>$parametros["solo_lectura"],
                                                        "id_subir"=>$parametros["id_subir"]));
 ?>
 <form name=form1 method=post action=<?=$link?>>
 <?
 }
 ?>
<input type="hidden" name="id_licitacion" value="<?=$ID?>">
<input type="hidden" name="control_ya_subido" value="0">
<input type="hidden" name="total" value="0">
<input type="hidden" name="flag_tipo" value="1">
<!-- flag tipo =1 es orden de compra flag_tipo=0 es de muestras -->
<table  width=100% align=Center <?=$estilos_tabla?>>
<?
if (!$solo_lectura) {
?>
 <tr id=mo>
    <td>
       Archivo de Orden de Compra/Muestras <?=$nombre_entidad?> </span>
    </td>
 </tr>
<?
}
else {
?>
 <tr id=mo>
   <td>
   Datos del Archivo de Orden de Compra/Muestras
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
   if ($id_subir){

   $sql="select archivos.* from archivos
           join subido_lic_oc using(idarchivo)
           where id_subir=$id_subir";
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
          <tr>
           <td width=30% id=ma_sf><b>OC N°:</b></td>
           <td><input type=text name=nro_orden value="<?=$nro_orden?>" size=35  onkeypress="return no_quotes();"  onpaste="fnPaste(this)" <?=$readonly?>></td>
          </tr>
          <tr>
           <td  id=ma_sf><b>Fecha de Notificación:</b></td>
           <td>
           <!-- default fecha de hoy -->
           <input type=text name="fecha_notificacion" value="<?=$fecha_notificacion?>" size=12 readonly>
           <?echo link_calendario("fecha_notificacion")?>
           </td>
        </tr>
        <tr>
           <td id=ma_sf><b>Vencimiento especificado por el cliente:</b></td>
           <td>
           <!-- default fecha entrega de la lic -->
           <b><?=$plazo_entrega?></b>
           </td>
        </tr>
        <tr>
           <td id=ma_sf><b>Dias Vencimiento OC:</b></td>
           <td>
             <table width=100% align=center>
             <?
             $sql="select * from dias_oc where activo=1 order by dias";
             $dias=sql($sql) or fin_pagina();
             ?>
                  <tr>
                   <td>
                   <b>Días</b> &nbsp;
                   <select name=dias <?=$disabled?>>
                      <option value=-1>Elija una Opción</option>
                      <?

                      for($i=0;$i<$dias->recordcount();$i++){
                       if ($dias->fields["id_dias"]==$cantidad_dias) $selected="selected";
                                                               else  $selected="";
                      ?>
                      <option value="<?=$dias->fields["id_dias"]?>" <?=$selected?>><?=$dias->fields["dias"]?></option>
                      <?
                      $dias->movenext();
                      }
                      ?>
                   </select>
                   </td>
                   <td><b>Tipo</b>&nbsp;
                   <select name=tipo_dias <?=$disabled?>>
                      <option value=-1>Elija una Opción</option>
                      <option  <?if ($_POST["tipo_dias"]=="Hábiles" || $tipos_dias=="Hábiles") echo "selected"?>>Hábiles</option>
                      <option  <?if ($_POST["tipo_dias"]=="Corridos" ||$tipos_dias=="Corridos") echo "selected"?>>Corridos</option>
                   </select>
                   </td>
                   </tr>
              </table>
           </td>
        </tr>
<?if ($solo_lectura) { ?>
        <tr>
           <td id=ma_sf><b>Fecha de Vencimiento</b></td>
           <td><b><?=$fecha_vencimiento?></b></td>
        </tr>
<?}?>
        <tr>
          <td id=ma_sf valign=top><b>Lugar de Entrega:</b></td>
          <td alig=left>
        <textarea name='lugar_entrega' rows=3 style="width:100%" <?=$readonly?>><?=$lugar_entrega?></textarea>
        <script>fnNoQuotes(document.all.lugar_entrega); fnNoQuotes(document.all.nro_orden)</script>
        </td>
      </tr>
      <tr>
      	<td id="ma_sf">
      		Plazo para entregar Gtía. de Contrato: 
      	</td>
      	<td>
      		<input type="text" name="t_vencimiento_entrega" value="<?=(($_POST["t_vencimiento_entrega"])?$_POST["t_vencimiento_entrega"]:8)?>" align="right" onkeypress="return filtrar_teclas(event,'0123456789');"> días
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
         <input type=submit name=traer value="Traer" <?=$disabled?>>
        </td>
   </tr>
   </table>
  </td>
 </tr>
 <tr>
     <td>
         <!-- Tabla con los renglones-->
         <table width=100% id="tabla_renglones" align=center >
            <tr id=mo>
               <td width=1%>&nbsp;</td>
               <td>Renglon</td>
               <td>Cant.</td>
               <td>Descripción</td>
               <td>Precio</td>
               <td>Cant Test</td>
               <td>Testigo</td>
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
                                     $sql="select renglon.id_renglon,renglon.codigo_renglon,renglones_oc.cantidad,renglon.cantidad as cantidad_testigo,
                                           renglon.titulo, renglones_oc.precio as precio,renglon.total as precio_testigo
                                           from  subido_lic_oc
                                           join renglones_oc using(id_subir)
                                           join renglon using(id_renglon)
                                           where subido_lic_oc.id_subir=$id_subir
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
            <input type=hidden name=items value="<?=$cantidad?>">
            <?
            for ($i=0;$i<$cantidad;$i++){
                    if ($solo_lectura){
                                     $precio_testigo=$resultado->fields["precio_testigo"];
                                     $cantidad_testigo=$resultado->fields["cantidad_testigo"];
                                     }
                                     else{
                                      $precio_testigo=$resultado->fields["precio"];
                                      $cantidad_testigo=$resultado->fields["cantidad"];
                                      }
            ?>
               <input type=hidden name="items_<?=$i?>" value="1">
               <input type=hidden name="id_renglon_<?=$i?>" value="<?=$resultado->fields["id_renglon"]?>">
               <td><input type=checkbox name="chk" value="1"></td>
               <td>
               <input type=text name="codigo_<?=$i?>" value="<?=$resultado->fields["codigo_renglon"]?>" size=15 class="text_4" readonly>
               </td>
               <td>
                <input type=text name="cant_<?=$i?>" value="<?=$resultado->fields["cantidad"]?>" size=3 onkeypress="return filtrar_teclas(event,'0123456789');" <?=$readonly?>>
               </td>
               <td>
                  <input type=text name="descripcion_<?=$i?>" value="<?=$resultado->fields["titulo"]?>" readonly size=70 class="text_4" readonly>
               </td>
               <td>
                <input type=text name="precio_<?=$i?>" value="<?=number_format($resultado->fields["precio"],"2",".","")?>" size=8 onkeypress="return filtrar_teclas(event,'0123456789.');" readonly <?=$readonly?>>
                <input type="button" name="editar_<?=$i?>" value="Editar" onclick="document.all.precio_<?=$i?>.readOnly=0"> 
               </td>
               <td align="center">
                <input type=text name="cant_testigo" value="<?=$cantidad_testigo?>" style="text-align:center" size=3 class="text_4" readonly>
               </td>
               <td>
                   <table width=100% align=center>
                     <tr>
                         <td>
                         <b><?=$simbolo?></b>
                         </td>
                         <td>
                         <input type=text name="precio_testigo_<?=$i?>" value="<?=formato_money($precio_testigo)?>" readonly size=10 class="text_3">
                         </td>
                    </tr>
                  </table>
               </td>
               </tr>
            <?

            $total_precio_testigo+=$precio_testigo*$cantidad_testigo;
            $total_precio+=$resultado->fields["precio"]*$resultado->fields["cantidad"];
            $resultado->movenext();
            }//del for

            ?>
                    <tr>
                      <td colspan=5 align=right id=ma_sf>
                      Totales
                      </td>
                      <td>
                        <table width=100% align=center>
                            <tr>
                               <td>
                                <font color=red>
                                <b><?=$simbolo?></b>
                                </font>
                                </td>
                                <td align=right>
                                <font color=red>
                                <b><?=formato_money($total_precio)?></b>
                                </font>
                                </td>
                                </font>
                             </tr>
                           </table>
                      </td>
                      <td>
                        <table width=100% align=center>
                            <tr>
                               <td>
                                <font color=red>
                                <b><?=$simbolo?></b>
                                </font>
                                </td>
                                <td align=right>
                                <font color=red>
                                <b><?=formato_money($total_precio_testigo)?></b>
                                </font>
                                </td>
                                </font>
                             </tr>
                           </table>
                      </td>
                    </tr>

            <!-- Fin de la parte dinamina -->
            <tr id=ma>
               <td colspan=7>
			   	<table width="100%">
					<tr>
						<td width="50%" align="left">
	                      <input type=button  name="ordcompra_borrar" value="Borrar Renglones" style="width:150" onclick="borrar_renglones();" <?=$disabled?>>
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
       <input type=submit name=aceptar value=Aceptar onclick="return control_datos()">
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