<?php
session_start();
include 'Incls/seccheck.inc';

// now ready to do db search
include 'Incls/datautils.inc';
$sql = "SELECT * FROM `labelsandletters` WHERE '1' ORDER BY `ZipCode` ASC;";
//echo "SQL: $sql<br>";
$res = doSQLsubmitted($sql);
$nbr_rows = $res->num_rows;
//echo "Nbr rows: $nbr_rows<br>";
if ($nbr_rows == 0) {
	print <<<nothingReturned
<!DOCTYPE html>
<html><head><title>Print Labels-Nothing</title><meta name="viewport" content="width=device-width, initial-scale=1.0"><link href="css/bootstrap.min.css" rel="stylesheet" media="screen"></head><body>
<div class="container">
<h4>There are no entries in the letters and labels queue.</h4>
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<a href="javascript:self.close();" class="btn btn-primary">CLOSE</a>
</div>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body></html>
nothingReturned;
	exit;
	}

// create html/css document for labels
print <<<labelPart1
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
  <title>MbrDB Label Output</title>
  </head>
<body>
labelPart1;
// include in CSS to format label printing
include 'Incls/label_print_css.inc';
// leave empty labels empty

echo "<div class=\"label\"><a href=\"javascript:self.close();\" class=\"btn btn-primary\">CLOSE</a></div>";

$blanks = $_REQUEST['labelstoskip'];
if ($blanks > 0) $blanks -= 1;
$sheetcount = 0;
$sheetcount += 1;
for ($i=0; $i<$blanks; $i++) {
	echo "<div class=\"label\"></div>";
	$sheetcount += 1;
	}
while ($r = $res->fetch_assoc()) {
	$mcid = $r[MCID]; $date = $r[Date]; $org = substr($r[Organization],0,24);	
	$name = substr($r[NameLabel1stline],0,24); 
	$addr = $r[AddressLine]; $city = $r[City]; $state = $r[State]; $zipcode = $r[ZipCode];
	if ($org == '') 
		echo "<div class=\"label\">
$name<br>
$addr<br>
$city, $state  $zipcode
<div style=\"text-align: right; \"><mcid>$mcid</mcid></div>
</div>";
	else {
		$name = 'Attn: ' . substr($r[NameLabel1stline],0,19);		
		echo "<div class=\"label\">
$org<br>
$name<br>
$addr<br>$city, $state  $zipcode
<div style=\"text-align: right; \"><mcid>$mcid</mcid></div>
</div>";
		}
	//echo "<pre>"; print_r($r); echo "</pre>";
	$sheetcount += 1;
	if ($sheetcount >= 30) {
		echo "<div class=\"page-break\"></div>";
		$sheetcount = 0;
		}
	}
print <<<labelPart2
<!-- <div class="label"><a href="javascript:self.close();" class="btn btn-primary">CLOSE</a></div> -->
</body>
</html>
labelPart2;
exit;
?>
