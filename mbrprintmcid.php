<!DOCTYPE html>
<html>
<head>
<title>MCID Information</title>
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

//include 'Incls/vardump.inc';
include 'Incls/datautils.inc';
//include 'Incls/mainmenu.inc';
include 'Incls/seccheck.inc';

if (isset($_REQUEST['filter'])) $mcid = $_REQUEST['filter'];
else if (isset($_SESSION['ActiveMCID'])) $mcid = $_SESSION['ActiveMCID'];
echo "<div class=\"container\">";
if ($mcid == "") {
	
	echo "<br /><h4>ERROR: there is no Active MCID.";
	//echo "<a href=\"javascript:self.close();\" class=\"btn btn-primary\"><b>CLOSE</b></a></h4>";
	echo "<a class=\"btn btn-primary\" href=\"MbrInfotabbed.php\">RETURN</a>";
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
echo "<h3>MCID Information&nbsp;&nbsp;&nbsp;
<a class=\"btn btn-primary\" href=\"MbrInfotabbed.php\">RETURN</a></h3>";
//echo "<h4>MCID Information</h4>";
//echo "<pre>donor records :"; print_r($r); echo "</pre>";
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
echo "</tr>";
echo "</table></div>";

// report any EDI for donor
if (($_SESSION['SecLevel'] == "devuser") OR ($_SESSION['SecLevel'] == "admin")) {
echo "<div class=\"container\"><h4>Extra Donor Information</h4>";
$sql = "SELECT * FROM `extradonorinfo` WHERE `MCID` = '$mcid';";
$res = doSQLsubmitted($sql);
	$nbr_rows = $res->num_rows;
	if ($nbr_rows == 0) {
		echo "<h4>NONE</h4>";
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
//$dflds = readDonorRecords($mcid);	

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
echo "<b><u>Funding Detail:</u></b><br />";
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
		echo "<td>".$r[Reminders]."</td>";
		echo "<td>".$r[Notes]."</td></tr>";
		}
	echo "</table>----- END OF REPORT -----</div>";
}


?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>
