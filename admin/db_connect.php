<?PHP
	
	$mysql_host 	= 'localhost:/tmp/mysql5.sock';
	$mysql_user 	= 'sallyDBwww';
	$mysql_password = 'swaDBadmin';
	$mysql_db 		= 'sally_www';

	// Connecting, selecting database
	$link = mysql_connect ($mysql_host, $mysql_user, $mysql_password);
	
	if (!$link) {
	   die('Could not connect: ' . mysql_error());
	}

	mysql_select_db($mysql_db, $link) or die('Could not select database: '.mysql_error().'<br>');

?>