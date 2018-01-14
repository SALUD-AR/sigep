<?
/*
Author: ferni

modificada por
$Author: marco_canderle $
$Revision: 1.1 $
$Date: 2005/12/15 18:15:51 $
*/

require_once ("../../config.php");

if ($parametros["id_accidentes_lab"]){
	$id_accidentes_lab = $parametros["id_accidentes_lab"];
}
else{
	$id_accidentes_lab = $_POST["id_accidentes_lab"];
}

if ($parametros["id_legajo"]){
	$id_legajo = $parametros["id_legajo"];
}
else{
	$id_legajo = $_POST["id_legajo"];
}

if ($_POST['guardar']=="Guardar Nuevo Accidente"){
   $db->StartTrans();
   $fech_inicio=Fecha_db($_POST['fech_inicio']);
   $art=$_POST['art'];
   $num_art=$_POST['num_art'];
   $fecha_inicio_art=Fecha_db($_POST['fecha_inicio_art']);
   $titulo=$_POST['titulo'];
   $descripcion=$_POST['descripcion'];

    $q="select nextval('accidentes_lab_id_accidentes_lab_seq') as id_acc_lab";
    $res=sql($q) or fin_pagina();
    $id_acc_lab=$res->fields['id_acc_lab'];
       
    $query="insert into personal.accidentes_lab
             (id_accidentes_lab, fech_inicio, art, num_art, fech_inicio_art, descripcion,id_legajo,titulo)
             values
             ($id_acc_lab, '$fech_inicio', '$art', '$num_art', '$fecha_inicio_art', '$descripcion',$id_legajo,'$titulo')";

    sql($query, "Error al insertar/actualizar el Accidente") or fin_pagina();
    
    $accion="Los datos del Accidente se guardaron con Exito";
	
    $db->CompleteTrans();

    $link=encode_link('modificar_legajo.php',array("accion"=>$accion,"id_legajo"=>$id_legajo,"cmd"=>"modificar"));
    header("Location:$link") or die("No se encontró la página destino");
}

if ($_POST['guardar_editar']=="Guardar"){
   $db->StartTrans();
   $fech_inicio=Fecha_db($_POST['fech_inicio']);
   $art=$_POST['art'];
   $num_art=$_POST['num_art'];
   $fecha_inicio_art=Fecha_db($_POST['fecha_inicio_art']);
   $titulo=$_POST['titulo'];
   $descripcion=$_POST['descripcion'];
   
   $query="update personal.accidentes_lab set 
             descripcion='$descripcion', fech_inicio='$fech_inicio', art='$art', num_art='$num_art', fech_inicio_art='$fecha_inicio_art', titulo='$titulo'  
             where id_accidentes_lab=$id_accidentes_lab ";

   sql($query, "Error al insertar/actualizar el muleto") or fin_pagina();
    
   $db->CompleteTrans();
    
   $accion="Los datos del Accidente de Trabajo se actializaron con Exito";
   echo "<center><b><font size='2' color='red'>$accion</font></b></center>";	
}

if ($id_accidentes_lab) {
$sql="select * from personal.accidentes_lab where id_accidentes_lab=$id_accidentes_lab";
$res=sql($sql, "Error al traer los datos del caso") or fin_pagina();

$titulo=$res->fields['titulo'];
$descripcion=$res->fields['descripcion'];
$fech_inicio=$res->fields['fech_inicio'];
$art=$res->fields['art'];
$num_art=$res->fields['num_art'];
$fecha_inicio_art=$res->fields['fech_inicio_art'];
}

echo $html_header;
?>
<script>
//controlan que ingresen todos los datos necesarios par el muleto
function control_nuevos()
{
 if(document.all.fech_inicio.value=="")
 {alert('Debe ingresar una Fecha de Inicio');
  return false;
 }
 if(document.all.art.value=="")
 {alert('Debe ingresar una A.R.T.');
  return false;
 }
 if(document.all.num_art.value=="")
 {alert('Debe ingresar un Numero de A.R.T.');
  return false;
 }
 if(document.all.fecha_inicio_art.value=="")
 {alert('Debe ingresar una Fecha de Inicio en A.R.T.');
  return false;
 }
 if(document.all.titulo.value=="")
 {alert('Debe ingresar un Titulo');
  return false;
 }
 if(document.all.descripcion.value=="")
 {alert('Debe ingresar una Descripción');
  return false;
 }
 return true;
}//de function control_nuevos()

function editar_campos()
{
	//document.all.fech_inicio.readOnly=false;
	document.all.art.readOnly=false;
	document.all.num_art.readOnly=false;
	//document.all.fecha_inicio_art.readOnly=false;
	document.all.titulo.readOnly=false;
	document.all.descripcion.readOnly=false;

	document.all.cancelar_editar.disabled=false;
	document.all.guardar_editar.disabled=false;
	document.all.editar.disabled=true;
 	return true;
}//de function editar_campos()

