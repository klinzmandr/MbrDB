<!DOCTYPE html>
<html>
<head>
<title>EDI Information</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body onChange="flagChange()">
<?php
session_start();
//include 'Incls/vardump.inc';
include 'Incls/seccheck.inc';
include 'Incls/mainmenu.inc';
include 'Incls/datautils.inc';

$mcid = isset($_SESSION['ActiveMCID']) ? $_SESSION['ActiveMCID'] : "";
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$precno = isset($_REQUEST['precno']) ? $_REQUEST['precno'] : "";
$pathinfo = isset($_REQUEST['pathinfo']) ? $_REQUEST['pathinfo'] : "";

if (($mcid == "") AND ($action == "delete")) {
	print <<<delError
<div class="container">
<h3>ERROR: Deletion of EDI may not be done as there is no active MCID selected.</h3>
<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></body></html>
delError;
	exit;
	}
	
if ($action == 'photodelete') {
//	echo "deleting photo row number: $precno<br>"; 
	$fsql = "DELETE FROM `photos` WHERE `Phid` = '".$precno."';";
	doSQLsubmitted($fsql);
//	echo "pathinfo for file: $pathinfo<br>";
	unlink($pathinfo);
	}
	
if (($mcid == "") AND ($action == "addnew")) {
	print <<<noMCID
<div class="container">
<h3>ERROR: Addition of EDI may not be done as there is no active MCID selected.</h3>
<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></body></html>
noMCID;
	exit;
	}
// info page if no mcid is 'active'
if ($mcid == "") {
print <<<pagePart1
<div class="container">
<h3>Extended Donor Information</h3>
<p>This page will display all Extended Donor Information (EDI) previously entered for the active MCID.  Use the MCID Lookup function to select a member''s MCID to make it 'active'.</p>
<p>Information recorded here is additional to that usually provided for a donor.  Please enter all donor funding and contributions on the 'Funding' tab and all contacts made on the 'Correspondence' tab.</p>
<p>EDI data is information entered to allow fund develpment activities to be documented when special donors have been identified and further information regarding the donor recorded for organizational historical purposes.  All data is addition to that recorded for a 'regular' donor.</p>
</div>  <!-- container -->
<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></div></body></html>
pagePart1;
exit;
}

// delete EDI record for active MCID - confirmation already done.
// continue to normal info page for active MCID on completion
if (($mcid != "") AND ($action == "delete")) {
	$sql = "SELECT * FROM `extradonorinfo` WHERE `MCID` = '$mcid'";
	$res = doSQLsubmitted($sql);
	$nbr_rows = $res->num_rows;
	if ($nbr_rows == 0) {
		print <<<nothingToDelete
<div class="container">
<h4>There is no Extended Donor Info for MCID $mcid</h4>
<!-- <a class="btn btn-primary" href="mbrinformation.php">CONTINUE</a> -->
</div>  <!-- container -->
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body></html>
nothingToDelete;
		exit;
		}
	else {
		$sql = "DELETE FROM `extradonorinfo` WHERE `MCID` = '$mcid'";
		$res = doSQLsubmitted($sql);
		$sql = "DELETE FROM `photos` WHERE `MCID` = '$mcid'";
		$res = doSQLsubmitted($sql);
		$pics = scandir('../mbrdbphotos');
		$l = strlen($mcid);
//		echo '<pre> pics'; print_r($pics); echo '</pre>';
		foreach ($pics as $p) {
			if (substr($p,0,$l) == $mcid) unlink('../mbrdbphotos/' . $p);
			} 
		print<<<delPage
<div class="container">
<h4>Deletion of EDI record and photos for: $mcid complete.</h4>
<!-- <a class="btn btn-primary" href="mbrinformation.php">CONTINUE</a> -->
</div>  <!-- container -->
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body></html>
delPage;
		exit;
		}
	}

