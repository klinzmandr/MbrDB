<!DOCTYPE html>
<html>
<head>
<title>Template Editor</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<div class="container">
<h3>Template Editor</h3>
<a class="btn btn-primary" href="admtemplatemaint.php">CANCEL AND RETURN</a><br /><br />
<?php
session_start();
//include 'Incls/vardump.inc';
include 'Incls/seccheck.inc';
include 'Incls/datautils.inc';

$path = $_REQUEST['path'];
$sql = "SELECT * FROM `templates` WHERE `TID` = '$path';";
//echo "sql: $sql<br />";
$res = doSQLsubmitted($sql);
$t = $res->fetch_assoc();
//echo "<b>Title:</b><br />";
$name = stripslashes($t[Name]);
$body = stripslashes($t[Body]);
print <<<form
<script type="text/javascript" src="js/nicEdit.js"></script>
<script type="text/javascript">
bkLib.onDomLoaded(function() {
	new nicEditor({buttonList : ['fontSize', 'fontFormat', 'left', 'center', 'right', 	'bold','italic','underline','indent', 'outdent', 'ul', 'ol', 'hr', 'forecolor', 
	'bgcolor','link','unlink']}).panelInstance('area1');
});
</script>

<form action="admtemplatemaint.php" method="post">
<b>Title:</b><br />
<input type="text" name="name" value="$name" style="width: 600px; " /><br /><br />
<b>Body:</b><br />
<textarea id="area1" name="body" rows="20" cols="90">$body</textarea><br />
<input type="hidden" name="path" value="$path">
<input type="hidden" name="upd" value="upd">
<input type="submit" name="submit" value="Submit">

form;
?>
<br /><br />
<!-- <a class="btn btn-primary" href="admtemplatemaint.php">CANCEL AND RETURN</a><br /> -->
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>
