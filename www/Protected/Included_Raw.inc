<?php

	/*
	 * Common Law Copyright 2008 eBrain (c) All rights reserved.
	 *
	 * This is unpublished proprietary code of Pallieter Koopmans.
	 * Your access to it does not give you permission to use it.
	 *
	 */

	// Contains all minimally required functionality common to all interactive pages.
	require_once(realpath(dirname(__FILE__).'/Config.inc'));

	// Start the session:
	session_start();
	// The temporary fix by PK on 20060214 for the Koffie Hosting PHP bug:
	// ini_set('session.save_handler', 'files');

	// Set the NEW debug handler:
	if ($Debug)
	{
		$_SESSION['Debug'][] = date('Y-m-d H:i:s');
	}

	// This function opens the database on every page call.
	function OpenDB($Server_Name, $DataBase_Name, $UserName, $Password)
	{
		$dbLink = mysql_connect($Server_Name, $UserName, $Password) or die ('Could not connect to the MySQL database because: '.mysql_error());
		mysql_select_db($DataBase_Name, $dbLink) or die ('Could not select the database because: '.mysql_error());
	}

	// This function returns LARGECAP two-letter ISO 639-1 alpha-2 codes:
	function getLanguages()
	{
		$Languages = array();
		// Check if there is a session with a language defined:
		if (!empty($_SESSION['Language']))
		{
			$Languages[] = $_SESSION['Language'];
		}
//		// Check browser language (if HTTP_ACCEPT_LANGUAGE exists, as some browsers and search engines do not supply one):
//		if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE']))
//		{
//			$LangList = explode(',',strtoupper($_SERVER['HTTP_ACCEPT_LANGUAGE']));
//			for ($i=0; $i<count($LangList); $i++)
//			{
//				$LanguageCode = strtoupper(substr($LangList[$i],0,2));
//				if (ord(substr($LanguageCode,0,1)) >= 65 && ord(substr($LanguageCode,0,1)) <= 90 && ord(substr($LanguageCode,1,1)) >= 65 && ord(substr($LanguageCode,1,1)) <= 90)
//				{
//					$Languages[] = $LanguageCode;
//				}
//			}
//		}
		// Ensure that we at least have one language available:
		if (empty($GLOBALS['DefaultLanguage']))
		{
			$Languages[] = 'EN'; // Force the most likely language to be used as an alternative.
		}
		else
		{
			// Otherwise use the default (as defined in Config.inc):
			$LanguageCode = strtoupper(substr($GLOBALS['DefaultLanguage'], 0, 2));
			// Make sure the first two characters are indeed two capitals and ASCII characters between 65 and 90 (A-Z).
			if (ord(substr($LanguageCode, 0, 1)) >= 65 && ord(substr($LanguageCode, 0, 1)) <= 90 && ord(substr($LanguageCode, 1, 1)) >= 65 && ord(substr($LanguageCode, 1, 1)) <= 90)
			{
				$Languages[] = $LanguageCode;
			}
		}
		// Make sure a Language (code) does not exist twice in the array (and the internal pointer has been set to its first element):
		$Languages = array_unique($Languages);
		return array_values($Languages);
	}

	// Get a Translation from ID or Name:
	function getWord($Arg1, $Arg2 = '', $MakeLink = true)
	{
		if (!empty($Arg2))
		{
			$Category = $Arg1;
			$IDName = $Arg2;
		}
		else
		{
			$IDName = $Arg1;
		}
		// Check if the IDName is not equal to the one(s) we use internally:
		if (!is_numeric($IDName) && $IDName == 'AddCategoryPlaceholderTID')
		{
			return $IDName;
		}
		// R@R2Do: this is not the absolute best way to do this, but might be a 99.999% good solution making it good enough for practical use? This because we don't want to loose too much speed!
//		elseif (!empty($Category) && !empty($_SESSION['getWords'][$Category][$IDName]))
//		{
//			return $_SESSION['getWords'][$Category][$IDName];
//		}
//		elseif (!empty($_SESSION['getWords'][$IDName]))
//		{
//			return $_SESSION['getWords'][$IDName];
//		}
		else
		{
			global $Debug, $DataBase, $getWordLinks;
//			if (empty($_SESSION['Language']))
//			{
				$Languages = getLanguages();
//			}
//			else
//			{
//				$Languages[] = $_SESSION['Language'];
//			}
			$QueryTranslation = 'SELECT * FROM '.$DataBase['TablePrefix'].'Translations WHERE ';
			if (is_numeric($IDName))
			{
				$QueryTranslation .= 'ID='.$IDName;
			}
			else
			{
				$QueryTranslation .= "Name='".$IDName."'";
			}
			$TranslationsArray = array();
			if (!empty($Category))
			{
				if ($TranslationResult = mysql_query($QueryTranslation." AND Category='".$Category."'"))
				{
					if (mysql_num_rows($TranslationResult) >= 1)
					{
						while ($Translation = mysql_fetch_assoc($TranslationResult))
						{
							$TranslationsArray[] = $Translation;
						}
					}
				}
				if ($TranslationResult = mysql_query($QueryTranslation." AND Category<>'".$Category."'"))
				{
					if (mysql_num_rows($TranslationResult) >= 1)
					{
						while ($Translation = mysql_fetch_assoc($TranslationResult))
						{
							$TranslationsArray[] = $Translation;
						}
					}
				}
			}
			else
			{
				if ($TranslationResult = mysql_query($QueryTranslation))
				{
					if (mysql_num_rows($TranslationResult) >= 1)
					{
						while ($Translation = mysql_fetch_assoc($TranslationResult))
						{
							$TranslationsArray[] = $Translation;
						}
					}
				}
			}
			if (!empty($TranslationsArray))
			{
				// Check if there is something to return (and return it):
				$LanguagesCount = count($Languages);
				$TranslationRowsCount = count($TranslationsArray);
				$i=0;
				while ($i < $LanguagesCount)
				{
					$j=0;
					while ($j < $TranslationRowsCount)
					{
						if (!empty($TranslationsArray[$j]['Lang_'.$Languages[$i]]))
						{
							if (!$Debug) // When testing we don't want too much data in the session.
							{
								if (!empty($Category))
								{
									$_SESSION['getWords'][$Category][$IDName] = stripslashes($TranslationsArray[$j]['Lang_'.$Languages[$i]]);
								}
								else
								{
									$_SESSION['getWords'][$IDName] = stripslashes($TranslationsArray[$j]['Lang_'.$Languages[$i]]);
								}
							}
							return stripslashes($TranslationsArray[$j]['Lang_'.$Languages[$i]]);
						}
						$j++;
					}
					$i++;
				}
			}
			// None of the preferred languages is found in the DB, return the $IDName:
			if ($Debug)
			{
				if ($getWordLinks && $MakeLink)
				{
					return '<a href="../Admin/Translations.php?Modus=Add&amp;IDName='.$IDName.'" target="_blank" title="Add the getWord to the database.">'.$IDName.'</a>'; // This allows easier searching for not or wrongly defined getWord IDs.
				}
				else
				{
					return "NoLangFor:".$IDName; // This allows easier searching for not or wrongly defined getWord IDs.
				}
			}
			else
			{
				// If there was nothing at all to return, return the argument itself (as most often this will be correct to do so; also, this probably poses no security risk):
				return $IDName;
			}
		}
	}

	// Establish a connection to the database:
	OpenDB($DataBase['Server'], $DataBase['Name'], $DataBase['User'], $DataBase['Password']);
	// Set the character set for MySQL output (i18n) to UniCode UTF-8:
	mysql_query("SET NAMES 'utf8' COLLATE 'utf8_general_ci'");

	// Set the LARGECAP two-letter ISO 639-1 alpha-2 code:
	$Language = (empty($_POST['Language']) ? '' : strtoupper(substr(trim($_POST['Language']), 0, 2))); // XSS prevention.
	if (!empty($Language))
	{
		$_SESSION['Language'] = $Language;
		// Reset any cached language-specific stuff:
		$_SESSION['getWords'] = false;
		unset($_SESSION['getWords']);
	}
	elseif (empty($_SESSION['Language']))
	{
		$Languages = getLanguages();
		$_SESSION['Language'] = $Languages[0];
	}

	// Set locale information:
	setlocale(LC_ALL, getWord('Messages', 'Locale'));
?>
