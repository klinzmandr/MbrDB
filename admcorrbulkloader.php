<!DOCTYPE html>
<html>
<head>
<title>Spreadsheet File Uploader</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/datepicker3.css" rel="stylesheet">
</head>
<body>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>

<?php
session_start();
//include 'Incls/vardump.inc';
include 'Incls/seccheck.inc';
include 'Incls/adminmenu.inc';
include 'Incls/datautils.inc';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$corrtype = isset($_REQUEST['corrtype']) ? $_REQUEST['corrtype'] : "";
$datesent = isset($_REQUEST['DateSent']) ? $_REQUEST['DateSent'] : "";
$notes = isset($_REQUEST['Notes']) ? $_REQUEST['Notes'] : "";
$colidx = isset($_REQUEST['colidx']) ? $_REQUEST['colidx'] : "";

echo '<div class="container">';
if ($action == '') {
// setup of initial input parameters
print <<<pagePart1

<h3>Correspondence Bulk Update Utility</h3>
<p>This page is designed to upload a spreadsheet and use it to add individual records to the correspondence table of the database for each MCID found in the spreadsheet.</p>
<p>Spreadsheets are usually saved in one of the following formats:  xls, xlsx, csv or odp.  This utility will accept a spreadsheet saved in any of these formats.</p>
<p>The prerequisites of the spreadsheet file to be uploaded are:
<ol>
<li>Only the FIRST worksheet tab of a spreadsheet file is used;</li>
<li>One cell in Row 1 must contain the column header MCID (uppercase);</li>
<li>All cells below Row 1 in that column must contain actual MCIDs that are in the
Membership Database;</li>
<li>Only MCIDs in that column are used.  All other column values are ignored.</li>
</ol></p>
<p style="color: red; ">
<b>
NOTE: if the Corr. Type drop-down list does not contain an appropriate selection, a new
correspondence type can be added using Maint. Lists -> Corr Types.
</b></p>

<p><a href="http://youtu.be/FSp0x00l6ak" target="_blank">Click this link for an instructional video (8:14)</a></p>
pagePart1;
echo '
<script>
function chkct() {
	var ct = $("#CT").val();
	if (ct.length == 0) {
		alert("Please select a Corresdpondence Type.");
		return false;
	}
	var fl = $("#file").val();
	if (fl.length == 0) {
		alert("Please select a spreadsheet file.");
		return false;
	}
	var ds = $("#dp1").val();
	if (ds.length == 0) {
		alert("Please select a date.");
		return false;
	}

	return true;
}
</script>
';
echo '
<form action="admcorrbulkloader.php" method="post" enctype="multipart/form-data"  onsubmit="return chkct()">
<br>Select spreadsheet file:&nbsp;
<input size=50 type="file" name="file" id="file" /><br>
Select the date sent, the correspondence type and any notes to be applied to ALL record&apos;s.<br>';
echo "Date Sent: <input type=\"text\" name=\"DateSent\" value=\"$datesent\" data-provide=\"datepicker\" id=\"dp1\" data-date-format=\"yyyy-mm-dd\" data-date-autoclose='true'/><br>";
echo '
Corr. Type: <select id="CT" name="corrtype" size="1">
<option value=""></option>
<option value="RenewalReminder">Renewal Reminder</option>';
loaddbselect('CorrTypes');
echo '
</select><br>
Notes: <input size="50" type="text" name="Notes" value=""><br><br>
<input type="hidden" name="action" value="addnew">
<input type="submit" name="submit" value="Submit" />
</form>
<br>';
exit;
}

// process upload and validate file
if ($action == 'addnew') {
	if ($_FILES["file"]["size"] > 2000000) {
		echo 'File size exceeds maximum allowed of 2 mBytes.<br>';
		echo 'Try again after splitting the file.<br>';
		exit;
	}
if ($_FILES["file"]["error"] > 0)  {		// check for upload error
  echo "ERROR: " . $_FILES["file"]["error"] . "<br />";
  echo "Try again<br>";
  exit;
  }
else {
//    	echo '<pre> file array: '; print_r($_FILES); echo '</pre>';
  move_uploaded_file($_FILES["file"]["tmp_name"], "uploads/" . $_FILES["file"]["name"]);
  }
}

// now read the newly uploaded file and validate that 
//		it has only 1 spreadsheet 
//		a column heading containing MCID

$Filepath = "uploads/" . $_FILES["file"]["name"];
require('spreadsheet-reader/php-excel-reader/excel_reader2.php');
require('spreadsheet-reader/SpreadsheetReader.php');

try {
	$Spreadsheet = new SpreadsheetReader($Filepath);
//	$BaseMem = memory_get_usage();
//	echo "Filepath: $Filepath<br>";
	$errs = "";
	$Sheets = $Spreadsheet -> Sheets();
//	echo '<pre> Sheets: '; print_r($Sheets); echo '</pre>';

	$Index = 0;			// first (only?) sheet tab
	$Spreadsheet -> ChangeSheet($Sheets[$Index]);
//	echo '<pre> row array: '; print_r($Spreadsheet); echo '</pre>';
	$foundMCID = 0;	$colidx = 0;
	$curritem = $Spreadsheet -> current();
//	echo "curritem count: " . count($curritem) . '<br>';
//	echo '<pre> curritem: '; print_r($curritem); echo '</pre>';
	if (count($curritem) == 0) $errs .= 'Unable to read spreadsheet from XLSX file<br>';
	foreach ($curritem as $Name) {
		if ($Name == 'MCID') {
			$foundMCID = 1;
			break;
		}
	$colidx++;		
	}
}
catch (Exception $E) {
	echo "from catch<br>";
	echo $E -> getMessage();
	}
echo "<h3>Upload successful. File stored as: " . "&apos;" . $_FILES["file"]["name"] . '&apos;</h3>';
// report errors or continue with what is entered	
if (!$foundMCID) $errs .= "No column named &apos;MCID&apos; is present.";
$alpha = array(a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z);
if ($colidx > 26) $errs .= 'Column named &apos;MCID&apos; was not found in the firstr 26 columns of the spreadsheet<br>';
$colalpha = strtoupper($alpha[$colidx]);
if (strlen($errs) > 0) { 
	echo "$errs<br>";
	echo "	<h3>Spreadsheet NOT valid.</h3>
	<br><br>
	<a class=\"btn btn-primary btn-danger\" href=\"admcorrbulkloader.php\">CANCEL</a>";
}
else {
	echo "<h3>Uploaded file validated.</h3><br>
	<h4>MCID column heading found in Column &apos;$colalpha&apos; of 1st spreadsheet in file &apos;" . $_FILES["file"]["name"] . "&apos;.<br>
	Corresondence Type to be applied to all MCIDs: $corrtype<br>
	Date to be applied to all corrrespondence records: $datesent<br><br>
	Click CONTINUE to apply updates or CANCEL to change parameters.</h4><br>
	<a class=\"btn btn-primary btn-success\"  href=\"admcorrbulkupd.php?file=$Filepath&corrtype=$corrtype&DateSent=$datesent&Notes=$notes&colidx=$colidx\">CONTINUE</a>
	&nbsp;&nbsp;
	<a class=\"btn btn-primary btn-danger\" href=\"admcorrbulkloader.php\">CANCEL</a>";
}
?>
</div>  <!-- container -->
</body>
</html>
