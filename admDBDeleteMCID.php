<!DOCTYPE html>
<html>
<head>
<title>MbrDB MCID Delete</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<?php
session_start();

//include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
include 'Incls/datautils.inc.php';			// NOTE: need to make sure the production database is being used.

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$mcid = isset($_REQUEST['mcid']) ? $_REQUEST['mcid'] : '';


if ($action == "") {
print <<<pagePart1
<h3>MbrDB MCID Delete</h3>
<p>This utility is provided to delete a member record and all associated records from the database:</p>
<p>Funding and volunteer time records are re-assigned to the dummy MCID ZZZ99 to allow continued historical reporting for both categories.</p>
<a class="btn btn-primary btn-success" href="admDBJanitor.php">Return</a><br />
<h4>Enter the MCID to delete:</h4>
<form action="admDBDeleteMCID.php">
MCID: <input type="text" name="mcid" value="">
<input type="hidden" name="action" value="list">
<input type="submit" name="submit" value="Delete">
</form>
pagePart1;
exit;
}

$reccnt = 0;

// list MCID record and their assoicated funding, corresondence and time records
if ($action == 'list') {
	$sd = date('Y-m-d H:i:s',strtotime(now));
	$sql = "SELECT * from `members` where `MCID` = '$mcid';";
	$res = doSQLsubmitted($sql);
//	echo "sql: $sql<br>";
	$mbrsfound = $res->num_rows;
	if ($mbrsfound == 0) {
		echo '<h3>No member record found</h3>
		    <a class="btn btn-primary" href="admDBDeleteMCID.php">Start Over</a>';
		exit();
		}
echo '
	<h3>Summary of all '.$mcid.' records.<br></h3>
	<a class="btn btn-danger" href="admDBDeleteMCID.php?mcid='.$mcid.'&action=delete">Continue to Delete MCID</a>
	<a class="btn btn-primary" href="admDBDeleteMCID.php">Start Over</a><br />';

		
//	echo 'MCID to delete:<br />';
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
	echo '<br>NOTE: archived records are re-assigned to the psudeo MCID of ZZZ99 to retain historical reporting accuracy<br><br>'; 
	}
	
if ($action == 'delete') {
	$sd = date('Y-m-d H:i:s',strtotime(now));
	print <<<pagePart3
	<h3>Delete and report summary of actions.</h3>
	<a class="btn btn-primary" href="admDBJanitor.php?action=">Done</a><br><br>
pagePart3;

  //	archive donation rows		
  //echo "Processing donation records for: $mcid<br>";
  $sql = "SELECT * FROM `donations` WHERE `MCID` = '$mcid'";
  $res = doSQLsubmitted($sql);
  $dcount = $res -> num_rows;
  $sql = "UPDATE `donations` SET `MCID`='ZZZ99', `Note`=CONCAT_WS(',',\"PREVMCID:$mcid \",`Note`) WHERE `MCID` = '$mcid'";
  $dres = doSQLsubmitted($sql);
  
  // delete correspondence rows		
  //echo "Processing correspondence records for: $mcid<br>";
  $csql = "DELETE from `correspondence` where `MCID` = '$mcid';";
  $ccnt = doSQLsubmitted($csql);
  $ccount += $ccnt;
  
  // archive vol time rows		
  //echo "Processing vol time records for: $mcid<br>";
  $sql = "SELECT * FROM `voltime` WHERE `MCID` = '$mcid'";
  $res = doSQLsubmitted($sql);
  $vtcount = $res -> num_rows;
  $vtsql = "UPDATE `voltime` SET `MCID`='ZZZ99', `VolNotes`=CONCAT_WS(',',\"PREVMCID:$mcid \", `VolNotes`) WHERE `MCID` = '$mcid'";
  $vtres = doSQLsubmitted($vtsql);
  
  // delete edi rows		
  //echo "Processing edi records for: $mcid<br>";
  $edisql = "DELETE from `extradonorinfo` where `MCID` = '$mcid';";
  $edicnt = doSQLsubmitted($edisql);
  $edicount += $edicnt;

  //echo "Processing member record for: $mcid<br>";
	$sql = "DELETE from `members` where `MCID` = '$mcid';";
	$mbrcount = doSQLsubmitted($sql);
	
	print <<<pagePart4
	<h3>Completion Report</h3>
	MCID deleted: $mcid<br>
	Funding records archived as ZZZ99: $dcount<br>
	Time records archived as ZZZ99: $vtcount<br>
	Correspondence records deleted: $ccount<br>
	Extra Donor Info records deleted: $edicount<br><br>
		
pagePart4;
	}
echo "Start date/time: $sd<br />";
$ed = date('Y-m-d H:i:s',strtotime(now));
echo "End date/time: $ed<br />";
	
?>

</body>
</html>
