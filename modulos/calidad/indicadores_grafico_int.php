<?php
require_once ("../../config.php");

$anio = $parametros['anio'];
$id_indi=$parametros['id_indi'];
$tamaño=$parametros['tamaño'];

echo $html_header;

$sql="select desc_medicion from calidad.desc_indicador_ins where id_desc_indicador_ins=$id_indi";
$result=sql($sql,'no se puede');?>

<form name='form1' action="indicadores_grafico_int.php" method="POST">
<table>
<br>
<?if($result->fields['desc_medicion']!=''){?>
<tr>
	<td>
		<b><font color="Black" size="+1">Descripción del Método de Medición: </b><?=$result->fields['desc_medicion'];?></font>
	</td>
</tr>
<?}?>

<?$link_s=encode_link("indicadores_grafico.php",array("anio"=>$anio,"id_indi"=>$id_indi,"tamaño"=>"large"));?>

<tr>
	<td>
	<br>
	<br>
		<img src='<?=$link_s?>' border=0 align=top>
	</td>
</tr>
</table>

<?echo $html_footer?>
