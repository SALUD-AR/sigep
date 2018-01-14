<?

require_once("../../config.php");

$msg=$parametros['msg'];

if($_POST['boton_borrar']=="Borrar")
{
	            $db->StartTrans();
 	            $i=1;$bien=1;
 	            while ($i<=$_POST['cant'])
                {
                  if ($_POST['borrar_'.$i]!="")
                  {
                   $sql="delete from log_pac_pap where id_pac_pap=".$_POST['borrar_'.$i];
                   if($db->Execute($sql))
                   {$sql="delete from pac_pap where id_pac_pap=".$_POST['borrar_'.$i];
                    if(!$db->Execute($sql))
                   	 $bien=0; 
                   }
                   else 
                    $bien=0; 
                  }
                  $i++;
                } 
                if($bien)
                 $msg="<b><center>Los items seleccionados se borraron con éxito</b></center>"; 
                else 
                 $msg="<b><center>Los items seleccionados no se pudieron borrar</b></center>";  
                $db->CompleteTrans(); 
}

echo $html_header;
variables_form_busqueda("pac_pap");
	
if ($cmd == "") {
	$cmd="pac";
    phpss_svars_set("_ses_pac_pap_cmd", $cmd);
}



$datos_barra = array(
					array(
						"descripcion"	=> "Pedido de Acción Correctiva",
						"cmd"			=> "pac",
						),
					array(
						"descripcion"	=> "Pedido de Acción Preventiva",
						"cmd"			=> "pap"
						),
				    array(
						"descripcion"	=> "Todas",
						"cmd"			=> "todas"
						),
					array(
						"descripcion"	=> "Estadísticas",
						"cmd"			=> "estadisticas"
						)	
				 );
generar_barra_nav($datos_barra);
if($cmd!="estadisticas")//funciona como antes.....
{
?>
<script>
var contador=0;
//esta funcion sirve para habilitar el boton de cerrar 
function habilitar_borrar(valor)
{
 if (valor.checked)
             contador++;
             else
             contador--;
 if (contador>=1)
         window.document.all.boton_borrar.disabled=0;
        else
         window.document.all.boton_borrar.disabled=1;
}//fin function
</script>
<form name="form1" method="POST" action="listado_pac_pap.php">
<?
if($cmd!="todas")
$orden = array(
		"default" => "1",
 //		"default_up" => "1",
		"1" => "pac_pap.id_pac_pap",
		"2" => "no_conformidad.descripcion"
	);
else
$orden = array(
		"default" => "1",
 //		"default_up" => "1",
		"1" => "pac_pap.id_pac_pap",
		"2" => "no_conformidad.descripcion",
		"3" => "pac_pap.tipo"
	);

$filtro = array(
		"pac_pap.id_pac_pap" => "ID",
		"no_conformidad.descripcion" => "No Conformidad",
		"pac_pap.descripcion" => "PAC/PAP Descripción",
		"pac_pap.area" => "Area",
		"pac_pap.accion_inmediata" => "Acción Inmediata",
		"pac_pap.causa_nc" => "Causa No Conformidad",
		"pac_pap.accion_correctiva" => "Acción Correctiva",
		"pac_pap.evaluacion_eficacia" => "Evaluación de Eficacia",
		
	);

$query="select id_pac_pap,pac_pap.tipo,pac_pap.descripcion,pac_pap.area,pac_pap.accion_inmediata,pac_pap.causa_nc,pac_pap.verificacion,no_conformidad.descripcion,pac_pap.accion_correctiva as ac,pac_pap.evaluacion_eficacia as ee 
from calidad.pac_pap join calidad.no_conformidad using (id_no_conformidad)";
$where="";
if($cmd=="pac")
{$where=" pac_pap.tipo=0";
 $contar="select count(*) from pac_pap where pac_pap.tipo=0";
}
elseif($cmd=="pap") 
{$where=" pac_pap.tipo=1";
 $contar="select count(*) from pac_pap where pac_pap.tipo=1";
} 
echo "<center>";

if($_POST['keyword'] || $keyword)// en la variable de sesion para keyword hay datos)
     $contar="buscar";
list($sql,$total_pac_pap,$link_pagina,$up) = form_busqueda($query,$orden,$filtro,$link_tmp,$where,$contar); 

$result = $db->Execute($sql) or die($db->ErrorMsg()."<br>Error en form busqueda");
?>

&nbsp;&nbsp;<input type=submit name=form_busqueda value='Buscar'>

</center>
<?=$msg;?>
<table border=0 width="95%" align="center" cellpadding="3" cellspacing='0' bgcolor=<?=$bgcolor3?>>
 <tr id=ma>
    <td align="left">
     <b>Total:</b> <?=$total_pac_pap?>.
    </td>
	<td align=right>
	 <?=$link_pagina?>
	</td>
  </tr>
</table>
<div style='position:relative; width:100%; height:78%; overflow:auto;'>
<table width='95%' border='0' cellspacing='2' align="center">
<tr id=mo>
 <td width="1%"></td>
 <td width='10%'><b><a id=mo href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"1","up"=>$up))?>'>ID</a></b></td>
 <td width='80%'><b><a id=mo href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"2","up"=>$up))?>'>No Conformidad</a></b></td>
 <?
 if($cmd=="todas") 
 {?>
 <td width="10%"><b><a id=mo href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"3","up"=>$up))?>'>Tipo</a></b></td>
 <?
 }?>
