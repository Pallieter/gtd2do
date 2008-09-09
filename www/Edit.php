<?php
	require_once(realpath('Protected/Included.inc'));
	if (empty($_SESSION['UserID']))
	{
		echo 'Go <a href="?Page=Login">login</a>.';
		echo '<script type="text/javascript">'."$('#Login').css('display', 'inline'); $('#Logout').css('display', 'none'); $('#Menu > ul').tabs('select', 6);</script>";
	}
	elseif (empty($_GET['ID']) OR !is_numeric($_GET['ID']))
	{
		// throw error
		echo '<script type="text/javascript">'."$('#ResponseBox').html('Error sending the ID.').fadeIn('slow').fadeTo(5000, 1).fadeOut('99999');</script>";
	}
	else
	{
		include_once(realpath('Dates.php'));
		include_once(realpath('tab.php'));
?>
	
<script type="text/javascript">

$(function()
{
        $('#TaskAddForm').submit(function()
        {
		$.ajax({
			url: 'TaskEdit.php',
			type: 'POST',
			data: $('input').serialize(),
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
				$("#ResponseBox").html(data);
				alert(data);
				alert('Submitted. 2Do: how to get the return data?' + data + ' nad also the status: ' + textStatus);
*/
				// FF2Do: maybe: update the dropdown boxes (fancy, but not really needed)
				// 2Do: optionally add the task to the projects task list in this view, but this might be a bad idea since we should only list the "next actions" tasks here (but, than again, we could be very fancy and ask the server for an answer on what is now the most urgent "next action" for the project the new task was submitted to)
				// Note: if a new project is added, the task NEEDS to be added to the projects/tasks listed here!
			}
		});
		return false;
	});
});

