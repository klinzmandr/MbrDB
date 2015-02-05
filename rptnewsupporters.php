<!DOCTYPE html>
<html>
<head>
<title>New Supporters Report</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/datepicker3.css" rel="stylesheet">
</head>
<body>
<?php
session_start();
//include 'Incls/vardump.inc';
include 'Incls/seccheck.inc';
include 'Incls/datautils.inc';

$action = isset($_REQUEST[action]) ? $_REQUEST[action] : '';
$sd = isset($_REQUEST[sd]) ? $_REQUEST[sd] : date('Y-01-01', strtotime(now));
$ed = isset($_REQUEST[ed]) ? $_REQUEST[ed] : date('Y-m-t', strtotime(now));

if (($sd == "") OR ($ed == "")) $action = '';

print <<<formPart
<!-- <div class="container"> -->
<h3>New Supporters Report&nbsp;&nbsp;&nbsp;<a class="btn btn-sm btn-primary" href="javascript:self.close();">(CLOSE)</a></h3>
<form action="rptnewsupporters.php" method="post">
Start Date:
<input type="text" name="sd" id="sd" value="$sd">
End Date:<input type="text" name="ed" id="ed" value="$ed" >
<input type="hidden" name="action" value="continue">
<input type="submit" name="submit" value="Submit">
</form>
<!-- <a class="btn btn-primary" href="rptnewsupporters.php?action=continue">Continue</a> -->

formPart;

