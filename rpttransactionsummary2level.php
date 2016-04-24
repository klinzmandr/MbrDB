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

<?php
/* NOTE TO SELF
This version only goes two deep: Purpose by Program
It does not use the 'Campaign' field
Keep this in case someone wants this it.
*/
session_start();
include 'Incls/seccheck.inc.php';
//include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';

$drangelo = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : date('Y-m-01', strtotime('-1 months'));
$drangehi = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : date('Y-m-01', strtotime(now));

print <<<inputForm
<script>
function chkDates(form) {
	alert("check values entered");
	var errmsg = "";
	var chkcnt = 0;
  return true;
</script>

<form action="rpttransactionsummary2level.php" method="post"  class="form" onsubmit="return chkDates(this)">
Start Date: 
<input type="text" name="sd" id="sd" value="$drangelo" style="width: 100px;"  /> 
and End Date:  
<input type="text" name="ed" id="ed" value="$drangehi" style="width: 100px";>
<input type="submit" name="submit" value="Submit">
</form>
inputForm;
if ($drangelo == '') exit();

// report all donation records
$dontotal = 0;
$sql = "SELECT * FROM `donations` WHERE `DonationDate` >= '$drangelo' AND `DonationDate` < '$drangehi' ORDER BY `DonationID` desc";
$dflds = doSQLsubmitted($sql);

$dontotal = $donreccount = 0;
while ($r = $dflds->fetch_assoc()) {
		$purp = $r[Purpose]; $prog = $r[Program]; $amt = $r[TotalAmount];
		$purptotal[$purp] += $amt;
		$dontotal += $amt;
		$donreccount += 1;
		if ($prog == "") $prog = 'Unclassified';
		$totarray[$purp][$prog] += $amt;
	//echo "<pre>donor records :"; print_r($r); echo "</pre>";
	}
echo '<br />';
$dontotalformatted = number_format($dontotal,2);
echo "Period Total:  $$dontotalformatted<br />";
if (count($totarray) > 0) {
	ksort($totarray);
	echo '<table border="0" class="table table-condensed">
<tr class="success"><th>Purpose</th><th>Program(s)</th><th>Total Amount</th></tr>';
	foreach ($totarray as $k => $v) {
		$purptotalformatted = number_format($purptotal[$k],2);
		echo "<tr><td>$k</td></tr>";
		ksort($v);
		foreach ($v as $kk => $vv) {
			//if ($kk == '') $kk = 'Unclassified';
			$vvformatted = number_format($vv,2);
			echo "<tr><td><td>$kk:</td><td align=\"right\">$$vvformatted</td</td></tr>";
			}
		echo "<tr><td align=\"right\">$k Total:</td><td></td><td align=\"right\">$$purptotalformatted</td></tr>";
		}
	echo '</table>';
	}
//echo "<pre>donor records :"; print_r($totarray); echo "</pre>";
?>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc.php"></script>
</div>
</body>
</html>
