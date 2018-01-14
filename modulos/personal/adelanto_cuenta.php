<?php

/*
Autor: Broggi
Creado: 01/06/2004

$Author: broggi $
$Revision: 1.6 $
$Date: 2004/08/31 14:29:14 $
*/

require_once "../../config.php";
//print_r($_post);

//******** Valores de los estados ******************************
//1 Pendiente
//2 Por Autorizar
//3 En Curso 
//4 Terminado
//5 Cancelado

//*********Borra los pendientes que no llegaron a pasar para****
//*********que los autorizen************************************
if ($_POST['borrar']=="Borrar")
{while ($i<=$_POST['total_pedidos'])
  {if ($_POST['autorizar_'.$i]!="")
   {$db->StartTrans();
    $sql = "delete from log_cuenta where id_cuenta=".$_POST['idcuenta_'.$i];
    $result_consulta = sql($sql) or fin_pagina();
    $sql = "delete from cuota where id_cuenta=".$_POST['idcuenta_'.$i];
    $result_consulta = sql($sql) or fin_pagina();
    $sql = "delete from cuenta where id_cuenta=".$_POST['idcuenta_'.$i];
    $result_consulta = sql($sql) or fin_pagina();
    $db->CompleteTrans();
   }
   $i++;	
  }
}	
//**************************************************************


if ($_POST['nuevo_pedido']=="Nuevo Pedido")
{require_once('nuevo_adelanto.php');
 exit;
}	

echo $html_header;
variables_form_busqueda("adelanto_cuenta");//para que funcione el form busqueda

//armo la barra de navegacion
if ($cmd == "") {
	$cmd="apa";
    phpss_svars_set("_ses_adelanto_cuenta_cmd", $cmd);
}
//apa= Pendientes
//aa= Por Autorizar
//ac= En Curso
//at= adelantos todos
$datos_barra = array(
					array(
						"descripcion"	=> "Pendientes",
						"cmd"			=> "apa",
						),
					array(
						"descripcion"	=> "Por Autorizar",
						"cmd"			=> "aa"
						),
				    array(
						"descripcion"	=> "En Curso",
						"cmd"			=> "ac"
						),
				    array(
						"descripcion"	=> "Todos",
						"cmd"			=> "at"
						)
			      
				     );//Prepara los datos para armar la barra de navegación
echo "</br>";
generar_barra_nav($datos_barra);
//fin de barra de navegacion

?>
<script>
var contador=0;
//esta funcion sirve para habilitar el boton de cerrar 
function habilitar_aceptar(valor)
{
 if (valor.checked)
             contador++;
             else
             contador--;
 if (contador>=1)
    {window.document.all.borrar.disabled=0;
     window.document.all.cancelar.disabled=0;  
    }    
    else
    {window.document.all.borrar.disabled=1;
     window.document.all.cancelar.disabled=1;
    }
}//fin function

function deshabilitar(valor)
{window.document.all.borrar.disabled=1;
 window.document.all.cancelar.disabled=1;
}	
</script>

<form name="form_pedido_adelanto" action="adelanto_cuenta.php" method="post">

<!--*******************para el from busqueda************************-->
<?
if ($cmd=="ac" )
{$orden = array(
		"default" => "3",
 		"default_up" => "0",
		"1" => "cuenta.id_cuenta ",
		"2" => "legajos.apellido",
		"3" => "cuenta.monto_adeudado",
		"4" => "cuenta.fecha_pedido",
		"5" => "cuenta.pagos_restantes",
              );
}
if ($cmd=="apa" or $cmd=="aa")
{$orden = array(
		"default" => "1",
 		"default_up" => "1",
		"1" => "cuenta.id_cuenta ",
		"2" => "legajos.apellido",
		"3" => "cuenta.monto",
		"4" => "cuenta.fecha_pedido",
		"5" => "cuenta.cantidad_pagos"
	//	"3" => "pac_pap.tipo"
       	);
}   
if ($cmd=="at")    	
{$orden = array(
		"default" => "6",
 		"default_up" => "0",
		"1" => "cuenta.id_cuenta ",
		"2" => "legajos.apellido",
		"3" => "cuenta.monto",
		"4" => "cuenta.fecha_pedido",
		"5" => "cuenta.cantidad_pagos",
		"6" => "cuenta.monto_adeudado",
		"7" => "cuenta.pagos_restantes"
	      );	
}	
if ($cmd=="ac" )
{$filtro = array(
		"cuenta.id_cuenta" => "ID",
		"legajos.apellido" => "Solicitante",
        "cuenta.fecha_pedido" => "Fecha",
        "cuenta.monto_adeudado" => "Monto Adeudado",
        "cuenta.pagos_restantes" => "Pagos Restantes",		
	    );
};	    
if ($cmd=="apa" or $cmd=="aa")
{$filtro = array(
		"cuenta.id_cuenta" => "ID",
		"cuenta.fecha_pedido" => "Fecha",
		"legajos.apellido" => "Solicitante",
		"cuenta.monto" => "Monto Solicitado",
		"cuenta.cantidad_pagos" => "Cantidad de Pagos"
       	);
}

