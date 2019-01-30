<?php
session_start();
?>
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
//include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
include 'Incls/datautils.inc.php';

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
	var myNicEditor = new nicEditor({fullPanel : true});
	myNicEditor.setPanel('myNicPanel');
	myNicEditor.addInstance('area1');
});
</script>

<form action="admtemplatemaint.php" method="post">
<b>Title:</b><br />
<input type="text" name="name" value="$name" style="width: 600px; " />
<h3 style="color: #FF0000; ">NOTE: Do NOT Copy/Paste from a word processor!!</h3>
<b>Body:</b><br />
<div id="myNicPanel" style="width: 750px;"></div>
<textarea id="area1" name="body" rows="20" cols="90">$body</textarea><br />
<input type="hidden" name="path" value="$path">
<input type="hidden" name="upd" value="upd">
<input type="submit" name="submit" value="Submit">
</form>

form;
print <<<scPart
<h3>Shortcode values available</h3>
<p>Shortcode allows message customization by allowing the insertion of specific data items from the members record into the message. The following may be inserted anyplace in a message template. Please note the use of the square brackets and capitalization both of which are REQUIRED.</p>
For all types of messages:
<ul>
First line of label: [NameLabel1stline]<br>
Correspondence salutation: [CorrSal]<br>
First name: [FName]<br>
Last name: [LName]<br>
Address line: [AddressLine]<br>
City: [City]<br>
State: [State]<br>
Zip: [ZipCode]<br>
Org name: [Organization]<br>
Email: [EmailAddress]<br>
Today&apos;s date: [date]<br>

</ul>
For receipts only:
<ul>
[total]<br>
[itemcount]<br>
</ul
scPart;
?>
<br /><br />
<!-- <a class="btn btn-primary" href="admtemplatemaint.php">CANCEL AND RETURN</a><br /> -->
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>
