<!DOCTYPE html>
<html>
<head>
<title>Member Information</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body onLoad="initForm(this)" onChange="flagChange()">
<script src="Incls/datevalidation.js"></script>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>

<?php
session_start();
//include "Incls/vardump.inc";
include 'Incls/seccheck.inc';
include 'Incls/datautils.inc';
include 'Incls/createcitydd.inc';

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
	
include 'Incls/mainmenu.inc';

echo "<div class=container>";
$mcid = $_SESSION['ActiveMCID'];
$seclevel = $_SESSION['SecLevel'];
$action = $_REQUEST['action'];
if ($filterflag == "--none--") { 
	$m = "<p><b>Use of the MCID field</b></p><p>The MCID field is used to access and update member/contact informaton.  No MCID entered will provide access to a page to do a general search of the entire database.</p>
	<p>Click the <a href=\"mbrsearchlist.php\">general search</a> button and enter any string of characters to search the all or part of the first name, last name, label name, address, or email addresses of the entire database.  This will produce a listing of ALL records that contain the target string entered.</p>
	<p>When a target list of records is displayed, click the bullet at the left of the associated MCID to access the specific member's record./p>
	<p>Once a single member record has been accessed, its correspondence and fund information records will be available by clicking on the main menu at the top of the page.  That MCID will remain the 'active' until a new MCID is selected or you click the \"Home\" menu choice.</p>";
	echo "<h2>No MCID entered.</h2>";
	echo "<br />";
	echo "$m";
	echo "<h4><a class=\"btn btn-large btn-primary\" href=\"mbrsearchlist.php\" name=\"filter\" value=\"--none--\">General Search</a></h4></div>";
	exit;
	}

if (($action == "") AND ($mcid == "")) {
	$mcinfo = "<h3>Member/Contact Informaton</h3>"; 
	$mcinfo .= "<p>This page will display all information of the Member/Contact Id (MCID) selected using the MCID entered or selected via the &apos;Lookup&apos; function.  The MCID displayed will be used for the display of all correspondence and donations information as well.  It will remain \"active\" until another is selected by either returning to the Home page or by using the &apos;Lookup&apos; to select a new one.</p>";
	$mcinfo .= "<p><b>Use of the MCID field</b></p><p>The MCID field is used to access and update member/contact informaton.  No MCID entered will provide access to a page to do a general search of the entire database using the character string provided in the search input field.</p>
	<p>Enter any string of characters to search the all or part of the first name, last name, label name, address, or email addresses of the entire database.  This will produce a listing of ALL records that contain the target string entered.</p>
	<p>When a target list of records is displayed, click the MCID link to access the specific member's record.</p>
	<p>Once a single member record has been accessed all correspondence and funding information records will be available by clicking on the tab menu in the member information page.  That MCID will remain the 'active' until a new MCID is selected using the Lookup or Search functions or you click the \"Home\" menu choice.</p>";

	echo $mcinfo;
	exit;
	}

// if action is to update, get all fields supplied and write them to the database before reading
if ($action == "update") {
	$uri = $_SERVER['QUERY_STRING'];
	parse_str($uri, $vararray);
	if (array_key_exists('mlist',$vararray)) {
		$listarray = $vararray[mlist];						// get list array
		$liststring = implode(",",$listarray);		// create list string
		unset($vararray[mlist]);									// delete array
		$vararray[Lists] = $liststring;						// add back the string
		}
	else $vararray[Lists] = '';									// if none are checked -----
	unset($vararray[action]);										// unset page action indicator
	$vararray[Notes] = stripslashes($vararray[Notes]);
	$vararray[LName] = stripslashes($vararray[LName]);
	$vararray[NameLabel1stline] = stripslashes($vararray[NameLabel1stline]);
	$vararray[Organization] = stripslashes($vararray[Organization]);
	$where = "`MCID`='" . $mcid . "'";
	sqlupdate('members',$vararray, $where);	
	}

// get member record from ActiveMCID and display the info in update form