if ($cmd=="at")   	
{$filtro = array(
		"cuenta.id_cuenta" => "ID",
		"cuenta.fecha_pedido" => "Fecha",
		"legajos.apellido" => "Solicitante",
		"cuenta.monto" => "Monto Solicitado",
		"cuenta.cantidad_pagos" => "Cantidad de Pagos",
		"cuenta.monto_adeudado" => "Monto Adeudado",
		"cuenta.pagos_restantes" => "Pagos Restantes"
	      );	
}	

$query="select cuenta.*,legajos.apellido,legajos.nombre from cuenta join legajos using (id_legajo)";	
$where="";

if($cmd=="apa")
{$where=" cuenta.estado=1 or cuenta.estado=4";
 $contar="select count(*) from cuenta where cuenta.estado=1";
}
if($cmd=="aa")
{$where=" cuenta.estado=2";
 $contar="select count(*) from cuenta where cuenta.estado=2";
}
if($cmd=="ac")
{$where=" cuenta.estado=3";
 $contar="select count(*) from cuenta where cuenta.estado=3";
}
echo "<br>";
echo "<center>";
$contar="buscar";


list($sql,$total_pedidos,$link_pagina,$up) = form_busqueda($query,$orden,$filtro,$link_tmp,$where,$contar); 
$resultado=sql($sql) or fin_pagina();

?>	

&nbsp;&nbsp;<input type=submit name=form_busqueda value='Buscar'>
</center>
<br>
<table width="30%" align="center">  
 <tr >
  <td align="center" >
   <input name="nuevo_pedido" type="submit" value="Nuevo Pedido" >
  </td>
 </tr>
</table>
<br>


<table width='95%' align="center" cellspacing="2" cellpadding="2" class="bordes">
 <tr id=ma>
  <td align="left" <?if ($cmd==apa) {echo "colspan='4'"; } else echo "colspan='3'"?>>
   <b>Total:</b> <?=$total_pedidos?> <b>Adelanto/s Encontrado/s.</b>
   <input name="total_pedidos" type="hidden" value=<?=$total_pedidos?>>
  </td>
  <td align="right" colspan="4">
   <?=$link_pagina?>
  </td>
 </tr>
 <tr id=mo>
  <?
   if($cmd=="apa") 
   {
  ?> 	 
   <td width="1%"></td>
  <?
   }
  ?> 
  <td width="5%" ><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"1","up"=>$up))?>'>ID</a></b></td>
  <td width="10%"><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"4","up"=>$up))?>'>Fecha</b></td>
  <td width="40%"><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"2","up"=>$up))?>'>Solicitante</a></b></td>
  <?
   if($cmd=="ac") 
   {
  ?> 	 
   <td width="20%"><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"3","up"=>$up))?>'>Monto Adeudado</b></td>
   <td width="10%"><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"5","up"=>$up))?>'>Pagos Restantes</b></td>
  <?
   }
   else 
   {
  ?>
   <td width="20%"><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"3","up"=>$up))?>'>Monto Solicitado</b></td>
   <td width="10%"><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"5","up"=>$up))?>'>Cantidad de Pagos</b></td>
  <?
   }
   if ($cmd=="at")
   {
  ?>
   <td width="20%"><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"6","up"=>$up))?>'>Monto Adeudado</b></td>
   <td width="10%"><b><a href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"7","up"=>$up))?>'>Pagos Restantes</b></td>
  <?
   } 
  ?>
 </tr>
<?
 /*$i=1;
 $cnr=1;*/
