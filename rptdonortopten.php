<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Donor Top 10 Summary</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/datepicker3.css" rel="stylesheet">
</head>
<body>
<?php
include 'Incls/seccheck.inc.php';
include 'Incls/datautils.inc.php';

$lodate = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : date('Y-01-01 ', strtotime("now"));
$hidate = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : date('Y-m-t', strtotime("previous month"));

echo "<div class=\"container\">";
echo "<h3>Donor Top 10 Summary  <a href=\"javascript:self.close();\" class=\"btn btn-primary\"><b>CLOSE</b></a></h3>";
print <<<periodForm
<form action="rptdonortopten.php" method="post"  class="form">
Period: From
<input type="text" name="sd" id="sd" value="$lodate" />
To
<input type="text" name="ed" id="ed" value="$hidate" />
<input type="submit" name="submit" value="Submit">
</form>
periodForm;

if ($lodate == '') {
	print <<<startRpt
</div>   <!-- container -->
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc.php"></script>
</body>
</html>
startRpt;
	exit;
	}

// top 10 by tranaction count
$sql = "SELECT `donations`.`MCID`, COUNT( `donations`.`MCID` ) AS `TransCnt`, 
	MAX( `donations`.`DonationDate` ) AS `LastDon`, 
	MAX( `donations`.`TotalAmount` ) AS `LargestDon`, 
	AVG( `donations`.`TotalAmount` ) AS `AvgDon`, 
	SUM( `donations`.`TotalAmount` ) AS `GrandTot`, 
	`members`.`NameLabel1stline` AS `Name` 
	FROM `donations`, `members` 
	WHERE `members`.`Inactive` = 'FALSE' 
		AND `donations`.`MCID` = `members`.`MCID` 
		AND `donations`.`DonationDate` BETWEEN '$lodate' AND '$hidate' 
	GROUP BY `donations`.`MCID` 
	ORDER BY `TransCnt` DESC 
	LIMIT 0 , 10;";
$res = doSQLsubmitted($sql);
echo "<h4>Top 10 By Transaction Count</h4>";
echo "<table class=\"table-condensed\">";
echo "<tr><th>MCID</th><th>Trans.Count</th><th>Last Funding</th><th>Largest Don.</th><th>Period Tot.</th><th>Period Avg.</th></tr>";
while ($r = $res->fetch_assoc()) {
	//echo "<pre>Top 30 by count->"; print_r($r); echo "</pre>";
	$avg = number_format($r[AvgDon],2); $last = number_format($r[LastDon],2); 
	$largest = number_format($r[LargestDon],2);	$grand = number_format($r[GrandTot],2);
echo "<tr><td>$r[MCID]</td><td align=\"center\">$r[TransCnt]</td><td align=\"center\">$r[LastDon]</td><td align=\"right\">$$largest</td><td align=\"right\">$$grand</td><td align=\"right\">$$avg</td><tr>";
}
echo "</table><br /><br />";

// top 10 by largest amount
$sql = "SELECT `donations`.`MCID`, 
	COUNT( `donations`.`MCID` ) AS `TransCnt`, 
	MAX( `donations`.`DonationDate` ) AS `LastDon`, 
	MAX( `donations`.`TotalAmount` ) AS `LargestDon`, 
	AVG( `donations`.`TotalAmount` ) AS `AvgDon`, 
	SUM( `donations`.`TotalAmount` ) AS `GrandTot`, 
	`members`.`NameLabel1stline` AS `Name` 
	FROM `donations`, `members` 
	WHERE `members`.`Inactive` = 'FALSE' 
		AND `donations`.`MCID` = `members`.`MCID` 
		AND `donations`.`DonationDate` BETWEEN '$lodate' AND '$hidate'  
	GROUP BY `donations`.`MCID` 
	ORDER BY `LargestDon` DESC 
	LIMIT 0 , 10;";