</script>
<?php
	//get the data from the taks.php
	$TaskToEditStr = 'SELECT * FROM '.$DataBase['TablePrefix'].'Tasks WHERE ID='.$_GET['ID'].' AND USERID='.$_SESSION['UserID'].' LIMIT 1';
	$TaskToEditRes = mysql_query($TaskToEditStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$TaskToEditStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
	if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$TaskToEditStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
	$TaskToEditRow = mysql_fetch_assoc($TaskToEditRes);
?>
<form action="?Page=TaskEdit" method="post" accept-charset="utf-8" id="TaskAddForm" name="TaskForm">
	<div class="TaskInput">
		<h3>Context</h3>
		<select name="Context_Selector" onchange="document.getElementById('Context').value=this.value;">
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
		</select><br />
		<input type="text" id="Context" name="Context" tabindex="1" 
		<?php
			$TaskContextStr = 'SELECT Name FROM '.$DataBase['TablePrefix'].'Contexts WHERE ID='.$TaskToEditRow['ContextID'].' LIMIT 1';
			$TaskContextRes = mysql_query($TaskContextStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$TaskContextStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
			if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$TaskContextStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
			$TaskContextRow = mysql_fetch_assoc($TaskContextRes);
			echo ' value="'.$TaskContextRow['Name'].'"';
		?>
		/>
	</div>
	<div class="TaskInput">
		<h3>Name</h3>
		<select name="Name_Selector" onchange="document.getElementById('Name').value=this.value;"></select><br />
		<input type="text" id="Name" name="Name" tabindex="2" 
		<?php
			echo ' value="'.$TaskToEditRow['Name'].'"';
		?>
		/>
	</div>
	<div class="TaskInput">
		<h3>Project</h3>
		<select name="Project_Selector" onchange="document.getElementById('Project').value=this.value;">
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
		<input type="text" id="Project" name="Project" tabindex="3" 
		<?php
			$TaskProjectStr = 'SELECT Name FROM '.$DataBase['TablePrefix'].'Projects WHERE ID='.$TaskToEditRow['ProjectID'].' LIMIT 1';
			$TaskProjectRes = mysql_query($TaskProjectStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$TaskProjectStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
			if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$TaskProjectStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
			$TaskProjectRow = mysql_fetch_assoc($TaskProjectRes);
			echo ' value="'.$TaskProjectRow['Name'].'"';
		?>
		/>
	</div>
	<div class="TaskInput">
		<h3>CheckList</h3>
		<select name="CheckList_Selector" onchange="document.getElementById('CheckList').value=this.value;"></select><br />
		<input type="text" id="CheckList" name="CheckList" onblur="w=window.open('<?php echo $FullSiteURL;?>CheckList.php', 'GTD2DoCheckList','scrollbars=yes,resizable=yes,toolbar=no'); w.focus(); return false;" tabindex="4" />
		<!-- Maybe we can integrate the checklists as an optgroup into the tasks dropdown? -->
	</div>
	<div class="TaskInput">
		<h3>Duration (mins)</h3>
		<select name="Duration_Selector" onchange="document.getElementById('Duration').value=this.value;">
			<option value="">Select One</option>
<?php
			$DurationStr = 'SELECT Duration FROM '.$DataBase['TablePrefix'].'Tasks WHERE UserID='.$_SESSION['UserID'].' AND Duration <> 0 GROUP BY Duration ORDER BY Duration ASC';
			$DurationRes = mysql_query($DurationStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$DurationStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
			if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$DurationStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
			while ($DurationRow = mysql_fetch_assoc($DurationRes))
			{
				$ArrayDates = getDuration($DurationRow['Duration']);
				$DateParsed = FormatDuration($ArrayDates);
				//echo ' value="'.$Dateparsed.'"';
				echo '<option value="'.$DurationRow['Duration'].'">'.$DateParsed.'</option>';
			}
?>
		</select><br />
		<input type="text" id="Duration" name="Duration" tabindex="5" 
			<?php
				echo ' value="'.$TaskToEditRow['Duration'].'"';
			?>
		/>
		<img align="MIDDLE" src="Images/help.jpg" alt="HELP" title="Help" onclick="window.open('InputDates.php?Time='+document.getElementById('Duration').value, 'Date Calulator', 'width=450, height=350')" style="width: 25px; height: 25px;" />
	</div>
	<div class="TaskInput">
		<h3>DateDue (strtotime)</h3>
		<select name="DateDue_Selector" onchange="document.getElementById('DateDue').value=this.value;">
			<option value="<?php echo date('Y-m-d'); ?>">today</option>
			<option value="<?php echo date('Y-m-d H:i', strtotime('+1 day')); ?>">tomorrow</option>
			<option value="<?php echo date('Y-m-d H:i', strtotime('+1 week')); ?>">next week</option>
			<option value="<?php echo date('Y-m-d H:i', strtotime('+1 month')); ?>">next month</option>
			<option value="9999-01-01 01:01:01">someday/maybe</option>
		</select><br />
		<input type="text" id="DateDueEdit" name="DateDueEdit" tabindex="6" 
		<?php
			echo ' value="'.$TaskToEditRow['DateDue'].'"';
		?>
		/>
	</div>
	<div class="TaskInput">
		<h3>Status</h3>
		<select name="Status_Selector" onchange="document.getElementById('Status').value=this.value;">
			<option value="0" <?php if (($TaskToEditRow['Status'] & 1)!=1 && ($TaskToEditRow['Status'] & 2)!=2) echo 'selected';?> >Not Started</option> 
			<option value="2" <?php if (($TaskToEditRow['Status'] & 2)==2) echo 'selected';?> >Working on</option> 
			<option value="1" <?php if (($TaskToEditRow['Status'] & 1)==1) echo 'selected';?> >Completed</option> 
			<!--BitMask: 1 = isDone, 2 = inProgress-->
		</select><br />
		<input type="text" id="Status" name="Status" tabindex="7" 
		<?php
			echo ' value="'.$TaskToEditRow['Status'].'"';
		?>
		/>
	</div>
	<div class="TaskInput">
		<h3>Notes</h3>
		<!-- 2Do: Auto search in order to re-use previously used notes? -->
		<!-- 2Do: add my "tab" functionality? -->
		<!-- 2Do: create a way to make this are bigger (optionally also with a WYSIWYG editor). Lightbox (after onclick of a make-bigger icon // or onfocus)? -->
		<textarea id="Notes" name="Notes" cols="5" rows="6" tabindex="8" onkeydown="insertTab(this, event);"><?php echo $TaskToEditRow['Notes']; ?></textarea>
		<a id="enlarge" href="#" onclick="$('#Notes').css('width', '400px').css('height', '50px'); $('#shorten').css('display','inline'); $('#enlarge').css('display','none');"><img src="Images/enlarge2.jpg" alt="Enlarge" title="Enlarge" style="width: 25px; height: 25px;" /></a>
		<a id="shorten" style="display: none;" href="#" onclick="$('#Notes').css('width', '150px').css('height', '50px'); $('#enlarge').css('display','inline'); $('#shorten').css('display','none');"><img src="Images/shorten2.jpg" alt="Shorten" title="Shorten" style="width: 25px; height: 25px;" /></a>
	</div>
	<div class="TaskInput">
		<input type="submit" value="UPDATE" tabindex="9" /> or <a href="#" onclick="$('#Menu > ul').tabs('url', 1, 'Task.php'); $('#Menu > ul').tabs('load', 1);">reset and add a new task</a>.
	</div>
</form>

<script type="text/javascript">
  Calendar.setup({
        inputField     :    "DateDueEdit",   // id of the input field
        displayArea    :    "DateDueEdit",
        eventName      :    "focus",
        ifFormat       :    "%Y-%m-%d %H:%M",       // format of the input field
        //daFormat       :    "%Y-%m-%d %H:%M",       // format of the input field
        align          :    "B",
        showsTime      :    true,
        timeFormat     :    "24",
    });
</script>

<br style="clear: both;" />
<input type="hidden" name="TaskID" 
<?php
			echo ' value="'.$_GET['ID'].'"';
		?>
/>
<input type="hidden" name="TabID" 
<?php
			echo ' value="'.$_GET['Tab'].'"';
		?>
/>


<h3> WHO | WHAT | WHERE | HOW | WHY | WHEN</h3>

<?php

// 2Do: Same project/tasks list as in Tasks.php, but only the first (sorting-wise) tasks.


	} // END: login check

?>