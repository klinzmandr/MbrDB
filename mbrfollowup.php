<!DOCTYPE html>
<html>
<head>
<title>Member Follow Up</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/datepicker3.css" rel="stylesheet">
</head>
<body onLoad="initForm(this)" onChange="flagChange()">
<?php
session_start();
//include 'Incls/vardump.inc';
include 'Incls/seccheck.inc';
include 'Incls/mainmenu.inc';
include 'Incls/datautils.inc';

$mcid = $_SESSION['ActiveMCID'];
echo "<div class=container>";

// apply changes to existing corresondence record and update member record as well
if ($_REQUEST['action'] == "apply") {
	$recno = $_REQUEST['id'];
	//echo "action is edit for record number $recno<br>";
	$uri = $_SERVER['QUERY_STRING'];
	//echo "query string: $uri<br>";
	parse_str($uri, $vararray);
	//echo "<pre> vararray "; print_r($vararray); echo "</pre>";
	unset($vararray[action]); unset($vararray[id]);
	$vararray[Notes] = stripslashes($vararray[Notes]);
	// echo "before update call - recno: $recno, mcid: $mcid<br>";
	sqlupdate('correspondence', $vararray, "`CORID` = '$recno'");	
	
	// now update member record with latest info
	$memflds[LastCorrDate] = date('Y-m-d');
	$memflds[LastCorrType] = $vararray[CorrespondenceType];
	sqlupdate('members', $memflds, "`MCID` = '$mcid'");
	$_REQUEST['action'] = "edit";
	}

//add new record
if ($_REQUEST['action'] == "") {
	$sql = "SELECT * FROM `correspondence` WHERE `CorrespondenceType` = '**NewRec**';";
	$res = doSQLsubmitted($sql);
	$nbr_rows = $res->num_rows;												
	if ($nbr_rows == 0) {															// add a new record unless one already exists
		$flds[CorrespondenceType] = '**NewRec**';				// corresondence type flag for new add
		$flds[DateSent] = date('Y-m-d'); 
		//$flds[MCID] = $_SESSION['ActiveMCID'];
		sqlinsert('correspondence', $flds);
		}
	$_REQUEST['action'] = "edit";
	}

// edit record
if ($_REQUEST['action'] == "edit") {
$sql = "SELECT * FROM `correspondence` WHERE `CorrespondenceType` = '**NewRec**';";
$res = doSQLsubmitted($sql);
$nbr_rows = $res->num_rows;
if ($nbr_rows == 0) {
	$recno = $_REQUEST['id'];
	$sql = "SELECT * FROM `correspondence` where `CORID`='$recno'";
	$res = doSQLsubmitted($sql);
	}
$row = $res->fetch_assoc();
$corrtype=$row['CorrespondenceType'];$datesent=$row['DateSent'];$note=$row['Notes'];$source=$row['SourceofInquiry'];
	}
$recno = $row[CORID];
//echo "sql: $sql<br>";
//echo '<pre> record '; print_r($row); echo '</pre>';
// show form
print <<<formPart1
<script type="text/javascript" src="js/nicEdit.js"></script>
<script type="text/javascript">
bkLib.onDomLoaded(function() {
	new nicEditor({buttonList : ['left', 'center', 'right', 'bold','italic','underline','indent', 'outdent', 'ul', 'ol', 'hr', 'forecolor', 
	'bgcolor','link','unlink']}).panelInstance('area1');
});
</script>
<h3>Add Follow Up Note for MCID: <a onclick="return chkchg()" href="MbrInfotabbed.php">$mcid</a></h3>
<p>Please initial all updates made. Click in text area to begin.</p>
<form action="mbrfollowup.php" method="get"  name="mcform" id="mcform" >
<div class="row">
<div class="col-sm-6">New Correspondence Note: 
<textarea id="area1" name="Notes" rows="15" cols="80">$note</textarea></div>
</div>  <!-- row -->
<div class="row"><div class="col-sm-2">
<input type="hidden" name="action" value="apply">
<input type="hidden" name="MCID" value="$mcid">
<input type="hidden" name="id" value="$recno">
<input type="hidden" name="CorrespondenceType" value="FollowUp">
<br><button type="submit" form='mcform' class="btn btn-larg btn-primary">Add Follow Up Record</button></div>
</form>
</div>  <!-- row -->
</div>  <!-- container -->
formPart1;

echo "</div>";  // container

?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>

</body>
</html>
