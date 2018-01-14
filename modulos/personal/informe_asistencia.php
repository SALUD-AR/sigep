<? 
/*
Autor: Mariela
$Author: mari $
$Revision: 1.1 $
$Date: 2006/06/06 17:10:11 $
*/
                                                
require("../../config.php");
require_once("gutils.php");

echo $html_header;


$mes=$parametros['mes'];
$agno=$parametros['agno'];
$nombre=$parametros['nombre'];
$falta_just=$parametros['falta_just'];
$falta_injust=$parametros['falta_injust'];
$tardanza_just=$parametros['tardanza_just'];
$tardanza_injust=$parametros['tardanza_injust'];
?>


<form name=form1 action="informe_asistencia.php" method="post">
<table width="95%" align="center" cellpadding="2" cellspacing="2">
  <tr id=mo>
     <td>  Asistencia del Mes de <?=calcula_nombre_mes($mes)?> de <?=$agno?> </td>
  </tr>
  <tr bgcolor="White">   
     <td>  <b>Usuario:</b> <?=$nombre?></td>  
   </tr>
</table>
<table width="95%" align="center" cellpadding="2" cellspacing="2">  
  <tr <?=$atrib_tr?>>
    <td> <b>Faltas Justificadas</b> </td>
    <td> <?=$falta_just?>  </td>
  </tr>
  <tr  <?=$atrib_tr?>>
   <td> <b>Faltas Injustificadas</b> </td>
   <td> <?=$falta_injust?>  </td>
  </tr>
  <tr  <?=$atrib_tr?>>
    <td>  <b>Llegadas Tardes Justificadas</b> </td> 
    <td> <?=$tardanza_just?> </td> 
  </tr>
  <tr  <?=$atrib_tr?>>
    <td><b>Llegadas Tardes Injustificadas</b></td>
    <td> <?=$tardanza_injust?> </td>
  </tr>
</table>
<br>
<table width="95%" align="center" cellpadding="2" cellspacing="2"> 
  <tr>
    <td align="center">
       <input type='button' name='cerrar' value='Cerrar' onclick='window.close();'>
    </td>
  </tr>
</table>     

  
 
</form>