$sql = "SELECT * FROM `members` WHERE MCID = '$mcid'";
$res = doSQLsubmitted($sql);
//$res = readMCIDrow($mcid);
if ($res->num_rows == 0) {
	echo "<h3>No MCID record found.  Please retry.</h3><br /><br />";
	echo "<a class=\"btn btn-large btn-primary\" href=\"mbrsearchlist.php\" name=\"filter\" value=\"--none--\">General Search</a><br /><br />";
	echo "<a class=\"btn btn-large btn-primary\" href=\"index.php\">CANCEL AND RETURN</a><br /><br />";
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
$eaddr=$row['EmailAddress']; $city=$row['City']; $state=$row['State']; 
$zip=$row['ZipCode']; $priphone=$row['PrimaryPhone'];
$memstatus=$row['MemStatus'];$memdate=$row['MemDate'];
$mctype=$row['MCtype'];$inact=$row['Inactive'];$inactdate=$row['Inactivedate'];
$e_mail=$row['E_Mail'];$mail=$row['Mail']; $notes=$row['Notes'];$lists=$row[Lists];
$lastduesdate = $row[LastDuesDate]; $lastduesamount = $row[LastDuesAmount];
$lastdondate = $row[LastDonDate]; $lastdonpurpose = $row[LastDonPurpose]; 
$lastdonamount = $row[LastDonAmount];
$lastcorrdate = $row[LastCorrDate]; $lastcorrtype = $row[LastCorrType];
$citieslist = createddown();
echo "<h3>Member Information for ". $lab1line . '('.$mcid.')</h3>';
print <<<pagePart1
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
	//reason += validatePhone(theForm.phone);
	//reason += validateEmpty(theForm.from);
	//reason += validateLists();    
	if (reason != "") {
		var r=confirm("Highlighted fields need attention.\\n\\nClick Cancel to correct.\\n\\nClick OK to Continue.");	
		if (r == true) { return true; }
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
    	error = "Required field(s) have not been filled in.\\n" }
    	} 
    else {
    	fld.style.background = 'White';
    	}
    return error;  
	}
	
<!-- Function to prevent user from using the 'Enter' key when cursor in a text field. -->
function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target)?evt.target:((evt.srcElement)?evt.srcElement:null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
	}

<!-- Does not allow use of Enter key when filling out a form -->
<!-- document.onkeypress = stopRKey; -->

function initForm(theDoc) {
	clearFilter(theDoc.filter);
	initAllFields(theDoc.mcform);
	return true;
	}

function clearFilter(theForm) {
	theForm.filter.value = "";
	return true;
	}
	
function initAllFields(form) {
// Initialize all form controls
  with (form) {
//		initRadio(ttaken,"$ttaken");
		initRadio(MemStatus,"$memstatus");
		initSelect(MCtype,"$mctype");
		initRadio(E_mail,"$e_mail");
		initRadio(Mail,"$mail");
		initRadio(Inactive,"$inact");
  	}
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
	}

