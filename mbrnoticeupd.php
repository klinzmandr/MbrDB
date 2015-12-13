<!DOCTYPE html>
<html>
<head>
<title>Lables and Letters Update</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- <link href="css/bootstrap.min.css" rel="stylesheet" media="screen"> -->
</head>
<body onchange="flagChange()">
<div class="container">
<h3>Lables and Letters Update</h3>
<p>This confirms that adding the member's mailing information and/or letter into the 'lablesandletters' table has been completed.  Use &apos;Reminders->Print Labels and Letters&apos; to print.</p><br />
<p>Click this button if this message remains.</p><br />
<a class="btn btn-primary" href="mbrinfotabbed.php" name="filter" value="--none--">RETURN</a>
<form action="mbrinfotabbed.php" name="FORM_NAME" method="post">
<input autofocus type="hidden" name="" value="" />
<input type="submit" />
</form>
<SCRIPT TYPE="text/JavaScript">document.forms["FORM_NAME"].submit();</SCRIPT> 
</div>

<?php
session_start();

$mcid = $_SESSION['ActiveMCID'];
//include 'Incls/vardump.inc';
include 'Incls/seccheck.inc';
include 'Incls/datautils.inc';

// add new row to table labelsandletters with info provided
$uri = $_SERVER['QUERY_STRING'];
parse_str($uri, $vararray);
unset($vararray[submit]);
//echo "<pre>array to db function: "; print_r($vararray); echo "</pre>";
$vararray[Date] = date('Y-m-d');
$vararray['Letter'] = addslashes($vararray['Letter']);
$notes = $vararray[Notes];
unset($vararray[Notes]);
$res = sqlinsert('labelsandletters', $vararray);

// add entry to correspondence table about this action
$fields[CorrespondenceType] = 'MailNotice';
$fields[DateSent] = date('Y-m-d');
$fields[MCID] = $mcid;
$fields[Reminders] = 'MailReminder';
$fields[Notes] = "Subject: $notes";
sqlinsert('correspondence', $fields);

// update member summary info
$mbrflds[LastCorrType] = $fields[CorrespondenceType];  
$mbrflds[LastCorrDate] = $fields[DateSent];
sqlupdate('members', $mbrflds, "`MCID` = '$mcid';");

?>

<!-- <script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script> -->
</div>
</body>
</html>
