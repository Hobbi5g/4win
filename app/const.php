<?

	define('PAGINATOR_GAMES_PER_PAGE', 12);
	define('PAGINATOR_MEMBERS_PER_PAGE', 10);

	define('SITEURL', 'http://'.$_SERVER['SERVER_NAME'].'/' );

	if (SITEURL == 'http://www.games4win.com/' ||
        SITEURL == 'http://games4win.com/' ||
		SITEURL == 'https://games4win.com/') {
		include('server_production.php');
		define("ENVIRONMENT", "production");
		define("DEBUG", false);
		define("INSTALLER_VENDOR", "installcore"); // installcore, solimba, amonetize
	}

	if (SITEURL == 'http://g4w.codingrabbits.com/') {
		define("CONNECTION_STRING", 'mysql://root:rootpwd1qazXSW@@localhost/g4w2');
		define("ENVIRONMENT", "development");
		define("DEBUG", true);
		define("APPLICATION_PATH", "/var/www/games4win88/");
		define("INSTALLER_VENDOR", false);
	}
	else
	{
		// LOCAL
		define("CONNECTION_STRING", 'mysql://root:@127.0.0.1/g4w2');
		define("ENVIRONMENT", "development");
		define("DEBUG", true);
		define("INSTALLER_VENDOR", false);
	}

