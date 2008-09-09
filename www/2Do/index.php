<?php
	/*
	 * Common Law Copyright 2007 eBrain © All rights reserved.
	 *
	 * This is unpublished proprietary code of Pallieter Koopmans.
	 * Your access to it does not give you permission to use it.
	 *
	 *	2Do:
	 *      	- lock the SAVE functionality based on browser identification or a lock code (stored in a hidden var + on the server in  a lock file)?
	 *      	- delete files?
	 *      	- autosave if unchanged functionality?
	 *	     	- scandir (PHP 5 only) to sort the files a-z
	 *		- attache the index.php (this file) to the email
	 */

	/*if (empty($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER'] <> 'Pallieter' || empty($_SERVER['PHP_AUTH_PW']) || crypt($_SERVER['PHP_AUTH_PW'], 'eBrain') <> 'eBeqgDCJfEYBI')
	{
		header('WWW-Authenticate: Basic realm="PK2Do"');
		header('HTTP/1.0 401 Unauthorized');
		exit('PK2Do requires authentication.');
	}*/

	$Error = '';
	$Backup = false;
	$Archived = 'Archived on: '; // String command used to send a tab to the archive.
	// Set the error reporting level:
	error_reporting(E_ALL);
	ini_set('display_errors', true);
	// Check the local OS for the end-of-line character to use (Windows, Macintosh or Linux/other):
	if (strtoupper(substr(PHP_OS, 0, 3) == 'WIN'))
	{
		$EoL = "\r\n";
	}
	elseif (strtoupper(substr(PHP_OS, 0, 3) == 'MAC'))
	{
		$EoL = "\r";
	}
	else
	{
		$EoL = "\n";
	}

	$DirPath = realpath(dirname(__FILE__).'/Data').'/';
	if (!empty($_POST))
	{
		foreach ($_POST['FileNames'] AS $Key => $FileName)
		{
			if (!empty($_POST['File'][$Key]))
			{
				// PHP 5:
				if (!file_put_contents($DirPath.$FileName, $_POST['File'][$Key]))
				{
					echo '<p>ERROR: '.$Key.' => '.$FileName.' => '.$_POST['File'][$Key].'</p>';
				}
				// Rewrite the file in PHP < 5:
				//if (is_writable($DirPath.$FileName))
				//{
				//	if (!$Handle = fopen($DirPath.$FileName, 'w'))
				//	{
				//		$Error .= '<p>Cannot open file: '.$DirPath.$FileName.'</p>';
				//	}
				//	if (fwrite($Handle, $_POST['File'][$Key]) === FALSE)
				//	{
				//		$Error .= '<p>Cannot write to file: '.$DirPath.$FileName.'</p>';
				//	}
				//	fclose($Handle);
				//}
				if (!empty($_POST['NewFile']) && $_POST['NewFile'] == 'Backup')
				{
					$Backup .= $EoL.'*** FILE: '.$FileName.$EoL.$EoL.$_POST['File'][$Key].$EoL;
				}
			}
			if (!empty($_POST['File'][$Key]) && substr($_POST['File'][$Key], 0, strlen($Archived)) == $Archived)
			{
				if (!rename($DirPath.$FileName, $DirPath.$FileName.'.Archived'))
				{
					$Error .= '<p>Cannot rename file: '.$DirPath.$FileName.'</p>';
				}
			}
		}
		if (!empty($_POST['NewFile']))
		{
			if ($_POST['NewFile'] == 'Backup')
			{
				if (empty($_SERVER['SERVER_NAME']))
				{
					$DomainName = 'Pallieter.org';
				}
				else
				{
					$DomainName = str_replace('www.', '', strtolower($_SERVER['SERVER_NAME']));
				}
				$Headers = 'MIME-Version: 1.0'.$EoL;
				$Headers .= 'Content-type: text/plain'.$EoL;
				$Headers .= 'X-Mailer: PHP '.phpversion().$EoL; // This helps avoid the spam-filters.
				$Headers .= 'Message-ID: <'.time().'-'.rand(1, 999).'@'.$DomainName.'>'.$EoL; // This helps avoid the spam-filters.
				$Headers .= 'Reply-To: PostMaster@'.$DomainName.$EoL; // This is added to help prevent this message being tagged as spam.
				$Headers .= 'Return-Path: PostMaster@'.$DomainName.$EoL; // This is added to help prevent this message being tagged as spam.
				$Headers .= 'From: "Backup2Email" <PK2Do@'.$DomainName.'>'.$EoL;
				$MailResult = mail('Pallieter@gmail.com', 'PK2Do Backup2Email from '.date('Y-m-d H:i:s'), $Backup, $Headers);
				if (empty($MailResult))
				{
					$Error .= '<p>Backup2Email failed: '.$MailResult.'</p>';
				}
			}
			else
			{
				if (!touch($DirPath.$_POST['NewFile'].'.txt'))
				{
					$Error .= '<p>Cannot create file: '.$DirPath.$_POST['NewFile'].'.txt</p>';
				}
			}
		}
	}

	header('Cache-Control: no-cache');
	// Print the XML-line so PHP does not trip in case the short_open_tag is true:
	echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">'."\n";
	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="EN">'."\n";
