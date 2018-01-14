<?

require_once("../../config.php");

$_ses_global_manuales_distrito=="plan_nacer";

if($parametros['download'])
{
 $query="select nombre,tipo,tamaño from archivo_manual where id_archivo_manual=".$parametros['download'];
 $result=sql($query) or fin_pagina();
 $path=UPLOADS_DIR."/Calidad/Manuales";
 $FileNameFull="$path/".$result->fields['nombre'];
 if (file_exists($FileNameFull))
 {
	Mostrar_Header($result->fields['nombre'],$result->fields['tipo'],$result->fields['tamaño']);
	readfile($FileNameFull);
 }
 else
 {
	Mostrar_Error("Se produjo un error al intentar abrir el archivo");
 }
}
$msg=$parametros['msg'];

echo $html_header;
variables_form_busqueda("manual");

if ($cmd == "") {
	$cmd="validos";
    phpss_svars_set("_ses_manual_cmd", $cmd);
}


$datos_barra = array(
     				array(
						"descripcion"	=> "Pendientes",
						"cmd"			=> "pendientes",
                        
						),
					array(
						"descripcion"	=> "Válidos",
						"cmd"			=> "validos",
                        
						),
				    array(
						"descripcion"	=> "Historial",
						"cmd"			=> "historial",
                       
						)
				 );
echo "<br>";
?>

<?
generar_barra_nav($datos_barra);
?>
<script>
var contador=0;
//esta funcion sirve para habilitar el boton de cerrar
function habilitar_historial(valor)
{
 if (valor.checked)
             contador++;
             else
             contador--;
 if (contador>=1)
         window.document.all.boton_historial.disabled=0;
        else
         window.document.all.boton_historial.disabled=1;
}//fin function
</script>
<br>
<?

$link=encode_link("listado_manual.php",array("distrito"=>$_ses_global_manuales_distrito));
?>
<form name="form1" method="POST" action="<?=$link?>">

<?
$orden = array(
		"default" => "2",
 //		"default_up" => "1",
 		"1" => "manual.id_clasificacion, manual.id_manual",
		"2" => "manual.id_clasificacion, manual.titulo",
		"3" => "manual.id_clasificacion, manual.estado",
        "4" => "manual.id_clasificacion, archivo_manual.nombre",
	);

$filtro = array(
		"manual.id_manual" => "ID",
		"manual.titulo" => "Título",
        "archivo_manual.nombre" => " Nombre Archivo"

	);

//************************************************************************
//ATENCION	ATENCION ATENCION ATENCION ATENCION ATENCION ATENCION ATENCION
//GUARDA QUE EL JOIN no trae el id_manual no se porque....y es necesario el
//left join porque no siempre hay archivos cargados en todo momento


$query="select manual.id_manual,titulo,estado,id_clasificacion,archivo_manual.id_archivo_manual,archivo_manual.nombre,log.fecha
              from manual
              left join (select max(fecha)as fecha,id_manual from calidad.log_manual where (tipo='archivo subido' or tipo='actualización del manual') group by (id_manual))as log using(id_manual)
              left join archivo_manual using (id_manual)";
$where="";
if($cmd=="pendientes")
    {
     $where=" ((estado=0 or estado=1) and historial=0)";
     $contar="select count(*) from manual where ((estado=0 or estado=1) and historial=0) ";
    }
    elseif($cmd=="validos")
     {
      $where=" estado=2 and historial=0";
      $contar="select count(*) from manual where (estado=2 and historial=0) ";
     }
    elseif($cmd=="historial")
      {
       $where=" historial=1";
       $contar="select count(*) from manual where historial=1 ";
      }


 if ($id_distrito)
       $where.=" and id_distrito=$id_distrito";
echo "<center>";
$itemspp=50;
if($_POST['keyword'] || $keyword)// en la variable de sesion para keyword hay datos)
     $contar="buscar";



list($sql,$total_pac_pap,$link_pagina,$up) = form_busqueda($query,$orden,$filtro,$link_tmp,$where,$contar);

$result = sql($sql) or fin_pagina();

?>

