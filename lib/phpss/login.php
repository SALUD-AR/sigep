<?

// load phpSecureSite
require("phpss.php");

// check the username and password
$status = phpss_login($_POST['username'], $_POST['password']);

// check if the user is allowed access
if ($status > 0) {
	header("Location: secret.php"); // redirect to secret page
} else {

	// check the error code
	switch ($status) {
		case PHPSS_LOGIN_AUTHFAIL:
			print("You entered a wrong username and/or password");
			break;

		case PHPSS_LOGIN_IPACCESS_DENY:
			print("Logins are not allowed from your IP address");
			break;

		case PHPSS_LOGIN_BRUTEFORCE_LOCK_ACCOUNT:
			print("This account is locked for logins due to too many failed login attempts");
			break;

		case PHPSS_LOGIN_BRUTEFORCE_LOCK_SRCIP:
			print("Logins from your IP address are not allowed because of too many failed login attempts");
			break;

		default:
			print("Unknown return code when attempting to authenticate user");
	}
}

?>