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
	$_SESSION['Debug']['TestArray3'] = '';
	$j = 0;
	$ProjectID = 0;
	$sortingOrderProjects = 1;
	foreach($_POST['Projects'] as $index => $value)
	{
		
		foreach ($_POST['Projects'][$index] as $index2 => $value2)
		{
			
			//$_SESSION['Debug']['TestAtrray12324'] .= $_POST['Projects'][$index][$index2].'=>'.$value2;
			$_SESSION['Debug']['TestArray3'] .= $value2.':';
			$temp = ( $j % 2 );
			
			//if ($temp !=0)
			if (!is_numeric($value2))
			{
				$sortingOrder = 1;
				foreach ($_POST['Projects'][$index][$index2] as $index3 => $value3)
				{
					foreach ($_POST['Projects'][$index][$index2][$index3] as $index4 => $value4)
					{
						//$_SESSION['Debug'][] = $value4.':'.$sortingOrder;
						//$_SESSION['Debug']['TestArray3'] .= $_POST['Projects'][$index][$index2][$index3][$index4].'=>'.$value4;
						$TaskStr = 'UPDATE '.$DataBase['TablePrefix'].'Tasks SET ProjectID='.$ProjectID.', Sorting='.$sortingOrder.' WHERE ID='.$value4.' LIMIT 1';
						$TaskRes = mysql_query($TaskStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$TaskStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
						if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$TaskStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); }
						$sortingOrder++;
					}	
				}	
			}
			else
			{
				$ProjectID = $value2;
				$ProjStr = 'UPDATE '.$DataBase['TablePrefix'].'Projects SET Sorting='.$sortingOrderProjects.' WHERE ID='.$ProjectID.' LIMIT 1';
				$ProjRes = mysql_query($ProjStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$ProjStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
				if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$ProjStr.'<br />File: '.__FILE__.' on line: '.(__LINE__); } 
				$sortingOrderProjects++;
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