<!DOCTYPE html>
<html>
<head>
<title>Mbr Summary Loader</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<?php
session_start();
//include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
include 'Incls/datautils.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if ($action == '') {
print <<<pagePart1
	
<h3>Member Summary Loader&nbsp;&nbsp;&nbsp;<a class="btn btn-primary" href="admDBJanitor.php">RETURN</a>&nbsp;&nbsp;&nbsp</h3>

<p>This page reads the members table as well as both the donations table and the correspondence table extracting the last dues/donation activity event and corresondence event for each MCID and updating that information into the members record.</p>
<p>Preliminary analysis:</p>

pagePart1;
// analysis of correspondence log
$sql = "SELECT * FROM `correspondence` WHERE 1";
$res = doSQLsubmitted($sql);
$crowcount = $res->num_rows;
while ($r = $res->fetch_assoc()) {
	$carray[$r[MCID]]++;
	}

// analysis of donations log
$sql = "SELECT * FROM `donations` WHERE 1;";
$res = doSQLsubmitted($sql);
$drowcount = $res->num_rows;
while ($r = $res->fetch_assoc()) {
	$darray[$r[MCID]]++;
	if ($r[TotalAmount] == 0) $nomoney++;
	}

// analysis of member table
$sql = "SELECT * FROM `members` WHERE 1;";
$res = doSQLsubmitted($sql);
$mrowcount = $res->num_rows;
while ($r = $res->fetch_assoc()) {
	$marray[$r[MCID]]++;
	}

echo "Total number of member records: $mrowcount<br>";
echo "Row count in donations log: $drowcount<br />";
echo "Total number of unique MCIDs in donations log: ".count($darray)."<br />";
echo "Number of final funding records without any amount: $nomoney<br /><br />";
echo "Row count in correspondence log: $crowcount<br />";
echo "Total number of unique MCIDs in correspondence log: ".count($carray)."<br />";
//echo '<pre> donations '; print_r($sumarray); echo '</pre>';
echo '<h4>Clicking continue will perform the updates.<br><br><a class="btn btn-primary btn-success" href="admmbrsummaryloader.php?action=upd">Continue</a></h4>';
exit;
}

if ($action == 'upd') {
$sd = date('Y-m-d H:i:s',strtotime(now));

// clear existing values from ALL member records
$UPDarray = array();
$UPDarray[LastDonDate] = '';
$UPDarray[LastDonPurpose] = '';
$UPDarray[LastDonAmount] = '';
$UPDarray[LastDuesDate] = '';
$UPDarray[LastDuesAmount] = '';
$UPDarray[LastCorrDate] = '';
$UPDarray[LastCorrType] = '';
$clrrc = sqlupdate('members',$UPDarray, '1');

// get info from donations table
	$sql = "SELECT * FROM `donations` WHERE 1=1 ORDER BY `DonationID` ASC;";
	$res = doSQLsubmitted($sql);
	$drowcnt = $res->num_rows;
	$dondatearray = array(); $duesdatearray = array(); 
	$donamtarray = array(); $duesamtarray = array(); 
	$purarray = array(); $dmcidarray = array(); $cmcidarray = array();
	$mcidarray = array();
	while ($r = $res->fetch_assoc()) {
		$mcidarray[$r[MCID]] += 1;
		$dmcidarray[$r[MCID]] += 1;
		if ($r[Purpose] == 'Dues') {
			$duesamtarray[$r[MCID]] = $r[TotalAmount];
			$duesdatearray[$r[MCID]] = $r[DonationDate];	
			}
    else {
			$purarray[$r[MCID]] = $r[Purpose];
			$donamtarray[$r[MCID]] = $r[TotalAmount];			
			$dondatearray[$r[MCID]] = $r[DonationDate];
			}
		if ($r[TotalAmount] == 0) $nomoney++;
		}
// get info from correspondence table
	$sql = "SELECT * FROM `correspondence` WHERE 1 ORDER BY `CORID` ASC;";
	$resc = doSQLsubmitted($sql);
	$crowcnt = $resc->num_rows;
	while ($c = $resc->fetch_assoc()) {
		$cmcidarray[$c[MCID]] += 1;
		$mcidarray[$c[MCID]] += 1;
		$corrtypearray[$c[MCID]] = $c[CorrespondenceType];
		$corrdatearray[$c[MCID]] = $c[DateSent];
		}
// get total record count for reporting
	$sqlm = "SELECT * FROM `members` WHERE 1;";
	$resm = doSQLsubmitted($sqlm);
	$totalmembercnt = $resm->num_rows;
	
// now update summary fields in member records needing it
	foreach ($mcidarray as $k => $v) {
		$flds = array();
		if (isset($purarray[$k])) $flds[LastDonPurpose] = $purarray[$k];		
		if (isset($dondatearray[$k])) $flds[LastDonDate] = $dondatearray[$k];
		if (isset($donamtarray[$k])) $flds[LastDonAmount] = $donamtarray[$k];
		if (isset($duesdatearray[$k])) $flds[LastDuesDate] = $duesdatearray[$k];
		if (isset($duesamtarray[$k])) $flds[LastDuesAmount] = $duesamtarray[$k];
		if (isset($corrdatearray[$k])) $flds[LastCorrDate] = $corrdatearray[$k];
		if (isset($corrtypearray[$k])) $flds[LastCorrType] = $corrtypearray[$k];
		//echo "<pre>$k update "; print_r($flds); echo '</pre>';
		if (($k != '' AND (count($flds) > 0))) {
			sqlupdate('members', $flds, "`MCID` = '$k'");
			$mbrcnt++;
			}
		}
	echo '<h3>Member Summary Loader - Update Completed&nbsp;&nbsp;&nbsp;<a class="btn btn-primary" href="admDBJanitor.php">RETURN</a></h3>';
	echo "Start date/time: $sd<br />";
	echo "Total Member records in DB: $totalmembercnt<br />";
	echo "Rows with summary info reset: $clrrc<br>";
	echo "Total Donation records in DB: $drowcnt<br />";
	echo "Total Corresdonence records id DB: $crowcnt<br />";
	echo 'Unique MCIDs Count in funding log: ' . count($dmcidarray) . '<br />';
	echo 'Unique MCIDs Count in correspondence log: ' . count($cmcidarray) . '<br />';
	echo "MCIDs updated: $mbrcnt<br />";
	$sd = date('Y-m-d H:i:s',strtotime(now));
	echo "End date/time: $sd<br />";
	}
?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
