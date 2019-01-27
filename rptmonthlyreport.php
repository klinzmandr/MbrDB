<!DOCTYPE html>
<html>
<head>
<title>Membership Monthly Report</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="all">
</head>
<body>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>

<?php
session_start();
//include 'Incls/vardump.inc.php';
//include 'Incls/seccheck.inc.php';
include 'Incls/datautils.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

$startyr = isset($_REQUEST['startyr']) ? $_REQUEST['startyr'] : date("Y",strtotime('now'));
$startmo = isset($_REQUEST['startmo']) ? $_REQUEST['startmo'] : date("m",strtotime("-1 month"));
$sd = $startyr . '-' . $startmo;
$ed = date('Y-m-t', strtotime($sd));
// echo "sd: $sd<br>";
// echo "ed: $ed<br>";

?>
<div class="container">
<!-- <form action="rptmonthlyreport.php" method="post"  name="sd"> -->
<form class="hidden-print" action="rptmonthlyreport.php" method="post"  name="sd">
Report Date: &nbsp;&nbsp;
<select id="startyr" name="startyr" >
<option value="2016">2016</option> 
<option value="2017">2017</option> 
<option value="2018">2018</option> 
<option value="2019" selected>2019</option> 
<option value="2020">2020</option>
<option value="2021">2021</option>
</select>
<select id="startmo" name="startmo" onchange="this.form.submit();" >
<option value="">Select Month</option> 
<option value="01">January</option> 
<option value="02">Febrary</option> 
<option value="03">March</option> 
<option value="04">April</option> 
<option value="05">May</option> 
<option value="06">June</option> 
<option value="07">July</option> 
<option value="08">August</option> 
<option value="09">September</option> 
<option value="10">October</option> 
<option value="11">November</option> 
<option value="12">December</option>
</select>
</form>

<?php
if (!isset($_REQUEST['startmo'])) {
  echo '<h3>Select target year and month</h3>';
  exit;
  }
if (strtotime($sd) > strtotime(now)) {
  echo '<h3>FUTURE date entered.</h3>';
  exit;
  }

	//echo "sd: $sd<br>";
	//$nsd = date('Y-m',strtotime("$sd +1 month"));
	//echo "nsd: $nsd<br>";
// ----------------------------- members 
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
	WHERE `MemDate` <= \"$ed\"";
$res = doSQLsubmitted($sql);
//echo "sql: $sql<br>";
$numrows = $res->num_rows;
$thisyear = date('Y',strtotime($sd));
//echo "thisyear: $thisyear<br>";

$thisyrmo = date('Y-m',strtotime($sd));
//echo "thisyrmo: $thisyrmo<br>";

