<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Expired MCID's</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<?php
include 'Incls/seccheck.inc.php';
include 'Incls/datautils.inc.php';

$expdate = calcexpirationdate();
$sql = "SELECT * 
	FROM `members`
	WHERE 	`members`.`Inactive` = 'TRUE'
	ORDER BY `MCID` ASC;";
$res = doSQLsubmitted($sql);
$numrows = $res->num_rows;
echo '<h3>Inactive MCID&apos;s&nbsp;&nbsp;<a class="btn btn-primary" href="javascript:self.close();">(CLOSE)</a></h3>
<p>List of '.$numrows.' inactive MCID&apos; eligible for deletion after Delete Date.</p>
<table class="table table-condensed">
<tr><th>InAct.</th><th>MCID</th><th>InactDate</th><th>DeleteDate</th><th>MemStatus</th><th>Name</th><th>Address</th><th>City</th><th>St</th><th>Zip</th><th>PriPhone</th><th>Email</th></tr>';

while ($r = $res->fetch_assoc()) {
	//echo '<pre>MCID'; print_r($r); echo '</pre>';
	if ($r['Last'] <= $expdate) {
		//if ($r[Inactive] == 'TRUE') $ia = 'TRUE';
		//else $ia = 'FALSE';
		//echo "inactive db flag: $r[Inactive]<br />";
		$deldate = date('Y-m-d', strtotime("$r[Inactivedate] +90 days"));
		echo "<tr><td>$r[Inactive]</td><td>$r[MCID]</td><td>$r[Inactivedate]</td><td>$deldate</td><td align=\"center\">$r[MemStatus]<td>$r[NameLabel1stline]</td><td>$r[AddressLine]</td><td>$r[City]</td><td>$r[State]</td><td>$r[ZipCode]</td><td>$r[PrimaryPhone]</td><td>$r[EmailAddress]</td></tr>";
		}
	}
echo '</table><br />----END OF REPORT----<br />';

?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
