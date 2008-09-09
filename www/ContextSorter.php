<?php
	require_once(realpath('Protected/Included_Raw.inc'));
	$_SESSION['Debug'][] = var_export($_POST, true);

	
/*	
	//-------------------------------------------------//
	//TEST
	$TaskStr = 'UPDATE '.$DataBase['TablePrefix'].'Tasks SET ProjectID=34 WHERE ID=45 LIMIT 1';
	$TaskRes = mysql_query($TaskStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$TaskStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
	if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$TaskStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
	//------------------------------------------------//
*/	
	$_SESSION['Debug']['TestArray2'] = '';
	$j = 0;
	$ContextID = 0;
	$sortingOrderContext = 1;
	foreach($_POST['Contexts'] as $index => $value)
	{
		
		foreach ($_POST['Contexts'][$index] as $index2 => $value2)
		{
			
			//$_SESSION['Debug']['TestArray2'] .= $_POST['Projects'][$index][$index2].'=>'.$value2;
			//$_SESSION['Debug']['TestArray2'] .= $value2.':';
			
			if (!is_numeric($value2))
			{
				$sortingOrder = 1;
				foreach ($_POST['Contexts'][$index][$index2] as $index3 => $value3)
				{
					foreach ($_POST['Contexts'][$index][$index2][$index3] as $index4 => $value4)
					{
						//$_SESSION['Debug'][] = $value4.':'.$sortingOrder;
						//$_SESSION['Debug']['TestArray2'] .= $_POST['Projects'][$index][$index2][$index3][$index4].'=>'.$value4;
						$TaskStr = 'UPDATE '.$DataBase['TablePrefix'].'Tasks SET ContextID='.$ContextID.', Sorting='.$sortingOrder.' WHERE ID='.$value4.' LIMIT 1';
						$TaskRes = mysql_query($TaskStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$TaskStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
						if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$TaskStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
						$sortingOrder++;
					}	
				}	
			}
			else
			{
				$ContextID = $value2;
				$ContStr = 'UPDATE '.$DataBase['TablePrefix'].'Contexts SET Sorting='.$sortingOrderContext.' WHERE ID='.$ContextID.' LIMIT 1';
				$ContRes = mysql_query($ContStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$ContStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
				if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$ContStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); } 
				$sortingOrderContext++;
			}
			$j++;
			
			
		}
		
	}
		
	/*foreach ($_POST['Sort'] as $ProjectID => $Tasks)
	{
		$i = 1;
		foreach ($Tasks as $TaskID => $Sorting)
		{
			$TaskStr = 'UPDATE '.$DataBase['TablePrefix'].'Tasks SET ProjectID='.$ProjectID.', Sorting='.$i.' WHERE ID='.$TaskID.' LIMIT 1';
			$TaskRes = mysql_query($TaskStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$TaskStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
			if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$TaskStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
			$i++;
		}
	}
*/
	echo "$('#ResponseBox').html('Order changed.').fadeIn('slow').fadeTo(5000, 1).fadeOut('99999');";
?>