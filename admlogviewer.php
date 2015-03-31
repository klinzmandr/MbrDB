<!DOCTYPE html>
<html>
<head>
<title>Log Viewer</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<?php
session_start();
//include 'Incls/vardump.inc';
include 'Incls/seccheck.inc';
include 'Incls/datautils.inc';

$sd = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : date('Y-m-d', strtotime('-1 day'));
$ed = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : date('Y-m-d H:i:s', strtotime(now));
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

echo "<h3>Activity Log Viewer&nbsp;&nbsp;&nbsp;<a class=\"btn btn-sm btn-primary\" href=\"javascript:self.close();\">CLOSE</a>&nbsp;&nbsp;&nbsp;<a class=\"btn btn-primary btn-success\" href=\"admlogviewer.php?sd=$sd&ed=$ed&action=form\">New Search</a></h3>";
if ($action == '') {
print <<<pagePart1
<h3>Database Activity Log Viewer</h3>
<p>This utility allows the activity log of the database to be examined.  This log is where all database requests are recorded along with the date/time, the originating page address and userid performing the action.</p>
<p>All actions within the date/time range are listed.  If there is a search string entered, it will be used to filter those actions listing only those that have matching strings in the userid and log activity fields.</p>
<p>The date format will default to the system standard of 'YYYY-MM-DD HH:MM:SS' for any date entered.  This will allow very narrow date/time ranges to be specific - right down to the second.</p>
<p>Default date range is from midnight of the current date to midnight of the date 30 days prior.</p>

pagePart1;
$action = 'form';

}

//echo "sd: $sd, ed: $ed<br />";
if ($action == 'form') {
print <<<formPart
<form action="admlogviewer.php">
Start: <input type="text" name="sd" value="$sd" placeholder="YYYY-MM-DD" onchange="ValidateDate(this)">
End: <input type="text" name="ed" value="$ed" placeholder="YYYY-MM-DD" onchange="ValidateDate(this)">
Search (optional)<input type="text" name="search" value="">
<input type="hidden" name="action" value="search">
<input type="submit" name="submit" value="Submit">
</form>

formPart;

	}

if ($action == 'search') {
	
	$search = $_REQUEST['search'];
	if (strlen($search) > 0) $search = "%$search%";
	else $search = '%';
	//$sdts = UNIX_TIMESTAMP($sd); $edts = strtotime($ed);
	//echo "log search action input: $search<br />";
	//$sql = "SELECT * FROM `log` WHERE ( `DateTime` >= '$sd' AND `DateTime` <= '$ed' ) AND ( `User` LIKE '$search' OR `SQL` LIKE '$search' OR `Page` LIKE '$search' );";	
	$sql = "SELECT * FROM `log` WHERE ( `DateTime` BETWEEN '$sd' AND '$ed' ) AND ( `User` LIKE '$search' OR `SQL` LIKE '$search' OR `Page` LIKE '$search' );";
	//echo "sql: $sql<br />";
	$res = doSQLsubmitted($sql);
	$rowcount = $res->num_rows;
	echo "Rows returned: $rowcount<br />";
	echo '<table border="1" >';
	echo '<tr><th>LogID</th><th>Date/Time</th><th>User Login</th><th>SecLevel</th><th>Ref Page</th><th>SQL submitted</th></tr>';
	while ($r = $res->fetch_assoc()) {
		//echo '<pre> Log record'; print_r($r); echo '</pre>';
		$seclevel = $r[SecLevel];
		//echo "seclevel: $seclevel<br />";
		echo "<tr><td>$r[LogID]</td><td>$r[DateTime]</td><td>$r[User]</td><td>$seclevel</td><td>$r[Page]</td><td>$r[SQL]</td></tr>";
		}
	echo '</table>';
	
echo "<a class=\"btn btn-primary btn-success\" href=\"admlogviewer.php?sd=$sd&ed=$ed&action=form\">New Search</a>";	
	
	}
?>
<script src="Incls/datevalidation.js"></script>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
