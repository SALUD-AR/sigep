<?php
require_once("../../config.php");

$user=$_ses_user['login'];
if (es_cuie($user)) {

		header('Location: efectores_detalle.php');
	}
	else {
		header('Location: seguimiento.php');
	}