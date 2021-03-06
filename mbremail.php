<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Email Notification</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body onchange="flagChange()">

<?php
//include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
include 'Incls/mainmenu.inc.php';
include 'Incls/datautils.inc.php';


$tname = isset($_REQUEST['template']) ? $_REQUEST['template'] : "";
$mcid = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : $_SESSION['ActiveMCID'];
$_SESSION['ActiveMCID'] = $mcid;

echo "<div class=\"container\"><h3>Edit and Send the Message to <a href=\"mbrinfotabbed.php\">$mcid.</a></h3>";
//echo "This is the active MCID: " . $_SESSION['ActiveMCID'] . "<br>";
$sql = "SELECT * FROM `members` WHERE `Inactive` = 'FALSE' AND MCID = '$mcid'";
$res = doSQLsubmitted($sql);
//$res = readMCIDrow($mcid);
$rc = $res->num_rows;
if ($rc == 0) {
	echo '<h4>Member is inactive</h4>
	<p>Please update the member record before proceeding.</p>
	<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></div></body></html>';
	exit;
	}

$row = $res->fetch_assoc();
//echo "<pre>MCID record"; print_r($row); echo "</pre>";
$emaddr = $row['EmailAddress']; $emailok = $row['E_Mail'];
if (($emaddr == "") OR ($emailok == 'FALSE')) {
	echo "<h3>Member $mcid does not have any email addresses on file OR does not wish to get e-mail messages</h3>.<br />";
	//echo "<a class=\"btn btn-primary\" href=\"mbrinfotabbed.php\" name=\"filter\" value=\"$mcid\">CANCEL AND RETURN</a>";
	echo '</div>   <!-- containerx -->
<script src="jquery.js"></script><script src="js/bootstrap.min.js"></script></div></body></html>';
	exit;
	}

// we are good, read and prep edit form
// echo "emaddr: $emaddr<br />";
$em = $row['FName'] . " " . $row[LName] . " &lt;" . $emaddr . "&gt;";

$emh = $mcid . ':' . $em;
$fromaddr = $EmailFROM;		// defined in datautils.inc.php

print <<<formPart1
<script type="text/javascript" src="js/nicEdit.js"></script>
<script type="text/javascript">
bkLib.onDomLoaded(function() {
	var myNicEditor = new nicEditor({fullPanel : true});
	myNicEditor.setPanel('myNicPanel');
	myNicEditor.addInstance('area1');
});
</script>

<script>
function chkemail(form) {
	//alert("email validation seen");
	var subj = document.getElementById('subject').value.length;
	var body = document.getElementById('area1').value.length;
	if ((subj == 0) || (body < 10)) {
		alert("Subject and/or text body is empty.");
		return false;
		}
	return true;
	}
</script>
<br />
To: $em<br />
From: $fromaddr<br />
<br />
<form name="emf" class="form" action="mbremailsend.php" method="post" onsubmit="return chkemail(this)">
Subject:<br />
<input type="text" id="subject" name="subject" value="$templatename" style="width: 500; "  placeholder="Subject" /><br />
Message:<br />
<div id="myNicPanel" style="width: 750px;"></div>
<textarea id="area1" name="body" rows="10" cols="90"></textarea><br />
<input type="hidden" name="to" value="$emh">
<input type="hidden" name="from" value="$fromaddr">
<input type ="submit" name="Submit" value="Send"><br />
<input type="reset" name="reset" value="Reset Form" />
</form>
formPart1;

//echo "template file name: $templateaddress<br />";
//echo "<pre>template file "; print_r($template); echo "</pre>";

?>

</div>   <!-- containerx -->
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>
