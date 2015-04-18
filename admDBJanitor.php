<!DOCTYPE html>
<html>
<head>
<title>MbrDB Janitor</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
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
	<li><a class="btn btn-primary btn-xs" href="admDBJanitor.php?action=list">Delete Expired Inactive</a><br />Delete inactive members records and their assoicated correspondence and EDI records. Inactive records are those marked as &apos;Inactive&apos; for longer than 90 days.<br>NOTE: associated funding and vol. time records are archived as MCID ZZZ99 and adding the original MCID into the notes field.</li>
	<li><a class="btn btn-primary btn-xs" href="admDBJanitor.php?action=log">Maintain Log</a><br />Deletion of all system log entries dated over 30 days.</li>
	<li><a class="btn btn-primary btn-xs" href="admmbrsummaryloader.php">Load/Reload Summary Info to Mbr Records</a><br />Read all funding and corresondence records and update all corresponding member record summary fields with the appropriate information. (NOTE: member record fiels are updated dynamically when funding and/or correspondece records are entered.  This utility is just to make sure all fields are up to date.)</li>
	<li><a class="btn btn-primary btn-xs" href="admsetmemdate.php">Load MemDate</a>This page will search the membership database for all records that have a NULL value in the MemDate field and replace it with the earliest funding record date found from the donations table.</li>
	<li><a class="btn btn-primary btn-xs" href="admreassignfunding.php">Reassign Funding Record(s)</a><br />Re-assign the existing MCID for a one or more funding record(s) by replacing it with a new one.</li>
	<li><a class="btn btn-primary btn-xs" href="admreassigncorr.php">Reassign Correspondence Record(s)</a><br />Re-assign the existing MCID for a one or more correspondence record(s) by replacing it with a new one.</li>
	<li><a class="btn btn-primary btn-xs" href="admreassignMCIDs.php">Batch Reassignment of MICDs</a><br />Re-assignment of ALL the funding, correspondence and vol time records assoicated with an MCID to a different, existing MCID.  Usually the &apos;OLD&apos; MCID record is deleted after this action has been performed by using the 'Delete Records' function to complete the process.</li>
</ul>
<p>These actions are seperate actions that must be indiviually selected in this utility.</p>
<br />
<br />
<a class="btn btn-primary btn-success" href="javascript:self.close();">CLOSE</a><br />
pagePart1;
exit;
}

$expdate = date('Y-m-d',strtotime("-30 days"));
//	echo "expdate: $expdate<br>";
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
	echo "<a class=\"btn btn-primary\" href=\"admDBJanitor.php?action=\">Done</a>";
	exit();
	}

// identify and delete members with exp dates older than 90 days from today
$expdate = date('Y-m-d', strtotime("-90 days"));

// list all expired inactive member records and their assoicated funding, corresondence and time records
if ($action == 'list') {
	$sd = date('Y-m-d H:i:s',strtotime(now));
	print <<<pagePart2
	<h3>List summary of all candidate records.<br></h3>
	<a class="btn btn-danger" href="admDBJanitor.php?action=delete">Continue</a>
	<a class="btn btn-primary" href="admDBJanitor.php?action=">Start Over</a><br />
pagePart2;
	$sql = "SELECT * from `members` where `Inactivedate` <= '$expdate';";
	$res = doSQLsubmitted($sql);
//	echo "sql: $sql<br>";
	$mbrsfound = $res->num_rows;
	if ($mbrsfound == 0) {
		echo "<h3>No expired member recordsfound</h3>";
		echo "<p>Expired member records are those that have been marked as &apos;Inactive&apos; from more than 90 days from todays date.</p>";
		echo "<a class=\"btn btn-primary\" href=\"admDBJanitor.php?action=\">Done</a>";
		exit();
		}
//	echo 'MCIDs to delete:<br />';
	while ($r = $res->fetch_assoc()) {
		$mcid = $r[MCID];
//		echo "&nbsp;&nbsp;$mcid<br />";
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
		$edisql = "SELECT * from `extradonorinfo` where `MCID` = '$mcid';";
		$edires = doSQLsubmitted($edisql);
		$edifound = $edires->num_rows;
		$edicount += $edifound;
		}
	echo "total member table rows to delete: $mbrsfound<br>";
	echo "donations table rows to <b>archive</b>: $doncount<br>";
	echo "correspondence table rows to delete: $corrcount<br>";
	echo "voltime table rows to <b>archive</b>: $timecount<br />";
	echo "extradonorinfo table rows to delete: $edicount<br />";
	}
	
if ($action == 'delete') {
	$sd = date('Y-m-d H:i:s',strtotime(now));
	print <<<pagePart3
	<h3>Delete and report summary of actions.</h3>
	<a class="btn btn-primary" href="admDBJanitor.php?action=">Done</a>
pagePart3;
	$sql = "SELECT * from `members` where `Inactivedate` <= '$expdate';";
	$mres = doSQLsubmitted($sql);
	while ($r = $mres->fetch_assoc()) {
		$mcid = $r[MCID];

//	archive donation rows		
		$sql = "UPDATE `donations` SET `MCID`='ZZZ99', `Note`=CONCAT_WS(',',\"PREVMCID:$mcid \",`Note`) WHERE `MCID` = '$mcid'";
		$dres = doSQLsubmitted($sql);
		$dcount += $dres->affected_rows;

// delete correspondence rows		
		$csql = "DELETE from `correspondence` where `MCID` = '$mcid';";
		$ccnt = doSQLsubmitted($csql);
		$ccount += $ccnt;

// archive vol time rows		
		$vtsql = "UPDATE `voltime` SET `MCID`='ZZZ99', `VolNotes`=CONCAT_WS(',',\"PREVMCID:$mcid \", `VolNotes`) WHERE `MCID` = '$mcid'";
		$vtres = doSQLsubmitted($vtsql);
		$tcount = $vtres->affected_rows;

// delete edi rows		
		$edisql = "DELETE from `extradonorinfo` where `MCID` = '$mcid';";
		$edicnt = doSQLsubmitted($edisql);
		$edicount += $edicnt;
		
		}
	$sql = "DELETE from `members` where `Inactivedate` <= '$expdate';";
	$mbrcount = doSQLsubmitted($sql);
	
	print <<<pagePart4
	<h3>Completion Report</h3>
	MCID&apos;s deleted: $mbrcount<br>
		
pagePart4;
	}
echo "Start date/time: $sd<br />";
$ed = date('Y-m-d H:i:s',strtotime(now));
echo "End date/time: $ed<br />";
	
?>

</body>
</html>
