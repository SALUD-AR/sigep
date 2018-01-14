<?php

include_once("../../config.php");
$mode = $_GET["mode"];
if ($mode == "usuarios") {
	include_once("usuarios_view.php");
   exit;
}
?>