<?php
	require_once(realpath('Protected/Included.inc'));
	if (empty($_SESSION['UserID']))
	{
		echo 'Go <a href="?Page=Login">login</a>.';
		echo '<script type="text/javascript">'."$('#Login').css('display', 'inline'); $('#Logout').css('display', 'none'); $('#Menu > ul').tabs('select', 7);</script>";
		$_SESSION['Debug'][] = 'Search tab';
	}
	else
	{
?>

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

$(function()
{
        $('#SearchForm').submit(function()
        {
		$.ajax({
			url: 'SearchEngine.php',
			type: 'POST',
			data: $('input').serialize()+'&'+$('select').serialize(),
			dataType: 'script',
			timeout: 2000,
			error: function(xhr, desc, exceptionobj)
			{
				alert('Failed to submit: ' + xhr.responseText);
			},
			success: function(data, textStatus)
			{
				this;
/*
				this;
				alert(textStatus);
				document.getElementsByTagName('head').item(0).appendChild(data);
				$("#ResponseBox").html('Search Completed');
				alert(data);
				alert('Submitted. 2Do: how to get the return data?' + data + ' nad also the status: ' + textStatus);
*/
				// 2Do: clear the form
				// 2Do: maybe: update the dropdown boxes (fancy, but not really needed)
				// 2Do: optionally add the task to the projects task list in this view, but this might be a bad idea since we should only list the "next actions" tasks here (but, than again, we could be very fancy and ask the server for an answer on what is now the most urgent "next action" for the project the new task was submitted to)
				// Note: if a new project is added, the task NEEDS to be added to the projects/tasks listed here!
			}
		});
		return false;
	});
});

</script>
<form action="?Page=Search" method="post" accept-charset="utf-8" id="SearchForm" name="SearchForm">
	<table border="1" cellpadding="2" cellspacing="2">
		<tr>
			<td>String</td>
			<td><input type="text" id="PlainText" name="PlainText" /></td>
		</tr>
		<tr><td>Context</td>
		<td><select name="Context">
			<option value="">Select One</option>
<?php
/*
optgroups:
					* static
					* recently assigned to (name + email)
					* recently used
					* top 7
					If it is a valid email address, assign the task (if in settings: notify the user)
*/
					$ContextsStr = 'SELECT ID, Name FROM '.$DataBase['TablePrefix'].'Contexts WHERE UserID='.$_SESSION['UserID'].' && (Status & 1) != 1 ORDER BY Sorting, Name';
					$ContextsRes = mysql_query($ContextsStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$ContextsStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
					if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$ContextsStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
					while ($ContextsRow = mysql_fetch_assoc($ContextsRes))
					{
						echo '<option value="'.$ContextsRow['Name'].'">'.$ContextsRow['Name'].'</option>';
					}
?>
	</select></td>
	</tr>
	<tr><td>Name</td>
		<td>
			<select name="Name_Selector">
				<option value="">Select One</option>
<?php
				$ProjectsStr = 'SELECT ID, Name FROM '.$DataBase['TablePrefix'].'Tasks WHERE UserID='.$_SESSION['UserID'].' ORDER BY Sorting, Name';
				$ProjectsRes = mysql_query($ProjectsStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$ProjectsStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
				if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$ProjectsStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
				while ($ProjectsRow = mysql_fetch_assoc($ProjectsRes))
				{
					echo '<option value="'.$ProjectsRow['Name'].'">'.$ProjectsRow['Name'].'</option>';
				}
?>	
			</select><br />
		</td>	
	</tr>
	<tr>
		<td>Project</td>
		<td>
		<select name="Project_Selector">
			<option value="">Select One</option>
<?php
			$ProjectsStr = 'SELECT ID, Name FROM '.$DataBase['TablePrefix'].'Projects WHERE UserID='.$_SESSION['UserID'].' && (Status & 1) != 1 ORDER BY Sorting, Name';
			$ProjectsRes = mysql_query($ProjectsStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$ProjectsStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
			if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$ProjectsStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
			while ($ProjectsRow = mysql_fetch_assoc($ProjectsRes))
			{
				echo '<option value="'.$ProjectsRow['Name'].'">'.$ProjectsRow['Name'].'</option>';
			}
?>
		</select><br />
		</td>
	</tr>
	<tr>
		<td>DateDue (YYYY-MM-DD)</td>
		<td>
			<input type="text" id="DateDueSearch" name="DateDue" tabindex="6" /> <br />
		</td>
		<td>
			
		<input type="radio" name="DateOpt" value="1"/> Before Date <br />
		<input type="radio" name="DateOpt" value="0" checked /> Exact Date<br />
		<input type="radio" name="DateOpt" value="2"/> After Date
		</td>
	</tr>
	<tr>
		<td>Status</td>
		<td>
		<select name="Status_Selector" id="Status">
			<option value="4" selected>Open Tasks</option>
			<option value="0">All Tasks</option>
			<option value="3">Not Started</option> <!--if ((Status&1)!=1 && (Status&2)!=2)-->
			<option value="2">Working on</option> <!--if ((Status&2)=2)-->
			<option value="1">Completed</option> <!--if ((Status&1)=1)-->
			<!--BitMask: 1 = isDone, 2 = inProgress-->
		</select><br />
		</td>	
	</tr>
	<tr>
		<td>Sorting Order</td>
		<td>
		<select name="Sorting_Selector1" id="Sorting_Selector1">
			<option value="Sorting" selected>Sorting</option>
			<option value="Name">Name</option>
			<option value="Project">Project</option> 
			<option value="Context">Context</option> 
			<option value="Duration">Duration</option> 
			<option value="Status">Status</option>
			<option value="DateDue">Date Due</option> 
			<option value="DateCreated">Date Created</option>
		</select><br />
		</td>	
		<td>
		<select name="Sorting_Selector2" id="Sorting_Selector2">
			<option value="Name" selected>Name</option>
			<option value="Sorting">Sorting</option> 
			<option value="Project">Project</option>
			<option value="Context">Context</option>
			<option value="Duration">Duration</option> 
			<option value="Status">Status</option> 
			<option value="DateDue">Date Due</option>
			<option value="DateCreated">Date Created</option>
		</select><br />
		</td>	
	</tr>
	
</table>
	<input type="submit" value="Search" tabindex="9" />

</form>

<script type="text/javascript">
  Calendar.setup({
        inputField     :    "DateDueSearch",   // id of the input field
        displayArea    :    "DateDueSearch",
        eventName      :    "focus",
        ifFormat       :    "%Y-%m-%d",       // format of the input field
        //daFormat       :    "%Y-%m-%d %H:%M",       // format of the input field
        align          :    "B",
        showsTime      :    true,
        timeFormat     :    "24",
    });
</script>

	<div id="SearchResults" style="display: none;">
		<h3>Results</h3><br/>
		<div id="Results"></div>
	</div>
<?php
	}
?>