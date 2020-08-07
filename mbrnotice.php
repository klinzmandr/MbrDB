<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Membership Notice</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>
$("document").ready( function() {
  $("#info").hide();
  $("#infobtn").click( function() {
    $("#info").toggle();
    });
  });

</script>
<?php
//include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
include 'Incls/mainmenu.inc.php';
include 'Incls/datautils.inc.php';

$items = isset($_REQUEST['items']) ? $_REQUEST['items'] : "";
$itemcount = isset($_REQUEST['itemcount']) ? $_REQUEST['itemcount'] : "";
$total = isset($_REQUEST['total']) ? $_REQUEST['total'] : "";
$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "";

$tname = isset($_REQUEST['template']) ? $_REQUEST['template'] : "";
$mcid = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : $_SESSION['ActiveMCID'];
$_SESSION['ActiveMCID'] = $mcid;

$templatedir = 'templates/letters';
?>

<div class=container>
<div id="info">
	<h3>Membership Notices</h3> 
	<p>This page will assist in the development of printing a label and/or letter notice to mail to the member whose membership has expired. Usually, this page is used after selection of an MCID from the &quot;Display Expired&quot; or the &quot;List In-Progress Reminders&quot; listing.</p>
	<p>It should be noted that a this notification process can NOT be initiated to send a notice to a member that does not have an expired membership. It should also be noted that an automatic entry to the members correspondence records is done as a part of completing this process that will record that the notice has been sent.</p>
	<p>The templates listed allow different messages to be accessed before being sent to the member.  Usually these message are sent in relation to an expired membership.  The selected letter template plus other mailing information is placed in the &quot;labelsandletters&quot; table with the current date.</p>
<p>Labels and letters are selected by creation date, editted and printed using LibreOffice using the appropriate label and/or letter templates accessed from the &quot;labelsandletters&quot; table of the database.</p>
<p>It should be noted that information in the &quot;labelsandletters&quot; table of the database and must be printed and sent as a separate action.  This facility will automatically add a note in the members correspondence log that this action has taken place as of this date.</p>
</div> <!-- info -->
</div>
<?php
// first let's make sure that the member is not expired
	$date = calcexpirationdate();																				// exp period: 11 months
	$sql = "SELECT * from `donations` WHERE `MCID` = '$mcid' AND `Purpose` = 'dues' AND `DonationDate` > '$date'";
	$results = doSQLsubmitted($sql);				

// parse out those rows to just show the latest payment made
	$results->data_seek(0);
	$nbr_rows = $results->num_rows;

	
	if ($type != 'receipt') {
		if ($nbr_rows > 0) {				//	any row returned means dues payment made within exp. period
			$row = $results->fetch_assoc();
		//echo "<pre>"; print_r($row); echo "</pre>";
			print <<<expNotice
<h3>MCID <a href="mbrinfotabbed.php">$mcid</a> does NOT have an expired membership</h3>
<!-- <a class="btn btn-primary" href="mbrinfotabbed.php" name="filter" value="$mcid">CANCEL AND RETURN</a> -->
</div>
</body>
</html>
expNotice;
//			exit;
			}	
		}

// check if MCID is inactive
//echo "This is the active MCID: " . $_SESSION['ActiveMCID'] . "<br>";
$sql = "SELECT * FROM `members` WHERE MCID = '".$mcid."'";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
$row = $res->fetch_assoc();
if ($row['Inactive'] == 'TRUE') {
	echo "<h3>Member <a href=\"mbrinfotabbed.php\">$mcid</a> marked as inactive</h3>
	<p>Please update the record before proceeding.</p>
	</div></body></html>";
	exit;
	}

// does MCID want to get mail
//echo "<pre>MCID record"; print_r($row); echo "</pre>";
if ($row['Mail'] == 'FALSE') {
print <<<noNotice
<h3>Member <a href="mbrinfotabbed.php">$mcid</a> does not want to receive any correspondence from PWC.</h3><br>
<!-- <a class="btn btn-primary" href="mbrinfotabbed.php" name="filter" value="$mcid">CANCEL AND RETURN</a> -->
</div></body></html>
noNotice;
	exit;
	}

