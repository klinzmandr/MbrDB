<!DOCTYPE html>
<html>
<head>
<title>Database Summary</title>
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

echo '<div class="container">';
print <<<pagePart1
<h3>Database Summary*&nbsp;&nbsp;&nbsp;<a class="btn btn-primary" href="javascript:self.close();">(CLOSE)</a></h3>
Connection Info: $mysqli->host_info, 
Client Info: $mysqli->client_info, 
Server Info: $mysqli->server_info<br />
Database in use: $_SESSION[DB_InUse]<br />
pagePart1;
$sql = "SELECT * FROM `members` WHERE 1";
$res = doSQLsubmitted($sql);
$numrows = $res->num_rows;
$thisyear = date('Y',strtotime(today));
$thisyrmo = date('Y-m',strtotime(today));
$lastyrmo = date('Y-m',strtotime("today -1 month"));
//echo "lastyrmo: $lastyrmo<br />";
$memdatemissing = 0; $memdateYTD = 0; $memdateMo = 0; $memlastMo = 0;
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
	$memdate=$r[MemDate];
	if ($memdate == "") $memdatemissing += 1;
	else {
		$memdateyr=substr($memdate,0,4); $memdateyrcount[$memdateyr]+=1;
		$memdatemo=substr($memdate,5,2); $memdateyrcount[$memdatemo]+=1;
		$memdateyrmo=substr($memdate,0,7); $memdateyrmocount[$memdateyrmo]+=1;
		}
	//echo "year: $thisyear, record date: $memdateyr<br>";
	if (($r[MemStatus] == 1) AND ($memdateyr == $thisyear)) $memdateYTD += 1;
	//echo "memdatemo: $memdateyrmo, this month: $thisyrmo<br>";
	if (($r[MemStatus] == 1) AND ($memdateyrmo == $thisyrmo)) $memdateMo += 1;
	//echo "lastdatemo: $memdateyrmo, last month: $lastyrmo<br>";
	if (($r[MemStatus] == 1) AND ($memdateyrmo == $lastyrmo)) $memlastMo += 1;
	
	
	// testing address fields
	if ($r[AddressLine] == '') $noaddr += 1;
	if ($r[City] == '') $nocity += 1;
	if ($r[State] == '') $nostate += 1;
	if ($r[ZipCode] == '') $nozip += 1;
	if ($r[Mail] == 'FALSE') $nomail += 1;
	if (($r[AddressLine] == '') && ($r[City] == '') && ($r[State] == '') && ($r[ZipCode] == '')) $missingall++;
	
	// testing inactive fields	
	$inactive = $r[Inactive];
	if ($inactive == 'TRUE') $inactivetrue+=1;
	if ($inactive == 'FALSE') $inactivefalse+=1;
	if (($inactive == 'TRUE') AND ($r[Inactivedate] == '')) $inactivemissing +=1;
	if (($inactive == 'TRUE') AND ($r[Inactivedate] != '')) {
		$expired = strtotime("-90 days"); $inactive = strtotime($r[Inactivedate]);
		if ($inactive <= $expired) $inactive_expired++;
		}
	
	if ($r[EmailAddress] != "") { $email += 1; }
	if ($r[PrimaryPhone] != "") { $phone += 1; }
	if (($r[PrimaryPhone] == "") AND ($r[EmailAddress] == "")) { $neither += 1; }
	if ($r[E_Mail] == 'TRUE') { $okemail += 1; }
	if ($r[E_Mail] == 'FALSE') { $noemail += 1; }
	}

print <<<formatMembers1
<h4>Member Summary:</h4>
Total number of member records: $numrows<br />
<div class="row">
<div class="col-sm-2"><b>New Members</b></div>
</div>  <!-- row -->
<div class="well">
<div class="row">
<div class="col-sm-4">Active (Status=1) Members: $memactive</div>
<div class="col-sm-4">New Members (YTD): $memdateYTD</div>
<div class="col-sm-4">New Members (This Month): $memdateMo</div>
</div>
<div class="row">
<div class="col-sm-4">Member missing Date Joined: $memdatemissing</div>
<div class="col-sm-4">Members Joined Last Mont: $memlastMo</div>
</div>
</div>  <!-- well -->
<div class="row">
<div class="col-sm-2"><b>Member Status</b></div>
</div>  <!-- row -->
<div class="well">
<div class="row">
</div>  <!-- row -->
<div class="row">
<div class="col-sm-4">Member Status:</div>
formatMembers1;
if (count($memstatuscont) > 0) ksort($memstatuscount);
if (count($memstatuscont) > 0) foreach ($memstatuscount as $k=>$v) {
	echo "<div class=\"col-sm-1\">$k=$v</div>";
	}
