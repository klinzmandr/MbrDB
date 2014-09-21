<!DOCTYPE html>
<html>
<head>
<title>Membership Summary</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<?php
session_start();

//include 'Incls/vardump.inc';
include 'Incls/datautils.inc';
include 'Incls/seccheck.inc';

$name = isset($_REQUEST['name']) ? $_REQUEST['name'] : "";
$listitem = isset($_REQUEST['listitem']) ? $_REQUEST['listitem'] : "";
$earliest = isset($_REQUEST['earliest']) ? $_REQUEST['earliest'] : "2000-01-01";
$today = date("Y-m-d",strtotime(now));
$oldest = isset($_REQUEST['oldest']) ? $_REQUEST['oldest'] : $today;

if ($name == "") {
print <<<pagePart1
<div class="container">
<h3>Member Summary Drilldown&nbsp;&nbsp;<a href="javascript:self.close();" class="btn btn-primary"><b>CLOSE</b></a></h3>
<p>This report provides the ability to list the various membership status availble.  A summary of all members in that status will be listed when selected.  Then you may select one of those from the dropdown menu to inspect the detail members associated with it.</p>

<form action="rptmembersummary.php">
<select name="name" onchange="this.form.submit()">
<option value="">Select a Membership Status:</option>
<option value="0">Contacts(0)</option>
<option value="1">Members(1)</option>
<option value="2">Volunteers(2)</option>
<option value="3">Donors(3)</option>
</select>
</form>
pagePart1;
exit;

}

// a member status is named, get items from db to count and summarize
echo "<div class=\"container\">";
$fullname = "";
if ($name == 0) $fullname = "Contact(0)";
if ($name == 1) $fullname = "Member(1)";
if ($name == 2) $fullname = "Volunteer(2)";
if ($name == 3) $fullname = "Donor(3)";
echo "<h3>Listing for Category: $fullname &nbsp;&nbsp;<a href=\"javascript:self.close();\" class=\"btn btn-primary\"><b>CLOSE</b></a></h3>";
echo "<a href=\"rptmembersummary.php?name=\">START OVER</a>&nbsp;&nbsp;";
$sql = "SELECT * 
	FROM `members` 
	WHERE `Inactive` = 'FALSE' 
		AND `MemStatus` = '$name' 
	ORDER BY `MCType`";
$res = doSQLsubmitted($sql);
$startdate = array(); $enddate = array();
while ($r = $res->fetch_assoc()) {
	//echo "<pre>$name: "; print_r($r); echo "</pre>";
	$fn = $r[MCtype];											// $name = Purpose, Program or Category now
	$nc[$fn] += 1;												// add up number of records per selection
	$tot += 1;														// create overall total count
	}
//echo "<pre>Count Summary "; print_r($nc); echo "</pre>";

print <<<formPart1
<form class="form-inline" name="mctypeform" action="rptmembersummary.php">
<input type="hidden" name="name" value="$name">
<select name="listitem" onchange="this.form.submit()">
<option value="">View members records for:</option>
formPart1;
foreach ($nc as $k => $v) {
	echo "<option value=\"$k\">$k</option>";
	}
echo "</select>";
echo "</form>";

if ($listitem == "") {
  echo "<div class=\"well\">";
	echo "<table border=\"0\" class=\"table-condensed\">";
	echo "<tr><td><b>MC Type</b></td><td><b>Mbr.Count</b></td></tr>";

	foreach ($nc as $k=>$v) {
		if ($k == "") echo "<td>NONE: $v</td>";
		else { 
			$ftot = number_format($nt[$k],2);
			echo "<tr>";
			echo "<td>$k</td><td align=\"right\">$v</td>";
			echo "</tr>";
			}
		}	
	$grtot = number_format($grandtot,2);
	echo "<tr><td>Type Count: ". count($nc)."</td><td>Total Mbrs: $tot</td></tr>";
	echo "</table>";
	echo "</div> <!-- well -->";
	exit;
	}

// do query and list all Member records for a specific item within a category
echo "Listing member records for $listitem&nbsp;&nbsp;";
echo "<a href=\"rptmembersummary.php?name=$name&listitem=\">(Choose Again)</a><br />";

$sql = "SELECT * 
	FROM `members` 
	WHERE `Inactive` = 'FALSE' 
		AND `MemStatus` = '$name' 
		AND `MCtype` = '$listitem' 
	ORDER BY `MCID`";
$res = doSQLsubmitted($sql);
echo "<div class=\"well\"><table class=\"table-condensed\">";
echo "<tr><td><b>MCID</b></td><td><b>MemStatus</b></td><td><b>Member Name</b></td></tr>";
while ($r = $res->fetch_assoc()) {
	//echo "<pre>$name: "; print_r($r); echo "</pre>";
	echo "<tr><td>$r[MCID]</td><td align=\"center\">$r[MemStatus]</td><td>$r[NameLabel1stline]</td></tr>";
	$detailcnt += 1;
	}
$detailtot = number_format($detailtot,2);
echo "<tr><td>Count: $detailcnt</td>&nbsp;<td></td></tr></table></div>";
echo "End of member list<br>";
?>
<a href="rptmembersummary.php?name="">START OVER</a>
</div>  <!-- container -->
<!-- <script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script> -->
</div>
</body>
</html>
