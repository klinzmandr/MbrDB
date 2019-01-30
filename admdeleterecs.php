<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Deletions</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body onchange="flagChange()">

<script> 
var chgFlag = 0;

function chkchg() {
	if (chgFlag == 0) { return true; }
	var r=confirm("All changes made will be lost.\n\nConfirm by clicking OK. (" + chgFlag + ")");	
	if (r == true) { return true; }
	return false;
	}

function flagChange() {
	chgFlag += 1;
	//alert("something has changed count: " + chgFlag);
	return true;
	}

</script>
<?php
//include 'Incls/vardump.inc.php';

//NOTE: should also add delete of assoc EDI record when deleting and MCID record!!!!

include 'Incls/seccheck.inc.php';
//include 'Incls/adminmenu.inc.php';
include 'Incls/datautils.inc.php';
echo '<div class="container">';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';


// delete of record(s) requested
if ($action == "delete") {
	$recnbr = $_REQUEST['recnbr'];
	$table = $_REQUEST['tablename'];
	$key = $_REQUEST['key'];
	$sql = "SELECT * FROM `".$table."` WHERE `".$key."` = '".$recnbr."';";
//echo "sql: $sql<br>";
	$res = doSQLsubmitted($sql);			// validate that row exists
//echo "<pre>"; print_r($res); echo "</pre>";
	if ($res->num_rows == 0) {
		echo "<h4>ERROR - invalid record number entered!</h4>";
		echo "<a href=\"admdeleterecs.php\"><h3>RETURN</h3></a>";
		exit();
		}
	$rcd = $res->fetch_assoc();
	//echo "<pre>"; print_r($rcd); echo "</pre>";
	
	// delete all funding, correspondence and voltime records for given MCID
	// $recnbr is the MCID
	if ($table == 'members') {
		$fsql = "DELETE FROM `donations` WHERE `MCID` = '".$recnbr."';";
		$fdelcount = doSQLsubmitted($fsql);
		echo "Funding records deleted for MCID $recnbr: $fdelcount<br>";
		$csql = "DELETE FROM `correspondence` WHERE `MCID` = '".$recnbr."';";
		$cdelcount = doSQLsubmitted($csql);
		echo "Correspondence records deleted for $recnbr: $cdelcount<br>";
		$vtsql = "DELETE FROM `voltime` WHERE `MCID` = '".$recnbr."';";
		$vtdelcount = doSQLsubmitted($vtsql);
		echo "Vol time records deleted for $recnbr: $vtdelcount<br>";
		$edisql = "DELETE FROM `extradonorinfo` WHERE `MCID` = '".$recnbr."';";
		$edidelcount = doSQLsubmitted($edisql);
		echo "EDI records deleted for $recnbr: $edidelcount<br>";
		}	
	
// delete associated correspondence for a funding 'dues' record
	if (($table == 'donations') AND ($rcd[Purpose] == 'Dues')) {
		//echo "dues record found<br>";
		$mcid = $rcd[MCID]; $date = $rcd[DonationDate];
		$qrysql = "SELECT * FROM `correspondence` WHERE `MCID` = '".$mcid."' AND `DateSent` = '".$date."' AND Reminders = 'RenewalPaid' limit 0,1;";		// use only the first occurance in case there were muliple for day
		//echo "correspondence deletion sql: $qrysql<br>";
		$corrdel = doSQLsubmitted($qrysql);
		$corr_rec = $corrdel->fetch_assoc();
		//echo "<pre>"; print_r($corr_rec); echo "</pre>";
		$corr_recno = $corr_rec[CORID];		// get the rec nbr for single record delete
		echo "<h4>Funding record nbr: $recnbr and assoicated correspondence rececord nbr: $corr_recno have been deleted.</h4>";
		$delsql = "DELETE FROM `correspondence` WHERE CORID = '".$corr_recno."' AND `MCID` = '".$mcid."' AND `DateSent` = '".$date."' AND Reminders = 'RenewalPaid';";
		doSQLsubmitted($delsql);		// delete the associated correspondence record
		}

// delete from donations or corresondence table
	$sql = "DELETE FROM `".$table."` WHERE `".$key."` = '".$recnbr."';";  // delete row requested
	$res = doSQLsubmitted($sql);
	//echo "<pre>"; print_r($res); echo "</pre>";
	echo "<h4>Deletion of record $recnbr from table \"$table\" has been completed.</h4>";
	echo "<a class=\"btn btn-primary\" href=\"admdeleterecs.php\">RETURN</a>";
	echo "</div><script src=\"jquery.js\"></script><script src=\"js/bootstrap.min.js\"></script>";
	exit();
	}


print <<<pagePart1
<!-- <div class="well"> -->
<h2>Database Administration&nbsp;&nbsp;&nbsp;
<a class="btn btn-primary" href="admDBJanitor.php">RETURN</a></h2>
<h4 style="color: red; ">Please note that all of these actions are irreversable.  They cannot be undone.  Please ensure you have the correct MCID and/or record number(s) before proceeding.  Records once deleted can never be retrieved.</h4>
<br />

<!-- <a class="btn btn-primary" href="index.php">CANCEL AND RETURN</a><br /><hr> -->

<h4>Provide the EXACT MCID for the member record to be deleted:</h4>
<b>NOTE: deletion of an MCID record will result in the deletion of ALL associated funding, correspondence and vol time records!</b>
<div class="row">
<div class="col-lg-4">
<form action="admdeleterecs.php" name="delmbr">
MCID: <input type="text" name="recnbr" value="">
<input type="hidden" name="tablename" value="members">
<input type="hidden" name="key" value="MCID">
<input type="hidden" name="action" value="delete">
<input type="submit" name="submit" value="Delete" onclick="return chkchg()">
</form>
</div>  <!-- col-lg-4 -->
</div>   <!-- row -->

<hr>
<h4>Enter the SINGLE record number of the corresondence record to be deleted:</h4>
<div class="row">
<div class="col-lg-6">
<form action="admdeleterecs.php" name="delcorr">
Correspondence Record Number: <input type="text" name="recnbr" value="">
<input type="hidden" name="tablename" value="correspondence">
<input type="hidden" name="key" value="CORID">
<input type="hidden" name="action" value="delete">
<input type="submit" name="submit" value="Delete" onclick="return chkchg()">
</form>
</div>  <!-- col-lg-4 -->
</div>   <!-- row -->
<hr>
<h4>Enter the SINGLE record number of the funding record to be deleted:</h4>
<b>NOTE: if the Purpose of the Funding Record is &apos;Dues&apos; then its associated correspondence record will also be deleted.</b>
<div class="row">
<div class="col-lg-6">
<form action="admdeleterecs.php" name="deldon">
Funding Record Number: <input type="text" name="recnbr" value="">
<input type="hidden" name="tablename" value="donations">
<input type="hidden" name="key" value="DonationID">
<input type="hidden" name="action" value="delete">
<input type="submit" name="submit" value="Delete" onclick="return chkchg()">
</form>

</div>  <!-- col-lg-4 -->
</div>   <!-- row -->
<!-- </div>  well -->
pagePart1;

?>

<script src="jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</div>
</body>
</html>