// add new record for active mcid if requested
// add of dup record will get kicked by DB if record already exists for mcid
// if dup exists, merely pass through and let the existing record be read.
if (($mcid != "") AND ($action == "addnew")) {
	include 'Incls/edi_template.inc';
	$sql = "Select `NameLabel1stline` FROM `members` WHERE `Inactive` = 'FALSE' AND `MCID` = '$mcid';";
	$res = doSQLsubmitted($sql);
	$rows = $res->num_rows;
	if ($rows == 0) {
		echo '<h3>MCID is an inactive member</h3>
		<p>Update the member record to make the member active before proceeding.</p>
		<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></body></html>';
		exit;
		}
	$r = $res->fetch_assoc();
	$namelabel1stline = $r[NameLabel1stline];
	$today = date('Y-m-d', strtotime(now));
	$lastupdater = $_SESSION['SessionUser'];
	//$sql = "INSERT INTO `pwcmbrdb`.`extradonorinfo` ( `RecID`, `MCID`, `NameLabel1stline`, `personal`, `education`, `business`, `other`, `wealth`, `research`, `DateEntered`, `LastUpdated`, `LastUpdater` ) VALUES (NULL, '$mcid', '$namelabel1stline', '$personal', '$education', '$business', '$other', '$wealth', '$research', '$today', '$today', '$lastupdater')";
	//$res = doSQLsubmitted($sql);
	$flds[MCID] = $mcid;
	$flds[NameLabel1stline] = $namelabel1stline;
	$flds[personal] = $personal;
	$flds[education] = $education;
	$flds[business] = $business;
	$flds[other] = $other;
	$flds[wealth] = $wealth;
	$flds[research] = $research;
	$flds[DateEntered] = $today;
	$flds[LastUpdated] = $today;
	$flds[LastUpdater] = $lastupdater;
	$res = sqlinsert('extradonorinfo',$flds);
	//echo "<h4>New EDI Record Added For: $mcid</h4><br />";
	// allow the fall through to read/display record just added
	}

// read EDI info for active MCID
if ($mcid != "") {
	$sql = "SELECT * FROM `extradonorinfo` WHERE `MCID` = '$mcid';";
	$res = doSQLsubmitted($sql);
	$nbr_rows = $res->num_rows;
	if ($nbr_rows == 0) {
		print <<<nadaEDI
<div class="container"><h4>No EDI available for MCID: $mcid</h4></div>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
nadaEDI;
		exit;
		}
	$row = $res->fetch_assoc();
	$personal = $row[personal]; $education = $row[education]; $business = $row[business];
	$other = $row[other]; $wealth = $row[wealth]; $research = $row[research];
	}
print <<<updPage1
<div class="container">
<h3>Extended Donor Information for $mcid</h3>
<ul id="myTab" class="nav nav-tabs">
  <li class=""><a href="#usage" data-toggle="tab">Usage</a></li>
  <li class=""><a href="#personal" data-toggle="tab">Personal</a></li>
  <li class=""><a href="#education" data-toggle="tab">Education</a></li>
  <li class=""><a href="#business" data-toggle="tab">Business</a></li>
  <li class=""><a href="#other" data-toggle="tab">Other Affiliations</a></li>
  <li class=""><a href="#wealth" data-toggle="tab">Wealth Sources</a></li>  
  <li class=""><a href="#photos" data-toggle="tab">Pics &amp; Docs</a></li>
  <li class=""><a href="#research" data-toggle="tab">Research By</a></li>
 </ul>

<div id="myTabContent" class="tab-content">
<div class="tab-pane fade active in" id="usage">
<p>Information on each of these tabs represent extended research on selected donors and supporters.  <b>It is intended that this information be confidential and private for the use of Pacific Wildlife Care only.</b>  Any unauthorized use is prohibited.</p>
<p>Each tab is free form in nature.  In initial entry of information, specific examples are provided that usually are noted during research.  Information placed in each tab may be searched for so the use of 'keywords' is encouraged to facilitate finding specific information across all donors for which research is done.</p>
<p>Click the appropriate tab to begin.  Updates or edits to information on each tab may be done by clicking the 'UPDATE' button for that tab.</p>
<p>Please enter all donor funding activity on the 'Funding' tab and all contacts made on the 'Correspondence' tab.</p>
</div>

