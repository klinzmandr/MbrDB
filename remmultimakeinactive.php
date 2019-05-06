<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Reminders - Make MCIDs Inactive</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>

<?php
//include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
//include 'Incls/mainmenu.inc.php';
include 'Incls/datautils.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

echo "<h3>Set MCIDs Inactive&nbsp;&nbsp;&nbsp;<a class=\"btn btn-primary\" href=\"remmultiduesnotices.php\">RETURN</a></h3>";
if ($action == '') {
	echo '<div class="container"><p>The following is a list of Member ID\'s that have been submitted to be designated as \'Inactive\' members.  This action is performed by setting the respective member record field \'Inactive\' as TRUE and setting the field \'Inactivedate\' to today\'s date.</p>
<p>It is recommended that this list be reviewed and any member indentifier\'s that should not be set to \'Inactive\' should be unchecked before clicking the \'Continue\' button.</p>';

	echo '<h3>The following list of members will be made inactive.</h3>';
	$ma = array();
	$ma = $_REQUEST['inact'];
// process mail array contains list of MCIDs and email addresses
	if (count($ma) > 0) foreach ($ma as $v) {		// unpack the mcid and email address values into diff arrays
		//echo "v: $v<br />";
		list($mcidv, $emv) = explode(":", $v);
		$emailarray[] = $emv;
		$mcidarray[] = $mcidv;
		}
	echo "<form action=\"remmultimakeinactive.php\" method=\"post\">";
	if (count($mcidarray) > 0) foreach ($mcidarray as $mcid) {
		// echo "mcid: $mcid<br />";
		$sql = "SELECT * FROM `members` WHERE `MCID` = '$mcid';";
		$res = doSQLsubmitted($sql);
		$r = $res->fetch_assoc();
		echo "<input type=\"checkbox\" name=\"inact[]\" value=\"$mcid\" checked>$mcid: $r[NameLabel1stline]<br />";
		//echo '<pre> inactivate '; print_r($r); echo '</pre>';
		}
	echo "<br><input type=\"hidden\" name=\"action\" value=\"continue\">
	<input type=\"submit\" name=\"submit\" value=\"Continue\"></form></div>
	<script src=\"jquery.js\"></script><script src=\"js/bootstrap.min.js\"></script></body></html>";
	exit;
	}

// apply updates to the list of MCIDs making all inactive
if ($action == 'continue') {	
$today = date('Y-m-d', strtotime('now'));
echo "<div class=\"container\">
<h3>The following list of member id's have been made 'INACTIVE' as of $today</h3>";
$mcids = array();
$mcids = $_REQUEST['inact'];
$updarray = array();
if (count($mcids) > 0) foreach ($mcids as $m) {
	echo "$m, ";
	$updarray['Inactive'] = "TRUE";
	$updarray['Inactivedate'] = $today;
	sqlupdate('members',$updarray,"`MCID` = '$m'");				// set member fields
	//echo '<pre> member update '; print_r($updarray); echo '</pre>';
	$insarray['CorrespondenceType'] = 'MbrInactive';
	$insarray['DateSent'] = $today;
	$insarray['MCID'] = $m;
	$insarray['Notes'] = 'Member set inactive by reminder system';
	sqlinsert('correspondence', $insarray); 
	}
else echo "<h3>No MCIDs were made inactive.</h3>";

echo "<br /><a class=\"btn btn-danger\" href=\"remmultiduesnotices.php\">DONE</a>";

}

?>
</div>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