?>
	<head>
		<title> &nbsp; PK2Do &nbsp; </title>
		<meta name="author" content="Pallieter Koopmans, http://www.eBrain.nl/" />
		<script type="text/javascript">
			<!-- //
				function insertTab(o, e)
				{
					var kC = e.keyCode ? e.keyCode : e.charCode ? e.charCode : e.which;
					if (kC == 9 && !e.shiftKey && !e.ctrlKey && !e.altKey)
					{
						var oS = o.scrollTop;
						if (o.setSelectionRange)
						{
							var sS = o.selectionStart;
							var sE = o.selectionEnd;
							o.value = o.value.substring(0, sS) + "\t" + o.value.substr(sE);
							o.setSelectionRange(sS + 1, sS + 1);
							o.focus();
						}
						else if (o.createTextRange)
						{
							document.selection.createRange().text = "\t";
							e.returnValue = false;
						}
						else
						{
							alert('90267450876');
						}
						o.scrollTop = oS;
						if (e.preventDefault)
						{
							e.preventDefault();
						}
						return false;
					}
					return true;
				}

				function selectMenu(Number)
				{
					var mi = document.getElementsByTagName('li');
					for (var i = 0; i < mi.length; i++) { mi[i].className = 'false'; };
					document.getElementById('Menu'+Number).className='Active';
					var ta = document.getElementsByTagName('textarea');
					for (var i = 0; i < ta.length; i++) { ta[i].style.display = 'none'; };
					document.getElementById('File'+Number).style.display = 'inline';
					document.getElementById('ActiveID').value = Number;
					return false;
				}
				function dateAsNumber(inDate,inWhat)
				{
					var what = "", yearBit = 0, monthBit = 0
					if (typeof(inWhat) == "undefined" || inWhat.toString() == "" || inWhat.toString() == null) inWhat = ""
					what = inWhat.toString().toUpperCase()
					if (what != "M" && what != "D") // we want yyyy bit
					yearBit = inDate.getFullYear() * Math.pow(10,13);
					if (what != "D") // we want month bit
					monthBit = inDate.getMonth() * Math.pow(10,11);
					return yearBit +
					monthBit +
					inDate.getDate() * Math.pow(10,09) +
					inDate.getHours() * Math.pow(10,07) +
					inDate.getMinutes() * Math.pow(10,05) +
					inDate.getSeconds() * Math.pow(10,03) +
					inDate.getMilliseconds()
				}
				function TimeSince()
				{
					var toDate = new Date();
					var tempDate = new Date();
					if (fromDate.getTime() > toDate.getTime())
					{
						tempDate = new Date(fromDate);
						fromDate = new Date(toDate);
						toDate = new Date(tempDate);
					}
					var totMonths = 12*toDate.getFullYear() + toDate.getMonth() + -12*fromDate.getFullYear() - fromDate.getMonth()
					var years = Math.floor(totMonths / 12)
					var months = totMonths - 12*years
					if (dateAsNumber(toDate,"D") < dateAsNumber(fromDate,"D")) months -= 1
					if (months < 0)
					{
						months = 0
						if (years > 0) years -= 1
					}
					var yearsOff = years + fromDate.getFullYear()
					var monthsOff = months + fromDate.getMonth()
					if (monthsOff >= 12)
					{
						monthsOff -= 12
						yearsOff += 1
					}
					var tempDate = new Date(fromDate);
					tempDate.setFullYear(yearsOff);
					tempDate.setMonth(monthsOff);
					while (tempDate.getDate() < fromDate.getDate() && tempDate.getDate() < 9 )
					tempDate.setTime(tempDate.getTime() - 1000*60*60*24); // Feb 29 etc.
					var milliSecs = toDate.getTime() - tempDate.getTime();
					var oneSecond = 1000;
					var oneMinute = 60 * 1000;
					var oneHour = 60 * oneMinute;
					var oneDay = 24 * oneHour;
					var oneWeek = 7 * oneDay;
					var weeks = Math.floor(milliSecs / oneWeek);
					milliSecs -= weeks * oneWeek;
					var days = Math.floor(milliSecs / oneDay);
					milliSecs -= days * oneDay;
					var hours = Math.floor(milliSecs / oneHour);
					milliSecs -= hours * oneHour;
					var minutes = Math.floor(milliSecs / oneMinute);
					milliSecs -= minutes * oneMinute;
					var seconds = Math.floor(milliSecs / oneSecond);
					var timeValue = "";
					if (years) timeValue += years + ((years==1) ? " year " : " years ");
					if (months) timeValue += months + ((months==1) ? " month " : " months ");
					if (weeks) timeValue += weeks + ((weeks==1) ? " week " : " weeks ");
					if (days) timeValue += days + ((days==1) ? " day " : " days ");
					//timeValue += hours + ((hours==1) ? "hour " :" hours ");
					//timeValue += minutes + ((minutes==1) ? " minute " : " minutes ");
					//timeValue += seconds + ((seconds==1) ? " second" : " seconds");
					timeValue += ((hours < 10) ? "0" : "")+hours;
					timeValue += ((minutes < 10) ? ":0" : ":")+minutes;
					if (hours == 0 && minutes == 0)
					{
						// timeValue += ((seconds < 10) ? ":0" : ":")+seconds;
					}
					document.getElementById('ElapsedTime').value = timeValue;
					setTimeout('TimeSince()', 1000);
				}
				var fromDate = new Date();
				window.onload = TimeSince;
			//-->
		</script>
                <style type="text/css">
			body
			{
				font: bold 11px verdana, arial, sans-serif;
			}
			textarea
			{
				display: none;
				width: 100%;
				height: 444px;
				margin-top: 10px;
			}
			input#ElapsedTime
			{
				border: 1px solid #6c6;
			}
			#NewFile
			{
				border: none;
				width: 100px;
				text-align: center;
				padding-right: 15px;
			}
			ul#TabNav
			{
				text-align: center;
				margin: 1em 0 1em 0;
				border-bottom: 1px solid #6c6;
				list-style-type: none;
				padding: 3px 10px 3px 10px;
			}
			ul#TabNav li
			{
				display: inline;
			}
			ul#TabNav li a
			{
				color: #666;
				background-color: #cfc;
				padding: 3px 4px;
				border: 1px solid #6c6;
				border-bottom: none;
				margin-right: 3px;
				text-decoration: none;
			}
			ul#TabNav a:hover
			{
				color: #000;
				background-color: #fff;
				cursor: pointer;
			}
			ul#TabNav li.Active
			{
				color: #000;
				background-color: #fff;
				border-bottom: 1px solid #fff;
			}
			ul#TabNav li.Active a
			{
				background-color: #fff;
				color: #000;
				position: relative;
				top: 1px;
				padding-top: 4px;
			}
		</style>
		<link rel="shortcut icon" href="favicon.ico" />
	</head>
	<body>
		<form name="Form2Do" method="post" action="" accept-charset="utf-8">
