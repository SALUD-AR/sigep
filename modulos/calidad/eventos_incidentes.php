<?


require_once("../../config.php");
variables_form_busqueda("encuesta");


if (!$cmd) {
	$cmd="pendientes";
	$_ses_encuesta["cmd"] = $cmd;
	phpss_svars_set("_ses_encuesta", $_ses_encuesta);
}


if ($_POST['nuevo']=="Nuevo" || $parametros["nuevo"]==1)
{   
    if($parametros["nuevo"]==1)
     $cmd="pendientes";
     
	$link=encode_link("nuevo_evento.php",array("id_evento"=>-1,"cmd"=>$cmd));
     header("location:$link");
	 //require_once("nuevo_evento.php");
	 //exit;
}	


$datos_barra = 
array(
					array(
						"descripcion"	=> "Pendientes",
						"cmd"			=> "pendientes"
						),
					array(
						"descripcion"	=> "Terminadas",
						"cmd"			=> "terminadas"
						)/*,
					array(
						"descripcion"	=> "Estadísticas",
						"cmd"			=> "estadisticas"
						)	*/
	 );
?>
<?=$html_header?>
</head>
<body>
<script>
// funciones que iluminan las filas de la tabla
function sobre(src,color_entrada) {
    src.style.backgroundColor=color_entrada;src.style.cursor="hand";
}
function bajo(src,color_default) {
    src.style.backgroundColor=color_default;src.style.cursor="default";
}
function link_sobre(src) {
    src.id='me';
}
function link_bajo(src) {
    src.id='mi';
}
</script>
<form name="form1" method="post" action="">
<? generar_barra_nav($datos_barra); ?>
  <br>
  <table align='center' width="90%" >
  <tr>
  <? if ($cmd=="pendientes")
  {
  	?>
  <td align="left" width="6%">
   <input type="submit" name="nuevo" value="Nuevo">
  </td>
  <?
  }
  ?> 
  <td align="center">
  
<?
/*estado 1=Terminada
         2=Pendiente
         3= no se todavia
 */          
 $q="select * from evento  ";
	//$q.="left join log_evento using (id_evento) ";
	$q.="left join tipo_evento using (id_tipo_evento)";
	
 $orden = array(
		        "default" => "2",
		        "default_up" => "0",
		        "1" => "evento.id_evento",
		        "2" => "evento.area",
		        "3" => "tipo_evento.id_tipo_evento"		
	           );	
 $filtro = array(
                 "evento.id_evento" => "ID Even./Inci.",
		         "evento.area" => "Area",
		         "tipo_evento.id_tipo_evento" => "Tipo Evento"
	            );	
 if ($cmd=='terminadas') $where = "evento.estado=2";		
 elseif ($cmd=='pendientes') $where = "evento.estado=1"; 
 echo "<center>{$parametros['msg']}</center>";

 list($sql,$total_usr,$link_pagina,$up) = form_busqueda($q,$orden,$filtro,$link_tmp,$where,"buscar");
 echo "&nbsp;&nbsp;<input type=submit name=buscar value='Buscar'>\n";
 $eventos= sql($sql) or reportar_error($sql,__FILE__,__LINE__); 
 ?>
 </td> 
 </tr>  
 
 </table>

  <table width="100%" border=0 cellpadding=1 cellspacing=1 id="tabla_resumen">
    <tr id=ma height=20 >
      <td align="left">Resultado: <? echo (($eventos)?$eventos->RecordCount():0 )." Eventos/Incidentes encontrados"; ?> </td>
    </tr>
  </table>

  <table width="100%" border="0" cellpadding="1" cellspacing="2" id="tabla_resultados">
    <tr id="mo" height=20 align="center"> 
      <td width="5%" id="mo">Mostrar Desc.</td>
      <td width="10%"><a id=mo href='<? echo encode_link($_SERVER['PHP_SELF'],array("sort"=>"1","up"=>$up)); ?>'>ID Even./Inci.</a></td>
      <td width="45%"><a id=mo href='<? echo encode_link($_SERVER['PHP_SELF'],array("sort"=>"2","up"=>$up)); ?>'>Area</a></td>
      <td width="40%"><a id=mo href='<? echo encode_link($_SERVER['PHP_SELF'],array("sort"=>"3","up"=>$up)); ?>'>Tipo de Evento</a></td>
    </tr>
 <? 
 
 //inicializo variables
 $eventos->MoveFirst();
 $cont=0;
//lleno la tabla
 while (!$eventos->EOF)
	{
		//va a la pagina referenciada codificada!!
 		$ref = encode_link("nuevo_evento.php",
 				array("id_evento"=>$eventos->fields['id_evento'],"cmd"=>$cmd));
 		//a la variable $onclick le doy la referencia
 		$onclick="onClick=\"location.href='$ref'\";"; 
 		$suc=$eventos->fields['suseso'];
		
 ?>
      <!-- No puedo dejar que me habra la pagina nuevo_evento.php en todo la fila lo hago todos menos el check-->
 	  <tr <?php echo $atrib;?>>
      <tr <?=atrib_tr()?>><!-- no tiene tile-->
      
      <td align=center> <INPUT type=checkbox name="check_<?=$cont?>" onclick="javascript: (this.checked)?Mostrar('desc_<?=$cont?>') :Ocultar ('desc_<?=$cont?>');"></td>
      <td align=center <?=$onclick?> title="<?=$suc?>"> <b> <? echo $eventos->fields['id_evento'] ?> </b></td>
      <td align=center <?=$onclick?> title="<?=$suc?>"> <b> <? echo $eventos->fields['area'] ?> </b></td>
      <td align=center <?=$onclick?> title="<?=$suc?>"> <b> <? echo $eventos->fields['tipo_evento'] ?> </b></td>      
      
      	    	
		<tr <?php echo $atrib;?>>
      	<tr <?=atrib_tr()?>>
      	<td colspan="4" align="center" <?=$onclick?>> 
      	<div id='desc_<?=$cont?>' style='display:none'>
      	<strong>
      	<textarea name="suceso" cols="150" rows="10" readonly="readonly" id="nombre"> <? echo $eventos->fields['suseso'] ?> </textarea>
		</strong>
  	  	</div>
		</td>
	    </tr>
  	  	
  	  	
  	  	
  	 </tr>
     
 <? 
	 	$cont++;
 		$eventos->MoveNext();	
	}//del while
 ?>
  </table>
 </form>
 <?


	
?>
</table>
<?=fin_pagina(false);?>