<!DOCTYPE html>
<html>
<head>
<title>Funding Summary</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="all">
<link href="css/datepicker3.css" rel="stylesheet">
</head>
<body>
<?php
session_start();

include 'Incls/datautils.inc.php';
include 'Incls/seccheck.inc.php';

$cname = isset($_REQUEST['cname']) ? $_REQUEST['cname'] : "";
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$sd = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : date('Y-m-01', strtotime("today"));
$ed = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : date('Y-m-d', strtotime("tomorrow -1 second"));
$today = date("Y-m-d",strtotime(now));

echo "<div class=\"container\">
<h3>Correspondence Summary Drilldown&nbsp;&nbsp;<a href=\"javascript:self.close();\" class=\"btn btn-primary\"><b>CLOSE</b></a></h3>";

// get date range
print <<<pagePart2
<form action="rptcorrdrilldown.php" method="post">
<input type="text" name="sd" value="$sd" size="8" id="sd"  placeholder="Start Date">
&nbsp;&nbsp;<input type="text" name="ed" value="$ed" size="8" id="ed"  placeholder="End Date">
<input type="hidden" name="action" value="continue">
&nbsp;&nbsp;<input type="submit" name= "submit" value="Submit">
</form>

pagePart2;

// explain report first time through
if ($action == "") {
print <<<pagePart1
<p>This report provides the ability to list and detail the various correspondence categories that are currently in the correspondence log of the database.  The categories are configured in by the administrator as corresondence categories.  Each category should specify a type of corresondence used to communicate with the member.  Some category values are historical and have been retained from prior systems and may be seen depending on the date range defined.</p>
<p>First, choose one of the categories to examine.  A list of all detail records in that category will be listed for the date range specified.</p>
</script><script src="jquery.js"></script><script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc.php"></script>
</body></html>

pagePart1;
exit;
}

// first all correspondence classes are listed with counts, get items from db to count and summarize

$sql = "SELECT * 
	FROM `correspondence` 
	WHERE `correspondence`.`DateSent` BETWEEN '$sd' AND '$ed'
	ORDER BY `CorrespondenceType`;";
//echo "SQL: $sql<br>";
$res = doSQLsubmitted($sql);
$rowcnt = $res->num_rows;

$ctypearray = array(); $ctypecountarray = array();
while ($r = $res->fetch_assoc()) {
	if ($r[MCID] == 'PWC99') continue;
	$ctypearray[$r[CorrespondenceType]] += 1;
	//echo '<pre> RenewalTY '; print_r($r); echo '</pre><br />';
	}
echo "<div class=\"hidden-print\">Categories in Range (Total sent $rowcnt):<ul>";
//echo "<pre>CTYPES "; print_r($ctypearray); echo "</pre>";
foreach ($ctypearray as $k => $v) {
	echo "<a href=\"rptcorrdrilldown.php?action=report&sd=$sd&ed=$ed&cname=$k\">$k ($v)</a><br />";
	}
echo "</ul></div>";
if ($cname == '') {
	echo '</script><script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></body></html><script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc.php"></script>
';
	exit;
	}

// do query and list all correspondence records for a specific corr type within date range
//echo '<pre> ctypearray '; print_r($ctypearray); echo '</pre>';
//echo "cname: $cname<br />";
$sql = "SELECT `correspondence`.*, `members`.`NameLabel1stline` 
	FROM `correspondence`, `members` 
	WHERE `correspondence`.`DateSent` BETWEEN '$sd' and '$ed'
		AND `members`.`MCID` = `correspondence`.`MCID` 
		AND `correspondence`.`CorrespondenceType` LIKE '%$cname%' 
	ORDER BY `correspondence`.`MCID` ASC, `correspondence`.`DateSent` ASC";
//echo "SQL: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
echo "<h4>Correspondence Category &apos;$cname&apos; ($rc)</h4>";
echo "<table class=\"table-condensed\">
<tr><th>MCID</th><th>DateSent</th><th>Name</th><th>Notes</th></tr>";
while ($r = $res->fetch_assoc()) {
	//echo "<pre>corr "; print_r($r); echo "</pre>";
	echo	"<tr><td>$r[MCID]</td><td>$r[DateSent]</td><td>$r[NameLabel1stline]</td><td>$r[Notes]</td></tr>";
	}
echo "</table>";
echo "----- END OF LIST -----<br>";
?>

</div>  <!-- container -->

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc.php"></script>

</div>
</body>
</html>
