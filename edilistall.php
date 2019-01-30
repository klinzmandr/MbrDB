<?php
session_start();
?>
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
include 'Incls/seccheck.inc.php';
include 'Incls/mainmenu.inc.php';
include 'Incls/datautils.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$kw = isset($_REQUEST['kw']) ? $_REQUEST['kw'] : "";

$sql = "SELECT * FROM `extradonorinfo` 
WHERE `personal` LIKE '%$kw%' 
   OR `education` LIKE '%$kw%'
   OR `business` LIKE '%$kw%'
   OR `other` LIKE '%$kw%'
   OR `wealth` LIKE '%$kw%'
   OR `research` LIKE '%$kw%'
ORDER BY `MCID`;";
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

<!-- filter form -->
<script>
function rset() {
	//alert("reset function");
	//document.getElementById("sd").value = '';
	document.getElementById("kw").value='';
	return true;
	}
</script>
<form name="filter" action="edilistall.php" method="post">
Filter: <input type="text" name="kw" id="kw" value="$kw">
<input type="submit" value="Apply"><input type="button" value="Reset" onclick="return rset()">
</form>

pagePart1;
echo "<table class=\"table-condensed\">";
echo "<tr><th>MCID</th><th>Name</th><th>Date Entered</th><th>Last Updated</th><th>Last Updater</th></tr>";
while ($r = $res->fetch_assoc()) {
	//echo "<pre>"; print_r($r); echo "</pre>";
	$edimcid = $r[MCID]; $namelabel1stline = $r[NameLabel1stline];
	$doe = $r[DateEntered]; $dlu = $r[LastUpdated]; $lastupdater = $r[LastUpdater];
	echo "<tr><td><a href=\"mbrinfotabbed.php?filter=$edimcid\">$edimcid</a></td><td>$namelabel1stline</td><td>$doe</td><td>$dlu</td><td>$lastupdater</td></tr>";
	}
echo "</table>";
?>
</div>  <!-- container -->
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
