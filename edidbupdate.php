<!DOCTYPE html>
<html>
<head>
<title>EDI DB Update</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body onChange="flagChange(); setUpd();">
<script>
function setUpd() {
	document.getElementById("hdr3").style.color="Red";
	}
</script>
<?php
session_start();
//include 'Incls/vardump.inc.php';
include 'Incls/seccheck.inc.php';
include 'Incls/mainmenu.inc.php';
include 'Incls/datautils.inc.php';

$mcid = isset($_SESSION['ActiveMCID']) ? $_SESSION['ActiveMCID'] : "";
$field = isset($_REQUEST['field']) ? $_REQUEST['field'] : "";
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";

if ($action == "APPLY") {
	// update field provided for active MCID updating last updated date in  process
	//$flds[MCID] = $mcid;
	//$flds[NameLabel1stline] = $_REQUEST['namelabel1stline'];
	$flds[$field] = $_REQUEST['fldval'];
	//$flds[DateEntered] = $_REQUEST['doe'];
	$flds[LastUpdater] = $_SESSION['SessionUser'];
	$today = date('Y-m-d', strtotime(now));
	$flds[LastUpdated] = $today;
	//echo "<pre>"; print_r($flds); echo "</pre>";
	//echo "Update mcid $mcid, field $field<br>";
	
	sqlupdate('extradonorinfo', $flds, "`MCID` = '$mcid'");

	}

// read EDI info specified for active MCID
$sql = "Select * from `extradonorinfo` where `MCID` = '$mcid';";
$res = doSQLsubmitted($sql);
$r = $res->fetch_assoc();
$recid = $r[RecID];
$namelabel1stline = $r[NameLabel1stline];
$fld = $r[$field];
$doe = $r[DateEntered]; $dlu = $r[LastUpdated];
print <<<pagePart1
<div class="container">
<h3 id="hdr3">EDI Database Update</h3>
<h4>MCID: $mcid</h4>
<h4>Extra Donor Info Section: $field</h4>
<div class="well">
<!-- RecID: $recid<br>
Name: $namelabel1stline<br>
Field: <pre>$fld</pre>
Date Entered: $doe<br>
Last Updated: $dlu<br> -->
<form action="edidbupdate.php" method="post"  name="fldform" class="form" role="form">
<textarea name="fldval" rows="15" cols="90">$fld</textarea><br />
<input type="hidden" name="field" value="$field">
<input type="hidden" name="namelabel1stline" value="$namelabel1stline">
<input type="hidden" name="doe" value="$doe">
<input class="btn btn-danger" type="submit" name="action" value="APPLY">
</form>
<br /><br />
<a class="btn btn-primary" href="ediaddupdate.php" onclick="return chkchg()">RETURN</a>
</div>  <!-- container -->
pagePart1;

?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