// check if mailing infor is complete
if ((strlen($row['NameLabel1stline']) == 0) OR (strlen($row['AddressLine']) == 0) OR (strlen($row['City']) == 0) OR (strlen($row['State']) == 0) OR (strlen($row['ZipCode']) == 0)) {
	echo "<h3>Mailing informamtion for member <a href=\"mbrinfotabbed.php\">$mcid</a> is incomplete.  Please correct this before proceeding.</h3>.<br />";
	//echo "<pre>dump of mbr info "; print_r($row); echo "</pre>";
	echo '
</div>
</body>
</html>';
	exit;
	}

// now we can list the template list for selection
if ($tname == "") {
// include 'Incls/vardump.inc.php';	
// echo "<pre>row "; print_r($row); echo "</pre>";	
$sql = "SELECT * FROM `templates` WHERE `Type` = 'mail';";
$res = doSQLsubmitted($sql);
print <<<tempForm1
<div class="container">
<button id="infobtn">More Info</button>
<h3>Membership Mail Notice</h3> 
<h4>Send a mail reminder to: <a href="mbrinfotabbed.php">$mcid</a></h4>
Select an Letter template from the selection list:<br>
<form action="mbrnotice.php" method="post">
<select name="template" onchange="this.form.submit()">
<option value=""></option>
tempForm1;
	while ($t = $res->fetch_assoc()) {
		$name = $t['Name']; $tid = $t[TID];
		echo "<option value=\"$tid\">$name</option>";
		}
print <<<tempForm2
</select>
<input type="hidden" name="mcid" value="$mcid">
<input type="hidden" name="items" value="$items">
<input type="hidden" name="itemcount" value="$itemcount">
<input type="hidden" name="total" value="$total">
<input type="hidden" name="type" value="receipt">
</form><br /><br />
</div>	
</div></body></html>
tempForm2;
	exit();
	}

// we are good, read the template and prep edit form
// template name given so read it and set up for edit form
//echo "read template and create edit form<br />"
echo "<div class=\"container\"><h3>Edit and Print the Message to ".$_SESSION['ActiveMCID']."</h3>";
$sql = "SELECT * FROM `templates` WHERE `TID` = '$tname';";
$tres = doSQLsubmitted($sql);
$t = $tres->fetch_assoc();
$templatename = stripslashes($t['Name']);
$template = stripslashes($t['Body']);

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
	if ($match == 'date') $newValue = date("F d, Y",strtotime('now'));
	if ($match == 'CorrSal') $newValue = $row['CorrSal'];
	if ($match == 'NameLabel1stline') $newValue = $row['NameLabel1stline'];
	if ($match == 'FName') $newValue = $row['FName'];
	if ($match == 'LName') $newValue = $row['LName'];
	if ($match == 'AddressLine') $newValue = $row['AddressLine'];
	if ($match == 'City') $newValue = $row['City']; 
	if ($match == 'State') $newValue = $row['State']; 
	if ($match == 'ZipCode') $newValue = $row['ZipCode'];
	if ($match == 'Organization') $newValue = $row['Organization']; 
	$template = str_replace($matches[0][$i], $newValue, $template);
	//echo "template: $template<br>";
	$newValue = '';
	}

print <<<editForm
<script type="text/javascript" src="js/nicEdit.js"></script>
<script type="text/javascript">
bkLib.onDomLoaded(function() {
	new nicEditor({buttonList : ['fontSize', 'fontFormat', 'left', 'center', 'right', 	'bold','italic','underline','indent', 'outdent', 'ul', 'ol', 'hr', 'forecolor', 
	'bgcolor','link','unlink']}).panelInstance('area1');
});
</script>

Template Name: $templatename<br />
<form action="mbrnoticeupd.php" method="get"  class="form">
<textarea id="area1" name="Letter" rows="20" cols="80">$template</textarea><br />
<input type="hidden" name="MCID" value="$mcid">
<input type="hidden" name="Organization" value="$row[Organization]">
<input type="hidden" name="NameLabel1stline" value="$row[NameLabel1stline]">
<input type="hidden" name="AddressLine" value="$row[AddressLine]">
<input type="hidden" name="City" value="$row[City]">
<input type="hidden" name="State" value="$row[State]">
<input type="hidden" name="ZipCode" value="$row[ZipCode]">
<input type="hidden" name="CorrSal" value="$row[CorrSal]">
<input type="hidden" name="Notes" value="$templatename">
<input type="submit" name="submit" value="Submit">
<form><br /><br />

editForm;

?>
</div>
</body>
</html>
