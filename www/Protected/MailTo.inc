<?php

/* Version: 3.3
 *
 * Class: MailTo: sends email messages from UNIX & Windows.
 *
 * Common Law Copyright 2007 eBrain. All rights reserved.
 *
 * This is unpublished proprietary code of Pallieter Koopmans.
 * Your access to it does not give you permission to use it.
 *
 */

/* Usage:

	* None of the variables are required; it will automatically send the POST variables to the default email address.

		require_once('Protected/MailTo.inc');
		$Email = new MailTo();
		$Email->ToName = 'Pallieter Koopmans';
		$Email->ToMail = 'WebMaster'; // An email address OR the username part of the email address on the local domain.
		$Email->FromName = 'FirstName LastName';
		$Email->FromMail = 'F.LastName@KoffieHosting.com';
		$Email->Subject = 'Test email from the eBrain MailTo Class';
		$Email->Message = 'Content: '.date('Y-m-d H:i:s').$Email->EoL.'NextLine';
		// $Email->Email();
		if ($Email->Email())
		{
			echo 'Success';
		}
		else
		{
			echo 'ERROR: '.$Email->Error;
		}
*/


// 2Do: add file handling to this script!
/*

$_FILES['userfile']['name'] // The original name of the file on the client machine.
$_FILES['userfile']['type'] // The mime type of the file, if the browser provided this information. An example would be "image/gif". This mime type is however not checked on the PHP side and therefore don't take its value for granted.
$_FILES['userfile']['size'] // The size, in bytes, of the uploaded file.
$_FILES['userfile']['tmp_name'] // The temporary filename of the file in which the uploaded file was stored on the server.
$_FILES['userfile']['error'] // The error code associated with this file upload.

// MIME = Multipurpose Internet Mail Extensions
// See also: http://pear.php.net/manual/en/package.mail.mail-mime.php

$Boundary = md5(uniqid(time()));
$EncodedFile = chunk_split( base64_encode($FileContent));

// MIME-Version: 1.0
// Content-Type: multipart/mixed; boundary="'.$Boundary.'";
// Content-Transfer-Encoding: 7bit
// Your email reader is not a MIME-compatible client.
// Contact your postmaster if you require assistance.
// --'.$Boundary.'
// Content-Type: text/plain; charset="iso-8859-1"
// Content-Transfer-Encoding: 7bit
// Hello! Here's that Zend.com logo you requested!
// -John
// --'.$Boundary.'
// Content-Type: image/jpeg; name="zendlogo.jpg";
// Content-Transfer-Encoding: base64
// Content-Disposition: attachment
// <base64 encoded data for the zendlogo.jpg image>
// --'.$Boundary.'--

if (is_uploaded_file($_FILES['Attachment']['tmp_name']))
{
	$FileName = $_FILES['Attachment']['tmp_name'];
	if (empty($_FILES['Attachment']['type']))
	{
		$Message .= 'Content-Type: application/octet-stream; name="'.$FileName.'"'.$EoL; // Use this for all unknown filetypes.
	}
	else
	{
		$Message .= 'Content-Type: '.$_FILES['Attachment']['type'].'; name="'.$letter.'"'.$EoL;
	}
//blabla to remember:
	$msg .= "Content-Transfer-Encoding: base64".$EoL;
	// Use either inline or attachment:
	// $msg .= "Content-Disposition: inline';
	$msg .= "Content-Disposition: attachment; filename=\"".$letter."\"".$EoL.$EoL; // !! This line needs TWO end of lines !! IMPORTANT !!
}
*/

class MailTo
{
	// Initialize the attributes (class-local variables) for this class:
	var $Error = '';

	// Set debuging mode:
	var $Debug = false; // Used for testing (true=on, false=off). In this mode, additional information/options and all error messages are shown.

	// Set the error reporting level:
	private function Debug()
	{
		if ($this->Debug)
		{
			error_reporting(E_ALL);
			ini_set('display_errors', true); // This helps on servers where the PHP error logging happens via a log file instead of displaying it on screen. Turn this off when the site goes live!
		}
		else
		{
			error_reporting(0);
			ini_set('display_errors', false);
		}
	}

