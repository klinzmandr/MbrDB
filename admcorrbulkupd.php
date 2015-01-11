<!DOCTYPE html>
<html>
<head>
<title>Correspondence Bulk Update</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>

<?php
session_start();
include 'Incls/vardump.inc';
include 'Incls/seccheck.inc';
include 'Incls/datautils.inc';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$Filepath = isset($_REQUEST['file']) ? $_REQUEST['file'] : ""; 
$corrtype = isset($_REQUEST['corrtype']) ? $_REQUEST['corrtype'] : "";
$datesent = isset($_REQUEST['DateSent']) ? $_REQUEST['DateSent'] : "";
$notes = isset($_REQUEST['Notes']) ? $_REQUEST['Notes'] : "";
$colidx = isset($_REQUEST['colidx']) ? $_REQUEST['colidx'] : "";

// Excel reader from http://code.google.com/p/php-excel-reader/
require('spreadsheet-reader/php-excel-reader/excel_reader2.php');
require('spreadsheet-reader/SpreadsheetReader.php');

date_default_timezone_set('UTC');
$mcidarray = array();
try	{
//	echo "Filepath: $Filepath\n";
	$Spreadsheet = new SpreadsheetReader($Filepath);
//	$BaseMem = memory_get_usage();
	$Sheets = $Spreadsheet -> Sheets();
	$Index = 0;			// first (only?) sheet tab
	$Spreadsheet -> ChangeSheet($Sheets[$Index]);
	foreach ($Spreadsheet as $Key => $Row) {
//		echo $Key.': ';
		if ($Row)	{
//			echo '<pre row: >'; print_r($Row[$colidx]); echo '</pre>';
			if ($Row[$colidx] != 'MCID') {
				if ($Row[$colidx] == '') break;
//				echo '<pre row: >'; print_r($Row[$colidx]); echo '</pre>';
				$mcidarray[$Row[$colidx]] += 1;				
			}
		}
	}
}
catch (Exception $E)	{
	echo $E -> getMessage();
}

// the MCID's are all isolated in an array, now we update the correspondence table
//echo '<pre mcidarray: >'; print_r($mcidarray); echo '</pre>';
$updarray = array(); $rc = 0;
foreach ($mcidarray as $mcid => $cnt) {
//	if ($updarray[MCID] == '') 
	$updarray[MCID] = strtoupper($mcid);
	$updarray[CorrespondenceType] = $corrtype;				// corresondence type for new add
	$updarray[DateSent] = $datesent;
	$updarray[Notes] = stripslashes($notes) . ' - added as bulk update';
//	echo '***TEST MODE ON***<pre updarray: >'; print_r($updarray); echo '</pre>';
	sqlinsert('correspondence', $updarray);
	$rc++;
}

echo "
<div class=\"container\">
<h3>$rc Correspondence records successfull added.</h3>
<br><br>
<a class=\"btn btn-primary btn-success\" href=\"admcorrbulkloader.php\">RETURN</a>
</div>  <!-- container -->
";
exit;

?>
