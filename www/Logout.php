<?php
	require_once(realpath('Protected/Included_Raw.inc'));

	$_SESSION['UserID'] = false;
	unset($_SESSION['UserID']);

	echo 'You are logged out.';
	//echo "<script>$('#Login').css('display', 'inline'); $('#Logout').css('display', 'none');</script>";
	echo ' <a href="?Page=Login" onclick="$(\'#Login\').css(\'display\', \'inline\'); $(\'#Logout\').css(\'display\', \'none\'); $(\'#Menu > ul\').tabs(\'select\', 6); return false;">Login again.</a>';
?>