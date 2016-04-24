<!DOCTYPE html>
<html>
<head>
<title>Membership Monthly Report</title>
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
$sd = isset($_REQUEST['startyrmo']) ? $_REQUEST['startyrmo'] : '';

$startyrmo = date('Y-m', strtotime("now -1 month"));

if ($action == '') {
print <<<pagePart1
<h3>Monthly Report&nbsp;&nbsp;<a href="javascript:self.close();" class="btn btn-primary"><b>CLOSE</b></a></h3>
<p>This will provide a summarization for the 'Year to Date' (YTD), last calendar month and the current month of membership and funding information.</p><br />
<br>
Use this as the Year and month for this report: 
<form action="rptmonthlyreport.php" method="post"  name="sd">
<input type="text" name="startyrmo" value="$startyrmo">&nbsp;&nbsp;
<input type="hidden" name="action" value="create">
<input type="submit" name="submit" value="CONTINUE" class="btn btn-success">
</form>

pagePart1;

}

if ($action == 'create') {	
	echo '<div class="container">';
	//echo "sd: $sd<br>";
	//$nsd = date('Y-m',strtotime("$sd +1 month"));
	//echo "nsd: $nsd<br>";
// ----------------------------- members -----------------------------------
// get nofunding count - where neither dues nor donation have been made
$sql = "SELECT * 
	FROM `members` 
	WHERE `LastDonDate` IS NULL 
		AND `LastDuesDate` IS NULL";
$res = doSQLsubmitted($sql);
$nofundingcnt = $res->num_rows;
// first read and summarize member records
$sql = "SELECT * 
	FROM `members` 
	WHERE 1";
$res = doSQLsubmitted($sql);
$numrows = $res->num_rows;
$thisyear = date('Y',strtotime($sd));
//echo "thisyear: $thisyear<br>";

$thisyrmo = date('Y-m',strtotime($sd));
//echo "thisyrmo: $thisyrmo<br>";

$memdatemissing = 0; $memdateYTD = 0; $memdateMo = 0; $memlastMo = array();
$inactivetrue = 0; $inactivefalse = 0; $inactivemissing = 0; $inactive_expired = 0; $nomail = 0;
$neither = 0; $state = 0; $noaddr = 0; $nocity = 0; $nostate = 0; $nozip = 0; $missingall = 0;
while ($r = $res->fetch_assoc()) {
	$memstatus=$r[MemStatus];
	$mctype = $r[MCtype];
	$memstatuscount[$memstatus] += 1;
	if ($mctype == '') $mctype = 'NONE';	
	$mctypecount[$mctype] += 1;
	if ($r[E_Mail] == 'TRUE') $memstatusemailcount[$memstatus] += 1;
	if ($r[MemStatus]==1) $memactive += 1;
	if ($r[Inactive] == 'TRUE') $inactivetrue++;
	$memdate=$r[MemDate];
	$memdateyr=substr($memdate,0,4);
	$memdateyrmo=substr($memdate,0,7);
	// only look at this years records	
	if ($thisyear == $memdateyr) {  						
		$newrecordsYTD += 1;
		$memstatuscountYTD[$r[MemStatus]] += 1;
		if (($r[MemStatus] == 1) OR ($r[MemStatus] == 2)) {
			$thisyrcount[$memdateyrmo] += 1;
			}
		//echo "year: $thisyear, record date: $memdateyr<br>";
		if ($r[MemStatus] == 1)	$memdateYTD += 1;
		if ($r[MemStatus] == 2)	$voldateYTD += 1;
		
		//echo "lastduesamount: $r[LastDuesAmount]<br />";
		if (($r[MemStatus] == 1) OR ($r[MemStatus] == 2)) {
			if ($memdateyrmo == $thisyrmo) 
				$memlastMo[$r[LastDuesAmount]] += 1;
			}
		//echo "memdatemo: $memdateyrmo, this month: $thisyrmo<br>";
		if (($r[MemStatus] == 1) AND ($memdateyrmo == $thisyrmo)) 
			$memdateMo += 1;
		}
	}
//echo '<pre> this year count'; print_r($thisyrcount); echo '</pre>';
$expdate = calcexpirationdate();
//echo '<pre> mctypecount '; print_r($memstatuscountYTD); echo '</pre>';
echo '<h2>Monthly Report&nbsp;&nbsp;<a href="javascript:self.close();" class="btn btn-primary"><b>CLOSE</b></a></h2>';
echo "<h3>Monthly Report for: " . date('F, Y',strtotime($sd)) . '</h3>';
echo "Report created: " . date('F d, Y',strtotime(now)) . '<br />';
echo "<h4>Membership Summary</h4>
<div class=\"well\">
<table class=\"table-condensed\">
<tr><td width=\"60%\">Records in DB</td><td>$numrows</td></tr>
<tr><td>Inactive records in DB</td><td>$inactivetrue</td></tr>
<tr><td>Mbr records with NO funding activity</td><td>$nofundingcnt</td></tr>
<tr><td>New records added(YTD)</td><td>$newrecordsYTD</td></tr>
<tr><td valign=\"top\">New records added(YTD) by status:</td><td>";
ksort($memstatuscountYTD);
echo "Contacts: $memstatuscountYTD[0]<br>";
echo "Members: $memstatuscountYTD[1]<br>";
echo "Volunteers: $memstatuscountYTD[2]<br>";
echo "Donors: $memstatuscountYTD[3]";

echo "</td></tr>
<tr><td valign=\"top\">New Mbrs and Vols in " . date('F, Y', strtotime($thisyrmo)) .  "<br>(by amount paid)</td><td>";
ksort($memlastMo);
foreach ($memlastMo as $k=>$v) {
	if ($k == '') continue;
	//if ($k == 0) continue;
	echo "$$k: $v<br />";
	}
echo "</td></tr>
<!-- <tr><td>New members this month:</td><td>$memdateMo</td></tr> -->
<!-- <tr><td></td><td>&nbsp;</td></tr> -->";
if ($memdatemissing != 0) 
	echo "<tr><td>Member date missing:</td><td>$memdatemissing</td></tr>";

echo "<tr><td valign=\"top\">YTD membership by month:</td><td>";
if (count($thisyrcount) > 0) 
	ksort($thisyrcount);
	foreach ($thisyrcount as $k => $v) {
		echo "$k: $v<br />";
		}

echo "</table></div>  <!-- well -->";

// ----------------------- funding ---------------------------
echo '<h4>Funding Summary</h4>';
echo '<div class="well">';
$sql = "select * from donations order by `DonationID`";
$res = doSQLsubmitted($sql);
$numrows = $res->num_rows;
//echo "funding thisyear: $thisyear<br>";
$expdate = date('Y-m-01',strtotime(' -11 months'));
$duesarray = array(); $mcidarray = array(); $exparray = array(); $moarray = array();
$amtarray = array();
while ($r = $res->fetch_assoc()) {
	$donyr = substr($r[DonationDate],0,4);
	//echo "donyrmo: $donyrmo<br>";
	if ($thisyear == $donyr) {  // look at this years records
		$thisyrcnt += 1;
		$programs[$r[Program]] += 1; $programsamt += $r[TotalAmount];		
		$purposes[$r[Purpose]] += 1; $purposesamt += $r[TotalAmount];
		$purtotal[$r[Purpose]] += $r[TotalAmount];
		$campaigns[$r[Campaign]] += 1; $campaignsamt += $r[TotalAmount];
		}
	$amtarray[$r[TotalAmount]] += $r[TotalAmount];
	
	$thisrecyrmo = substr($r[DonationDate],0,7);
	if ($thisyrmo == $thisrecyrmo) {
		$lastmopurcnt[$r[Purpose]] += 1;
		$lastmopuramt[$r[Purpose]] += $r[TotalAmount];
		$lastmotot += $r[TotalAmount];
		}

	if (($r[DonationDate] >= $expdate) AND ($r[Purpose] == 'Dues')) {
		$donyrmo = substr($r[DonationDate],0,7);
		$mcid = $r[MCID];
		$duesarray[$donyrmo]++;
		$mcidarray[$mcid] = $donyrmo;
		//$moarray[$donyrmo]++;
		}
	else $exparray[$mcid]++;			// member expired
	}
// echo '<pre> purposes '; print_r($purposes); echo '</pre>';
//ksort($mcidarray);
//echo '<pre> mcidarray '; print_r($mcidarray); echo '</pre>';
$mbrcount = count($mcidarray);
$expcount = count($exparray);		// an interesting number but not indicitive of lapsed members
// bias date back to current reporting yrmo
foreach ($mcidarray as $m => $d) {
	$dd = date('Y-m', strtotime($d . ' +11 months'));
	$moarray[$dd]++;
	}
ksort($moarray);

//echo '<pre>MCID'; print_r($mcidarray); echo '</pre>';
//echo '<pre>Month'; print_r($moarray); echo '</pre>';
$purcount = count($purposes); $progcount = count($programs); $campcount = count($campaigns);
$purposesamt = number_format($purposesamt,2);
$programsamt = number_format($programsamt,2);
$campaignsamt = number_format($campaignsamt,2);
echo "<table class=\"table-condensed\">
<tr><td width=\"60%\">Total Funding Records</td><td>$numrows</td></tr>
<tr><td>Current paid-up members</td><td>$mbrcount</td></tr> 
<tr><td>YTD Funding Rec Count</td><td>$thisyrcnt</td></tr>
<tr><td>Current Expiration Date</td><td>$expdate</td></tr>
<tr><td valign=\"top\">Member expiration distribution:<br /> (NOTE: number of Members <br />expiring each month.)</td><td>";
//echo '<pre>Amounts '; print_r($amtarray); echo '</pre>';
foreach ($moarray as $k=>$v) {
	if ($k == '**NewRec**') continue;
	echo "$k: $v<br />";
	}

$lystr = date('Y', strtotime($thisyrmo));
echo "</td></tr>
<tr><td valign=\"top\">$lystr YTD Program Distribution</td><td>";
foreach ($purposes as $k=>$v) {
	$pt = number_format($purtotal[$k]);
	if ($k == '**NewRec**') continue;
	if ($k == "") echo "NONE: $$pt ($v)<br>";
	else echo "$k: $$pt ($v)<br>";
	$ytdtotal += $purtotal[$k];
	}
//echo "YTD Total: $$ytdtotal";
	
$lmstr = date('F', strtotime($thisyrmo));
//echo "$lmstr<br />";
echo "</td></tr>

<tr><td valign=\"top\">$lmstr Program Distribution</td><td>";
if (count($lastmopurcnt) > 0) 
	foreach ($lastmopurcnt as $k=>$v) {
		$pt = number_format($lastmopuramt[$k]);
		if ($k == '**NewRec**') continue;
		if ($k == "") echo "NONE: $$pt ($v)<br>";
		else echo "$k: $$pt ($v)<br>";
		}
//echo "$lmstr total: $$mototal<br>";
$fmototal = number_format($lastmotot);
echo "<tr><td align=\"right\">$lmstr Total funding:</td><td>$$fmototal</td></tr>";
echo "<tr><td>YTD Total funding - All Purposes</td><td>$$purposesamt</td></tr>
</table></div>  <!-- well -->";

//echo '<pre> total amount '; print_r($purposesamt); echo '</pre>';

// -------------------- correspondence ----------------------------------
// correspondence table report

$sql = "SELECT * from `correspondence` where 1";
$res = doSQLsubmitted($sql);
$reccnt = $res->num_rows;
while ($r = $res->fetch_assoc()) {
	$corryr = substr($r[DateSent],0,4);  	// get year
	$corryrmo = substr($r[DateSent],0,7);		// get year and month
	if (($r[CorrespondenceType]) == '**NewRec**') continue;
	if ($corryrmo == $thisyrmo) {
		$corrlastmo[$r[CorrespondenceType]] += 1;
		$mocount++;
		}
	if (strtotime($thisyear) <= strtotime($corryr)) {  // only look at this years records 
		$rec_count++;
		$corrtype[$r[CorrespondenceType]] += 1;
		}
	}

$type_count = count($corrtype);
echo '<h4>Correspondence Summary</h4>
<div class="well">';
echo '<table class="table-condensed">';
echo "<td>Total Correspondence Rec Count</td><td>$reccnt</td</tr>";
echo "<tr><td>$lystr YTD Correspondence records</td><td>$rec_count</td></tr>";
echo "<tr><td valign=\"top\">Corr. Types</td><td>";
foreach ($corrtype as $k => $v) {
	if ($k == "") echo "NONE: $v<br />";
	else echo "$k: $v<br />";
	}
echo "</td></tr>";
//echo "<tr><td>Total number of Corr. types:</td><td>$type_count</td></tr>";
echo "<td>Correspondence sent in <b>". date('F',strtotime('today -1 month')) ."</b></td><td></td></tr>";
echo "<td>Total items sent:</td><td>$mocount</td></tr><tr><td></td><td>";
foreach ($corrlastmo as $kk => $vv) {
	if ($kk == "") echo "NONE: $vv<br />";
	else echo "$kk: $vv<br />";
	}

echo '</td></tr></table></div>  <!-- well -->
----- END OF REPORT -----';
//echo '<pre> month '; print_r($corrlastmo); echo '</pre>';
}
?>
</div>  <!-- container -->
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>