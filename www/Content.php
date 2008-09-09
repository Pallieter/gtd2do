<?php
	$Page = (empty($_GET['Page']) ? '' : trim(strip_tags(substr($_GET['Page'], 0, 64)))); // XSS prevention.
	$Action = (empty($_GET['Action']) ? '' : trim(strip_tags(substr($_GET['Action'], 0, 64)))); // XSS prevention.
	require_once(realpath('Protected/Included_Raw.inc'));

	// 2Do: create the non-AJAX version:
/*
One more thing. If you want, you don't even have to change the URL for
doing the ajax version. jQuery sets the "X-Requested-With" request
header to "XmlHttpRequest" for ajax requests, so you could just check
for that on the server and return JSON if you find it and your regular
HTML if you don't.
*/


	if (!empty($Page) && file_exists(realpath($HomeDir.'Protected/Modules/'.$Page.'.inc')))
	{
		require(realpath($HomeDir.'Protected/Modules/'.$Page.'.inc'));
	}
	elseif (!empty($Page))
	{
		echo getWord($Page);
	}
	else
	{
		echo getWord('About');
	}
	echo "\n";

?>