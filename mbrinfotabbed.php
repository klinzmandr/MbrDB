<!DOCTYPE html>
<html>
<head>
<title>Member Information</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="Incls/datevalidation.js"></script>

<?php
session_start();
//include "Incls/vardump.inc.php";
include 'Incls/seccheck.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/createcitydd.inc.php';

$filter = isset($_REQUEST['filter']) ? $_REQUEST['filter']: "";
$filter = $filterflag = rtrim($filter);
if ($filter == "--none--") {
	$filter = "";
	unset($_SESSION['ActiveMCID']);
	unset($_REQUEST['filter']);
	}
if ($filter != "") {
	$_SESSION['ActiveMCID'] = $filter;
	}
	
include 'Incls/mainmenu.inc.php';

echo "<div class=container>";
$mcid = $_SESSION['ActiveMCID'];
$seclevel = $_SESSION['SecLevel'];
$action = $_REQUEST['action'];
// if action is to update, get all fields supplied and write them to the database before reading
if ($action == "update") {
	$uri = $_SERVER['QUERY_STRING'];
	parse_str($uri, $vararray);
	// echo '<pre> input uri '; print_r($vararray); echo '</pre>';
  // adding saftey check to make sure MCID from input page is same as ActiveMCID
  // MCIDx is from the input form of the update page.
	if ($_REQUEST['MCIDx'] != $_SESSION['ActiveMCID']) {
	  echo 'MCIDx: '.$_REQUEST['MCIDx']. ', ActiveMCID: '.$_SESION['ActiveMCID'].'<br>'; 
	  echo "<h2 style=\"color: red; \">ERROR: MCID mismatch!!!</h2>
	  <b>If this error occurs please note the actions being taken immediately prior to
	  seeing this message and notify dave.klinzman@yahoo.com immediately. Please  
	  provide this information and any other notes along with the MCID's involved.</b><br>";
	  $log = 'XUpdate Error. MCIDx: '. $_REQUEST['MCIDx'] . ', ActiveMCID: '. $_SESSION['ActiveMCID'].'<br>';
	  addlogentry($log);                       // log the error
	  $log = 'SESSION ' . var_export($_SESSION, TRUE);
	  addlogentry($log);
	  $log = 'REQUEST ' . var_export($_REQUEST, TRUE);
	  addlogentry($log);	                         // log the sesssion variables
	  unset($_SESSION['ActiveMCID']);       // force new lookup for MCID
	  exit;
    }

	if (array_key_exists('mlist',$vararray)) {
		$listarray = $vararray[mlist];						// get list array
		$liststring = implode(",",$listarray);		// create list string
		unset($vararray[mlist]);									// delete array
		$vararray[Lists] = $liststring;						// add back the string
		}
	else $vararray[Lists] = '';									// if none are checked -----
	unset($vararray[action]);										// unset page action indicator
	unset($vararray[MCIDx]);                     // unset MCID field 
  //  echo '<pre> input after uri '; echo "mcid: $mcid, "; print_r($vararray); echo '</pre>';

	$vararray[Notes] = stripslashes($vararray[Notes]);
	$vararray[LName] = stripslashes($vararray[LName]);
	$vararray[NameLabel1stline] = stripslashes($vararray[NameLabel1stline]);
	$vararray[Organization] = stripslashes($vararray[Organization]);
  $where = "`MCID`='" . $mcid . "'";
	sqlupdate('members',$vararray, $where);
  echo '	
  <h3 style="color: red; " id="X">Update Completed.</h3>';
	
	}

// get member record from ActiveMCID and display the info in update form
$sql = "SELECT * FROM `members` WHERE MCID = '$mcid'";
$res = doSQLsubmitted($sql);
//$res = readMCIDrow($mcid);
if ($res->num_rows == 0) {
	echo "<h3>No MCID record is currently active.</h3><br /><br />";
	echo "<a class=\"btn btn-large btn-primary\" href=\"mbrsearchlist.php\" name=\"filter\" value=\"--none--\">General Search</a><br /><br />";
	unset($_SESSION['ActiveMCID']);    // invalid MCID
	exit;
	} 
