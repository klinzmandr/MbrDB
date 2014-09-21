<!DOCTYPE html>
<html>
<head>
<title>Member Correspondence</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/datepicker3.css" rel="stylesheet">
</head>
<body onLoad="initForm(this)" onChange="flagChange()">
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>

<?php
session_start();
include 'Incls/seccheck.inc';
//include 'Incls/vardump.inc';
//include 'Incls/mainmenu.inc';
include 'Incls/datautils.inc';

$mcid = $_SESSION['ActiveMCID'];
echo "<div class=container>";
if ($mcid == "") {
	print <<<corrInfo
<h4>Correspondence Log</h4> 
<p>This page will display all information about correspondence to the member/contact.  The MCID used is the &quot;active&quot; MCID selected by using the MCID entered or selected via the MCID Lookup function of the main menu.  An MCID will remain &quot;active&quot; until another is selected using the Lookuup function or the Home page is selected.</p>
	<h4>Special Note the Correspondence Log</h4>
	<p>The Correspondence Log is used to note all contact with the each member. This will provide an historical record concerning the relationship between the funding entity and the organization.</p>
	<p>Special entries are automatically created when an MCID is determined to have an expired membership and reminder notices are generated.  Additionally, a special entry is made when a &apos;dues&apos; payment has been entered.</p>
<script src="jquery.js"></script> <script src="js/bootstrap.min.js"></script></body></html>
corrInfo;
exit();
	}

//add new record
if ($_REQUEST['action'] == "add") {
	$sql = "SELECT * FROM `correspondence` 
		WHERE `CorrespondenceType` = '**NewRec**'
			AND `MCID` = '$mcid';";
	$res = doSQLsubmitted($sql);
	$nbr_rows = $res->num_rows;
	if ($nbr_rows == 0) {
		$flds[CorrespondenceType] = '**NewRec**';				// corresondence type for new add
		$flds[DateSent] = date('Y-m-d'); 
		$flds[MCID] = $mcid;
		sqlinsert('correspondence', $flds);
		//echo "action is add<br>";
		}
	$_REQUEST['action'] = "edit";
	$_REQUEST['id'] = '';
	}

// edit record
$recid = $_REQUEST['id'];									// empty if new, record number if not
if ($_REQUEST['action'] == "edit") {
if ($recid == '') {
	$sql = "SELECT * FROM `correspondence` 
		WHERE `CorrespondenceType` = '**NewRec**'
		AND `MCID` = '$mcid';";
	$res = doSQLsubmitted($sql);
	}
//$nbr_rows = $res->num_rows;
else {
	$sql = "SELECT * FROM `correspondence` 
		WHERE `CORID`='$recid'";
	$res = doSQLsubmitted($sql);
	}
$row = $res->fetch_assoc();
$recno=$row['CORID'];$corrtype=$row['CorrespondenceType'];$datesent=$row['DateSent'];$mcid=$row['MCID'];$note=$row['Notes'];$source=$row['SourceofInquiry'];

print <<<formPart1
<script>
function anchorconfirm() {
	var r=confirm("Confirm this action by clicking OK."); 
	if (r==true) { return true; }
	return false;
	}
	
function initForm(form) {
// Initialize all form controls
  with (form.mcform) {
//		initRadio(ttaken,"$ttaken");
		initSelect(CorrespondenceType,"$corrtype");
		}
	}
	
function initSelect(control,value) {
// Initialize a selection list (single valued)
// alert("initSelect: control: " + control.length + ", value: " + value);
	if (value == "") return;
	for (var i = 0; i < control.length; i++) {
		if (control.options[i].value == value) {
			control.options[i].selected = true;
			break;
			}
		}
	}

var reason = "";
// validate entire form before submission to database
function validateForm(theForm) {
	//alert("validation entered");
	reason = "";
	reason += validateEmpty(theForm.CorrespondenceType);
	reason += validateNotes(theForm.Notes);    
	if (reason != "") {
  	alert("Some fields need attention:\\n\\n" + reason);
  	return false;
		}
	return true;
	}

function validateNotes(fld) {
	//alert("enter note validation");
	var progval = document.mcform.CorrespondenceType.value;
	var pos = progval.search("Other");
	var error = "";
	if ((pos >= 0) && (fld.value.length == 0)) {
		fld.style.background = '#F7645E';
		error = "Addition notes are required for Correspondence Type.\\n";
		return error;
		}
	fld.style.background = 'White';
	return error;
	}

function validateEmpty(fld) {
  var error = "";
  if (fld.value.length == 0) {
  	fld.style.background = '#F7645E';
  	if (reason == "") {
    	error = "Required field(s) have not been filled in.\\n" }
    	} 
    else {
    	fld.style.background = 'White';
    	}
    return error;  
	}
	
</script>
<div class="well">
<h4>RecNo: $recno  MCID: $mcid</h4>
<form action="mbrcorrespondence.php" method="get"  name="mcform" id="mcform" class="form-inline" onsubmit="return validateForm(this)">
<div class="row">
<div class="col-sm-3">
Corr. Type: <select name="CorrespondenceType" size="1">
<option value=""></option>
<option value="RenewalReminder">Renewal Reminder</option>
formPart1;
loaddbselect('CorrTypes');
print <<<formPart2
</select>
</div>  <!-- col-sm-3 -->
<div class="col-sm-3">Date Sent: <input type="text" name="DateSent" value="$datesent" data-provide="datepicker" id="dp1" data-date-format="yyyy-mm-dd" data-date-autoclose="true"/></div>
</div>  <!-- row -->
<div class="row">
<div class="col-sm-6">Note: <textarea name="Notes" rows="3" cols="80">$note</textarea></div>
</div>  <!-- row -->
<div class="row"><div class="col-sm-2">
<input type="hidden" name="action" value="apply">
<input type="hidden" name="MCID" value="$mcid">
<input type="hidden" name="id" value="$recno">
<button type="submit" form='mcform' class="btn-sm btn-primary">Update Record</button></div>
</form>
</div>  <!-- row -->
</div>  <!-- well -->
formPart2;
	}

