<?php
require_once("../../config.php");

variables_form_busqueda("paciente_listado");

$fecha_hoy=date("Y-m-d H:i:s");
$fecha_hoy=fecha($fecha_hoy);

$orden = array(
        "default" => "1",
        "1" => "apellido_benef",
        "2" => "numero_doc"      
       );
$filtro = array(
		    "numero_doc" => "DNI",
        "apellido_benef" => "Apellido"                       
       );


$sql_tmp="
select 
  uad.beneficiarios.id_beneficiarios,
  uad.beneficiarios.apellido_benef as a,
  uad.beneficiarios.nombre_benef as b,
  uad.beneficiarios.numero_doc as c,
  uad.beneficiarios.fecha_nacimiento_benef as d,
  uad.beneficiarios.localidad_nac as e
  from uad.beneficiarios";


echo $html_header;
?>
<form name=form1 action="paciente_listado.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
    	</td>
     </tr>
</table>

<?
if ($_POST['buscar'])$result = sql($sql) or die;?>

<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=12 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$total_muletos?></td>       
      </tr>
    </table>
    
   </td>
  </tr>
  

  <tr>
    <td align=right id=mo><a id=mo href='<?=encode_link("paciente_listado.php",array("sort"=>"2","up"=>$up))?>'>DNI</a></td>
    <td align=right id=mo><a id=mo href='<?=encode_link("paciente_listado.php",array("sort"=>"1","up"=>$up))?>'>Apellido</a></td>      	
    <td align=right id=mo>Nombre</td>
    <td align=right id=mo>Fecha Nacimiento</td>
    <td align=right id=mo>Domicilio</td>        
    <td align=right id=mo>Registro</td>       
  </tr>
 <?
 if ($_POST['buscar']){
   while (!$result->EOF){
   	$ref = encode_link("paciente_admin.php",array("id_beneficiarios"=>$result->fields['id_beneficiarios'],"pagina_listado"=>"paciente_listado.php"));
    $onclick_elegir="location.href='$ref'";?>
     
     <tr <?=atrib_tr()?>>   
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['c']?></td>     
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['a']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['b']?></td>
     <td onclick="<?=$onclick_elegir?>"><?=Fecha($result->fields['d'])?></td> 
     <td onclick="<?=$onclick_elegir?>"><?=$result->fields['e']?></td>     
     <?$ref_paciente = encode_link("paciente_admin.php",array("id_beneficiarios"=>$result->fields['id_beneficiarios']));?>
     <td align="center">  <a href="<?=$ref_paciente?>" title="Registro Dato"><IMG src='<?=$html_root?>/imagenes/iso.jpg' height='20' width='20' border='0'></a></td>  
     </tr>
	<?$result->MoveNext();
    }}?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>
