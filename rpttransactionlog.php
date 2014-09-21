<!DOCTYPE html>
<html>
<head>
<title>Transaction Report</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/datepicker3.css" rel="stylesheet">
</head>
<body>
<div class="container">
<h3>Transaction Log <a href="javascript:self.close();" class="btn btn-xs btn-primary">(CLOSE)</a></h3>

<?php
session_start();
include 'Incls/seccheck.inc';
//include 'Incls/vardump.inc';
include 'Incls/datautils.inc';

$drangelo = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : date('Y-m-01', strtotime("previous month"));
$drangehi = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : date('Y-m-t', strtotime("previous month"));

print <<<inputForm
<script>
function chkDates(form) {
	alert("check values entered");
	var errmsg = "";
	var chkcnt = 0;
  return true;
</script>

<form action="rpttransactionlog.php" method="post"  class="form" onsubmit="return chkDates(this)">
Start Date: 
<input type="text" name="sd" id="sd" value="$drangelo"> and End Date:  
<input type="text" name="ed" id="ed" value="$drangehi">
<input type="submit" name="submit" value="Submit">
</form>
inputForm;
if ($drangelo == '') exit();

// report all donation records
$dontotal = 0;
$sql = "SELECT * 
	FROM `donations` 
	WHERE `DonationDate` BETWEEN '$drangelo' AND '$drangehi' 
	ORDER BY `DonationID` DESC";
$dflds = doSQLsubmitted($sql);

$dontotal = $dreccount = 0;
while ($r = $dflds->fetch_assoc()) {
	$key = sprintf("%sF%06.0d",$r[DonationDate],$r[DonationID]);
	//$dresultarray[$key] = $r;
	$totaleventarray[$key] = $r;
	$dontotal += $r['TotalAmount'];
	$dreccount++;
	//echo "<pre>donor records :"; echo "key: $key<br />"; print_r($r); echo "</pre>";
	}
//echo "<pre>donor records :"; print_r($totaleventarray); echo "</pre>";
$dontotalformatted = number_format($dontotal,2);
echo "<div class=\"row\">";
echo "<div class=\"col-sm-5\">Funding records: $dreccount</div>";

echo "<div class=\"col-sm-5\">Total Funding: $$dontotalformatted</div>";
echo "</div>";
echo "<a href=\"downloads/TransactionLog.csv\" download=\"TransactionLog.csv\">DOWNLOAD CSV FILE</a>";
echo "<button type=\"button\" class=\"btn btn-xs btn-default\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Fields separated by semicolon(;)\nText fields are quoted.\"><span class=\"glyphicon glyphicon-info-sign\" style=\"color: blue; font-size: 20px\"></span></button>";
$fa = array();			// download file array
if (count($totaleventarray) > 0) {
	krsort($totaleventarray);
  echo "<table class=\"table-condensed\">";	
  $fa[] = "CheckDate;DateEntered;ChkNbr;MCID;Purpose;Program;Campaign;Amount;Notes\n";
  echo "<tr><th>CheckDate</th><th>DateEntered</th><th>ChkNbr</th><th>MCID</th><th>Purpose</th><th>Program</th><th>Campaign</th><th>Amount</th><th>Notes</th></tr>";
	$translate = array("\\" => ' ', "\n" => ' ', "\t"=>' ', "\r"=>' ', "\"" =>'');
	//echo "<pre>translate "; print_r($translate); echo "</pre>";
  foreach ($totaleventarray as $k=>$r) {
  	if ($r[Purpose] == '**NewRec**') continue;
		$date = substr($k,0,10); 	$type = substr($k,10,1);	$seq = substr($k,11,6);
		$dateent = date('Y-m-d',strtotime($r[TimeStamp]));
		$note = strtr($r[Note], $translate);
		$fa[] = "$date;$dateent;$r[CheckNumber];\"$r[MCID]\";$r[Purpose];$r[Program];$r[Campaign];$r[TotalAmount];\"$note\"\n";
		echo "<tr><td>$date</td><td>$dateent</td><td>$r[CheckNumber]</td><td>$r[MCID]</td><td>$r[Purpose]</td><td>$r[Program]</td><td>$r[Campaign]</td><td align=\"right\">$$r[TotalAmount]</td><td>$note</td></tr>";
	 	}
	 	echo "</table><br />---END OF REPORT--<br />";
	 	file_put_contents('downloads/TransactionLog.csv',$fa);
	}
?>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc"></script>

</div>
</body>
</html>