if ($action == '') {
// set up intro page	
print <<<pagePart1
<p>This report is a listing of new contacts, members, volunteers or donors that have <b>joined</b> within the following specific date range (default is THIS year to date).</p>
<p>Listed supporters are selected by comparing the &apos;Date Joined&apos; of each supporter record to the specified date range. If the &apos;Date Joined&apos; is within the specified date range, it is included in this listing.  Funding records for each selected member are included in the totals if they are also within the specified date range and are marked as &apos;Dues&apos; or a &apos;Donation&apos;.</p>
<p>The &apos;Date Joined&apos; of the supporter record is set on introduction of the supporter into the database.  It can not be changed once established.</p>
<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc"></script>
</body></html>

pagePart1;
exit;
}
if ($action == 'continue') {
	$sql= "SELECT `members`.*, `members`.`MemDate`, `members`.`MCID`, `donations`.`MCID` AS `MCID-Funding`, `donations`.`DonationDate`, `donations`.`TotalAmount`, `donations`.`Purpose`, `members`.`MemDate` 
	FROM `pwcmbrdb`.`donations` AS `donations`, 
		`pwcmbrdb`.`members` AS `members` 
	WHERE `donations`.`MCID` = `members`.`MCID` 
	AND `members`.`MemDate` BETWEEN '$sd' AND '$ed' 
	AND `donations`.`DonationDate` BETWEEN '$sd' AND '$ed' 
	AND ( `donations`.`Purpose` = 'donation' OR `donations`.`Purpose` = 'dues' ) 
	ORDER BY `members`.`MCID` ASC;";
	$res = doSQLsubmitted($sql);
	$rowcnt = $res->num_rows;
	if ($rowcnt == 0) {
		echo '<h3>No new supporters found in date range provivded</h3>';
		print<<<endPage
		</div>  <!-- container -->
<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc"></script>
</body></html>

endPage;
		exit;
		}
// process SQL results		
//	echo "SQL: $sql<br />";
	$fa = array();					// array to capture csv file for download
	$mcidarray = array(); 	// array to capture html output
	$mcidcounts = array(); 	// array to capture counts and totals
	$translate = array("\\" => ' ', "\n" => ' ', "\t"=>' ', "\r"=>' ', "\"" =>'');

	$duestot = 0; $dontot = 0;
	while ($r = $res->fetch_assoc()) {
//		echo '<pre> New member '; print_r($r); echo '</pre>';
		unset($r[MbrID]);
		unset($r[TimeStamp]);
		unset($r[Source]);
		unset($r[Account]);
		unset($r[Member]);
		unset($r[E_Mail]);
		unset($r[Inactive]);
		unset($r[Inactivedate]);
		unset($r[Mail]);
		unset($r[PaidMemberYear]);
		unset($r[MasterMemberID]);
		unset($r[CorrSal]);
		unset($r[Lists]);
		unset($r[LastDonDate]);
		unset($r[LastDonPurpose]);
		unset($r[LastDonAmount]);
		unset($r[LastDuesDate]);
		unset($r[LastDuesAmount]);
		unset($r[LastCorrDate]);
		unset($r[LastCorrType]);
		
		$mcidarray[$r[MCID]] = $r;		// save for output
		if ($r[Purpose] == 'Dues') {
			$mcidcounts[$r[MCID]][duescount] += 1;
			$mcidcounts[$r[MCID]][dues] += $r[TotalAmount]; 
			$duestot += $r[TotalAmount]; }
		else {
			$mcidcounts[$r[MCID]][doncount] += 1;
			$mcidcounts[$r[MCID]][donations] += $r[TotalAmount]; 
			$dontot += $r[TotalAmount]; }
		
		
		}
//	echo '<pre>mcid '; print_r($mcidarray); echo '</pre>';
//	echo '<pre>mcid counts '; print_r($mcidcounts); echo '</pre>';

/*
// dump in csv format
*/

//	create reports
	$duestot = number_format($duestot);
	$dontot = number_format($dontot);
	$rowcnt = count($mcidarray);
	$hdr = 'MCID;MemStatus;MemDate;MCType;Lname;Fname;Org;Address;City;St;Zip;';
	$hdr .= "Phone;Email;DuesCt;TotDues;DonCt;TotDonations;Notes\n";
	$fa[] =  $hdr;
	echo "<h5>New supporters in date range: $rowcnt with total dues paid of $$duestot and total donations paid of $$dontot</h5>";
	echo "<a href=\"downloads/Supporters.csv\" download=\"supporters.csv\">DOWNLOAD CSV FILE</a>";
	echo "<button type=\"button\" class=\"btn btn-xs btn-default\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Downloaded file contains more fields than shown\nFields separated by semicolon(;)\nText fields are quoted.\"><span class=\"glyphicon glyphicon-info-sign\" style=\"color: blue; font-size: 20px\"></span></button>";

// create table report
echo '<table class="table">
	<tr><th>MCID</th><th>MemStatus</th><th>MC Type</th><th>DateJoined</th><th>TotDues</th><th>TotDon</th><th>LabelLine1</th><th>Email Address</th><th>Phone</th><th>Notes</th>';
foreach ($mcidarray as $k => $r) {
		$r[Notes] = strtr($r[Notes], $translate);
		
		$dues = number_format($mcidcounts[$k][dues]);
		$duescsv = $mcidcounts[$k][dues];
		$duescsvct = '';
		if (isset($mcidcounts[$k][duescount])) {
			$duescsvct = $mcidcounts[$k][duescount];
			$dues = '(' . number_format($mcidcounts[$k][duescount]) . ')$' . $dues; }
			
		$dons = number_format($mcidcounts[$k][donations]);
		$donscsv = $mcidcounts[$k][donations];
		$donscsvct = '';
		if (isset($mcidcounts[$k][doncount])) {
			$donscsvct = $mcidcounts[$k][doncount];
			$dons = '(' . number_format($mcidcounts[$k][doncount]) . ')$' . $dons; }
		echo "<tr><td>$r[MCID]</td><td>$r[MemStatus]</td><td>$r[MCtype]</td><td>$r[MemDate]</td><td align=right>$dues</td><td align=right>$dons</td><td>$r[NameLabel1stline]</td><td>$r[EmailAddress]</td><td>$r[PrimaryPhone]</td><td>$r[Notes]</td></tr>";
		$r[MCID] = "\"$r[MCID]\"";		// escape for csv output
		$r[Notes] = "\"$r[Notes]\"";
		$r[AddressLine] = "\"$r[AddressLine]\"";
		$fa[] = "$r[MCID];$r[MemStatus];$r[MemDate];$r[MCType];$r[LName];$r[FName];$r[Organization];$r[AddressLine];$r[City];$r[State];$r[ZipCode];$r[PrimaryPhone];$r[EmailAddress];$duescsvct;$duescsv;$donscsvct;$donscsv;$r[Notes]\n";
//	echo '<pre> New member '; print_r($r); echo '</pre>';
		}
	echo '</table>----- End of Report -----';		
	file_put_contents('downloads/Supporters.csv',$fa); // save csv file
	}
// echo '<pre> csv '; print_r($fa); echo '</pre>';

?>
</div>  <!-- container -->
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc"></script>
</body>
</html>
