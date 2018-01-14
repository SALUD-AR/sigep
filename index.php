<?
require_once "config.php";
require_once("./modulos/permisos/permisos.class.php");

// Cargar el menu viejo
$old_menu = BROWSER_OK ? false : true;

if ($parametros['mode'] == "logout") {
	phpss_logout();
	$mode = "";
	include_once(ROOT_DIR."/login.php");
	exit;
}

if ($parametros['mode'] == "debug") {
   if (permisos_check("inicio","debug")) {
		$_ses_user["debug"] = $parametros["debug_status"];
		phpss_svars_set("_ses_user", $_ses_user);
	}
}

$res_width=$_ses_user['res_width'];
$res_height=$_ses_user['res_height'];
if ($res_width >= 1024) {
    $size=2;
    $tam=230;
    $letra="12px";
}
else {
	$size=1;    
	$tam=180;
	$letra="10px";
}
?>
<html>
<head>
<title>Sistema de Gestión Ministerio de Salud</title>

<link rel='icon' href='<? echo ((($_SERVER['HTTPS'])?"https":"http")."://".$_SERVER['SERVER_NAME']).$html_root; ?>/favicon.ico'/>
<link REL='SHORTCUT ICON' HREF='<? echo ((($_SERVER['HTTPS'])?"https":"http")."://".$_SERVER['SERVER_NAME']).$html_root; ?>/favicon.ico'/>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<link rel="stylesheet" type="text/css" href="<?php echo $html_root; ?>/lib/bootstrap-3.3.1/css/bootstrap.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $html_root; ?>/lib/estilos_bootstrap.css"/>
<?

if ($old_menu) {
	echo "<link rel='STYLESHEET' type='text/css' href='$html_root/lib/dhtmlXMenu_xp.css'>";
	echo "<script  src='$html_root/lib/dhtmlXCommon.js'></script>";
	echo "<script  src='$html_root/lib/dhtmlXProtobar.js'></script>";
	echo "<script  src='$html_root/lib/dhtmlXMenuBar.js'></script>";
	echo "<script  src='$html_root/lib/dhtmlXTree.js'></script>";
}
?>
</head>
<?

/*****************************************************************************************/
//Cuando use menu_xml va esta linea
$usuario=new user($_ses_user['login']);
/*****************************************************************************************/

$onclick=encode_link("index.php",array("mode" => "logout")); 
$root="$html_root/imagenes/logo_salud1.jpg"; 
$root1="$html_root/imagenes/logo_sumar.png"; 
$root2="$html_root/imagenes/logo_remediar.jpg"; 
$accesos=$usuario->get_Accesos();

?>

