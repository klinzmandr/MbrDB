<!DOCTYPE html>
<html>
<head>
<title>MbrDB Janitor</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<?php
session_start();

//include 'Incls/vardump.inc';
include 'Incls/seccheck.inc';
include 'Incls/datautils.inc';			// NOTE: need to make sure the production database is being used.

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if ($action == "") {
print <<<pagePart1
<h3>MbrDB Janitor</h3>
<p>This utility is provided to take various actions to help maintain the database:</p>
<ul>
	<li><a class="btn btn-primary btn-xs" href="admlogtailer.php">Tail Database Activity Log</a><br />Log Tailer will display the last 15 log records in reverse chronological order and auto-refresh every 5 seconds.  This will allow the latest activity to be followed ('tailed').</li>
	<li><a class="btn btn-primary btn-xs" href="admdeleterecs.php?action=list">Delete Records</a><br />Delete an individual member, funding or correspondence record.</li>
	<li><a class="btn btn-primary btn-xs" href="admDBJanitor.php?action=list">Delete Expired Inactive</a><br />Delete all expired, inactive members records and their assoicated funding and correspondence records.  (NOTE: expired records are those marked as &apos;Inactive&apos; for longer than 90 days.)</li>
	<li><a class="btn btn-primary btn-xs" href="admDBJanitor.php?action=log">Maintain Log</a><br />Deletion of all system log entries dated over 30 days.</li>
	<li><a class="btn btn-primary btn-xs" href="admmbrsummaryloader.php">Load/Reload Summary Info to Mbr Records</a><br />Read all funding and corresondence records and update all corresponding member record summary fields with the appropriate information. (NOTE: member record fiels are updated dynamically when funding and/or correspondece records are entered.  This utility is just to make sure all fields are up to date.)</li>
	<li><a class="btn btn-primary btn-xs" href="admsetmemdate.php">Load MemDate</a>This page will search the membership database for all records that have a NULL value in the MemDate field and replace it with the earliest funding record date found from the donations table.</li>
	<li><a class="btn btn-primary btn-xs" href="admreassignfunding.php">Reassign Funding Record(s)</a><br />Re-assign the existing MCID for a one or more funding record(s) by replacing it with a new one.</li>
	<li><a class="btn btn-primary btn-xs" href="admreassigncorr.php">Reassign Correspondence Record(s)</a><br />Re-assign the existing MCID for a one or more correspondence record(s) by replacing it with a new one.</li>
	<li><a class="btn btn-primary btn-xs" href="admreassignMCIDs.php">Batch Reassignment of MICDs</a><br />Re-assignment of ALL the funding and correspondence records assoicated with an MCID to a different, existing MCID.  Usually the &apos;OLD&apos; MCID record is deleted after this action has been performed by using the 'Delete Records' function to complete the process.</li>
</ul>
<p>These actions are seperate actions that must be indiviually selected in this utility.</p>
<br />
<br />
<a class="btn btn-primary btn-success" href="javascript:self.close();">CLOSE</a><br />
pagePart1;
exit;
}

$expdate = date('Y-m-d',strtotime("-30 days"));
$reccnt = 0;
if ($action == 'phone') {
	echo "<h3>Phone number Maintenance</h3>";
	echo "Clean and set up phone numbers in member database<br>";
	echo "All phone nubers formatted as 123-456-7890<br>";
	$sql = "SELECT `MCID`,`PrimaryPhone` from `members`;";
	$res = doSQLsubmitted($sql);
	$rowcnt = $res->num_rows;
	while ($r = $res->fetch_assoc()) {
		$flds = array(); $phone = '';
		$mcid = $r[MCID]; $phone = $r[PrimaryPhone];
		//echo "Before - MCID: $mcid, Phone: $phone<br>";
		$phone = preg_replace("/[\(\)\.\-\ \/]/i", "", $phone);
		if ($phone != '') {
		  if (is_numeric($phone)) {
				$newphone = substr($phone,0,3) . '-' . substr($phone,3,3) . '-' . substr($phone,6,4);
				$flds[PrimaryPhone] = $newphone;
				$reccnt++;
				}
			else { 
				$flds[PrimaryPhone] = '';
				$notnumber++;
				}
			//echo "After - MCID: $mcid, Phone: " . $flds[PrimaryPhone] . "<br>";
			sqlupdate('members', $flds, "`MCID` = '$mcid'");
			}
		}
	echo "Total Records Read: $rowcnt<br>Total records formatted: $reccnt<br>Total records not numeric: $notnumber<br>";
	echo "<a class=\"btn\" href=\"admDBJanitor.php?action=\">Done</a>";
	exit();
	}
