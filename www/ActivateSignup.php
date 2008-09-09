<script type="text/javascript">
	function redirect()
	{
  		window.locationf="http://192.168.24.13/?Page=Login";
	}
</script>
<?php
	require_once(realpath('Protected/Included_Raw.inc'));
	$Tokenillo = (empty($_GET['Token']) ? '' : mysql_real_escape_string($_GET['Token']));
	
	$ActivateStr = 'UPDATE '.$DataBase['TablePrefix'].'Users SET Status=1 WHERE Token="'.$Tokenillo.'"';
	$ActivateRes = mysql_query($ActivateStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$ActivateStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
	echo 'Account activated succesfully. You will be redirected soon';
	//redirect after 6 seconds
	echo '<script type="text/javascript"> setTimeout("redirect()", 6000); </script>'; 
	
?>