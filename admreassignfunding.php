<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Re-Assign MCID</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>

<?php
//include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
include 'Incls/datautils.inc.php';

$action = $_REQUEST['action'] ? $_REQUEST['action'] : "";

if ($action == '') {
	print <<<pagePart1
<h3>Reassignment of Funding Record MCIDs</h3>
<p>This utility will replace the MCID field of specified funding record(s) with a new one.  </p>
<p>Funding records to be reassigned are identified by individual record numbers entered into the input field seperated by commas.  </p>
<p>The new MCID is specified in the input field and must be one of an existing member record.  </p>
<p>The funding records identified will not be changed other than to have the new MCID replace the current one.  </p>

<a class="btn btn-warning" href="admreassignfunding.php?action=form">CONTINUE</a><br /><br />
<a class="btn btn-primary" href="admDBJanitor.php">CANCEL AND RETURN</a><br />
<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></body></html>

pagePart1;
	exit;
	}

if ($action == 'form') {
	echo '<h4>Provide the following parameters:</h4>';
	print <<<inputForm
<form class="form" action="admreassignfunding.php">
<p>Enter one or more record numbers separated with commas.  Please ensure these are the correct record numbers since this change has no undo option.</p>
Record numbers:<input autofocus name="recno" type="text" value=""><br />
New MCID: <input type="text" name="mcid" value="">
<input type="hidden" name="action" value="apply">
<input type="submit" name="submit" value="Submit"><br /><br />
<a class="btn btn-primary" href="admreassignfunding.php">RETURN</a>
</form>	
<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></body></html>

inputForm;
	exit;
	}

if ($action == 'apply') {
	$rn = $_REQUEST['recno']; $mcid = $_REQUEST['mcid'];
	$sql = "SELECT * FROM `members` WHERE `MCID` = '$mcid'";
	$res = doSQLsubmitted($sql);
	$rowcount = $res->num_rows;
	if ($rowcount == 0) {
		echo '<h3>MCID provided is not defined on a member record</h3>
		<a class="btn btn-warning" href="admreassignfunding.php?action=form">RESTART</a>';
		exit;
		}
	// mcid OK - let's apply it to the record(s)
	$r = $res->fetch_assoc();
	$mcid = $r[MCID];							// get the one in the record in upper case
	$rnarray = explode(',', $rn);	// put list of rec numbers into an array
	$flds[MCID] = $mcid;					// update field array just has mcid in it
	//echo '<pre> rec nbrs '; print_r($rnarray); echo '</pre>';
	echo '<h4>The following updates have been made:</h4>';
	foreach ($rnarray as $m) {
		$where = "`DonationID` = '$m'";
		sqlupdate("donations",$flds,$where);
		//echo "where: $where<br />";
		echo "Donation record $m updated, new MCID is $mcid<br />";
		}
	echo '<br />----- End of Update -----<br />';
	echo "<a class=\"btn btn-primary\" href=\"admDBJanitor.php\">RETURN</a><br />";
	}

?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