<body topmargin=0 leftmargin=0 rigthmargin=0 marginwidth="0" marginheight="0" style="{overflow-x:auto; overflow-y:hidden;}" onresize="fix_size();">  
<table name="contenido" id="contenido" width='100%' align="center" cellpadding="0" cellspacing="0" border="0">
  <tr>
		<td height="54" valign="top">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" height="54"  >
			<tr>
				<td width="375">
				<img border="0" src="<?=$root?>" width="171" height="38">
				<td>
				<? $cant_accesos=count($accesos);
				   $j=0;
				   ?>
				  <table border="0" cellpadding="0" cellspacing="0" width="150" >
					<tr>
				   <?
				   for($i=0;$i<$cant_accesos;$i++) {
				   	 if ($accesos[$i]) {
                      $modulo=$usuario->permisos->getmoduloByName($accesos[$i]);
                      $pagina=$usuario->permisos->getdescByName($accesos[$i]);
                      $descripcion=$usuario->permisos->getpathByName($accesos[$i]).$pagina;
                      $j++;
                      if (strpos($accesos[$i],"?") === false) 
                          $nombre_pagina=$accesos[$i].".php";
                      else 
                          $nombre_pagina=ereg_replace("\?",".php?",$accesos[$i]);
                    
                    ?> 
				       <td height="16px" nowrap align="center">
                           <a href="<?=$html_root?>/modulos/<?=$modulo?>/<?=$nombre_pagina?>" target="frame2">
                             <img src='<?=$html_root?>/imagenes/<?=($i+1)?>.jpg' alt='' border='0' title="<?=$pagina?>" 
          		             onclick="tempLinkText='<?=$descripcion?>'; tempLinkHref='<?=$html_root?>/modulos/<?=$modulo?>/<?=$nombre_pagina?>';">
                           </a>
                       </td>      
				     <? if ($j==($cant_accesos/2)) { ?>
				       </tr>
				       <tr>
				      <?}
				     }
				   }?>
				     
				 </tr>			
				 </table>
				 
				<?if (permisos_check("inicio","debug")) {
                   $debug_status = $_ses_user["debug"];
                   if ($debug_status == "on") {
                      $debug_link = encode_link($html_root."/index.php",array("mode"=>"debug","debug_status"=>"off"));
	                  $debug_desc = "Debugger Activado\nHaga click aquí para desactivarlo";
                   }
                  else {
	                  $debug_link = encode_link($html_root."/index.php",array("mode"=>"debug","debug_status"=>"on"));
	                  $debug_desc = "Debugger Desactivado\nHaga click aquí para activarlo";
                    }
				   echo "<td align='right'><A onclick='location.href=\"$debug_link&menu=\"+tempLinkHref;' target='_top'><img src='$html_root/imagenes/debug-$debug_status.gif' alt='$debug_desc' border='0' width='32px' height='30px'></a></td>";
				   

                   }?>				
                 <td align="right">
				<?list($dia,$mes,$anio,$dia_s) = split("-", date("j-n-Y-w",mktime()));?>
				<table border="0" cellpadding="0" cellspacing="0" width="<?=$tam?>" >
					<tr>
						<td align="right">
						<b><font face="Trebuchet MS" size="<?=$size?>" color="Navy">
						Gestion Ministerio de Salud
						</font></b></td>
					</tr>
					
					<tr>
						<td align="right">
	    					<b><font face="Trebuchet MS" size="<?=$size?>" color="Red"><?=$_ses_user["name"]?>
	    					</font></b>
						</td>
					</tr>		
					<tr>
						<td align="right"><b>
						  <font face="Trebuchet MS" size="<?=$size?>">
						  <?=$dia_semana[$dia_s]." ".$dia." de ".$meses[$mes]." de ".$anio?> 
						  </font></b>
						</td>
					</tr>
		
				</table>
				</td>
				<? 
                 $link_eventos= encode_link("$html_root/modulos/calidad/nuevo_evento.php",array("nuevo"=>1,"id_evento"=>-1,"cmd"=>"pendienetes"));
                 $titulo_eventos="Cada vez que necesite hacer una sugerencia haz clic aquí...";
                 $link=encode_link("$html_root/modulos/calidad/nuevo_evento.php",array("id_evento"=>-1,"cmd"=>"pendientes"));
                   ?>
				<td width="54" align="center">
				<a href="<?=$link?>" target="frame2">
				<img border="0" src="<?=$html_root?>/imagenes/iso.jpg" width="36" height="29" alt="<?=$titulo_eventos?>"></td>
				<td width="54" align="center">
				 <a href="<?=$onclick?>"><img border="0" src="<?=$html_root?>/imagenes/salir.jpg" width="24" height="24" alt="Salir"></a>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td background="<?=$html_root."/imagenes/fondo2.jpg"?>">
	    <span id="ruta_menu" style="width:100%;padding-left:10px;color:#000000;font-weight:bold;text-align:left;">
          <a id='link_menu_actual'  style='color:#000000;font-weight:bold;text-decoration:none;font-size:<?=$letra?>;font-family:Trebuchet MS'; target='frame2'></a>
        &nbsp;
          </span>
		</td>
	</tr>
<tr>
  <td colspan=2>
<?php
if ($old_menu) { 
    echo '<div id="xpstyle" style="width:100%;"></div>';
}
else {
    require_once("lib/fleximenu/autoload.php");

    $main = new Menu;

	$arbol=new ArbolOfPermisos("root");	
	$arbol->createMenu($usuario);
	$main = $arbol->saveBootstrapMenu($main);
?>
<header class="navbar navbar-inverse bs-docs-nav" role="banner">
    <nav class="navbar bs-navbar" role="navigation">
      <ul class="nav navbar-nav">
            <?php echo bootstrapItems($main); ?>
      </ul>
    </nav>
</header>

<?php
} // else old_menu
?>
  </td>
</tr>
<?
if ($parametros['mode'] == "debug") {  //es porque seleccionó el debugger y tiene que mantener la pagina
 	//echo "GET= ".$_GET['menu'];
    $div=ereg_replace("^$html_root","",$_GET['menu']);
    //echo " DIV despues de reemplazar= ".$div;
	$div=explode("/",$div,4);
	//echo "DIV despues del explode= ";
	//print_r($div);  
	$pagina_inicio=ereg_replace("\.php","",$div[3]);
 
}
else {
	$pagina_inicio=$usuario->get_pagina_inicio();
}