// get row data from result
$res->data_seek(0);
$row = $res->fetch_assoc();
//echo '<pre> MCID'; print_r($row); echo '</pre>';
// get data values from sql query result
$_SESSION['MemStatus'] = $row[MemStatus];		// set memstatus for mbrdonations check
$mcid=$row['MCID'];  $fname=$row['FName']; $lname=$row['LName'];
$org=$row['Organization']; $addr=$row['AddressLine']; 
$lab1line=$row['NameLabel1stline']; $corrsal=$row['CorrSal']; 
$eaddr=$row['EmailAddress']; $eaddr2=$row['EmailAddress2'];
$city=$row['City']; $state=$row['State']; 
$zip=$row['ZipCode']; $priphone=$row['PrimaryPhone'];
$memstatus=$row['MemStatus'];$memdate=$row['MemDate'];
$mctype=$row['MCtype'];$inact=$row['Inactive'];$inactdate=$row['Inactivedate'];
$e_mail=$row['E_Mail'];$mail=$row['Mail']; $notes=$row['Notes'];$lists=$row[Lists];
$lastduesdate = $row[LastDuesDate]; $lastduesamount = $row[LastDuesAmount];
$lastdondate = $row[LastDonDate]; $lastdonpurpose = $row[LastDonPurpose]; 
$lastdonamount = $row[LastDonAmount];
$lastcorrdate = $row[LastCorrDate]; $lastcorrtype = $row[LastCorrType];
$citieslist = createddown();
?>

<h3>Member Information for <?=$lab1line?> (<?=$mcid?>)
&nbsp;&nbsp;
<span id="helpbtn" title="Help" class="glyphicon glyphicon-question-sign" style="color: blue; font-size: 20px"></span></h3>
<div id="help">
	<p><b>Use of the MCID field</b></p><p>The MCID field is used to access and update member/contact informaton.  No MCID entered will provide access to a page to do a general search of the entire database.</p>
	<p>Click the <a href="mbrsearchlist.php">general search</a> button and enter any string of characters to search the all or part of the first name, last name, label name, address, or email addresses of the entire database.  This will produce a listing of ALL records that contain the target string entered.</p>
	<p>When a target list of records is displayed, click the bullet at the left of the associated MCID to access the specific member's record.</p>
	<p>Once a single member record has been accessed, its correspondence and fund information records will be available by clicking on the main menu at the top of the page.  That MCID will remain the 'active' until a new MCID is selected or you click the &quot;Home&quot; menu choice.</p>
<h3>Member/Contact Informaton</h3>
<p>This page will display all information of the Member/Contact Id (MCID) selected using the MCID entered or selected via the &apos;Lookup&apos; function.  The MCID displayed will be used for the display of all correspondence and donations information as well.  It will remain \"active\" until another is selected by either returning to the Home page or by using the &apos;Lookup&apos; to select a new one.</p>
<p><b>Use of the MCID field</b></p><p>The MCID field is used to access and update member/contact informaton.  No MCID entered will provide access to a page to do a general search of the entire database using the character string provided in the search input field.</p>
	<p>Enter any string of characters to search the all or part of the first name, last name, label name, address, or email addresses of the entire database.  This will produce a listing of ALL records that contain the target string entered.</p>
	<p>When a target list of records is displayed, click the MCID link to access the specific member's record.</p>
	<p>Once a single member record has been accessed all correspondence and funding information records will be available by clicking on the tab menu in the member information page.  That MCID will remain the 'active' until a new MCID is selected using the Lookup or Search functions or you click the &quot;Home&quot; menu choice.</p>
</div>   <!-- help -->

