<!DOCTYPE html>
<html>
<head>
<title>Reminders List</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>

<?php
session_start();

//include 'Incls/vardump.inc';
include 'Incls/seccheck.inc';
//include 'Incls/mainmenu.inc';
include 'Incls/datautils.inc';

$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort']: 'Count';
 
$sql = "SELECT `correspondence`.*, `members`.`MemStatus`, `members`.`Inactive`, `members`.`NameLabel1stLine` 	
	FROM `members`, `correspondence` 
	WHERE `members`.`MCID` = `correspondence`.`MCID` 
		AND `correspondence`.`Reminders` IS NOT NULL 
		AND (`members`.`MemStatus` = 1 OR `members`.`MemStatus` = 2) 
		AND `members`.`Inactive` = 'FALSE' 
	ORDER BY `correspondence`.`MCID` ASC, `correspondence`.`CORID` ASC;";
$results = doSQLsubmitted($sql);

$nbr_rows = $results->num_rows;
if ($nbr_rows == 0) {
	print <<<noRem
<div class="container">
<h3>No in-progress reminders are noted.
<a href="javascript:self.close();" class="btn btn-primary">CLOSE</a></h3>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>
noRem;
	exit;
	}

$results->data_seek(0);
$ar = array(); $mstat = array(); $labelname = array(); $corrtype = array();
$dr = array(); $tr = array(); $pr = array();
// parse result rows to find those reminders without renewal notices
while ($r = $results->fetch_assoc()) {
	$mcidid = $r['MCID'];
	if (stripos($r['Reminders'],"remind") !== FALSE) {  // count the reminder notices sent since last renewal paid
		//echo "reminder records: $r[CORID], mcid: $mcidid<br>";
		$ar[$mcidid] += 1;
		$tr[$mcidid] = $r[DateSent]; 
		if (strtotime($dr[$mcidid]) < strtotime($r[DateSent])) {
			$dr[$mcidid] = $r[DateSent];
			$mstat[$mcidid] = $r[MemStatus];
			$labelname[$mcidid] = $r[NameLabel1stLine];
			$corrtype[$mcidid] = $r[CorrespondenceType];
			}
		}
	if (stripos($r['Reminders'],"RenewalPaid") !== FALSE) {		// forget it all since a renewal done
		unset($ar[$mcidid]);
		unset($dr[$mcidid]);
		unset($tr[$mcidid]);
		}
	}
//echo "<pre>active MCID's"; print_r($ar); echo "</pre>";
$drcount = count($dr);

echo "<div class=\"container\">";
echo '<h3>In-progress Reminders&nbsp;&nbsp;<a href="javascript:self.close();" class="btn btn-primary">CLOSE</a></h3>
';
echo "<h4>Listing of $drcount MCIDs:</h4>
<form>
Sort by: <select name=sort onchange='this.form.submit()'>
<option>Select sort order</option>
<option>Count</option>
<option>Last Sent</option>
<option>MCID</option>
</select>
</form>

	<p>The MCIDs listed are those members and volunteers that have been sent reminders.  The listing provides the sorted total count of reminders that have been sent as well as the date of the last one. An MCID will be removed from this list when one of the following occurs:</p>
	<ol>
	<li>A funding payment is received and entered as a &apos;Dues&apos; payment (or a donation for a 3-Donor),</li>
	<li>The member record is marked as &apos;Inactive&apos; or</li>
	<li>The status of the member record is marked as &apos;0-Contacts.</li>
</ol>";
if ($sort == 'MCID') echo '<h4>Sorted by ' . $sort . ' ascending</h4>';
else echo '<h4>Sorted by ' . $sort . ' decending</h4>';
echo "<table border=\"0\" class=\"table table-condensed\">";
echo "<tr><th>MCID</th><th align=\"center\">Name</th><th>MemStatus</th><th align=\"center\">Count</th><th>Last Sent</th><th align=\"center\">Type Sent</th></tr>";

// ksort($dr);					// sorted by MCID
arsort($ar);							// sorted by reminder cound
arsort($tr);						// sorted by date sent
// echo '<pre>'; print_r($tr); echo '</pre>';

$pr = $tr;
if ($sort == 'Count') $pr = $ar;
if ($sort == 'MCID') { ksort($ar); $pr = $ar; }

foreach ($pr as $mcid => $value) {
	$ls = $dr[$mcid];
	$ms = $mstat[$mcid];
	$name = $labelname[$mcid];
	$ct = $corrtype[$mcid];
	$count = $ar[$mcid];
print <<<bulletForm
<tr>
<!-- <td>
<form action="mbrinformation.php" method="post">
<input type="radio" name="filter" id="1" value="$mcid" onclick='this.form.submit()'></td></form>
</td> -->
<td>$mcid</td><td>$name</td><td align="center">$ms</td><td>$count</td><td>$ls</td><td>$ct</tr>

</div>  <!-- container -->
bulletForm;
}
echo "</table>";

?>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>