echo '</div><div class="row"><div class="col-sm-4">Member Status Email OK Count:</div>';
if (count($memstatusemailcount) > 0) ksort($memstatusemailcount);
if (count($memstatusemailcount) > 0) foreach ($memstatusemailcount as $k=>$v) {
	echo "<div class=\"col-sm-1\">$k=$v</div>";
	}

echo '</div>  <!-- row --></div>  <!-- well -->';
echo '<div class="row"><div class="col-sm-3"><b>Members Types:</b></div>  <!-- class -->
</div>  <!-- row --><div class="well"><div class="row">';
if (count($mctypecount) > 0) ksort($mctypecount);
if (count($mctypecount) > 0) foreach ($mctypecount as $k => $v) {
	echo "<div class=\"col-sm-2\">$k=$v</div>";
	}
echo '</div>  <!-- row --></div>  <!-- well -->';
echo '<div class="row"><div class="col-sm-3"><b>Members Inactive:</b></div></div>';
print <<<formatMembers3
<div class="well">
<div class="row">
<div class="col-sm-4">Inactive False(all records): $inactivefalse</div>
<div class="col-sm-3">Inactive True (all records): $inactivetrue</div>
</div>  <!-- row -->
<div class="row">
<div class="col-sm-4">Inactive True missing date: $inactivemissing</div>
<div class="col-sm-6">Expired Inactive (Inactive > 90 days): $inactive_expired</div>
</div>

</div>  <!-- well -->
<div class="row">
<div class="col-sm-4"><b>Members Addresses:</b></div>
</div>
<div class="well">
<div class="row">
<div class="col-sm-4">Missing Address Line: $noaddr</div>
<div class="col-sm-4">Missing City: $nocity</div>
</div>
<div class="row">
<div class="col-sm-4">Missing State: $nostate</div>
<div class="col-sm-4">Missing Zip: $nozip</div>
</div>
<div class="row">
<div class="col-sm-4">Missing All Address Info: $missingall</div>
<div class="col-sm-4">Mail Not OK: $nomail</div>
</div>
</div>  <!-- well -->
<div class="row">
<div class="col-sm-6"><b>Member phone numbers and email addresses:</b></div>
</div>
<div class="well">
<div class="row">
<div class="col-sm-4">With Email Address: $email</div>
<div class="col-sm-4">With Phone Number: $phone</div>
</div>
<div class="row">
<div class="col-sm-4">With Neither: $neither</div>
</div>
<div class="row">
<div class="col-sm-4">Email OK: $okemail</div>
<div class="col-sm-4">Email Not OK: $noemail</div>
</div>
</div> <!-- well -->

<h4>Funding Summary</h4>
formatMembers3;

$sql = "select * from donations order by `DonationID`";
$res = doSQLsubmitted($sql);
$numrows = $res->num_rows;
$thisyear = date('Y',strtotime(now));
$thisyrmo = date('Y-m',strtotime(now));
$expdate = date('Y-m-01',strtotime('-11 months'));
$duesarray = array(); $mcidarray = array(); $exparray = array();
while ($r = $res->fetch_assoc()) {
	$programs[$r[Program]] += 1; $programsamt += $r[TotalAmount];
	$purposes[$r[Purpose]] += 1; $purposesamt += $r[TotalAmount];
	$campaigns[$r[Campaign]] += 1; $campaignamt += $r[TotalAmount];
	if (($r[DonationDate] >= $expdate) AND ($r[Purpose] == 'Dues')) {
		$donmo = substr($r[DonationDate],0,7);
		$mcid = $r[MCID];
		$duesarray[$donmo]++;
		$mcidarray[$mcid] = $donmo;
		}
	else $exparray[$mcid]++;
	}
$mbrcount = count($mcidarray);
$expcount = count($exparray);		// an interesting number but not indicitive of lapsed members
asort($mcidarray);
$moarray = array();
foreach ($mcidarray as $m => $d) {
	$moarray[$d]++;
	}