// apply changes to corresondence record, add a new reminder record if value = "reminder"
if ($_REQUEST['action'] == "apply") {
	$recno = $_REQUEST['id'];
	//echo "action is edit for record number $recno<br>";
	$uri = $_SERVER['QUERY_STRING'];
	//echo "query string: $uri<br>";
	parse_str($uri, $vararray);
	//echo "<pre>"; print_r($vararray); echo "</pre>";
	unset($vararray[action]); unset($vararray[id]);
	$vararray[Notes] = stripslashes($vararray[Notes]);
	//echo "before update call - recno: $recno, mcid: $mcid<br>";
	if ($vararray['CorrespondenceType'] == 'RenewalReminder') {
		//echo "reminder flag seen<br>";
		$vararray['Reminders'] = 'RenewalReminder';		// set renewal notice flag in Reminders col as well
		}
	sqlupdate('correspondence', $vararray, "`CORID`='$recno'");
	// now update member record with latest info
	$mcid = $_REQUEST['MCID'];
	$memflds[LastCorrDate] = $vararray[DateSent];
	$memflds[LastCorrType] = $vararray[CorrespondenceType];
	sqlupdate('members', $memflds, "`MCID` = '$mcid'");
	}

// read db for all MCID donation records
$sql = "SELECT * 
	FROM `correspondence` 
	WHERE `MCID` = '$mcid' 
	ORDER BY `DateSent` DESC, `CORID` DESC";
$results = doSQLsubmitted($sql);

// multiple rows returned, list correspondence records
$results->data_seek(0);
echo "<h4>Correspondence Log for $mcid&nbsp;&nbsp; <a  class=\"btn btn-xs btn-primary\" href=\"mbrcorrespondence.php?action=add\" onclick=\"return anchorconfirm()\">(Add new record)</a></h4>";
echo "<table class=\"table\">";
echo "<tr><th>Edit</th><th>Corr. ID</th><th>Corr. Type</th><th>Date Sent</th><th>Source</th><th>Notes</th></tr>";
while ($row = $results->fetch_assoc()) {
	$corrid=$row['CORID'];$mcid=$row['MCID'];$corrtype=$row['CorrespondenceType'];$datesent=$row['DateSent'];$source=$row['SourceofInquiry'];$notes=$row['Notes'];
	if ($corrtype == "**NewRec**") { $corrtype = ""; }
	$imagelink = "<img src=\"config/b_edit.png\" width=\"16\" height=\"16\" alt=\"EDIT\" longdesc=\"EDIT\" />";
	if (stripos($corrtype,"renewalpaid") !== FALSE) {
		$imagelink = "";
		}
	print <<<bulletForm
<tr><td>
<a onclick="return chkchg()" href="mbrcorrespondence.php?action=edit&id=$corrid">
$imagelink</a></td>
<td>$corrid</td><td>$corrtype</td><td>$datesent</td><td>$source</td><td>$notes</td></tr>
bulletForm;
	}
echo "</table>";
echo "</div>";  // container

?>

</body>
</html>
