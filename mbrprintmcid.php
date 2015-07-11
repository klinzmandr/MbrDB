<br><!DOCTYPE html>
<html>
<head>
<title>MCID Information</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="all">
<style>
  .page-break  {
    clear: left;
    display:block;
    page-break-after:always;
    }
</style>
</head>
<body>

<!-- <p>Dump of all information for the 'ActiveMCID'</p> -->
<?php
session_start();

//include 'Incls/vardump.inc';
include 'Incls/datautils.inc';
include 'Incls/seccheck.inc';
echo '<div class="hidden-print">';
include 'Incls/mainmenu.inc';
echo '</div>';

if (isset($_REQUEST['filter'])) $mcid = $_REQUEST['filter'];
else if (isset($_SESSION['ActiveMCID'])) $mcid = $_SESSION['ActiveMCID'];
echo "<div class=\"container\">";
if ($mcid == "") {
	
	echo "<br /><h4>ERROR: there is no Active MCID.";
//	echo "<a href=\"javascript:self.close();\" class=\"btn btn-primary\"><b>CLOSE</b></a></h4>";
//	echo "<a class=\"btn btn-primary\" href=\"mbrinfotabbed.php\">RETURN</a>";
	echo '<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>';
	exit;
	}

// get info for MCID
$sql = "SELECT * FROM `members` WHERE `MCID` = '".$mcid."'";
$mrec = doSQLsubmitted($sql);
$mrec->data_seek(0);
$r = $mrec->fetch_assoc();
//echo "<pre>MCID info: "; print_r($row); echo "</pre>";

//echo "<h3>MCID Information   <a href=\"javascript:self.close();\" class=\"btn btn-primary\"><strong>CLOSE</strong></a></h3>";
//echo '<div class="page-break"></div>';
//echo "<h3>MCID Information&nbsp;&nbsp;&nbsp;
//<a class=\"btn btn-primary\" href=\"mbrinfotabbed.php\">RETURN</a></h3>";
//echo "<h4>MCID Information</h4>";
//echo "<pre>donor records :"; print_r($r); echo "</pre>";
echo "<h2>MCID Information</h2>";
echo "<table>";
echo "<tr><td><b>MCID: ".$r[MCID]."</b></td></tr>";
echo "<tr><td>Organization ".$r[Organization]."</td></tr>";
echo "<tr><td>First Name: ".$r[FName]."</td>";
echo "<td>Last Name: ".$r['LName']."</td></tr>";
echo "<td>Label Line 1: ".$r['NameLabel1stline']."</td>";
echo "<td>Salutation: ".$r['CorrSal']."</td></tr>";
echo "<tr><td>Address: ".$r['AddressLine']."</td>";
echo "<td>City: ".$r['City']."</td></tr>";
echo "<tr><td>State: ".$r['State']."</td>";
echo "<td>Zip: ".$r['ZipCode']."</td></tr>";
echo "<tr><td>Home Phone: ".$r['PrimaryPhone']."</td>";
echo "<td>Email: ".$r['EmailAddress']."</td></tr>";
echo "<tr><td>Date Joined: ".$r['MemDate']."</td>";
echo "<td>Member Status: ".$r['MemStatus']."</td>";
echo "<tr><td>MC Type: ".$r['MCtype']."</td>";
echo "<td>No Mail: ".$r['Mail']."</td></tr>";
echo "<tr><td>Email: ".$r['E_Mail']."</td>";
echo "<td>Inactive: ".$r['Inactive']."</td>";
echo "<td>Inactive Date: ".$r['Inactivedate']."</td></tr>";
echo "<tr><td colspan=\"3\">Notes: ".$r['Notes']."</td></tr>";
//echo "</tr>";
echo "</table></div>";

// report any EDI for donor
if (($_SESSION['SecLevel'] == "devuser") OR ($_SESSION['SecLevel'] == "admin")) {
echo "<div class=\"container\"><h4>Extra Donor Information</h4>";
$esql = "SELECT * FROM `extradonorinfo` WHERE `MCID` = '$mcid';";
$eres = doSQLsubmitted($esql);
	$enbr_rows = $eres->num_rows;
	if ($enbr_rows == 0) {
		echo "<div class=\"container\"><h4>NONE</h4></div>";
		}
	else {
		$er = $eres->fetch_assoc();
		echo "<div class=\"container\"><table class=\"table\">";
		echo "<tr><th>MCID</th><th>Name</th><th>Date Entered</th><th>Last Updated</th><th>Last Updater</th></tr>";
		echo "<tr><td>$mcid</td><td>$r[NameLabel1stline]</td><td>$r[DateEntered]</td><td>$r[LastUpdated]</td><td>$r[LastUpdater]</td></tr>";
		echo "</table>";
		
		echo "<h5>Personal</h5><pre>$er[personal]</pre>";
		echo "<h5>Education</h5><pre>$er[education]</pre>";
		echo "<h5>Business</h5><pre>$er[business]</pre>";
		echo "<h5>Other Affiliations</h5><pre>$er[other]</pre>";
		echo "<h5>Wealth Sources</h5><pre>$er[wealth]</pre>";
		
		$psql = "SELECT * from `photos` WHERE `MCID` = '$mcid'";
		$pres = doSQLsubmitted($psql);
		$prows = $pres->num_rows;
		echo "<h5>Photos and documents available online: $prows</h5>";
		echo '</div>';		
		}
	}

