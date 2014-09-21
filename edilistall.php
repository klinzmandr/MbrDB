<!DOCTYPE html>
<html>
<head>
<title>List All EDI Rcds</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<?php
session_start();
include 'Incls/seccheck.inc';
include 'Incls/mainmenu.inc';
include 'Incls/datautils.inc';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";

if ($action == "update") {
	echo "action is update<br>";
	}

$sql = "SELECT * FROM `extradonorinfo` ORDER BY `MCID`;";
$res = doSQLsubmitted($sql);
$nbr_rows = $res->num_rows;
	if ($nbr_rows == 0) {
		print <<<nadaEDI
<div class="container"><h3>No MCID with Extended Donor Info exists.</h3></div>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
nadaEDI;
		exit;
		}
print <<<pagePart1
<div class="container">
<h3>List of MCID with EDI Records</h3>
pagePart1;
echo "<table class=\"table-condensed\">";
echo "<tr><th>MCID</th><th>Name</th><th>Date Entered</th><th>Last Updated</th><th>Last Updater</th></tr>";
while ($r = $res->fetch_assoc()) {
	//echo "<pre>"; print_r($r); echo "</pre>";
	$edimcid = $r[MCID]; $namelabel1stline = $r[NameLabel1stline];
	$doe = $r[DateEntered]; $dlu = $r[LastUpdated]; $lastupdater = $r[LastUpdater];
	echo "<tr><td><a href=\"MbrInfotabbed.php?filter=$edimcid\">$edimcid</a></td><td>$namelabel1stline</td><td>$doe</td><td>$dlu</td><td>$lastupdater</td></tr>";
	}
echo "</table>";
?>
</div>  <!-- container -->
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
