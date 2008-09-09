<?php
	require_once(realpath('Protected/Included.inc'));
	if (empty($_SESSION['UserID']))
	{
		echo 'Go <a href="?Page=Login">login</a>.';
		echo '<script type="text/javascript">'."$('#Login').css('display', 'inline'); $('#Logout').css('display', 'none'); $('#Menu > ul').tabs('select', 7);</script>";
		$_SESSION['Debug'][] = 'Settings tab';
	}
	else
	{
		//get the token for the RSS
		$RSSStr = 'SELECT TokenRSS FROM '.$DataBase['TablePrefix'].'Users WHERE ID='.$_SESSION['UserID'];
		$RSSRes = mysql_query($RSSStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$RSSStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
		if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$RSSStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
		$RSSRow = mysql_fetch_assoc($RSSRes);
		echo '<p>Click here to make a backup <img ALIGN="middle" src="Images/Backup.gif" alt="B2E" title="Backup2Email" onclick="$.getScript(\'Backup.php\' );" style="width: 25px; height: 25px;" /></p>'; 
		echo 'We do not allow you to customize this script yet, but we will eventually allow you to change your name, add your own stylesheet, set some notification options (l;ike if you want to receive email when a deligated task gets marked as done).<br />';
		// 2Do: Get TokenRSS using $_SESSION['UserID']
		echo 'Your <a href="RSS.php?Token='.$RSSRow['TokenRSS'].'">RSS</a> feed.';
	}
?>