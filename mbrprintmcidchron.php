<!DOCTYPE html>
<html>
<head>
<title>MCID Chronology Information</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
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

//include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/seccheck.inc.php';
//include 'Incls/mainmenu.inc.php';

if (isset($_REQUEST['filter'])) $mcid = $_REQUEST['filter'];
else if (isset($_SESSION['ActiveMCID'])) $mcid = $_SESSION['ActiveMCID'];
if ($mcid == "") {
	print <<<errMsg
<div class="container">
<br /><h4>ERROR: there is no Active MCID.&nbsp;&nbsp;&nbsp;
<!-- <a href="javascript:self.close();" class="btn btn-primary"><b>CLOSE</b></a> -->
<a class="btn btn-primary" href="mbrinfotabbed.php">RETURN</a>
<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></div></body></html>
errMsg;
	exit;
	}

// get info for MCID
$sql = "SELECT * FROM `members` WHERE `MCID` = '".$mcid."'";
$mrec = doSQLsubmitted($sql);
$mrec->data_seek(0);
$r = $mrec->fetch_assoc();
//echo "<pre>MCID info: "; print_r($row); echo "</pre>";
echo "<div class=\"container\">";
//echo "<h3>MCID Information   <a href=\"javascript:self.close();\" class=\"btn btn-primary\"><strong>CLOSE</strong></a></h3>";
//echo '<div class="page-break"></div>';
echo "<h3>MCID Information
<a class=\"btn btn-primary\" href=\"mbrinfotabbed.php\">RETURN</a></h3>";
echo "<table class=\"table-condensed\">";
echo "<tr><h4>MCID: ".$r[MCID]."</h4></tr>";
echo "<tr><td>Organization: ".$r[Organization]."</td></tr>";
echo "<td>Label Line 1: ".$r['NameLabel1stline']."</td>";
echo "<tr><td>Date Joined: ".$r['MemDate']."</td>";
echo "<td>Member Status: ".$r['MemStatus']."</td>";
echo "<td>MC Type: ".$r['MCtype']."</td></tr>";
//echo "<td>No Mail: ".$r['Mail']."</td></tr>";
//echo "<tr><td>Email: ".$r['E_Mail']."</td>";
echo "<tr><td>Inactive: ".$r['Inactive']."</td>";
echo "<td>Inactive Date: ".$r['Inactivedate']."</td></tr>";
//echo "<tr><td colspan=\"3\">Notes: ".$r['Notes']."</td></tr>";
echo "</tr>";
echo "</table>";

// report any EDI for donor
if (($_SESSION['SecLevel'] == "devuser") OR ($_SESSION['SecLevel'] == "admin")) {
echo "<h4>Extended Donor Information</h4>";
$sql = "SELECT * FROM `extradonorinfo` WHERE `MCID` = '$mcid';";
$res = doSQLsubmitted($sql);
	$nbr_rows = $res->num_rows;
	if ($nbr_rows == 0) {
		echo "<ul><h5>No Extended Donor Info for MCID</h5></ul>";
		}
	else {
		$r = $res->fetch_assoc();
		echo "<table class=\"table\">";
		echo "<tr><th>MCID</th><th>Name</th><th>Date Entered</th><th>Last Updated</th><th>Last Updater</th></tr>";
		echo "<tr><td>$mcid</td><td>$r[NameLabel1stline]</td><td>$r[DateEntered]</td><td>$r[LastUpdated]</td><td>$r[LastUpdater]</td></tr>";
		echo "</table>";
		
		echo "<h5>Personal</h5><pre>$r[personal]</pre>";
		echo "<h5>Education</h5><pre>$r[education]</pre>";
		echo "<h5>Business</h5><pre>$r[business]</pre>";
		echo "<h5>Other Affiliations</h5><pre>$r[other]</pre>";
		echo "<h5>Wealth Sources</h5><pre>$r[wealth]</pre>";
		}
	}		

// report all donation records
$dontotal = 0;
$sql = "SELECT * FROM `donations` WHERE MCID = '".$mcid."' order by `DonationID` desc";
$dflds = doSQLsubmitted($sql);

$dontotal = $dreccount = 0;
while ($r = $dflds->fetch_assoc()) {
	$key = sprintf("%sF%06.0d",$r[DonationDate],$r[DonationID]);
	//$dresultarray[$key] = $r;
	$totaleventarray[$key] = $r;
	$dontotal += $r['TotalAmount'];
	$dreccount++;
	//echo "<pre>donor records :"; print_r($r); echo "</pre>";
	}
//echo "<pre>donor records :"; print_r($dresultarray); echo "</pre>";
$dontotalformatted = number_format($dontotal,2);
echo "<h4>Chronological Event Summary</h4>";
echo "Funding(F) record count: $dreccount<br>";

// report all correspondence records
$sql = "SELECT * FROM `correspondence` WHERE MCID = '".$mcid."' order by `CORID` desc";
$cflds = doSQLsubmitted($sql);
$trecordcount = 0;
while ($r = $cflds->fetch_assoc()) {
	$key = sprintf("%sC%06.0d",$r[DateSent],$r[CORID]);
	$totaleventarray[$key] = $r;
	$trecordcount++;
	//echo "<pre>corr. record :"; print_r($r); echo "</pre>";
	}
echo "Correspondence(C) record count: $trecordcount<br>";
echo "Total Funding Provided: $$dontotalformatted<br />";
krsort($totaleventarray);
echo "<table class=\"table-condensed\">";
echo "<tr><th>Date</th><th>Type</th><th>Rec#</th><th>Purpose</th><th>Program</th><th>Campaign</th><th>Amount</th><th>Corr. Type</th><th>Notes</th></tr>";
foreach ($totaleventarray as $k=>$r) {
	$date = substr($k,0,10); 	$type = substr($k,10,1);	$seq = substr($k,11,6);
	if ($type == 'F') 
		echo "<tr><td>$date</td><td align=\"center\">$type</td><td>$seq</td><td>$r[Purpose]</td><td>$r[Program]</td><td>$r[Campaign]</td><td align=\"right\">$$r[TotalAmount]</td><td>-</td><td>$r[Note]</td></tr>";
	if ($type == 'C')
	echo "<tr><td>$date</td><td align=\"center\">$type</td><td>$seq</td><td>-</td><td>-</td><td>-</td><td align=\"right\">-</td><td>$r[CorrespondenceType]</td><td>$r[Notes]</td></tr>";
	}
echo "</table><br />---END OF SUMMARY--<br />";
?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>
