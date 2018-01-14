<?php

require_once("../../config.php");

variables_form_busqueda("ins_dpto_per_per");

if ($cmd == "")  $cmd="activos";

$fecha_hoy=date("Y-m-d H:i:s");
$fecha_hoy=fecha($fecha_hoy);

$orden = array(
        "default" => "1",
        "1" => "nombre"
        
             
       );
$filtro = array(
		"SMIProcesoAfiliados.periodo" => "Periodo"
                
       );
       
$datos_barra = array(
     array(
        "descripcion"=> "Activos",
        "cmd"        => "activos"
     ),
     array(
        "descripcion"=> "Inactivos",
        "cmd"        => "inactivos"
     ),
     array(
        "descripcion"=> "Todos",
        "cmd"        => "todos"
     )
);

generar_barra_nav($datos_barra);


$sql_tmp="SELECT
  nacer.dpto.nombre,
  count(smiafiliados.id_smiafiliados) AS cb
FROM
  nacer.smiafiliados 
  left join nacer.SMIAfiliadosAux on (SMIAfiliados.clavebeneficiario = SMIAfiliadosAux.clavebeneficiario)
  left join nacer.SMIProcesoAfiliados on (SMIProcesoAfiliados.Id_ProcAfiliado = SMIAfiliadosAux.Id_ProcesoIngresoAfiliados)
  left join nacer.efe_conv on (cuieefectorasignado=cuie)
  left join nacer.dpto on (departamento=codigo)
 ";

if ($cmd=="activos")
    $where_tmp=" (smiafiliados.activo='S')
    			 GROUP BY
  nacer.dpto.nombre";
    

if ($cmd=="inactivos")
    $where_tmp=" (smiafiliados.activo='N')
     GROUP BY
  nacer.dpto.nombre";
    
if ($cmd=="todos")
    $where_tmp=" GROUP BY
  nacer.dpto.nombre";

echo $html_header;
?>
<form name=form1 action="ins_dpto_per.php" method=POST>
<table cellspacing=2 cellpadding=2 border=0 width=100% align=center>
     <tr>
      <td align=center>
        <font color="Red">Ejemplo: 200812-200901</font>
		<?list($sql,$total_muletos,$link_pagina,$up) = form_busqueda($sql_tmp,$orden,$filtro,$link_tmp,$where_tmp,"buscar");?>
	    &nbsp;&nbsp;<input type=submit name="buscar" value='Buscar'>
	  </td>
     </tr>
</table>

<?$result = sql($sql) or die;?>

<table border=0 width=100% cellspacing=2 cellpadding=2 bgcolor='<?=$bgcolor3?>' align=center>
  <tr>
  	<td colspan=11 align=left id=ma>
     <table width=100%>
      <tr id=ma>
       <td width=30% align=left><b>Total:</b> <?=$total_muletos?></td>       
       <td width=40% align=right><?=$link_pagina?></td>
      </tr>
    </table>
   </td>
  </tr>
  

  <tr>
    <td align=right id=mo><a id=mo href='<?=encode_link("ins_dpto_per.php",array("sort"=>"1","up"=>$up))?>'>NOMBRE</a></td>    
    <td align=right id=mo>Inscriptos</td>        
  </tr>
 <?
   while (!$result->EOF) {?>
    
    <tr <?=atrib_tr()?>>             
     <td ><?=$result->fields['nombre']?></td>
     <td ><?=$result->fields['cb']?></td>     
    </tr>
	<?$result->MoveNext();
    }?>
    
</table>
</form>
</body>
</html>
<?echo fin_pagina();// aca termino ?>