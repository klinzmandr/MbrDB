<!DOCTYPE html>
<html>
<head>
<title>Membership Home Page</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body onchange="flagChange()">
<script>
<!-- Form change variable must be global -->
var chgFlag = 0;

function chkchg() {
	if (chgFlag == 0) { return true; }
	var r=confirm("All changes made will be lost.\n\nConfirm by clicking OK. (" + chgFlag + ")");	
	if (r == true) { return true; }
	return false;
	}

function flagChange() {
	chgFlag += 1;
	//alert("something has changed count: " + chgFlag);
	return true;
	}
</script>

<?php
session_start();
include 'Incls/seccheck.inc.php';
include 'Incls/adminmenu.inc.php';
include 'Incls/datautils.inc.php';

$file = isset($_REQUEST['file'])? $_REQUEST['file'] : "";
$action = isset($_REQUEST['action'])? $_REQUEST['action'] : "";
$updfile = isset($_REQUEST['updfile'])? $_REQUEST['updfile'] : "";
$ta = isset($_REQUEST['ta'])? $_REQUEST['ta'] : "";

echo "<div class=\"container\">";
//echo "database in use: ".$_SESSION['DB_InUse']."<br>";
if ($action == "update") {
	//echo "update requested for record: $updfile<br>";
	//echo "with the values:<br />"; 
	//echo "<pre>"; echo $ta; echo "</pre><br />";
	$rc = updatedblist($updfile,$ta);
	//echo "rows updated: $rc<br>";
	echo "<h4>File updated successfully: $updfile</h4>";
	$file = $updfile;
	}

echo "<h2>List Maintenance Utility</h2>";
if ($file == "") {
	echo '<p>Choose a menu option to update a specific list.</p>
	<p>All list (except the Admin Users list, use a free form text file to define the list items used.  Lines that begin with a double slash (//) are provided for comments (which are encouraged.)  The comment lines as well as blank lines are ignored </p>
	<p>Lines are formated into two parts seperated with a colon (:). The first part is used to write into the database and the second is the descriptive text that is displayed in the drop down selection list.  The first part is what is used when searching or creating reports from the database so careful thought should be put into the selection of the terms used.  </p>
	<p>Spaces are important and count even though they are not immediately visable.  For example, to create a &apos;blank&apos; item in the list one would specifify &apos; : &apos; (without the apostrophies, of course.)</p>';
	
	echo "<p>Make sure to save your changes after performaing any updates.</p>";
	}

if ($file == "MCTypes") {
	echo "<h3>Member/Contact Types</h3>";
	echo "<form action=\"admlistmaint.php\" method=\"post\">";
	echo "<textarea name=\"ta\" rows=\"20\" cols=\"100\">";
	echo readdblist('MCTypes');
	echo "</textarea><br />";	
	echo "<input type=\"hidden\" name=\"action\" value=\"update\">";
	echo "<input type=\"hidden\" name=\"updfile\" value=\"$file\">";	
	echo "<input type=\"submit\" name=\"Submit\" value=\"Submit Changes\" />";
	echo "</form>";
	echo '<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></div></body></html>';
	exit;
	}

if ($file == "Programs") {
	echo "<h3>Fund Programs</h3>";
	echo "<form action=\"admlistmaint.php\" method=\"post\">";
	echo "<textarea name=\"ta\" rows=\"20\" cols=\"100\">";
	echo readdblist('Programs');
	echo "</textarea><br />";	
	echo "<input type=\"hidden\" name=\"action\" value=\"update\">";
	echo "<input type=\"hidden\" name=\"updfile\" value=\"$file\">";	
	echo "<input type=\"submit\" name=\"Submit\" value=\"Submit Changes\" />";
	echo "</form>";
	echo '<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></div></body></html>';
	exit;
	}

if ($file == "Purposes") {
	echo "<h3>Funding Purposes</h3>";
	echo "<form action=\"admlistmaint.php\" method=\"post\">";
	echo "<textarea name=\"ta\" rows=\"20\" cols=\"100\">";
	echo readdblist('Purposes');
	echo "</textarea><br />";	
	echo "<input type=\"hidden\" name=\"action\" value=\"update\">";
	echo "<input type=\"hidden\" name=\"updfile\" value=\"$file\">";	
	echo "<input type=\"submit\" name=\"Submit\" value=\"Submit Changes\" />";
	echo "</form>";
	echo '<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></div></body></html>';
	exit;
	}

if ($file == "Campaigns") {
	echo "<h3>Funding Campaigns</h3>";
	echo "<form action=\"admlistmaint.php\" method=\"post\">";
	echo "<textarea name=\"ta\" rows=\"20\" cols=\"100\">";
	echo readdblist('Campaigns');
	echo "</textarea><br />";	
	echo "<input type=\"hidden\" name=\"action\" value=\"update\">";
	echo "<input type=\"hidden\" name=\"updfile\" value=\"$file\">";	
	echo "<input type=\"submit\" name=\"Submit\" value=\"Submit Changes\" />";
	echo "</form>";
	echo '<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></div></body></html>';
	exit;
	}

if ($file == "CorrTypes") {
	echo "<h3>Correspondence Types</h3>";
	echo "<form action=\"admlistmaint.php\" method=\"post\">";
	echo "<textarea name=\"ta\" rows=\"20\" cols=\"100\">";
	echo readdblist('CorrTypes');
	echo "</textarea><br />";	
	echo "<input type=\"hidden\" name=\"action\" value=\"update\">";
	echo "<input type=\"hidden\" name=\"updfile\" value=\"$file\">";	
	echo "<input type=\"submit\" name=\"Submit\" value=\"Submit Changes\" />";
	echo "</form>";
	echo '<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></div></body></html>';
	exit;
	}

if ($file == "Locs") {
	echo "<h3>City, State and Zips</h3>";
	echo "<form action=\"admlistmaint.php\" method=\"post\">";
	echo "<textarea name=\"ta\" rows=\"20\" cols=\"100\">";
	echo readdblist('Locs');
	echo "</textarea><br />";	
	echo "<input type=\"hidden\" name=\"action\" value=\"update\">";
	echo "<input type=\"hidden\" name=\"updfile\" value=\"$file\">";	
	echo "<input type=\"submit\" name=\"Submit\" value=\"Submit Changes\" />";
	echo "</form>";
	echo '<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></div></body></html>';
	exit;
	}

if ($file == "Tester") {
	echo "<h3>Tester Record - called when adminmenu.inc.php is changed</h3>";
	echo "<form action=\"admlistmaint.php\" method=\"post\">";
	echo "<textarea name=\"ta\" rows=\"20\" cols=\"100\">";
	echo readdblist('Tester');
	echo "</textarea><br />";	
	echo "<input type=\"hidden\" name=\"action\" value=\"update\">";
	echo "<input type=\"hidden\" name=\"updfile\" value=\"$file\">";	
	echo "<input type=\"submit\" name=\"Submit\" value=\"Submit Changes\" />";
	echo "</form>";
	echo '<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></div></body></html>';
	exit;
	}

?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>