<script>
var reason = "";
// validate entire form before submission to database
function validateForm(theForm) {
	//alert("validation entered");
	if (!validateLists()) return false;
	reason = "";
	reason += validateEmpty(theForm.FName);
	reason += validateEmpty(theForm.LName);
	reason += validateEmpty(theForm.NameLabel1stline);
	reason += validateCorrSal(theForm.CorrSal);
	reason += validateEmpty(theForm.AddressLine);
	reason += validateEmpty(theForm.City);
	reason += validateEmpty(theForm.State);
	reason += validateEmpty(theForm.ZipCode);
	//reason += validateEmpty(theForm.MemStatus);
	reason += validateEmpty(theForm.MCtype);
	//reason += validateEmpty(theForm.MemDate);
	//reason += validatePassword(theForm.pwd);
	reason += validateEmail(theForm.EmailAddress);
	//reason += validateEmail(theForm.EmailAddress2);
	//reason += validatePhone(theForm.phone);
	//reason += validateEmpty(theForm.from);
	//reason += validateLists();    
	if (reason != "") {
		var r=confirm("Highlighted fields need attention.\n\nClick OK to correct.\n\nClick CANCEL to update without corrections.");	
		if (!r == true) { return true; }
		return false;
  	}
	}

function validateLists() {
	var cnt = 0; var error = ""; 
	//var memstatus = $memstatus;
	var memstatus = document.getElementsByName("MemStatus"); 
	var fld = document.getElementsByName("mlist[]");
	for(var i=0; i < fld.length; i++) {
		if(fld[i].checked) cnt += 1; }
	if ((memstatus[2].checked) && (cnt == 0)) {				
		alert("A volunteer must be registered on at least one mailing list.\\n");
		return false;
		}
	return true;
	}

function validateEmail(fld) {
	var error = "";
//       document.getElementById("EMR1").checked = true;	
	if ((document.getElementById("EMR1").checked == true) && (fld.value.length == 0)) {
		fld.style.background = '#F7645E';
		error = "Email address needs to be supplied.\\n";
		return error;
		}
	fld.style.background = 'White';
	return error;
	}

function validateCorrSal(fld) {
  var error = "";
  if (fld.value.length == 0) {
  	fld.value = document.mcform.FName.value;
  	return error;
    	}
  return error;  
	}
function validateEmpty(fld) {
  var error = "";
  if (fld.value.length == 0) {
  	fld.style.background = '#F7645E';
  	if (reason == "") {
    	error = "Required field(s) have not been filled in.\n" }
    	} 
    else {
    	fld.style.background = 'White';
    	}
    return error;  
	}
	
$(document).ready(function() {
//Turn off submit on "Enter" key for a mbr form
$("#mcform").bind("keypress", function (e) {
  if (e.keyCode == 13) {    // enter key
    return false;
    }
  });

  $("#filter").val('');
  $("#MCT").val("<?=$mctype?>");      // init drop down
  $("[name=MemStatus]").val(["<?=$memstatus?>"]); // init all radios
	$("[name=E_mail]").val(["<?=$e_mail?>"]);
	$("[name=Mail]").val(["<?=$mail?>"]);
	$("[name=Inactive]").val(["<?=$inact?>"]);

	// if no email address defined
	if (document.getElementById("EMA").value == "") {
		document.getElementById("EMR1").checked = false;
		document.getElementById("EMR2").checked = true;
		}
	// if no address line defined
	if (document.getElementById("ALN").value == "") {
		document.getElementById("ALR1").checked = false;
		document.getElementById("ALR2").checked = true;
		}
	if (document.getElementById("NL1").value == "") {
		chgFlag += 1;						// flag that a update is needed on the form
		}
});

// if email address defined assume it is ok to send email
function setupemailok() {
  var em = $("#EMA").val();
  var emtest = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
  if (!emtest.test(em)) {
    $("#EMA").css("background", '#F7645E');
    alert("Email address entered is invalid.\n");
    return;
    }
  $("#EMA").css("background", 'white');

	if (document.getElementById("EMA").value != "") {
		document.getElementById("EMR1").checked = true;
		document.getElementById("EMR2").checked = false;
		}
	else {
	  document.getElementById("EMA2").value = "";
		document.getElementById("EMR1").checked = false;
		document.getElementById("EMR2").checked = true;
		}
	}

// if address line entered assume it is OK to send mail
function setupaddrok() {
	if (document.getElementById("ALN").value != "") {
		document.getElementById("ALR1").checked = true;
		document.getElementById("ALR1").value = "TRUE";
		document.getElementById("ALR2").checked = false;
		}
	else {
		document.getElementById("ALR1").checked = false;
		document.getElementById("ALR2").checked = true;
		document.getElementById("ALR2").value = "FALSE";
		}
	}

