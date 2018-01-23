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
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc.php"></script>
<script>
$(document).ready(function () { 
  $("#help").hide();
  $("#catsbtn").click(function() { $("#cats").toggle(); });
  $("#helpbtn").click ( function() {
    $("#help").toggle();
    });
	$("#yr").val("$yr");
	});

</script>

<?php
session_start();

include 'Incls/datautils.inc.php';
include 'Incls/seccheck.inc.php';

$cname = isset($_REQUEST['cname']) ? $_REQUEST['cname'] : "";
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$sd = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : date('Y-m-01', strtotime("today"));
$ed = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : date('Y-m-d', strtotime("tomorrow -1 second"));
$today = date("Y-m-d",strtotime(now));
?>
<div class="container">
<h3>Correspondence Summary Drilldown&nbsp;&nbsp;<a href="javascript:self.close();" class="btn btn-primary hidden-print"><b>CLOSE</b></a>
</h3>
<button class="hidden-print" id="helpbtn">More Info</button>
<form action="rptcorrdrilldown.php" method="post">
<input type="text" name="sd" value="<?=$sd?>" size="8" id="sd"  placeholder="Start Date">
&nbsp;&nbsp;<input type="text" name="ed" value="<?=$ed?>" size="8" id="ed"  placeholder="End Date">
<input type="hidden" name="action" value="continue">
&nbsp;&nbsp;<input class="hidden-print" type="submit" name= "submit" value="Submit">
</form>

<div id="help">
<p>This report provides the ability to list and detail the various correspondence categories that are currently in the correspondence log of the database.  An entry is recorded into the correspondence log when a form of communications is initiated, usually an email or mail.  Some entries are automatic.  Others like bulk mailings, for example, require other need additional steps be done to keep the correspondence log up to date. </p>
<p>The various categories are defined by the administrator as &quot;correspondence categories&quot; in the &quot;Admin&quot; list maintenance functions.  Each category should specify a type of correspondence used to communicate with the member.</p>
<p>Some category values are historical and have been retained from prior systems and may be seen depending on the date range defined.</p>
<p>To use this report choose one of the categories to examine.  A list of all detail records in that category will be listed for the date range specified that can be optionally displayed or hidden.</p>
</div>

<?php
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
	if ($r[CorrespondenceType] == '**NewRec**') continue;
	if ($r[CorrespondenceType] == 'RenewalPaid') continue;
	$ctypearray[$r[CorrespondenceType]] += 1;
	// echo '<pre> corr '; print_r($r); echo '</pre><br />';
	}
$catcount = count($ctypearray);
echo "
<div class=\"hidden-print\">
<button id=\"catsbtn\">Show/Hide $catcount categories in date range</button>
<ul>";
// echo "<pre>CTYPES "; print_r($ctypearray); echo "</pre>";
echo '<div id="cats">';
foreach ($ctypearray as $k => $v) {
	echo "<a href=\"rptcorrdrilldown.php?action=report&sd=$sd&ed=$ed&cname=$k\">$k ($v)</a><br />";
	}
echo '
</ul>
</div>   <!-- cats -->
</div>   <!-- hidden-print -->
';
if ($cname == '') {
	echo '</body></html>';
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

</div>
</body>
</html>
