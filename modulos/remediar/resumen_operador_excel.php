<?php

require_once ("../../config.php");

$sql=$parametros["sql"];
$result=sql($sql) or fin_pagina();

excel_header("resumen_operador.xls");

?>
<form name=form1 method=post action="resumen_operador_excel.php">
 <br>
 <table width="100%" align=center border=1 bordercolor=#585858 cellspacing="0" cellpadding="5"> 
  <tr bgcolor=#C0C0FF>
  <td align=right id=mo>Fecha de Carga</td>
    <td align=right id=mo>Usuario</td>
    <td align=right id=mo>Factores C/Identificacion del mismo usuario</td>
    <td align=right id=mo>Factores S/Identificacion del mismo usuario</td>
    <td align=right id=mo>Identificacion S/Factores</td>
    <td align=right id=mo>Fichas a no enviado por el usuario</td>
  </tr>
  <?   
  while (!$result->EOF) {
       $usuario_carga=$result->fields['usuario_carga'];
       $sq2="select upper(nombre||' '||apellido) as nomus
            from sistema.usuarios
            where upper(nombre||' '||apellido)  like '$usuario_carga%'";
       $resulta = sql($sq2) or die;
       if($resulta->recordcount()>0){
       $usuario_carga=$resulta->fields['nomus'];
       }?>
    <tr>     
	 <td><?=$result->fields['fecha_carga']?></td>
     <td><?=$usuario_carga?></td>
     <td><?=$result->fields['fr_c_id_mus']?></td>
     <td><?=$result->fields['fr_s_id_mus']?></td>
     <td><?=$result->fields['id_s_fr']?></td>
     <td><?=$result->fields['t_no_env']?></td>
    </tr>
	<?$result->MoveNext();
    }?>
 </table>
 </form>