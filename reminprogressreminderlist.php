<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Reminders List</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/bootstrap-sortable.css" rel="stylesheet" media="all">
</head>
<body>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-sortable.js"></script>
<style> th { color: red; } </style>

<script>
$(function() {
// event triggered when any column of table heading is clicked
  $("#tab").on("sorted", function() {
    var x = $(this).text();
    // alert("Table sorted by column heading clicked");
    
    });
});
</script>

<?php
//include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
//include 'Incls/mainmenu.inc.php';
include 'Incls/datautils.inc.php';

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
		//echo "reminder records: $r['CORID'], mcid: $mcidid<br>";
		$ar[$mcidid] += 1;
		$tr[$mcidid] = $r['DateSent']; 
		if (strtotime($dr[$mcidid]) < strtotime($r['DateSent'])) {
			$dr[$mcidid] = $r['DateSent'];
			$mstat[$mcidid] = $r['MemStatus'];
			$labelname[$mcidid] = $r['NameLabel1stLine'];
			$corrtype[$mcidid] = $r['CorrespondenceType'];
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
	<p>The MCIDs listed are those members and volunteers that have been sent reminders.  The listing provides the sorted total count of reminders that have been sent as well as the date of the last one. An MCID will be removed from this list when one of the following occurs:</p>
	<ol>
	<li>A funding payment is received and entered as a &apos;Dues&apos; payment (or a donation for a 3-Donor),</li>
	<li>The member record is marked as &apos;Inactive&apos; or</li>
	<li>The status of the member record is marked as &apos;0-Contacts.</li>
</ol>
<p>This table is <b><font color=red>SORTABLE</font></b>.  Click the column heading to sort the listing by the table column ascending or descending values.</p>";

echo '<table id="tab" border=1 class="table table-condensed sortable">
<thead><tr><th>MCID</th><th align="center">Name</th><th>MemStatus</th><th align="center">Count</th><th>Last Sent</th><th align="center">Type Sent</th></tr></thead><tbody>';

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
<td>$mcid</td><td>$name</td><td align="center">$ms</td><td>$count</td><td>$ls</td><td>$ct</tr>

</div>  <!-- container -->
bulletForm;
}
echo "</tbody></table>";

?>
</div>
</body>
</html>