// if email address defined assume it is ok to send email
function setupemailok() {
	if (document.getElementById("EMA").value != "") {
		document.getElementById("EMR1").checked = true;
		document.getElementById("EMR2").checked = false;
		}
	else {
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

function initRadio(control,value) {
//alert("initRadio");
// Initialize a radio button
	for (var i = 0; i < control.length; i++) { 
		if (control[i].value == value) {
			control[i].checked = true;
			break;
		}
	}
}

function setflds(theForm) {
	//alert("entered");
	var ffld = theForm.FName.value;
	var lfld = theForm.LName.value;
	//alert("ffld: "+ffld+", lfld: "+lfld);
	theForm.NameLabel1stline.value = ffld + " " + lfld;
	theForm.CorrSal.value = ffld;
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
<div style="text-align: center"><button type="submit" form='mcform' class="btn btn-primary">Update Member</button></div>

<!-- Tab definition header  -->
<ul id="myTab" class="nav nav-tabs">
  <li class="active"><a href="#home" data-toggle="tab">Main</a></li>
  <li class=""><a href="mbrdonations.php" onclick="return chkchg()">Funding</a></li>
  <li class=""><a href="mbrcorrespondence.php" onclick="return chkchg()">Correspondence</a></li>

pagePart1;
// show lists tab if member is a volunteer
// or an admin because admins can see everything!
//if (($memstatus == 2) OR ($seclevel == 'admin')) 
//	echo '<li class=""><a href="#lists" data-toggle="tab">Lists</a></li>';

print <<<pagePart2
 	<li class=""><a href="#lists" data-toggle="tab">Lists</a></li>
	<li class=""><a href="#summary" data-toggle="tab">Summary</a></li>
	<li class=""><a href="mbrfollowup.php" onclick="return chkchg()">Follow Up</a></li>
</ul>
<!-- Tab 1 Demographic information -->
<div id="myTabContent" class="tab-content">
<div class="tab-pane fade active in" id="home">
<div class="well">
<h4>Contact Information</h4>
<div class="row">
<div class="col-sm-4">First: <input placeholder="First Name" autofocus type="text" name="FName" value="$fname" onchange="setflds(document.mcform)"></div>
<div class="col-sm-4">Last: <input placeholder="Last Name" type="text" name="LName" value="$lname" onchange="setflds(document.mcform)"></div>
</div>

<div class="row">
<div class="col-sm-4">Label Line: <input id="NL1" placeholder="Label Line" name="NameLabel1stline" value="$lab1line"></div>
<div class="col-sm-5">Corr. Sal:<input placeholder="Correspondence Salutation" name="CorrSal" value="$corrsal"></div>
</div>
<div class="row">
<div class="col-sm-4">Org: <input placeholder="Organization" name="Organization" value="$org"></div>
<div class="col-sm-4">Addr Line: <input id="ALN" placeholder="Address Line" name="AddressLine" value="$addr" onchange="setupaddrok()"></div>
</div>
<div class="row">
<div class="col-sm-4">City: <input id="CI" placeholder="City" name="City" value="$city" onblur="loadcity()"  autocomplete="off" ></div>
<div class="col-sm-2">State: <input id="ST" placeholder="State	" type="text" name="State" value="$state" style="width: 50px; " /></div>
<div class="col-sm-3">Zip: <input id="ZI" type="text" name="ZipCode" value="$zip" size="10" maxlength="10" style="width: 100px;" placeholder="Zip" onchange="validatezipcode(this)"/></div>
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
var citylist = $citieslist;
$('#CI').typeahead({source: citylist})

</script>

<div class="row">
<div class="col-sm-4">Phone: <input type="text" name="PrimaryPhone" value="$priphone" size="12" maxlength="12" style="width: 125px;" onchange="return ValidatePhone(this)"  placeholder="Primary Phone" /></div>

<div class="col-sm-4">Email: <input id="EMA" placeholder="Email" style="width: 200px;" name="EmailAddress" value="$eaddr" onchange="setupemailok()"></td></tr></div>
</div>
<div class="row">
<div class="col-sm-12">Notes:<textarea name="Notes" rows="2" cols="60">$notes</textarea></div>
</div>  <!-- row -->
</div>  <!-- well -->
<!-- </div>  tab pane -->

<!-- Tab 2 membership information -->
<!-- <div class="tab-pane fade" id="detail"> -->
<div class="well">
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
pagePart2;
loaddbselect('MCTypes');
print <<<pagePart3
</select>
<!-- Mbr Type:<input placeholder="MC TYpe" name="MCtype" value="$mctype"> -->
</div>  <!-- col-sm-5 -->
</div>  <!-- row -->
<div class="row">
<div class="col-sm-3">
<!-- Date Joined:<input onchange="ValidateDate(this)" placeholder="YYYY-MM-DD" name="MemDate" value="$memdate" style="width: 100px;"> -->
Date Joined: $memdate
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
<div class="col-sm-3">Mbr Inactive?: 
<input onclick="setInactiveDate()" type="radio" name="Inactive" value="TRUE" />Yes
<input onclick="clearInactiveDate()" type="radio" name="Inactive" value="FALSE" />No
</div>
<div class="col-sm-4">Date Inactive: <input placeholder="Date Inactive" name="Inactivedate"  onchange="ValidateDate(this)" value="$inactdate"></div>
</div>
</div>  <!-- well -->
</div> <!-- tab 1 pane -->

<!-- tabs 2 & 3 are new pages -->

pagePart3;
echo '
<!-- Tab 5 Email lists (hidden unless memstatus == 2) -->
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
</div>  <!-- tab pane -->

<!-- Tab 6 Summary of member dues/correspondence -->';
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
<a class=\"btn btn-xs btn-primary\" onclick=\"return chkem()\" href=\"mbremail.php?tname=x\">SEND AN EMAIL</a> &nbsp;&nbsp;
<a class=\"btn btn-xs btn-primary\" href=\"mbrsendreceipt.php\" onclick=\"return chkem()\">SEND A RECEIPT</a>&nbsp;&nbsp;
<a class=\"btn btn-xs btn-primary\" onclick=\"return chkem()\" href=\"mbremailnotice.php\">SEND A REMINDER EMAIL</a>&nbsp;&nbsp;
<a class=\"btn btn-xs btn-primary\" onclick=\"return chkem()\" href=\"mbrnotice.php\">PRINT A REMINDER LETTER</a>
</div>
<br />";
echo '<table border=0 class="table"><tr>';

//Funding column
echo "<td><b>Funding</b><br />
&nbsp;&nbsp;&nbsp;&nbsp;Last Dues Date:  $lastduesdate<br />
&nbsp;&nbsp;&nbsp;&nbsp;Last Dues Amount: $$lastduesamount<br /><br>
&nbsp;&nbsp;&nbsp;&nbsp;Last Non-dues Purpose: $lastdonpurpose<br /> 
&nbsp;&nbsp;&nbsp;&nbsp;Last Non-Dues Date: $lastdondate<br />
&nbsp;&nbsp;&nbsp;&nbsp;Last Non-Dues Amount: $$lastdonamount<br /><br />";

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
	<a class=\"btn btn-xs btn-primary\" href=\"mbrprintmcid.php\">Print MCID Summary Report</a></div>
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
