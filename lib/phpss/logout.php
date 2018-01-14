<?php

require("global.php");

// check that we actually have a valid session to destroy
Autenticar();

// destroy the session
phpss_logout();

// redirect to login form
header("Location: loginform.php");

?>

