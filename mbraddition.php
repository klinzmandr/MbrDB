<html>
<head>
<title>MCID Addition</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body onChange="flagChange()">

<?php
session_start();
//include 'Incls/vardump.inc';

include 'Incls/datautils.inc';
include 'Incls/seccheck.inc';
include 'Incls/mainmenu.inc';

$action = $_REQUEST['action'];

if ($action == "add") {
	$table = $_REQUEST['tablename'];
	$mcid = $_REQUEST['mcid'];
	$fields[MCID] = $_REQUEST['mcid'];
	$fields[MemStatus] = 0;
	$fields[Inactive] = 'FALSE';
	$fields[MemDate] = date('Y-m-d');
	$fields[Source] = 'MbrDB';
	$fields[LastDonDate] = ''; $fields[LastDonPurpose] = ''; $fields[LastDonAmount] = '0.00'; 
	$fields[LastDuesDate] = ''; $fields[LastDuesAmount] = '0.00'; 
	$fields[LastCorrDate] = ''; $fields[LastCorrType] = '';
	$lead3 = substr($mcid,0,3);
	$target = substr($mcid,0,3) . '%';
	//echo "search string: $lead3<br />";
	$sql = "SELECT `MCID` from `members` WHERE `MCID` LIKE '$mcid';";
	$res = doSQLsubmitted($sql);
	$rowcnt = $res->num_rows;
	//echo "row count from select: $rowcnt<br>";
	if ($rowcnt > 0) {
		$avail = array();
		for ($i = 0; $i<100; $i++) {				// create list of possibles	
			$str= sprintf("%s%02d",$lead3,$i);
			$avail[$str] = 1;
			}
		//echo "available list: "; print_r($avail); echo '<br />';
		$sql = "SELECT `MCID` from `members` WHERE `MCID` LIKE '$target';";
		$res = doSQLsubmitted($sql);
		while ($r = $res->fetch_assoc()) {					// read search results
			//echo 'mcid from SELECT: '; print_r($r); echo '<br>';
			unset($avail[$r[MCID]]);									// delete from possibilities if already used
			}
		//print_r($avail);
		echo "<h3>MCID $mcid already in use</h3>";
		echo 'Please select an available MCID&apos;s from the following list:<br />
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
			echo "<a href=\"MbrInfotabbed.php?filter=".$fields[MCID]."\"><h3>Click to Complete MCID Info Entry</h3></a>";
			exit;
			}
		echo "<h2>Add unsuccessful!  Try another MCID.</h2>";
		}
	}
print <<<pagePart1
<script>

function chkmcid() {
	var mcid = document.addmbr.mcid.value;
	mcid = mcid.toUpperCase();
	document.addmbr.mcid.value = mcid;
	if(!mcid.match(/^([A-Z]{3})([0-9]{2})/g))  {
		document.addmbr.mcid.focus(); 
		alert("Please enter the MCID as 3 characters plus 2 digits."); 
		return false;	
		}
	if (mcid.length > 5) {
		document.addmbr.mcid.focus(); 
		alert("Please enter the MCID as 3 characters plus 2 digits!"); 
		return false;	
		}
	return true;
	}
function confirmadd() {
	var r=confirm("Adding MCID "+mcid+" to database.\\n\\nConfirm by clicking OK.");	
	if (r == true) { return true; }
	return false;
	}
</script>
<div class="container">
<h2>Adding A New Member</h2>
<p>This function is to add a new member/contact record to the database.</p>
<p>This requires that a unique 5 character Member/Contact Identifier (MCID) be proposed to be used to identify that member.</p>
<p>The MCID is comprise of 3 alphabetic letters (usually the first three letters of the name of the member or organizational name or any organizational achronym) plus 2 numeric digits (usually the first 2 digits of the members street address or the last 2 digits of their phone number.)  This combination provides a predictable method that facilitates easy lookup for future reference.</p>
<p>Entry of the proposed MCID requires that the format of 3 letters and 2 digits are enforced.  Occasionally this will result in duplication of an existing MCID.  A list of alternative MCID&apos;s are provided in the event that the the one proposed is already in use.  Merely pick one from the list provided and enter it to complete the process.</p>
<p>Once you have successfully added a new record with a unique MCID for the member you will be presented with a blank input page where the remaining information about the member may be entered.</p>
<br />
<!-- <a class="btn btn-primary" href="MbrInfotabbed.php">CANCEL ADDITION</a><br /><hr> -->
<h4>Provide the EXACT MCID proposed for the new member record to be added:</h4>
<div class="row">
<div class="col-lg-4">
<form action="mbraddition.php" name="addmbr" onsubmit="return chkmcid()">
MCID: <input type="text" name="mcid" value="" onchange="chkmcid()">
<input type="hidden" name="tablename" value="members">
<input type="hidden" name="key" value="MCID">
<input type="hidden" name="action" value="add">
<input type="submit" name="submit" value="Add MCID" onclick="return confirmadd()">
</form>
</div>   <!-- col -->
</div>  <!-- row -->
</div>  <!-- container -->
pagePart1;

?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
