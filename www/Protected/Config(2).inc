<?php	// Includes all common server and website variables.

	// Set debuging mode:
	$Debug = false; // Used for testing (true=on, false=off). In this mode, additional information/option and all error messages are shown.
//	if (!($Debug && !empty($_SERVER['HTTP_HOST']) && false === strpos($_SERVER['HTTP_HOST'], 'www.')))
//	{
//		$Debug = true;
//	}

	// Set the error reporting level:
	if ($Debug)
	{
		error_reporting(E_ALL);
		ini_set('display_errors', true); // This helps on servers where the PHP error logging happens via a log file instead of displaying it on screen. Turn this off when the site goes live!
	}
	else
	{
		error_reporting(0);
		ini_set('display_errors', false); // Prevents visible output.
	}

	$DataBase['Server']		= 'localhost'; // This is almost always: localhost
	$DataBase['User']		= 'raul'; // Database login account.
	$DataBase['Password']		= ''; // Database login password.
	$DataBase['Name']		= 'GIT2Do'; // Preferably use the CVS name of the project (with the appropriate SwitchCase).
	$DataBase['TablePrefix']	= '2Do_'; // Use the project-initials.
	$DataBase['Engine']		= 'mysql'; // Use a PEAR compatible data source name.

	$HomeDir			= 'C:\\wamp\\www\\'; // Absolute path (INcluding the trailing slash).
	$DomainName			= 'localhost'; // The primary domain name (eg: eBrain.nl).
	$wwwDir				= ''; // Path from $SiteURL/ to the main index file (INcluding trailing slash). This is used to rewrite links to the absolute URL path.

	$SiteURL			= 'http://'.$DomainName.'/';
	$FullSiteURL			= $SiteURL.$wwwDir; // URL path to the main index file (INcluding trailing slash); in most cases this includes the /www/.

	$AddedImagesDir			= $HomeDir.'Uploads/';
	$AddedImagesRelDir		= $FullSiteURL.'Uploads/';

	$Time				= time(); // This is so we have 1 definate common time throughout the whole script.

	$DefaultLanguage		= 'EN'; // Set the default language (use the LARGECAP two-letter ISO 639-1 alpha-2 code). Only used if this is not provided by the database.
	$getWordLinks			= false; // Used to auto-create in-site links to the Translations Matrix.

?>