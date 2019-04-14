<?php
session_start(); 
?>
<html>
<head>
<title>MCID Addition</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>

<?php

unset($_SESSION['ActiveMCID']);

//include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/seccheck.inc.php';
include 'Incls/mainmenu.inc.php';

$action = $_REQUEST['action'];

if ($action == "add") {
	$mcid = $_REQUEST['mcid'];
	$fields['MCID'] = $_REQUEST['mcid'];
	$fields['MemStatus'] = 0;
	$fields['Inactive'] = 'FALSE';
	$fields['MemDate'] = date('Y-m-d');
	$fields['Source'] = 'MbrDB';
	$fields['LastDonDate'] = ''; $fields['LastDonPurpose'] = ''; 
	$fields['LastDonAmount'] = '0.00'; 
	$fields['LastDuesDate'] = ''; $fields['LastDuesAmount'] = '0.00'; 
	$fields['LastCorrDate'] = ''; $fields['LastCorrType'] = '';
	$lead3 = substr($mcid,0,3);
	$lead5 = substr($mcid,0,5);
	$mcidwild = substr($mcid,0,5) . '%';
	$target = substr($mcid,0,3) . '%';
	//echo "search string: $lead3<br />";
	$sql = "SELECT `MCID` from `members` WHERE `MCID` = '$mcid';";
	$res = doSQLsubmitted($sql);
	$rowcnt = $res->num_rows;
	//echo "row count from select: $rowcnt<br>";
	if ($rowcnt == 1) {
    echo "<h2>MCID &apos;$mcid&apos; already in use</h2>
    To enter an associated family members append an A, B, C or D to make it unique.<br>";
    $avail = array();
		for ($i = 0; $i<100; $i++) {				// create list of other possibles	
			$str= sprintf("%s%02d",$lead3,$i);
			$avail[$str] = 1;
			}
		//echo "available list: "; print_r($avail); echo '<br />';
		$sql = "SELECT `MCID` from `members` WHERE `MCID` LIKE '$target';";
		$res = doSQLsubmitted($sql);
		while ($r = $res->fetch_assoc()) {		// read search results
			//echo 'mcid from SELECT: '; print_r($r); echo '<br>';
			unset($avail[$r['MCID']]);			// delete from possibilities if already used
			}
		//print_r($avail);
		echo "<h3>MCID $lead3&apos;s available for use</h3>";
		// echo '<h3>List of available MCID&apos;s from the following list:</h3><br />
		echo '
		<div class="row">';
		$keys = array_keys($avail);
		//print_r($keys);
		$listsize = count($keys);
		//if ($listsize > 24) $listsize = 24; 
		//echo "listsize: $listsize<br />";
		for ($i = 0; $i<$listsize; $i++) {
			echo '<div class="col-sm-1">' . $keys[$i] . '</div>';
			}
		echo '</div>';   
		}
	else {
		$res = sqlinsert('members', $fields);
		if ($res !== FALSE) {
			//$_SESSION['ActiveMCID'] = $fields[MCID];
			echo "<h2>Add of MCID $mcid has been completed.</h2>";
			echo "<a href=\"mbrinfotabbed.php?filter=".$fields['MCID']."\"><h3>Click to Complete MCID Info Entry</h3></a>";
			exit;
			}
		echo "<h2>Add unsuccessful!  Try another MCID.</h2>";
		}
	}
?>

<script>
$(document).ready(function() {
  $("#mcid").change(function() {
    chkmcid();
  });
});

function chkmcid() {
  var mcid = $("#mcid").val();    	
  mcid = mcid.toUpperCase();
  $("#mcid").val(mcid);
  // mcid pattern: 3 chars a-z, 2 digits 0-9, optonal suffix a-f
  if (mcid.length == 5)
   var mcidstr = new RegExp(/^([A-Z]{3})([0-9]{2})/g);
  if (mcid.length == 6)
   var mcidstr = new RegExp(/^([A-Z]{3})([0-9]{2})([A-D])/g);
  if ((mcid.length < 5) || (mcid.length >6)) {
  	document.addmbr.mcid.focus(); 
  	alert("Please enter the MCID as 3 characters plus 2 digits."); 
  	return false;	
  	}  	  
  var tf = mcidstr.test(mcid);   // test if 5 or 5+1
  if (tf) {
  	return true;	
  	}
  alert("Proposed MCID id not properly constructed.\n\nIt must be either a 5 or 6 character string as documented in the help section.\n\nAppended 6th character may only be A, B, C or D.");
  return false;
}


function confirmadd() {
  var mcid = $("#mcid").val().toUpperCase();
	var r=confirm("Adding MCID "+mcid+" to database.\n\nConfirm by clicking OK.");	
	if (r == true) { return true; }
	return false;
	}
</script>
<div class="container">
<h2>Adding A New Member</h2>  
<button id="helpbtn">Info about adding new supporters</button>
<div id="help">
<p>This function is to add a new member/contact record to the database.</p>
<p>This requires that a unique 5 character Member/Contact Identifier (MCID) be proposed to be used to identify that member.</p>
<p>The MCID is comprise of 3 alphabetic letters (usually the first three letters of the name of the member or organizational name or any organizational acronym) plus 2 numeric digits (usually the first 2 digits of the members street address or the last 2 digits of their phone number.)  This combination provides a predictable method that facilitates easy lookup for future reference.</p>
<p>In the case that a &quot;family&quot; membership needs to have multiple family members entered (usually for the purpose of volunteer hours accounting) the letters A through D can be appended to the MCID to create an associated record.  This associated record should only be used for entry of volunteer hours and the dues payments for the volunteer named.  Its mailing address should be marked as &quot;NO&quot; to prevent duplicate labels and mailings. The email address is required for volunteer communications.  All financial type entries (donataions, etc.) should be enered using the main supporter record.</p>
<p>Entry of the proposed MCID requires that the format of 3 letters and 2 digits are enforced.  Occasionally this will result in duplication of an existing MCID.  A list of alternative MCID&apos;s are provided in the event that the the one proposed is already in use.  A list of those in use is provided for information.  A list is also provided of those available to use.</p>
<p>Once you have successfully added a new record with a unique MCID for the member you will be presented with a blank input page where the remaining information about the member may be entered.</p>
<br />
</div>    <!-- help -->
<!-- <a class="btn btn-primary" href="mbrinfotabbed.php">CANCEL ADDITION</a><br /><hr> -->
<h4>Provide the EXACT MCID proposed for the new member record to be added:</h4>
<div class="row">
<div class="col-lg-4">
<form action="mbraddition.php" name="addmbr" onsubmit="return chkmcid()">
MCID: <input id="mcid" type="text" name="mcid" value="" >
<input type="hidden" name="key" value="MCID">
<input type="hidden" name="action" value="add">
<input type="submit" name="submit" value="Add MCID" onclick="return confirmadd()">
</form>
</div>   <!-- col -->
</div>  <!-- row -->
</div>  <!-- container -->

</body>
</html>
