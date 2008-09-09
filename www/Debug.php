<?php
	require_once(realpath('Protected/Included_Raw.inc'));
	if (empty($_SESSION['Debug']))
	{
		echo '<p>No debug info available.</p>';
	}
	else
	{
		echo '<pre>';
		foreach ($_SESSION['Debug'] AS $Report)
		{
			echo htmlspecialchars($Report).'<br />';
		}
		echo '</pre>';
		$_SESSION['Debug'] = false;
		unset($_SESSION['Debug']);
	}
?>