//echo '<pre>MCID'; print_r($mcidarray); echo '</pre>';
//echo '<pre>Month'; print_r($moarray); echo '</pre>';
$purcount = count($purposes); $progcount = count($programs); $campcount = count($campaigns);
$purposesamt = number_format($purposesamt,2);
$programsamt = number_format($programsamt,2);
$campaignsamt = number_format($campaignamt,2);

print <<<formatSummary0
<div class="row">
<div class="col-sm-4"><b>Funding Records:</b></div>
</div>
<div class="well">
<div class="row">Total Number of Funding Records: $numrows</div>
<div class="row">Current annual paid-up members: $mbrcount, Expiration Date used: $expdate</div>
<div class="row">Monthly expiration distribution: (NOTE: number of &apos;Dues paid&apos; records expiring each month.)</div>
<div class="row">
formatSummary0;
foreach ($moarray as $k=>$v) {
	echo '<div class="col-sm-2">'.$k.': '.$v.'</div>';
	}
print <<<formatSummary1
</div>  <!-- row -->
</div>  <!-- well -->
<div class="row">
<div class="col-sm-4"><b>Purposes:</b></div>
</div>
<div class="well">
<div class="row">
formatSummary1;
if (count($purposes) > 0) foreach ($purposes as $k=>$v) {
	if ($k == "") echo "<div class=\"col-sm-3\">NONE: $v</div>";
	else echo "<div class=\"col-sm-3\">$k: $v</div>";
	}
print <<<formatSummary2
</div>
<div class="row">
<div class="col-sm-3">Total count - All Purposes: $purcount</div>
<div class="col-sm-4">Total funding - All Purposes: $$purposesamt</div>
</div>

</div>  <!-- well -->
<div class="row">
<div class="col-sm-4"><b>Programs:</b></div>
</div>
<div class="well">
<div class="row">
formatSummary2;
if (count($programs) > 0) foreach ($programs as $k=>$v) {
	if ($k == "") echo "<div class=\"col-sm-3\">NONE: $v</div>";
	else echo "<div class=\"col-sm-3\">$k: $v</div>";
	}
print <<<formatSummary3
</div>
<div class="row">
<div class="col-sm-3">Count of All Programs: $progcount</div>
<div class="col-sm-4">Total funding - All Programs: $$programsamt</div>
</div>

</div>  <!-- well -->
<div class="row">
<div class="col-sm-4"><b>Campaigns:</b></div>
</div>
<div class="well">
<div class="row">
formatSummary3;
if (count($campaigns) > 0) foreach ($campaigns as $k=>$v) {
	if ($k == "") echo "<div class=\"col-sm-3\">NONE: $v</div>";
	else echo "<div class=\"col-sm-3\">$k: $v</div>";
	}
print <<<formatSummary4
</div>  <!-- row -->
<div class="row">
<div class="col-sm-3">Count of All Campaigns: $campcount</div>
<div class="col-sm-4">Total funding - All Programs: $$campaignsamt</div>
</div>
</div>  <!-- well -->
<div class="row">
</div>
<h4>Correspondence Summary</h4>
formatSummary4;

// correspondence table report
$sql = "SELECT * from `correspondence` where 1";
$res = doSQLsubmitted($sql);
while ($r = $res->fetch_assoc()) {
	$rec_count++;
	$corrtype[$r[CorrespondenceType]] += 1;
	}
$type_count = count($corrtype);
print <<<corrReport
Total number of Correspondence records: $rec_count<br />
<div class="well">
<div class="row">
corrReport;
if (count($corrtype) > 0) foreach ($corrtype as $k => $v) {
	if ($k == "") echo "<div class=\"col-sm-3\">NONE: $v</div>";
	else echo "<div class=\"col-sm-3\">$k: $v</div>";
	}
print <<<corrSummary
</div>  <!-- row -->
<div class="row">
<div class="col-sm-4">Total count - all correspondence types: $type_count</div>
</div>
</div>  <!-- well -->
<div class="row">
</div>
corrSummary;

?>
<p class="text-info">* Many classes identified as "Purposes", "Programs", "Campaigns" and "Corresondence Types" are historical and could probably be retired and removed.</p>
</div>  <!-- container -->

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
