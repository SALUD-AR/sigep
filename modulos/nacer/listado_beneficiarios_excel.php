<?php

require_once ("../../config.php");

$cmd=$parametros["cmd"];

$sql_tmp="select id_smiafiliados, afiapellido,afinombre,afidni,nombre,cuie,activo,motivobaja,mensajebaja,clavebeneficiario,fechainscripcion
			from nacer.smiafiliados
			left join nacer.efe_conv on (cuieefectorasignado=cuie)";

if ($cmd=="activos")
    $where_tmp=" (smiafiliados.activo='S')";
    
if ($cmd=="activos_con_ceb")
    $where_tmp=" (smiafiliados.activo='S' and ceb='S')";
    
if ($cmd=="activos_sin_ceb")
    $where_tmp=" (smiafiliados.activo='S' and ceb='N')";    

if ($cmd=="inactivos")
    $where_tmp=" (smiafiliados.activo='N')";

$result=sql($sql_tmp." where ".$where_tmp) or fin_pagina();

//echo $html_header;
excel_header("beneficiarios.xls");

?>
<form name=form1 method=post action="listado_beneficiarios_excel.php">
<table width="100%">
  <tr>
   <td>
    <table width="100%">
     <tr>
      <td align=left>
       <b>Total beneficiarios: </b><?=$result->RecordCount();?> 
       </td>       
      </tr>
      <tr>
      <td align=left>
       <b>Estado: <font size="+1" color="Red"><?=$cmd;?> </font></b>
       </td>       
      </tr>
    </table>  
   </td>
  </tr>  
 </table> 
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor=#C0C0FF>
    <td align=right id=mo>Apellido</td>      	
    <td align=right id=mo>Nombre</td>
    <td align=right id=mo>DNI</td>
    <td align=right id=mo>Nombre Efector</td>
    <td align=right id=mo>CUIE</td>   
    <td align=right id=mo>Activo</td>
    <td align=right id=mo>Cod Baja</td>
    <td align=right id=mo>Mensaje Baja</td>     
    <td align=right id=mo>Clave Beneficiario</td>
    <td align=right id=mo>F Ins</td>
  </tr>
  <?   
  while (!$result->EOF) {?>  
    <tr>     
     <td><?=$result->fields['afiapellido']?></td>
     <td><?=$result->fields['afinombre']?></td>
     <td><?=$result->fields['afidni']?></td>     
     <td><?=$result->fields['nombre']?></td> 
     <td><?=$result->fields['cuie']?></td> 
     <td><?=$result->fields['activo']?></td> 
     <td><?=$result->fields['motivobaja']?></td> 
     <td><?=$result->fields['mensajebaja']?></td> 
     <td><?=$result->fields['clavebeneficiario']?></td>  
     <td><?=fecha($result->fields['fechainscripcion'])?></td> 
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>
