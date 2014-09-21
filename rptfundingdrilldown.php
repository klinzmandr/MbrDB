<!DOCTYPE html>
<html>
<head>
<title>Funding Summary</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/datepicker3.css" rel="stylesheet">
</head>
<body>
<?php
session_start();

include 'Incls/datautils.inc';
include 'Incls/seccheck.inc';

//$start = $_REQUEST['startdate'] : date('Y-m-d', strtotime("first day of previous month"));
$name = isset($_REQUEST['name']) ? $_REQUEST['name'] : "";
$listitem = isset($_REQUEST['listitem']) ? $_REQUEST['listitem'] : "";
$earliest = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : date('Y-m-01', strtotime("previous month"));
$today = date("Y-m-d",strtotime(now));
$oldest = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : date('Y-m-t', strtotime("previous month"));

if ($name == "") {
print <<<pagePart1
<div class="container">
<h3>Funding Summary Drilldown&nbsp;&nbsp;<a href="javascript:self.close();" class="btn btn-primary"><b>CLOSE</b></a></h3>
<p>This report provides the ability to list and detail the various funding categories that are currently in the funding records of the database.  The categories are: Purpose, Program and Campaign.  Each category should only the values defined by the organization.  When importing an existing database, many category values are retained and either should be redefined.  If not, then certain reports will have to be date constraigned to show on those funding items received since the implementation of the system.</p>
<p>Define the date range needed (the default is last calendar month) THEN choose one of the categories to examine.  A list of all unique values in that category will be listed.</p>
<p>Then you may select one of those values from the dropdown list to inspect the detail funding records associated with it.</p>
<p>NOTE:  this report is generatd from the values actually found on the database and does NOT take into consideration the values that have been defined for these categories when a new funding record is created.</p>

<form action="rptfundingdrilldown.php">
From:<input type="text" name="sd" id="sd" value="$earliest">
&nbsp;&nbsp;
To:<input type="text" name="ed" id="ed" value="$oldest">
<select name="name" onchange="this.form.submit()">
<option value="">Select a Funding Category:</option>
<option value="Purpose">Purpose</option>
<option value="Program">Program</option>
<option value="Campaign">Campaign</option>
</select>
</form>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc"></script>
</div>
</body>
</html>
pagePart1;
exit;

}

// a funding class is named, get items from db to count and summarize
echo "<div class=\"container\">";
echo "<h3>Listing for Category: $name &nbsp;&nbsp;<a href=\"javascript:self.close();\" class=\"btn btn-primary\"><b>CLOSE</b></a></h3>";
echo "<a href=\"rptfundingdrilldown.php?name=\">START OVER</a>&nbsp;&nbsp;";
$sql = "SELECT * 
	FROM `donations` 
	WHERE `DonationDate` BETWEEN '$earliest' AND '$oldest'
		AND `Purpose` IS NOT NULL 
	ORDER BY `Purpose`;";
//echo "SQL: $sql<br>";
$res = doSQLsubmitted($sql);
$startdate = array(); $enddate = array();
while ($r = $res->fetch_assoc()) {
	//echo "<pre>$name: "; print_r($r); echo "</pre>";
	$totreccount++;												// total record count from query
	$fn = $r[$name];											// $name = Purpose, Program or Category now
	$nt[$fn] += $r[TotalAmount];					// capture purpose, program or campaign name amount
	$nc[$fn] += 1;												// add up number of records
	if ($r[DonationDate] == "") {					// get earliest and last dates of records
		echo "Donation Date missing.  Rec Nbr: " . $r[DonationID] . ", MCID: ".$r[MCID]."<br>"; }
	else {
		if (!isset($startdate[$fn])) {
			$startdate[$fn] = '2020-01-01';
			}
		if (strtotime($startdate[$fn]) > strtotime($r[DonationDate])) $startdate[$fn] = $r[DonationDate];
		if (strtotime($enddate[$fn]) < strtotime($r[DonationDate])) $enddate[$fn] = $r[DonationDate];
		}
	}