function setflds(theForm) {
	var labline = $("#FN").val() + " " + $("#LN").val();
	$("#NL1").val(labline.substring(0,24));
	$("[name=CorrSal]").val($("#FN").val());
	return;
	}

function checkmbr(fld) {
	var mctype = fld.value;
	var mcval = mctype.substring(0,1);
	//var memstatus = document.mcform.MemStatus;
	for (var i = 0; i < document.mcform.MemStatus.length; i++) {
     if (document.mcform.MemStatus[i].checked) {
    	memstatus = i;
    	break;
    	}
   	}
	if (mcval != memstatus) {
		fld.value = "";
		alert("Please select a Mbr Type that corresponds with the selected Mbr Status");
		return false;
		}
	return true;
	}

function chgmemstatus() {
	//alert("chg memstat entered");
	document.getElementById("MCT").options[0].selected=true;
	return true;
	}
function setInactiveDate(fld) {
	//alert("set inactive date entered");
	var d = new Date();
  var curr_date = d.getDate();
  var curr_month = d.getMonth() + 1; 
  var curr_year = d.getFullYear();
	document.mcform.Inactivedate.value = curr_year + "-" + curr_month + "-" + curr_date;
	return true;
	}

function clearInactiveDate(fld) {
	document.mcform.Inactivedate.value = '';
	}
</script>

<script>
function ValidatePhone(fld)  {
//alert("validation entered");
var errmsg = "";
var stripped = fld.value.replace(/[\(\)\.\-\ \/]/g, '');
if (stripped.length == 7)
	stripped = "805" + stripped;
if (stripped.length != 10) { 
	errmsg += "Invalid phone number.  Please include the Area Code.\\n";
	}
if(!stripped.match(/^[0-9]{10}/))  { 
	errmsg += "Non-numeric character entered.\\n";
	}
if (errmsg.length > 0) {
	errmsg += "\\nValid formats: 123-456-7890 or 123 456 7890 or 123-456-7890 or 1234567890";
	fld.style.background = '#F7645E';
	alert(errmsg);
	return false;
	}
var newval = stripped.substr(0,3) + "-" + stripped.substr(3,3) + "-" + stripped.substr(6,4);
fld.value = newval;
fld.style.background = 'White';
return true;
}
</script>

<script>
function validatezipcode(fld) {
	//alert("validation of zip code entered");
	var errmsg = "";
	var newval = "";
	var stripped = fld.value.replace(/[\(\)\.\-\ \/]/g, '');
	if (isNaN(stripped)) errmsg += "Non-numeric character entered.\\n";
	if (stripped.length == 5) newval = stripped.substr(0,5);
	else if (stripped.length == 9) newval = stripped.substr(0,5) + "-" + stripped.substr(5,4);
	else errmsg += "Zip code must be either 5 or 9 digits.\\n";
	if (errmsg.length > 0) {
		fld.value = "";
		errmsg += "Invalid Zip Code.  Please re-enter.\\n\\n"; 
		errmsg += "Valid formats are 12345 or 123456789 or 12345-6789\\n";
		alert(errmsg);
		return true;
		}
	fld.value = newval;
	return true;
	}
</script>

<form name="mcform" id="mcform" class="form-horizontal" role="form" onsubmit="return validateForm(this)">
<div style="text-align: center"><button type="submit" form='mcform' class="updb btn btn-primary">Update Member</button></div>
<!-- Tab definition header  -->
<ul id="myTab" class="nav nav-tabs">
  <li class="active"><a href="#home" data-toggle="tab">Main</a></li>
  <li class=""><a href="#funding" data-toggle="tab">Funding</a></li>
  <li class=""><a href="#corr" data-toggle="tab">Correspondence</a></li>


 	<li class=""><a href="#lists" data-toggle="tab">VolLists</a></li>
 	<li class=""><a href="#time" data-toggle="tab">VolTime</a></li>
	<li class=""><a href="#summary" data-toggle="tab">Summary</a></li>
	<li class="lvr"><a href="mbrfollowup.php" onclick="return chkchg()">Follow Up</a></li>
