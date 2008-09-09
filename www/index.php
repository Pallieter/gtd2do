<?php
	/*
	 * Common Law Copyright 2008 eBrain (c) All rights reserved.
	 *
	 * This is unpublished proprietary code of Pallieter Koopmans.
	 * Your access to it does not give you permission to use it.
	 *
	 */

/* 2Do:
	- RSS feeds for: tasks
	- Export in XML
	- create nice icons, so it is visually clear what is what
	- Weekly Revieuw:
		Gather loose papers
		Process all notes
		Check voice mail
		Check Inbox
		Empty your head
		Completed Activities
		Missed Calendar Items
		Upcoming Calendar
		Review Waiting Ons
		Review Next Actions
		See Projects w/o activity
		Review Lists
		Prioritize Goals
		Balance Project List
*/
	$Page = (empty($_GET['Page']) ? '' : trim(strip_tags(substr($_GET['Page'], 0, 64)))); // XSS prevention.
	$Action = (empty($_GET['Action']) ? '' : trim(strip_tags(substr($_GET['Action'], 0, 64)))); // XSS prevention.
	require_once(realpath('Protected/Included_Raw.inc'));
	header('Content-Type: text/html; charset=utf-8'); // This is a duplicate for the meta-tag (and officially both are not needed, as the XHTML DocType already requires UTF-8 to be used), but many (older) browsers still need it.
	// Print the XML-line so PHP does not trip in case the short_open_tag is true:
	echo '<?'.'xml version="1.0" encoding="UTF-8"?>'."\n";
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Pallieter.org ~ A simple GTD-based task manager (using PHP 5+, MySQL 4+ and JQuery as the AJAX framework).</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="author" content="eBrain ~ Innovative Internet Ideas, http://www.eBrain.nl/" />
		<meta name="copyright" content="Common Law Copyright 2008 Pallieter Koopmans &copy; All rights reserved." />
		<meta name="description" content="A simple GTD-based task manager (using PHP 5+, MySQL 4+ and JQuery as the AJAX framework) with a 100% non-javascript fallback." />
		<meta name="application-name" content="GTD2Do"/>
		<meta name="application-url" content="<?php echo $DomainName; ?>" />
		<meta name="keywords" content="eBrain, Pallieter, Koopmans, GTD, 2Do, tasks, todo, next, action, actions, php, mysql, ajax, jquery, backup, organizing, organize, planning, review, sorting, daily, weekly, monthly, quarterly, yearly, duration, context, time, management, manage, agenda, tabs, sortable, lists, xhtml, css, database, backup, rss, xml, standard, method, methodology, standards, compliant, online, internet, mobile, opera, browser, calendar" />
		<link rel="shortcut icon" href="<?php echo $FullSiteURL; ?>Images/Favicon.ico" />
		<link type="text/css" rel="stylesheet" href="<?php echo $FullSiteURL; ?>GTD2Do.css" />
		
		<!-- calendar stylesheet -->
		<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css" title="win2k-cold-1" />

		<!-- main calendar program -->
		<script type="text/javascript" src="jscalendar/calendar.js"></script>

		<!-- language for the calendar -->
		<script type="text/javascript" src="jscalendar/lang/calendar-en.js"></script>

		<!-- the following script defines the Calendar.setup helper function, which makes adding a calendar a matter of 1 or 2 lines of code. -->
		<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>
		
		<style type="text/css">
<?php
			if (!empty($_SESSION['UserID']) && is_numeric($_SESSION['UserID']))
			{
				echo '#Login { display: none; }';
			}
			else
			{
				echo '#Logout { display: none; }';
			}