	// Check single-line inputs: returns false if text contains newline character.
	private function _has_no_newlines($has_no_newlines)
	{
		return preg_match("/(%0A|%0D|\\n+|\\r+)/i", $has_no_newlines) == 0;
	}
	// Check multi-line inputs: returns false if text contains newline followed by email-header specific string.
	private function _has_no_emailheaders($has_no_emailheaders)
	{
		return preg_match("/(%0A|%0D|\\n+|\\r+)(content-type:|to:|cc:|bcc:)/i", $has_no_emailheaders) == 0;
	}
	// Send the actual email:
	public function Email()
	{
		$this->Debug();

		// Run some (spam) checks and decide what to do:
		if (empty($DomainName))
		{
			if (!empty($_GLOBAL['DomainName']))
			{
				$DomainName = $_GLOBAL['DomainName'];
			}
			elseif (empty($_SERVER['SERVER_NAME']))
			{
				$DomainName = 'KoffieHosting.com';
			}
			else
			{
				$DomainName = str_replace('www.', '', strtolower($_SERVER['SERVER_NAME']));
			}
		}
		if (empty($_GLOBAL['Time']))
		{
			$Time = time();
		}
		else
		{
			$Time = $_GLOBAL['Time'];
		}

		if (!empty($this->ToMail) && (!$this->_has_no_newlines($this->ToMail) || !$this->_has_no_emailheaders($this->ToMail)))
		{
			$this->Error = 'ToMail: illegal content'; // '<p>Uw email is NIET verzonden omdat een of meerdere velden onjuiste informatie bevat.</p><p>Probeer het <a href="javascript:history.go(-1);">nogmaals</a>.</p>';
			return false;
		}
		if (!empty($this->FromMail) && (!$this->_has_no_newlines($this->FromMail) || !$this->_has_no_emailheaders($this->FromMail)))
		{
			$this->Error = 'FromMail: illegal content'; // '<p>Uw email is NIET verzonden omdat een of meerdere velden onjuiste informatie bevat.</p><p>Probeer het <a href="javascript:history.go(-1);">nogmaals</a>.</p>';
			return false;
		}

	 	// Check the local OS for the end-of-line character to use (Windows, Macintosh or Linux/other):
		if (strtoupper(substr(PHP_OS, 0, 3) == 'WIN'))
		{
			$EoL = "\r\n";
		}
		elseif (strtoupper(substr(PHP_OS, 0, 3) == 'MAC'))
		{
			$EoL = "\r";
		}
		else
		{
			$EoL = "\n";
		}
		// 2Do: move this outside the functions!
		// $this->EoL = $EoL; // We can use this ($Email->EoL) to format the message text.

		// Send the email:
		if (!empty($this->ToMail) && strpos($this->ToMail, '@') && eregi("^([a-z0-9_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]+$", $this->ToMail))
		{
			$MailRecipient = $this->ToMail;
		}
		elseif (!empty($this->ToMail) && (!$this->_has_no_newlines($this->ToMail) || !$this->_has_no_emailheaders($this->ToMail)))
		{
			$MailRecipient = $this->ToMail.'@'.$DomainName; // This seperation of the email address is required to prevent SPAM and illegal use of this mail script.
		}
		else
		{
			$MailRecipient = 'PostMaster'; // Email UID to use when it is not defined by the form (leave as is: PostMaster).
			$MailRecipient .= '@'.$DomainName; // This seperation of the email address is required to prevent SPAM and illegal use of this mail script.
		}
		if (!empty($this->ToName) && (!$this->_has_no_newlines($this->ToName) || !$this->_has_no_emailheaders($this->ToName)))
		{
			$MailRecipient = '"'.$this->ToName.'" <'.$MailRecipient.'>';
		}
		if (empty($this->Message))
		{
			$MessageBody = '';
			foreach ($_POST AS $Key => $Value)
			{
				if ($Key <> 'Submit')
				{
					// To design the email we add a number of tabs based on the length of the key:
					if (strlen($Key) < 6)
					{
						$MessageBody .= $Key."\t\t\t --> ".$Value.$EoL;
					}
					elseif (strlen($Key) < 12)
					{
						$MessageBody .= $Key."\t\t --> ".$Value.$EoL;
					}
					else
					{
						$MessageBody .= $Key."\t --> ".$Value.$EoL;
					}
				}
			}
			if (!empty($_SESSION['ResellerID']))
			{
				$MessageBody .= "ResellerID\t --> ".$_SESSION['ResellerID'].$EoL;
			}
			$MessageMeta = '';
			if (!empty($_COOKIE['FirstVisit']))
			{
				$MessageMeta .= "FirstVisit\t\t --> ".$_COOKIE['FirstVisit'].$EoL;
			}
			$MessageMeta .= "RemoteIP\t\t --> ".$_SERVER['REMOTE_ADDR'].$EoL."RemoteHost\t\t --> ".gethostbyaddr($_SERVER['REMOTE_ADDR']).$EoL."BrowserLang\t\t --> ".$_SERVER['HTTP_ACCEPT_LANGUAGE'];
			$Message = $MessageBody.$MessageMeta;
		}
		else
		{
			$Message = $this->Message;
		}
		if (empty($this->Subject))
		{
			$Subject = $DomainName.' Contact Form';
		}
		else
		{
			$Subject = $this->Subject;
		}
		// Build the message:
		$Headers = 'MIME-Version: 1.0'.$EoL;
		if (!empty($Message) && strpos(strtolower($Message), '<html') && strpos(strtolower($Message), '<html') < 222)
		{
			$Headers .= 'Content-type: text/html; charset=utf-8'.$EoL;
		}
		else
		{
			$Headers .= 'Content-type: text/plain'.$EoL;
		}
		$Headers .= 'X-Mailer: PHP '.phpversion().$EoL; // This helps avoid the spam-filters.
		$Headers .= 'Message-ID: <'.$Time.'-'.rand(1, 999).'@'.$DomainName.'>'.$EoL; // This helps avoid the spam-filters.
		if (!empty($this->FromMail) && eregi("^([a-z0-9_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]+$", $this->FromMail)) // alternative regexp:  '^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$'
		{
			$Headers .= 'Reply-To: '.$this->FromMail.$EoL; // This is added to help prevent this message being tagged as spam.
			$Headers .= 'Return-Path: '.$this->FromMail.$EoL; // This is added to help prevent this message being tagged as spam.
			if (empty($this->FromName))
			{
				$Headers .= 'From: '.$this->FromMail.$EoL;
			}
			else
			{
				$Headers .= 'From: "'.trim($this->FromName).'" <'.$this->FromMail.'>'.$EoL;
			}
		}
		else
		{
			$Headers .= 'Return-Path: NoReply@'.$DomainName.$EoL; // This is added to help prevent this message being tagged as spam.
			$Headers .= 'From: NoReply@'.$DomainName.$EoL;
		}
		if (!empty($this->BccMail) && eregi("^([a-z0-9_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]+$", $this->BccMail))
		{
			// Add the BCC header information:
			$Headers .= 'Bcc: '.$this->BccMail.$EoL;
		}
		$MailResult = mail($MailRecipient, $Subject, $Message, $Headers);
		if (empty($MailResult))
		{
			if (!empty($this->Debug))
			{
				echo '<hr />';
				echo '<pre>';
				print_r($_POST); // Showing this is a potential security risk.
				print_r($_FILES); // Showing this is a potential security risk.
				echo "\n\n".'MailRecipient: '.$MailRecipient;
				echo '<br /><br />Subject: ';
				print_r(htmlspecialchars($Subject));
				echo '<br /><br />Headers:<br />';
				print_r(htmlspecialchars($Headers));
				echo '<br /><br />Message:<br />';
				print_r(htmlspecialchars($Message));
				echo '</pre>';
			}
			$this->Error = print_r(htmlspecialchars($Message), true);
			return false;
		}
		else
		{
			return true;
		}
	}
}

?>