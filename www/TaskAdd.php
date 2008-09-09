<?php

/* Tasks
	ID
	UserID
	ContextID
	ProjectID
 	Name
 	Notes
 	DateDue
 	DateCreated
 	Sorting
 	Status
*/
 	require_once(realpath('Protected/Included_Raw.inc'));
	if (!empty($_SESSION['UserID']) && !empty($_POST))
	{
		foreach ($_POST as $Key => $Value)
		{
	 		if (!empty($Value))
	 		{
		 		$$Key = mysql_real_escape_string($Value);
		 	}
	 	}
	 	// Insert the task into the DB:
		$TaskStr = 'INSERT INTO '.$DataBase['TablePrefix'].'Tasks SET UserID='.$_SESSION['UserID'].', ';
		if (!empty($Context) && is_numeric($Context))
		{
			$TaskStr .= "ContextID='".$Context."', ";
		}
		elseif (!empty($Context) && !is_numeric($Context))
		{
			// Check that the context does not exist in the database:
			$CheckContextStr = 'SELECT ID FROM '.$DataBase['TablePrefix'].'Contexts WHERE UserID='.$_SESSION['UserID'].' AND ';
			$CheckContextStr .= "Name='".$Context."' LIMIT 1 ";
			$CheckContextRes = mysql_query($CheckContextStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$CheckContextStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
			if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$CheckContextStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
			//If the context does not exist add it
			$CheckContextRow = mysql_fetch_assoc($CheckContextRes);
			if (!empty($CheckContextRow['ID']))
			{
				$TaskStr .= "ContextID='".$CheckContextRow['ID']."', ";
			}
			else
			{
				
				// Insert this new Context into the DB:
				/*
					ID
					UserID
					Name
					DateCreated
					Sorting
					Status
				*/
				$ContextStr = 'INSERT INTO '.$DataBase['TablePrefix'].'Contexts SET UserID='.$_SESSION['UserID'].', ';
				$ContextStr .= "Name='".$Context."', ";
				$ContextStr .= ' DateCreated = NOW()';
				$ContextRes = mysql_query($ContextStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$ContextStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
				if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$ContextStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
				$Success = ""; // 2Do: add the jQuery stuff to reload the dropdown (make the dropdown a seperate PHP file that outputs the options, this so we can reload the SELECT via AJAX?)
				$TaskStr .= "ContextID='".mysql_insert_id()."', ";
			}
		}
		if (!empty($Project) && is_numeric($Project))
		{
			$TaskStr .= "ProjectID='".$Project."', ";
		}
		elseif (!empty($Project) && !is_numeric($Project))
		{
			
			//check that the project does not exist in the database
			$CheckProjectStr = 'SELECT ID FROM '.$DataBase['TablePrefix'].'Projects WHERE UserID='.$_SESSION['UserID'].' AND ';
			$CheckProjectStr .= "Name='".$Project."' LIMIT 1 ";
			$CheckProjectRes = mysql_query($CheckProjectStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$CheckProjectStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
			if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$CheckProjectStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
			//If the context does not exist add it
			$CheckProjectRow = mysql_fetch_assoc($CheckProjectRes);
			if (mysql_num_rows($CheckProjectRes) == 0)
			{
				// Insert this new project into the DB:
				/*
					ID
					UserID
					Name
					Postponed
					DateCreated
					Sorting
					Status
				*/
				$ProjectStr = 'INSERT INTO '.$DataBase['TablePrefix'].'Projects SET UserID='.$_SESSION['UserID'].', ';
				$ProjectStr .= "Name='".$Project."', ";
				$ProjectStr .= ' DateCreated = NOW()';
				$ProjectRes = mysql_query($ProjectStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$ProjectStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
				if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$ProjectStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
				$Success = ""; // 2Do: add the jQuery stuff to reload the dropdown (make the dropdown a seperate PHP file that outputs the options, this so we can reload the SELECT via AJAX?)
				$TaskStr .= "ProjectID='".mysql_insert_id()."', ";
			}
			else
			{
				$TaskStr .= "ProjectID='".$CheckProjectRow['ID']."', ";
			}
		}
		if (!empty($Name))
		{
			$TaskStr .= "Name='".$Name."', ";
		}
		if (!empty($_POST['Notes']))
		{
			$TaskStr .= "Notes='".mysql_real_escape_string($_POST['Notes'])."', ";
		}
		if (!empty($Duration))
		{
			$TaskStr .= "Duration='".$Duration."', ";
		}
		//switch for status
		switch($_POST['Status'])
		{
			case 1: $TaskStr .= "Status='1', ";
			   break;
			case 2: $TaskStr .= "Status='2', ";
			   break;
			default: $TaskStr .= "Status='0', ";
		}
		if (!empty($DateDue) && ($TimeStampDue = strtotime($DateDue)) !== false)
		{
			$TaskStr .= "DateDue='".date('Y-m-d H:i:s', $TimeStampDue)."', ";
		}
		$TaskStr .= ' DateCreated = NOW()';
		$TaskRes = mysql_query($TaskStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$TaskStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
		if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$TaskStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
		if ($TaskRes)
		{
			echo "$('#Name,#Notes').val('');";
			echo "$('#Name').focus();";
			echo "$('#ResponseBox').html('Task submitted.').fadeIn('slow').fadeTo(5000, 1).fadeOut('99999')";
		}
		else
		{
			echo 'ERROR: '.$TaskStr;
		}
	}
	else
	{
		echo 'ERROR: no session';
	}
?>