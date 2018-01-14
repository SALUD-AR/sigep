<? 
/*
Autor: Mariela
$Author: ferni $
$Revision: 1.7 $
$Date: 2007/03/20 15:19:32 $
*/

require("../../config.php");
require_once("gutils.php");
echo $html_header;

function ultimoDia($mes,$ano){ 
    $ultimo_dia=28; 
    //while (checkdate($mes,$ultimo_dia + 1,$ano)){ $ultimo_dia++;} 
    $ultimo_dia=date("t", mktime(0, 0, 0, intval($mes_resumen), 1, intval($agno_resumen)));
    return $ultimo_dia; 
} 

$mes_resumen=$parametros['mes_resumen'] or $mes_resumen=$_POST['mes_resumen'] or $mes_resumen=date("m");
$agno_resumen=$parametros['agno_resumen'] or $agno_resumen=$_POST['agno_resumen'] or $agno_resumen=date("Y");
$ubicacion=$parametros['ubicacion'] or $ubicacion=$_POST['ubicacion'] or $ubicacion='San Luis';
$ultimo_dia=ultimoDia($mes_resumen,$agno_resumen);   //ultimo dia del mes seleccionado

if (strlen($mes_resumen)<=1) $mes_resumen="0".$mes_resumen;

$datos=array("San Luis","Todas");

$ausencia_justificada="#3333CC";
$ausencia_injustificada="#CC0000";
$llegadas_tarde_injustificada="#000000";
$llegadas_tarde_justificada="#5A9755";
$asistio="#FFFFFF";
$fin_de_semana="#AAAAAA";
$color_feriado="FBA39E";

$trabaja_dias=horas_trabaja($ubicacion);
$trabaja_dias_defecto=horas_trabaja_defecto();

?>
<form name='form1' action="reporte_asistencia_mes.php" method="POST">
<input type="hidden" name="mes_resumen" value='<?=$mes_resumen?>'>
<input type="hidden" name="agno_resumen" value='<?=$agno_resumen?>'>
<table border=0 width="100%" cellspacing=0 cellpadding=3 bgcolor='<?=$bgcolor3?>' align=center>
 <tr>
  <td align="center">Resumen de asistencia del mes de&nbsp;
	<? 
	g_draw_select("mes_resumen", $mes_resumen);
	echo "de&nbsp;";
	g_draw_range_select("agno_resumen", $agno_resumen, 1995, date("Y"));
	echo " &nbsp;&nbsp;<b>Ubicación:</b>";
	g_draw_value_select("ubicacion", $ubicacion, $datos, $datos);
	?>
	<input type="submit" name="actualizar" value="Actualizar datos">
	</td>
	</tr>
</table>

<?


