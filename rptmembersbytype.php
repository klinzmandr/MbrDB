<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>List Members By Type</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>

<?php
//include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
include 'Incls/datautils.inc.php';
//include 'Incls/mainmenu.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if ($action == 'search') {
	$cbox = $_REQUEST['cbox'];
// echo '<pre> cbox'; print_r($cbox); echo '</pre>';
	$cblist = "('" . implode("','",$cbox) . "')";
// echo "cblist: $cblist<br>";

  $sql = "SELECT * 
  	FROM `members` 
  	WHERE `Inactive` = 'FALSE' 
		AND  `MCtype` in $cblist 
  	ORDER BY `MCID`";
  $results = doSQLsubmitted($sql);
	$numrows = $results->num_rows;
	$cb = implode(', ', $cbox);
	//echo "number of member records of status '$status' is $numrows<br>";
	echo "<div class=\"container\">
	<h3>Members by Member Type(s)&nbsp;&nbsp;&nbsp;<a class=\"btn btn-xs btn-primary\" href=\"rptmembersbytype.php\">(RETURN)</a></h3>
	Count: $numrows, Type(s) selected: $cb<br>";

	echo "<a href=\"downloads/MembersByType.csv\" download=\"MemberStatus.csv\">DOWNLOAD CSV FILE</a>";
	echo "<button type=\"button\" class=\"btn btn-xs btn-default\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Fields separated by semicolon(;)\nText fields are quoted.\"><span class=\"glyphicon glyphicon-info-sign\" style=\"color: blue; font-size: 20px\"></span></button>";
  echo "<table class=\"table-condensed table-hover\">";
  echo "<tr><th>MCID</th><th>Label 1st Line</th><th>MCType</th><th>Organization</th><th>Email</th><th>AddressLine</th><th>City</th><th>State</th><th>ZipCode</th></tr>";
  $csv = array();
  $csv[] = "MCID;Name;MCType;Organization;Email Address;Street Address;City;St;Zip;Notes\n";
  $translate = array("\\" => ' ', "\n" => ' ', "\t"=>' ', "\r"=>' ', "\"" =>'');
	while ($r = $results->fetch_assoc()) {
		$note = strtr($r[Notes], $translate);
		$csv[] = "\"$r[MCID]\";\"$r[NameLabel1stline]\";$r[MCtype];$r[Organization];$r[EmailAddress];\"$r[AddressLine]\";$r[City];$r[State];$r[ZipCode];\"$note\"\n";
		echo "<tr><td>$r[MCID]</td><td>$r[NameLabel1stline]</td><td>$r[MCtype]</td><td>$r[Organization]</td><td>$r[EmailAddress]</td><td>$r[AddressLine]</td><td>$r[City]</td><td>$r[State]</td><td>$r[ZipCode]</td><td>$note</td></tr>";
		}
	echo "</table>";
	echo '---- END OF REPORT ----</div>';
	file_put_contents('downloads/MembersByType.csv',$csv);
	exit;
	}

$systemlists = readdblist('MCTypes');
$mctypes = formatdbrec($systemlists);
// echo '<pre> syslistsarray '; print_r($mctypes); echo '</pre>';	

$ua = $_SERVER['HTTP_USER_AGENT'];

foreach ($mctypes as $k => $v) {
	$val = substr($k,0,1);
	switch ($val) {
		case 0: $mctype0[$k] = $v; break;
		case 1: $mctype1[$k] = $v; break;
		case 2: $mctype2[$k] = $v; break;
		case 3: $mctype3[$k] = $v; break;
		}
	}

echo '
<div class="container">
<h3>Choose the Member Type(s) to list:&nbsp;&nbsp;&nbsp;<a class="btn btn-primary" href="javascript:self.close();">(CLOSE)</a></h3>

<script>
function chkvals(form) {
	//alert("check values entered");
	var errmsg = ""; var chks = 0;
	var elems = document.getElementsByName("cbox[]");
	for (i = 0; i < elems.length; i++) {
		if (elems[i].checked) chks++;
		}
	if (chks == 0) {
		errmsg += "No Member Type(s) have been selected\\n";
		}
	if (errmsg == "") return true;
	alert(errmsg);
	return false;
	}
</script>

<form class="form" name="MemType" onsubmit="return chkvals(this)">
<table border=1 class="table table-condensed" border=0>
<tr><td valign="top">Contacts:<ul>';

foreach ($mctype0 as $k => $v) {
	echo "<input type=checkbox name=cbox[] value=\"$k\"> - $v<br>";
	}
echo '</ul></td><td valign=top>Members:<ul>';
foreach ($mctype1 as $k => $v) {
	echo "<input type=checkbox name=cbox[] value=\"$k\"> - $v<br>";
	}
echo '</ul></td><td valign=top>Volunteers:<ul>';
foreach ($mctype2 as $k => $v) {
	echo "<input type=checkbox name=cbox[] value=\"$k\"> - $v<br>";
	}
echo '</ul></td><td valign=top>Supporters:<ul>';
foreach ($mctype3 as $k => $v) {
	echo "<input type=checkbox name=cbox[] value=\"$k\"> - $v<br>";
	}

echo '</ul></td></tr></table>
<input type="hidden" name="action" value="search">
<input type="submit" name="submit" value="submit">';

echo '
</form>

</div>  <!-- container -->';


?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>
