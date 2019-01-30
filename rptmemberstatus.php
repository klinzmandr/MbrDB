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

$status = $_REQUEST['status'];
if ($status != "") {
	if ($status == 0) $desc = "Contacts";
  if ($status == 1) $desc = "Members";
  if ($status == 2) $desc = "Volunteers"; 
  if ($status == 3) $desc = "Organizations";
  $sql = "SELECT * 
  	FROM `members` 
  	WHERE `Inactive` = 'FALSE' 
		AND `MemStatus` =  '".$status."' 
  	ORDER BY `MCID`";
  $results = doSQLsubmitted($sql);
	$numrows = $results->num_rows;
	//echo "number of member records of status '$status' is $numrows<br>";
	echo "<div class=\"container\"><h3>List of $numrows Members with Status $status: $desc &nbsp;&nbsp;&nbsp;<a class=\"btn btn-xs btn-primary\" href=\"javascript:self.close();\">(CLOSE)</a></h3>";
	print <<<listForm
	<form class="form" name="MemStat">
<select onchange='this.form.submit()'name="status" size="1">
<option value="">Select a Member Status</option>
<option value="0">0-Contact</option>
<option value="1">1-Member</option>
<option value="2">2-Volunteer</option>
<option value="3">3-Donor</option>
</select>&nbsp;&nbsp;
listForm;
	echo "<a href=\"downloads/MemberStatus.csv\" download=\"MemberStatus.csv\">DOWNLOAD CSV FILE</a>";
	echo "<button type=\"button\" class=\"btn btn-xs btn-default\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Fields separated by semicolon(;)\nText fields are quoted.\"><span class=\"glyphicon glyphicon-info-sign\" style=\"color: blue; font-size: 20px\"></span></button>";
	
//	<span class="glyphicon glyphicon-info-sign" style="color: #FF0000; font-size: +4; "></span>

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
	file_put_contents('downloads/MemberStatus.csv',$csv);
	exit;
	}

print <<<formPart1
<div class="container">
<h3>Member Status to list:&nbsp;&nbsp;&nbsp;<a class="btn btn-primary" href="javascript:self.close();">(CLOSE)</a></h3>

<form class="form" name="MemStat">
<select onchange='this.form.submit()' name="status" size="1">
<option value="">Select a Member Status</option>
<option value="0">0-Contact</option>
<option value="1">1-Member</option>
<option value="2">2-Volunteer</option>
<option value="3">3-Donor</option>
</select>
<!-- <input type=submit name="submit" value="submit"> -->
</form>

</div>  <!-- container -->
formPart1;

?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>