$sql="select id_asistencia,fecha,tipo,comentario,hora_entra,id_usuario,usuario from 
      (select id_usuario,usuarios.apellido || ' ' || usuarios.nombre as usuario from sistema.usuarios
       left join personal.distrito on distrito.id_distrito=usuarios.pcia_ubicacion 
       left join permisos.phpss_account on (usuarios.login=phpss_account.username) 
        where visible=1 and active='true'";
if ($ubicacion != 'Todas')	
  $sql.=" and (distrito.nombre ilike '%$ubicacion%')";
$sql.=" order by id_usuario) as sub1
     left join 
       (select id_asistencia,fecha,tipo,comentario,hora_entra,id_usuario
        from (
         select fecha, 1 as tipo, comentario,-1 as id_asistencia,id_usuario from personal.inasistencia where fecha ilike '$agno_resumen-$mes_resumen-%'
            and tipo_justificacion= 1 
        union 
         select fecha, 3 as tipo, comentario,-1 as id_asistencia,id_usuario from personal.inasistencia where fecha ilike '$agno_resumen-$mes_resumen-%' 
            and tipo_justificacion=2 
        union 
         select fecha,2 as tipo, '' as comentario,id_asistencia,id_usuario from personal.asistencia  
            left join personal.inasistencia using (id_usuario,fecha) where fecha ilike '$agno_resumen-$mes_resumen-%' and id_inasistencia is null order by fecha asc
        ) as r 
         left join (select id_asistencia,hora_entra from personal.asistencia where fecha ilike '$agno_resumen-$mes_resumen-%' ) as d using (id_asistencia) 
       ) as sub2
      using (id_usuario)
";

$sql.= " ORDER BY sub1.usuario,sub2.fecha  ASC";

$res=sql($sql,"$sql") or fin_pagina();

$datos_usuario=array();
$id_ant="";
$i=-1;

while (!$res->EOF) {
	$id_usuario=$res->fields['id_usuario'];	
	if ($id_ant != $id_usuario ) {
	 $datos_usuario[++$i]['nombre']=$res->fields['usuario'];
	 $datos_usuario[$i]['id_usuario']=$res->fields['id_usuario'];
	 $datos_usuario[$i]['datos']=array();
	 $j=0;
	 $datos_usuario[$i]['datos'][$j]['id_asistencia']=$res->fields['id_asistencia'];
	
	 $datos_usuario[$i]['datos'][$j]['fecha']=$res->fields['fecha'];
	 $datos_usuario[$i]['datos'][$j]['comentario']=$res->fields['comentario'];
	 $datos_usuario[$i]['datos'][$j]['tipo']=$res->fields['tipo'];
	 $datos_usuario[$i]['datos'][$j]['hora_entra']=$res->fields['hora_entra'];
	}
	else {
	 $datos_usuario[$i]['datos'][$j]['id_asistencia']=$res->fields['id_asistencia'];
	 $datos_usuario[$i]['datos'][$j]['fecha']=$res->fields['fecha'];
	 $datos_usuario[$i]['datos'][$j]['comentario']=$res->fields['comentario'];
	 $datos_usuario[$i]['datos'][$j]['tipo']=$res->fields['tipo'];
	 $datos_usuario[$i]['datos'][$j]['hora_entra']=$res->fields['hora_entra'];
	}
	
	$j++;
	$id_ant=$id_usuario;
    $res->MoveNext();
}

$cantidad_datos=count($datos_usuario);

$asistencia=array();
$asistencia[]=date("t", mktime(0, 0, 0, intval($mes_resumen), 1, intval($agno_resumen)));//cuántos días tiene el mes y agno pedidos
for($j=1;$j<=$ultimo_dia;$j++) {
	$ddd=date_spa("D", $agno_resumen."-".$mes_resumen."-".$j);//nombre corto del día
	if (($ddd=="Dom")||($ddd=="Sab")) $asistencia[$j]=$fin_de_semana;//si $ddd es domingo o sábado marcarlo ("22") para no contar falta ese día
	else $asistencia[$j]=$asistio;
}

?>

<br>
<table border="1" cellpadding="0"  align="center" cellspacing="0">
 <tr>
   <td id="mo">Usuarios</td>
    <? 
    for ($i=1;$i<=$ultimo_dia;$i++) {
    	if ($asistencia[$i]!=$fin_de_semana){?><td height="15" id="mo_sf" width="15" align="center"><?=$i?></td>
    	<? }else{ ?><td height="15" id="ma_sf" width="15" align="center"><?=$i?></td>
         <?}}?>
   <td id="mo">&nbsp;</td>        
 </tr>
 
   <? 
      for($j=0;$j<$cantidad_datos;$j++) { 
       $datos=array();
       $nombre=$datos_usuario[$j]['nombre'];
       $id_usuario=$datos_usuario[$j]['id_usuario'];
       $datos=$datos_usuario[$j]['datos'];
      
     ?>
      <tr>
        <td id=ma style="cursor:hand" onclick="window.open('<?=encode_link('reporte_asistencia.php',array("id_usuario"=>$id_usuario))?>', '', '');">
           <?=$nombre?></td>
        <?
        $tardanza_injust=0;
        $tardanza_just=0;
        $falta_just=0;
        $falta_injust=0;
        
        $k=0;        
       for  ($i=1;$i<=$ultimo_dia;$i++) {
         
        	$dia=(($i<10)?"0$i":$i);
        	$num=calcula_numero_dia_semana($dia,$mes_resumen,$agno_resumen);
        	        	       	  
        	if (feriado($dia."/".$mes_resumen."/".$agno_resumen) ) {
        	  $color_celda=$color_feriado;
        	}
        	elseif (($datos[$k]['fecha']=="$agno_resumen-$mes_resumen-$dia")&&($datos[$k]["tipo"]==1)){
        		$color_celda=$ausencia_justificada;
        		 $falta_just++;
        		if ($datos[$k]["comentario"]) $title=$datos[$k]["comentario"];
        		$k++;
        	}elseif (($datos[$k]["fecha"]=="$agno_resumen-$mes_resumen-$dia")&&($datos[$k]["tipo"]==3)){
        		$color_celda=$llegadas_tarde_justificada;
        		$tardanza_just++;
        		if ($datos[$k]["comentario"]) $title=$datos[$k]["comentario"];
        		$k++;
        	}else if (($datos[$k]['fecha']=="$agno_resumen-$mes_resumen-$dia")&&($datos[$k]['tipo']==2)){
        		 
        		
        		 if ($trabaja_dias[$id_usuario][$num]) 
        		       $hora_entra=g_timeToSec($trabaja_dias[$id_usuario][$num].":00");
        		 else $hora_entra=g_timeToSec($trabaja_dias_defecto[$num].":00");        		
        		 
                 if ((g_timeToSec($datos[$k]["hora_entra"])) > ($hora_entra + g_timeToSec("00:15:00"))) {
        		     $color_celda=$llegadas_tarde_injustificada;
        		     $tardanza_injust++;
        		     $onclick="onclick=\"window.open('".encode_link("justificar_inasistencia.php",array("id_usuario"=>$id_usuario, "tipo"=>"2","nombre"=>$nombre,"fecha"=>"$agno_resumen-$mes_resumen-$dia"))."','','');\" style=\"cursor:hand\"";
        	         $title=" Justificar Tardanza";
        		}
        		else $color_celda=$asistio;
        		$k++;
        	}else if ($asistencia[$i]==$fin_de_semana) {
        		$color_celda=$fin_de_semana;
        	}else{
        		$color_celda=$ausencia_injustificada;
        		$falta_injust++;
        		$onclick="onclick=\"window.open('".encode_link("justificar_inasistencia.php",array("id_usuario"=>$id_usuario, "tipo"=>"1","nombre"=>$nombre,"fecha"=>"$agno_resumen-$mes_resumen-$dia"))."','','');\" style=\"cursor:hand\"";
        		$title=" Justificar Inasistencia";
        	}
         	?> 
             <td width="18" height="18" bgcolor='<?=$color_celda?>' <?=$onclick?> title="<?=$title?>">&nbsp;</td>
      <?
      		$title="";
   				$onclick="";
   				$color_celda="";
  
        }
        $link_reporte=encode_link("informe_asistencia.php",array("mes"=>"$mes_resumen","agno"=>$agno_resumen,"nombre"=>$nombre,"falta_just"=>$falta_just,"falta_injust"=>$falta_injust,"tardanza_just"=>$tardanza_just,"tardanza_injust"=>$tardanza_injust));
        
        ?>
           
           <td><input type="button" title="Reporte Asistencia" value="R" onclick="window.open('<?=$link_reporte?>','','top=50, left=170, width=500, height=200, scrollbars=1, status=1,directories=0')"> </td>
        </tr>  
        
   <?
  
  }?>      
   
 </tr>
</table>



<br>
<table border=1 width="80%" cellspacing=0 cellpadding=0 bgcolor='#FFFFFF' align=center>
<tr>
  <td colspan="15" align="left">&nbsp;Referencia de colores:</td>
</tr>
<tr>
  <td align='center' width="2%" bgcolor='<?=$asistio?>'><font color='<?=$asistio?>'>.</td>
  <td align="left" width="31%">&nbsp;Presente</td>
  <td></td>
  <td align='center' width="2%" bgcolor='<?=$ausencia_justificada?>'><font color='<?=$ausencia_justificada?>'>.</td>
  <td align="left" width="31%" >&nbsp;Ausencia Justificada</td>
  <td></td>
  <td align='center' width="2%" bgcolor='<?=$ausencia_injustificada?>'><font color='<?=$ausencia_injustificada?>'>.</td>
  <td align="left" width="31%" >&nbsp;Ausencia Injustificada</td>
  <td></td>
</tr>
<tr> 
  <td align='center' width="2%" bgcolor='<?=$color_feriado?>'><font color='<?=$asistio?>'>&nbsp;</td>
  <td align="left" width="31%">&nbsp;Feriado</td>
  <td></td>
  <td align='center' width="2%" bgcolor='<?=$llegadas_tarde_justificada?>'><font color='<?=$llegadas_tarde_justificada?>'>.</td>
  <td align="left" width="31%">&nbsp;Llegadas tarde (justificada) </td>
  <td></td>
  <td align='center' width="2%" bgcolor='<?=$llegadas_tarde_injustificada?>'><font color='<?=$llegadas_tarde_injustificada?>'>.</td>
  <td align="left" width="31%">&nbsp;Llegadas tarde (injustificada) </td>
  <td></td>
</tr>
</table>

</form>
<?
fin_pagina();
?>