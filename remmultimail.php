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
<body onchange="flagChange()">

<?php
//include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
include 'Incls/mainmenu.inc.php';
include 'Incls/datautils.inc.php';

$tname = isset($_REQUEST['template']) ? $_REQUEST['template'] : "";
$inarray = array();
$inarray = $_REQUEST['mail'];

// process mail array contains list of MCIDs and email addresses
foreach ($inarray as $v) {		// unpack the mcid and email address values into diff arrays
	//echo "v: $v<br />";
	list($mcidv, $emv) = explode(":", $v);
	$emailarray[] = $emv;
	$mcidarray[] = $mcidv;
	}

$templatedir = 'templates/letters';

if ($tname == "") {
// now we can list the template list for selection
	$sql = "SELECT * FROM `templates` WHERE `Type` = 'mail';";
	$res = doSQLsubmitted($sql);
	print <<<tempForm1
<script>
function canit() {
//  alert("canit entered");
  if (confirm("Do you REALLY want to CANCEL this action?\\n\\nCLICK OK to CANCEL this form and RETURN to reminders list.")) { return true; }
  return false;
}
</script>
<div class="container"><h3>Membership Mail Notice</h3> 
<h4>Send Mail Reminders&nbsp;&nbsp;<a onclick="return canit()" class="btn btn-primary" href="remmultiduesnotices.php">CANCEL & RETURN</a></h4>
Select a letter template from the selection list:<br>
<form action="remmultimail.php" method="post">
tempForm1;
	echo "<select name=\"template\">";
	echo '<option value=""></option>';
	while ($t = $res->fetch_assoc()) {
		$name = $t['Name']; $tid = $t['TID'];
		echo "<option value=\"$tid\">$name</option>";
		}
	echo '</select>';
	echo '<br />to send to:<br />';
	foreach ($mcidarray as $v) { 		// get info for each mcid to allow review and delselection 
		$sql = "SELECT * FROM `members` WHERE `MCID` = '$v';";
		$res = doSQLsubmitted($sql);
		$r = $res->fetch_assoc();
		
		echo "<input type=\"checkbox\" name=\"mail[]\" value=\"$v\" checked>$v: $r[NameLabel1stline]<br />";
		
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
//echo '<pre> Template Name '; print_r($t['Name']); echo '</pre>';
//echo '<pre> Template Body '; print_r($t['Body']); echo '</pre>';

print <<<scrPart
<script type="text/javascript" src="js/nicEdit.js"></script>
<script type="text/javascript">
bkLib.onDomLoaded(function() {
	new nicEditor({buttonList : ['fontSize', 'fontFormat', 'left', 'center', 'right', 	'bold','italic','underline','indent', 'outdent', 'ul', 'ol', 'hr', 'forecolor', 
	'bgcolor','link','unlink']}).panelInstance('area1');
});
</script>
scrPart;

echo "<form action=\"remmultimailupd.php\" method=\"post\"  class=\"form\">";
foreach ($mcidarray as $m) {
	$sql = "SELECT * FROM `members` WHERE `MCID` = '$m';";
	$mres = doSQLsubmitted($sql);
	$row = $mres->fetch_assoc();
	// check if mailing info is complete
	if ((strlen($row['NameLabel1stline']) == 0) 
			OR (strlen($row['AddressLine']) == 0) 
			OR (strlen($row['City']) == 0) 
			OR (strlen($row['State']) == 0) 
			OR (strlen($row['ZipCode']) == 0)) {
		echo "<h4>Mailing informamtion for member $m is incomplete.  
			Please correct this before sending to this member.</h4>";
		//echo "<pre>dump of mbr info "; print_r($row); echo "</pre>";
		}
	else 
		echo "<input type=\"checkbox\" name=\"mail[]\" value=".$row['MCID']." checked>".$row['MCID'].' '.$row['NameLabel1stline'].'<br />';
	}

// read the template and prep edit form
$sql = "SELECT * FROM `templates` WHERE `TID` = '$tid' AND `Type` = 'mail';";
$res = doSQLsubmitted($sql);
$t = $res->fetch_assoc();
$templatename = stripslashes($t['Name']);
$templatebody = stripslashes($t['Body']);
$temprecno = $t['TID'];

//$org = $row['Organization']; $name = $row['NameLabel1stline']; $addr = $row['AddressLine'];
//$city = $row['City']; $state = $row['State']; $zip = $row['ZipCode']; $corrsal = $row['CorrSal'];

// perform shortcode translations
$regex = "/\[(.*?)\]/";
preg_match_all($regex, $templatebody, $matches);
// echo "<pre>matches "; print_r($matches); echo "</pre>";
// echo "<pre>row "; print_r($row); echo "</pre>";
for ($i = 0; $i < count($matches[1]); $i++) {
	$match = rtrim($matches[1][$i]);
	if (strpos($match, 'EmailAddress') !== false) $newValue = $row['EmailAddress'];
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
	$templatebody = str_replace($matches[0][$i], $newValue, $templatebody);
	//echo "templatebody: $templatebody<br>";
	$newValue = '';
	}

print <<<editForm
<h4>Edit Subject and Message</h4>
<input type="text" name="Topic" value="$templatename" style="width: 650px;" placeholder="Subject"><br>
<textarea id="area1" name="Letter" rows="10" cols="80">$templatebody</textarea><br />
<input type="submit" name="submit" value="Submit">
</form>
<br /><br />

editForm;

?>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>
