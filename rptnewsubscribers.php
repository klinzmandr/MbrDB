<!DOCTYPE html>
<html>
<head>
<title>New Subscribers Report</title>
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
$sd = isset($_REQUEST[sd]) ? $_REQUEST[sd] : date('Y-m-01', strtotime("previous month -2 months"));
$ed = isset($_REQUEST[ed]) ? $_REQUEST[ed] : date('Y-m-t', strtotime("previous month"));

if (($sd == "") OR ($ed == "")) $action = '';

// set up intro page
if ($action == '') {	
print <<<pagePart1
<div class="container">
<h3>New Subscribers Report&nbsp;&nbsp;&nbsp;<a class="btn btn-sm btn-primary" href="javascript:self.close();">(CLOSE)</a></h3>
<p>This report is a listing of members registered as &apos;subscribers&apos; who have joined within the date range period (default of 3 months) listed with their total counts, contributions and contact information.</p>
<p>The &apos;Date Joined&apos; is set on introduction of the member into the database.  It can not be changed once established.</p>

<form action="rptnewsubscribers.php" method="post">
Start Date:
<input type="text" name="sd" id="sd" value="$sd">
End Date:<input type="text" name="ed" id="ed" value="$ed" >
<input type="hidden" name="action" value="continue">
<input type="submit" name="submit" value="Submit">
</form>
</div>  <!-- container -->
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc"></script>
pagePart1;
exit;
}

print <<<pagePart2
<div class="container">
<h3>New Subscribers Report&nbsp;&nbsp;&nbsp;<a class="btn btn-sm btn-primary" href="javascript:self.close();">(CLOSE)</a></h3>
pagePart2;
$sql = "SELECT `donations`.`MCID`, `donations`.`DonationDate`, `donations`.`Program`, `donations`.`TotalAmount`, `members`.* 
	FROM `pwcmbrdb`.`members` AS `members`, `pwcmbrdb`.`donations` AS `donations` 
	WHERE `members`.`MCID` = `donations`.`MCID` 
		AND `donations`.`DonationDate` BETWEEN '$sd' AND '$ed' 
		AND `donations`.`Program` LIKE '%subscr%' 
	ORDER BY `donations`.`MCID` ASC";
$res = doSQLsubmitted($sql);
$rowcnt = $res->num_rows;
if ($rowcnt == 0) {
	echo '<h3>No subscribers found in date range provivded</h3>';
	print<<<endPage
	</div>  <!-- container -->
<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc"></script>
</body></html>

endPage;
exit;
	}
//echo "SQL: $sql<br />";
$ra = array();		// array to capture results of query
$tots = array(); 	// array for counts and totals
while($r = $res->fetch_assoc()) {
	if ((strtotime($r[MemDate]) <= strtotime($sd)) OR (strtotime($r[MemDate]) >= strtotime($ed)))
		continue; 
	$ra[$r[MCID]] = $r;
	$tots[$r[MCID]][count] += 1;
	$tots[$r[MCID]][total] += $r[TotalAmount];
	}

echo "Date range from $sd to $ed<br>";
echo "<a href=\"downloads/NewSubscribers.csv\" download=\"NewSubscribers.csv\">DOWNLOAD CSV FILE</a>";
echo "<button type=\"button\" class=\"btn btn-xs btn-default\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Fields separated by semicolon(;)\nText fields are quoted.\"><span class=\"glyphicon glyphicon-info-sign\" style=\"color: blue; font-size: 20px\"></span></button>";
$csv[] = "MCID;Cnt;TotAmt;Name;Date Joined;Phone;Email\n";
echo '<table class="table-condensed">';
echo '<tr><th>MCID</th><th>Cnt</th><th>TotAmt</th><th>MemType</th><th>Full Name</th><th>DateJoined</th><th>Phone Nbr</th><th>Email Address</th></tr>';

// NOTE: may want to do an 'array_multisort' to sort by MemDate (date joined)

foreach ($ra as $r) {
	$mcid = $r[MCID];
	$total = $tots[$mcid][total];
	$count = $tots[$mcid][count];
	echo "<tr><td>$r[MCID]</td><td align='right'>$count</td><td align=\"right\">$$total</td><td>$r[MCtype]</td><td>$r[NameLabel1stline]</td><td>$r[MemDate]</td><td>$r[PrimaryPhone]</td><td>$r[EmailAddress]</td></tr>";
	$csv[] = "$r[MCID];$count;$$total;$r[NameLabel1stline];$r[MemDate];$r[PrimaryPhone];$r[EmailAddress]\n";
	}
echo '</table>----- End of Report -----';		
file_put_contents("downloads/NewSubscribers.csv", $csv);
?>
</div>  <!-- container -->
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc"></script>
</body>
</html>
