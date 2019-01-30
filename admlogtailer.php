<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Log Tail</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<META http-equiv="refresh" content="5;URL=admlogtailer.php">
</head>
<body>

<?php
//include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
include 'Incls/datautils.inc.php';

$self = $_SESSION['SessionUser'];
$sql = "SELECT * FROM `log` WHERE `User` != '$self' ORDER BY `LogID` DESC LIMIT 0,15;";	
//echo "sql: $sql<br />";
$res = doSQLsubmitted($sql);
$rowcount = $res->num_rows;
//echo "Rows returned: $rowcount<br />";
echo '<table border="1" >';
echo '<tr><th>LogID</th><th>Date/Time</th><th>User Login</th><th>SecLevel</th><th>Ref Page</th><th>SQL submitted</th></tr>';
while ($r = $res->fetch_assoc()) {
	//echo '<pre> Log record'; print_r($r); echo '</pre>';
	$seclevel = $r[SecLevel];
	//echo "seclevel: $seclevel<br />";
	echo "<tr><td>$r[LogID]</td><td>$r[DateTime]</td><td>$r[User]</td><td>$seclevel</td><td>$r[Page]</td><td>$r[SQL]</td></tr>";
	}
echo '</table>';
	
echo "<a class=\"btn btn-success\" href=\"admlogtailer.php\">REFRESH</a><br /><br />
<a class=\"btn btn-primary\" href=\"admDBJanitor.php\">RETURN</a>";	
?>
<!-- <script src="Incls/datevalidation.js"></script> -->
<!-- <script src="jquery.js"></script> -->
<script src="js/bootstrap.min.js"></script>
</body>
</html>