?>
		</style>
		<script type="text/javascript" src="<?php echo $FullSiteURL; ?>Scripts/jquery-1.2.6.pack.js"></script>

		<script type="text/javascript" src="<?php echo $FullSiteURL; ?>Scripts/jquery-ui-personalized-1.5.min.js"></script>

		<script type="text/javascript" src="<?php echo $FullSiteURL; ?>Scripts/interface-1.2.js"></script>
		<script type="text/javascript" src="<?php echo $FullSiteURL; ?>Scripts/inestedsortable.js"></script>
		
		<script type="text/javascript">
			window.defaultStatus = '   Pallieter.org/GTD2Do';

			$(document).ready(function()
			{
				$('#Menu > ul').tabs();
<?php
	if (!empty($Page) && $Page == 'Login')
	{
		echo "$('#Menu > ul').tabs('select', 0);";
	}
	elseif (!empty($Page) && $Page == 'Edit' && !empty($Action))
	{
		echo "$('#Menu > ul').tabs('url', 1, 'Edit.php?ID=".$Action."&Tab=2'); $('#Menu > ul').tabs('select', 1);";
	}
	// 2Do: once the jquery {selected: 5} bug gets fixed, we should put the following code active again:
	//elseif (!empty($Page))
	//{
	//	echo "$('#Menu > ul').tabs('select', 5);";
	//}
?>
			});

		</script>
	</head>
	<body>
		<div style="float: right;">
<?php
	if (empty($_SESSION['UserID']))
	{
		echo '<a href="http://www.gravatar.com/" title="Create your own Globally Recognized Avatar." onclick="w=window.open(this.href, \'GRAvatar\', \'toolbar=no,scrollbars=no,resizable=yes,width=800,height=600\'); w.focus(); return false;"><img src="Images/GRAvatar.gif" id="GRAvatar" alt="GRAvatar" /></a>';
	}
	else
	{
		$UserStr = 'SELECT UID FROM '.$DataBase['TablePrefix'].'Users WHERE ID='.$_SESSION['UserID'].' LIMIT 1';
		$UserRes = mysql_query($UserStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$UserStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
		//if ($Debug) { echo 'MySQL Query: '.$UserStr.'<br />File: '.__FILE__.' on line: '.(__LINE__)."<br />\n"; }
		$Email = mysql_result($UserRes, 0);
		if (!empty($Email))
		{
			echo '<img src="http://www.gravatar.com/avatar/'.md5(strtolower($Email)).'.jpg?d=wavatar&amp;s=80" id="GRAvatar" alt="GRAvatar" />';
		}
	}
?>
		</div>
		<div style="height: 55px; width: 500px;"><span id="ResponseBox">Logo &amp; name of this website...</span></div>
		<div id="Menu" class="flora">
			<ul>
				<li><a href="Content.php<?php if (!empty($Page)) { echo '?Page='.$Page; }; if (!empty($Page) && !empty($Action)) { echo '&amp;Action='.$Action; } ?>" title="Views" id="Content"><span>About</span></a></li>
				<li><a href="Task.php" title="Views"><span>2Do</span></a></li>
				<li><a href="Tasks.php" title="Views"><span>Tasks</span></a></li>
				<li><a href="Contexts.php" title="Views"><span>Contexts</span></a></li>
				<li><a href="Projects.php" title="Views"><span>Projects</span></a></li>
				<li><a href="Search.php" title="Views"><span>Search</span></a></li>
				<li><a href="Settings.php" title="Views"><span>Settings</span></a></li>
				<li id="Login"><a href="Login.php"><span>Login</span></a></li>
				<li id="Logout"><a href="Logout.php"><span>Logout</span></a></li>
<?php
				if ($Debug)
				{
					echo '<li><a href="Debug.php" title="Views"><span>Debug</span></a></li>';
				}
?>
			</ul>
		</div>
		<div id="Views"></div>
		<p class="CopyRight">Common Law Copyright <?php echo date('Y'); ?> <a href="http://www.eBrain.nl/" title="eBrain ~ Innovative Internet Ideas" onmouseover="window.status='   eBrain ~ Innovative Internet Ideas'; return true" class="CopyRight">eBrain</a> &copy; All rights reserved.</p>
	</body>
</html>