while(!$resultado->EOF)
{$sql_adicional="select * from (select count(id_cuota) as pagos_restantes, id_cuenta 
                 from personal.cuota where id_cuenta=".$resultado->fields['id_cuenta']."
                 and estado=1 group by id_cuenta) as cuotas_pagar join 
                 (select sum(monto_cuota) as monto_adeudado, id_cuenta
                 from personal.cuota where id_cuenta=".$resultado->fields['id_cuenta']."
                 and estado=1 group by id_cuenta) as adeudado using(id_cuenta)";
 $ejecuta_sql_adicional=sql($sql_adicional) or fin_pagina();
  ?> 

  <tr <?=atrib_tr()?> >
  <?
  if($cmd=="apa") 
   {
   ?> 	 
   <td width="1%"><input type="checkbox" name="autorizar_<? echo $i; ?>" value="<? echo $resultado->fields['id_cuenta']; ?>" onclick="habilitar_aceptar(this)"></td>
   <?
   }
 $link = encode_link("nuevo_adelanto.php",array("pagina"=>"adelanto_cuenta","id"=>$resultado->fields["id_cuenta"],"cmd"=>$cmd));  
 ?>	
  <a href='<?=$link?>'>
 <?
  switch ($resultado->fields['estado'])
  {case 1 : $color_estado="yellow";
   break; 
   case 2 : $color_estado="orange";
   break;
   case 3 : $color_estado="green";
   break;
   case 4 : $color_estado="red";
   break;
   case 5 : $color_estado="gray";
   break;
   default : $color_estado="black";
  }	     
 ?>
  
 <td align="center" <?if ($cmd=="at") echo"bgcolor=$color_estado"?>>
  <?=$resultado->fields['id_cuenta']?> 
  <input name="idcuenta_<?=$i?>" type="hidden" value=<?=$resultado->fields['id_cuenta']?>>
 </td>
 <td align="center">
  <?
   $fecha=split(' ',$resultado->fields['fecha_pedido']);//función que separa la fecha de la hora
   echo fecha($fecha[0]);//aca me queda solo la fecha 
  ?>
 </td>
 <td>
  <?=$resultado->fields['apellido']. ', '.$resultado->fields['nombre']?> 
 </td>
 <?
  if ($cmd=="ac")
   {
 ?>
   	<td align="center">
     $ <?=formato_money($ejecuta_sql_adicional->fields['monto_adeudado'])?>
    </td>
    <td align="center">
     <?=$ejecuta_sql_adicional->fields['pagos_restantes']?>
    </td>
  <?   	
   }	
  else  
  {
 ?> 	  
  <td align="center">
   $ <?=formato_money($resultado->fields['monto'])?>
  </td>
  <td align="center">
   <?=$resultado->fields['cantidad_pagos']?>
  </td>
 <? 
  }
  if ($cmd=="at")
  {
 ?> 
   <td align="center">
     $ <?=formato_money($ejecuta_sql_adicional->fields['monto_adeudado'])?>
    </td>
    <td align="center">
     <?if ($ejecuta_sql_adicional->fields['pagos_restantes']=="") echo "0"; else echo$ejecuta_sql_adicional->fields['pagos_restantes'] ?>
    </td>
  <?
  }  	
 ?> 
 </a>
</tr>
<?
 $i++;
 $resultado->MoveNext();
}//del while
?>
</table>
<!--*********************************-->

<br>
<input type="hidden" name="cant" value="<? echo $resultado->RecordCount(); ?>">

<table width="30%" align="center">
<?
 if ($cmd=="apa") 
  {
 ?>	
 <tr>
  <td align="center">
   <input name="borrar" type="submit" value=Borrar disabled>
  </td>
  <td align="center">
   <input name="cancelar" type="button" value="Cancelar" disabled onclick="document.form_pedido_adelanto.reset();deshabilitar(this)">
  </td>
 </tr>
 <?
 }
 ?> 
</table>
<?
 if ($cmd==at)
 {
?>
 <table border=1 bordercolor='#000000' bgcolor='#FFFFFF' width='100%' cellspacing=0 cellpadding=0>
  <tr>
   <td colspan=10 bordercolor='#FFFFFF'>
    <b>Colores de referencia ID:</b>
   </td>
  </tr>
  <tr>
   <td width=33% bordercolor='#FFFFFF'>
    <table border=1 bordercolor='#FFFFFF' cellspacing=0 cellpadding=0 wdith=100%>
     <tr>
      <td width=15 bgcolor='yellow' bordercolor='#000000' height=15>
       &nbsp
      </td>
      <td bordercolor='#FFFFFF'>
       Pendientes
      </td>
     </tr>
    </table>
   </td>
   <td width=33% bordercolor='#FFFFFF'>
    <table border=1 bordercolor='#FFFFFF' cellspacing=0 cellpadding=0 wdith=100%>
     <tr>
      <td width=15 bgcolor='orange' bordercolor='#000000' height=15>
       &nbsp
      </td>                
      <td bordercolor='#FFFFFF'>
       Por Autorizar
      </td>
     </tr>
    </table>
   </td>
   <td width=33% bordercolor='#FFFFFF'>
    <table border=1 bordercolor='#FFFFFF' cellspacing=0 cellpadding=0 wdith=100%>
     <tr>
      <td width=15 bgcolor='green' bordercolor='#000000' height=15>
       &nbsp
      </td>
      <td bordercolor='#FFFFFF'>
       En Curso
      </td>
     </tr>
    </table>
   </td>
  </tr> 
  <tr> 
   <td width=33% bordercolor='#FFFFFF'>
    <table border=1 bordercolor='#FFFFFF' cellspacing=0 cellpadding=0 wdith=100%>
     <tr>
      <td width=15 bgcolor='red' bordercolor='#000000' height=15>
       &nbsp
      </td>
      <td bordercolor='#FFFFFF'>
       Rechazado
      </td>
     </tr>
    </table>
   </td>
   <td width=33% bordercolor='#FFFFFF'>
    <table border=1 bordercolor='#FFFFFF' cellspacing=0 cellpadding=0 wdith=100%>
     <tr>
      <td width=15 bgcolor='gray' bordercolor='#000000' height=15>
       &nbsp
      </td>
      <td bordercolor='#FFFFFF'>
       Cancelado
      </td>
     </tr>
    </table>
   </td>
  </tr>
 </table> 

<?
 }
?>    	
</form>
<?=fin_pagina(false);?>
<!--</body>
</html>-->