</ul>
<!-- Tab 1 Demographic information -->
<div id="myTabContent" class="tab-content">
<div class="tab-pane fade active in" id="home">
<div class="well">
<h4>Contact Information</h4>
<input type="hidden" name="MCIDx" value="<?=$mcid?>">
<div class="row">
<div class="col-sm-4">First: <input placeholder="First Name" autofocus type="text" id="FN" name="FName" value="<?=$fname?>" onchange="setflds(document.mcform)"></div>
<div class="col-sm-4">Last: <input placeholder="Last Name" type="text" id="LN" name="LName" value="<?=$lname?>" onchange="setflds(document.mcform)"></div>
</div>

<div class="row">
<div class="col-sm-4">Label Line: 
<input id="NL1" placeholder="Label Line" name="NameLabel1stline" maxlength="24" value="<?=$lab1line?>"></div>
<div class="col-sm-5">Corr. Sal:<input placeholder="Correspondence Salutation" name="CorrSal" value="<?=$corrsal?>"></div>
</div>
<div class="row">
<div class="col-sm-4">Org: <input placeholder="Organization" name="Organization" value="<?=$org?>"></div>
<div class="col-sm-4">Addr Line: <input id="ALN" placeholder="Address Line" name="AddressLine" value="<?=$addr?>" onchange="setupaddrok()"></div>
</div>
<div class="row">
<div class="col-sm-4">City: <input id="CI" placeholder="City" name="City" value="<?=$city?>" onblur="loadcity()"  autocomplete="off" ></div>
<div class="col-sm-2">State: <input id="ST" placeholder="State	" type="text" name="State" value="<?=$state?>" style="width: 50px; " /></div>
<div class="col-sm-3">Zip: <input id="ZI" type="text" name="ZipCode" value="<?=$zip?>" size="10" maxlength="10" style="width: 100px;" placeholder="Zip" onchange="validatezipcode(this)"/></div>
</div>
<script src="js/bootstrap3-typeahead.js"></script>
<script>
function loadcity() {
	//alert("loadcity");
	var cv = $("#CI").val();
	var cva = cv.split(",");
	$("#CI").val(cva[0]);
	$("#ST").val(cva[1]);
	$("#ZI").val(cva[2]);
	}
</script>

<script>
var citylist = <?=$citieslist?>;
$('#CI').typeahead({source: citylist})

</script>

<div class="row">
Phone: <input type="text" name="PrimaryPhone" value="<?=$priphone?>" size="12" maxlength="12" style="width: 125px;" onchange="return ValidatePhone(this)"  placeholder="Primary Phone" />
&nbsp;
Email: <input id="EMA" placeholder="Email" style="width: 200px;" name="EmailAddress" value="<?=$eaddr?>" onblur="setupemailok()"></td></tr>
&nbsp;
2nd Email: <input id="EMA2" placeholder="Alt Email" style="width: 200px;" name="EmailAddress2" value="<?=$eaddr2?>"></td></tr>
</div>

<div class="row">
<div class="col-sm-12">Notes:
<textarea name="Notes" rows="2" cols="60"><?=$notes?></textarea></div>
</div>  <!-- row -->

<!-- </div>  tab pane -->

<!-- Tab 2 membership information -->
<!-- <div class="tab-pane fade" id="detail"> -->
<h4>Membership Detail</h4>
<div class="row">
<div class="col-sm-7">
Mbr Status:&nbsp;
<input onchange="chgmemstatus()" type="radio" name="MemStatus" value="0" checked/>0-Contact
<input onchange="chgmemstatus()" type="radio" name="MemStatus" value="1" />1-Member
<input onchange="chgmemstatus()" type="radio" name="MemStatus" value="2" />2-Vol.
<input onchange="chgmemstatus()" type="radio" name="MemStatus" value="3" />3-Donor
</div>  <!-- col-sm-7 -->
</div>	<!-- row -->
<div class="row">
<div class="col-sm-5 col-sm-offset-1">
Mbr Type:<select id="MCT" name="MCtype" size="1" onChange="checkmbr(this)">
<option value=""></option>
<?php
loaddbselect('MCTypes');
?>
</select>
<!-- Mbr Type:<input placeholder="MC TYpe" name="MCtype" value="$mctype"> -->
</div>  <!-- col-sm-5 -->
</div>  <!-- row -->
<div class="row">
<div class="col-sm-4">
<!-- Date Joined:<input onchange="ValidateDate(this)" placeholder="YYYY-MM-DD" name="MemDate" value="$memdate" style="width: 100px;"> -->
Date Joined: <?=$memdate?>
</div>  <!-- col-sm-3 -->
<script>
function chkvalidemail(fld) {
	var val = fld.value;
	if (val === 'TRUE') {
		if (document.getElementById("EMA").value == "") {
			document.getElementById("EMR1").checked = false;
			document.getElementById("EMR2").checked = true;
			alert("NO Email Address Available!");
			}
		}
	return true;
	}