</tr>
<?
$i=1;
$cnr=1;
while(!$result->EOF)
{
  if ($cnr==1)
  {$color2=$bgcolor2;
   $color=$bgcolor1;
   $atrib ="bgcolor='$bgcolor1'";
   $cnr=0;
  }
  else
  {$color2=$bgcolor1;
   $color=$bgcolor2;
   $atrib ="bgcolor='$bgcolor2'";
   $cnr=1;
  }
  $atrib.=" onmouseover=\"this.style.backgroundColor = '#ffffff'\" onmouseout=\"this.style.backgroundColor = '$color'\"";	

$link = encode_link("pac_pap.php",array("pagina"=>"listado","id"=>$result->fields["id_pac_pap"]));
?> 
<tr <?=$atrib?> style="cursor:hand">
  <td width="1%"><input type="checkbox" name="borrar_<? echo $i; ?>" value="<? echo $result->fields['id_pac_pap']; ?>" onclick="habilitar_borrar(this)"></td>
  <a href='<?=$link?>'>
 <?
 //color de acuerdo a qué este guardado y que no
 if($result->fields['ac']=="")
  $fondo_id="red";
 elseif($result->fields['ee']=="") 	
  $fondo_id="yellow";
 else
  $fondo_id="green";
 ?> 
 <td align="center" bgcolor="<?=$fondo_id?>">
  <?=$result->fields['id_pac_pap']?> 
 </td>
 <td>
  <?=$result->fields['descripcion']?> 
 </td>
 <?
 if($cmd=="todas")
 {?>
 <td align="center">
  <?if($result->fields['tipo']==0)
     echo "P.A.C.";
    else
     echo "P.A.P.";
  ?> 
 </td>
 <?
 }
 ?>
</a></tr>
<?
 $i++;
 $result->MoveNext();
}//del while
?>
</table>
</div>
<input type="hidden" name="cant" value="<? echo $result->RecordCount(); ?>">
<center>
  <input type="button" name="boton_nuevo" value="Agregar Nuevo" onclick="document.location='pac_pap.php'">
  <input type="submit" name="boton_borrar" value="Borrar" disabled>
</center>
<table width='95%' bgcolor="white" align="center">
 <tr>    
    <td width='50%' align='right'>
    <b><font size="-3"> Faltan: Acción Correctiva y Evalucación de Eficacia
    </td>
    <td width='3%' bgcolor='red'>&nbsp;
    
    </td>
    <td  width='30%' align='right'>
    <b><font size="-3"> Falta: Evaluación de Eficacia
    </td>
    <td width='3%' bgcolor='yellow'>&nbsp;
    
    </td>
    <td  width='10%' align='right'>
    <b><font size="-3"> Completa
    </td>
    <td width='3%' bgcolor='green'>&nbsp;
    
    </td>
 </tr>
</table>
<?
}
else//estadisticas
{//cantidad de no conformidades que aparecen en los datos cargados
$query="select count(pac_pap.id_no_conformidad) as cant,no_conformidad.descripcion,no_conformidad.tipo from pac_pap right join no_conformidad using (id_no_conformidad) group by no_conformidad.descripcion,no_conformidad.tipo order by no_conformidad.tipo,no_conformidad.descripcion";
$result=$db->Execute($query) or die($db->ErrorMsg()."<br>Error al traer los datos de no conformidad");

?>
<br>
<table width='95%' border='0' cellspacing='2' align="center">	
<tr id=mo>
 <td>
  Descripcion
 </td>
 <td>
  Tipo
 </td>
 <td>
  Cantidad
 </td>
</tr>
<?
while(!$result->EOF)
{$tr_color=(((++$i)%2)==0)?$bgcolor1:$bgcolor2;
?>
 <tr bgcolor=<?=$tr_color?>>
  <td>
   <font size=2><?=$result->fields['descripcion']?></font>
  </td>
  <td align="center">
   <font size=2><?if($result->fields['tipo']==0) echo "P.A.C.";else echo "P.A.P.";?></font>
  </td>
  <td align="center">
  <font size=2><?=$result->fields['cant']?></font>
  </td>
 </tr> 
<?
 $result->MoveNext();
}
?>
</table>
<?
}	
?>
</form>
</body>
</html>