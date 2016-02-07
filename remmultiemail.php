<!DOCTYPE html>
<html>
<head>
<title>Membership Notice</title>
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

$tname = isset($_REQUEST['template']) ? $_REQUEST['template'] : "";
$inarray = array();
$inarray = $_REQUEST['email'];

// process mail array contains list of MCIDs and email addresses
foreach ($inarray as $v) {		// unpack the mcid and email address values into diff arrays
	//echo "v: $v<br />";
	list($mcidv, $emv) = explode(":", $v);
	$emailarray[] = $emv;
	$mcidarray[] = $mcidv;
	}

if ($tname == "") {
// now we can list the template list for selection
	$sql = "SELECT * FROM `templates` WHERE `Type` = 'email';";
	$res = doSQLsubmitted($sql);
	print <<<tempForm1
<div class="container">
<h3>Membership Email Notice</h3> 
<h4>Send Email Reminders&nbsp;&nbsp;<a class="btn btn-primary btn-xs" href="remmultiduesnotices.php">RETURN</a></h4>
Select an Email template from the selection list:<br>
<form action="remmultiemail.php" method="post">
tempForm1;
	echo "<select name=\"template\">";
	echo '<option value=""></option>';
	while ($t = $res->fetch_assoc()) {
		$name = $t[Name]; $tid = $t[TID];
		echo "<option value=\"$tid\">$name</option>";
		}
	echo '</select>';
	echo '<br />to send to:<br />';
	foreach ($mcidarray as $v) { 		// get info for each mcid to allow review and delselection 
		$sql = "SELECT * FROM `members` WHERE `MCID` = '$v';";
		$res = doSQLsubmitted($sql);
		$r = $res->fetch_assoc();
		
		echo "<input type=\"checkbox\" name=\"email[]\" value=\"$v\" checked>$v: $r[NameLabel1stline]<br />";
		
		//echo '<pre> MCID '; print_r($v); echo '</pre>';
		}
		
	echo "<input type=\"submit\" name=\"submit\" value=\"Submit\">
	</form>
	<br /><br />";
	echo '</div><script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></div></body></html>';		
	exit;
}

// -------------- template name is supplied, read it for edit and list mcid's for review
$tid = $_REQUEST['template'];

echo "<div class=\"container\"><h4>Send To List&nbsp;&nbsp;<a class=\"btn btn-primary btn-xs\" href=\"remmultiduesnotices.php\">RETURN</a></h4>";
//echo '<pre> Template Name '; print_r($t[Name]); echo '</pre>';
//echo '<pre> Template Body '; print_r($t[Body]); echo '</pre>';

print <<<scrPart
<script type="text/javascript" src="js/nicEdit.js"></script>
<script type="text/javascript">
bkLib.onDomLoaded(function() {
	new nicEditor({buttonList : ['fontSize', 'fontFormat', 'left', 'center', 'right', 	'bold','italic','underline','indent', 'outdent', 'ul', 'ol', 'hr', 'forecolor', 
	'bgcolor','link','unlink']}).panelInstance('area1');
});
</script>
scrPart;

// read the template and prep edit form
$sql = "SELECT * FROM `templates` WHERE `TID` = '$tid' AND `Type` = 'email';";
$res = doSQLsubmitted($sql);
$t = $res->fetch_assoc();
$templatename = stripslashes($t[Name]);
$templatebody = stripslashes($t[Body]);
$temprecno = $t[TID];

// perform shortcode translations
$regex = "/\[(.*?)\]/";
preg_match_all($regex, $templatebody, $matches);
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
	$templatebody = str_replace($matches[0][$i], $newValue, $templatebody);
	//echo "templatebody: $templatebody<br>";
	$newValue = '';
	}

echo '
<h4>Edit Subject and Message</h4>
<form action="remmultiemailupd.php" method="post"  class="form">';
foreach ($mcidarray as $m) {
	$sql = "SELECT * FROM `members` WHERE `MCID` = '$m';";
	$mres = doSQLsubmitted($sql);
	$row = $mres->fetch_assoc();
	$emaddr = $row[MCID].":".$row[EmailAddress];
	echo "<input type=\"checkbox\" name=\"email[]\" value=\"$emaddr\" checked>".$row[MCID].' '.$row[NameLabel1stline].'<br />';
	}
echo '
<input type="text" name="Topic" value="'.$templatename.'" style="width: 650px;" placeholder="Subject"><br>
<textarea id="area1" name="Letter" rows="10" cols="100">'.$templatebody.'</textarea><br />
<input type="submit" name="submit" value="Submit">
<form><br /><br />';

//echo '<pre> templatebody '; print_r($templatebody); echo '</pre>';
?>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>