</script>
<script>
function chkvalidmail(fld) {
	var val = fld.value;
	if (val === 'TRUE') {
		if (document.getElementById("ALN").value == "") {
			document.getElementById("ALR1").checked = false;
			document.getElementById("ALR2").checked = true;
			alert("NO Mail Address Available!");
			}
		}
	return true;
	}
</script>
<div class="col-sm-3">Email OK?: 
<input id="EMR1" type="radio" name="E_mail" value="TRUE" onchange="return chkvalidemail(this)" />Yes
<input id="EMR2" type="radio" name="E_mail" value="FALSE" />No
</div>
<div class="col-sm-3">Mail OK?: 
<input id="ALR1" type="radio" name="Mail" value="TRUE" onchange="return chkvalidmail(this)" />Yes
<input id="ALR2" type="radio" name="Mail" value="FALSE" />No
</div>
</div>  <!-- row -->
<div class="row">
<div class="col-sm-4">Mbr Inactive?: 
<input onclick="setInactiveDate()" type="radio" name="Inactive" value="TRUE" />Yes
<input onclick="clearInactiveDate()" type="radio" name="Inactive" value="FALSE" />No
</div>
<div class="col-sm-5">Date Inactive: <input placeholder="Date Inactive" name="Inactivedate"  onchange="ValidateDate(this)" value="<?=$inactdate?>"></div>
</div>
</div>  <!-- well -->
</div> <!-- tab 1 pane -->

<?php
// ============= Tab 2 Funding List ======================
// always read db for all MCID donation records including new one if just added
$sql = "SELECT * FROM `donations` WHERE `MCID` = '$mcid' ORDER BY `DonationDate` DESC";
$results = doSQLsubmitted($sql);
$rc = $results -> num_rows;
$rowcount = 10;
if ($rc <= 10) $rowcount = $rc;
echo '
<div class="tab-pane fade" id="funding">
<div class="well">
<b>Funding Records (latest '.$rowcount.' of '.$rc.')</b>&nbsp;&nbsp;<a  class="btn btn-primary btn-xs lvr" href="mbrdonations.php">&nbsp;&nbsp;&nbsp;List&nbsp;All&nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;<a  class="btn btn-primary btn-xs lvr" href="mbrdonations.php?action=add">Add new record</a>';

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

<table class="table">
<tr><th>Edit</th><th>RecNo</th><th>Purpose</th><th>Program</th><th>Campaign</th><th>DonDate</th><th>ChkNbr</th><th>Amount</th><th>Donated For</th><th>Notes</th></tr>
listHdr;
$totdonations = 0; $counter = 0;
while ($row = $results->fetch_assoc()) {
  $counter += 1;
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
  if ($counter < 11) echo $frm;
	}
echo "</table>";
$totdonations = number_format($totdonations,2);
echo "<h3>Total of all donations: $$totdonations</h3>";
echo '</div>  <!-- well -->
</div>  <!-- tab pane -->';
// ======== Tab 3 Correspondence =============
$sql = "SELECT * FROM `correspondence` WHERE `MCID` = '$mcid' ORDER BY `DateSent` DESC, `CORID` DESC";
$results = doSQLsubmitted($sql);
$rc = $results -> num_rows;
$rowcount = 10;
if ($rc <= 10) $rowcount = $rc;

