<?php
session_start();
unset($_SESSION['ActiveMCID']);
?>
<!DOCTYPE html>
<html>
<head>
<title>Member Search</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>

<?php
//include "Incls/vardump.inc.php";
include 'Incls/seccheck.inc.php';
include "Incls/datautils.inc.php";
include 'Incls/mainmenu.inc.php';

$filter = (isset($_REQUEST['filter'])) ? $_REQUEST['filter'] : "";
?>

<div class="container">
<h1>Member Search&nbsp;&nbsp;
<span id="helpbtn" title="Help" class="glyphicon glyphicon-question-sign" style="color: blue; font-size: 20px"></span></h1>
<form action="mbrsearchlist.php" method="post"  name="searchform" class="form-inline">
<input type="text" name="filter" class="form-control" style="width: 200px; "  placeholder="Search String" autofocus value=<?=$filter?>>
<input type="submit" class="btn btn-default" name="submit" value="Apply">
</form>
<br />
<div id="help">
<p><b>Tips for a General Search</b></p>
<p>Any character string may be entered,  The string provided is compared to the first name, last name, label name, address, city and email adress fields of every record in the database.</p><p>All records containing the string will be listed on the results page with a bullet preceeding it.  Click the specific bullet assoicated with the MCID to access that members information along with all its assoicated correspondence and funding records.</p>
<p>Try and keep the string entered to 4-6 characters even though many more may be entered.  Entering a longer string may result in no records being returned.</p>
<p>NOTE: A percent sign (%) may be used to list all member records of currently on the database.</p><br><br>
</body>
</html>
</div>    <!-- help -->

<?php
if (strlen($filter) == 0) exit;
// search db for filter value
$sql = "SELECT * FROM `members` WHERE `MCID` LIKE '%".$filter."%' OR `FName` LIKE '%".$filter."%' OR `LName` LIKE '%".$filter."%' OR `NameLabel1stline` LIKE '%".$filter."%' OR `Organization` LIKE '%".$filter."%' OR `AddressLine` LIKE '%".$filter."%' OR `EmailAddress` LIKE '%".$filter."%' OR `EmailAddress2` LIKE '%".$filter."%' OR `City` LIKE '%".$filter."%' OR `ZipCode` LIKE '%".$filter."%'OR `Notes` LIKE '%".$filter."%' ORDER BY `MCID`";
$results = doSQLsubmitted($sql);
//$results = searchForMember($filter);
$nbrofrows = $results->num_rows;
//echo "Number of rows in result: $nbrofrows<br>";
// nothing returned so ask again
if ($nbrofrows == 0) {
	echo "<h2>No members found with the search string provided</h2>";
	echo "<p>Enter a string to search for in first name, last name, email address, address or city</p><br /><br />";
	exit();
	}
// only 1 found so pass it to mbrinfotabbed page
if ($nbrofrows == 1) {
		echo "only 1 match<br />";
		$results->data_seek(0);
		$row = $results->fetch_assoc();
		$mcid = $_SESSION['ActiveMCID'] = $row['MCID'];
print <<<oneBullet
<form action="mbrinfotabbed.php" name="FORM_NAME" method="post">
<input autofocus type="text" name="filter" value="$mcid" />
<input type="submit" />
</form>
<SCRIPT TYPE="text/JavaScript">document.forms["FORM_NAME"].submit();</SCRIPT>
<br /><br />
oneBullet;
	exit;
	}
// multiple rows returned, list identifying fields to select from
$results->data_seek(0);
//echo "<fieldset><legend>List containing: $filter</legend>";
echo "<fieldset><h3>List of $nbrofrows containing: $filter</h3>";
echo "<table class=\"table table-condensed\">";
echo '<tr><th>MCID</th><th>Last Name</th><th>First Name</th><th>Address</th><th>City</th><th>Email(s)</th></tr>';
while ($row = $results->fetch_assoc()) {
	$mcid=$row['MCID'];  $fname=$row['FName']; $lname=$row['LName'];
	$org=$row['Organization']; $addr=$row['AddressLine']; $lab1line=$row['NameLabel1stline']; 
	$city = $row['City']; $eaddr=$row['EmailAddress']; $eaddr2=$row['EmailAddress2'];
echo "
<tr><td><a href=\"mbrinfotabbed.php?filter=$mcid\">$mcid</a></td><td>$lname</td><td>$fname</td><td>$addr</td><td>$city</td><td>$eaddr";
if (strlen($eaddr2)>0) echo "<br>$eaddr2";
echo '</td></tr>';

}
echo "</fieldset></table>";
?>

</body>
</html>
