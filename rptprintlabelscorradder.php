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
// include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
include 'Incls/datautils.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$corrtype = isset($_REQUEST['corrtype']) ? $_REQUEST['corrtype'] : "";
$datesent = isset($_REQUEST['DateSent']) ? $_REQUEST['DateSent'] : "";
$notes =    isset($_REQUEST['Notes']) ? $_REQUEST['Notes'] : "";
$colidx = isset($_REQUEST['colidx']) ? $_REQUEST['colidx'] : "";
$mcidcount = isset($_REQUEST['count']) ? $_REQUEST['count'] : "0";

echo '<div class="container">';
if ($action == '') {
// setup of initial input parameters
print <<<pagePart1

<h3>Print Label Correspondence Bulk Adder&nbsp;&nbsp;&nbsp;<a href="javascript:self.close();" class="btn btn-primary btn-xs">CANCEL</a></h3>
<p>This action will add an individual correspondence record for each MCID for which a label has been created.  Each correspondence record will be created with the correspondence type and date selected.</p>
<h4>Corresponence records to create: $mcidcount</h4>
pagePart1;
echo '
<script>
function chkct() {
	var ct = $("#CT").val();
	if (ct.length == 0) {
		alert("Please select a Corresdpondence Type.");
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
<form action="rptprintlabelscorradder.php" method="post" enctype="multipart/form-data"  onsubmit="return chkct()">
<br>Select the date sent and the correspondence type to be applied to ALL MCID&apos;s.<br>';
echo "Date Sent: <input type=\"text\" name=\"DateSent\" value=\"$datesent\" data-provide=\"datepicker\" id=\"dp1\" data-date-format=\"yyyy-mm-dd\" data-date-autoclose='true'/><br>";
echo '
Corr. Type: <select id="CT" name="corrtype" size="1">
<option value=""></option>
<option value="RenewalReminder">Renewal Reminder</option>';
loaddbselect('CorrTypes');
echo '
</select><br>
Notes: <input type="text" name="Notes" value="" size="50"><br><br>
<input type="hidden" name="file" value="uploads/corraddarray.csv">
<input type="hidden" name="count" value="'. $mcidcount. '">
<input type="hidden" name="action" value="addnew">
<input type="submit" name="submit" value="Submit" />
</form>
<br>';
exit;
}

// now read the file and validate that 
//		it has only 1 spreadsheet 
//		a column heading containing MCID

$Filepath = "uploads/corraddarray.csv";
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
//echo "<h3>Upload successful. File stored as: " . "&apos;" . $_FILES["file"]["name"] . '&apos;</h3>';
//echo 'count after col search: ' . $Spreadsheet->count() . '<br>';
// report errors or continue with what is entered	
if (!$foundMCID) $errs .= "No column named &apos;MCID&apos; is present.";
$alpha = array(a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z);
if ($colidx > 26) $errs .= 'Column named &asos;MCID&apos; was not found in the firstr 26 columns of the spreadsheet<br>';
$colalpha = strtoupper($alpha[$colidx]);
if (strlen($errs) > 0) { 
	echo "$errs<br>";
	echo "	<h3>Spreadsheet NOT valid.</h3>
	<br><br>
	<a class=\"btn btn-primary btn-da<h2></h2>nger\" href=\"admcorrbulkloader.php\">CANCEL</a>";
}
else {
	echo "<h3>Validation completed.</h3>
	<h4>Spreadsheet contains $mcidcount member id&apos;s.<br>
	MCID column heading found.<br>
	Corresondence Type to be applied to all MCID's: $corrtype<br>
	Date to be applied to all corrrespondence records: $datesent<br><br>
	Click CONTINUE to apply correspondence records to all MCID&apos;s or CLOSE ro cancel further actions.</h4><br>
	<a class=\"btn btn-primary btn-success\"  href=\"rptprintlabelscorradderupd.php?file=$Filepath&corrtype=$corrtype&DateSent=$datesent&Notes=$notes&colidx=$colidx\">CONTINUE</a>
	&nbsp;&nbsp;
	<a href=\"javascript:self.close();\" class=\"btn btn-primary\">CLOSE</a>";
}
?>
</div>  <!-- container -->
</body>
</html>