echo '
<div class="tab-pane fade" id="corr">
<div class="well">
<b>Funding Records (latest '.$rowcount.' of '.$rc.')</b>&nbsp;&nbsp;<a  class="btn btn-primary btn-xs lvr" href="mbrcorrespondence.php">&nbsp;&nbsp;&nbsp;List&nbsp;All&nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;<a  class="btn btn-primary btn-xs lvr" href="mbrcorrespondence.php?action=add">Add new record</a>';

// multiple rows returned, list correspondence records
$results->data_seek(0);
echo '<table class="table">
<tr><th>Edit</th><th>Corr. ID</th><th>Corr. Type</th><th>Date Sent</th><th>Source</th><th>Notes</th></tr>';
$counter = 0;
while ($row = $results->fetch_assoc()) {
  $counter += 1;
	$corrid=$row['CORID'];$mcid=$row['MCID'];$corrtype=$row['CorrespondenceType'];$datesent=$row['DateSent'];$source=$row['SourceofInquiry'];$notes=$row['Notes'];
	if ($corrtype == "**NewRec**") { $corrtype = ""; }
	$imagelink = "<img src=\"config/b_edit.png\" width=\"16\" height=\"16\" alt=\"EDIT\" longdesc=\"EDIT\" />";
	if (stripos($corrtype,"renewalpaid") !== FALSE) {
		$imagelink = "";
		}
	if ($counter <= 10) {
	print <<<bulletForm
<tr><td>
<a onclick="return chkchg()" href="mbrcorrespondence.php?action=edit&id=$corrid">
$imagelink</a></td>
<td>$corrid</td>
<td>$corrtype</td><td>$datesent</td><td>$source</td><td>$notes</td></tr>
bulletForm;
  }
}

echo '</table></div>  <!-- well -->
</div>  <!-- tab pane -->';

// ========== Tab 4 Vol Lists ================
echo '
<div class="tab-pane fade" id="lists">
<div class="well">
<h4>Volunteer Email Lists</h4>';
$text = readdblist('EmailLists');
$listkeys[AUL] = 'Active/Unlisted';
$listkeys += formatdbrec($text);
$listkeys[VolInactive] = 'Vol Inactive';

foreach ($listkeys as $k => $v) {
	//echo "key: $k, value: $v<br />";
	if (stripos($lists, $k) !== FALSE) {
		echo "<input type=\"checkbox\" name=\"mlist[]\" value=\"$k\" checked>$v<br>";
		}
	else {
		echo "<input type=\"checkbox\" name=\"mlist[]\" value=\"$k\">$v<br>";
		}
	//echo "key: $k, value: $v<br>";
	}
echo '</div>  <!-- well -->
</div>  <!-- tab pane -->';

echo '
<!-- Tab 5 Vol Time -->
<div class="tab-pane fade" id="time">
<div class="well">
<h4>Volunteer Time</h4>';

$sql = "SELECT * FROM `voltime` 
WHERE `MCID` = '$mcid' 
ORDER BY `VolDate` DESC;";
$res = doSQLsubmitted($sql);
$rowcnt = $res->num_rows;

if ($rowcnt > 0) {
echo "<b>Period Entry Count:</b> $rowcnt<br />";
// table: voltime: VTID,VTDT,MCID,VolDate,VolTime,VolMilage,VolCategory,VolNotes

while ($r = $res->fetch_assoc()) {
$trows[] = "<tr><td>$r[VolDate]</td><td>$r[VolTime]</td><td>$r[VolMileage]</td><td>$r[VolCategory]</td><td>$r[VolNotes]</td></tr>";
$vc = 'Uncategorized';
if (strlen($r[VolCategory]) > 0) $vc = $r[VolCategory];
$totalvolhrs += $r[VolTime];
$tothrs[$vc] += $r[VolTime];
$totmiles += $r[VolMileage];
	}
echo "<b>Total Miles Driven:</b> $totmiles,&nbsp;";
echo "<b>Total Volunteer Hours:</b> $totalvolhrs<br />";
echo "<b>Total Hours by Category:</b><br />";
if (count($tothrs) != 0) {
	foreach ($tothrs as $k => $v) echo "&nbsp;&nbsp;&nbsp;$k: $v<br />";
	}
echo "<b>Detail Records</b><br />";
echo '<table class="table table-condensed">';
echo '<tr><th width="15%">Date</th><th>Vol Time</th><th>Mileage</th><th>Category</th><th>Notes</th></tr>';
if (count($trows) != 0) foreach ($trows as $l) { echo $l; }
echo '</table>---- End of Report ----<br>';	
}
else {
	echo 'NO TIME RECORDS TO REPORT<br>';
}