$modulo_inicio=$usuario->permisos->getmoduloByName($pagina_inicio);
$descripcion_inicio=$usuario->permisos->getpathByName($pagina_inicio).$usuario->permisos->getdescByName($pagina_inicio);

if (strpos($pagina_inicio,"?") === false) 
         $pagina_inicio=$pagina_inicio.".php";
else 
         $pagina_inicio=ereg_replace("\?",".php?",$pagina_inicio);

$src=$html_root."/modulos/$modulo_inicio/$pagina_inicio";


	echo "<tr><td>";
	echo "<iframe name='frame2' id='frame2' style='width:100%;height:100%;overflow-y:hidden;overflow-x:hidden;}' marginwidth=0 marginheight=0 frameborder=0 align='center' src='$src'></iframe>\n";
	echo "</td></tr>\n";

?>
</table>
<?php 
if ($old_menu) {
?>
    <script type="text/javascript">
	  function onButtonClick(itemId,itemValue) {
		//document.all.frame2.src='';
	  };

	    menu=new dhtmlXMenuBarObject(document.getElementById('xpstyle'),'100%',20,"",0);
		menu.setOnClickHandler(onButtonClick);
		menu.setGfxPath("imagenes/menu/");
	    menu.loadXML("<? echo ((($_SERVER['HTTPS'])?"https":"http")."://".$_SERVER['SERVER_NAME'])."$html_root/menu_xml.php"; ?>");  
        menu.showBar();
 	
        
//	    menu=new dhtmlXMenuBarObject(document.getElementById('xpstyle'),'100%',20,"",0);
//		menu.setOnClickHandler(onButtonClick);
//		menu.setGfxPath("imagenes/menu/");
//        menu.loadXML("menunew.xml");
//		menu.showBar();
//	
    </script>
<?php
}
?>
<script type="text/javascript" src="<?php echo $html_root;?>/lib/jquery/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="<?php echo $html_root;?>/lib/bootstrap-3.3.1/js/bootstrap.min.js"></script>
<script type='text/javascript' src='<?php echo $html_root;?>/lib/bootstrap-dialog/bootstrap-dialog.min.js'></script>
<script type='text/javascript' src='<?php echo $html_root;?>/lib/funciones.js'></script>

<script type="text/javascript">
  function fix_size() {
    //	alert($( window ).height()+' - ' + $( document ).height()+' - ' + window.document.body.clientHeight );
	//seteamos al maximo posible el largo del frame 2, para no desperdiciar espacio al final de la pagina ?>
	//var largo_body = window.document.body.clientHeight - ((document.all)?88:108);
	var position = $('#frame2').position();
	var largo_body = $( document ).height() - position.top; //((document.all)?88:108);
	document.getElementById('frame2').style.height=largo_body+"px";
	document.getElementById('contenido').style.width=((document.all)?100:100)+"%";
    }
 
 //Valores para la pagina de inicio

 var tempLinkText='<?=$descripcion_inicio?>';
 var tempLinkHref='<?=$src?>';
 var tempLinkTarget='frame2';
 var html_root='<? echo $html_root;?>';

 //se pone la ruta de inicio por defecto
 //objeto de tipo <a> se usa para actualizar la ruta al menu actual
 var oPath=document.getElementById('link_menu_actual');
 
 oPath.updateLink=function ()
 {
 	this.innerText=tempLinkText;
 	this.href=tempLinkHref;
 	this.target=tempLinkTarget;
  };


	$( document ).ready(function() {
		fix_size();
		$('.navbar a[target=frame2]').on('click', function() {
			var $this = $(this),
	    	$bc = $('<span></span>');

	  		$this.parents('li').each(function(n, li) {
		    	var $a = $(li).children('a').clone();
		  	  	var parents = $(li).parents('li').length;
		  		if (n == 0) {
					tempLinkTarget=$a.attr("target");
					tempLinkHref=$a.attr("href");
					//window.location.hash = $a.text();
		      	}
		      	if (parents > 0) {
		      		$bc.prepend(' > ', $a.text());
		      	} else {
	      			$bc.prepend($a.text());
	      		}
		  	});
			tempLinkText=$bc.text();
			oPath.updateLink();
		    return true;
		});
	});
</script>
</body>
</html>
