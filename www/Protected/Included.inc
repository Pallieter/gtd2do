<?php
	/*
	 * Common Law Copyright 2008 eBrain (c) All rights reserved.
	 *
	 * This is unpublished proprietary code of Pallieter Koopmans.
	 * Your access to it does not give you permission to use it.
	 *
	 */

	// Contains all functionality common to all interactive pages (includes active code & functions).
	require_once(realpath(dirname(__FILE__).'/Included_Raw.inc'));

	// eBrain MailTo Class:
	require_once(realpath(dirname(__FILE__).'/MailTo.inc'));

	// Start output buffering with gzip content encoding:
	ob_start('ob_gzhandler');

	if (empty($_COOKIE['FirstVisit']))
	{
		$_COOKIE['FirstVisit'] = date('c');
	}
?>