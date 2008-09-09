<script type="text/javascript">
jQuery( 
function($) 
    	{
		$('#Projects').NestedSortable
		(
  			{
    				accept: 'project-page',
    				noNestingClass: 'nochilds',
    				onChange: function(serialized) 
    				{
					$('#list-container-ser')
					.html("Array: "+serialized[0].hash);
					$.ajax(
					{
						url: 'TaskSorter.php',
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
	// 2Do: sorting for the projects (duplicate from Projects.php)!

	require_once(realpath('Protected/Included_Raw.inc'));

	if (empty($_SESSION['UserID']))
	{
		echo 'Go <a href="?Page=Login">login</a>.';
		echo '<script type="text/javascript">'."$('#Login').css('display', 'inline'); $('#Logout').css('display', 'none'); $('#Menu > ul').tabs('select', 7);</script>";
		$_SESSION['Debug'][] = 'Projects tab';
	}
	else
	{
		$Contexts = array();
		$Contexts[0] = 'Undefined';
		$ContextsStr = 'SELECT ID, Name FROM '.$DataBase['TablePrefix'].'Contexts ORDER BY ID';
		$ContextsRes = mysql_query($ContextsStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$ContextsStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
		if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$ContextsStr.'<br />File: '.__FILE__.' on line: '.(__LINE__)."<br />\n"; }
		while ($ContextsRow = mysql_fetch_assoc($ContextsRes))
		{
			$Contexts[$ContextsRow['ID']] = $ContextsRow['Name'];
		}
		//var_dump($Contexts);

		echo '<ul id="Projects">';
		echo '<li class="project-page" id="0">Not in a Project:<ul>';
		$TasksStr = 'SELECT * FROM '.$DataBase['TablePrefix'].'Tasks WHERE ProjectID=0 && UserID='.$_SESSION['UserID'].' && (Status & 1) != 1 ORDER BY ContextID, Sorting, Name';
		$TasksRes = mysql_query($TasksStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$TasksStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
		if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$TasksStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
		while ($TasksRow = mysql_fetch_assoc($TasksRes))
		{
			echo '<li id="Task_'.$TasksRow['ID'].'" class="project-page nochilds">'.$Contexts[$TasksRow['ContextID']].' = '.$TasksRow['ID'].': '.htmlspecialchars($TasksRow['Name']).'&nbsp;&nbsp';
			echo '<a href="#" onclick="$(\'#Menu > ul\').tabs(\'url\',1, \'Edit.php?ID='.$TasksRow['ID'].'&amp;Tab=4\'); $(\'#Menu > ul\').tabs(\'select\', 1);">';
			echo '<img BORDER=0 ALIGN="middle" ALT="Edit" HEIGHT=25 WIDTH=25 src="images/pencil-icon.png"></A>';
			echo '<a href="#" id="done" onclick="MarkAsDone('.$TasksRow['ID'].');">';
			echo '<img BORDER=0 ALIGN="middle" ALT="Mark as Done" TITLE="Mark as Done" HEIGHT=20 WIDTH=25 src="images/tick.png"></A></li>';
		}
		echo '</ul></li>';
		$ProjectsStr = 'SELECT ID, Name FROM '.$DataBase['TablePrefix'].'Projects WHERE UserID='.$_SESSION['UserID'].' && (Status & 1) != 1 ORDER BY Sorting, Name';
		$ProjectsRes = mysql_query($ProjectsStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$ProjectsStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
		if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$ProjectsStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
		while ($ProjectsRow = mysql_fetch_assoc($ProjectsRes))
		{
			echo '<li class="project-page" id="'.$ProjectsRow['ID'].'">'.$ProjectsRow['Name'].'<ul>';
			$TasksStr = 'SELECT * FROM '.$DataBase['TablePrefix'].'Tasks WHERE ProjectID='.$ProjectsRow['ID'].' && UserID='.$_SESSION['UserID'].' && (Status & 1) != 1 ORDER BY ProjectID, Sorting, Name';
			$TasksRes = mysql_query($TasksStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$TasksStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
			if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$TasksStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
			while ($TasksRow = mysql_fetch_assoc($TasksRes))
			{
				echo '<li id="Task_'.$TasksRow['ID'].'" class="project-page nochilds">'.$Contexts[$TasksRow['ContextID']].' = '.$TasksRow['ID'].': '.htmlspecialchars($TasksRow['Name']).'&nbsp;&nbsp';
				echo '<a href="#" onclick="$(\'#Menu > ul\').tabs(\'url\',1, \'Edit.php?ID='.$TasksRow['ID'].'&amp;Tab=4\'); $(\'#Menu > ul\').tabs(\'select\', 1);">';
				echo '<img BORDER=0 ALIGN="middle" ALT="Edit" HEIGHT=25 WIDTH=25 src="images/pencil-icon.png"></A>';
				echo '<a href="#" id="done" onclick="MarkAsDone('.$TasksRow['ID'].');">';
				echo '<img BORDER=0 ALIGN="middle" ALT="Mark as Done" TITLE="Mark as Done" HEIGHT=20 WIDTH=25 src="images/tick.png"></A></li>';
			}
			echo '</ul></li>';
		}
		echo '</ul>';
	}
?>