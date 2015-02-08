<!DOCTYPE html>
<html>
<head>
<title>Member Donations</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/datepicker3.css" rel="stylesheet">
</head>
<body onLoad="initForm(this)" onChange="flagChange()">
<?php
session_start();
include 'Incls/seccheck.inc';
// include 'Incls/vardump.inc';
include 'Incls/mainmenu.inc';
include 'Incls/datautils.inc';

$mcid =   isset($_SESSION['ActiveMCID']) ? $_SESSION['ActiveMCID'] : '';
$mcidmemstatus =   isset($_SESSION['MemStatus']) ? $_SESSION['MemStatus'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
echo "<div class=container>";
if ($mcid == "") {
	print <<<fundingInfo
<h3>Funding Log</h3> 
<p>Funding log enteries provide a historical way to track financial activity for an organization.  This facility is used to provide an ongoing log of all financial contributions, regardless of type, and their sources.  Entries are made based on the currentl active MCID.  The MCID used is the &quot;active&quot; MCID selected by using the MCID entered or selected via the search function.  An MCID will remain &quot;active&quot; until another is selected using the filter or search page.</p>
<p>Over a period of time, this log will enhance the ability of the organization to know and understand their sources of income.</p>  
<h4>Special Note about the Funding Log</h4>
<p>There is a relationship between the Funding and Correspondence logs used to provide dues expiration reminders notices to MCID entities.  Reminder notices are created when a membership has lapsed.  This means that no membership payment noted as &apos;dues&apos; has been made within the annual membership period (the system default is 11 months).  When a payment is entered and designated to be for membership &apos;dues&apos;, an automatic entry is written to the correspondence log.  This will provide the system a notice the dues for that member has been paid and that the reminder for that specific MCID is no longer necessary.</p>
<p>Also, please note that any funding payment will cause the Inactive flag of the members record to be set to &apos;NO&apos; and set the Inactivedate to NULL.</p>
<script src="jquery.js"></script> <script src="js/bootstrap.min.js"></script></body></html>
fundingInfo;
exit();
	}

// delete existing donation record and, if for 'dues', its assoicated correspondence record
if ($action == "delete") {
	$recnbr = $_REQUEST['id']; $date = $_REQUEST['date'];
	//echo "Deletion action requested for $recno<br>";
  $qrysql = "SELECT * FROM `correspondence` WHERE `MCID` = '".$mcid."' AND `DateSent` = '".$date."' AND Reminders = 'RenewalPaid' limit 0,1;";		// use only the first occurance in case there were muliple for day
	//echo "correspondence deletion sql: $qrysql<br>";
	$corrdel = doSQLsubmitted($qrysql);
	$corr_rec = $corrdel->fetch_assoc();
	//echo "<pre>"; print_r($corr_rec); echo "</pre>";
	$corr_recno = $corr_rec[CORID];		// get the rec nbr for single record delete
	//echo "<h4>Funding record nbr: $recnbr and assoicated correspondence rececord nbr: $corr_recno have been deleted.</h4>";
	$delsql = "DELETE FROM `correspondence` WHERE CORID = '".$corr_recno."' AND `MCID` = '".$mcid."' AND `DateSent` = '".$date."' AND Reminders = 'RenewalPaid';";
	doSQLsubmitted($delsql);		// delete the associated correspondence record
	
	// now modify the donantion record to make it look like a new one
	$donarray[Purpose] = '**NewRec**';				// donation purpose for new add
	$donarray[Program] = '';
	//$donarray[Campaign] = '';
	//$donarray[DonationDate] = date('Y-m-d');	// today's date
	//$donarray[CheckNumber] = '';
	//$donarray[TotalAmount] = '';
	//$donarray[MembershipDonatedFor] = '';
	//$donarray[Note] = '';
	$donarray[MCID] = $mcid;
	sqlupdate('donations', $donarray, "`DonationID`='$recnbr'");
	$action = "edit";
	}

//add new record
if ($action == "add") {		// add new, empty donation record unless an empty one exists
	$sql = "SELECT * FROM `donations` 
		WHERE `Purpose` = '**NewRec**' 
			AND `MCID` = '$mcid';";
	$res = doSQLsubmitted($sql);
	$nbr_rows = $res->num_rows;
	//echo "nbr_rows from search for new rec: $nbr_rows<br>";
	if ($nbr_rows == 0) {
		$flds[Purpose] = '**NewRec**';				// donation purpose for new add
		$flds[Program] = '';
		$flds[DonationDate] = date('Y-m-d');	// today's date
		$flds[MCID] = $mcid;
		$rows = sqlinsert('donations', $flds);
		//echo "affected row count: $rows<br>";
		}
	$_REQUEST['id'] = '';
	$_REQUEST['action'] = "edit";
	}

// edit record
$recid = $_REQUEST['id'];									// empty if new, record number if not
if ($_REQUEST['action'] == "edit") {
	if ($recid == '') {
		$sql = "SELECT * FROM `donations` 
			WHERE `Purpose` = '**NewRec**' 
				AND `MCID` = '$mcid';";
		$res = doSQLsubmitted($sql);
		}
	else {
		$recno = $_REQUEST['id'];
		$sql = "SELECT * FROM `donations` 
			WHERE `DonationID`='$recno'";
		$res = doSQLsubmitted($sql);
		}
	$row = $res->fetch_assoc();
	$donid=$row['DonationID'];$mcid=$row['MCID'];$purpose=$row['Purpose'];
	$program=$row['Program']; $campaign=$row['Campaign'];
	$dondate=$row['DonationDate'];$chknbr=$row['CheckNumber'];
	$totamt=$row['TotalAmount'];$mbrdonatedfor=$row['MembershipDonatedFor'];
	$note=$row['Note'];
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
		initSelect(Purpose,"$purpose");
		initSelect(Program,"$program");
		initSelect(Campaign,"$campaign");
		if (Program.value == "") chgFlag += 1;
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
</script>

<script>	
var reason = "";
// validate entire form before submission to database
function validateForm(theForm) {
	// validate that 'dues' payment is for a member or volunteer
	var memstat = $mcidmemstatus;
	var pur = theForm.Purpose.value;
	if (pur == "Dues" && ((memstat == 0) || (memstat == 3))) {
		var r=confirm("A Dues payment is being entered for a supporter that is not a member or volunteer.\\n\\nEither the Purpose needs to be changed or the supporter record re-classified to be a 'Member' or 'Volunteer'. \\n\\nClick CANCEL and make corrections or OK to continue.");
		if (r==false) { return false; }
		var rr = confirm("Do you REALLY want to post a DUES payment for a non-member/volunteer?\\n\\nCLICK OK to confirm");
		if (rr == false) return false;
	}

	//alert("validation entered");
	reason = "";
	reason += validateEmpty(theForm.DonationDate);
	reason += validateEmpty(theForm.TotalAmount);
	reason += validateEmpty(theForm.Purpose);
	reason += validateEmpty(theForm.Program);
	reason += validateNote(theForm.Note);
	if (!validateprog(theForm.Program)) {
		return false;
		}
	if (theForm.TotalAmount.value == 0) {
		if (document.getElementById("NOTE").value == "") {
			theForm.Note.style.background = 'Pink';
			reason += "Any payment of $0.00 requires an explanatory note.";
			}
		}
	if (reason != "") {
		alert (reason);
		return false;
		}
	return true;
	}

// if program has "Other" then the note field is necessary to be entered
function validateNote(fld) {
	//alert("enter note validation");
	var progval = document.mcform.Program.value;
	var pos = progval.search("Other");
	var error = "";
	if ((pos >= 0) && (fld.value.length == 0)) {
		fld.style.background = '#F7645E';
		error = "Addition notes are required for Program.//n";
		return error;
		}
	fld.style.background = 'White';
	return error;
	}

function validateEmpty(fld) {
  var error = "";
  if (fld.value.length == 0) {
  	fld.style.background = '#F7645E';
    error = "Required field(s) have not been filled in.\\n" 
    return error;
    } 
	else {
		fld.style.background = 'White';
		}
	return "";
	}

function validateprog(fld) {
	//alert ("start validating program");
	var purpose = document.mcform.Purpose.value.substring(0,3).toLowerCase();
	var prog = fld.value.substring(0,3).toLowerCase();
	if (purpose != prog) {
		fld.value = "";
  	alert("Program selected invalid for Purpose designated.");
  	return false;
		}
	return true;
	}
</script>

<div class="well">
<h4>RecNo: $donid  MCID: $mcid</h4>
<form action="mbrdonations.php" method="get"  name="mcform" id="mcform" class="form-inline" onsubmit="return validateForm(this)">
<div class="row">
<div class="col-sm-3">
Purpose: <select name="Purpose" size="1">
<option value=""></option>
<option value="Dues">Dues</option>
formPart1;

loaddbselect('Purposes');

print <<<formPart2
</select>
</div>  <!-- col-sm-3 -->
<div class="col-sm-5">
Program: <select name="Program" size="1" onchange="validateprog(this)">
<option value=""></option>
formPart2;

loaddbselect('Programs');

print <<<formPart3
</select>
</div>  <!-- col-sm-5 -->
<div class="col-sm-4">
Campaign: <select name="Campaign" size="1">
formPart3;

loaddbselect('Campaigns');

print <<<formPart4
</select>
</div>  <!-- col-sm-4 -->
</div>  <!-- row -->

<div class="row">
<div class="col-sm-3">Don. Date:<br> 
<input type="text" name="DonationDate" value="$dondate" data-provide="datepicker" id="dp1" data-date-format="yyyy-mm-dd" data-date-autoclose="true"/></div>
<div class="col-sm-3">ChkNbr:<br><input style="width: 100px; " placeholder="Check Number" type="text" name="CheckNumber" value="$chknbr"></div>
<div class="col-sm-3">Amount:<br><input style="width: 100px; " placeholder="Amount" type="text" name="TotalAmount" value="$totamt"></div>
<div class="col-sm-3">Donated For:<br><input placeholder="Donated For" type="text" name="MembershipDonatedFor" value="$mbrdonatedfor"></div>
<div class="col-sm-6">Note:<br><textarea id="NOTE" name="Note" rows="3" cols="80">$note</textarea></div>
</div>  <!-- row -->
<div style="text-align: center">
<input type="hidden" name="action" value="apply">
<input type="hidden" name="id" value="$donid">
<button type="submit" form='mcform' class="btn-larg btn-primary">Update Record</button></div>
</form>
</div>  <!-- well -->
formPart4;
	}

// apply changes
if ($action == "apply") {
	$recno = $_REQUEST['id'];
	//echo "action is edit for record number $recno<br>";
	$uri = $_SERVER['QUERY_STRING'];
	//echo "query string: $uri<br>";
	parse_str($uri, $vararray);
	//echo "<pre>"; print_r($vararray); echo "</pre>";
	$vararray[Note] = stripslashes($vararray[Note]);
	unset($vararray[action]); unset($vararray[id]);
	//echo "<pre>donation array"; print_r($vararray); echo "</pre>";
	$memflds = array();								// array for updates to member record
	$memflds[Inactive] = 'FALSE';				// make sure that member record is not inactive 
	$memflds[Inactivedate] = '';
	if ($vararray['Purpose'] == "Dues") {			// if dues paid, auto-insert record into corresp.
		$fields[CorrespondenceType] = 'RenewalPaid';
		//$fields[DateSent] = date('Y-m-d');
		$fields[DateSent] = $vararray[DonationDate];  // use date of donation record for matching searches
		$fields[MCID] = $mcid;
		$fields[Reminders] = 'RenewalPaid';
		$fields[Notes] = "auto-added on payment of dues";
		//echo "<pre>donations array"; print_r($vararray); echo "</pre>";
		//echo "<pre>correspondence array"; print_r($fields); echo "</pre>";
		sqlinsert('correspondence', $fields);
		
		// update fields of member row with date and amount of last dues paid		
		$memflds[LastDuesDate] = $vararray[DonationDate];
		$memflds[LastDuesAmount] = $vararray[TotalAmount];
		sqlupdate('members', $memflds, "`MCID` = '$mcid'");
		}
	// or update fields of member record of mcid with date and amount of other payment type
	else {
		$memflds[LastDonDate] = $vararray[DonationDate];
		$memflds[LastDonAmount] = $vararray[TotalAmount];
		$memflds[LastDonPurpose] = $vararray[Purpose];
		sqlupdate('members', $memflds, "`MCID` = '$mcid'");
		}
	//echo "before update call - recno: $recno, mcid: $mcid<br>";
	sqlupdate('donations', $vararray, "`DonationID`='$recno'");
	}


// always read db for all MCID donation records including new one if just added
$sql = "SELECT * FROM `donations` WHERE `MCID` = '$mcid' ORDER BY `DonationDate` DESC";
$results = doSQLsubmitted($sql);

// multiple rows returned, list donation records
$results->data_seek(0);
print <<<listHdr
<script>
function confirmDel() {
	var r=confirm("This will change this Dues payment record.\\n\\nConfirm by clicking OK.");	
	if (r == true) { return true; }
	return false;
	}
</script>
<h3>Funding Log for <a href="mbrinfotabbed.php" onclick="return (validateForm(mcform)&&chkchg())">$mcid</a>&nbsp;&nbsp;<a  class="btn btn-primary" href="mbrdonations.php?action=add" onclick="return anchorconfirm()">(Add new record)</a></h3>
<table class="table">
<tr><th>Edit</th><th>RecNo</th><th>Purpose</th><th>Program</th><th>Campaign</th><th>DonDate</th><th>ChkNbr</th><th>Amount</th><th>Donated For</th><th>Notes</th></tr>
listHdr;
$totdonations = 0;
while ($row = $results->fetch_assoc()) {
	$donid=$row['DonationID'];$mcid=$row['MCID'];$purpose=$row['Purpose'];
	$program=$row['Program']; $campaign = $row['Campaign'];
	$dondate=$row['DonationDate'];$chknbr=$row['CheckNumber'];
	$totamt=$row['TotalAmount'];$mbrdonatedfor=$row['MembershipDonatedFor'];
	$note=$row['Note'];
	$totdonations += $totamt;
	if ($purpose == "**NewRec**") $purpose = "";
	$frm = "<tr>";
	if ($purpose == "Dues") {
		$frm .= "<td><a onclick=\"return confirmDel()\" href=\"mbrdonations.php?action=delete&id=$donid&date=$dondate\"><img src=\"config/b_edit.png\" width=\"16\" height=\"16\" alt=\"DELETE\" longdesc=\"DELETE\" /></a></td>"; 
		}
	else {
  $frm .= "<td><a onclick=\"return chkchg()\" href=\"mbrdonations.php?action=edit&id=$donid\">
<img src=\"config/b_edit.png\" width=\"16\" height=\"16\" alt=\"EDIT\" longdesc=\"EDIT\" /></a></td>";
		}
  $frm .= "<td>$donid</td><td>$purpose</td><td>$program</td><td>$campaign</td><td>$dondate</td><td>$chknbr</td><td>$totamt</td><td>$mbrdonatedfor</td><td>$note</td></tr>";
  echo $frm;
	}
echo "</table>";
$totdonations = number_format($totdonations,2);
echo "<h3>Total of all donations: $$totdonations</h3>";
echo "</div>";  // container

?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>

</body>
</html>
