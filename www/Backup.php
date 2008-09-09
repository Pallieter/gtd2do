<?php
	require_once(realpath('Protected/Included_Raw.inc'));
	
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

	// Get everything from DB and store in $Message:
	//Task without context
	$Message = 'Backup: '.$EoL;
	$Message .= $EoL;
	$Message .= 'No Project: '.$EoL;
	$TasksStr = 'SELECT * FROM '.$DataBase['TablePrefix'].'Tasks WHERE ProjectID=0 && UserID='.$_SESSION['UserID'].' && (Status & 1) != 1 ORDER BY ContextID, Sorting, Name';
	$TasksRes = mysql_query($TasksStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$TasksStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
	if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$TasksStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
	while ($TasksRow = mysql_fetch_assoc($TasksRes))
	{
		$Message .= "\t".$TasksRow['ID'].': '.htmlspecialchars($TasksRow['Name']).' '.$EoL;
		$Message .= "\t\t".'Due for: '.$TasksRow['DateDue'].''.$EoL;
		$Message .= "\t\t".'Current Status:';
		if ($TasksRow['Status'] == 1)
		{
			$Message .= 'Completed'.$EoL;
		}
		else
		{
			$Message .= 'Not Completed'.$EoL;
		}
	}
	$Message .= $EoL;
	$ProjectsStr = 'SELECT ID, Name FROM '.$DataBase['TablePrefix'].'Projects WHERE UserID='.$_SESSION['UserID'].' && (Status & 1) != 1 ORDER BY Sorting, Name';
	$ProjectsRes = mysql_query($ProjectsStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$ProjectsStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
	if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$ProjectsStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
	while ($ProjectsRow = mysql_fetch_assoc($ProjectsRes))
	{
		$Message.= $ProjectsRow['ID'].':'.$ProjectsRow['Name'].' '.$EoL;
		$TasksStr = 'SELECT * FROM '.$DataBase['TablePrefix'].'Tasks WHERE ProjectID='.$ProjectsRow['ID'].' && UserID='.$_SESSION['UserID'].' && (Status & 1) != 1 ORDER BY ProjectID, Sorting, Name';
		$TasksRes = mysql_query($TasksStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$TasksStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
		if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$TasksStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
		while ($TasksRow = mysql_fetch_assoc($TasksRes))
		{
			$Message .= "\t".$TasksRow['ID'].': '.htmlspecialchars($TasksRow['Name']).' '.$EoL;
			$Message .= "\t\t".'Due for: '.$TasksRow['DateDue'].''.$EoL;
			$Message .= "\t\t".'Current Status:';
			if ($TasksRow['Status'] == 1)
			{
				$Message .= 'Completed'.$EoL;
			}
			else
			{
				$Message .= 'Not Completed'.$EoL;
			}
		}
		$Message .= $EoL; 
	}
	$Message .= '';
	
	// Send $Message to the user:
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
	//$Headers .= 'From: "Backup2Email" <PK2Do@'.$DomainName.'>'.$EoL;
	//$MailResult = mail('Pallieter@gmail.com', 'PK2Do Backup2Email from '.date('Y-m-d H:i:s'), $Message, $Headers);
	$email = 'dasf136@hotmail.com';
	$subject = 'PK2Do Backup2Email from '.date('Y-m-d H:i:s');
	$MailResult = mail($email, $subject, $Message, $Headers);
	// Give feedback to the user:
	if (empty($MailResult))
	{
		echo "$('#ResponseBox').html('The backup was NOT send.').fadeIn('slow').fadeTo(5000, 1).fadeOut('99999');";
	}
	else
	{
		echo "$('#ResponseBox').html('The backup was send.').fadeIn('slow').fadeTo(5000, 1).fadeTo(5000, 1).fadeOut('99999');";
	}
?>