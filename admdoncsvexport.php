<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Funding CSV Export</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>

<?php
include 'Incls/seccheck.inc.php';
include 'Incls/datautils.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if ($action == '') {

print <<<pagePart1
<h3>Funding CSV Export</h3>
<p>This function will export all rows of the donations database table to a comma seperated variable (CSV) formatted file suitable for opening with a spreadsheet program.  All text fields are seperated by commas, and all text fields are delimited by double quotes.</p>
<a class="btn btn-success" href="admdoncsvexport.php?action=continue">CONTINUE</a>
<br><br>
<a class="btn btn-danger" href="indexadmin.php">CANCEL</a>

pagePart1;
exit;
}

$sql = "SELECT * FROM `donations` WHERE 1";		// select all member rows

$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
echo "<h3>Export Funding Table to CSV File</h3>";
echo "Number of rows exported: $rc<br>";
echo "<a href=\"downloads/Funding.csv\" download=\"Funding.csv\">DOWNLOAD CSV FILE</a>";
echo "<button type=\"button\" class=\"btn btn-xs btn-default\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Fields separated by semicolon(;)\nText fields are quoted.\"><span class=\"glyphicon glyphicon-info-sign\" style=\"color: blue; font-size: 20px\"></span></button>";
$translate = array("\\" => ' ', "\n" => ' ', "\t"=>' ', "\r"=>' ', "\"" =>'');
$csv = array();		// output file array
$flds = array();  // field col name array
$flds = $res->fetch_fields();		// get field names for header
unset($flds[0]);	// drop DonationID
unset($flds[1]);	// drop TimeStamp

foreach ($flds as $k=>$v) {
	$hdr .= $v->name . ';';
	}
$fp = fopen('downloads/Donations.csv', "w");
$csv = rtrim($hdr,";") . "\n";		// column names are 1st line
fwrite($fp, $csv);
//echo "header: $csv<br>";

while ($r = $res->fetch_assoc()) {
	// delete all columns not wanted from the return results array
	unset($r['DonationID']);
	unset($r['TimeStamp']);
	
	// cleanze and format text fields with double quotes to delimit them
	$r['MCID'] = '"' . strtr($r['MCID'], $translate) . '"';
	$r['Note'] = '"' . strtr($r['Note'], $translate) . '"';
	$r[] = "\n";
	// now implode the array with commas to seperate the fields and 
	// remove new lines, tabs, double quote marks and back slashes
	$csv = implode(';',$r);
	fwrite($fp, $csv);
}
fclose($fp);

?>
<br><br><a class="btn btn-success" href="indexadmin.php">RETURN</a>
</body>
</html>
