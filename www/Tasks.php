<script type="text/javascript">
jQuery( 
function($) 
    	{
		$('#Projects').NestedSortable
		(
  			{
    				accept: 'page-item1',
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
<script type="text/javascript">

$(document).ready(function()
{
	/*
	//Sortable
	$('#Projects').sortable(
	{
		axis: 'y',
		items: '.Sortable',
		update: function (sorted, ui)
		{
			// $('#Projects').sortable('toArray')
			// $('#Projects').sortable('serialize')
			// Possibly use: http://www.prodevtips.com/2008/01/29/sorting-with-jquery-sortable/ (but that has no AJAX, but uses a POST).
			var dataString = ''; // 2Do: it should not be needed to do this conversion, but I could not get it to work otherwise, the above tries didn't work.
			jQuery.each($('#Projects').sortable('toArray'), function(key, value)
			{
				dataString += value+'='+key+'&';
			});
			$.ajax(
			{
				url: 'TaskSorter.php',
				type: 'POST',
				data: dataString,
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
		}
	});
	
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
	});*/
});

</script>

<?php

	require_once(realpath('Protected/Included_Raw.inc'));
	if (empty($_SESSION['UserID']))
	{
		echo 'Go <a href="?Page=Login">login</a>.';
		echo '<script type="text/javascript">'."$('#Login').css('display', 'inline'); $('#Logout').css('display', 'none'); $('#Menu > ul').tabs('select', 6);</script>";
		$_SESSION['Debug'][] = 'Tasks tab';
		
	}
	else
	{
		echo '<ul id="Projects">';
		echo '<li class="page-item1" id="0">Not in a project:<ul>';
		$TasksStr = 'SELECT * FROM '.$DataBase['TablePrefix'].'Tasks WHERE ProjectID=0 && UserID='.$_SESSION['UserID'].' && (Status & 1) != 1 ORDER BY Sorting, Name';
		$TasksRes = mysql_query($TasksStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$TasksStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
		if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$TasksStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
		while ($TasksRow = mysql_fetch_assoc($TasksRes))
		{
			// echo '<li id="Sort[0]['.$TasksRow['ID'].']" class="page-item1 nochilds">'.$TasksRow['ID'].': '.htmlspecialchars($TasksRow['Name']).'</li>';
			echo '<li id="'.$TasksRow['ID'].'" class="page-item1 nochilds">'.$TasksRow['ID'].': '.htmlspecialchars($TasksRow['Name']).'&nbsp;&nbsp';
			echo '<a href="#" onclick="$(\'#Menu > ul\').tabs(\'url\',1, \'Edit.php?ID='.$TasksRow['ID'].'&amp;Tab=2\'); $(\'#Menu > ul\').tabs(\'select\', 1);">';
			echo '<img BORDER=0 ALIGN="middle" ALT="Edit" HEIGHT=25 WIDTH=25 src="images/pencil-icon.png"></A>&nbsp;&nbsp';
			echo '<a href="#" id="done" onclick="MarkAsDone('.$TasksRow['ID'].');">';
			echo '<img BORDER=0 ALIGN="middle" ALT="Mark as Done" TITLE="Mark as Done" HEIGHT=20 WIDTH=25 src="images/tick.png"></A></li>';
		}
		echo '</ul></li>';
		$ProjectsStr = 'SELECT ID, Name FROM '.$DataBase['TablePrefix'].'Projects WHERE UserID='.$_SESSION['UserID'].' && (Status & 1) != 1 ORDER BY Sorting, Name';
		$ProjectsRes = mysql_query($ProjectsStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$ProjectsStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
		if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$ProjectsStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
		while ($ProjectsRow = mysql_fetch_assoc($ProjectsRes))
		{
			echo '<li class="page-item1" id="'.$ProjectsRow['ID'].'">'.$ProjectsRow['Name'].'<ul>';
			$TasksStr = 'SELECT * FROM '.$DataBase['TablePrefix'].'Tasks WHERE ProjectID='.$ProjectsRow['ID'].' && UserID='.$_SESSION['UserID'].' && (Status & 1) != 1 ORDER BY Sorting, Name';
			$TasksRes = mysql_query($TasksStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$TasksStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
			if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$TasksStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
			while ($TasksRow = mysql_fetch_assoc($TasksRes))
			{

				// echo '<li id="Sort['.$ProjectsRow['ID'].']['.$TasksRow['ID'].']" class="page-item1 nochilds">'.$TasksRow['ID'].': '.htmlspecialchars($TasksRow['Name']).'</li>';
				echo '<li id="Task_'.$TasksRow['ID'].'" class="page-item1 nochilds">'.$TasksRow['ID'].': '.htmlspecialchars($TasksRow['Name']).'&nbsp;&nbsp';
				echo '<a href="#" onclick="$(\'#Menu > ul\').tabs(\'url\',1, \'Edit.php?ID='.$TasksRow['ID'].'&amp;Tab=2\'); $(\'#Menu > ul\').tabs(\'select\', 1);">';
				echo '<img BORDER=0 ALIGN="middle" ALT="Edit" HEIGHT=25 WIDTH=25 src="images/pencil-icon.png"></A>&nbsp;&nbsp';
				echo '<a href="#" id="done" onclick="MarkAsDone('.$TasksRow['ID'].');">';
				echo '<img BORDER=0 ALIGN="middle" ALT="Mark as Done" TITLE="Mark as Done" HEIGHT=20 WIDTH=25 src="images/tick.png"></A></li>';
			}
			echo '</ul></li>';
		}
		echo '</ul>';
		echo '<div id="list-container-ser">';
		echo 'Drag the elements to change their position';
	        echo '</div>';
		echo '<p>2Do: figure out how to find out to what Project a task is sorted to (or if that is not possible: restrict the sorting to within the Project).<br />Idea that might work otherwise is: if nothing works, we might be able to add a hidden LI to each project to detect the next project while we loop the POST.</p>';
	}

?>