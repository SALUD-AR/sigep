<?php
require_once("../../config.php");

variables_form_busqueda("ins_listado_old");
cargar_calendario();

if ($_POST['generarnino']=='Generar'){
        $fechaemp=Fecha_db($_POST['fechaemp']);
		$fechakrga=Fecha_db($_POST['fechakrga']);
		$link=encode_link("../inscripcion/ins_listado_remediar_xls.php",array("fechaemp"=>$fechaemp, "fechakrga"=>$fechakrga));?>
	<script>
	window.open('<?=$link?>')
	</script>	
<?}

$orden = array(
        "default" => "1",
        "1" => "num_doc",		     
       );
$filtro = array(		
		"num_doc" => "Numero de Documento",		
		"apellido" => "Apellido",
		);
       
if ($cmd == "")  $cmd="d";

$datos_barra = array(
     array(
        "descripcion"=> "Diabeticos",
        "cmd"        => "d"
     ),    
     array(
        "descripcion"=> "Hipertensos",
        "cmd"        => "h"
     )
);

generar_barra_nav($datos_barra);

$sql_tmp="SELECT * FROM trazadoras.clasificacion_remediar2";

if ($cmd=="d")
    $where_tmp=" (diabetico='SI') or (dmt <> '0')"; 

if ($cmd=="h")
    $where_tmp=" (hipertenso='SI')"; 
    
echo $html_header;
cargar_calendario();?>
<script>
function control_muestra()
{ 
 if(document.all.fechaemp.value==""){
  alert('Debe Ingresar una Fecha DESDE');
  return false;
 } 
 if(document.all.fechakrga.value==""){
  alert('Debe Ingresar una Fecha HASTA');
  return false;  
 } 

 alert('Se genera un Excel Completo, buscar las columnas Diabetico e Hipertenso para realizar los filtros que necesite.');


return true;
}
</script>
<form name=form1 action="listado_diab_hip.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
		&nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	   </td>	
	</tr>
	<tr>
	<td align=center>       
	   &nbsp;&nbsp;<b>Fecha desde: </b>
		<input type=text id="fechaemp" name="fechaemp" size=11 maxlength="11" onchange="esFechaValida(this);"> <?=link_calendario('fechaemp');?>
		&nbsp;&nbsp;<b>Fecha hasta: </b>
		<input type=text id="fechakrga" name="fechakrga"  size=11 maxlength="11" onchange="esFechaValida(this);"> <?=link_calendario('fechakrga');?>		
		<? if(1==1){
				$permiso_genera_archivo_remediar="";
			}
			else {
				$permiso_genera_archivo_remediar="disabled";
			}?>
	    &nbsp;&nbsp;<input type=submit name="generarnino" value='Generar' <?=$permiso_genera_archivo_remediar?> onclick="return control_muestra();">
	  </td>
     </tr>  
</table>

<?$result = sql($sql) or die;?>

<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=10 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$total_muletos?></td>       
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  

  <tr>
  	<td align=right id=mo>Clave Beneficiario</td>    	    
    <td align=right id=mo><a id=mo href='<?=encode_link("ins_listado_remediar.php",array("sort"=>"1","up"=>$up))?>'>Documento</a></td>      	    
    <td align=right id=mo>Apellido</a></td>      	    
    <td align=right id=mo>Nombre</a></td>
    <td align=right id=mo>F NAC</td>
    <td align=right id=mo>F Carga Clasificacion</td>
    <td align="right" id="mo">Clasif.</td>     	    
    <td align="right" id="mo">Seg.</td>     	    

  </tr>
 <?
   while (!$result->EOF) {
   
	$clave_beneficiario=$result->fields['clave_beneficiario'];
	$fecha_nac=$result->fields['fecha_nac'];?>
  
    <tr <?=atrib_tr()?>>     
     <td ><?=$result->fields['clave_beneficiario']?></td>
     <td ><?=$result->fields['num_doc']?></td>        
     <td ><?=$result->fields['apellido']?></td>     
     <td ><?=$result->fields['nombre']?></td>     
     <td ><?=fecha($result->fields['fecha_nac'])?></td>
     <td ><?=fecha($result->fields['fecha_carga'])?></td>
     <td align="center">
       	 <?$ref = encode_link("../trazadoras/remediar_carga.php",array("clave_beneficiario"=>$result->fields['clave_beneficiario'],"pagina"=>'listado_diab_hip.php'));
       	 
       	   echo "<a href='#' title='Seguimiento' onclick=window.open('".$ref."','Clasificacion','menubar=1,resizable=1,scrollbars=1,width=1000,height=750')><IMG src='$html_root/imagenes/flech.png' height='20' width='20' border='0'></a>";?>
     </td>   
     <td align="center">
       	 <?$ref = encode_link("../trazadoras/seguimiento_admin.php",array("clave_beneficiario"=>$result->fields['clave_beneficiario'],"pagina"=>'listado_diab_hip.php'));
       	 
       	   echo "<a href='#' title='Seguimiento' onclick=window.open('".$ref."','Seguimiento','menubar=1,resizable=1,scrollbars=1,width=900,height=700')><IMG src='$html_root/imagenes/flech.png' height='20' width='20' border='0'></a>";?>
 
     </td> 
   </tr>
	<?$result->MoveNext();
    }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>
