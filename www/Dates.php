<?php
	//Function to parse the duration
	function getDuration($num)
	{
		$arrayres = array();
		//minutes in one hour
		$minutesHour = 60;
		//minutes in one day
		$minutesDay = 1440;
		//minutes in one month
		$minutesMonth = 43200;
		//minutes in one year
		$minutesYear = 518400;
		//vars
		$mod = 0;
		$years = 0;
		$months = 0;
		$days = 0;
		$hours = 0;
		$minutes = 0;
		$years = floor($num / $minutesYear);
		$arrayres[] = $years;
		$mod = $num % $minutesYear;
		$months = floor($mod / $minutesMonth);
		$arrayres[] = $months;
		$mod = $mod % $minutesMonth;
		$days = floor($mod / $minutesDay);
		$arrayres[] = $days;
		$mod = $mod % $minutesDay;
		$hours = floor($mod / $minutesHour);
		$arrayres[] = $hours;
		$mod = $mod % $minutesHour;
		$arrayres[] = $mod;
		return $arrayres;										
	}
	
	function FormatDuration($array)
	{
		$finalString = "";
		if ($array[0] != 0)
			if ($array[0] == 1)
				$finalString = $array[0]." year, ";
			else
				$finalString = $array[0]." years, ";
		if ($array[1] != 0)
			if ($array[1] == 1)
				$finalString = $finalString.$array[1]." month, ";
			else
				$finalString = $finalString.$array[1]." months, ";
		if ($array[2] !=0)
			if ($array[2] == 1)
				$finalString = $finalString.$array[2]." day, ";
			else
				$finalString = $finalString.$array[2]." days, ";
		if ($array[3] !=0)
			if ($array[3] == 1)
				$finalString = $finalString.$array[3]." hour, ";
			else
				$finalString = $finalString.$array[3]." hours, ";
		if ($array[4] !=0)
			if ($array[4] == 1)
				$finalString = $finalString.$array[4]." minute";
			else
				$finalString = $finalString.$array[4]." minutes";
		return $finalString;
	}
?>