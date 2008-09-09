<?php
 	require_once(realpath('Protected/Included_Raw.inc'));
	if (!empty($_SESSION['UserID']) && !empty($_POST['task']) && is_numeric($_POST['task']))
	{
	 	// UPDATE the task into the DB:
		$TaskStr = 'UPDATE '.$DataBase['TablePrefix'].'Tasks SET Status = 1 WHERE ID='.$_POST['task'].' AND UserID='.$_SESSION['UserID'];
		$TaskRes = mysql_query($TaskStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$TaskStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
		if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$TaskStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
		if ($TaskRes)
		{
//			echo "$('#Name,#Notes').val('');";
//			echo "$('#Name').focus();";
			echo "$('#ResponseBox').html('Task ".$_POST['task']." was marked as done.').fadeIn('slow').fadeTo(5000, 1).fadeOut('99999');";
			//echo "$('#Menu > ul').tabs('url',2 , 'Tasks.php');";
			echo "$('#Task_".$_POST['task']."').remove();";
//			echo "$('#Menu > ul').tabs('select', ".$_POST['TabID'].");";
		}
		else
		{
			$_SESSION['Debug'][] = 'ERROR: '.$TaskStr;
		}
	}
	else
	{
		echo "$('#ResponseBox').html('ERROR: no session: ".$_POST['TaskID']."').fadeIn('slow').fadeTo(5000, 1).fadeOut('99999')";
	}
?>