<?php
	require_once(realpath('Protected/Included_Raw.inc'));

	$UID = (empty($_POST['UID']) ? '' : mysql_real_escape_string($_POST['UID']));
	$PWD = (empty($_POST['PWD']) ? '' : mysql_real_escape_string($_POST['PWD']));

	if (empty($_SESSION['UserID']) && !empty($UID) && !empty($PWD))
	{
		// Check for a valid email address:
		if (eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $UID))
		{
			$UserStr = 'SELECT ID, PWD, Status FROM '.$DataBase['TablePrefix']."Users WHERE UID='".$UID."' LIMIT 1";
			$UserRes = mysql_query($UserStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$UserStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
			if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$UserStr.'<br />File: '.__FILE__.' on line: '.(__LINE__)."<br />\n"; }
			$UserRow = mysql_fetch_assoc($UserRes);
			if (!empty($UserRow['ID']) && $UserRow['PWD'] == md5($PWD) && ($UserRow['Status'] & 1) == 1)
			{
				$_COOKIE['UID'] = $UID;
				$_SESSION['UserID'] = $UserRow['ID'];
				$LoginStr = 'UPDATE '.$DataBase['TablePrefix']."Users SET DateLogin='".date('Y-m-d H:i:s')."' WHERE ID=".$UserRow['ID']." LIMIT 1";
				$LoginRes = mysql_query($LoginStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$LoginStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
				if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$LoginStr.'<br />File: '.__FILE__.' on line: '.(__LINE__)."<br />\n"; }
				echo "$('#ResponseBox').html('You are now logged in.').fadeIn('slow').fadeTo(5000, 1).fadeOut('99999');";
				echo "$('#Login').css('display', 'none'); $('#Logout').css('display', 'inline'); $('#Menu > ul').tabs('select', 1);";
			}
			elseif (!empty($UserRow['ID']) && $UserRow['PWD'] != md5($PWD))
			{
				echo "$('#ResponseBox').html('ERROR: wrong password.').fadeIn('slow').fadeTo(5000, 1).fadeOut('99999');";
			}
			elseif (!empty($UserRow['ID']) && $UserRow['PWD'] == md5($PWD) && ($UserRow['Status'] & 1) == FALSE)
			{
				echo "$('#ResponseBox').html('Account not activated').fadeTo(5000, 1).fadeOut('99999');";
			}
			else
			{
				// Create a new account:
				//insert the data into the database
/*				$InsertStr = 'INSERT INTO '.$DataBase['TablePrefix']."Users UID='".$UID."', PWD='".$PWD."'";
				$LoginRes = mysql_query($LoginStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$LoginStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
				if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$LoginStr.'<br />File: '.__FILE__.' on line: '.(__LINE__)."<br />\n"; }
				// build the logic to choose which template to use
    				//$template_file = 'templates/register_mail_template.tlp';
    				//$template =& new Template($template_file);
				
				// Load the template:
    				$template_file = 'templates/register_mail_template.tlp';
				$template =& new Template($template_file);
       				$output = $template->Evaluate();
*/
				// email address
				$email = $UID;
				// The subject
				$subject = "Confirmation mail";
				$Token = md5(rand());
				$InsertStr = 'INSERT INTO '.$DataBase['TablePrefix']."Users SET UID='".$UID."', PWD='".md5($PWD)."', DateCreated='".date('Y-m-d H:i:s')."', TokenRSS='".md5(rand())."',Token='".$Token."'";
				$InsertRes = mysql_query($InsertStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$InsertStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
				if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$InsertStr.'<br />File: '.__FILE__.' on line: '.(__LINE__)."<br />\n"; }
				$ConfirmationLink = $FullSiteURL.'?Page=ActivateSignup&Action='.$Token;
				// Load the template:
	                	$TemplateTags = array('[[NAME]]', '[[URL]]');
	                 	$TemplateValues = array('guest', $ConfirmationLink);
	                     	$message = str_replace($TemplateTags, $TemplateValues, file_get_contents(realpath($HomeDir.'/templates/register_mail_template.tlp')));
      			  	$mail = mail($email, $subject, $message);
				if ($mail) 
				{
					echo "$('#ResponseBox').html('Thank you for your registration. Now you have to activate your account. We have sent an email to your account in order to do it.');"; 
				}
				else
				{
					 echo "$('#ResponseBox').html('Email failed.');";
				}
			}
		}
		else
		{
			echo "$('#ResponseBox').html('ERROR: not a valid email address.').fadeIn('slow').fadeTo(5000, 1).fadeOut('99999');";
		}
	}
	else
	{
		echo "$('#ResponseBox').html('You where already logged in.').fadeIn('slow').fadeTo(5000, 1).fadeOut('99999');";
		echo "$('#Login').css('display', 'none'); $('#Logout').css('display', 'inline'); $('#Menu > ul').tabs('select', 0);";
	}
	
    /*
    class Template
    {
    	var $template;
    	var $variables;

    	function Template($template)
    	{     
        	$this->template = @file_get_contents($template);
        	$this->resetVars(); 
    	}

    	function resetVars()
    	{
        	$this->variables = array();
    	}

    	function Add($var_name, $var_data)
    	{
        	$this->variables[$var_name] = $var_data;
    	}

    	function AddArray($array)
    	{
        	if ( !is_array($array) ){
            	return;
        }
        foreach ( $array as $name => $data ){
            $this->Add($name, $data);
        }
        return;
    }

    function Evaluate($direct_output = false){
        $template = addslashes($this->template);       
        foreach ( $this->variables as $variable => $data ){
            $$variable = $data;
        }
        eval("\$template = \"$template\";");       
        if ( $direct_output ) {
            echo stripslashes($template);
        } else {
            return stripslashes($template); 
        }
    }
}*/

?>