// report volunteer committees/email distro lists
echo '<h4>Volunteer Committees/Email List(s)</h4>';
$lists = $r[Lists];
if (strlen($lists) == 0) {
	echo '<div class="container"><h4>NONE</h4></div>'; }
else {
	$liststr = readdblist('EmailLists');
	$listarray = formatdbrec($liststr);
	$vollists = explode(",", rtrim($lists));
//	echo '<pre>vol list '; print_r($vollists); echo '</pre>';
//	echo '<pre> vol cats '; print_r($listarray); echo '</pre>';
	echo '<div class="container">
	<table>';
	foreach ($vollists as $v) {
		if (isset($listarray[$v])) echo "<tr><td>$listarray[$v]</td></tr>";
		}
	echo '</table></div>';
	}


// report any volunteer time
echo "<h4>Volunteer Time Served</h4>";
$sql = "SELECT * FROM `voltime` WHERE `MCID` = '".$mcid."' ORDER BY `VolDate`;";
// echo "sql: $sql<br>";
$voltime = doSQLsubmitted($sql);
$nbr_rows = $voltime->num_rows;
$vsd = date("Y-m-d", strtotime("now + 1 year")); $vld = '2014-01-01'; $volsvc = array();
if ($nbr_rows == 0) {
	echo "<div class=\"container\"><h4>NONE</h4></div>";
	} 
else {
	while ($r = $voltime->fetch_assoc()) {
//		echo "<pre>volunteer records :"; print_r($r); echo "</pre>";
		if (strtotime($vsd) > strtotime($r[VolDate])) $vsd = $r[VolDate]; 
		if (strtotime($vld) < strtotime($r[VolDate])) $vld = $r[VolDate]; 
		$volsvc[$r[VolCategory]][time] += $r[VolTime];
		$volsvc[$r[VolCategory]][miles] += $r[VolMileage];
		$volsvc[$r[VolCategory]][count] += 1;
	}
echo "<div class=\"container\">";
echo "<b>Earliest date: $vsd, Latest date: $vld</b><br>";
echo '
<table border=0><tr><th>Category</th><th>TotHours</th><th>SvcCnt</th><th>TotMileage</th></tr>';
foreach ($volsvc as $k => $v) {
	$tothrs += $v[time]; $totsvc += $v[count]; $totmiles += $v[miles];
	echo "
<tr><td>$k</td><td align=right>$v[time]</td><td align=right>$v[count]</td><td align=right>$v[miles]</td></tr>"; 
	}
echo "
<tr><td><b>TOTALS</b></td><td align=right>$tothrs</td><td align=right>$totsvc</td><td align=right>$totmiles</td>";
echo '
</tr></table></div>';
}

// now report any vol time from previous vol time records
$sql = "SELECT * from `voltimeprev` 
WHERE `TMCID` = '$mcid' 
ORDER BY `SvcDate` ASC";
$res = doSQLsubmitted($sql);
$rowcnt = $res->num_rows;
if ($rowcnt > 0) {
	$vsd = date("Y-m-d", strtotime("now + 1 year")); $vld = '2000-01-01'; 
// table: voltime: VTID,VTDT,MCID,VolDate,VolTime,VolMilage,VolCategory,VolNotes
	$totalvolhrs = $totaltranshrs = $totmiles = 0;
	while ($r = $res->fetch_assoc()) {
		if (strtotime($vsd) > strtotime($r[SvcDate])) $vsd = $r[SvcDate]; 
		if (strtotime($vld) < strtotime($r[SvcDate])) $vld = $r[SvcDate];
		$totalvolhrs += $r[VolHrs];
		$totaltranshrs += $r[TransHrs];
		$totmiles += $r[Mileage];
		}
echo '<h4>Volunteer Service Prior to Jan 1, 2014</h4>';
echo '<div class=container><table>';
echo "<tr><td><b>Previous Service Entry Count:</b></td><td align=right>$rowcnt</td></tr>";
echo "<tr><td>Earliest Date:</td><td>$vsd</td></tr>";
echo "<tr><td>Latest Date:</td><td>$vld</td></tr>";
echo "<tr><td>Total Volunteer Hours:</td><td align=right>$totalvolhrs</td></tr>";
echo "<tr><td>Total Transporter Hours:</td><td align=right>$totaltranshrs</td></tr>";
echo "<tr><td>Total Miles Driven:</td><td align=right>$totmiles</td></tr>";
echo '</table></div>';
}

