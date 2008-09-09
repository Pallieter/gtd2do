<?php
	require_once(realpath('Protected/Included.inc'));
	if (empty($_GET['Token']))
	{
		echo 'ERROR: no token';
	}
	else
	{
		$TaskNum = 1;
		$RSSStr = 'SELECT ID, UID, Name FROM '.$DataBase['TablePrefix'].'Users WHERE TokenRSS = "'.$_GET['Token'].'" LIMIT 1';
		$RSSRes = mysql_query($RSSStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$RSSStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
		if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$RSSStr.'<br />File: '.__FILE__.' on line: '.(__LINE__)."<br />\n"; }
		$RSSRow = mysql_fetch_assoc($RSSRes);
		echo '<?xml version="1.0"?>';
		echo '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
   		echo '<channel>';
      			echo '<title>GIT2Do RSS Feed</title>';
      			//echo '<link>'.$DomainName.'</link>';
      			echo '<link>http://'.$DomainName.'/</link>';
      			echo '<atom:link href="http://'.$DomainName.'/RSS.php" rel="self" type="application/rss+xml" />';
      			echo '<description>GIT2DO</description>';
      			echo '<language>en-us</language>';
      			echo '<pubDate>'.date('r').'</pubDate>'; // Current time.
			$LastDateStr = 'SELECT DateCreated FROM '.$DataBase['TablePrefix'].'Tasks WHERE UserID = '.$RSSRow['ID'].' ORDER BY DateCreated DESC LIMIT 1';
			$LastDateRes = mysql_query($LastDateStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$LastDateStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
			if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$LastDateStr.'<br />File: '.__FILE__.' on line: '.(__LINE__)."<br />\n"; }
			$LastDateRow = mysql_fetch_assoc($LastDateRes);
			echo '<lastBuildDate>'.date('r', strtotime($LastDateRow['DateCreated'])).'</lastBuildDate>'; // Time of the last task in this feed.
      			echo '<docs>http://blogs.law.harvard.edu/tech/rss</docs>';
      			echo '<generator>PHP/MySQL - GTD2Do</generator>';
      			echo '<managingEditor>'.$RSSRow['UID'].' ('.$RSSRow['Name'].')</managingEditor>'; // 2Do: Email from the user
      			echo '<webMaster>webmaster@'.$DomainName.' (GTD2Do)</webMaster>';
      	  		$RSSTasksStr = 'SELECT * FROM '.$DataBase['TablePrefix'].'Tasks WHERE USERID = '.$RSSRow['ID'].' ORDER BY DateCreated DESC Limit 10';
			$RSSTasksRes = mysql_query($RSSTasksStr) or die ('MySQL Error: '.mysql_error().'<br />MySQL Query: '.$RSSTasksStr.'<br />File: '.__FILE__.' on line: '.(__LINE__ - 1));
			if ($Debug) { $_SESSION['Debug'][] = 'MySQL Query: '.$RSSTasksStr.'<br />File: '.__FILE__.' on line: '.(__LINE__)."<br />\n"; }
      	  		while ($RSSTasksRow = mysql_fetch_assoc($RSSTasksRes))
			{
      	  			echo '<item>';
         			echo '<title>'.$RSSTasksRow['Name'].'</title>';
         			echo '<link>http://'.$DomainName.'/Index.php?Page=Edit&amp;Action='.$RSSTasksRow['ID'].'</link>';
         			echo '<description>Task Num:'.$TaskNum.'</description>';
         			echo '<pubDate>'.date('r', strtotime($RSSTasksRow['DateCreated'])).'</pubDate>';
         			echo '<guid>http://liftoff.msfc.nasa.gov/2003/06/03.html#'.$RSSTasksRow['ID'].'</guid>';
				echo '</item>'."\n";
				$TaskNum++;
			}
   		echo '</channel>';
		echo '</rss>';
	}
?>