<div class="tab-pane fade" id="personal">
Info about Spouse, children, parents, siblings, other sig. Relationships
<a class="btn btn-primary btn-mini" href="edidbupdate.php?field=personal">Update</a>
<div class="well">
<pre>
$personal
</pre>
</div>  <!-- well -->
</div>  <!-- tab-pane -->
<div class="tab-pane fade" id="education">
Info about College, Advanced Degrees, Honorary Degrees
<a class="btn btn-primary btn-mini" href="edidbupdate.php?field=education">Update</a>
<div class="well">
<pre>
$education
</pre>
</div>  <!-- well -->
</div>
<div class="tab-pane fade" id="business">
Info about Address, position, description, private or public, phone, email
<a class="btn btn-primary btn-mini" href="edidbupdate.php?field=business">Update</a>
<div class="well">
<pre>
$business
</pre>
</div>  <!-- well -->
</div>
<div class="tab-pane fade" id="other">
Info about Board memberships, nonprofits, political, religious, social groups
<a class="btn btn-primary btn-mini" href="edidbupdate.php?field=other">Update</a>
<div class="well">
<pre>
$other
</pre>
</div>  <!-- well -->
</div>  <!-- tab-pane -->
<div class="tab-pane fade" id="wealth">
Info about Salary/annual income, Stock holdings, Real property, Personal Property, Foundations, Company ownership
<a class="btn btn-primary btn-mini" href="edidbupdate.php?field=wealth">Update</a>
<div class="well">
<pre>
$wealth
</pre>
</div>  <!-- well -->
</div>  <!-- tab-pane -->
<script>
function confirmContinue() {
	var r=confirm("This action cannot be reversed.\\n\\nConfirm this action by clicking OK or CANCEL"); 
	if (r==true) { return true; }
	return false;
	}
function advisory() {
	var r=confirm("This will open a new window or tab.\\n\\nConfirm this action by clicking OK or CANCEL"); 
	if (r==true) { return true; }
	return false;
	}
</script>

<div class="tab-pane fade" id="photos">
<p>Listing of all related pictures and documents uploaded.</p>
<a class="btn btn-primary btn-mini" href="edidbphotoupd.php?field=photos&action=NEW">Add new Pic or Doc</a><br><br>
<table class="table">

updPage1;

$sql = "SELECT * from `photos` WHERE `MCID` = '$mcid'";
$res = doSQLsubmitted($sql);
$rows = $res->num_rows;
if ($rows == 0) {
		echo '<h3>No photos or documents available.</h3>';
		}
else {
	echo '<tr><th>View</th><th>Del</th><th>Title</th><th>Notes</th></tr>';
		while ($r = $res->fetch_assoc()) {
			echo "<tr><td width=\"5%\">
<a href=\"$r[PathInfo]\" target=\"_blank\" <span onclick=\"return advisory()\" title=\"View Photo\" class=\"glyphicon glyphicon-camera\" style=\"color: blue; font-size: 20px\"></span></a>
</td>
<td width=\"5%\">
<a href=\"ediaddupdate.php?action=photodelete&precno=$r[Phid]&pathinfo=$r[PathInfo]\" <span onclick=\"return confirmContinue()\" title=\"Delete Photo\" class=\"glyphicon glyphicon-trash\" style=\"color: blue; font-size: 20px\"></span></a>
</td>
<td>$r[Title]</td>
<td>$r[Notes]</td>
</tr>";
 	}
 }
echo '</table>';
echo '</div>  <!-- tab-pane -->';

print <<<updPage2
<div class="tab-pane fade" id="research">
Prepared for, Requested by, Date, Researchers comments
<a class="btn btn-primary btn-mini" href="edidbupdate.php?field=research">Update</a>
<div class="well">
<pre>
$research
</pre>
</div>  <!-- well -->
</div>  <!-- tab-pane -->
</div>  <!-- tab-content -->
</div>  <!-- container -->
updPage2;
?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