<?php
	$Menu = array();
	$Files = array();
	$ActiveID = 0;
	// Loop all files:
	if (is_dir($DirPath))
	{
		if ($DirHandle = opendir($DirPath))
		{
			$i = 0;
			$FileNames = '';
			while (($File = readdir($DirHandle)) !== false)
			{
				// Filter non-files, this file, files ending in .php and files starting with a dot:
				if (is_file($DirPath.$File) && $File <> basename(__FILE__) && strtolower(substr($File, strrpos($File, '.'))) <> '.php' && substr($File, strrpos($File, '.')) <> '.Archived' && substr($File, 0, 1) <> '.')
				{
					$Menu[$i] = substr($File, 0, strrpos($File, '.'));
					// $Files[$i] = '<div id="Container'.$i.'"><textarea id="File'.$i.'" name="File'.$i.'">'.file_get_contents($File).'</textarea></div>';
					$Files[$i] = '<textarea id="File'.$i.'" name="File['.$i.']" onkeydown="insertTab(this, event);" rows="9" cols="99"';
					if (!is_writable($DirPath.$File))
					{
						$Files[$i] .= ' disabled="disabled"';
					}
					if ((!empty($_POST['ActiveID']) && $_POST['ActiveID'] == $i) || (empty($_POST['ActiveID']) && $i == 0))
					{
						$Files[$i] .= ' style="display: inline;" tabindex="1" ';
						$ActiveID = $i;
					}
					$Files[$i] .= '>'.file_get_contents($DirPath.$File).'</textarea>';
					$FileNames .= '<input type="hidden" name="FileNames['.$i.']" value="'.$File.'" />';
					$i++;
				}
			}
			closedir($DirHandle);
		}
		else
		{
			$Error .= '<p>ERROR: openDir</p>';
		}
	}
	else
	{
		$Error .= '<p>ERROR: isDir</p>';
	}

	// echo '<div style="float: left; margin-top: 3px;">PK2Do</div>';
	echo '<div style="float: left; margin-top: 3px;"><img src="Backup.gif" alt="B2E" title="Backup2Email" onclick="document.getElementById(\'NewFile\').value=\'Backup\'; document.Form2Do.submit();" style="width: 15px; height: 15px;" /></div>';

	echo '<div style="float: right; margin-top: 3px;">';
	echo '<input type="hidden" id="ActiveID" name="ActiveID" value="'.$ActiveID.'" />';
	echo '<input tabindex="2" type="text" name="NewFile" id="NewFile" />'; // onfocus="this.className=\'NewFileInput\';" onblur="this.className=\'NewFile\';"
	echo '<input tabindex="3" type="submit" id="ElapsedTime" value="00:00" />'; // :00
	echo '</div>';

	echo "\n".'<ul id="TabNav">';
	asort($Menu);
	foreach ($Menu AS $Number => $Title)
	{
		echo '<li id="Menu'.$Number.'"';
		if ($ActiveID == $Number)
		{
			echo ' class="Active"';
		}
		echo '><a onclick="selectMenu('.$Number.');">'.$Title.'</a></li>';
	}
	echo '</ul><div>'."\n";

	if (!empty($Error))
	{
		echo $Error;
	}

	foreach ($Files AS $Number => $Content)
	{
		echo $Content;
	}
	echo "\n".$FileNames.'</div>';
?>
		</form>
	</body>
</html>