$memdatemissing = 0; $memdateYTD = 0; $memdateMo = 0; $memlastMo = array();
$inactivetrue = 0; $inactivefalse = 0; $inactivemissing = 0; $inactive_expired = 0; $nomail = 0;
$neither = 0; $state = 0; $noaddr = 0; $nocity = 0; $nostate = 0; $nozip = 0; $missingall = 0; $donpaidcurrent = 0; $volarray = array();
while ($r = $res->fetch_assoc()) {
	$memstatus=$r[MemStatus];
	$mctype = $r[MCtype];
	$memstatuscount[$memstatus] += 1;
	if ($mctype == '') $mctype = 'NONE';	
	$mctypecount[$mctype] += 1;
	if ($mctype == 2) $volarray[$r[MCID]] += 1;
	if ($r[E_Mail] == 'TRUE') $memstatusemailcount[$memstatus] += 1;
	if ($r[MemStatus]==1) $memactive += 1;
	if ($r[Inactive] == 'TRUE') $inactivetrue++;
	$memdate=$r[MemDate];
	$memdateyr=substr($memdate,0,4);
	$memdateyrmo=substr($memdate,0,7);

 	if (strtotime($r[LastDuesDate]) > (strtotime('-12 months')))
    $mempaidcurrent += 1;
  if ($r[Inactive] == 'FALSE')  $memisactive += 1;
  if (preg_match('/subscr/i', $r[MCtype])) $memsubscr += 1;
  if (strtotime($r[LastDonDate]) > (strtotime('-12 months')))
    $donpaidcurrent += 1;
    
	// to only look at this years records	
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
$closebtn = '';
if (isset($_SESSION['SessionActive'])) $closebtn = '&nbsp;&nbsp;<a href="javascript:self.close();" class="hidden-print btn btn-primary"><b>CLOSE</b></a>';
echo "<h2>Monthly Report $closebtn</h2>";
echo "<h3>for month of: " . date('F, Y',strtotime($sd)) . '</h3>';
echo "Report created: " . date('F d, Y',strtotime(now)) . '<br />';
ksort($memstatuscountYTD);
print <<<sumPart1
<style>
  .page-break  {
    clear: left;
    display:block;
    page-break-after:always;
    }
</style>

<script>
$(document).ready(function() {
  $("[id^=sum]").hide();
  $("#startyr").val("$startyr");
  $("#startmo").val("$startmo");
  });
function tog(f) {
  var id = "#" + f;
  $(id).toggle();
  }
</script>

<h4>Database Summary 
<span onclick='tog("sum-1")'><span title="Help" class="hidden-print glyphicon glyphicon-question-sign" style="color: blue; font-size: 20px"></span>
</span>
</h4>
<div id="sum-1">
<p>The membership section of this report summarizes all of the supporter records on the database. A new record is created when a new supporter is entered into the database. An explanation of each item follows.
<ol>
	<li><b>Total Records in DB</b> - the total number of supporter records contained in the entire database.</li>
	<li><b>Record counts by status</b></li>
  	<ul>
  	<li><b>Contacts</b> - supporter records with status of 0</li>
  	<li><b>Members</b> - supporter records with status of 1</li>
  	<li><b>Volunteers</b> - supporter records with status of 2</li>
  	<li><b>Donors</b> - supporter records with status of 3</li>
  	<li><b>Marked as Inactive</b> - supporter records marked as &apos;Inactive&apos;</li>
  	</ul>
	
	<li><b>Supporter Summary</b></li>
  	<ul>
  	<li><b>Mbrs/Vols current & paid</b> - total number of supporter records having a funding payment marked as &apos;Dues&apos; within the last 12 months.</li>
  	<li><b>Donors current & paid</b> - total number of donor records having a donation payment dated within the last 12 months.</li>
  	<li><b>Marked as Active</b> - total number of ALL supporter records that are marked  as &apos;Active&apos; regardless of funding activity.</li>
  	<li><b>Subscribing members</b> - total number of supporter records marked as subscribing monthly contributors.</li>
  	<li><b>Mbr records with NO funding activity</b> - the total number of supporter records that are marked as &apos;Active&apos; but have no associated funding records.</li>
  	</ul>

	<li><b>Supporters Added(YTD)</b> - the total number of supporter records that have been added during the selected year.</li>
	<li><b>Supported Added(YTD) by status:</b> - the total number of new supporter records entered for the selected year grouped by supporter type.</li>
	<li><b>New Mbrs and Vols in <i>month</i>, <i>year</i></b> - the total number of new supporter records identified as members or volunteers entered for the selected month and year grouped by the amount that was paid.</li>
	<li><b>YTD Mbrs and Vols by month</b> - the total number of new supporter records identified as members or volunteers entered for the selected calendar year grouped by the month entered.</li>
</ol>
</p>
</div>
sumPart1;
//echo '<pre> mctypecount '; print_r($memstatuscount); echo '</pre>';

echo '
<div class="well">
<table border=0 class="table-condensed">
<tr><td width="60%">Total Records in DB</td><td>'.$numrows.'</td></tr>
<tr><td valign="top">Record counts by status:</td><td>
Contacts: '.$memstatuscount[0].',<br>
Members: '.$memstatuscount[1].',<br>
Volunteers: '.$memstatuscount[2].',<br>
Donors: '.$memstatuscount[3].'<br>
Marked as Inactive:'.$inactivetrue.' ('. number_format(($inactivetrue/$numrows)*100,2).'%)
</td></tr>
<tr><td valign="top">Supporter Summary</td><td>
Mbrs/Vols current &amp; paid: '.$mempaidcurrent.'<br>
Donors current & paid: ' . $donpaidcurrent .'<br>
Marked as Active: '.$memisactive.'<br>
Subscribing members: '.$memsubscr.'<br>
Mbrs with NO funding activity: '.$nofundingcnt.'</td></tr>
<tr><td>Supporters Added(YTD)</td><td>'.$newrecordsYTD.'</td></tr>
<tr><td valign="top">Supporters Added(YTD) by status:</td><td>
Contacts: '.$memstatuscountYTD[0].'<br>
Members: '.$memstatuscountYTD[1].'<br>
Volunteers: '.$memstatuscountYTD[2].'<br>
Donors: '.$memstatuscountYTD[3];

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

echo "<tr><td valign=\"top\">YTD Mbrs and Vols by month:</td><td>";
if (count($thisyrcount) > 0) 
	ksort($thisyrcount);
	foreach ($thisyrcount as $k => $v) {
		echo "$k: $v<br />";
		}

echo "</table></div>  <!-- well -->";

// ----------------------- funding 
echo '<div class="page-break"></div>
<h4>Funding Summary';
print <<<sumPart2
<span onclick='tog("sum-2")'><span title="Help" class="hidden-print glyphicon glyphicon-question-sign" style="color: blue; font-size: 20px"></span>
</span>
</h4>
<div id="sum-2">
<p>The funding section of this report summarizes all of the funding records on the database. A funding record is created each time funding is received. An explantion of each report item follows.
<ol>
	<li><b>Total Funding Records</b> - the total count of all funding records on the database.</li>
	<!-- <li><b>Current paid-up members</b> - the total number of supporters marked as &apos;members&apos; that have made a payment marked as &apos;Dues&apos; AFTER the Current Ageing Date.</li> -->
	<li><b>YTD Funding Rec Count</b> - the total number of funding records entered for the selected year.</li>
	<li><b>Current Ageing Date</b> - the date used to determine the age of a funding payment.  This date is 11 months prior to the year and month selected for this report&apos;s year/month.  Any member whose LAST dues payment made before this date is considered as an expired member.  Any dues payment made after this date means that the member is considered current and active.</li>
	<li><b>Member expiration distribution</b> - these counts are based on the last payment marked as &apos;Dues&apos; PLUS 11 months to determine when an annual member will be considered expired.</li>
	<li><b>&apos;<i>year</i>&apos; YTD Purpose Distribution</b> - the total amount and (count) for the each funding &apos;Purpose&apos; entered during the selected year.</li>
	<li><b>&apos;<i>month</i>&apos; Purpose Distribution</b> - the total amount and (count) for the each funding &apos;Purpose&apos; entered during the selected year AND month .</li>
	<li><b>YTD Total funding - All Purposes</b> - the total amount of funding entered during the selected year.</li>
</ol>
</p>
</div>

sumPart2;
echo '<div class="well">';
$sql = "SELECT * 
FROM `donations`
WHERE `DonationDate` <= \"$ed\" 
ORDER BY `DonationID`";
$res = doSQLsubmitted($sql);
//echo "sql: $sql<br>";
$numrows = $res->num_rows;
//echo "funding thisyear: $thisyear<br>";
$expdate = date('Y-m-01',strtotime(' -11 months'));
$duesarray = array(); $mcidarray = array(); 
$exparray = array(); $moarray = array();
$amtarray = array();
while ($r = $res->fetch_assoc()) {
	$donyr = substr($r[DonationDate],0,4);
	//echo "donyrmo: $donyrmo<br>";
	if ($thisyear == $donyr) {  // look at this years records
		$thisyrcnt += 1;
    if (preg_match('/subscr/i', $r[Program])) {		
		  $programsubytd += 1; $programsubamtytd += $r[TotalAmount]; 
		  $purposes[$r[Program]] += 1; 
	    $purtotal[$r[Program]] += $r[TotalAmount];
	    }	
		else {
	   $purposes[$r[Purpose]] += 1; 
	   $purtotal[$r[Purpose]] += $r[TotalAmount];
	   }
		$purposesamt += $r[TotalAmount];
		$campaigns[$r[Campaign]] += 1; $campaignsamt += $r[TotalAmount];
		}
	$amtarray[$r[TotalAmount]] += $r[TotalAmount];
	
	$thisrecyrmo = substr($r[DonationDate],0,7);
  if ($thisyrmo == $thisrecyrmo) {  // look at this year-month records
    if (preg_match('/subscr/i', $r[Program])) {		
		  $lastmosubytd += 1; $lastmosubamtytd += $r[TotalAmount];
		  $lastmopurcnt[$r[Program]] += 1; 
		  $lastmopuramt[$r[Program]] += $r[TotalAmount];
		   
		  }	
    else {
		  $lastmopurcnt[$r[Purpose]] += 1; 
		  $lastmopuramt[$r[Purpose]] += $r[TotalAmount];
		  }
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
$purposesamt = number_format($purposesamt,0);
$programsubamtytd = number_format($programsubamtytd,0);
$campaignsamt = number_format($campaignsamt,0);
echo "<table class=\"table-condensed\">
<tr><td width=\"60%\">Total Funding Records</td><td>$numrows</td></tr>
<!-- <tr><td>Current paid-up members</td><td>$mbrcount</td></tr> --> 
<tr><td>YTD Funding Rec Count</td><td>$thisyrcnt</td></tr>
<tr><td>Current Ageing Date</td><td>$expdate</td></tr>
<tr><td valign=\"top\">Member expiration distribution:<br /> (NOTE: number of Members <br />expiring each month.)</td><td>";
//echo '<pre>Amounts '; print_r($amtarray); echo '</pre>';
ksort($moarray);
foreach ($moarray as $k=>$v) {
	if ($k == '**NewRec**') continue;
	echo "$k: $v<br />";
	}

$lystr = date('Y', strtotime($thisyrmo));
echo "</td></tr>
<tr><td valign=\"top\">$lystr YTD Purpose Distribution</td><td>";
ksort($purposes);
//$purposesamt -= $proramsubamtytd;
//echo "purposes-Dues: " . $purposes[Dues] . '<br>';
foreach ($purposes as $k=>$v) {
	$pt = number_format($purtotal[$k]);
	if ($k == '**NewRec**') continue;
	if ($k == "") echo "NONE: $$pt ($v)<br>";
	else echo "$k: $$pt ($v)<br>";
	$ytdtotal += $purtotal[$k];
	}
//echo 'Subscriptions: $'.$programsubamtytd." ($programsubytd)<br>";
//echo "YTD Total: $$ytdtotal";
	
$lmstr = date('F', strtotime($thisyrmo));
//echo "$lmstr<br>";
echo '</td></tr>';
echo "<tr><td align=\"right\">YTD Total funding - All Purposes</td><td>$$purposesamt</td></tr>
<tr><td valign=\"top\">$lmstr, $lystr Purpose Distribution</td><td>";
ksort($lastmopurcnt);
if (count($lastmopurcnt) > 0) 
	foreach ($lastmopurcnt as $k=>$v) {
		$pt = number_format($lastmopuramt[$k]);
		if ($k == '**NewRec**') continue;
		if ($k == "") echo "NONE: $$pt ($v)<br>";
		else echo "$k: $$pt ($v)<br>";
		}
//echo 'Subscriptions: $'.number_format($lastmosubamtytd,0)." ($lastmosubytd)<br>";
//echo "$lmstr total: $$mototal<br>";
$fmototal = number_format($lastmotot);
echo "<tr><td align=\"right\">$lmstr Total funding:</td><td>$$fmototal</td></tr>
</table></div>  <!-- well -->";

//echo '<pre> total amount '; print_r($purposesamt); echo '</pre>';

// -------------------- correspondence
// correspondence table report

$sql = "SELECT * 
FROM `correspondence` 
WHERE `DateSent` <= \"$ed\"";
$res = doSQLsubmitted($sql);
//echo "sql: $sql<br>";
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
echo '<div class="page-break"></div>
<h4>Correspondence Summary';
print <<<sumPart3
<span onclick='tog("sum-3")'><span title="Help" class="hidden-print glyphicon glyphicon-question-sign" style="color: blue; font-size: 20px"></span>
</span>
</h4>
<div id="sum-3">
<p>The correspondence section of this report summarizes all of the correspondence records on the database. A correspondence record is created when some form of communication with the supporter is undertaken.  Some actions are automatic (i.e. reminders) and some have to be manually initiated (i.e. sending a newsletter).  An explanation of each report item follows.
<ol>
	<li><b>Total Correspondence Rec Count</b> - the total number of correspondence records contained in the entire database.</li>
	<li><b><i>year</i> YTD Correspondence records</b> - total number of correspondence records entered during the selected year.</li>
	<li><b>YTD Correspondence by type</b> - total count of each different category of correspondence record entered for the selected year.</li>
	<li><b>Correspondence sent in <i>month</i></b> - total number of correspondence records entered during the selected year and month.</li>
	<li><b>Monthly correspondence by type</b> - total count of each different category of correspondence record entered during the selected year and month.</li>
</ol>
</p>
</div>

sumPart3;

// correspondence records report
echo '<div class="well">
<table border=0 class="table-condensed">';
echo "<td>Total Correspondence Rec Count</td><td>$reccnt</td</tr>";
echo "<tr><td>$lystr YTD Correspondence records</td><td>$rec_count</td></tr>";
echo "<tr><td valign=\"top\">YTD Correspondence by type</td><td>";
foreach ($corrtype as $k => $v) {
	if ($k == "") echo "NONE: $v<br />";
	else echo "$k: $v<br />";
	}
echo "</td></tr>";
//echo "<tr><td>Total number of Corr. types:</td><td>$type_count</td></tr>";
echo "<td>Correspondence sent in <b>". date('F',strtotime($sd)) ."</b></td><td>$mocount</td></tr>";
echo "<tr><td valign='top'>Monthly correspondence by type</td><td>";
foreach ($corrlastmo as $kk => $vv) {
	if ($kk == "") echo "NONE: $vv<br />";
	else echo "$kk: $vv<br />";
	}

//echo '<pre> month '; print_r($corrlastmo); echo '</pre>';
echo '</td></tr></table></div>  <!-- well -->';
$sql = "SELECT * 
FROM `voltime` 
WHERE `VolDate` <= \"$ed\"";
$res = doSQLsubmitted($sql);
//echo "sql: $sql<br>";
$trcnt = $res->num_rows;
$actvol = array(); $avYTD = array();
while ($r = $res->fetch_assoc()) {
  if (strtotime($r[VolDate]) >= strtotime('-12 months')) $actvol[$r[MCID]] += 1;
  if (strtotime($r[VolDate]) >= strtotime($startyr.'-01-01')) {
    $avYTD[$r[MCID]] += 1;
    $actvolYTD[$r[VolCategory]][$r[MCID]] += 1;
    $volhrsYTD[$r[VolCategory]] += $r[VolTime];
    $volhrsTot += $r[VolTime];
    $volmilesTot += $r[VolMileage];
    }
  if (substr($r[VolDate],0,7) == $sd) {
    $movols[$r[MCID]] += 1;
    $actvolmo[$r[VolCategory]][$r[MCID]] += 1;
    $volhrsmo[$r[VolCategory]] += $r[VolTime];
    $volhrsmoTot += $r[VolTime];
    $volmilesmoTot += $r[VolMileage];
    }
  }

// volunteer time reported
echo '<div class="page-break"></div>
<h4>Volunteer Time Summary';
print <<<sumPart4
<span onclick='tog("sum-4")'><span title="Help" class="hidden-print glyphicon glyphicon-question-sign" style="color: blue; font-size: 20px"></span>
</span>
</h4>
<div id="sum-4">
<p>The Volunteer section of this report summarizes all of the volunteer time records recorded on the database. A volunteer time record is created when entered from the sign-in sheets.  An explanation of each report item follows.</p>
<ol>
	<li><b>Total Vol Time Rec&apos;s in DB</b> - the total count of volunteer time records contained in the entire database.</li>
	<li><b>Registered Volunteers in DB</b> - the total number of supporters identified as volunteers in the membership database.</li>
	<li><b>Active Volunteers</b> - the number of volunteers that have logged at least 1 time entry in the last 12 months.</li>
	<li><b>YTD Vol Time by category</b> - a summary of the number of total volunteer hours followed by the count of the different volunteers that contributed to that time category. Following are YTD totals for hours, mileage driven and volunteers for the selected year and up to and including the selected month.</li>
	<li><b><i>month</i> and <i>year</i> Vol Time by category</b> - a summary of the total volunteer hours followed by the count of the different volunteers that contributed to each category for the report month. Totals are for hours, mileage driven and different volunteers for the selected month.</li>
</ol>
</div>

sumPart4;
echo '<div class="well">
<table border=0 class="table-condensed">';
echo "<td>Total Vol Time Rec Count</td><td>$trcnt</td</tr>";
echo '<tr><td>Registered Volunteers in DB</td><td>'.count($volarray).'</td></tr>';
echo '<tr><td>Active Volunteers</td><td>'.count($actvol).'</td></tr>';
//echo '<tr><td>'.$lystr.' YTD Volunteers</td><td>'.count($avYTD).'</td></tr>';
echo "<tr><td valign=\"top\">YTD Vol Time by category</td><td>";

ksort($volhrsYTD);
foreach ($volhrsYTD as $k => $v) {
  if ($k == '') continue;
  echo "$k: $v (". count($actvolYTD[$k]) . ")<br>";
  }
echo '</td></tr>';
echo '<tr>
<td align="right">YTD Total Vol Hours<br>YTD Total Mileage<br>YTD Volunteers</td>
<td>'.$volhrsTot.'<br>'.$volmilesTot.'<br>'.count($avYTD).'</td></tr>';
echo "<tr><td valign=\"top\">$lmstr, $startyr Vol Time by category</td><td>";

ksort($volhrsmo);
foreach ($volhrsmo as $k => $v) {
  if ($k == '') continue;  
  echo "$k: $v (". count($actvolmo[$k]) . ")<br>";
  }
echo '</td></tr>';
echo '<tr><td align="right"><b>'.$lmstr.'</b> Total Vol Hours<br>Total Mileage<br>Volunteers</td><td>'.$volhrsmoTot.'<br>'.$volmilesmoTot.'<br>'.count($movols).'</td></tr>';
 
echo '</table>';
echo '</div> <!-- well -->';
echo '----- END OF REPORT -----';

?>
</div>  <!-- container -->
</body>
</html>