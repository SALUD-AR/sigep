<?
/*AUTOR: diegoinga

$Author: mari $
$Revision: 1.1 $
$Date: 2006/12/27 20:30:03 $
*/


require_once("../../config.php");
$descripcion="";
switch($_POST['boton']) {
case "Guardar":  
	
	$fecha_emision=date("Y-m-d"); 
	$fecha_evento=fecha_db($_POST['fecha_evento']); 
	$id_proveedor = $_POST['id_proveedor'];
	$id_prod_esp = $_POST['id_prod_esp'];
	$usuario = $_POST['usuario'];
	$descripcion_inconformidad = $_POST['descripcion_inconformidad'];
	$disposicion = $_POST['disposicion'];
	$area = $_POST['area'];
	$tipo_producto = $_POST['tipo_producto'];
	$texto_deteccion = $_POST['texto_deteccion'];
	$nro_serie = $_POST['nro_serie'];
	$id = $_POST['id'];
	$bar_code=$_POST['bar_code'];
	
	if ($fecha_evento == "") $fecha_evento = "NULL";
		else  $fecha_evento = "'".$fecha_evento."'";
	if ($id_proveedor == "") $id_proveedor = "NULL";
	if ($id_prod_esp == ""){
		$id_prod_esp = "NULL";
		$bar_code = "";		
	} 
	if ($disposicion == "-1") $disposicion = "NULL";
	
	if ($_POST['ie']=="Insertar") {
		$sql="insert into noconformes(id_proveedor,fecha_evento,usuario,fecha_emision,descripcion_inconformidad,id_disposicion,id_prod_esp,area,id_tipo_producto,deteccion,nro_serie,cod_barra)
				values($id_proveedor,$fecha_evento,'$usuario','$fecha_emision','$descripcion_inconformidad',$disposicion,$id_prod_esp,'$area',$tipo_producto,'$texto_deteccion','$nro_serie','$bar_code');";
		sql($sql,"No se puede Ejecutar la Consulta") or fin_pagina();
		$msg="<b><center>El Producto No Conforme se insertó con éxito</center></b>";
		$link=encode_link("no_conformes.php",array("msg"=>$msg));  
		header("location: $link");
	}
	else {  //actualizo
		//print_r($_POST);
		
		$sql="update noconformes set id_proveedor=$id_proveedor, fecha_evento=$fecha_evento, 
					usuario='$usuario', fecha_emision='$fecha_emision', descripcion_inconformidad='$descripcion_inconformidad', 
					id_disposicion=$disposicion, id_prod_esp=$id_prod_esp,area='$area',
					id_tipo_producto=$tipo_producto,deteccion='$texto_deteccion', cod_barra='$bar_code',nro_serie='$nro_serie' 
					where id_noconforme=$id";
		sql($sql,"No se puede Ejecutar la Consulta") or fin_pagina();
		$msg="<b><center>El Producto No Conforme se actualizó con éxito</center></b>";
		$link=encode_link("no_conformes.php",array("msg"=>$msg));  
		header("location: $link");
	}
	break;
default:{
         if($parametros['id']){
          $id=$parametros['id'];
          $sql="select noconformes.deteccion,noconformes.nro_serie,noconformes.id_tipo_producto,proveedor.razon_social, 
          proveedor.id_proveedor,noconformes.id_prod_esp,noconformes.fecha_evento,noconformes.usuario,
          noconformes.descripcion_inconformidad,noconformes.id_disposicion,noconformes.area, noconformes.cod_barra 
          from calidad.noconformes 
          left join general.producto_especifico
          using (id_prod_esp) 
          left join general.proveedor 
          using (id_proveedor) 
          where noconformes.id_noconforme=$id";
          $resultado=sql($sql,"No se puede Ejecutar la Consulta") or fin_pagina();
          $fecha_evento=fecha($resultado->fields['fecha_evento']);
          $usuario=$resultado->fields['usuario'];
          $descripcion_inconformidad=$resultado->fields['descripcion_inconformidad'];
          $disposicion=$resultado->fields['id_disposicion'];
          $area=$resultado->fields['area'];
          $nro_serie=$resultado->fields['nro_serie'];
          $tipo_producto=$resultado->fields['id_tipo_producto'];
          $texto_deteccion=$resultado->fields['deteccion'];
          $bar_code = $resultado->fields['cod_barra'];
         }
         else //inserto nuevo
          extract($_POST);
echo $html_header;
?>         
<SCRIPT language='JavaScript' src="../../lib/funciones.js">
cargar_calendario();
</script>

<br>
<?
$link=encode_link("detalle_no_conformes.php", array("pagina"=>$parametros['pagina'],"id" =>$parametros['id']));
?>

<form name="form1" action="<?=$link?>" method="POST">
<input type="hidden" name="id" value="<?=$id?>">
<table width="90%"  border="1" align="center">
<?
$sql="select id_proveedor,razon_social from proveedor order by razon_social";
$resultado_proveedor=$db->Execute($sql) or die($db->ErrorMsg()."<br>".$sql);
if($id=="")
{//nuevo numero
 $sql="select max(id_noconforme) from noconformes";
 $resultado_nuevo=$db->Execute($sql) or die($db->ErrorMsg()."<br>".$sql);

 if ($resultado_nuevo->fields['max']=="")
 $id=0;
 else 
  $id=$resultado_nuevo->fields['max'] + 1;
} 
?>
  <tr>
    <td id=mo colspan="4">Detalle de Producto No Conforme</td>
  </tr>
  <tr>  
    <td colspan="4"><b>ID:<? echo $id; ?></b></td> 
  </tr>
<tr>
<td>
<?
 $sql="select * from tipo_producto";
 $resultado_tipo_prod=$db->Execute($sql) or die($db->ErrorMsg()."<br>".$sql);
?>
<strong>Producto</strong>
</td>
<td>
<select name="tipo_producto">
<?
while(!$resultado_tipo_prod->EOF)
{
?>
<option value="<?=$resultado_tipo_prod->fields['id_tipo_producto'];?>" <?=($tipo_producto==$resultado_tipo_prod->fields['id_tipo_producto'])?"selected":"";?>><?=$resultado_tipo_prod->fields['descripcion'];?></option>
<?
$resultado_tipo_prod->MoveNext();
}
?>
</select>
</td>
<td>
<strong>Nro de Serie</strong>
</td>
<td>
<input type="text" name="nro_serie" value="<?=$nro_serie;?>">
</td>
</tr>
<tr>
    <td><strong>Fecha Evento</strong></td>
    <td><input type="text" name="fecha_evento" value="<?=$fecha_evento?>">&nbsp;<? cargar_calendario(); echo link_calendario("fecha_evento"); ?></td>
    <td><strong>Usuario</strong></td>
    <td><input type="text" name="usuario" value="<? if($usuario=="") echo $_ses_user['name'];else echo $usuario; ?>"></td>
    </tr>
    <tr>
     <td><strong>Área</strong></td>
     <td colspan="3"><input type="text" name="area" value="<?=$area?>" style="size:50"></td>
</tr>
   <tr>
     <td colspan="4" align="center">
      <br>
       <strong>Como se detecto</strong><br>
  		<textarea name="texto_deteccion" cols="80" rows="4"><?=$texto_deteccion?></textarea>
  	 </td>
  	</tr>
      	<tr>
  	 <td colspan="4">
  		<strong>Disposición</strong>&nbsp;
  	 	<?
  		 $query="select * from disposicion";
  		 $disp=$db->Execute($query) or die($db->ErrorMsg()."<br>Error al traer las disposiciones");
  		?>
  		<select name="disposicion">
  		 <option value=-1>Seleccione una disposicion</option>
  		<?
        while(!$disp->EOF)
        {?>
         <option value=<?=$disp->fields['id_disposicion']?> <?if($disp->fields['id_disposicion']==$_POST['disposicion'] || $disp->fields['id_disposicion']==$disposicion) echo "selected"?>><?=$disp->fields['descripcion']?></option>
        <?
         $disp->MoveNext();
        }?> 
  		</select>
  	 </td>
  	</tr>	 	
    <tr>
     <td colspan="4" align="center">
      <br>
       <strong>Causa</strong><br>
  		<textarea name="descripcion_inconformidad" cols="80" rows="4"><?=$descripcion_inconformidad?></textarea>
  	 </td>
  	</tr>
  	<!--<tr>
  	<td colspan="4">
  	<?/*if ($bar_code=="") $bar_code=$_POST['bar_code'];
  	  if ($_POST['boton_cod_barra']=='Ver') $bar_code=$_POST['bar_code'];*/
  	 ?>
  	<strong>Bar Code&nbsp;</strong><input type="text" name="bar_code" value="<?//=$bar_code?>">&nbsp;&nbsp;&nbsp;<input type="submit" name="boton_cod_barra" value="Ver">
  	</td>
  	</tr>
  	<tr>
  	<td colspan="4">
  	<?
  	/*$sql = "select codigos_barra.id_prod_esp,producto_especifico.descripcion,proveedor.razon_social, 
  			proveedor.id_proveedor 
  			from general.log_codigos_barra 
  			join general.codigos_barra 
  			using(codigo_barra) 
  			join general.producto_especifico 
  			using(id_prod_esp) 
  			join compras.orden_de_compra 
  			using(nro_orden) 
  			join general.proveedor 
  			using(id_proveedor) 
  			where log_codigos_barra.codigo_barra = '$bar_code'  and log_codigos_barra.tipo ilike 'Producto Ingresado%'";
  	$resultado_prod=sql($sql,"No se puede Ejecutar la Consulta") or fin_pagina();*/
  	?>
  	<strong>Producto </strong><input type="text" name="producto" value="<?//=$resultado_prod->fields['descripcion'];?>" readonly size="70">
  	<input type="hidden" name="id_prod_esp" value="<?//=$resultado_prod->fields['id_prod_esp'];?>">
  	</td>
  	</tr>
  	<tr>
    <td colspan="4"><B>Proveedor</b>&nbsp;<input type="text" name="proveedor" value="<?//=$resultado_prod->fields['razon_social'];?>" readonly size="68">
    <input type="hidden" name="id_proveedor" value="<?//=$resultado_prod->fields['id_proveedor'];?>">   
    </td>
   
  </tr>-->
</table> 
<br>
<center>
<input type="submit" name="boton" value='Guardar'>
<input type="hidden" name="ie" value='<?if($parametros['pagina']!="listado")echo "Insertar"; else echo "Editar"?>'>
<input type="button" name="boton" value='Volver' onclick="document.location='no_conformes.php'">
<input type="button" name="imprimir" value='Imprimir Etiqueta' onclick="window.open('<?php echo encode_link("imprimir_etiqueta.php",array("id"=>$id)) ?>','','toolbar=0,location=0,directories=0,status=0, menubar=0,scrollbars=0,left=0,top=0,width=350,height=5');">
</center>
</form>
</body>
</html>
<?
  break;
 }//del default
}//fin switch
fin_pagina();
?>
