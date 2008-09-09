<?php
	require_once(realpath('Protected/Included_Raw.inc'));
	if (empty($_SESSION['UserID']))
	{
		// Show login form:

?>
		<script type="text/javascript">
			$(function()
			{
			        $('#LoginForm').submit(function()
			        {
					$.ajax({
						url: 'LoginCheck.php',
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
						}
					});
					return false;
				});
			});
		</script>
		<form action="?Page=LoginCheck" method="post" id="LoginForm">
			<table cellspacing="2" cellpadding="2">
				<tr><td>Email</td><td><input type="text" name="UID" id="UID" maxlength="128"<?php if (!empty($_COOKIE['UID'])) { echo ' value="'.$_COOKIE['UID'].'"'; } ?> tabindex="1" /></td></tr>
				<tr><td>Password</td><td><input type="password" name="PWD" id="PWD" maxlength="128" tabindex="2" /></td></tr>
				<tr><td></td><td><input type="submit" value="Login" tabindex="3" /></td></tr>
			</table>
		</form>
<?php

	}
	else
	{
		echo 'Already logged in.';
		echo ' <a href="#" onclick="$(\'#Login\').css(\'display\', \'none\'); $(\'#Logout\').css(\'display\', \'inline\'); $(\'#Menu > ul\').tabs(\'select\', 7); return false;">Click to logout.</a>';
	}
?>