&nbsp;&nbsp;<input type=submit name=form_busqueda value='Buscar'>
</center>
<br>
<?="<b><center>$msg</center></b>";?>
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
<div style='position:relative; width:100%; height:55%; overflow:auto;'>
<table width='95%' border='0' cellspacing='2' align="center">
<tr id=mo>
 <td width='10%'><b><a id=mo href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"1","up"=>$up,"distrito"=>$_ses_global_manuales_distrito))?>'>ID</a></b></td>
 <?
 if($cmd=="pendientes")
 {
 ?>
 <td width='10%'><b><a id=mo href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"3","up"=>$up,"distrito"=>$_ses_global_manuales_distrito))?>'>Estado</a></b></td>
 <?
 }
 ?>
 <td width='60%'><b><a id=mo href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"2","up"=>$up,"distrito"=>$_ses_global_manuales_distrito))?>'>Título</a></b></td>
 <td width='20%'><b><a id=mo href='<?=encode_link($_SERVER["PHP_SELF"],array("sort"=>"4","up"=>$up,"distrito"=>$_ses_global_manuales_distrito))?>'>Archivo</a></b></td>
 <?if (permisos_check('inicio','permiso_ver_fecha'))
	   {?>
 <td width='20%'><b>Fecha</b></td>
 <?}?>
</tr>
<?
$i=1;
$cnr=1;
$flagvisible = "";//lo utilizo para verificar si muestro o no la clasificacion

while(!$result->EOF){
            $link = encode_link("detalle_manual.php",array("distrito"=>$_ses_global_manuales_distrito,"pagina"=>"listado","id"=>$result->fields["id_manual"]));
            tr_tag($link)?>
                              
            <?
            //condicion si lo muestra o no la descripcion
            if (($flagvisible=="") or ($flagvisible != $result->fields['id_clasificacion']))
            	$visible="";
            else 
            	$visible="none";
            ?>
            
            <tr >
      		<td colspan="4" align="left"> 
      		<div id='desc' style='display:<?=$visible?>'>
      		<strong>
      		<?
            //realizo consulta para recuperar el nombre de la descripcion
      		$sql_clasi="select descripcion from clasificacion_manual where id_clasificacion=".$result->fields['id_clasificacion'];
      		$result_clasi = sql($sql_clasi) or fin_pagina(); 		
			//imprimo la descripcion        		
            echo $result_clasi->fields['descripcion'];
      		//le asigno valor para verificar cuando vuelva de while
      		$flagvisible=$result->fields['id_clasificacion'];
      		?>
      		</strong>
  	  		</div>
			</td>
	    	</tr>
            
	    	
 			<tr <?=tr_tag($link)?>
            
      		<td align="center"> <?=$result->fields['id_manual']?> </td>
             <?
             if($cmd=="pendientes")
                 {
                 ?>
                 <td align="center">
                  <?
                  switch($result->fields['estado'])
                  {case 0:echo "Pendiente";break;
                   case 1:echo "Revisado";break;
                  }
                  ?>
                 </td>
                 <?
                 }
                 ?>

            <td><?=$result->fields['titulo']?></td>
            
            <td align="right">
              <?
               if($result->fields['nombre']=="")
                echo "&nbsp;";
                else
                {
                $link=encode_link("listado_manual.php",array("distrito"=>$_ses_global_manuales_distrito,"download"=>$result->fields['id_archivo_manual']));
               	?>
                <a href='<?=$link?>'><?=$result->fields['nombre']?></a>
                <?
                }
              ?>
             </td>
              <?if (permisos_check('inicio','permiso_ver_fecha'))
	          {?>
             <td>
             <?=Fecha($result->fields['fecha']);?>
             </td>
             <?}?>
            </tr> 
          
            <?
             $i++;
             $result->MoveNext();
 }//del while
?>
</table>
</div>
<input type="hidden" name="cant" value="<? echo $result->RecordCount(); ?>">
<center>
<?
$link=encode_link("detalle_manual.php",array("distrito"=>$_ses_global_manuales_distrito));
?>
  <input type="button" name="boton_nuevo" value="Agregar Nuevo" onclick="document.location='<?=$link?>'">
</center>
</form>
</body>
</html>
<?
echo fin_pagina();
?>