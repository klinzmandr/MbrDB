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
session_start();
//include 'Incls/vardump.inc';
include 'Incls/seccheck.inc';
include 'Incls/datautils.inc';

$action = $_REQUEST['action'] ? $_REQUEST['action'] : "";

if ($action == '') {
	print <<<pagePart1
<h3>Reassignment of MCIDs on Funding and Corresondence Records</h3>
<p>This utility will replace the current MCID field of all existing funding and corresondence record(s) with the new one specified.  </p>
<p>The new MCID must be exist on the database before this utility will proceed.  Use the 'Add New MCID' function on the main menu if necessary.</p>
<p>A preliminary report of all existing records assigned to the existing MCID will be presented for review before any changes are made.  It is recommended that this report be printed in case any reversal actions are necessary.  Reversals must be on a record by record basis using the record number provided.</p>
<p>The records identified will not be changed other than to have the new MCID replace the current one.</p>

<a class="btn btn-warning" href="admreassignMCIDs.php?action=form">CONTINUE</a><br /><br />
<a class="btn btn-primary" href="admDBJanitor.php">CANCEL AND RETURN</a><br />
<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></body></html>

pagePart1;
	exit;
	}

if ($action == 'form') {
	echo '<h2>Provide the following parameters:</h2>';
	print <<<inputForm
<form class="form" action="admreassignMCIDs.php">
Enter the existing MCID:
<input autofocus name="oldmcid" type="text" value=""><br />
Enter the new MCID: <input type="text" name="newmcid" value="">
<input type="hidden" name="action" value="report">
<input type="submit" name="submit" value="Submit"><br /><br />
<a class="btn btn-primary" href="admreassignMCIDs.php">RETURN</a>
</form>	
<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></body></html>

inputForm;
	exit;
	}

if ($action == 'report') {
	echo '<h2>The following records listed will be modified by this action.</h2>';
	$oldmcid = $_REQUEST['oldmcid']; $newmcid = $_REQUEST['newmcid'];
	//echo "oldmcid: $oldmcid, newmcid: $newmcid<br />";
	$sql = "SELECT * FROM `members` WHERE `MCID` = '$newmcid'";
	//echo "sql: $sql<br />";
	$res = doSQLsubmitted($sql);
	$rowcount = $res->num_rows;
	//echo "rowcount: $rowcount<br />";
	if ($rowcount == 0) {
		echo '<h3>New MCID provided is not defined on a member record</h3>
		<a class="btn btn-warning" href="admreassignMCIDs.php?action=form">RESTART</a>';
		exit;
		}
	$sql = "SELECT * FROM `members` WHERE `MCID` = '$oldmcid'";
	$res = doSQLsubmitted($sql);
	$rowcount = $res->num_rows;
	if ($rowcount == 0) {
		echo '<h3>Existing MCID provided is not defined on a member record</h3>
		<a class="btn btn-warning" href="admreassignMCIDs.php?action=form">RESTART</a>';
		exit;
		}
	// mcids OK - let's report impacted funding records on old MCID
	$sql = "SELECT * FROM `donations` WHERE `MCID` = '$oldmcid';";
	$res = doSQLsubmitted($sql);
	$donrows = $res->num_rows;
	if ($donrows > 0) {
		echo "<h4>Funding Records for $oldmcid: the following $donrows will be modified:</h4>";
		while ($r = $res->fetch_assoc()) {
			echo "$r[DonationID], ";
			}
		}
	else echo "<h4>No donation records assoicated with the existing MCID $oldmcid</h4>";
	// report impacted corresondence records on old MCID
	$sql = "SELECT * FROM `correspondence` WHERE `MCID` = '$oldmcid';";
	$res = doSQLsubmitted($sql);
	$corrows = $res->num_rows;
	//echo "corrows: $corrows<br />";
	if ($corrows > 0) {
		echo "<h4>Correspondence Records for $oldmcid: the following $corrows will be modified:</h4>";
		while ($r = $res->fetch_assoc()) {
			echo "$r[CORID], ";
			}
		}
	else echo "<h4>No corresondence records assoicated with the existing MCID $oldmcid</h4>";
	echo '<h3>This list should be printed before proceeding in case any reversal of this action is necessary.</h3>----- End of Report -----<br />';
	echo "<a class=\"btn btn-success\" href=\"admreassignMCIDs.php?action=apply&oldmcid=$oldmcid&newmcid=$newmcid\">CONTINUE</a><br /><br />";
	echo "<a class=\"btn btn-danger\" href=\"admDBJanitor.php\">CANCEL AND RETURN</a><br />";
	}		// action = 'report'

// apply changes as requested......
if ($action == 'apply') {
	$oldmcid = $_REQUEST['oldmcid']; $newmcid = $_REQUEST['newmcid'];
	$newmcid = strtoupper($newmcid);
	//echo "oldmcid: $oldmcid, newmcid: $newmcid<br />";
	$newfld[MCID] = $newmcid;
	$where = "`MCID` = '$oldmcid'";
	$donrows = 0; $corrows = 0;
	$donrows = sqlupdate('donations', $newfld, $where);
	$corrows = sqlupdate('correspondence', $newfld, $where);
	echo "<h4>Update completed successfully</h4>
	Donation records updated: $donrows<br />
	Corresondence records changed: $corrows<br /><br />";
	echo "<a class=\"btn btn-primary\" href=\"admDBJanitor.php\">RETURN</a><br />";
	}

?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