// report all education classes attended
$sql = "SELECT * FROM `voltime` 
	WHERE MCID = '".$mcid."'
	AND `VolCategory` = 'Education'  
	ORDER BY `VolDate` desc";
$resed = doSQLsubmitted($sql);
$rced = $resed->num_rows;
echo '<h4>Volunteer Education Courses Taken</h4>';
echo '<div class=container>';
if ($rced) {
	echo '<table class="table-condensed">';
	echo '<tr><th>Agency</th><th>CourseId</th><th>CourseDate</th><th>Dur.</th><th>Notes</th></tr>';
	while ($r = $resed->fetch_assoc()) {
//	echo '<pre> ed rec '; print_r($r); echo '</pre>';
	list($ed,$notes) = explode('/',$r[VolNotes]);
	list($agency,$course) = explode(':',$ed);
		echo "<tr><td>$agency<td>$course</td><td>$r[volDate]</td><td>$r[VolTime]</td><td>$r[VolNotes]</td></tr>";
		$totaledhrs += $r[VolTime];
		}
	echo "</table>
	<br>Total Education Hours: $totaledhrs<br></div>";
	}
else echo '<h4>NONE</h4></div>'; 

// report all donation records
$dontotal = 0;
$sql = "SELECT * FROM `donations` WHERE MCID = '".$mcid."' order by `DonationID` desc";
$dflds = doSQLsubmitted($sql);
// echo '<div class="page-break"></div>';
echo "<h4>Funding</h4>";
echo "<div class=\"container\"><b><u>Funding Summary:</u></b>";
echo "<table>";
echo "<tr><th>Purpose</th><th>Amount</th></tr>";
$sql = "SELECT `MCID`, `Purpose`, SUM( `TotalAmount` ) FROM `donations` AS `donations` GROUP BY `MCID`, `Purpose` HAVING ( ( `MCID` = '$mcid' ) )";
$res = doSQLsubmitted($sql);
while ($r = $res->fetch_assoc()) {
	echo "<tr><td>".$r[Purpose]."</td><td>$".$r['SUM( `TotalAmount` )']."</td></tr>";
	//echo "<pre>summary records :"; print_r($r); echo "</pre>";
	}
echo "</table>";
echo "<br><b><u>Funding Detail:</u></b><br />";
echo "<table><tr><th>Don.ID</th><th>Purpose</th><th>Program</th><th>Don. Date</th><th>Check Nbr</th><th>Amount</th><th>Member For</th><th>Note</th></tr>";
while ($r = $dflds->fetch_assoc()) {
	echo "<tr><td>".$r[DonationID]."</td>";
	//echo "<td>".$r[MCID]."</td>";
	echo "<td>".$r[Purpose]."</td>";
	echo "<td>".$r[Program]."</td>";
	echo "<td>".$r[DonationDate]."</td>";
	echo "<td>".$r[CheckNumber]."</td>";
	echo "<td>".$r[TotalAmount]."</td>";
	echo "<td>".$r[MembershipDonatedFor]."</td>";
	echo "<td>".$r[Note]."</td></tr>";
	$dontotal += $r['TotalAmount'];
	//echo "<pre>donor records :"; print_r($r); echo "</pre>";
	}
echo "</table>";
$dontotalformatted = number_format($dontotal,2);
echo "Total Funding Provided: $$dontotalformatted<br /><br />";

echo "</div>";

// report all correspondence records
$sql = "SELECT * FROM `correspondence` WHERE MCID = '".$mcid."' order by `CORID` desc";
$cflds = doSQLsubmitted($sql);
$rowcount = $cflds->num_rows;
if ($rowcount > 0) {
	echo "<h4>Correspondence</h4><div class=\"container\">";
	echo "<table>";
	echo "<tr><th>Corr.ID</th><th>CorrespondenceType</th><th>DateSent</th><th>Source</th><th>Notes</th><th>Reminders</th></tr>";
	while ($r = $cflds->fetch_assoc()) {
		echo "<td>".$r[CORID]."</td>";
		echo "<td>".$r[CorrespondenceType]."</td>";
		echo "<td>".$r[DateSent]."</td>";
		echo "<td>".$r[SoureofInquiry]."</td>";
		echo "<td>".$r[Notes]."</td>";
		echo "<td>".$r[Reminders]."</td></tr>";
		}
	echo "</table>----- END OF REPORT -----</div>";
}


?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>
