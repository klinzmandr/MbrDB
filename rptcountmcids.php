<?php
// change to track in smartgit
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>MCID Distributions</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/datepicker3.css" rel="stylesheet">
</head>
<body onload="initSelect()">

<?php
//include 'Incls/vardump.inc.php';
//include 'Incls/seccheck.inc.php';
// include 'Incls/mainmenu.inc.php';
include 'Incls/datautils.inc.php';

// generate the report
$sql = "SELECT `MCID`
FROM `members`
ORDER BY `MCID` ASC;";

// echo "sql: $sql<br />";
$rc = 0;
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
// echo "Total Pages Used: $rc<br />";
$whatarray = array(); $whoarray = array(); $whotimemin = array(); $whotimemax = array();
echo "<h3>Summary of MCIDs in database</h3>";
while ($r = $res->fetch_assoc()) {
	$mpart = substr($r[MCID], 0, 3);
	$mcidpartialarray[$mpart] += 1;
	}
arsort($mcidpartialarray);
// echo '<pre>'; print_r($mcidpartialarray); echo '</pre>';
$i = 0;
echo "Total count of MCIDs in database: $rc<br>";
echo "Unique 3 letter combinations: " . count($mcidpartialarray) . "<br>";
echo "Count of first 3 letter combinations of most used:<ul>";
foreach ($mcidpartialarray as $k => $v) {
	$val = $mcidpartialarray[$i];
	echo "MCID: $k, count: $v<br>";
	$i++;
	if ($i > 19) break;
	}
echo '</ul>';
?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc.php"></script>

</body>
</html>