//echo "<pre>Count Summary "; print_r($nc); echo "</pre>";
//echo "<pre>Total Summary "; print_r($nt); echo "</pre>";
//echo "<pre>Start Date "; print_r($startdate); echo "</pre>";
//echo "<pre>End Date "; print_r($enddate); echo "</pre>";
print <<<formPart1
<form class="form-inline" name="classform" action="rptfundingdrilldown.php">
<input type="hidden" name="name" value="$name">
<!-- <input type="hidden" name="sd" id="sd" value="$earliest">
<input type="hidden" name="ed" id="ed" value="$oldest"> -->
formPart1;
echo "From:<input type=\"text\" name=\"sd\" id=\"sd\" value=\"$earliest\">";
echo "&nbsp;&nbsp;To:<input type=\"text\" name=\"ed\" id=\"ed\" value=\"$oldest\">";
echo "<select name=\"listitem\" onchange=\"this.form.submit()\">
<option value=\"\">View funding records for:</option>";
foreach ($nc as $k => $v) {
	echo "<option value=\"$k\">$k</option>";
	}
echo "</select>";
echo "</form>";

if ($listitem == "") {
  echo "<div class=\"well\">";
	echo "<table border=\"0\" class=\"table-condensed\">";
	echo "<tr><td><b>Name</b></td><td><b>Rec.Count</b></td><td><b>Total Funds</b></td><td><b>Earliest Start</b></td><td><b>Latest End</b></td></tr>";

	foreach ($nc as $k=>$v) {
		if ($k == "") echo "<td>NONE: $v</td>";
		else { 
			$ftot = number_format($nt[$k],2);
			echo "<tr>";
			echo "<td>$k</td><td align=\"right\">$v</td><td align=\"right\">$$ftot</td><td>".$startdate[$k]."</td><td>".$enddate[$k]."</td>";
			echo "</tr>";
			$grandtot += $nt[$k];
			$catcount += $v;
			}
		}	
	$grtot = number_format($grandtot,2);
	echo "<tr><td>Total $name&apos;s: ". count($nc)."</td><td>Rec's: $catcount out of $totreccount</td><td>Total Value:</td><td>$". 	$grtot ."</td></tr>";
	echo "</table>";
	echo "</div> <!-- well -->";
	print <<<secForm
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc"></script>
</div>
</body>
</html>
secForm;
	exit;
	}

// do query and list all funding records for a specific item within a category
echo "Listing funding records for $listitem&nbsp;&nbsp;";
//echo "earliest: $earliest, oldest: $oldest<BR>";
//echo "<a href=\"rptfundingdrilldown.php?name=\">START OVER</a>&nbsp;&nbsp;";
echo "<a href=\"rptfundingdrilldown.php?name=$name&listitem=\">(Choose Again)</a><br />";

//$sql = "SELECT * FROM `donations` WHERE `$name` = '$listitem'";
$sql = "SELECT `donations`.`MCID`, `donations`.`$name`, `donations`.`DonationDate`, `donations`.`TotalAmount`, `members`.`NameLabel1stline` 
	FROM `members`, `donations`
	WHERE `members`.`MCID` = `donations`.`MCID` 
		AND `donations`.`$name` = '$listitem' 
		AND `DonationDate`	 BETWEEN '$earliest' AND '$oldest' 
	ORDER BY `MCID`;";
//echo "SQL: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
//echo "rc: $rc<br />";
while ($r = $res->fetch_assoc()) {
	//echo "<pre>$name: "; print_r($r); echo "</pre>";
	$resarray[] = $r;
	$detailtot += $r[TotalAmount];
	$detailcnt += 1;
	}
echo "<div class=\"well\">";
$detailtot = number_format($detailtot,2);
echo "Count: $detailcnt&nbsp;&nbsp;&nbsp;&nbsp;Total: $$detailtot<br />";
echo "<table class=\"table-condensed\">";
echo "<tr><th>MCID</th><th>$name</th><th>DonationDate</th><th>Amount</th><th>Donor Name</th></tr>";
foreach ($resarray as $r) {
		echo "<tr><td>$r[MCID]</td><td>".$r[$name]."</td><td>$r[DonationDate]</td><td align=\"right\">$$r[TotalAmount]</td><td>$r[NameLabel1stline]</td></tr>";
	}	
echo "</table>";
echo "----- END OF LIST -----<br>";
?>
<a href="rptfundingdrilldown.php?name="">START OVER</a>
</div>  <!-- container -->
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc"></script>

</div>
</body>
</html>