</script>

<form name='form1' action='accidente_lab_admin.php' method='POST'>
<br>

<input type="hidden" name="id_accidentes_lab" value="<?=$id_accidentes_lab?>">
<input type="hidden" name="id_legajo" value="<?=$id_legajo?>">

<table width=95% border=0 cellspacing=0 cellpadding=6 bgcolor=<?=$bgcolor2?> align="center" class="bordes">
 <tr>
    <td style="border:<?=$bgcolor3?>" align=center id=mo>
    <?
    if (!$id_accidentes_lab) {
    ?>
     <font size=+1><b> Nuevo Accidente Laboral</b></font>
    <? }
        else {
    ?>
      <font size=+1><b>Accidentes Laborales</b></font>
    <? } ?>
    </td>
 </tr>
 
 <tr><td>
 	<table width=95% align="center">
    <tr>
     <td id=ma colspan="2">
      <font size=+1><b> Descripción del Accidente Laboral</b></font>
      </td>
     </tr>
     
     <tr>
       <td>
        <table>
         <tr>
           <td  colspan="2">
            <b> Fecha del Accidente </b>
           </td>
          </tr>
          <tr>
           <td  colspan="2">
             <input type='text' name='fech_inicio' value='<?=Fecha($fech_inicio);?>' size=15 readonly>
             &nbsp;<? cargar_calendario(); echo link_calendario("fech_inicio"); ?>
             
           </td>
          </tr>
          <tr>
           <td colspan="2">
            <b> Nombre de la A.R.T. </b>
           </td>
          </tr>
          <tr>
           <td  colspan="2">
            <input type='text' name='art' value='<?=$art;?>' size=50
                   <? if ($id_accidentes_lab) echo "readonly"?>>
           </td>
          </tr>
          
          <tr>
           <td colspan="2">
            <b> Número de Afiliado de A.R.T. </b>
           </td>
          </tr>
          <tr>
           <td  colspan="2">
            <input type='text' name='num_art' value='<?=$num_art;?>' size=50
                   <? if ($id_accidentes_lab) echo "readonly"?>>
           </td>
          </tr>
          
          <tr>
           <td colspan="2">
            <b> Fecha de Inicio la A.R.T. </b>
           </td>
          </tr>
          <tr>
           <td  colspan="2">
            <input type='text' name='fecha_inicio_art' value='<?=fecha($fecha_inicio_art);?>' size=15 readonly>
            &nbsp;<? cargar_calendario(); echo link_calendario("fecha_inicio_art"); ?>
           </td>
          </tr>
          
        </table>
      </td>
      <td>
        <table>
        
	        <tr>
	           <td colspan="2">
	            <b> Titulo </b>
	           </td>
	          </tr>
	          <tr>
	           <td  colspan="2">
	            <input type='text' name='titulo' value='<?=$titulo;?>' size=50 <? if ($id_accidentes_lab) echo "readonly"?>>
	           </td>
	        </tr>
	          
          <tr><td valign='top'><b> Descripción </b></td></tr>
          <tr><td><textarea cols='50' rows='10' name='descripcion' <? if ($id_accidentes_lab) echo "readonly"?>><?=$descripcion;?></textarea></td></tr>
        </table>
      </td>
     </tr>
   </table>
 </td></tr>
 
 
 <br>
 <? if (!($id_accidentes_lab)){?>
 <tr align="center">
 	<td align="center">
   			<table width=95% align="center">
			    <tr align="center">
			     <td align="center"><input type='submit' name='guardar' value='Guardar Nuevo Accidente' onclick="return control_nuevos()"
			         title="Guardar Datos"></td>
			    </tr>
			 </table>
  	</td>
 </tr>
 <?}?>
 
 <?if ($id_accidentes_lab){?>
 <table class="bordes" align="center" width="60%"><br>
 <tr align="center" id="ma">
    <td align="center">
      <center>
      <input type=button name="editar" value="Editar" onclick="editar_campos()" title="Edita Campos" style="width=130px"> &nbsp;&nbsp;
      <input type="submit" name="guardar_editar" value="Guardar" title="Guarda Accidente" disabled style="width=130px">&nbsp;&nbsp;
      <input type="button" name="cancelar_editar" value="Cancelar" title="Cancela Edicion" disabled style="width=130px" onclick="document.location.reload()">
      </center>
    </td>
 </tr> 
 </table>
<?}?>
 
 <tr align="center">
    <td align="center"><br>
      <center>
      <?$ref = encode_link("modificar_legajo.php",array("id_legajo"=>$id_legajo,"cmd"=>"modificar"));
    	$onclick_volver="location.href='$ref'";	
   	  ?>
      <input type=button name="volver" value="Volver" onclick="<?=$onclick_volver?>" title="Volver al Legajo" style="width=150px">
      </center>
    </td>
 </tr> 
 
</table> 
</form>
<?=fin_pagina();// aca termino ?>
