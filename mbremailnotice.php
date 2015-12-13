<html>
<head>
<title>Email Notification</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body onchange="flagChange()">

<?php
session_start();
//include 'Incls/vardump.inc';
include 'Incls/seccheck.inc';
include 'Incls/mainmenu.inc';
include 'Incls/datautils.inc';

$items = isset($_REQUEST['items']) ? $_REQUEST['items'] : "";
$itemcount = isset($_REQUEST['itemcount']) ? $_REQUEST['itemcount'] : "";
$total = isset($_REQUEST['total']) ? $_REQUEST['total'] : "";
$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "";

$tname = isset($_REQUEST['template']) ? $_REQUEST['template'] : "";
$mcid = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : $_SESSION['ActiveMCID'];
$_SESSION['ActiveMCID'] = $mcid;

if ($mcid == "") {
	//check if there is an active MCID, bail if not, access the members info for name and email address info.
	$mcinfo = "<div class=container>";
	$mcinfo .= "<h3>Membership Email Notices</h3>"; 
	$mcinfo .= "<p>This page will assist in the development of an email notice being generated to mail to the member whose membership has expired.  Usually, this page is used after selection of an MCID from the \"Display Expired\" or the \"List In-Progress Reminders\" listing.  It should be noted that a reminder email notification can NOT be sent to a member that does not have an expired membership.  It should also be noted that an automatic entry to the members correspondence records is done as a part of sending this email notification.</p>";
	$mcinfo .= "<p>The templates listed allow different messages to be accessed and editted before being sent to the members email address.</p><p>Usually these message are sent in relation to an expired membership.</p><p>It should be noted that an email notice sent using this facility will automatically add a notice in the members correspondence log.</p>";
	echo $mcinfo;
	echo '<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></div></body></html>';
	exit;
	}

// first let's make sure that the member is expired
	$date = calcexpirationdate();																				// exp period: 11 months
	$sql = "SELECT * from `donations` WHERE `MCID` = '$mcid' AND `Purpose` = 'dues' AND `DonationDate` > '$date'";
	$results = doSQLsubmitted($sql);				
	
	//$date = date('Y-m-01', strtotime('-11 months'));									// this is the expiration period
	//$results = readDuesOwedrecords($date);

// parse out those rows to just show the latest payment made
	$results->data_seek(0);
	$nbr_rows = $results->num_rows;
	
	if ($type != 'receipt') {
		if ($nbr_rows > 0) {				//	any row returned means dues payment made within exp. period
			print <<<expNotice
<h3>PLEASE NOTE: MCID <a href="mbrinfotabbed.php">$mcid</a> does NOT have an expired membership</h3>

<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></div></body></html>
expNotice;
//		exit;
			}
		}

//echo "This is the active MCID: " . $_SESSION['ActiveMCID'] . "<br>";
$sql = "SELECT * FROM `members` WHERE MCID = '$mcid'";
$res = doSQLsubmitted($sql);
//$res = readMCIDrow($mcid);
$row = $res->fetch_assoc();
//echo "<pre>MCID record"; print_r($row); echo "</pre>";

// check if MCID is Inactive

// check if MCID is 
if ($row['Inactive'] == 'TRUE') {
	echo "<h3>Member <a href=\"mbrinfotabbed.php\">$mcid</a> is Inactive.</h3>
	Please update the record before proceedding.<br>";
	echo '</div>   <!-- containerx -->
<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></div></body></html>';
	exit;
	}

//check if MCID is OK with email and has an email address
$emaddr = $row['EmailAddress']; $emailok = $row[E_Mail];
if (($emaddr == "") OR ($emailok == 'FALSE')) {
	echo "<h3>Member <a href=\"mbrinfotabbed.php\">$mcid</a> does not have any email addresses on file or does not wish to get email.</h3>.<br />";
	//echo "<a class=\"btn btn-primary\" href=\"mbrinfotabbed.php\" name=\"filter\" value=\"$mcid\">CANCEL AND RETURN</a>";
	echo '</div>   <!-- containerx -->
<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></div></body></html>';
	exit;
	}

