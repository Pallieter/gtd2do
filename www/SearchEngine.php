<?php
	require_once(realpath('Protected/Included.inc'));
	$stringAux = '<ul>';

	if (!empty($_POST))
	{
		foreach ($_POST as $fieldname => $value)
		{
   			$_SESSION['Debug'][] = "\$" . $fieldname . "='" . $value . "';";
		} 
		// Search string (determine which primary sorting we have):
		if ($_POST['Sorting_Selector1']=='Project')
		{
			$SearchStr = 'SELECT *, P.Name AS ListingName, P.ID as IDProject FROM '.$DataBase['TablePrefix'].'Projects AS P JOIN '.$DataBase['TablePrefix'].'Tasks AS T ON T.ProjectID = P.ID WHERE T.UserID='.$_SESSION['UserID'].' ';
			$Table = 'T.';
			$OrderBy = 'P.Name, '.$Table.$_POST['Sorting_Selector2'];
		}
		elseif ($_POST['Sorting_Selector1']=='Context')
		{
			$SearchStr = 'SELECT *, C.Name AS ListingName, C.ID as IDContext FROM '.$DataBase['TablePrefix'].'Contexts AS C JOIN '.$DataBase['TablePrefix'].'Tasks AS T ON T.ContextID = C.ID WHERE T.UserID='.$_SESSION['UserID'].' ';
			$Table = 'T.';
			$OrderBy = 'C.Name, '.$Table.$_POST['Sorting_Selector2'];
		}
		else
		{
			$SearchStr = 'SELECT * FROM '.$DataBase['TablePrefix'].'Tasks WHERE UserID='.$_SESSION['UserID'].' ';
			$Table = '';
			$OrderBy = $_POST['Sorting_Selector1'].', '.$_POST['Sorting_Selector2'];
		}
		if (!empty($_POST['Context']))
		{
			$ContextStr = 'SELECT ID FROM '.$DataBase['TablePrefix'].'Contexts WHERE UserID='.$_SESSION['UserID'].' AND Name="'.$_POST['Context'].'" LIMIT 1';
			$ContextRes = mysql_query($ContextStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$ContextStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
			if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$ContextStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
			while ($ContextRow = mysql_fetch_assoc($ContextRes))
			{
				$SearchStr .= 'AND ContextID='.$ContextRow['ID'].' '; 
			}
		}
		if (!empty($_POST['Project_Selector']))
		{
			$ProjectStr = 'SELECT ID, Name FROM '.$DataBase['TablePrefix'].'Projects WHERE UserID='.$_SESSION['UserID'].' AND Name="'.$_POST['Project_Selector'].'" LIMIT 1';
			$ProjectRes = mysql_query($ProjectStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$ProjectStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
			if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$ProjectStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
			while ($ProjectRow = mysql_fetch_assoc($ProjectRes))
			{
				$SearchStr .= 'AND ProjectID='.$ProjectRow['ID']. ' '; 
			}
		}
		if (!empty($_POST['Name_Selector']))
		{
			$SearchStr .= 'AND '.$Table.'Name="'.$_POST['Name_Selector']. '" '; 
		}
		if (!empty($_POST['DateDueSearch']))
		{
			switch ($_POST['DateOpt'])
			{
				case 0: $SearchStr .= 'AND '.$Table.'DateDue="'.$_POST['DateDue']. '" '; 
				break;
				case 1: $SearchStr .= 'AND '.$Table.'DateDue<"'.$_POST['DateDue']. '" '; 
				break;
				case 2: $SearchStr .= 'AND '.$Table.'DateDue>"'.$_POST['DateDue']. '" '; 
				break;
			}
		}
		if (!empty($_POST['Status_Selector']))
		{
			switch ($_POST['Status_Selector'])
			{
				// completed:
				case 1: $SearchStr .= 'AND ('.$Table.'Status & 1) = 1 '; //if ((Status&2)=2)
				break;
				// working on:
				case 2: $SearchStr .= 'AND ('.$Table.'Status & 2) = 2 '; //if ((Status&1)=1)
				break;
				// not started:
				case 3: $SearchStr .= 'AND ('.$Table.'Status & 1) != 1 AND ('.$Table.'Status & 2) != 2 '; //((Status&1)!=1 && (Status&2)!=2)
				break;
				// open tasks:
				case 4: $SearchStr .= 'AND ('.$Table.'Status & 1) != 1 ';
			}
		}
		if (!empty($_POST['PlainText']))
		{
			$SearchStr .= 'AND ('.$Table.'Notes LIKE "%'.$_POST['PlainText']. '%" OR '.$Table.'Name LIKE "%'.$_POST['PlainText'].'%") '; 
		}		
		$SearchStr .= 'ORDER BY '.$OrderBy;
		$SearchRes = mysql_query($SearchStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$SearchStr.' <br /> File: '.__FILE__.' on line: '.(__LINE__ - 1));
		if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$SearchStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
		echo "$('#ResponseBox').html('Search Completed.').fadeIn('slow').fadeTo(5000, 1).fadeOut('99999');";
		echo "$('#SearchResults').css('display', 'inline');";
		if (mysql_num_rows($SearchRes) == 0) 
		{
    			echo "$('#Results').html('No results found').fadeIn('fast');";
		}
		else
		{
			$ListingID = 0;
			// Collect the output:
			while ($SearchRow = mysql_fetch_assoc($SearchRes))
			{
				$_SESSION['Debug'][] = print_r($SearchRow,true);
					if ($_POST['Sorting_Selector1']=='Project' && $ListingID != $SearchRow['ProjectID'])
					{
						$ListingID = $SearchRow['ProjectID'];
						$stringAux .= '<li>'.$SearchRow['ListingName'].'</li>';
					}
					elseif ($_POST['Sorting_Selector1']=='Context' && $ListingID != $SearchRow['ContextID'])
					{
						$ListingID = $SearchRow['ContextID'];
						$stringAux .= '</li><li>'.$SearchRow['ListingName'];
					}
					$stringAux .= '<ul id="Task_'.$SearchRow['ID'].'">';
					$stringAux .= '<li>Task '.$SearchRow['ID'].':</li>';
					$stringAux .= '<a href="#" onclick="$(\\\'#Menu > ul\\\').tabs(\\\'url\\\',1, \\\'Edit.php?ID='.$SearchRow['ID'].'&amp;Tab=4 \\\'); $(\\\'#Menu > ul\\\').tabs(\\\'select\\\', 1);">';
					$stringAux .= '<img ALT="Edit" HEIGHT=25 WIDTH=25 src="images/pencil-icon.png"></a>';
					$stringAux .= '<a href="#" id="done" onclick="MarkAsDone('.$SearchRow['ID'].');">';
					$stringAux .= '<img ALT="Mark as Done" TITLE="Mark as Done" HEIGHT=20 WIDTH=25 src="images/tick.png"></A>';
					$stringAux .= '<ul> ';
					$stringAux .= '<li>Task name:'.$SearchRow['Name'].'</li>';
					//get the project name
					$ProjectStr = 'SELECT Name FROM '.$DataBase['TablePrefix'].'Projects WHERE UserID='.$_SESSION['UserID'].' AND ID="'.$SearchRow['ProjectID'].'" LIMIT 1';
					$ProjectRes = mysql_query($ProjectStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$ProjectStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
					if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$ProjectStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
					$ProjectRow = mysql_fetch_assoc($ProjectRes);
					//----------------------------------------------------//
					$stringAux .= '<li>Project:'.$ProjectRow['Name'].'</li>';
					//get the status name
					switch ($SearchRow['Status'])
					{
						case 1: $stringAux .= '<li>Status:Completed</li>'; //completed
						break;
						case 2: $stringAux .= '<li>Status:Working on </li>'; //working on
						break;
						case 0: $stringAux .= '<li>Status:Not Started</li>'; //not started
						break;
					}
					$stringAux .= '<li>Date Due:'.$SearchRow['DateDue'].'</li>';
					$stringAux .= '</ul>';
					$stringAux .= '</ul>';
			}
			$stringAux .= '</ul>';
			// Send collected output to the browser:
			echo "$('#Results').html('". $stringAux."').fadeIn('fast');";
			//$_SESSION['Debug'][] = "$('#Results').html('". $stringAux."').fadeIn('fast');";
		}
	}
	else
	{
		echo "$('#ResponseBox').html('Not a valid search request.').fadeIn('slow');";
	}
?>