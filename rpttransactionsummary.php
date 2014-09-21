<!DOCTYPE html>
<html>
<head>
<title>Transaction Summary</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/datepicker3.css" rel="stylesheet">
</head>
<body>
<div class="container">
<h3>Transaction Summary <a href="javascript:self.close();" class="btn btn-primary">(CLOSE)</a></h3>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc"></script>

<?php
session_start();
include 'Incls/seccheck.inc';
//include 'Incls/vardump.inc';
include 'Incls/datautils.inc';

$sd = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : date('Y-m-01', strtotime("previous month"));
$ed = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : date('Y-m-t', strtotime("previous month"));

print <<<inputForm

<form action="rpttransactionsummary.php" method="post"  class="form"">
Start Date: <input type="text" name="sd" id="sd" value="$sd" style="width: 100px;"  placeholder="YYYY-MM-DD" /> and End Date:  
<input placeholder="YYYY-MM-DD" type="text" name="ed" id="ed" value="$ed" style="width: 100px";>
<input type="submit" name="submit" value="Submit">
</form>
inputForm;

// report all donation records
$dontotal = 0;
$sql = "SELECT * FROM `donations` 
WHERE `DonationDate` between '$sd' AND '$ed' 
ORDER BY `DonationID` desc";
$dflds = doSQLsubmitted($sql);

$dontotal = $donreccount = 0;
while ($r = $dflds->fetch_assoc()) {
		$purp = $r[Purpose]; $prog = $r[Program]; $camp = $r[Campaign]; $amt = $r[TotalAmount];
		$purptotal[$purp] += $amt;
		$dontotal += $amt;
		$donreccount += 1;
		if ($prog == "") $prog = '--------';
		if ($camp == "") $camp = '--------';
		$totarray[$purp][$prog][$camp] += $amt;
	//echo "<pre>donor records :"; print_r($r); echo "</pre>";
	}
echo '<br />';
$dontotalformatted = number_format($dontotal,2);
echo "Period Total:  $$dontotalformatted<br />";
if (count($totarray) > 0) {
	ksort($totarray);
	echo '<table border="0" class="table-condensed">
	<tr class="success"><th>Purpose</th><th>Program(s)</th><th>Campaign</th><th>Total Amount</th></tr>';
	foreach ($totarray as $k => $v) { // purpose
		$purptotalformatted = number_format($purptotal[$k],2);
		echo "<tr><td>$k</td></tr>";
		ksort($v);
		foreach ($v as $kk => $vv) {  // program
			echo "<tr><td><td>$kk</td></tr>";
				ksort($vv);
				foreach ($vv as $kkk => $vvv) {  // campaign
					$vvvformatted = number_format($vvv,2);
					echo "<tr><td></td><td></td><td>$kkk</td><td align=\"right\">$$vvvformatted</td</td></tr>";
					}
			//$vvvformatted = number_format($vvv,2);
			//echo "<tr><td><td>$kkk:</td><td align=\"right\">$$vvvformatted</td</td></tr>";
			}
		echo "<tr><td align=\"right\">$k Total:</td><td>==========</td><td>==========</td><td align=\"right\">$$purptotalformatted</td></tr>";
		}
	}
echo '</table>----- END OF REPORT -----';
//echo "<pre>donor records :"; print_r($totarray); echo "</pre>";
?>
</div>
</body>
</html>
