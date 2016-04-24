<!DOCTYPE html>
<html>
<head>
<title>Correspondence CSV Export</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<?php
session_start();
include 'Incls/seccheck.inc.php';
include 'Incls/datautils.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if ($action == '') {

print <<<pagePart1
<h3>Correspondence CSV Export</h3>
<p>This function will export all rows of the members database table to a comma seperated variable (CSV) formatted file suitable for opening with a spreadsheet program.  All text fields are seperated by commas, and all text fields are delimited by double quotes.</p>
<a class="btn btn-success" href="admcorrcsvexport.php?action=continue">CONTINUE</a>
<br><br>
<a class="btn btn-danger" href="indexadmin.php">CANCEL</a>

pagePart1;
exit;
}

$sql = "SELECT * FROM `correspondence` WHERE 1";		// select all member rows

$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
echo "<h3>Export Correspondence Table to CSV File</h3>";
echo "Number of rows exported: $rc<br>";
echo "<a href=\"downloads/Correspondence.csv\" download=\"Correspondence.csv\">DOWNLOAD CSV FILE</a>";
echo "<button type=\"button\" class=\"btn btn-xs btn-default\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Fields separated by semicolon(;)\nText fields are quoted.\"><span class=\"glyphicon glyphicon-info-sign\" style=\"color: blue; font-size: 20px\"></span></button>";
$translate = array("\\" => ' ', "\n" => ' ', "\t"=>' ', "\r"=>' ', "\"" =>'');
$csv = array();		// output file array
$flds = array();  // field col name array
$flds = $res->fetch_fields();		// get field names for header
unset($flds[0]);	// drop CORID

foreach ($flds as $k=>$v) {
	$hdr .= $v->name . ';';
	}
$csv[] = rtrim($hdr,";") . "\n";		// column names are 1st line
//echo "header: $csv<br>";

while ($r = $res->fetch_assoc()) {
	// delete all columns not wanted from the return results array
	unset($r[CORID]);
	
	// cleanze and format text fields with double quotes to delimit them
	$r[MCID] = '"' . strtr($r[MCID], $translate) . '"';
	$r[Notes] = '"' . strtr($r[Notes], $translate) . '"';
	$r[] = "\n";
	
	// now implode the array with commas to seperate the fields and 
	// remove new lines, tabs, double quote marks and back slashes
	$csv[] = implode(';',$r);
}
file_put_contents('downloads/Correspondence.csv',$csv);	// save into text file for download

?>
<br><br><a class="btn btn-success" href="indexadmin.php">RETURN</a>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
