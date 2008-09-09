<?php

include_once(realpath('Dates.php'));

$Array = getDuration($_GET['Time']);

?>
<script language="JavaScript">
	
	function CalculateMins()
	{
		//minutes in one hour
		var minutesHour = 60;
		//minutes in one day
		 var minutesDay = 1440;
		//minutes in one month
		var minutesMonth = 43200;
		//minutes in one year
		var minutesYear = 518400;
		var years = 0;
		var days = 0;
		var months = 0;
		var hours = 0;
		var minutes = 0;
		var total = 0;
		years = parseInt(document.getElementById("Years").value);
		if (!isNaN(years))
			years = years * minutesYear;
		months = parseInt(document.getElementById("Months").value);
		if (!isNaN(months))
			months = months * minutesMonth;
		days = parseInt(document.getElementById("Days").value);
		if (!isNaN(days))
			days = days * minutesDay;
		hours = parseInt(document.getElementById("Hours").value);
		if (!isNaN(hours))
			hours = hours * minutesHour;
		minutes = parseInt(document.getElementById("Minutes").value);
		total = minutes + hours + days + months + years;
		// Transport total to the opening window and close this window:
		window.opener.document.TaskForm.Duration.value = total;
		window.close();
	}
	
</script>

<h3>Enter the task duration y click on Calculate!</h3>
<table cellspacing="10">
	<tr>
		<td>Years:</td> 
		<td><input type="text" id="Years" name="Years" value="<?php echo $Array[0]; ?>"/></td>
	</tr>
	<tr>
		<td>Months:</td>
		<td><input type="text" id="Months" name="Months" value="<?php echo $Array[1]; ?>"/></td>
	</tr>
	<tr>
		<td>Days:</td>
		<td><input type="text" id="Days" name="Days" value="<?php echo $Array[2]; ?>"/></td>
	</tr>
	<tr>
		<td>Hours:</td>
		<td><input type="text" id="Hours" name="Hours" value="<?php echo $Array[3]; ?>"/></td>
	</tr>
	<tr>
		<td>Minutes:</td>
		<td><input type="text" id="Minutes" name="Minutes" value="<?php echo $Array[4]; ?>"/><br /></td>
	</tr>
	<tr>
		<td></td>
		<td><button type="button" OnClick="CalculateMins()">Calculate</button></td>
	</tr>
</table>