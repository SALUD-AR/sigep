<?php

// load the global-include
require("global.php");

// check if the session is valid
Autenticar();

?>
<html>
<body>

Welcome to the secret page! Nothing of interest here...
<p>
You may now <a href="logout.php">log out</a>.

</body>
</html>