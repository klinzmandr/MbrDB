<!DOCTYPE html>
<html>
<head>
<title>Correpondence Followups</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
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
$duesval = isset($_REQUEST['duesval']) ? $_REQUEST['duesval'] : 0;
$donval = isset($_REQUEST['donval']) ? $_REQUEST['donval'] : 0;
$today = date("Y-m-d",strtotime(now));

//echo "<div class=\"container\">";
echo "<h3>Correspondence Followup Report&nbsp;&nbsp;<a href=\"javascript:self.close();\" class=\"btn btn-primary\"><b>CLOSE</b></a></h3>";

$dueschkfld = "<input type=\"checkbox\" name=\"chkdues\" value=\"chkdues\">";
if (isset($_REQUEST['chkdues'])) $dueschkfld = "<input type=\"checkbox\" name=\"chkdues\" value=\"chkdues\" checked>";
$donchkfld = "<input type=\"checkbox\" name=\"chkdon\" value=\"chkdon\">";
if (isset($_REQUEST['chkdon'])) $donchkfld = "<input type=\"checkbox\" name=\"chkdon\" value=\"chkdon\" checked>";

// get date range and minimum values for dues and donations
print <<<pagePart2

<form action="rptcorrfollowup.php" method="post">
For a date range from  
<input type="text" name="sd" id="sd" value="$sd" placeholder="Start Date">
&nbsp;to&nbsp;
<input type="text" name="ed" id="ed" value="$ed" placeholder="End Date"><br>
$dueschkfld 
for Dues payment greater than <input type="text" name="duesval" value="$duesval"> AND/OR<br>
$donchkfld
for Donation payment greater than 
<input type="text" name="donval" value="$donval">
<input type="hidden" name="action" value="continue">
&nbsp;&nbsp;<input type="submit" name= "submit" value="Submit">
</form>

pagePart2;

// explain report first time through
if ($action == "") {
print <<<pagePart1
<p>This report lists those members who have LAST dues AND/OR LAST donation payments within the date range specified but have NOT had any followup correspondence sent to them.</p>
<p>The following are will cause an MCID to not be listed:</p>
<ul>
Any thank you correspondence sent after the last Dues or Donation payment date.<br>
The MCID is designated as a subscribing member or volunteer. <br>
The MCID is marked as inactive.<br>
The last dues payment for the MCID is less than the minimum (if specified)<br>
The last donation payment for the MCID is less than the minimum (if specified)
</ul>
<p>A member&apos;s MCID being in this list means that there probably should be a follow up contact made with the member acknowledging the funding payment.</p>
</script><script src="jquery.js"></script><script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc.php"></script>
</body></html>

pagePart1;
exit;
}

// first all correspondence classes are listed with counts, get items from db to count and summarize

$sql = "SELECT `correspondence`.*, `members`.*  
	FROM `correspondence`, `members`  
	WHERE `correspondence`.`MCID` = `members`.`MCID`
	AND ( `members`.`LastDuesDate` between '$sd' and '$ed' 
			OR `members`.`LastDonDate` between '$sd' and '$ed' )  
	AND ( `members`.`LastDuesDate` > `members`.`LastCorrDate`
			OR 	`members`.`LastDonDate` > `members`.`LastCorrDate` )
	ORDER BY `members`.`MCID` ASC;";

//echo "SQL: $sql<br>";
$res = doSQLsubmitted($sql);
$rowcnt = $res->num_rows;

$ctypearray = array(); $ctypecountarray = array();
while ($r = $res->fetch_assoc()) {
	//echo '<pre> returned '; print_r($r); echo '</pre>';
	if ($r[MCID] == 'OTD00') continue;															// forget about one time donations
	if ($r[CorrespondenceType] == 'RenewalReminder') continue;			// don't remember renewal reminders
	if (stripos($r[MCtype],'subscr') !== FALSE) continue;						// subscribing members don't count
	if ($r[Inactive] == 'TRUE') continue;														// neither do inactive members
	if (stripos($r[CorrespondenceType], 'TY') !== FALSE) { 					// TY note sent so drop this one
		unset($ctypearray[$r[MCID]]);
		continue;
		}
	if (isset($_REQUEST['chkdues'])) {										// drop anything below min value for dues
		if ($r[LastDuesAmount] < $_REQUEST['duesval']) continue;
		}
	if (isset($_REQUEST['chkdon'])) {											// drop anything below min value for don's			
		if ($r[LastDonAmount] < $_REQUEST['donval']) continue;
		}
	$ctypearray[$r[MCID]] = $r;																			// report this one
	//echo '<pre> RenewalTY '; print_r($r); echo '</pre><br />';
	}

//echo "<pre>ctypearray "; print_r($ctypearray); echo "</pre>";
$cnt = count($ctypearray);
if ($cnt > 0) {
	echo '<table class="table-condensed">';
	echo "<h3>Follow Up Actions ($cnt) </h3>
	<tr><th>MCID</th><th>LastDuesDate</th><th>LastDuesAmt</th><th>LastDonDate</th><th>LastDonAmt</th><th>LastCorrDate</th><th>LastCorrType</th><th>Name</th><th>MemberType</th><th>Member Notes</th></tr>";
	ksort($ctypearray);
	foreach ($ctypearray as $k => $v) {
		echo "<tr><td>$k</td><td>$v[LastDuesDate]</td><td>$v[LastDuesAmount]</td><td>$v[LastDonDate]</td><td>$v[LastDonAmount]</td><td>$v[LastCorrDate]</td><td>$v[LastCorrType]</td><td>$v[NameLabel1stline]</td><td>$v[MCtype]</td><td>$v[Notes]</td></tr>";
		}
	echo "</table>===== END OF REPORT =====<br>";
	}
else echo "<h3>No follow up actions for the given date range.</h3>";

?>

<!-- </div>  container -->

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="Incls/bootstrap-datepicker-range.inc.php"></script>

</div>
</body>
</html>
