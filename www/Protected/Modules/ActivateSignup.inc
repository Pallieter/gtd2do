<?php
	// FF2Do: fix this include path!
	$Result = realpath('C:\\wamp\\www\\Protected\\Included_Raw.inc');
	require_once($Result);
	$Tokenillo = (empty($_GET['Action']) ? '' : mysql_real_escape_string($_GET['Action']));
	
	$ActivateStr = 'UPDATE '.$DataBase['TablePrefix'].'Users SET Status = (Status|1) WHERE Token="'.$Tokenillo.'" LIMIT 1';
	$ActivateRes = mysql_query($ActivateStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$ActivateStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
	if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$ActivateStr.'<br />File: '.__FILE__.' on line: '.(__LINE__)."<br />\n"; }
	if (mysql_affected_rows() != 0)
	{
		echo 'Account activated succesfully. You will be redirected soon.';
		echo ' Or <a href="?Page=Login" onclick="$(\'#Login\').css(\'display\', \'inline\'); $(\'#Logout\').css(\'display\', \'none\'); $(\'#Menu > ul\').tabs(\'select\', 6); return false;">click here</a> to login.';
		//redirect after 6 seconds
		// 2Do: if the tab "about" is not the active tab, don't execute the timeout
		//echo '<script type="text/javascript"> function redirect() { window.location="http://192.168.24.13/?Page=Login"; }; setTimeout("redirect()", 6000); </script>';
		echo '<script type="text/javascript"> ';
		// echo '$(\'#Content\').attr({href: \'#Views\'}); '; // Content.php
		echo "$('#Menu > ul').tabs('url',0, 'Content.php'); ";
		echo 'function redirect() { $(\'#Login\').css(\'display\', \'inline\'); $(\'#Logout\').css(\'display\', \'none\'); $(\'#Menu > ul\').tabs(\'select\', 6); return false; }; setTimeout("redirect()", 6000); ';
		echo '</script>';
	}
	else
	{
		echo 'Error! Try registering again';
	}
?>