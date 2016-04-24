<?php
session_start();
include 'Incls/seccheck.inc.php';
//include 'Incls/vardump.inc.php';

// now ready to do db search
include 'Incls/datautils.inc.php';

$sql = "SELECT * FROM `labelsandletters` WHERE `Letter` IS NOT NULL ORDER BY `ZipCode` ASC;";
//echo "SQL: $sql<br>";
$res = doSQLsubmitted($sql);
$nbr_rows = $res->num_rows;
//echo "Nbr rows: $nbr_rows<br>";
if ($nbr_rows == 0) {
	print <<<nothingReturned
<!DOCTYPE html>
<html><head><title>Print Letters-Nothing</title><meta name="viewport" content="width=device-width, initial-scale=1.0"><link href="css/bootstrap.min.css" rel="stylesheet" media="screen"></head><body>
<div class="container">
<h4>There are no entries in the letters and labels queue.</h4>
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
  <title>MbrDB Letter Output</title>
</head>
<body>
labelPart1;
// include css to print the letter
include 'Incls/letter_print_css.inc.php';
while ($r = $res->fetch_assoc()) {
	$ltr = stripslashes($r[Letter]);
	
	if (isset($_REQUEST['header'])) { 
		$body = "<div class=\"hdr\"><img src=\"Incls/letterlogo.jpg\" width=\"100%\" height=\"130\" alt=\"PWC Logo\" /></div>";
		}
	else {
		$body = "<div class=\"hdr\"></div>";	
		}
	//$body .= "<div class=\"ltr\">";
	//$body .= "$org<br>$name<br>$addr<br>$city, $state  $zipcode";
	//$body .= "<br /><br /><br />Dear $corrsal,";
	$body .= "<br />$ltr</div>";
	$body .= "<div class=\"page-break\"></div>";
	if (strlen($ltr) > 15) {  // skip printing letter if printing just labels
		echo $body;
		}
	}
print <<<labelPart2
</body>
</html>
<div class="label"><a href="javascript:self.close();" class="btn btn-primary">CLOSE</a></div>
labelPart2;
exit;

?>