<script type="text/javascript">
jQuery( 
function($) 
    	{
		$('#Contexts').NestedSortable
		(
  			{
    				accept: 'context-page',
    				noNestingClass: 'nochilds',
    				onChange: function(serialized) 
    				{
					$('#list-container-ser')
					.html("Array: "+serialized[0].hash);
					$.ajax(
					{
						url: 'ContextSorter.php',
						type: 'POST',
						data: serialized[0].hash,
						dataType: 'script',
						timeout: 2000,
						error: function(xhr, desc, exceptionobj)
						{
							alert('Failed to submit: ' + xhr.responseText);
						},
						success: function(data, textStatus)
						{
							this;
						}
					});
				},
  			}
		);
    	});
</script>

<script type="text/javascript">

        function MarkAsDone(TaskID)
        {
		$.ajax({
			url: 'MarkDone.php',
			type: 'POST',
			data: "task="+TaskID,
			dataType: 'script',
			timeout: 2000,
			error: function(xhr, desc, exceptionobj)
			{
				alert('Failed to submit: ' + xhr.responseText);
			},
			success: function(data, textStatus)
			{
				this;
				alert('Submitted: '+textStatus);
			}
		});
		return false;
	}

</script>


<?php

	require_once(realpath('Protected/Included_Raw.inc'));

	if (empty($_SESSION['UserID']))
	{
		echo 'Go <a href="?Page=Login">login</a>.';
		echo '<script type="text/javascript">'."$('#Login').css('display', 'inline'); $('#Logout').css('display', 'none'); $('#Menu > ul').tabs('select', 7);</script>";
		$_SESSION['Debug'][] = 'Contexts tab';
	}
	else
	{
		//Get the projects
		$Projects = array();
		$Projects[0] = 'Undefined';
		$ProjectsStr = 'SELECT ID, Name FROM '.$DataBase['TablePrefix'].'Projects ORDER BY ID';
		$ProjectsRes = mysql_query($ProjectsStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$ProjectsStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
		if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$ProjectsStr.'<br />File: '.__FILE__.' on line: '.(__LINE__)."<br />\n"; }
		while ($ProjectsRow = mysql_fetch_assoc($ProjectsRes))
		{
			$Projects[$ProjectsRow['ID']] = $ProjectsRow['Name'];
		}
		echo '<ul id="Contexts">';
		echo '<li class="context-page" id="0">Not in a context:<ul>';
		//List the task without context
		$TasksStr = 'SELECT * FROM '.$DataBase['TablePrefix'].'Tasks WHERE ContextID=0 && UserID='.$_SESSION['UserID'].' && (Status & 1) != 1 ORDER BY Sorting, Name';
		$TasksRes = mysql_query($TasksStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$TasksStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
		if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$TasksStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
		while ($TasksRow = mysql_fetch_assoc($TasksRes))
		{
			echo '<li id="Task_'.$TasksRow['ID'].'" class="context-page nochilds">'.$Projects[$TasksRow['ProjectID']].' = '.$TasksRow['ID'].': '.htmlspecialchars($TasksRow['Name']).'&nbsp;&nbsp';
			echo '<a href="#" onclick="$(\'#Menu > ul\').tabs(\'url\',1, \'Edit.php?ID='.$TasksRow['ID'].'&amp;Tab=3\'); $(\'#Menu > ul\').tabs(\'select\', 1);">';
			echo '<img BORDER=0 ALIGN="middle" ALT="Edit" HEIGHT=25 WIDTH=25 src="images/pencil-icon.png"></A>';
			echo '<img BORDER=0 ALIGN="middle" ALT="Mark as Done" TITLE="Mark as Done" HEIGHT=20 WIDTH=25 src="images/tick.png"></A></li>';
		}
		echo '</ul></li>';
		$ContextsStr = 'SELECT ID, Name FROM '.$DataBase['TablePrefix'].'Contexts WHERE UserID='.$_SESSION['UserID'].' && (Status & 1) != 1 ORDER BY Sorting, Name';
		$ContextsRes = mysql_query($ContextsStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$ContextsStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
		if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$ContextsStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
		while ($ContextsRow = mysql_fetch_assoc($ContextsRes))
		{
			echo '<li class="context-page" id="'.$ContextsRow['ID'].'">'.$ContextsRow['Name'].'<ul>';
			$TasksStr = 'SELECT * FROM '.$DataBase['TablePrefix'].'Tasks WHERE ContextID='.$ContextsRow['ID'].' && UserID='.$_SESSION['UserID'].' && (Status & 1) != 1 ORDER BY Sorting, Name';
			$TasksRes = mysql_query($TasksStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$TasksStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
			if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$TasksStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
			while ($TasksRow = mysql_fetch_assoc($TasksRes))
			{

				echo '<li id="Task_'.$TasksRow['ID'].'" class="context-page nochilds">'.$Projects[$TasksRow['ProjectID']].' = '.$TasksRow['ID'].': '.htmlspecialchars($TasksRow['Name']).'&nbsp;&nbsp';
				echo '<a href="#" onclick="$(\'#Menu > ul\').tabs(\'url\',1, \'Edit.php?ID='.$TasksRow['ID'].'&amp;Tab=3\'); $(\'#Menu > ul\').tabs(\'select\', 1);">';
				echo '<img BORDER=0 ALIGN="middle" ALT="Edit" HEIGHT=25 WIDTH=25 src="images/pencil-icon.png"></A>';
				echo '<img BORDER=0 ALIGN="middle" ALT="Mark as Done" TITLE="Mark as Done" HEIGHT=20 WIDTH=25 src="images/tick.png"></A></li>';
			}
			echo '</ul></li>';
		}
		echo '</ul>';
		echo '<div id="list-container-ser">';
		echo 'Drag the elements to change their position';
	        echo '</div>';
	}

?>
<?php		/*
		$Projects = array();
		$Projects[0] = 'Undefined';
		$ProjectsStr = 'SELECT ID, Name FROM '.$DataBase['TablePrefix'].'Projects ORDER BY ID';
		$ProjectsRes = mysql_query($ProjectsStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$ProjectsStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
		if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$ProjectsStr.'<br />File: '.__FILE__.' on line: '.(__LINE__)."<br />\n"; }
		while ($ProjectsRow = mysql_fetch_assoc($ProjectsRes))
		{
			$Projects[$ProjectsRow['ID']] = $ProjectsRow['Name'];
		}

		echo '<ul>';
		echo '<li>Not in a context:<ul>';
		$TasksStr = 'SELECT * FROM '.$DataBase['TablePrefix'].'Tasks WHERE ContextID=0 && UserID='.$_SESSION['UserID'].' && (Status & 1) != 1 ORDER BY ProjectID, Sorting, Name';
		$TasksRes = mysql_query($TasksStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$TasksStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
		if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$TasksStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
		while ($TasksRow = mysql_fetch_assoc($TasksRes))
		{
			echo '<li>'.$Projects[$TasksRow['ProjectID']].' = '.$TasksRow['ID'].': '.htmlspecialchars($TasksRow['Name']).'</li>';
		}
		echo '</ul></li>';
		$ContextsStr = 'SELECT ID, Name FROM '.$DataBase['TablePrefix'].'Contexts WHERE UserID='.$_SESSION['UserID'].' && (Status & 1) != 1 ORDER BY Sorting, Name';
		$ContextsRes = mysql_query($ContextsStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$ContextsStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
		if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$ContextsStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
		while ($ContextsRow = mysql_fetch_assoc($ContextsRes))
		{
			echo '<li>'.$ContextsRow['Name'].'<ul>';
			$TasksStr = 'SELECT * FROM '.$DataBase['TablePrefix'].'Tasks WHERE ContextID='.$ContextsRow['ID'].' && UserID='.$_SESSION['UserID'].' && (Status & 1) != 1 ORDER BY ProjectID, Sorting, Name';
			$TasksRes = mysql_query($TasksStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$TasksStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
			if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$TasksStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
			while ($TasksRow = mysql_fetch_assoc($TasksRes))
			{
				echo '<li>'.$Projects[$TasksRow['ProjectID']].' = '.$TasksRow['ID'].': '.htmlspecialchars($TasksRow['Name']).'</li>';
			}
			echo '</ul></li>';
		}
		echo '</ul>';
	}
?>*/
?>