if ($action == 'log') {
	$sql = "DELETE from `log` where `DateTime` <= '$expdate';";
	$del_log_count = doSQLsubmitted($sql);
	echo "<h3>Maintian Log File</h3>";
	echo "Deletion of log file completed.<br />Deleted $del_log_count log records<br />";
	echo "<a class=\"btn\" href=\"admDBJanitor.php?action=\">Done</a>";
	exit();
	}
// identify and delete expired members older than expiration date
$expdate = date('Y-m-d', strtotime("-90 days"));

// list all expired inactive member records and their assoicated funding, corresondence and time records
if ($action == 'list') {
	print <<<pagePart2
	<h3>List summary of all candidate records.<br></h3>
	<a class="btn" href="admDBJanitor.php?action=delete">Continue</a>
	<a class="btn" href="admDBJanitor.php?action=">Start Over</a><br />
pagePart2;
	$sql = "SELECT * from `members` where `Inactivedate` <= '$expdate';";
	$res = doSQLsubmitted($sql);
	$mbrsfound = $res->num_rows;
	if ($mbrsfound == 0) {
		echo "<h3>No expired member recordsfound</h3>";
		echo "<p>Expired member records are those that have been marked as &apos;Inactive&apos; from more than 90 days from todays date.</p>";
		echo "<a class=\"btn\" href=\"admDBJanitor.php?action=\">Done</a>";
		exit();
		}
	echo 'MCIDs to delete:<br />';
	while ($r = $res->fetch_assoc()) {
		$mcid = $r[MCID];
		echo "&nbsp;&nbsp;$mcid<br />";
		$dsql = "SELECT * from `donations` where `MCID` = '$mcid';";
		$dres = doSQLsubmitted($dsql);
		$donationsfound = $dres->num_rows;
		$doncount += $donationsfound;
		$csql = "SELECT * from `correspondence` where `MCID` = '$mcid';";
		$cres = doSQLsubmitted($csql);
		$corrfound = $cres->num_rows;
		$corrcount += $corrfound;
		$tsql = "SELECT * from `voltime` where `MCID` = '$mcid';";
		$tres = doSQLsubmitted($tsql);
		$timefound = $tres->num_rows;
		$timecount += $timefound;
		}
	echo "total member table rows to delete: $mbrsfound<br>";
	echo "donations table rows to delete: $doncount<br>";
	echo "correspondence table rows to delete: $corrcount<br>";
	echo "voltime table rows to delete: $timecount<br />";
	}
	
if ($action == 'delete') {
	print <<<pagePart3
	<h3>Delete and report summary of actions.</h3>
	<a class="btn" href="admDBJanitor?action=">Done</a>
pagePart3;
	$sql = "SELECT * from `members` where `Inactivedate` <= '$expdate';";
	$mres = doSQLsubmitted($sql);
	while ($r = $mres->fetch_assoc()) {
		$mcid = $r[MCID];
		//$mcid = "xxxxx";

		$dsql = "DELETE from `donations` where `MCID` = '$mcid';";
		$dcnt = doSQLsubmitted($dsql);
		$dcount += $dcnt;
	
		$csql = "DELETE from `correspondence` where `MCID` = '$mcid';";
		$ccnt = doSQLsubmitted($csql);
		$ccount += $ccnt;
		
		$tsql = "DELETE from `voltime` where `MCID` = '$mcid';";
		$tcnt = doSQLsubmitted($tsql);
		$tcount += $tcnt;
		}
	$sql = "DELETE from `members` where `Inactivedate` <= '$expdate';";
	$mbrcount = doSQLsubmitted($sql);
	
	print <<<pagePart4
	<h3>Completion Report</h3>
	member records deleted: $mbrcount<br>
	donation records deleted: $dcount<br>
	correspondence records deleted: $ccount<br>
	voltime records deleted: $tcount<br>
pagePart4;
	}


?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