if ($tname == '') {
// now we can list the template list for selection
	$sql = "SELECT * FROM `templates` WHERE `Type` = 'email';";
	$res = doSQLsubmitted($sql);
	print <<<templForm1
<div class="container"><h3>Membership Email Notice</h3>
<h4>Send a email reminder to: <a href="mbrinfotabbed.php">$mcid</a></h4>
Select an email template from the selection list:<br>
<form action="mbremailnotice.php" method="post">
<select name="template">
<option value=""></option>
templForm1;
	while ($t = $res->fetch_assoc()) {
		$name = $t[Name];
		$recno = $t[TID];
		if (substr($t,0,1) == '.') continue;
		echo "<option value=\"$recno\">$name</option>";
		}
print <<<templForm2
</select>
<input type="hidden" name="mcid" value="$mcid">
<input type="hidden" name="items" value="$items">
<input type="hidden" name="itemcount" value="$itemcount">
<input type="hidden" name="total" value="$total">
<input type="hidden" name="type" value="receipt">
<input type="submit" name="submit" value="Submit">
</form>	
<br><br>
</div>
<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></div></body></html>

templForm2;
	exit();
	}

// list template options in a form and pick one to use 
if ($tname != "") {
// include 'Incls/vardump.inc';	
// echo "<pre>row "; print_r($row); echo "</pre>";
echo "<div class=\"container\"><h3>Edit and Send the Message to ".$_SESSION['ActiveMCID']."</h3>";
$sql = "SELECT * FROM `templates` WHERE `TID` = '$tname';";
$res = doSQLsubmitted($sql);
$t = $res->fetch_assoc();
$template = stripslashes($t[Body]);
$templatename = stripslashes($t[Name]);
$em= $mcid . ':' . $row[FName] . " " . $row[LName] . " <" . $row[EmailAddress] . ">";
$emx= $row[FName] . " " . $row[LName] . " <" . $row[EmailAddress] . ">";
$emx = htmlentities($emx);
$fromaddr = $EmailFROM;			// defined in datautils.inc

// perform shortcode translations
$regex = "/\[(.*?)\]/";
preg_match_all($regex, $template, $matches);
// echo "<pre>matches "; print_r($matches); echo "</pre>";
// echo "<pre>row "; print_r($row); echo "</pre>";
for ($i = 0; $i < count($matches[1]); $i++) {
	$match = rtrim($matches[1][$i]);
	if (strpos($match, 'EmailAddress') !== false) $newValue = $row[EmailAddress];
	if ($match == 'total') $newValue = $total;
	if ($match == 'itemcount') $newValue = $itemcount; 
	if ($match == 'date') $newValue = date("F d, Y",strtotime(now));
	if ($match == 'CorrSal') $newValue = $row[CorrSal];
	if ($match == 'NameLabel1stline') $newValue = $row[NameLabel1stline];
	if ($match == 'FName') $newValue = $row[FName];
	if ($match == 'LName') $newValue = $row[LName];
	if ($match == 'AddressLine') $newValue = $row[AddressLine];
	if ($match == 'City') $newValue = $row[City]; 
	if ($match == 'State') $newValue = $row[State]; 
	if ($match == 'ZipCode') $newValue = $row[ZipCode];
	if ($match == 'Organization') $newValue = $row[Organization]; 
	$template = str_replace($matches[0][$i], $newValue, $template);
	//echo "template: $template<br>";
	$newValue = '';
	}
print <<<formPart1
<script type="text/javascript" src="js/nicEdit.js"></script>
<script type="text/javascript">
bkLib.onDomLoaded(function() {
	new nicEditor({buttonList : ['fontSize', 'fontFormat', 'left', 'center', 'right', 	'bold','italic','underline','indent', 'outdent', 'ul', 'ol', 'hr', 'forecolor', 
	'bgcolor','link','unlink']}).panelInstance('area1');
});
</script>

<br />
To: $emx<br />
From: $fromaddr<br />
<br />
<form class="form" action="mbremailnoticesend.php" method="post">
Subject:<br />
<input type="text" name="subject" value="$templatename" style="width: 500; "  placeholder="Subject" /><br />
Message:<br />
<textarea id="area1" name="body" rows="8" cols="100">$template</textarea><br />
<input type="hidden" name="to" value="$emaddr">
<input type="hidden" name="from" value="$fromaddr">
<input type ="submit" name="Submit" value="Send"><br />
<input type="reset" name="reset" value="Reset Form" />
</form>
formPart1;

//echo "template file name: $templateaddress<br />";
//echo "<pre>template file "; print_r($template); echo "</pre>";
}
?>
<!-- <a class="btn btn-primary" href="mbrinfotabbed.php" name="filter" value="--none--">CANCEL AND RETURN</a> -->
</div>   <!-- containerx -->
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>