echo '</div>  <!-- well -->
</div>  <!-- tab pane -->';

// =========== Tab 6 Summary of member dues/correspondence ===========
echo '<div class="tab-pane fade" id="summary">
<div class="well">';
//echo '<p>this will be a pane with the summary of the members donations, correspondence volunteer time and a link to the email send page.</p>';

// read and initialize from member record
echo '<script>
function chkem() {
var l = EmailAddr.length;
if (l == 0) {
	alert("No FROM email address configured.\nContact the system administrator.");
	return false;
	}
return true;
}
</script>';
echo "<div style=\"text-align: center\">
<a class=\"btn btn-xs btn-primary lvr\" onclick=\"return chkem()\" href=\"mbremail.php?tname=x\">SEND AN EMAIL</a> &nbsp;&nbsp;
<a class=\"btn btn-xs btn-primary lvr\" href=\"mbrsendreceipt.php\" onclick=\"return chkem()\">SEND A RECEIPT</a>&nbsp;&nbsp;
<a class=\"btn btn-xs btn-primary lvr\" onclick=\"return chkem()\" href=\"mbremailnotice.php\">SEND A REMINDER EMAIL</a>&nbsp;&nbsp;
<a class=\"btn btn-xs btn-primary lvr\" onclick=\"return chkem()\" href=\"mbrnotice.php\">PRINT A REMINDER LETTER</a>
</div>
<br />";
echo '<table border=0 class="table"><tr>';

//Funding column
echo "<td><b>Dues Funding</b><br />
&nbsp;&nbsp;&nbsp;&nbsp;Last Dues Date:  $lastduesdate<br />
&nbsp;&nbsp;&nbsp;&nbsp;Last Dues Amount: $$lastduesamount<br /><br>
<b>Last Non-Dues Funding:</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;Purpose: $lastdonpurpose<br /> 
&nbsp;&nbsp;&nbsp;&nbsp;Date: $lastdondate<br />
&nbsp;&nbsp;&nbsp;&nbsp;Amount: $$lastdonamount<br /><br />";

//Correspondence column
echo "<td><b>Correspondence</b><br />
&nbsp;&nbsp;&nbsp;&nbsp;Date of Last Corr: $lastcorrdate<br />
&nbsp;&nbsp;&nbsp;&nbsp;Last Correspondence Type: $lastcorrtype<br><br>";

// volunteer lists 
echo "<b>Volunteer Committees/Email List(s)</b><br />";
if (strlen($lists) == 0) {
	echo "&nbsp;&nbsp;&nbsp;&nbsp;==NONE=="; }
else {
	$liststr = readdblist('EmailLists');
	$listarray = formatdbrec($liststr);
	$vollists = explode(",", rtrim($lists));
//	echo '<pre>vol list '; print_r($vollists); echo '</pre>';
//	echo '<pre> vol cats '; print_r($listarray); echo '</pre>';
	foreach ($vollists as $v) {
		if (isset($listarray[$v])) echo "&nbsp;&nbsp;&nbsp;&nbsp;$listarray[$v]<br>";
		}
	}
	echo '</td></tr></table>';
	echo "<div style=\"text-align: center\">
	<a class=\"btn btn-xs btn-primary lvr\" href=\"mbrprintmcid.php\">Print MCID Summary Report</a></div>
	</div>  <!-- well -->
	</div>  <!-- tab-pane -->
	<!-- end all tab definitions -->
	</div>  <!-- tab content -->";

?>
<input type="hidden" name="action" value="update">
</form>
</div>
<hr></div><br /><br />
</body>
</html>
