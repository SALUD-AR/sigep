<?

// load phpSecureSite
require("phpss.php");

// define a phpss_auth() wrapper function
function Autenticar() {

	// call phpss_auth()
	$status = phpss_auth();

	// check the return value
	switch($status) {

		case PHPSS_AUTH_ALLOW:
			break;	// access is allowed, do nothing

		case PHPSS_AUTH_NOCOOKIE:
			exit("You need to be logged in to access this page");
			break;

		case PHPSS_AUTH_INVKEY:
			exit("You are using an invalid session");
			break;

		case PHPSS_AUTH_IPACCESS_DENY:
			exit("Access is not allowed from your IP address");

		case PHPSS_AUTH_ACLDENY:
			exit("You do not have permission to view this page");
			break;

		case PHPSS_AUTH_HIJACK:
			exit("Your IP address is different from the one the session was created with.");
			break;

		case PHPSS_AUTH_TIMEOUT:
			exit("You session has timed out, please log in again");
			break;
	}

}

?>