// echo "sql: $sql<br>";
$res = doSQLsubmitted($sql);
echo "<h4>Top 10 By Period Largest Single Amount</h4>";
echo "<table class=\"table-condensed\">";
echo "<tr><th>MCID</th><th>Trans.Count</th><th>Last Funding</th><th>Largest Don.</th><th>Period Tot.</th><th>Period Avg.</th></tr>";
while ($r = $res->fetch_assoc()) {
	//echo "<pre>Top 30 by count->"; print_r($r); echo "</pre>";
	$avg = number_format($r[AvgDon],2); $largest = number_format($r[LargestDon],2);	
	$grand = number_format($r[GrandTot],2);
echo "<tr><td>$r[MCID]</td><td align=\"center\">$r[TransCnt]</td><td align=\"center\">$r[LastDon]</td><td align=\"right\">$$largest</td><td align=\"right\">$$grand</td><td align=\"right\">$$avg</td><tr>";
}
echo "</table><br /><br />";

// top 10 by period total
$sql = "SELECT `donations`.`MCID`, 
	COUNT( `donations`.`MCID` ) AS `TransCnt`, 
	MAX( `donations`.`DonationDate` ) AS `LastDon`, 
	MAX( `donations`.`TotalAmount` ) AS `LargestDon`, 
	AVG( `donations`.`TotalAmount` ) AS `AvgDon`, 
	SUM( `donations`.`TotalAmount` ) AS `GrandTot`, 
	`members`.`NameLabel1stline` AS `Name` 
	FROM `donations`, `members` 
	WHERE `members`.`Inactive` = 'FALSE' 
		AND `donations`.`MCID` = `members`.`MCID` 
		AND `donations`.`DonationDate` BETWEEN '$lodate' AND '$hidate'  
		GROUP BY `donations`.`MCID` 
		ORDER BY `GrandTot` DESC 
		LIMIT 0 , 10;";
$res = doSQLsubmitted($sql);
echo "<h4>Top 10 By Period Total</h4>";
echo "<table class=\"table-condensed\">";
echo "<tr><th>MCID</th><th>Trans.Count</th><th>Last Funding</th><th>Largest Don.</th><th>Period Tot.</th><th>Period Avg.</th></tr>";
while ($r = $res->fetch_assoc()) {
	//echo "<pre>Top 30 by count->"; print_r($r); echo "</pre>";
	$avg = number_format($r[AvgDon],2); $largest = number_format($r[LargestDon],2);	
	$grand = number_format($r[GrandTot],2);
echo "<tr><td>$r[MCID]</td><td align=\"center\">$r[TransCnt]</td><td align=\"center\">$r[LastDon]</td><td align=\"right\">$$largest</td><td align=\"right\">$$grand</td><td align=\"right\">$$avg</td><tr>";
}
echo "</table><br /><br />";

// top 10 by period average amount
$sql = "SELECT `donations`.`MCID`, 
	COUNT( `donations`.`MCID` ) AS `TransCnt`, 
	MAX( `donations`.`DonationDate` ) AS `LastDon`, 
	MAX( `donations`.`TotalAmount` ) AS `LargestDon`, 
	AVG( `donations`.`TotalAmount` ) AS `AvgDon`, 
	SUM( `donations`.`TotalAmount` ) AS `GrandTot`, 
	`members`.`NameLabel1stline` AS `Name` 
	FROM `donations`, `members` 
	WHERE `members`.`Inactive` = 'FALSE' 
		AND `donations`.`MCID` = `members`.`MCID` 
		AND `donations`.`DonationDate` BETWEEN '$lodate' AND '$hidate'  
	GROUP BY `donations`.`MCID` 
	ORDER BY `AvgDon` DESC 
	LIMIT 0 , 10;";
$res = doSQLsubmitted($sql);
echo "<h4>Top 10 By Period Average</h4>";
echo "<table class=\"table-condensed\">";
echo "<tr><th>MCID</th><th>Trans.Count</th><th>Last Funding</th><th>Largest Don.</th><th>Period Tot.</th><th>Period Avg.</th></tr>";
while ($r = $res->fetch_assoc()) {
	//echo "<pre>Top 30 by count->"; print_r($r); echo "</pre>";
	$avg = number_format($r[AvgDon],2); $largest = number_format($r[LargestDon],2);	
	$grand = number_format($r[GrandTot],2);
echo "<tr><td>$r[MCID]</td><td align=\"center\">$r[TransCnt]</td><td align=\"center\">$r[LastDon]</td><td align=\"right\">$$largest</td><td align=\"right\">$$grand</td><td align=\"right\">$$avg</td><tr>";
}
echo "</table><br /><br />";



?>
</div>   <!-- container -->
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc.php"></script>
</body>
</html>
