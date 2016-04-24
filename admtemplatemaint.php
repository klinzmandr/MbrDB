<!DOCTYPE html>
<html>
<head>
<title>Maintain Templates</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>

<?php
session_start();
//include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
include 'Incls/adminmenu.inc.php';
include 'Incls/datautils.inc.php';

//echo "Check in:<br />";

if (isset($_REQUEST['upd'])) {  // update with info from admtemplateedit 
	$path = $_REQUEST['path'];
	$name = $_REQUEST['name'];
	$body = $_REQUEST['body'];
	$flds[Name] = $name;
	$flds[Body] = $body;
//	echo '<pre> flds '; print_r($flds); echo '</pre>';
	sqlupdate('templates',$flds, "`TID` = '$path'");
	//echo "save editted template: $path -> $req<br>";
	}

if (isset($_REQUEST['addltr'])) {
	$req = $_REQUEST['addltr'];
	$flds[Type] = 'mail';
	$flds[Name] = $req;
	$flds[Body] = '<p>enter text here</p>';
	sqlinsert('templates',$flds);
	//echo "mail add: $req<br />";
	}
 
if (isset($_REQUEST['addeml'])) {
	$req = $_REQUEST['addeml'];
	$flds[Type] = 'email';
	$flds[Name] = $req;
	$flds[Body] = '<p>enter text here</p>'; 
	sqlinsert('templates',$flds);
	//echo "email add: $req<br />";
	}

if (isset($_REQUEST['delete'])) {
	$req = $_REQUEST['delete'];
	$sql = "DELETE FROM `templates` WHERE `TID` = '$req';";
	doSQLsubmitted($sql);
	//echo "delete sql: $sql<br />";
	}

echo "<div class=\"container\">";
echo "<h3>Maintain Reminder Templates</h3>";
echo "<p>This page is to create and edit the corresondence templates used when sending reminders.</p>";
//echo "<a class=\"btn btn-primary\" href=\"index.php\">CANCEL AND RETURN</a><br />";

echo "<hr><h4>Current Email Templates</h4>";
$sql = "SELECT * FROM `templates` WHERE `type` = 'email';";
$res = doSQLsubmitted($sql);
echo "Choose from the following list";

echo "<table class=\"table table-condensed\">";
echo "<tr><td>Edit</td><td>Del</td><td>Email Template Name</td></tr>";
echo "<form>";

$recno = 0;
while ($t = $res->fetch_assoc()) {
	$recno = $t[TID];
	$l = "<tr><td width=\"20\">
	<a href=\"admtemplateedit.php?path=$recno\">
  <img src=\"config/b_edit.png\" width=\"16\" height=\"16\" alt=\"EDIT\">
  </a></td>";
	$l .= "<td width=\"20\">
	<a href=\"admtemplatemaint.php?delete=$recno\">
	<img src=\"config/b_drop.png\" width=\"16\" height=\"16\"  alt=\"DELETE\"/>
	</a></td><td>";
	//$l .= rtrim($t,".txt");
	$l .= "$t[Name]</td></tr>";
	echo $l;
	}
//echo "<input type=\"submit\" name=\"submit\" value=\"Submit\">";
echo "</form></table>";	


echo "Add new email template (Note: this name will become the subject line of the email sent)";
echo "<form action=\"admtemplatemaint.php\" method=\"post\">";
echo "<input type=\"text\" style=\"width: 400px; \" name=\"addeml\" value=\"\" />";
echo "<input type=\"submit\" name=\"submit\" value=\"Submit\">";
echo "</form>";	

echo "<hr><h4>Current Letter Templates</h4>";
$sql = "SELECT * FROM `templates` WHERE `type` = 'mail';";
$res = doSQLsubmitted($sql);
echo "Choose from the following list";

echo "<table class=\"table table-condensed\">";
echo "<tr><td>Edit</td><td>Del</td><td>Letter Template Name</td></tr>";
echo "<form>";

$recno = 0;
while ($t = $res->fetch_assoc()) {
	$recno = $t[TID];
	$l = "<tr><td width=\"20\">
	<a href=\"admtemplateedit.php?path=$recno\">
	<img src=\"config/b_edit.png\" width=\"16\" height=\"16\" alt=\"EDIT\"></a></td>";
	$l .= "<td width=\"20\">
	<a href=\"admtemplatemaint.php?delete=$recno\">
	<img src=\"config/b_drop.png\" width=\"16\" height=\"16\" alt=\"DELETE\" />
	</a></td><td>";
	//$l .= rtrim($t,".txt");
	$l .= "$t[Name]</td></tr>";
	echo $l;
	}
//echo "<input type=\"submit\" name=\"submit\" value=\"Submit\">";
echo "</form></table>";	

echo "Add new letter template";
echo "<form action=\"admtemplatemaint.php\" method=\"post\">";
echo "<input type=\"text\" name=\"addltr\" value=\"\" style=\"width: 400px; \" />";
echo "<input type=\"submit\" name=\"submit\" value=\"Submit\">";
echo "</form>";	
print <<<notePart
Please note that the name provided when creating a new either template will appear alphabetially in the drop down list.  Additionally, the name given to the email template will appear as the default subject line in the email developed for sending as a reminder.
notePart;

echo "</div>";

?>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>
