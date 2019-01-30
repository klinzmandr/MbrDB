<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Lables and Letters Update</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body onchange="flagChange()">
<div class="container">
<h3>Lables and Letters Update</h3>
<p>This confirms that all the request to add the members listed to the 'lablesandletters' table has been completed, individual entries made to each members correspondence logs and the member records update with these actions.  Use &apos;Reminders->Print Labels and Letters&apos; to print these items.</p><br />

<?php
$mcidarray = array();
$mcidarray = $_REQUEST['mail'];
$letter = $_REQUEST['Letter'];
$topic = $_REQUEST['Topic'];

//include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
include 'Incls/datautils.inc.php';

// add new row to table labelsandletters with info provided
echo '<h4>';
foreach ($mcidarray as $m) {
	$sql = "SELECT * FROM `members` WHERE `MCID` = '$m';";
	$res = doSQLsubmitted($sql);
	$mr = $res->fetch_assoc();
	
	// update letters and labels table with mailing and label info
	$vararray[MCID] = $mr[MCID];
	$vararray[Date] = date('Y-m-d');
	$vararray[Organization] = $mr[Organization];
	$vararray[NameLabel1stline] = $mr[NameLabel1stline];
	$vararray[AddressLine] = $mr[AddressLine];
	$vararray[City] = $mr[City];
	$vararray[State] = $mr[State];
	$vararray[ZipCode] = $mr[ZipCode];
	$vararray[CorrSal] = $mr[CorrSal];
	$vararray[Letter] = addslashes($letter);
	sqlinsert('labelsandletters', $vararray);
	//echo '<pre> lettersandlabels '; print_r($vararray); echo '</pre>';
	
	// add entry to correspondence table about this action
	$fields[CorrespondenceType] = 'MailReminder';
	$fields[DateSent] = date('Y-m-d');
	$fields[MCID] = $mr[MCID];
	$fields[Reminders] = 'MailReminder';
	$fields[Notes] = "Mail reminder sent by reminder system";
	sqlinsert('correspondence', $fields);
	//echo '<pre> correspondence '; print_r($fields); echo '</pre>';
	
	// update member summary info
	$mbrflds[LastCorrType] = $fields[CorrespondenceType];  
	$mbrflds[LastCorrDate] = $fields[DateSent];
	sqlupdate('members', $mbrflds, "`MCID` = '$mcid';");
	//echo '<pre> members '; print_r($mbrflds); echo '</pre>';
	echo "$mr[MCID], ";
	}
echo '</hr>';
	
?>
<br><br><a class="btn btn-primary" href="remmultiduesnotices.php">RETURN</a>
<!-- <script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script> -->
</div>
</body>
</html>
